<?php

namespace models;

/**
 * Modelo para Ventas. Soporta todas las operaciones basicas que se realizaran 
 * con una venta.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Sale extends Model
{

    // FUTURE FIXME: idProduct no deberia ser un simple identificador,
    // sino una referencia al modelo de productos. Con mas tiempo se hubiese 
    // hecho asi.

    private $idSale;     // identificador de la venta (auto-incremental)
    private $idProduct;  // identificador del producto
    public  $price;      // precio asignado a la venta
    public  $stock;      // numero de unidades existentes en venta

    /**
     * Construye una venta a partir de los parametros recibidos.
     *
     * @param in $idSale
     *     Identificador de la venta, null si no creada previamente.
     * @param in $idProduct
     *     Identificador del producto, si se ha proporcionado $idSale no sera 
     *     necesario proporcionar este, puesto que puede ser recuperado desde 
     *     la BD.
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
     * Devuelve un array de objetos Sale donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Sale con todas las ventas encontradas que cumplan la 
     *     condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los identificadores de ventas que cumplan la condicion 
        // dada
        $ids = \database\DAOFactory::getDAO("sale")->select(["idVenta"], $where);
        if (!$ids) return array();

        // genera un array de objetos Sale creandolos a partir de los 
        // identificadores anteriores yllamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $sale = new Sale($id["idVenta"]);
            if (!$sale->fill()) break;
            $found[ ] = $sale;
        }

        return $found;
    }

    /**
     * Rellena el objeto Sale actual con todos los datos, obteniendolos desde 
     * la base de datos. Es necesario que tenga su atributo "idSale" 
     * establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos de la venta con el identificador asignado
        $rows = $this->dao->select(["*"], ["idVenta" => $this->idSale]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->idProduct = $rows[0]["idProducto"];
        $this->price     = $rows[0]["precio"];
        $this->stock     = $rows[0]["stock"];

        return true;
    }

    /**
     * Rellena el objeto Sale actual con todos los datos, obteniendolos desde 
     * la base de datos. A diferencia de fill(), utiliza para ello el atributo 
     * "idProduct", por tanto es necesario que este este establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fromProduct()
    {
        // obtiene todos los datos de la venta con el identificador de producto 
        // asignado (un producto no puede tener mas de una venta, por tanto se 
        // puede explotar esta caracteristica para rellenar los datos a partir 
        // del id de producto)
        $rows = $this->dao->select(["*"], ["idProducto" => $this->idProduct]);
        if (!$rows) return false;

        // rellena todos los atibutos con los datos obtenidos
        $this->idSale = $rows[0]["idVenta"];
        $this->price  = $rows[0]["precio"];
        $this->stock  = $rows[0]["stock"];

        return true;
    }

    /**
     * Almacena la venta en la base de datos. Se encarga de comprobar si se 
     * trata de una nueva insercion o una actualizacion en base a si el 
     * atributo "idSale" esta a null (insercion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion ha resultado satisfactoria, False 
     *     en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = ["idProducto" => $this->idProduct];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->price)) $data["precio"] = $this->price;
        if (isset($this->stock)) $data["stock"]  = $this->stock;

        // si idSale no es null, entonces es un update
        if (isset($this->idSale))
            return $this->dao->update($data, ["idVenta" => $this->idSale]);

        // sino, es un insert
        return $this->dao->insert($data);
    }

    /**
     * Elimina la venta de la base de datos. El objeto debe haber sido 
     * previamente creado con el atributo "idSale" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion ha resultado satisfactoria, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idVenta" => $this->idSale]);
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

        // valida que el producto exista
        $product = new Product($this->idProduct);
        if (!$product->fill()) return false;

        // valida que el precio y el stock sean numeros
        if (!is_numeric($this->price) || !is_numeric($this->stock))
            return false;

        // valida que el precio y el stock sean iguales o superiores a cero
        return $this->price >= 0.0 && $this->stock >= 0;
    }

    /**
     * Devuelve el identificador de la venta de esta instancia.
     *
     * @return int
     *     Identificador de la venta a la que hace referencia este objeto de 
     *     modelo.
     */
    public function getId()
    {
        return $this->idSale;
    }

    /**
     * Devuelve el identificador del producto de esta instancia.
     *
     * @return int
     *     Indentificador del producto al que esta asociado la venta a la que 
     *     hace referencia este objeto de modelo.
     */
    public function getProductId()
    {
        return $this->idProduct;
    }

}

?>
