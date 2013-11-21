<?php

namespace models;

class Sale extends Model
{

    private $idSale;
    private $idProduct;
    public  $price;
    public  $stock;

    public function __construct($idSale = null, $idProduct = null)
    {
        parent::__construct();

        $this->idSale    = $idSale;
        $this->idProduct = $idProduct;
        $this->price     = null;
        $this->stock     = null;
    }

    public static function findBy($where)
    {
        $ids = \database\DAOFactory::getDAO("sale")->select(["idVenta"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $sale = new Sale($id["idVenta"]);
            if (!$sale->fill()) break;
            $found[ ] = $sale;
        }

        return $found;
    }

    public function fill()
    {
        $rows = $this->dao->select(["*"], ["idVenta" => $this->idVenta]);
        if (!$rows) return false;

        $this->idProduct = $rows[0]["idProducto"];
        $this->price     = $rows[0]["precio"];
        $this->stock     = $rows[0]["stock"];

        return true;
    }

    public function save()
    {
        $data = ["idProducto" => $this->idProduct];

        if (isset($this->price)) $data["precio"] = $this->price;
        if (isset($this->stock)) $data["stock"]  = $this->stock;

        if (isset($this->idSale))
            return $this->dao->update($data, ["idVenta" => $this->idSale]);
        else
            return $this->dao->insert($data);
    }

    public function delete()
    {
        return $this->dao->delete(["idVenta" => $this->idSale]);
    }

    public function validate()
    {
        // TODO: validar que el producto existe (apoyarse en modelo de 
        // productos)
        // TODO: validar que el precio sea superior a 0.0
        // TODO: validar que el stock sea superior a 0
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getId()
    {
        return $this->idSale;
    }

    public function getProductId()
    {
        return $this->idProduct;
    }

}

?>
