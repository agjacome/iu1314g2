<?php

namespace models;

/**
 * Modelo para Subastas. Soporta todas las operaciones basicas que se 
 * realizaran con una subasta.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Bidding extends Model
{

    // FUTURE FIXME: idProduct no deberia ser un simple identificador, sino una 
    // referencia a un objeto del modelo Product. Con mas tiempo se hubiese 
    // hecho asi.

    private $idBidding;  // identificador de la subasta (auto-incremental)
    private $idProduct;  // identificador del producto
    public  $minBid;     // puja minima para la subasta
    public  $limitDate;  // fecha limite para la subasta

    /**
     * Construye una subasta a partir de los parametros recibidos.
     *
     * @param int $idBidding
     *     Identificador de la subasta, null si no creada previamente.
     * @param int $idProduct
     *     Identificador del producto, si se ha proporcionado $idBidding no 
     *     sera necesario proporcionar este, puesto que puede ser recuperado 
     *     de la BD.
     */
    public function __construct($idBidding = null, $idProduct = null)
    {
        parent::__construct();

        $this->idBidding = $idBidding;
        $this->idProduct = $idProduct;
        $this->minBid    = null;
        $this->limitDate = null;
    }

    /**
     * Devuelve un array de objetos Bidding donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Bidding con todas las subastas encontradas que cumplan la 
     *     condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los identificadores de subastas que cumplan la 
        // condicion
        $ids = \database\DAOFactory::getDAO("bidding")->select(["idSubasta"], $where);
        if (!$ids) return array();

        // genera un array de objetos Bidding creandolos con los 
        // identificadores anteriores y llamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $bidding = new Bidding($id["idSubasta"]);
            if (!$bidding->fill()) break;
            $found[ ] = $bidding;
        }

        return $found;
    }

    /**
     * Rellena el objeto Bidding actual con todos los datos, obteniendolos 
     * desde la base de datos. Es necesario que tenga su atributo "idBidding" 
     * establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos de la subasta con el identificador asignado
        $rows = $this->dao->select(["*"], ["idSubasta" => $this->idBidding]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->idProduct = $rows[0]["idProducto"];
        $this->minBid    = $rows[0]["pujaMinima"];
        $this->limitDate = $rows[0]["fechaLimite"];

        return true;
    }

    /**
     * Rellena el objeto Bidding actual con todos los datos, obteniendolos 
     * desde la base de datos. A diferencia de fill(), utiliza para ello el 
     * atrbuto "idProduct", por tanto es necesario que este este establecido 
     * (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fromProduct()
    {
        // obtiene todos los datos de la subasta con el identificador de 
        // producto asignado (un producto no puede tener mas de una subasta, 
        // por tanto se puede explotar esta caracteristica para rellenar los 
        // datos a partir del id de producto)
        $rows = $this->dao->select(["*"], ["idProducto" => $this->idProduct]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->idBidding = $rows[0]["idSubasta"];
        $this->minBid    = $rows[0]["pujaMinima"];
        $this->limitDate = $rows[0]["fechaLimite"];

        return true;
    }

    /**
     * Almacena la subasta en la base de datos. Se encarga de comprobar si se 
     * trata de una nueva insercion o una actualizacion en base a si el 
     * atributo "idBidding" esta a null (insercion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente, 
     *     False en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = ["idProducto" => $this->idProduct];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->minBid))    $data["pujaMinima"]  = $this->minBid;
        if (isset($this->limitDate)) $data["fechaLimite"] = $this->limitDate;

        // si idBidding no es null, entonces es un update
        // TODO: no se dara el caso, las subastas no son modificables una vez 
        // creadas, eliminar esta parte
        if (isset($this->idBidding))
            return $this->dao->update($data, ["idSubasta" => $this->idBidding]);

        // sino, es un insert
        return $this->dao->insert($data);
    }

    /**
     * Elimina la subasta de la base de datos. El objeto debe haber sido 
     * previamente creado con el atributo "idBidding" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion ha resultado satisfactoria, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idSubasta" => $this->idBidding]);
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

        // valida que el identificador de producto se corresponda a un producto 
        // existente
        $product = new Product($this->idProduct);
        if(!$product->fill()) return false;

        // validar que la puja minima sea un numero y sea superior a 0.0
        if (!is_numeric($this-minBid) || $this->minBid <= 0.0)
            return false;

        // valida que la fecha limite sea superior a la fecha actual (correcto 
        // puesto que las subastas no son modificables, solo se comprobara en 
        // el momento de creacion)
        if (date("Y-m-d H:i:s") >= $this->limitDate)
            return false;

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Devuelve el identificador de la subasta de esta instancia.
     *
     * @return int
     *     Identificador de la subasta a la que hace referencia este objeto de 
     *     modelo.
     */
    public function getId()
    {
        return $this->idBidding;
    }

    /**
     * Devuelve el identificador del producto de esta instancia.
     *
     * @return int
     *     Identificador del producto al que esta asociado la subasta a la que 
     *     hace referencia este objeto de modelo.
     */
    public function getProductId()
    {
        return $this->idProduct;
    }

    /**
     * Devuelve la puja mas alta hasta el momento de la subasta.
     *
     * @return mixed Bid/False
     *     Objeto Bid haciendo referencia a la puja mas alta si existen pujas, 
     *     False si aun no existen pujas para esta subasta.
     */
    public function getHighestBid()
    {
        // TODO: obtener un array con TODAS las pujas de la subasta puede 
        // llegar a ser un problema, deberia optimizarse esto para obtener 
        // exclusivamente la ultima, haciendo uso de un $dao->query() y una 
        // sentencia SQL apropiada. E incluso metiendo un metodo en el DAO de 
        // Bidding para realizar esta operacion.

        // obtiene todas las pujas de esta subasta
        $bids = Bid::findBy(["idSubasta" => $this->idBidding]);

        // si no hay pujas, retorna false
        if (count($bids) === 0) return false;

        // findBy proporcionara resultado ordenado por clave primaria
        // (comportamiento por defecto de BD), y dado que los ids son
        // incrementales, la puja mas alta sera la ultima del array
        return end($bids);
    }

}

?>
