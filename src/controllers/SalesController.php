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
        // FIXME: decidir accion por defecto para /index.php?controller=sale
        trigger_error("Aun no implementado", E_USER_ERROR);
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

        // crea venta, valida campos e inserta a la BD
        $this->sale = new \models\Sale(null, $this->product->getId());
        $this->sale->price = $this->request->price;
        $this->sale->stock = $this->request->stock;

        return $this->sale->validate() && $this->sale->save();
    }

    /**
     * Modifica los datos almacenados de una venta. Solo se permite 
     * modificacion al propietario del producto en venta y/o al administrador.
     */
    public function update()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Elimina una venta, estableciendo del producto de nuevo a pendiente. Solo 
     * se permite la eliminacion de la venta al propietario del producto y/o al 
     * administrador.
     */
    public function delete()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona los datos concretos de una venta de producto.
     */
    public function get()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en venta en el sistema.
     */
    public function listing()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en venta por parte del 
     * usuario identificado o un usuario dado si invocado por administrador.
     */
    public function owned()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en venta por los cuales el 
     * usuario identificado, o bien uno dado si invocado por administrador, ha 
     * pujado.
     */
    public function purchased()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Realiza una compra de un producto en venta y su pago asociado. Si la 
     * compra no es pagada, no se almacenara en la BD y, por tanto, quedara 
     * descartada. No se consideran "compras pendientes de pago".
     */
    public function purchase()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Metodo privado para manejar el pago de compras.
     */
    private function pay()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}

?>
