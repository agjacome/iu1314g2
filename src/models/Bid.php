<?php

namespace models;
/**
 * Clase que proporciona soporte para manejar Pujas.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */

class Bid extends Model
{

    private $idBid;
    private $idBidding;
    private $login;
    public  $quantity;
    public  $date;
    public  $idPayment;

    /**
     * Construye una nueva instancia de Bid a partir de los datos
     * recibidos como parámetros
     */
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

    /**
     * Devuelve un array con todas las pujas que
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

    /**
     * Devuelve un array con todas las subastas que
     * coincidan con los parámetros de la búsqueda SQL
     *
     * @param array $where
     *      Contiene las condiciones para la búsqueda SQL
     *
     * @return array $found
     *     Devuelve los resultados de la búsqueda SQL
     */
    public static function findByIdSubasta($where)
    {
        $ids = \database\DAOFactory::getDAO("bid")->select(["idSubasta"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $bidding = new Bidding($id["idSubasta"]);
            if (!$bidding->fill()) break;
            $found[ ] = $bidding;
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
        $rows = $this->dao->select(["*"], ["idPuja" => $this->idBid]);
        if (!$rows) return false;

        $this->idBidding = $rows[0]["idSubasta"];
        $this->login     = $rows[0]["login"];
        $this->quantity  = $rows[0]["cantidadPujada"];
        $this->date      = $rows[0]["fechaPuja"];
        $this->idPayment = $rows[0]["idPago"];

        return true;
    }

    /**
     * Guarda la puja en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
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

    /**
     * Elimina la puja de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idPuja" => $this->idBid]);
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
        // valida que el login existe, validar que la subasta existe 
        $user = new User($this->login);
        if (!$user->fill()) return false;
        $bidding = new Bidding($this->idBidding);
        if (!$bidding->fill()) return false;

        // valida que la cantidad pujada es superior a la ultima de la 
        // subasta (apoyarse en modelo de subasta)
        $bid = $bidding->getHighestBid();
        if ($bid !== false) {
            if ($bid->quantity >= $this->quantity) return false;
        } else {
            if ($bidding->minBid >= $this->quantity) return false;
        }

        // validar que el pago, SI NO NULO, existe
        if (isset($this->idPayment)) {
            $payment= new Payment($this->idPayment);
            if (!$payment->fill()) return false;
        }

        return true;
    }

    /**
     * Devuelve el id de la puja
     *
     * @return string $idBid
     *     El id de la puja
     */
    public function getId()
    {
        return $this->idBid;
    }

    /**
     * Devuelve el id de la subasta a la
     * que pertenece la puja
     *
     * @return string $idBidding
     *     El id de la subasta
     */
    public function getBiddingId()
    {
        return $this->idBidding;
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
