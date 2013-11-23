<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Subastas.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Bidding extends Model
{

    private $idBidding;
    private $idProduct;
    public  $minBid;
    public  $limitDate;

    /**
     * Construye una nueva instancia de Bidding a partir de los datos
     * recibidos como parámetros
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
     * Devuelve un array con todas las subastas que
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
        $ids = \database\DAOFactory::getDAO("bidding")->select(["idSubasta"], $where);
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
        $rows = $this->dao->select(["*"], ["idSubasta" => $this->idBidding]);
        if (!$rows) return false;

        $this->idProduct = $rows[0]["idProducto"];
        $this->minBid    = $rows[0]["pujaMinima"];
        $this->limitDate = $rows[0]["fechaLimite"];

        return true;
    }

    /**
     * Comprueba que el producto existe
     *
     * @return boolean
     *     True si se encuentra el producto en la
     *      base de datos
     */
    public function fromProduct()
    {
        $rows = $this->dao->select(["*"], ["idProducto" => $this->idProduct]);
        if (!$rows) return false;

        $this->idBidding = $rows[0]["idSubasta"];
        $this->minBid    = $rows[0]["pujaMinima"];
        $this->limitDate = $rows[0]["fechaLimite"];

        return true;
    }

    /**
     * Guarda la subasta en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
    public function save()
    {
        $data = ["idProducto" => $this->idProduct];

        if (isset($this->minBid))    $data["pujaMinima"]  = $this->minBid;
        if (isset($this->limitDate)) $data["fechaLimite"] = $this->limitDate;

        if (isset($this->idBidding))
            return $this->dao->update($data, ["idSubasta" => $this->idBidding]);
        else
            return $this->dao->insert($data);
    }

    /**
     * Elimina la subasta de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idSubasta" => $this->idBidding]);
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
        // valida que el producto existe
        $product = new Product($this->idProduct);
        if(!$product->fill()) return false;

        // validar que la puja minima sea superior a 0.0
        if ($this->minBid <= 0.0) return false;
        // validar que la fecha limite sea posterior a la fecha de
        // creacion (actual)
        if (date("Y-m-d H:i:s") >= $this->limitDate)
            return false;

        return true;
    }

    /**
     * Devuelve el id de la subasta
     *
     * @return string $idBidding
     *     El id de la subasta
     */
    public function getId()
    {
        return $this->idBidding;
    }

    /**
     * Devuelve el id del producto
     *
     * @return string $idProduct
     *     El id del producto
     */
    public function getProductId()
    {
        return $this->idProduct;
    }

    /**
     * Devuelve la puja más alta actual
     * de la subasta
     *
     * @return Bid end($bids)
     *     La puja más alta (la última
     *      contenida en $bids)
     */
    public function getHighestBid()
    {
        // findBy proporcionara resultado ordenado por clave primaria
        // (comportamiento por defecto de BD), y dado que los ids son
        // incrementales, la puja mas alta sera la ultima del array
        $bids = Bid::findBy(["idSubasta" => $this->idBidding]);

        if (count($bids) === 0)
            return false;

        return end($bids);
    }

}

?>
