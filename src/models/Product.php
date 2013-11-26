<?php

namespace models;

/**
 * Modelo para Productos. Soporta todas las operaciones basicas que se 
 * realizaran con un producto.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Product extends Model
{

    // FUTURE FIXME: owner no deberia ser un simple identificador, sino una 
    // referencia a un objeto del modelo User. Con mas tiempo se hubiese hecho 
    // asi.

    private $idProduct;    // identificador del producto (auto-incremental)
    private $owner;        // login del propietario del producto
    public  $state;        // estado del producto (pendiente, subasta, venta)
    public  $name;         // nombre del producto
    public  $description;  // descripcion del producto

    /**
     * Construye un producto a partir de los parametros recibidos.
     *
     * @param int $idProduct
     *     Identificador del producto, null si no creado previamente.
     * @param String $owner
     *     Login del propietario del producto, si se ha proporcionado 
     *     $idProduct no sera necesario proporcionar este, puesto que puede ser 
     *     recuperado de la BD.
     */
    public function __construct($idProduct = null, $owner = null)
    {
        parent::__construct();

        $this->idProduct =   $idProduct;
        $this->owner       = $owner;
        $this->state       = null;
        $this->name        = null;
        $this->description = null;
    }

    /**
     * Devuelve un array de objetos Product donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Product con todos los productos encontrados que cumplan la 
     *     condicion establecida.
     */
    public static function findby($where)
    {
        // obtiene todos los identificadores de productos que cumplan la 
        // condicion
        $ids = \database\DAOFactory::getDAO("product")->select(["idProducto"], $where);
        if (!$ids) return array();

        // genera un array de objetos Product creandolos con los 
        // identificadores anteriores y llamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

    /**
     * Devuelve un array con todos los productos con nombre similar a uno 
     * proporcionado (uso de "LIKE" en SQL).
     *
     * @param String $name
     *     Nombre para realizar busqueda de producto.
     *
     * @return array
     *     Array de Product con todos los productos con un nombre coincidente 
     *     o similar al dado.
     */
    public static function findByName($name)
    {
        // realiza la busqueda de todos los identificadores de productos que 
        // tengan un nombre similar al dado (via SQL LIKE)
        $ids = \database\DAOFactory::getDAO("product")->query(
            "SELECT idProducto FROM PRODUCTO WHERE nombre LIKE ?",
            "%" . $name . "%");
        if (!$ids || !is_array($ids)) return array();

        // genera un array de objetos Product creandolos con los 
        // identificadores anteriores y llamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

    /**
     * Devuelve un array con todos los productos que tengan estado no pendiente 
     * (ie: en venta o en subasta).
     *
     * @return array
     *     Array de Product con todos los productos que no esten en estado 
     *     pendiente.
     */
    public static function findByStateAvailable()
    {
        // realiza la busqueda de todos los identificadores de productos que 
        // cumplan la condicion de no estar en estado pendiente
        $ids = \database\DAOFactory::getDAO("product")->query(
            "SELECT idProducto FROM PRODUCTO WHERE estado != ?",
            "pendiente");
        if (!$ids || !is_array($ids)) return array();

        // genera un array de objetos Product creandolos con los 
        // identificadores anteriores y llamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

    /**
     * Rellena el objeto Product con todos los datos, obteniendolos desde la 
     * base de datos. Es necesario que tenga su atributo "idProduct" 
     * establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener todos los datos, False en caso 
     *     contrario.
     */
    public function fill()
    {
        // obtiene todos los datos del producto con el identificador asignado
        $rows = $this->dao->select(["*"], ["idProducto" => $this->idProduct]);
        if (!$rows) return false;

        // rellena los atributos con los datos obtenidos
        $this->owner       = $rows[0]["propietario"];
        $this->state       = $rows[0]["estado"];
        $this->name        = $rows[0]["nombre"];
        $this->description = $rows[0]["descripcion"];

        return true;
    }

    /**
     * Almacena el producto en la base de datos. Se encarga de comprobar si se 
     * trata de una nueva insercion o una actualizacion en base a si el 
     * atributo "idProduct" esta a null (insercion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente, 
     *     False en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = [
            "propietario" => $this->owner,
            "estado"      => $this->state,
            "nombre"      => $this->name
        ];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->description)) $data["descripcion"] = $this->description;

        // si idProduct no es null, entonces es un update
        if (isset($this->idProduct))
            return $this->dao->update($data, ["idProducto" => $this->idProduct]);

        // sino, es un insert
        return $this->dao->insert($data);
    }

    /**
     * Elimina el producto de la base de datos. El objeto debe haber sido 
     * previamente creado con el atributo "idProduct" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idProducto" => $this->idProduct]);
    }

    /**
     * Valida los datos existentes en el objeto, para comprobar que cumplan 
     * una serie de condiciones concretas.
     *
     * @return boolean
     *     True si todas las condiciones necesarias han sido cumplidas, False 
     *     en caso contrario.
     */
    public function validate()
    {
        // FUTURE FIXME: no se deberia retornar un simple true/false, sino que 
        // si una condicion no se cumple deberia devolverse un mensaje 
        // indicando qué no se ha cumplido, posiblemente usando una excepcion 
        // para cada caso, para que el controlador la capture y muestre el 
        // mensaje adecuado a la vista y no un simple mensaje de error 
        // generico.

        // name solo puede tener letras, números y guiones
        if (!filter_var($this->name, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-zA-Z0-9\-]+/"]]))
            return false;

        // descrpicion debe limpiarse para prevenir ataques XSS
        $this->description = filter_var($this->description, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        // nombre debe tener como minimo 4 caracteres y como maximo 255
        if (strlen($this->name) <4 || strlen($this->name) > 255)
            return false;

        // estado debe ser "pendiente", "subasta" o "venta" exclusivamente
        if ($this ->state !==  "pendiente" && $this->state !== "subasta" && $this->state !== "venta")
            return false;

        // el propietario debe existir como usuario en la BD
        $user = new User($this->owner);
        if (!$user->fill()) return false;

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Devuelve el identificador del producto de esta instancia.
     *
     * @return int
     *     Identificador del producto al que hace referencia este objeto de 
     *     modelo.
     */
    public function getId()
    {
        return $this->idProduct;
    }

    /**
     * Devuelve el login del propietario del producto de esta instancia.
     *
     * @return String
     *     Login del propietario del producto al que hace referencia este 
     *     objeto de modelo.
     */
    public function getOwner()
    {
        return $this->owner;
    }

}

?>
