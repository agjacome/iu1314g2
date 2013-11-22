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
        $this->available();
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
                $this->redirect("product", "owned");
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
        $request = isset($this->request->name)  && isset($this->request->description);

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
            $this->view->assign("product", $this->product);
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
        if ($this->product->getOwner() !== $this->session->username && !$this->isAdmin()) {
            $this->setFlash($this->lang["product"]["delete_err"]);
            $this->redirect("product");
        }

        // el propietario solo puede eliminar el producto si esta en estado
        // pendiente (no hay subastas ni ventas activas), al administrador se
        // le permiten estas salvajadas sin problemas
        if ($this->product->getOwner() === $this->session->username && $this->product->state !== "pendiente") {
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

        // se calcula la media de puntuaciones del producto y se almacenan los 
        // comentarios y puntuaciones en un array para pasarselo a la vista
        $ratings = \models\Rating::findBy(["idProducto" => $this->product->getId()]);

        $rateAvg  = 0.0; $comments = array();
        foreach ($ratings as $rating) {
            $rateAvg += intval($rating->rating);
            $comments[ ] = [
                "login"   => $rating->getLogin(),
                "comment" => $rating->commentary,
                "rating"  => $rating->rating
            ];
        }
        if (count($ratings) > 0) $rateAvg /= count($ratings);


        // se le pasan los datos del producto a la vista y se renderiza
        $this->view->assign("product" , $this->product);
        $this->view->assign("rateAvg" , $rateAvg);
        $this->view->assign("ratings" , $ratings);
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

        $products = \models\Product::findBy(null);

        // se le pasa el listado a la vista y se renderiza
        $this->view->assign("list", $products);
        $this->view->render("product_list");
    }

    /**
     * Proporciona un listado de los productos en estado subasta o venta,
     * disponible para todos los usuarios
     */
    public function available()
    {
        // obtiene los productos disponibles (en venta y subasta) y los 
        // devuelve a una vista renderizada
        $products = \models\Product::findByStateAvailable();
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

        $products = \models\Product::findBy(["propietario" => $this->session->username]);

        $this->view->assign("list", $products);
        $this->view->render("product_list");
    }

    /**
     * Califica un producto dado. Solo se permitira calificar productos a 
     * usuarios identificados en el sistema, y por cada usuario se permitira 
     * una sola calificacion a un producto.
     */
    public function rate()
    {
        // solo se permite puntuar/comentar a usuarios identificados en el 
        // sistema
        if (!$this->isLoggedIn())
            $this->redirect("product");

        // debe recibirse el identificador del producto
        if (!isset($this->request->prod)) {
            $this->setFlash($this->lang["product"]["rate_err"]);
            $this->redirect("product");
        }

        // comprueba que el producto exista
        $this->product = new \models\Product($this->request->prod);
        if (!$this->product->fill()) {
            $this->setFlash($this->lang["product"]["rate_err"]);
            $this->redirect("product");
        }

        // comprueba que el usuario no haya puntuado todavia el producto
        $rating = new \models\Rating(null, $this->request->prod, $this->session->username);
        if (!$rating->isNewRating()) {
            $this->setFlash($this->lang["product"]["rate_err"]);
            $this->redirect("product");
        }

        // si es GET, muestra formulario de puntuacion
        if ($this->request->isGet()) {
            $this->view->assign("product", $this->product);
            $this->view->render("product_rate");
        }

        // si es POST, almacena la puntuacion y redirige
        if ($this->request->isPost()) {
            if ($this->ratePost()) {
                $this->setFlash($this->lang["product"]["rate_ok"]);
                $this->redirect("product");
            } else {
                $this->setFlash($this->lang["product"]["rate_err"]);
                $this->redirect("product", "rate");
            }
        }
    }

    /**
     * Metodo privado interno para el manejo de la peticion POST en rate()
     */
    private function ratePost()
    {
        // comprueba que se hayan recibido los datos necesarios
        if (!isset($this->request->rating) || !isset($this->request->comment))
            return false;

        // crea modelo con datos proporcionados
        $rating = new \models\Rating(null, $this->request->prod, $this->session->username);
        $rating->rating     = $this->request->rating;
        $rating->commentary = $this->request->comment;

        // valida y almacena en la BD
        return $rating->validate() && $rating->save();
    }

}

?>
