<?php

namespace controllers;

class UsersController extends Controller
{

    private $user;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->user = new \models\User();
    }

    public function defaultAction()
    {
        // hack temporal para pruebas
        if (isset($this->session->logged_in) && $this->session->logged_in)
            print "Identificado como {$this->session->username}.";
        else
            print "No identificado.";
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
        if (isset($this->session->logged_in) && $this->session->logged_in)
            $this->redirect("user");

        if ($this->request->isGet()) {
            // View aun no implementado
            // $this->view->render("loginForm.php");

            // hack temporal para indicar que debe hacerse un post
            print "debe hacerse por POST!<br>";
        }

        if ($this->request->isPost()) {
            try {

                $users = \models\User::findBy(["login" => $this->request->login]);
                $this->user = $users[0]; // deberia haber un solo usuario con el login

                $this->session->logged_in = true;
                $this->session->username  = $this->user->getLogin();
                $this->session->userrole  = $this->user->role;

                $this->setFlash($this->lang["user"]["identified"] . $this->user->getLogin());

                $this->redirect("user");

            } catch (\models\exceptions\NotFoundException $nfe) {

                $this->session->logged_in = false;
                $this->setFlash($nfe->message);
                $this->redirect("user", "login");

            }
        }
    }

    public function logout()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}

?>
