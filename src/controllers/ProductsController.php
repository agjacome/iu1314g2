<?php

namespace controllers;

/**
 * Controlador para Productos.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class ProductsController extends Controller
{

    private $product;  // modelo de producto, se instanciara cuando resulte necesario

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
     * Accion por defecto del controlador de productos
     */
    public function defaultAction()
    {
        // FIXME: decidir accion por defecto para /index.php?controller=product
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Crea un nuevo producto. Solo se permite la creacion a usuarios 
     * identificados en el sistema.
     */
    public function create()
    {
        // solo se permitira creacion de productos a usuarios identificados
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // si GET, muestra formulario de insercion de producto
        if ($this->request->isGet())
            $this->view->render("product_create");

        // si POST, inserta producto y redirige
        if ($this->request->isPost()) {
            if ($this->createPost()) {
                $this->setFlash($this->lang["product"]["create_ok"]);
                $this->redirect("product");
            } else {
                $this->setFlash($this->lang["product"]["create_err"]);
                $this->redirect("product", "create");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en create()
     */
    private function createPost()
    {
        // campos requeridos para creacion de producto
        // (todos salvo login, que se obtendra de la sesion)
        $request =
            isset($this->request->state) &&
            isset($this->request->name)  &&
            isset($this->request->description);

        // comprueba que todos los campos existan
        if (!$request) return false;

        // crea producto, valida campos e inserta en la BD
        $this->product = new \models\Product(null, $this->session->username);
        $this->product->state = "pendiente";
        $this->product->name  = $this->request->name;
        $this->product->description = $this->request->description;

        return $this->product->validate() && $this->product->save();
    }

    /**
     * Modifica los datos de un producto en concreto. Solo se permite la
     * modificacion al propietario y/o a un administrador.
     */
    public function update()
    {
        // bajo ninguna circunstancia se permite la modificacion de productos
        // sin estar identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador del producto a modificar
        if (!isset($this->request->id))
            $this->redirect("product");

        // el producto a modificar debe existir previamente en la BD
        $this->product = new \models\Product($this->request->id);
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["product"]["update_err"]);
            $this->redirect("product");
        }

        // solo se permite modificacion del producto al propietario del mismo o
        // a un administrador
        if ($this->session->username !== $this->product->getOwner() && !$this->isAdmin()) {
            $this->setFlash($this->lang["product"]["update_err"]);
            $this->redirect("product");
        }

        // si GET, redirige al formulario de modificacion, que recibe todos los
        // datos actuales del producto
        if ($this->request->isGet()) {
            $this->view->assign("owner" , $this->product->owner);
            $this->view->assign("state" , $this->product->state);
            $this->view->assign("name"  , $this->product->name);
            $this->view->assign("descr" , $this->product->description);

            $this->view->render("product_update");
        }

        // si POST, realiza la modificacion y redirige
        if ($this->request->isPost()) {
            if ($this->updatePost()) {
                $this->setFlash($this->lang["product"]["update_ok"]);
                $this->redirect("product");
            } else {
                $this->setFlash($this->lang["product"]["update_err"]);
                $this->redirect("product", "update");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en update().
     */
    private function updatePost()
    {
        // para los dos campos modificables desde el formulario (nombre y
        // descripcion, puesto que propietario e id son inmutables y estado se
        // modifica cuando se crea la venta/subasta asociada, no aqui).
        if (!empty($this->request->name))
            $this->product->name = $this->request->name;

        if (!empty($this->request->description))
            $this->product->description = $this->request->description;

        // valida campos y almacena en BD
        return $this->product->validate() && $this->product->save();
    }

    /**
     * Elimina un producto de la BD. Solo el propietario podra eliminar el
     * producto, siempre y cuando este no este en venta ni en subasta. Se
     * permite la eliminacion de productos por parte del administrador bajo
     * cualquier circunstancia.
     */
    public function delete()
    {
        // en ningun caso se permite la eliminacion de productos sin estar
        // identificado en el sistema
        if (!$this->isLoggedIn())
            $this->redirect("user", "login");

        // se debe proporcionar el identificador del producto a eliminar
        if (!isset($this->request->id))
            $this->redirect("product");

        // comprueba si existe un producto con el identificador dado
        $this->product = new \models\Product($this->request->id);
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["product"]["delete_err"]);
            $this->redirect("product");
        }

        // solo el propietario del producto o un administrador podran
        // eliminarlo
        if ($this->product->owner !== $this->session->username && !$this->isAdmin()) {
            $this->setFlash($this->lang["product"]["delete_err"]);
            $this->redirect("product");
        }

        // el propietario solo puede eliminar el producto si esta en estado
        // pendiente (no hay subastas ni ventas activas), al administrador se
        // le permiten estas salvajadas sin problemas
        if ($this->product->owner === $this->session->username && $this->product->state !== "pendiente") {
            $this->setFlash($this->lang["product"]["delete_err"]);
            $this->redirect("product");
        }

        // elimina el producto, si es posible, y redirecciona acordemente
        if ($this->product->delete()) {
            $this->setFlash($this->lang["product"]["delete_ok"]);
            $this->redirect("product", "available");
        } else {
            $this->setFlash($this->lang["product"]["delete_err"]);
            $this->redirect("product");
        }
    }

    /**
     * Proporciona los datos concretos de un producto en particular.
     */
    public function get()
    {
        // se debe proporcionar el identificador del producto a consultar
        if (!isset($this->request->id))
            $this->redirect("product");

        // si no se encuentra el producto con el identificador dado,
        // redirecciona mostrando el error
        $this->product = new \models\Product($this->request->id);
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["product"]["get_err"]);
            $this->redirect("product");
        }

        // se le pasan los datos del producto a la vista
        $this->view->assign("id"    , $this->product->getId());
        $this->view->assign("owner" , $this->product->getOwner());
        $this->view->assign("state" , $this->product->state);
        $this->view->assign("name"  , $this->product->name);
        $this->view->assign("descr" , $this->product->description);
        $this->view->render("product_get");
    }

    /**
     * Proporciona un listado de TODOS los productos existentes, disponible
     * solo para administradores.
     */
    public function listing()
    {
        // solo permite mostrar listado de TODOS los productos al administrador
        // si no es admin, redireccion a la lista de productos disponibles
        if (!$this->isAdmin())
            $this->redirect("product", "available");

        $list = \models\Product::findBy(null);

        // el listado muestra ID, propietario, estado y nombre del producto
        $products = array();
        foreach ($list as $product) {
            $products[ ] = [
                "id"    => $product->getId(),
                "owner" => $product->getOwner(),
                "state" => $product->state,
                "name"  => $product->name
            ];
        }

        $this->view->assign("list", $products);
        $this->view->render("product_list");
    }

    /**
     * Proporciona un listado de los productos en estado subasta o venta,
     * disponible para todos los usuarios
     */
    public function available()
    {
        // FIXME: implementacion FEA e ineficiente, crear en modelo de producto 
        // un metodo para obtener todos a traves de un dao->query("SELECT * 
        // FROM PRODUCTO WHERE estado = 'subasta' OR estado = 'venta') e 
        // invocarlo desde aqui, en lugar de hacer dos consultas a findBy
        // El problema viene derivado de que SQLDAO, en su select() solo hace 
        // ANDs, y nunca ORs, pero para este tipo de casos es para los que se 
        // proporcina el query() para consultas arbitrarias.
        $products = array();

        // introduce en el array de productos los productos en subasta
        $list = \models\Product::findBy(["estado" => "subasta"]);
        foreach ($list as $product) {
            $products[ ] = [
                "id"    => $product->getId(),
                "owner" => $product->getOwner(),
                "state" => $product->state,
                "name"  => $product->name
            ];
        }

        // introduce en el array de productos los productos en venta
        $list = \models\Product::findBy(["estado" => "venta"]);
        foreach ($list as $product) {
            $products[ ] = [
                "id"    => $product->getId(),
                "owner" => $product->getOwner(),
                "state" => $product->state,
                "name"  => $product->name
            ];
        }

        $this->view->assign("list", $products);
        $this->view->render("product_list");
    }

    /**
     * Proporciona un listado de los productos de los que el usuario
     * identificado es propietario.
     */
    public function owned()
    {
        // solo permite mostrar listado de productos en posesion a usuarios 
        // loggeados
        if (!$this->isLoggedIn())
            $this->redirect("product", "available");

        $list = \models\Product::findBy(["propietario" => $this->session->username]);

        // el listado muestra ID, estado y nombre del producto
        $products = array();
        foreach ($list as $product) {
            $products[ ] = [
                "id"    => $product->getId(),
                "state" => $product->state,
                "name"  => $product->name
            ];
        }

        $this->view->assign("list", $products);
        $this->view->render("product_list");
    }

}

?>
