<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Compras.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Purchase extends Model
{

    private $idPurchase;
    private $idSale;
    private $login;
    public  $quantity;
    public  $date;
    public  $idPayment;

    /**
     * Construye una nueva instancia de Purchase a partir de los datos
     * recibidos como parámetros
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
     * Devuelve un array con todas las compras que
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
        $ids = \database\DAOFactory::getDAO("purchase")->select(["idCompra"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $purchase = new Purchase($id["idCompra"]);
            if (!$purchase->fill()) break;
            $found[ ] = $purchase;
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
        $rows = $this->dao->select(["*"], ["idCompra" => $this->idPurchase]);
        if (!$rows) return false;

        $this->idSale    = $rows[0]["idVenta"];
        $this->login     = $rows[0]["login"];
        $this->quantity  = $rows[0]["cantidad"];
        $this->date      = $rows[0]["fechaCompra"];
        $this->idPayment = $rows[0]["idPago"];

        return true;
    }

    /**
     * Guarda la compra en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
    public function save()
    {
        $data = [
            "idVenta" => $this->idSale,
            "login"   => $this->login
        ];

        if (isset($this->quantity))  $data["cantidad"] = $this->quantity;
        if (isset($this->idPayment)) $data["idPago"]   = $this->idPayment;

        if (isset($this->idPurchase))
            return $this->dao->update($data, ["idCompra" => $this->idPurchase]);
        else
            return $this->dao->insert($data);
    }

    /**
     * Elimina la compra de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idCompra" => $this->idPurchase]);
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
        // valida que el login existe, validar que la venta existe
        $user = new User($this->login);
        if (!$user->fill()) return false;
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

        return true;
    }

    /**
     * Devuelve el id de la compra
     *
     * @return string $idPurchase
     *     El id de la compra
     */
    public function getId()
    {
        return $this->idPurchase;
    }

    /**
     * Devuelve el id de la compra
     *
     * @return string $idSale
     *     El id de la compra
     */
    public function getSaleId()
    {
        return $this->idSale;
    }

    /**
     * Devuelve el el id del usuario
     *
     * @return string $login
     *     El id del usuario
     */
    public function getLogin()
    {
        return $this->login;
    }

}


?>
