<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Pagos.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Payment extends Model
{

    private $idPayment;
    public  $payMethod;
    public  $creditCard;
    public  $paypal;
    public  $commission;

    /**
     * Construye una nueva instancia de Payment a partir de los datos
     * recibidos como parámetros
     */
    public function __construct($idPayment = null)
    {
        parent::__construct();

        $this->idPayment  = $idPayment;
        $this->payMethod  = null;
        $this->creditCard = null;
        $this->paypal     = null;
        $this->commission = null;
    }

    /**
     * Devuelve un array con todos los pagos que
     * coincidan con los parámetros de la búsqueda SQL
     *
     * @param array $where
     *      Contiene las condiciones para la búsqueda SQL
     *
     * @return array $found
     *     Devuelve los resultados de la búsqueda SQL
     */
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
        $rows = $this->dao->select(["*"], ["idPago" => $this->idPayment]);
        if (!$rows) return false;

        $this->payMethod  = $rows[0]["metodoPago"];
        $this->creditCard = $rows[0]["numTarjeta"];
        $this->paypal     = $rows[0]["cuentaPaypal"];
        $this->commission = $rows[0]["comision"];

        return true;
    }

    /**
     * Guarda el pago en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
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

    /**
     * Elimina el pago de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idPago" => $this->idPayment]);
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
        // valida que el metodo de pago es o "paypal" o "tarjeta"
        if ($this->payMethod !=="paypal" && $this->payMethod !== "tarjeta")
            return false;

        // valida que, segun metodo de pago, o bien el numero de tarjeta 
        // o bien la cuenta de paypal ha sido introducida
        if ($this->payMethod === "paypal" && !isset($this->paypal))
            return false;
        elseif ($this->payMethod === "tarjeta" && !isset($this->creditCard))
            return false;

        // valida que cuenta de paypal es un email valido
        if ($this->payMethod === "paypal" && !filter_var($this->paypal, FILTER_VALIDATE_EMAIL))
            return false;
        if ($this->payMethod === "tarjeta" && strlen($this->creditCard) != 16)
            return false;


        // valida que la comision es un valor valido
        if ($this->commission <= 0.0)
            return false;

        return true;
    }

    /**
     * Devuelve el id del pago
     *
     * @return string $idPayment
     *     El id del pago
     */
    public function getId()
    {
        return $this->idPayment;
    }

}

?>
