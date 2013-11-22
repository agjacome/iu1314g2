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
            $payment = new Payment($id["idPago"]);
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
        else {
            $ret = $this->dao->insert($data);
            // FIXME: esto es un hack muy feo, deberia crearse un metodo en 
            // SQLDAO para recuperar el ultimo ID, y no invocar a la conexion a 
            // BD desde aqui, pero no queda tiempo para corregirlo ahora
            $this->idPayment = \database\DatabaseConnection::getConnection()->lastInsertId();
            return $ret;
        }
    }

    public function delete()
    {
        return $this->dao->delete(["idPago" => $this->idPayment]);
    }

    public function validate()
    {
        // valida que el metodo de pago es o "paypal" o "tarjeta"
        if ($this->payMethod !=="paypal" && $this->payMethod !== "tarjeta")
            return false;

        // valida que, segun metodo de pago, o bien el numero de tarjeta 
        // o bien la cuenta de paypal ha sido introducida
        if ($this->payMethod === "paypal" && !isset($this->paypal))
            return false;
        elseif ($this->payMethod === "tarjeta" && !isset($this->tarjeta))
            return false;

        // valida que cuenta de paypal es un email valido
        if ($this->payMethod === "paypal" && !filter_var($this->paypal, FILTER_VALIDATE_EMAIL))
                return false;

        // valida que la comision es un valor valido
        if ($this->commission <= 0.0)
            return false;

        return true;
    }

    public function getId()
    {
        return $this->idPayment;
    }

}

?>
