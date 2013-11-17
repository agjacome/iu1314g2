<?php

namespace controllers;

class UsersController extends Controller
{

    private $user;

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function defaultAction()
    {
        // FIXME: hack temporal para pruebas
        $this->view->render("user.php");
    }

    public function create()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function update()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function delete()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function get()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function listing()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

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
