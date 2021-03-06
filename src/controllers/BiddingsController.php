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
    private $bid;     // modelo de puja, se instanciara cuando resulte necesario
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

        // TODO: mover las comprobaciones sobre el modelo a los modelos.
        // el producto debe existir y estar en estado pendiente
        $this->product = new \models\Product($this->request->product);
        if (!$this->product->fill() || $this->product->state !== "pendiente") {
            $this->setFlash($this->lang["bidding"]["create_err"]);
            $this->redirect("product"); }

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
     * Solo se permite la eliminación de subastas al administrador.
     */
    public function delete()
    {
        // en ningun caso se permite la eliminacion de subasta si no es administrador
        if (!$this->isAdmin())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador de la subasta a eliminar
        if (!isset($this->request->id))
            $this->redirect("bidding");

        // TODO: mover las comprobaciones sobre el modelo a los modelos.
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

        // TODO: mover la obtencion de la media de puntuaciones al modelo de 
        // producto.
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

    /**
     * Proporciona un listado de todos las subastas de productos en posesion 
     * por el usuario identificado
     */
    public function owned()
    {
        // en ningun caso se podra acceder si el usuario no esta logueado
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // obtiene todas las subastas del usuario
        $products = \models\Product::findBy(["propietario" => $this->session->username, "estado" => "subasta"]);

        // se crea un array donde cada elemento sera una par (subasta, producto) 
        // para todas las subastas del usuario existentes en la BD
        $list = array();
        foreach ($products as $product) {
            $bidding = new \models\Bidding(null, $product->getId());
            if (!$bidding->fromProduct()) break;

            $list[ ] = [
                "bidding" => $bidding,
                "product" => $product,
            ];
        }

        // se devuelve el array de datos creado y se renderia la vista
        $this->view->assign("list", $list);
        $this->view->render("bidding_list");
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

        // TODO: mover todas las comprobaciones sobre el modelo a los modelos.
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

        // comprueba que el propietario del producto no sea el mismo usuario 
        // logueado
        if ($this->product->getOwner() === $this->session->username) {
            $this->setFlash($this->lang["bidding"]["bid_err"]);
            $this->redirect("bidding");
        }

        // comprueba que no se haya superado la fecha limite
        if (date("Y-m-d H:i:s") >= $this->bidding->limitDate) {
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
                $this->setFlash($this->lang["bidding"]["bid_ok"]);
                $this->redirect("bidding");
            } else {
                $this->setFlash($this->lang["bidding"]["bid_er"]);
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

        $this->bid = new \models\Bid(null, $this->bidding->getId(), $this->session->username);
        $this->bid->quantity = $this->request->quantity;
        return $this->bid->validate() && $this->bid->save();
    }

    /**
     * Realiza el pago de una puja dada. Debera realizarse el pago una vez la 
     * subasta ha terminado (se ha llegado a la fecha limite), y solo debera 
     * permitirse el pago de la puja ganadora.
     */
    public function payBid()
    {
        // solo se permite pagar a usuarios identificados
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // debe recibirse el identificador de la puja a pagar
        if (!isset($this->request->bid)) {
            $this->setFlash($this->lang["bidding"]["pay_err"]);
            $this->redirect("bidding", "pendingPayments");
        }

        // TODO: mover todas las comprobaciones sobre el modelo a los modelos.
        // comprueba que la puja exista y no tenga un pago asociado
        $this->bid = new \models\Bid($this->request->bid);
        if (!$this->bid->fill() || isset($this->bid->idPayment)) {
            $this->setFlash($this->lang["bidding"]["pay_err"]);
            $this->redirect("bidding", "pendingPayments");
        }

        // comprueba que la subasta existe y ya haya superado la fecha limite
        $this->bidding = new \models\Bidding($this->bid->getBiddingId());
        if (!$this->bidding->fill() || date("Y-m-d H:i:s") < $this->bidding->limitDate) {
            $this->setFlash($this->lang["bidding"]["pay_err"]);
            $this->redirect("bidding", "pendingPayments");
        }

        // si GET, muestra formulario de pago
        if ($this->request->isGet()) {
            $this->view->assign("bid", $this->bid);
            $this->view->render("bid_pay");
        }

        // si POST, realiza el pago
        if ($this->request->isPost()) {
            if ($this->payBidPost()) {
                $this->setFlash($this->lang["bidding"]["pay_ok"]);
                $this->redirect("bidding", "pendingPayments");
            } else {
                $this->setFlash($this->lang["bidding"]["pay_err"]);
                $this->redirect("bidding", "pendingPayments");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en payBid()
     */
    private function payBidPost()
    {
        // comprueba que se haya recibido el metodo de pago
        if (!isset($this->request->payMethod))
            return false;

        // crea el pago
        $payment = new \models\Payment();

        // segun el metodo de pago elegido, o la cuenta de paypal o la tarjeta 
        // de credito deben haberse proporcionado
        // FIXME: estas condicionales son feas, buscar una forma mas "limpia" 
        // de hacer la comprobacion de tipo de pago
        $payment->payMethod = $this->request->payMethod;
        if ($payment->payMethod === "paypal") {
            if (empty($this->request->paypal)) return false;
            $payment->paypal = $this->request->paypal;
        } elseif ($payment->payMethod === "tarjeta") {
            if (empty($this->request->creditCard)) return false;
            $payment->creditCard = $this->request->creditCard;
        } else { // metodo de pago desconocido
            return false;
        }

        // obtiene la comision actual que recibe la tienda de toda transaccion 
        // y la almacena en el pago
        $store = new \models\Store();
        $store->fill();
        $payment->commission = $store->commission;

        // valida y almacena el pago
        if (!$payment->validate() || !$payment->save()) return false;

        // actualiza la puja para indicar el identificador de pago
        $this->bid->idPayment = $payment->getId();
        return $this->bid->validate() && $this->bid->save();
    }

    /**
     * Proporciona un listado de todas las subastas ganadas por el usuario 
     * identificado, cuya puja aun no ha sido pagada.
     */
    public function pendingPayments()
    {
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // obtiene todas las pujas ganadoras pendientes de pago
        $bids = \models\Bid::findByLoginWhereWin($this->session->username);

        // se crea un array donde cada elemento sera una tupla (puja, subasta, 
        // producto) recuperando para ello los datos necesarios
        $list = array();
        foreach ($bids as $bid) {
            $bidding = new \models\Bidding($bid->getBiddingId());
            if (!$bidding->fill()) break;

            $product = new \models\Product($bidding->getProductId());
            if (!$product->fill()) break;

            $list[ ] = [
                "bid"     => $bid,
                "bidding" => $bidding,
                "product" => $product
            ];
        }

        // se devuelve el array de datos creado y se renderia la vista
        $this->view->assign("list", $list);
        $this->view->render("bid_pending");
    }

}

?>
