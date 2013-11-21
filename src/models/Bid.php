<?php

namespace models;

class Bid extends Model
{

    private $idBid;
    private $idBidding;
    private $login;
    public  $quantity;
    public  $date;
    public  $idPayment;

    public function __construct($idBid = null, $idBidding = null, $login = null)
    {
        parent::__construct();

        $this->idBid     = $idBid;
        $this->idBidding = $idBidding;
        $this->login     = $login;
        $this->quantity  = null;
        $this->date      = null;
        $this->idPayment = null;
    }

    public static function findBy($where)
    {
        $ids = \database\DAOFactory::getDAO("bid")->select(["idPuja"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $bid = new Bid($id["idPuja"]);
            if (!$bid->fill()) break;
            $found[ ] = $bid;
        }

        return $found;
    }

    public function fill()
    {
        $rows = $this->dao->select(["*"], ["idPuja" => $this->idBid]);
        if (!$rows) return false;

        $this->idBidding = $rows[0]["idSubasta"];
        $this->login     = $rows[0]["login"];
        $this->quantity  = $rows[0]["cantidadPujada"];
        $this->date      = $rows[0]["fechaPuja"];
        $this->idPayment = $rows[0]["idPago"];

        return true;
    }

    public function save()
    {
        $data = [
            "idSubasta" => $this->idBidding,
            "login"     => $this->login,
        ];

        if (isset($this->quantity))  $data["cantidadPuja"] = $this->quantity;
        if (isset($this->idPayment)) $data["idPago"] = $this->idPayment;

        if (isset($this->idBid))
            return $this->dao->update($data, ["idPuja" => $this->idBid]);
        else
            return $this->dao->insert($data);
    }

    public function delete()
    {
        return $this->dao->delete(["idPuja" => $this->idBid]);
    }

    public function validate()
    {
        // TODO: validar que el login existe, validar que la subasta existe 
        // (apoyarse en modelos de usuario y subasta)
        // TODO: validar que la cantidad pujada es superior a la ultima de la 
        // subasta (apoyarse en modelo de subasta)
        // TODO: validar que el pago, SI NO NULO (aka no ultima puja de 
        // subasta) existe (apoyarse en modelo de pago)
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getId()
    {
        return $this->idBid;
    }

    public function getBiddingId()
    {
        return $this->idBidding;
    }

    public function getLogin()
    {
        return $this->login;
    }

}

?>
