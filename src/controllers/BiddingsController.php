<?php

namespace controllers;

/**
 * Controlador para Subastas y Pujas con sus Pagos asociados.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class BiddingsController extends Controller
{
    private $bidding; // modelo de subasta, se instanciara cuando resulte necesario
    private $product; // modelo de producto, se instanciara cuando resulte necesario

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
     * Accion por defecto para controlador de subastas
     */
    public function defaultAction()
    {
        $this->listing();
    }

    /**
     * Crea una nueva subasta asociada a un producto dado. Solo se permite 
     * creacion de subasta al usuario en posesion del producto y/o al 
     * administrador.
     */
    public function create()
    {
        // solo se permite creacion de subastas a usuarios identificados
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador del producto a poner en subasta
        if (!$this->request->product)
            $this->redirect("product");

        // el producto debe existir y estar en estado pendiente
        $this->product = new \models\Product($this->request->product);
        if (!$this->product->fill() || $this->product->state !== "pendiente") {
            $this->setFlash($this->lang["bidding"]["create_err"]);
            $this->redirect("product");
        }

        // solo se permite la puesta en subasta al propietario o a un
        // administrador
        if ($this->session->username !== $this->product->getOwner() && !$this->isAdmin()) {
            $this->setFlash($this->lang["bidding"]["create_err"]);
            $this->redirect("bidding");
        }

        // si GET, redirige al formulario de creacion de subasta, con los datos
        // necesarios del producto
        if ($this->request->isGet()) {
            $this->view->assign("product", $this->product);
            $this->view->render("bidding_create");
        }

        // si POST, inserta la subasta y redirige
        if ($this->request->isPost()) {
            if ($this->createPost()) {
                $this->setFlash($this->lang["bidding"]["create_ok"]);
                $this->redirect("bidding");
            } else {
                $this->setFlash($this->lang["bidding"]["create_err"]);
                $this->redirect("bidding", "create");
            }
        }
    }

    /**
     * Metodo privato interno para el manejo de la peticion POST en create()
     */
    private function createPost()
    {
        // campos necesarios para creacion de subasta
        $required = isset($this->request->minBid) && isset($this->request->limitDate);
        if (!$required) return false;

        // crea subasta, actualiza estado de producto, valida campos e inserta en
        // la base de datos
        $this->bidding = new \models\Bidding(null, $this->product->getId());
        $this->bidding->minBid    = $this->request->minBid;
        $this->bidding->limitDate = $this->request->limitDate;
        $this->product->state     = "subasta";

        return $this->bidding->validate() && $this->product->validate() &&
            $this->bidding->save() && $this->product->save();
    }

    /**
     * Elimina una subasta, estableciendo el producto de nuevo a pendiente. 
     * Solo se permite la eliminacion de la subasta si no existen pujas o si 
     * asi lo decide el administrador (eliminando todas las pujas asociadas).
     */
    public function delete()
    {
        // en ningun caso se permite la eliminacion de subasta si no es administrador
        if (!$this->isAdmin())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador de la subasta a eliminar
        if (!isset($this->request->id))
            $this->redirect("bidding");

        // comprueba si existe la subasta y producto asociado al id dado
        $this->bidding = new \models\Bidding($this->request->id);
        if (!$this->bidding->fill()) {
            $this->setFlash($this->lang["bidding"]["delete_err"]);
            $this->redirect("bidding");
        }
        $this->product = new \models\Product($this->bidding->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["bidding"]["delete_err"]);
            $this->redirect("bidding");
        }

        // solo el administrador podra eliminar la venta
        if (!$this->isAdmin()) {
            $this->setFlash($this->lang["bidding"]["delete_err"]);
            $this->redirect("bidding");
        }

        // elimina la subasta, si es posible, actualizando el estado del producto
        // de nuevo a "pendiente" y redirecciona acordemente
        $this->product->state = "pendiente";
        if ($this->product->validate() && $this->product->save() && $this->bidding->delete()) {
            $this->setFlash($this->lang["bidding"]["delete_ok"]);
            $this->redirect("bidding");
        } else {
            $this->setFlash($this->lang["bidding"]["delete_err"]);
            $this->redirect("bidding");
        }
    }

    /**
     * Proporciona los datos concretos de una subasta de producto.
     */
    public function get()
    {
        // se debe proporcionar el identificador de la subasta a consultar
        if (!isset($this->request->id))
            $this->redirect("bidding");

        // obtiene datos de la subasta y el producto
        $this->bidding = new \models\Bidding($this->request->id);
        if (!$this->bidding->fill()) {
            $this->setFlash($this->lang["bidding"]["get_err"]);
            $this->redirect("bidding");
        }
        $this->product = new \models\Product($this->bidding->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["bidding"]["get_err"]);
            $this->redirect("bidding");
        }

        // obtiene puntuaciones y puntuacion media del producto
        $ratings = \models\Rating::findBy(["idProducto" => $this->product->getId()]);

        $rateAvg = 0.0;
        foreach ($ratings as $rating) $rateAvg  += intval($rating->rating);
        if (count($ratings) > 0) $rateAvg /= count($ratings);

        // obtiene la puja mas alta hasta el momento (puja minima si ninguna)
        $currentBid = $this->bidding->getHighestBid();
        if (!$currentBid) $currentBid = $this->bidding->minBid;
        else $currentBid = $currentBid->quantity;

        // se le pasan los datos de la subasta y producto a la vista, y se
        // renderiza
        $this->view->assign("product"       , $this->product);
        $this->view->assign("bidding"       , $this->bidding);
        $this->view->assign("currentBid"    , $currentBid);
        $this->view->assign("rate"          , $rateAvg);
        $this->view->assign("ratings"       , $ratings);
        $this->view->render("bidding_get");
    }

    /**
     * Proporciona un listado de todos los productos en subasta en el sistema.
     */
    public function listing()
    {
        $biddings = \models\Bidding::findBy(null);

        // se crea un array donde cada campo sera un par subastas, producto),
        // recuperando para ello los datos del producto en cada una de las
        // subastas obtenidas en la instruccion anterior
        $list = array();
        foreach ($biddings as $bidding) {
            $product = new \models\Product($bidding->getProductId());
            if (!$product->fill()) break;

            $list[ ] = [
                "bidding"    => $bidding,
                "product"    => $product
            ];
        }

        // se devuelve el array de pares creado
        $this->view->assign("list", $list);
        $this->view->render("bidding_list");
    }

    public function owned()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Realiza una pija de un producto en subasta. Debe comprobar que la puja 
     * sea mas alta a la ultima almacenada en dicha subasta o superior a la 
     * puja minima fijada si no hay pujas. Solo se permitira pujar a usuarios 
     * identificados.
     */
    public function makeBid()
    {
        // solo se permite pujar a usuarios identificados
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // debe recibirse el identificador de la subasta
        if (!isset($this->request->bidding)) {
            $this->setFlash($this->lang["bidding"]["bid_err"]);
            $this->redirect("bidding");
        }

        // comprueba que la subasta y el producto asociado existan
        $this->bidding = new \models\Bidding($this->request->bidding);
        if (!$this->bidding->fill()) {
            $this->setFlash($this->lang["bidding"]["bid_err"]);
            $this->redirect("bidding");
        }
        $this->product = new \models\Product($this->bidding->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["bidding"]["bid_err"]);
            $this->redirect("bidding");
        }

        // si GET, muestra formulario de puja, que recibe los datos del 
        // producto y la subasta, y la puja mas alta hasta el momento
        if ($this->request->isGet()) {
            $currentBid = $this->bidding->getHighestBid();
            if (!$currentBid) $currentBid = $this->bidding->minBid;
            else $currentBid = $currentBid->quantity;

            $this->view->assign("product"    , $this->product);
            $this->view->assign("bidding"    , $this->bidding);
            $this->view->assign("currentBid" , $currentBid);
            $this->view->render("bidding_bid");
        }

        // si POST, realiza la compra y pago asociado
        if ($this->request->isPost()) {
            if ($this->makeBidPost()) {
                $this->setFlash($this->lang["bidding"]["purchase_ok"]);
                $this->redirect("bidding");
            } else {
                $this->setFlash($this->lang["bidding"]["purchase_err"]);
                $this->redirect("bidding");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en makeBid()
     */
    private function makeBidPost()
    {
        if (!isset($this->request->quantity)) return false;

        $bid = new \models\Bid(null, $this->bidding->getId(), $this->session->username);
        $bid->quantity = $this->request->quantity;
        return $bid->validate() && $bid->save();
    }

    /**
     * Realiza el pago de una puja dada. Debera realizarse el pago una vez la 
     * subasta ha terminado (se ha llegado a la fecha limite), y solo debera 
     * permitirse el pago de la puja ganadora.
     */
    public function payBid()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todas las subastas ganadas por el usuario 
     * identificado, cuya puja aun no ha sido pagada.
     */
    public function pendingPayments()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}
?>
