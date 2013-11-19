<?php

namespace models;

class Purchase extends Model
{

    private $idPurchase;
    private $idSale;
    private $login;
    public  $quantity;
    public  $date;
    public  $idPayment;

    public function __construct($idPurchase = null, $idSale = null, $login = null)
    {
        parent::__construct();

        $this->idPurchase = $idPurchase;
        $this->idSale     = $idSale;
        $this->login      = $login;
        $this->quantity   = null;
        $this->date       = null;
        $this->idPayment  = null;
    }

    public static function findBy($where)
    {
        $ids = \database\DAOFactory::getDAO("purchase")->select(["idCompra"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $purchase = new Purchase($id);
            if (!$purchase->fill()) break;
            $found[ ] = $purchase;
        }

        return $found;
    }

    public function fill()
    {
        $rows = $this->dao->select(["*"], ["idCompra" => $this->idPurchase]);
        if (!$rows) return false;

        $this->idProduct = $rows[0]["idProducto"];
        $this->login     = $rows[0]["login"];
        $this->quantity  = $rows[0]["cantidad"];
        $this->date      = $rows[0]["fechaCompra"];
        $this->idPayment = $rows[0]["idPago"];

        return true;
    }

    public function save()
    {
        $data = [
            "idProducto" => $this->idProduct,
            "login"      => $this->login
        ];

        if (isset($this->quantity))  $data["cantidad" => $this->quantity];
        if (isset($this->idPayment)) $data["idPago" => $this->idPago];

        if (isset($this->idPurchase))
            return $this->dao->update($data, ["idCompra" => $this->idPurchase]);
        else
            return $this->dao->insert($data);
    }

    public function delete()
    {
        return $this->dao->delete(["idCompra" => $this->idPurchase]);
    }

    public function validate()
    {
        // TODO: validar que el login existe, validar que el producto existe
        // (apoyarse en modelos de usuario y producto)
        // TODO: validar que la cantidad es mayor que 0 y menor o igual al 
        // stock del producto (apoyarse en modelo de producto)
        // TODO: validar que el pago, SI NO NULO (aka pendiente de pago), 
        // existe (apoyarse en modelo de pago)
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getId()
    {
        return $this->idPurchase;
    }

    public function getProductId()
    {
        return $this->idProudct;
    }

    public function getLogin()
    {
        return $this->login;
    }

}


?>
