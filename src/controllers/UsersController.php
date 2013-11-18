<?php

namespace controllers;

/**
 * Controlador de usuarios, se ocupa de gestionar las acciones respecto a los usuarios y su base de datos así como devolver
 * lo que corresponda y redireccionar.
 *
 *  @package  controllers;
 */

class UsersController extends Controller
{
    private $user;

    public function __construct($request)
    {
        parent::__construct($request);
    }

    /**
     * Método que será llamado en caso de no existir la acción (método) que el usuario pida, en caso de que ninguno de los métodos
     * restantes sea el que pida el usuario, este será llamado, y llevará a la vista de usuario.
     */
    public function defaultAction()
    {
        // FIXME: hack temporal para pruebas
        $this->view->render("user.php");
    }

    /**
     * Crea un nuevo usuario, a no ser que este ya exista en la base de datos o ya esté logeado.
     */
    public function create()
    {
        // si ya esta loggeado y no es admin, no se permite acceso a registro
        if (isset($this->session->logged) && $this->session->logged && $this->session->userrole !== "admin")
            $this->redirect("user");

        // si GET, redirige al formulario de registro
        if ($this->request->isGet())
            $this->view->render("register.php");

        // si POST, realiza el registro y redirige
        if ($this->request->isPost()) {
            if ($this->createPost()) {
                $this->setFlash($this->lang["user"]["create_ok"]);
                $this->redirect("user");
            } else {
                $this->setFlash($this->lang["user"]["create_err"]);
                $this->redirect("user", "create");
            }
        }
    }

    private function createPost()
    {
        // campos requeridos para registro (todos)
        $required =
            isset($this->request->login)          &&
            isset($this->request->password)       &&
            isset($this->request->verifyPassword) &&
            isset($this->request->email)          &&
            isset($this->request->name)           &&
            isset($this->request->address)        &&
            isset($this->request->telephone);

        // comprueba que todos los campos existan y que la contraseña y su
        // verificacion sean iguales
        if (!$required || $this->request->password !== $this->request->verifyPassword)
            return false;

        // comprueba que el login proporcionado sea nuevo
        $this->user = new \models\User(strtolower($this->request->login));
        if (!$this->user->isNewLogin())
            return false;

        // comprueba validez y guarda contraseña cifrada
        if (!$this->user->setPassword($this->request->password))
            return false;

        $this->user->role      = "usuario";
        $this->user->email     = $this->request->email;
        $this->user->name      = $this->request->name;
        $this->user->address   = $this->request->address;
        $this->user->telephone = $this->request->telephone;

        // valida campos y almacena en BD
        return $this->user->validate() && $this->user->save();
    }

    /**
     * Almacena cambios de un usuario en la base de datos.
     */
    public function update()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Borra a un usuario de la base de datos.
     */
    public function delete()
    {
        // en ningun caso se permite la eliminacion de usuarios sin estar 
        // identificado en el sistema
        if (!isset($this->session->logged) || !$this->session->logged)
            $this->redirect();

        // se debe proporcionar el login del usuario a eliminar
        if (!isset($this->request->login))
            $this->redirect("user");
        $login = $this->request->login;

        // solo se permite eliminacion del propio usuario o necesidad de 
        // permisos de administrador
        if ($this->session->username !== $login && $this->session->userrole !== "admin") {
            $this->setFlash($this->lang["user"]["delete_err"]);
            $this->redirect("user");
        }

        // elimina al usuario, si es posible, y redirecciona acordemente
        $this->user = new \models\User($login);
        if ($this->user->delete()) {

            $this->setFlash($this->lang["user"]["delete_ok"]);

            // si el usuario eliminado es el mismo al logeado, debe hacerse 
            // un logout para evitar problemas, logout() se encarga del 
            // redireccionamiento a inicio
            if ($this->session->username === $login)
                $this->logout();

            // sino, es que era un admin, y lo redirigimos al listado de 
            // usuarios
            $this->redirect("user", "list");

        } else {
            $this->setFlash($this->lang["user"]["delete_err"]);
            $this->redirect("user");
        }
    }

    /**
     * Recupera un usuario de la base de datos.
     */
    public function get()
    {
        // en ningun caso se permite la consulta de datos de usuario a un 
        // usuario no identificado en el sistema
        if (!isset($this->session->logged) || !$this->session->logged)
            $this->redirect();

        // se debe proporcionar el login del usuario a consultar
        if (!isset($this->request->login))
            $this->redirect("user");
        $login = $this->request->login;

        // solo se permite la consulta de datos al propio usuario a un usuario 
        // con permisos de administrador
        if ($this->session->username !== $login && $this->session->userrole !== "admin") {
            $this->setFlash($this->lang["user"]["get_err"]);
            $this->redirect("user");
        }

        $users = \models\User::findBy(["login" => $login]);

        // si no se encuentra un usuario con el login proporcionado, 
        // redirecciona mostrando el error
        if (count($users) === 0) {
            $this->setFlash($this->lang["user"]["get_err"]);
            $this->redirect("user");
        }

        // se le pasan los datos del usuario a la vista
        $this->view->assign("login"     , $users[0]->getLogin());
        $this->view->assign("role"      , $users[0]->role);
        $this->view->assign("email"     , $users[0]->email);
        $this->view->assign("name"      , $users[0]->name);
        $this->view->assign("address"   , $users[0]->address);
        $this->view->assign("telephone" , $users[0]->telephone);
        $this->view->render("getUser.php");
    }

    /**
     * Lista los usuarios de la base de datos.
     */
    public function listing()
    {
        // solo permite mostrar listado de usuarios al administrador
        if (!isset($this->session->logged) || !$this->session->logged || $this->session->userrole !== "admin")
            $this->redirect();

        $list = \models\User::findBy(null);

        // el listado solo muestra el login y el email
        $users = array();
        foreach ($list as $user) {
            $users[ ] = [
                "login" => $user->getLogin(),
                "email" => $user->email,
            ];
        }

        $this->view->assign("list", $users);
        $this->view->render("userList.php");
    }

    /**
     * Login de un usuario en el sistema
     */
    public function login()
    {
        // redirige si usuario ya identificado
        if (isset($this->session->logged) && $this->session->logged)
            $this->redirect("user");

        // si GET, muestra formulario de login
        if ($this->request->isGet())
            $this->view->render("login.php");

        // si POST, realiza identificacion y redirige adecuadamente
        if ($this->request->isPost()) {
            $users = \models\User::findBy(["login" => strtolower($this->request->login)]);

            // solo hay un usuario con el login
            if (count($users) === 1) {
                $this->user = $users[0];

                // si contraseña correcta, identifica al usuario
                if ($this->user->checkPassword($this->request->password)) {
                    $this->session->logged   = true;
                    $this->session->username = $this->user->getLogin();
                    $this->session->userrole = $this->user->role;

                    $this->setFlash($this->lang["user"]["login_ok"] . $this->user->getLogin());
                    $this->redirect("user");
                }
            }

            // si algun error, marca sesion como no iniciada y redirige a login
            // de nuevo
            $this->session->logged = false;
            $this->setFlash($this->lang["user"]["login_err"]);
            $this->redirect("user", "login");
        }
    }

    /**
     * Cambia los parámetros de sesión de manera que el usuario deje de estar logeado y redirige al índice.
     */
    public function logout()
    {
        if (isset($this->session->logged) && $this->session->logged) {
            $this->session->logged   = false;
            $this->session->username = null;
            $this->session->userrole = null;
        }

        $this->redirect();
    }

}

?>
