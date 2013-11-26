<?php

namespace models;

/**
 * Modelo para Compras. Soporta todas las operaciones basicas que se realizaran 
 * con una compra.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Purchase extends Model
{

    // FUTURE FIXME: idSale, login y idPayment no deberian ser simples 
    // identificadores, sino referencias al resto de modelos. Con mas tiempo se 
    // hubiese hecho asi.

    private $idPurchase;  // identificador de la compra (auto-incremental)
    private $idSale;      // identificador de la venta
    private $login;       // login del usuario que realiza la compra
    public  $quantity;    // cantidad comprada
    public  $date;        // fecha (timestamp) de la compra
    public  $idPayment;   // id del pago de la compra

    /**
     * Construye una compra a partir de los parametros recibidos.
     *
     * @param int $idPurchase
     *     Identificador de la compra, null si no creada previamente.
     * @param int $idSale
     *     Identificador de la venta, si se ha proporcionado $idPurchase no 
     *     sera necesario proporcionar esta, puesto que puede ser recuperada de 
     *     la BD.
     * @param String $login
     *     Login del usuario que realiza la compra. Si se ha proporcionado 
     *     $idPurchase no sera necesario proporcionar este, puesto que puede 
     *     ser recuperado desde la BD.
     */
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

    /**
     * Devuelve un array de objetos Purchase donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Purchase con todas las compras rencontradas que cumplan la 
     *     condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los identificadores de compras que cumplan la 
        // condicion
        $ids = \database\DAOFactory::getDAO("purchase")->select(["idCompra"], $where);
        if (!$ids) return array();

        // genera un array de objetos Purchase creandolos con los 
        // identificadores anteriores y llamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $purchase = new Purchase($id["idCompra"]);
            if (!$purchase->fill()) break;
            $found[ ] = $purchase;
        }

        return $found;
    }

    /**
     * Rellena el objeto Purhcase actual con todos los datos, obteniendolos 
     * desde la base de datos. Es necesario que tenga su atributo "idPurchase" 
     * establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos de la compra con el identificador asignado
        $rows = $this->dao->select(["*"], ["idCompra" => $this->idPurchase]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->idSale    = $rows[0]["idVenta"];
        $this->login     = $rows[0]["login"];
        $this->quantity  = $rows[0]["cantidad"];
        $this->date      = $rows[0]["fechaCompra"];
        $this->idPayment = $rows[0]["idPago"];

        return true;
    }

    /**
     * Almacena la compra en la base de datos. Se encarga de comprobar si se 
     * trata de una nueva insercion o una actualizacion en base a si el 
     * atributo "idPurchase" esta a null (insecion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente. 
     *     False en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = [
            "idVenta" => $this->idSale,
            "login"   => $this->login
        ];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->quantity))  $data["cantidad"] = $this->quantity;
        if (isset($this->idPayment)) $data["idPago"]   = $this->idPayment;

        // si idPurchase no es null, entonces es un update
        if (isset($this->idPurchase))
            return $this->dao->update($data, ["idCompra" => $this->idPurchase]);

        // sino, es un insert
        return $this->dao->insert($data);
    }

    /**
     * Elimina la compra de la base de datos. El objeto debe haber sido 
     * previamente inicializado con el atributo "idPurchase" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion ha resultado satisfactoria, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idCompra" => $this->idPurchase]);
    }

    /**
     * Valida los datos existentes en el objeto, para comprobar que cumplan 
     * una serie de condiciones concretas.
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

        // valida que existe un usuario con el login proporcionado
        $user = new User($this->login);
        if (!$user->fill()) return false;

        // valida que existe una venta con el identificador proporcionado
        $sale = new Sale($this->idSale);
        if (!$sale->fill()) return false;

        // valida que la cantidad es mayor que 0 y menor o igual al 
        // stock del producto
        if (!is_numeric($this->quantity)) return false;
        if ($this-quantity > $sale->stock || $this->quantity <= 0)
            return false;

        // valida que el pago existe
        if (isset($this->idPayment)) {
            $payment = new Payment($this->idPayment);
            if (!$payment->fill()) return false;
        }

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Devuelve el identificador de la compra de esta instancia.
     *
     * @return int
     *     Identificador de la compra a la que hace referencia este objeto de 
     *     modelo.
     */
    public function getId()
    {
        return $this->idPurchase;
    }

    /**
     * Devuelve el identificador de la venta de esta instancia.
     *
     * @return int
     *     Identificador de la venta a la que esta asociada la compra a la que 
     *     hace referencia este objeto de modelo.
     */
    public function getSaleId()
    {
        return $this->idSale;
    }

    /**
     * Devuelve el login del usuario de esta instancia.
     *
     * @return String
     *     Login del usuario al que esta asociada la compra a la que hace 
     *     referencia este objeto.
     */
    public function getLogin()
    {
        return $this->login;
    }

}

?>
