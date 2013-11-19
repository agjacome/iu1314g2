<?php

namespace models;

class Payment extends Model
{

    private $idPayment;
    public  $payMethod;
    public  $creditCard;
    public  $paypal;
    public  $commission;

    public function __construct($idPayment = null)
    {
        parent::__construct();

        $this->idPayment  = $idPayment;
        $this->payMethod  = null;
        $this->creditCard = null;
        $this->paypal     = null;
        $this->commission = null;
    }

    public static function findBy($where)
    {
        $ids = \database\DAOFactory::getDAO("payment")->select(["idPago"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $payment = new Payment($id);
            if (!$payment->fill()) break;
            $found[ ] = $payment;
        }

        return $found;
    }

    public function fill()
    {
        $rows = $this->dao->select(["*"], ["idPago" => $this->idPayment]);
        if (!$rows) return false;

        $this->payMethod  = $rows[0]["metodoPago"];
        $this->creditCard = $rows[0]["numTarjeta"];
        $this->paypal     = $rows[0]["cuentaPaypal"];
        $this->commission = $rows[0]["comision"];

        return true;
    }

    public function save()
    {
        $data = [
            "metodoPago" => $this->payMethod,
            "comision"   => $this->commission
        ];

        if (isset($this->creditCard)) $data["numTarjeta"]   = $this->creditCard;
        if (isset($this->paypal))     $data["cuentaPaypal"] = $this->paypal;

        if (isset($this->idPayment))
            return $this->dao->update($data, ["idPago" => $this->idPayment]);
        else
            return $this->dao->insert($data);
    }

    public function delete()
    {
        return $this->dao->delete(["idPago" => $this->idPayment]);
    }

    public function validate()
    {
        // TODO: validar que el metodo de pago es o "paypal" o "tarjeta"
        // TODO: validar que, segun metodo de pago, o bien el numero de tarjeta 
        // o bien la cuenta de paypal ha sido introducida
        // TODO: validar que tarjeta de credito es un numero de tarjeta de 
        // credito valido
        // TODO: validar que cuenta de paypal es un email valido
        // TODO: validar que la comision es un valor valido
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getId()
    {
        return $this->idPayment;
    }

}

?>
