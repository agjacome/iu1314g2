<?php

namespace controllers;

/**
 * Controlador para Usuarios del sistema.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class UsersController extends Controller
{

    private $user; // modelo de usuario, se instanciara cuando resulte necesario

    /**
     * Constructor, construye la instancia de Controller a partir de la
     * peticion recibida.
     *
     * @param \components\Request $request
     *     Peticion HTTP recibida, encapsulada dentro de un objeto Request (ver
     *     en namespace components).
     */
    public function __construct($request)
    {
        parent::__construct($request);
    }

    /**
     * Accion por defecto del controlador de usuarios.
     */
    public function defaultAction()
    {
        // no se permite acceso al panel de usuario sin estar logueado
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // el metodo por defecto es el accesso a los detalles del usuario 
        // propio
        $this->request->login = $this->session->username;
        $this->get();
    }

    /**
     * Crea un nuevo usuario mediante el proceso habitual de registro. Los 
     * usuarios pueden crearse bien siendo administrador (nuevo usuario) o 
     * mediante el registro de usuarios no identificados.
     */
    public function create()
    {
        // si ya esta loggeado y no es admin, no se permite acceso a registro
        if ($this->isLoggedIn() && !$this->isAdmin())
            $this->redirect("user");

        // si GET, redirige al formulario de registro
        if ($this->request->isGet())
            $this->view->render("user_register");

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

    /**
     * Metodo privado interno para el manejo de la peticion POST en create()
     */
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
     * Modifica los datos de un usuario dado. Si es administrador, podra 
     * modificar a cualquier usuario, en otro caso solo el propio usuario puede 
     * modificar sus datos. El nombre de usuario no se modificara bajo ninguna 
     * circunstancia.
     */
    public function update()
    {
        // en ningun caso se permite la modificacion de usuarios sin estar 
        // identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect();

        // se debe proporcionar el login del usuario a modificar
        if (!isset($this->request->login))
            $this->redirect("user");

        // el usuario a modificar debe existir previamente en la base de datos
        $this->user = new \models\User(strtolower($this->request->login));
        if ($this->user->isNewLogin()) {
            $this->setFlash($this->lang["user"]["update_err"]);
            $this->redirect("user");
        }
        $this->user->fill();

        // solo se permite modificacion del propio usuario o necesidad de 
        // permisos de administrador
        if ($this->session->username !== $this->request->login && !$this->isAdmin()) {
            $this->setFlash($this->lang["user"]["update_err"]);
            $this->redirect("user");
        }

        // si GET, redirige al formulario de modificacion
        if ($this->request->isGet()) {
            $this->view->assign("user", $this->user);
            $this->view->render("user_update");
        }

        // si POST, realiza la modificacion y redirige
        if ($this->request->isPost()) {
            if ($this->updatePost()) {
                $this->setFlash($this->lang["user"]["update_ok"]);
                $this->redirect("user");
            } else {
                $this->setFlash($this->lang["user"]["update_err"]);
                $this->redirect("user", "update");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en update()
     */
    private function updatePost()
    {
        // comprueba  que la contraseña y su verificacion sean iguales
        if (!empty($this->request->password) && ($this->request->password !== $this->request->verifyPassword))
            return false;

        // comprueba validez y guarda contraseña cifrada
        if (!empty($this->request->password) && !$this->user->setPassword($this->request->password))
            return false;

        // solo se puede cambiar rol si el usuario loggeado es administrador
        if (!empty($this->request->role) && $this->isAdmin())
            $this->user->role = $this->request->role;

        // para cada uno de los campos, si se ha proporcionado, actualiza los 
        // datos en $this->user
        if (!empty($this->request->email))
            $this->user->email = $this->request->email;

        if (!empty($this->request->name))
            $this->user->name = $this->request->name;

        if (!empty($this->request->address))
            $this->user->address = $this->request->address;

        if (!empty($this->request->telephone))
            $this->user->telephone = $this->request->telephone;

        // valida campos y almacena en BD
        return $this->user->validate() && $this->user->save();
    }

    /**
     * Borra a un usuario de la base de datos.
     */
    public function delete()
    {
        // en ningun caso se permite la eliminacion de usuarios sin estar 
        // identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect();

        // se debe proporcionar el login del usuario a eliminar
        if (!isset($this->request->login))
            $this->redirect("user");
        $login = $this->request->login;

        // solo se permite eliminacion del propio usuario o necesidad de 
        // permisos de administrador
        if ($this->session->username !== $login && !$this->isAdmin()) {
            $this->setFlash($this->lang["user"]["delete_err"]);
            $this->redirect("user");
        }

        // elimina al usuario, si es posible, y redirecciona acordemente
        $this->user = new \models\User(strtolower($login));
        if ($this->user->delete()) {

            $this->setFlash($this->lang["user"]["delete_ok"]);

            // si el usuario eliminado es el mismo al logeado, debe hacerse 
            // un logout para evitar problemas, logout() se encarga del 
            // redireccionamiento a inicio
            if ($this->session->username === $login)
                $this->logout();

            // sino, es que era un admin, y lo redirigimos al listado de 
            // usuarios
            $this->redirect("user", "listing");

        } else {
            $this->setFlash($this->lang["user"]["delete_err"]);
            $this->redirect("user");
        }
    }

    /**
     * Muestra los detalles de un usuario concreto. Si es admin, puede ver a 
     * cualquier usuario, sino solo a si mismo.
     */
    public function get()
    {
        // en ningun caso se permite la consulta de datos de usuario a un 
        // usuario no identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect();

        // se debe proporcionar el login del usuario a consultar
        if (!isset($this->request->login))
            $this->redirect("user");

        // si no se encuentra un usuario con el login proporcionado, 
        // redirecciona mostrando el error
        $this->user = new \models\User(strtolower($this->request->login));
        if ($this->user->isNewLogin()) {
            $this->setFlash($this->lang["user"]["get_err"]);
            $this->redirect("user");
        }

        // solo se permite la consulta de datos al propio usuario a un usuario 
        // con permisos de administrador
        if ($this->session->username !== $this->request->login && !$this->isAdmin()) {
            $this->setFlash($this->lang["user"]["get_err"]);
            $this->redirect("user");
        }

        // se le pasan los datos del usuario a la vista
        $this->user->fill();
        $this->view->assign("user", $this->user);
        $this->view->render("user_get");
    }

    /**
     * Proporciona un listado de todos los usuarios de la base de datos. Solo 
     * disponible para administrador.
     */
    public function listing()
    {
        // solo permite mostrar listado de usuarios al administrador
        if (!$this->isAdmin())
            $this->redirect();

        $users = \models\User::findBy(null);

        $this->view->assign("list", $users);
        $this->view->render("user_list");
    }

    /**
     * Realiza el proceso de identificacion e inicio de sesion de un usuario en 
     * el sistema.
     */
    public function login()
    {
        // redirige si usuario ya identificado
        if ($this->isLoggedIn())
            $this->redirect("user");

        // si GET, muestra formulario de login
        if ($this->request->isGet())
            $this->view->render("user_login");

        // si POST, realiza identificacion y redirige adecuadamente
        if ($this->request->isPost()) {
            $this->user = new \models\User(strtolower($this->request->login));

            // solo hay un usuario con el login
            if (!$this->user->isNewLogin) {
                $this->user->fill();

                // si contraseña correcta, identifica al usuario
                if ($this->user->checkPassword($this->request->password)) {
                    $this->session->logged   = true;
                    $this->session->username = $this->user->getLogin();
                    $this->session->userrole = $this->user->role;

                    $this->setFlash($this->lang["user"]["login_ok"]);
                    $this->redirect();
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
     * Realiza el proceso de cierre de sesion del usuario.
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->session->logged   = false;
            $this->session->username = null;
            $this->session->userrole = null;
        }

        $this->redirect();
    }

}

?>
