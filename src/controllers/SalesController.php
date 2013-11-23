<?php

namespace controllers;

/**
 * Controlador para Ventas y Compras con sus Pagos asociados.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class SalesController extends Controller
{

    private $product; // modelo de producto, se instanciara cuando resulte necesario
    private $sale;    // modelo de venta, se instanciara cuando resulte necesario

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
     * Accion por defecto del controlador de ventas
     */
    public function defaultAction()
    {
        $this->listing();
    }

    /**
     * Crea una nueva venta asociada a un producto dado. Solo se permite
     * creacion de venta al usuario propietario del producto y/o al
     * administrador.
     */
    public function create()
    {
        // solo se permite creacion de ventas a usuarios identificados
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador del producto a poner en venta
        if (!$this->request->product)
            $this->redirect("product");

        // el producto debe existir y estar en estado pendiente
        $this->product = new \models\Product($this->request->product);
        if (!$this->product->fill() || $this->product->state !== "pendiente") {
            $this->setFlash($this->lang["sale"]["create_err"]);
            $this->redirect("product");
        }

        // solo se permite la puesta en venta al propietario o a un
        // administrador
        if ($this->session->username !== $this->product->getOwner() && !$this->isAdmin()) {
            $this->setFlash($this->lang["sale"]["create_err"]);
            $this->redirect("sale");
        }

        // si GET, redirige al formulario de creacion de venta, con los datos
        // necesarios del producto
        if ($this->request->isGet()) {
            $this->view->assign("product", $this->product);
            $this->view->render("sale_create");
        }

        // si POST, inserta la venta y redirige
        if ($this->request->isPost()) {
            if ($this->createPost()) {
                $this->setFlash($this->lang["sale"]["create_ok"]);
                $this->redirect("sale");
            } else {
                $this->setFlash($this->lang["sale"]["create_err"]);
                $this->redirect("sale", "create");
            }
        }
    }

    /**
     * Metodo privato interno para el manejo de la peticion POST en create()
     */
    private function createPost()
    {
        // campos necesarios para creacion de venta
        $required = isset($this->request->price) && isset($this->request->stock);
        if (!$required) return false;

        // crea venta, actualiza estado de producto, valida campos e inserta en
        // BD
        $this->sale = new \models\Sale(null, $this->product->getId());
        $this->sale->price    = $this->request->price;
        $this->sale->stock    = $this->request->stock;
        $this->product->state = "venta";

        return $this->sale->validate() && $this->product->validate() &&
            $this->sale->save() && $this->product->save();
    }

    /**
     * Modifica los datos almacenados de una venta. Solo se permite
     * modificacion al propietario del producto en venta y/o al administrador.
     */
    public function update()
    {
        // bajo ninguna circunstancia se permite modificacion de ventas sin
        // estar identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador de venta a modificar
        if (!isset($this->request->id))
            $this->redirect("sale");

        // la venta a modificar debe existir previamente en la BD
        $this->sale    = new \models\Sale($this->request->id);
        if (!$this->sale->fill()) {
            $this->setFlash($this->lang["sale"]["update_err"]);
            $this->redirect("sale");
        }
        $this->product = new \models\Product($this->sale->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["sale"]["update_err"]);
            $this->redirect("sale");
        }

        // solo se permite modificacion de venta al propietario del producto
        // y/o a un administrador
        if ($this->session->username !== $this->product->getOwner() && !$this->isAdmin()) {
            $this->setFlash($this->lang["sale"]["update_err"]);
            $this->redirect("sale");
        }

        // si GET, redirige al formulario de modificacion, que recibe todos los
        // datos actuales del producto y la venta
        if ($this->request->isGet()) {
            $this->view->assign("product" , $this->product);
            $this->view->assign("sale"    , $this->sale);
            $this->view->render("sale_update");
        }

        // si POST, realiza la modificacion y redirige
        if ($this->request->isPost()) {
            if ($this->updatePost()) {
                $this->setFlash($this->lang["sale"]["update_ok"]);
                $this->redirect("sale");
            } else {
                $this->setFlash($this->lang["sale"]["update_err"]);
                $this->redirect("sale", "update");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en update()
     */
    private function updatePost()
    {
        // para los dos campos modificables desde el formulario (precio y
        // stock, puesto que el resto son inmutables), comprueba que no lleguen
        // vacios, si es asi no modifica el contenido previo
        if (!empty($this->request->price))
            $this->sale->price = $this->request->price;
        if (!empty($this->request->stock))
            $this->sale->stock = $this->request->stock;

        // valida campos y almacena en BD
        return $this->sale->validate() && $this->sale->save();
    }

    /**
     * Elimina una venta, estableciendo del producto de nuevo a pendiente. Solo
     * se permite la eliminacion de la venta al propietario del producto y/o al
     * administrador.
     */
    public function delete()
    {
        // en ningun caso se permite la eliminacion de ventas sin estar
        // identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador de la venta a eliminar
        if (!isset($this->request->id))
            $this->redirect("sale");

        // comprueba si existe la venta y producto asociado al id dado
        $this->sale = new \models\Sale($this->request->id);
        if (!$this->sale->fill()) {
            $this->setFlash($this->lang["sale"]["delete_err"]);
            $this->redirect("sale");
        }
        $this->product = new \models\Product($this->sale->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["sale"]["delete_err"]);
            $this->redirect("sale");
        }

        // solo el propietario del producto en venta y/o un administrador
        // podran eliminar la venta
        if ($this->product->getOwner() !== $this->session->username && !$this->isAdmin()) {
            $this->setFlash($this->lang["sale"]["delete_err"]);
            $this->redirect("sale");
        }

        // elimina la venta, si es posible, actualizando el estado del producto
        // de nuevo a "pendiente" y redirecciona acordemente
        $this->product->state = "pendiente";
        if ($this->product->validate() && $this->product->save() && $this->sale->delete()) {
            $this->setFlash($this->lang["sale"]["delete_ok"]);
            $this->redirect("sale");
        } else {
            $this->setFlash($this->lang["sale"]["delete_err"]);
            $this->redirect("sale");
        }
    }

    /**
     * Proporciona los datos concretos de una venta de producto.
     */
    public function get()
    {
        // se debe proporcionar el identificador de la venta a consultar
        if (!isset($this->request->id))
            $this->redirect("sale");

        // obtiene datos de la venta y el producto
        $this->sale = new \models\Sale($this->request->id);
        if (!$this->sale->fill()) {
            $this->setFlash($this->lang["sale"]["get_err"]);
            $this->redirect("sale");
        }
        $this->product = new \models\Product($this->sale->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["sale"]["get_err"]);
            $this->redirect("sale");
        }

        // obtiene puntuaciones y puntuacion media del producto
        $ratings = \models\Rating::findBy(["idProducto" => $this->product->getId()]);

        $rateAvg = 0.0;
        foreach ($ratings as $rating) $rateAvg  += intval($rating->rating);
        if (count($ratings) > 0) $rateAvg /= count($ratings);

        // se le pasan los datos de la venta y producto a la vista, y se
        // renderiza
        $this->view->assign("product" , $this->product);
        $this->view->assign("sale"    , $this->sale);
        $this->view->assign("rate"    , $rateAvg);
        $this->view->assign("ratings" , $ratings);
        $this->view->render("sale_get");
    }

    /**
     * Proporciona un listado de todos los productos en venta en el sistema.
     */
    public function listing()
    {
        // obtiene todas las ventas del sistema
        $sales = \models\Sale::findBy(null);

        // se crea un array donde cada elemento sera una par (venta, producto) 
        // para todas las ventas existentes en la BD
        $list = array();
        foreach ($sales as $sale) {
            $product = new \models\Product($sale->getProductId());
            if (!$product->fill()) break;

            $list[ ] = [
                "sale"    => $sale,
                "product" => $product,
            ];
        }

        // se devuelve el array de datos creado y se renderia la vista
        $this->view->assign("list", $list);
        $this->view->render("sale_list");
    }

    /**
     * Proporciona un listado de todos los productos en posesion del usuario 
     * identificado.
     */
    public function owned()
    {
        // solo se puede acceder estando identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // obtiene todas las ventas del usuario
        $products = \models\Product::findBy(["propietario" => $this->session->username, "estado" => "venta"]);

        // se crea un array donde cada elemento sera una par (venta, producto) 
        // para todas las ventas del usuario existentes en la BD
        $list = array();
        foreach ($products as $product) {
            $sale = new \models\Sale(null, $product->getId());
            if (!$sale->fromProduct()) break;

            $list[ ] = [
                "sale"    => $sale,
                "product" => $product,
            ];
        }

        // se devuelve el array de datos creado y se renderia la vista
        $this->view->assign("list", $list);
        $this->view->render("sale_list");
    }

    /**
     * Proporciona un listado de todos los productos en venta por los cuales el
     * usuario identificado, o bien uno dado si invocado por administrador, ha
     * pujado.
     */
    public function purchased()
    {
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // obtiene todas las compras del usuario
        $purchases = \models\Purchase::findBy(["login" => $this->session->username]);

        // se crea un array donde cada elemento sera una tupla (compra, pago, 
        // venta, producto) para todas las compras del usuario existentes en la 
        // BD
        $list = array();
        foreach ($purchases as $purchase) {
            $sale = new \models\Sale($purchase->getSaleId());
            if (!$sale->fill()) break;

            $payment = new \models\Payment($purchase->idPayment);
            if (!$payment->fill()) break;

            $product = new \models\Product($sale->getProductId());
            if (!$product->fill()) break;

            $list[ ] = [
                "purchase" => $purchase,
                "payment"  => $payment,
                "sale"     => $sale,
                "product"  => $product
            ];
        }

        // se devuelve el array de datos creado y se renderiza la vista
        $this->view->assign("list", $list);
        $this->view->render("purchase_list");
    }

    /**
     * Realiza una compra de un producto en venta y su pago asociado. Si la
     * compra no es pagada, no se almacenara en la BD y, por tanto, quedara
     * descartada. No se consideran "compras pendientes de pago".
     */
    public function purchase()
    {
        // solo se permite comprar a usuarios identificados
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // debe recibirse el identificador de la venta
        if (!isset($this->request->sale)) {
            $this->setFlash($this->lang["sale"]["purchase_err"]);
            $this->redirect("sale");
        }

        // comprueba que la venta y el producto asociado existan
        $this->sale = new \models\Sale($this->request->sale);
        if (!$this->sale->fill()) {
            $this->setFlash($this->lang["sale"]["purchase_err"]);
            $this->redirect("sale");
        }
        $this->product = new \models\Product($this->sale->getProductId());
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["sale"]["purchase_err"]);
            $this->redirect("sale");
        }

        // comprueba que el propietario del producto no sea el mismo usuario 
        // logueado
        if ($this->product->getOwner() === $this->session->username) {
            $this->setFlash($this->lang["sale"]["purchase_err"]);
            $this->redirect("sale");
        }

        // comprueba si la venta tiene stock
        if ($this->sale->stock <= 0) {
            $this->setFlash($this->lang["sale"]["purchase_err"]);
            $this->redirect("sale");
        }

        // si GET, muestra formulario de compra, que recibe los datos del 
        // producto y la venta
        if ($this->request->isGet()) {
            $this->view->assign("product" , $this->product);
            $this->view->assign("sale"    , $this->sale);
            $this->view->render("sale_purchase");
        }

        // si POST, realiza la compra y pago asociado
        if ($this->request->isPost()) {
            if ($this->purchasePost()) {
                $this->setFlash($this->lang["sale"]["purchase_ok"]);
                $this->redirect("sale", "purchased");
            } else {
                $this->setFlash($this->lang["sale"]["purchase_err"]);
                $this->redirect("sale");
            }
        }
    }

    /**
     * Metodo privado para el manejo de la peticion POST en purchase()
     */
    private function purchasePost()
    {
        // comprueba que todos los datos necesarios hayan sido recibidos
        $required = isset($this->request->quantity) && isset($this->request->payMethod);
        if (!$required) return false;

        // crea la compra y el pago
        $purchase = new \models\Purchase(null, $this->request->sale, $this->session->username);
        $payment  = new \models\Payment();

        // segun el metodo de pago elegido, o la cuenta de paypal o la tarjeta 
        // de credito deben haberse proporcionado
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

        // almacena los datos de la compra en el modelo de compra
        $purchase->quantity  = $this->request->quantity;
        $purchase->idPayment = $payment->getId();

        // resta la cantidad comprada a la venta
        $this->sale->stock -= $purchase->quantity;

        // valida y guarda los datos de la compra en la BD
        return $purchase->validate() && $this->sale->validate() &&
               $purchase->save() && $this->sale->save();
    }

}

?>
