<?php

namespace models;

/**
 * Modelo para Pujas. Soporta todas las operaciones basicas que se realizaran 
 * con una puja.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Bid extends Model
{

    // FUTURE FIXME: idBidding, login y idPayment no deberian ser simples 
    // identificadores, sino referencias al resto de modelos. Con mas tiempo se 
    // hubiese hecho asi.

    private $idBid;      // identificador de la puja (auto-incremental)
    private $idBidding;  // identificador de la subasta
    private $login;      // login del usuario que ha realizado la puja
    public  $quantity;   // cantidad pujada
    public  $date;       // fecha (timestamp) de la puja
    public  $idPayment;  // id del pago de la puja (nulo si no hay pago)

    /**
     * Construye una puja a partir de los parametros recibidos.
     *
     * @param int $idBid
     *     Identificador de la puja, null si no creada previamente.
     * @param int $idBidding
     *     Identificador de la subasta, si se ha proporcionado $idBid no sera 
     *     necesario proporcionar esta, puesto que puede ser recuperada de la 
     *     BD.
     * @param String $login
     *     Login del usuario que realiza la puja. Si se ha proporcionado $idBid 
     *     no sera necesario proporcionar este, puesto que puede ser recuperado 
     *     desde la BD.
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
     * Devuelve un array de objetos Bid donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Bid con todas las pujas encontradas que cumplan la 
     *     condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los identificadores de pujas que cumplan la condicion
        $ids = \database\DAOFactory::getDAO("bid")->select(["idPuja"], $where);
        if (!$ids) return array();

        // genera un array de objetos Bid creandolos con los identificadores 
        // anteriores y llamando a fill() para recuperar todos sus datos
        $found = array();
        foreach ($ids as $id) {
            $bid = new Bid($id["idPuja"]);
            if (!$bid->fill()) break;
            $found[ ] = $bid;
        }

        return $found;
    }

    /**
     * Devuelve un array con todas las pujas ganadoras de subastas realizadas 
     * por un usuario dado.
     *
     * @param String $login
     *     El login del usuario para realizar la busqueda de sus pujas 
     *     ganadoras.
     *
     * @return array
     *     Array de Bid con todas las pujas ganadas por el usuario dado.
     */
    public static function findByLoginWhereWin($login)
    {
        // FUTURE FIXME: esto deberia estar en el DAO y no en el modelo
        // consulta parametrizada a realizar para obtener todos los 
        // identificadores de pujas ganadoras realizadas por un usuario
        $query = "SELECT idPuja FROM PUJA NATURAL JOIN SUBASTA
                  WHERE login = ? AND fechaLimite <= ? AND idPago IS NULL";

        // realiza la busqueda de todos los identificadores de pujas que 
        // cumplan la condicion de ser pujas ganadoras realizadas por el 
        // usuario dado
        $ids = \database\DAOFactory::getDAO("bid")->query($query, $login, date("Y-m-d H:i:s"));
        if (!$ids || !is_array($ids)) return array();

        // genera un array de objetos Bid creandolos con los identificadores 
        // anteriores y llamando a fill() para recuperar todos sus datos
        $found = array();
        foreach ($ids as $id) {
            $bid = new Bid($id["idPuja"]);
            if (!$bid->fill()) break;
            $found[ ] = $bid;
        }

        return $found;
    }

    /**
     * Rellena el objeto Bid actual con todos los datos, obteniendolos desde la 
     * base de datos. Es necesario que tenga su atributo "idBid" establecido 
     * (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos da la puja con el identificador asignado
        $rows = $this->dao->select(["*"], ["idPuja" => $this->idBid]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->idBidding = $rows[0]["idSubasta"];
        $this->login     = $rows[0]["login"];
        $this->quantity  = $rows[0]["cantidadPuja"];
        $this->date      = $rows[0]["fechaPuja"];
        $this->idPayment = $rows[0]["idPago"];

        return true;
    }

    /**
     * Almacena la puja en la base de datos. Se encarga de comprobar si se 
     * trata de una nueva insercion o una actualizacion en base a si el 
     * atributo "idBid" esta a null (insercion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente. 
     *     False en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = [
            "idSubasta" => $this->idBidding,
            "login"     => $this->login,
        ];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->quantity))  $data["cantidadPuja"] = $this->quantity;
        if (isset($this->idPayment)) $data["idPago"] = $this->idPayment;

        // si idBid no es null, entonces es un update
        if (isset($this->idBid))
            return $this->dao->update($data, ["idPuja" => $this->idBid]);

        // sino, es un insert
        return $this->dao->insert($data);
    }

    /**
     * Elimina la puja de la base de datos. El objeto debe haber sido 
     * previamente inicializado con el atributo "idBid" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion ha resultado satisfactoria, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idPuja" => $this->idBid]);
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

        // valida que el identificador de subasta se corresponde a una subasta 
        // existente
        $bidding = new Bidding($this->idBidding);
        if (!$bidding->fill()) return false;

        // valida que el login se corresponde a un usuario existente
        $user = new User($this->login);
        if (!$user->fill()) return false;

        // valida que la cantidad pujada sea un numero
        if (!is_numeric($this->quantity)) return false;

        // valida que la cantidad pujada es superior a la puja mas alta 
        // existente en la subasta, o superior al minimo si la subasta aun no 
        // tiene pujas
        if (!isset($this->idBid)) {
            $bid = $bidding->getHighestBid();
            if ($bid !== false) {
                if ($bid->quantity >= $this->quantity) return false;
            } else {
                if ($bidding->minBid >= $this->quantity) return false;
            }
        }

        // valida que si el identificador de pago ha sido proporcionado, se 
        // corresponde a un pago existente
        if (isset($this->idPayment)) {
            $payment = new Payment($this->idPayment);
            if (!$payment->fill()) return false;
        }

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Devuelve el identificador de la puja de esta instancia.
     *
     * @return int
     *     Identificador de la puja a la que hace referencia este objeto de 
     *     modelo.
     */
    public function getId()
    {
        return $this->idBid;
    }

    /**
     * Devuelve el identificador de la subasta de esta instancia.
     *
     * @return int
     *     Identificador de la subasta a la que esta asociada la puja a la que 
     *     hace referencia este objeto de modelo.
     */
    public function getBiddingId()
    {
        return $this->idBidding;
    }

    /**
     * Devuelve el login del usuario de esta instancia.
     *
     * @return String
     *      Login del usuario al que esta asociado la puja a la que hace 
     *      referencia este objeto de modelo.
     */
    public function getLogin()
    {
        return $this->login;
    }

}

?>
