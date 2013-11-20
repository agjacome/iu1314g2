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
        $this->product = new \models\Product($this->sale->getProductId());
        if (!$this->sale->fill() || !$this->product->fill()) {
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

        // se le pasan los datos de la venta y producto a la vista, y se
        // renderiza
        $this->view->assign("product" , $this->product);
        $this->view->assign("sale"    , $this->sale);
        $this->view->render("sale_get");
    }

    /**
     * Proporciona un listado de todos los productos en venta en el sistema.
     */
    public function listing()
    {
        // obtiene todas las ventas del sistema
        $sales = \models\Sale::findBy(null);

        // se crea un array donde cada campo sera un par (venta, producto),
        // recuperando para ello los datos del producto en cada una de las
        // ventas obtenidas en la instruccion anterior
        $list = array();
        foreach ($sales as $sale) {
            $product = new \models\Product($sale->getProductId());
            if (!$product->fill()) break;

            $list[ ] = [
                "sale"    => $sale,
                "product" => $product
            ];
        }

        // se devuelve el array de pares creado
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
