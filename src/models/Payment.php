<?php

namespace models;

/**
 * Modelo para Pagos. Soporta todas las operaciones basicas que se realizaran
 * con un pago.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Payment extends Model
{

    // TODO: en lugar de almacenar un atributo para el metodo de pago y dos
    // atributos segun los datos, aprovechar el polimorfismo creando dos
    // "sub-modelos" de Pago, uno para PayPal y otro para Tarjeta de Credito,
    // evitando asi ademas la necesidad de las condiciones estilo "if
    // ($this->payMethod === "paypal") ..."

    private $idPayment;   // identificador del pago (auto-incremental)
    public  $payMethod;   // metodo de pago
    public  $creditCard;  // tarjeta de credito (si metodo de pago es tarjeta)
    public  $paypal;      // cuenta de paypal (si metodo de pago es paypal)
    public  $commission;  // comision obtenida por la tienda en este pago

    /**
     * Construye un pago a partir de los parametros recibidos.
     *
     * @param int $idPayment
     *     Identificador del pago, null si no creado previamente.
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
     * Devuelve un array de objetos Payment donde todos ellos cumplen una
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Payment con todos los pagos encontrados que cumplan la
     *     condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los identificadores de pagso que cumplan la condicion
        $ids = \database\DAOFactory::getDAO("payment")->select(["idPago"], $where);
        if (!$ids) return array();

        // genera un array de objetos Payment creandolos con los
        // identificadores anteriores y llamando a fill() para recuperar todos
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $payment = new Payment($id["idPago"]);
            if (!$payment->fill()) break;
            $found[ ] = $payment;
        }

        return $found;
    }

    /**
     * Rellena el objeto Payment actual con todos los datos, obteniendolos
     * desde la base de datos. Es necesario que tenga su atributo "idPayment"
     * establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos del pago con el identificador asignado
        $rows = $this->dao->select(["*"], ["idPago" => $this->idPayment]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->payMethod  = $rows[0]["metodoPago"];
        $this->creditCard = $rows[0]["numTarjeta"];
        $this->paypal     = $rows[0]["cuentaPaypal"];
        $this->commission = $rows[0]["comision"];

        return true;
    }

    /**
     * Almacena el pago en la base de datos. Se encarga de comprobar si se
     * trata de una nueva insercion o una actualizacion en base a si el
     * atributo "idPayment" esta a null (insercion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente,
     *     False en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = [
            "metodoPago" => $this->payMethod,
            "comision"   => $this->commission
        ];

        // datos opcionales, que pueden no estar establecidos para la
        // insercion/modificacion
        if (isset($this->creditCard)) $data["numTarjeta"]   = $this->creditCard;
        if (isset($this->paypal))     $data["cuentaPaypal"] = $this->paypal;

        // si idPayment no es null, entonces es un update
        if (isset($this->idPayment))
            return $this->dao->update($data, ["idPago" => $this->idPayment]);

        // sino, es un insert
        $ret = $this->dao->insert($data);

        // FUTURE FIXME: esto es un hack muy feo, deberia crearse un metodo
        // en SQLDAO para recuperar el ultimo ID, y no invocar a la
        // conexion a BD desde aqui, pero no queda tiempo para corregirlo
        // ahora
        $this->idPayment = \database\DatabaseConnection::getConnection()->lastInsertId()

        return $ret;
    }

    /**
     * Elimina el pago de la base de datos. El objeto debe haber sido 
     * previamente incializado con el atributo "idPayment" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idPago" => $this->idPayment]);
    }

    /**
     * Valida los datos existentes en el objeto, para comprobar que cumplan una 
     * serie de condiciones concretas.
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

        // valida que el metodo de pago sea o "paypal" o "tarjeta"
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

        // valida que la tarjeta de credito tenga longitud 16
        if ($this->payMethod === "tarjeta" && strlen($this->creditCard) != 16)
            return false;

        // valida que la comision es un numero y es superior a 0.0
        if (!is_numeric($this->commission) || $this->commission <= 0.0)
            return false;

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Devuelve el identificador del pago de esta instancia.
     *
     * @return int
     *     Identificador del pago al que hace referencia este objeto de modelo.
     */
    public function getId()
    {
        return $this->idPayment;
    }

}

?>
