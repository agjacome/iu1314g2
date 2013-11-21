<?php

namespace models;

class Product extends Model
{
    private $idProduct;
    private $owner;
    public  $state;
    public  $name;
    public  $description;

    public function __construct($idProduct = null, $owner = null)
    {
        parent::__construct();

        $this->idProduct =   $idProduct;
        $this->owner       = $owner;
        $this->state       = null;
        $this->name        = null;
        $this->description = null;
    }

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

    public static function findByName($name)
    {
        $ids = \database\DAOFactory::getDAO("product")->query(
            "SELECT idProducto FROM PRODUCTO WHERE nombre LIKE ?",
            "%" . $name . "%");
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $product = new Product($id["idProducto"]);
            if (!$product->fill()) break;
            $found[ ] = $product;
        }

        return $found;
    }

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

    public function delete()
    {
        return $this->dao->delete(["idProducto" => $this->idProduct]);
    }

    public function validate()
    {
        // name solo puede tener letras, números y guiones
        if(!filter_var($this->name, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-zA-Z0-9\-]+/"]]))
            return false;

        // FIXME: descripcion puede tener cualquier caracter, pero debe ser 
        // limpiada contra ataques XSS (ver FILTER_SANITIZE_STRING)
        //descripcion puede tener letras, numeros, guiones y guiones bajos
        if(!filter_var($this->description, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-zA-Z0-9\-_]+/"]]))
            return false;

        // TODO: nombre debe tener como minimo 4 caracteres y como maximo 255
        // TODO: estado debe ser "pendiente", "subasta" o "venta" 
        // exclusivamente
        // TODO: propietario debe existir (apoyarse en modelo usuario para 
        // comprobacion)
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getId()
    {
        return $this->idProduct;
    }

    public function getOwner()
    {
        return $this->owner;
    }

}

?>
