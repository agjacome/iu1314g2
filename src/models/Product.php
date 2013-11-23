<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar productos.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Product extends Model
{
    private $idProduct;
    private $owner;
    public  $state;
    public  $name;
    public  $description;

    /**
     * Construye una nueva instancia de Product a partir de los datos
     * recibidos como parámetros
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
     * Devuelve un array con todos los productos que
     * coincidan con los parámetros de la búsqueda SQL
     *
     * @param array $where
     *      Contiene las condiciones para la búsqueda SQL
     *
     * @return array $found
     *     Devuelve los resultados de la búsqueda SQL
     */
    public static function findby($where)
    {
        $ids = \database\DAOFactory::getDAO("product")->select(["idProducto"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

    /**
     * Devuelve un array con todos los productos que
     * coincidan con el nombre dado
     *
     * @param string $name
     *      El nombre del producto a buscar
     *
     * @return array $found
     *     Devuelve los resultados de la búsqueda SQL
     */
    public static function findByName($name)
    {
        $ids = \database\DAOFactory::getDAO("product")->query(
            "SELECT idProducto FROM PRODUCTO WHERE nombre LIKE ?",
            "%" . $name . "%");
        if (!$ids || !is_array($ids)) return array();

        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

    /**
     * Devuelve un array con todos los productos que
     * estén disponibles
     *
     * @return array $found
     *     Devuelve los resultados de la búsqueda SQL
     */
    public static function findByStateAvailable()
    {
        $ids = \database\DAOFactory::getDAO("product")->query(
            "SELECT idProducto FROM PRODUCTO WHERE estado != ?",
            "pendiente");
        if (!$ids || !is_array($ids)) return array();

        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

    /**
     * Rellena el objeto con los datos obtenidos
     * de la base de datos
     *
     * @return boolean
     *     True si se encuentran los datos en la
     *      base de datos
     */
    public function fill()
    {
        $rows = $this->dao->select(["*"], ["idProducto" => $this->idProduct]);
        if (!$rows) return false;

        $this->owner       = $rows[0]["propietario"];
        $this->state       = $rows[0]["estado"];
        $this->name        = $rows[0]["nombre"];
        $this->description = $rows[0]["descripcion"];

        return true;
    }

    /**
     * Guarda el producto en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
    public function save()
    {
        $data = [
            "propietario" => $this->owner,
            "estado"      => $this->state,
            "nombre"      => $this->name
        ];

        if (isset($this->description)) $data["descripcion"] = $this->description;

        if (isset($this->idProduct))
            return $this->dao->update($data, ["idProducto" => $this->idProduct]);
        else
            return $this->dao->insert($data);
    }

    /**
     * Elimina el producto de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idProducto" => $this->idProduct]);
    }

    /**
     * Valida los datos que introduce el usuario
     *
     * @return boolean
     *     False si alguno de los datos es incorrecto
     *      o no cumple los requisitos requeridos
     */
    public function validate()
    {
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

        // propietario debe existir (apoyarse en modelo usuario para comprobacion)
        $user = new User($this->owner);
        if (!$user->fill())
            return false;

        return true;
    }

    /**
     * Devuelve el id del producto
     *
     * @return string $idProduct
     *     El id del producto
     */
    public function getId()
    {
        return $this->idProduct;
    }

    /**
     * Devuelve el id del propietario
     *
     * @return string $owner
     *     El id del propietario
     */
    public function getOwner()
    {
        return $this->owner;
    }

}

?>
