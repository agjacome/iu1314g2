<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Ventas.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Sale extends Model
{

    private $idSale;
    private $idProduct;
    public  $price;
    public  $stock;

    /**
     * Construye una nueva instancia de Sale a partir de los datos
     * recibidos como parámetros
     */
    public function __construct($idSale = null, $idProduct = null)
    {
        parent::__construct();

        $this->idSale    = $idSale;
        $this->idProduct = $idProduct;
        $this->price     = null;
        $this->stock     = null;
    }

    /**
     * Devuelve un array con todas las ventas que
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
        $ids = \database\DAOFactory::getDAO("sale")->select(["idVenta"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $sale = new Sale($id["idVenta"]);
            if (!$sale->fill()) break;
            $found[ ] = $sale;
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
        $rows = $this->dao->select(["*"], ["idVenta" => $this->idSale]);
        if (!$rows) return false;

        $this->idProduct = $rows[0]["idProducto"];
        $this->price     = $rows[0]["precio"];
        $this->stock     = $rows[0]["stock"];

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

        $this->idSale = $rows[0]["idVenta"];
        $this->price  = $rows[0]["precio"];
        $this->stock  = $rows[0]["stock"];

        return true;
    }

    /**
     * Guarda la venta en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
    public function save()
    {
        $data = ["idProducto" => $this->idProduct];

        if (isset($this->price)) $data["precio"] = $this->price;
        if (isset($this->stock)) $data["stock"]  = $this->stock;

        if (isset($this->idSale))
            return $this->dao->update($data, ["idVenta" => $this->idSale]);
        else
            return $this->dao->insert($data);
    }

    /**
     * Elimina la venta de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idVenta" => $this->idSale]);
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
        // valida que el producto exista
        $product = new Product($this->idProduct);
        if (!$product->fill()) return false;

        // valida que el precio y el stock sean iguales o superiores a cero
        return $this->price >= 0.0 && $this->stock >= 0;
    }

    /**
     * Devuelve el id de la venta
     *
     * @return string $idSale
     *     El id de la venta
     */
    public function getId()
    {
        return $this->idSale;
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

}

?>
