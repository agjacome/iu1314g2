<?php

namespace controllers;

class StoresController extends Controller
{

    private $store;

    public function __construct($request)
    {
        parent::__construct($request);

        // dado que es un modelo unico, esto es: no se necesitaran hacer 
        // busquedas de ningun tipo, se puede instanciar ya en el constructor
        $this->store = new \models\Store();
        $this->store->fill();
    }

    public function defaultAction()
    {
        $this->view->render("store");
    }

    public function changeCommission()
    {
        // solo permitido para administrador
        if (!$this->isAdmin())
            $this->redirect();

        // si GET, muestra el formulario de cambio de comision
        if ($this->request->isGet()) {
            $this->view->assign("commission", $this->store->commission);
            $this->view->render("store_change_commission");
        }

        // si POST, realiza el cambio y redirige
        if ($this->request->isPost()) {
            if ($this->changeCommissionPost()) {
                $this->setFlash($this->lang["store"]["change_comm_ok"]);
                $this->redirect("store");
            } else {
                $this->setFlash($this->lang["store"]["change_comm_err"]);
                $this->redirect("store", "changeCommission");
            }
        }
    }

    public function changeCommissionPost()
    {
        // para cambiar la comision, es obligatorio rellenar el campo comision 
        // del formulario
        if (!isset($this->request->commission))
            return false;

        // cambia, valida y guarda nueva comision
        $this->store->commission = $this->request->commission;
        return $this->store->validate() && $this->store->save();
    }

    public function stats()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}

?>
