<?php

namespace models;

class Bidding extends Model
{

    private $idBidding;
    private $idProduct;
    public  $minBid;
    public  $limitDate;

    public function __construct($idBidding = null, $idProduct = null)
    {
        parent::__construct();

        $this->idBidding = $idBidding;
        $this->idProduct = $idProduct;
        $this->minBid    = null;
        $this->limitDate = null;
    }

    public static function findBy($where)
    {
        $ids = \database\DAOFactory::getDAO("bidding")->select(["idSubasta"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $bidding = new Bidding($id["idSubasta"]);
            if (!$bidding->fill()) break;
            $found[ ] = $bid;
        }

        return $found;
    }

    public function fill()
    {
        $rows = $this->dao->select(["*"], ["idSubasta" => $this->idBidding]);
        if (!$rows) return false;

        $this->idProduct = $rows[0]["idProducto"];
        $this->minBid    = $rows[0]["pujaMinima"];
        $this->limitDate = $rows[0]["fechaLimite"];

        return true;
    }

    public function save()
    {
        $data = ["idProducto" => $this->idProduct];

        if (isset($this->minBid))    $data["pujaMinima"]  = $this->minBid;
        if (isset($this->limitDate)) $data["fechaLimite"] = $this->limitDate;

        if (isset($this->idBidding))
            return $this->dao->update($data, ["idSubasta" => $this->idBidding]);
        else
            return $this->dao->insert($data);
    }

    public function delete()
    {
        return $this->dao->delete(["idSubasta" => $this->idBidding]);
    }

    public function validate()
    {
        // TODO: validar que el producto existe (apoyarse en modelo de
        // productos)
        // TODO: validar que la puja minima sea superior a 0.0
        // TODO: validar que la fecha limite sea posterior a la fecha de
        // creacion (actual)
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getId()
    {
        return $this->idBidding;
    }

    public function getProductId()
    {
        return $this->idProduct;
    }

    public function getHighestBid()
    {
        // findBy proporcionara resultado ordenado por clave primaria
        // (comportamiento por defecto de BD), y dado que los ids son
        // incrementales, la puja mas alta sera la ultima del array
        $bids = Bid::findBy(["idSubasta" => $this->idBidding]);

        if (count($bids) === 0)
            return false;

        return end($bids);
    }

}

?>
