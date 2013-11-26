<?php

namespace models;

/**
 * Modelo para Calificaciones. Soporta todas las operaciones basicas que se 
 * realizaran con una calificacion.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Rating extends Model
{

    // FUTURE FIXME: idProduct y login no deberian ser simples identificadores, 
    // sino referencias al resto de modelos. Con mas tiempo se hubiese hecho 
    // asi.

    private $idRating;    // identificador de la calificacion (auto-incremental)
    private $idProduct;   // identificador del producto
    private $login;       // login del usuario que realiza la compra
    public  $rating;      // puntuacion otorgada (1-5)
    public  $commentary;  // comentario incluido en la puntuacion

    /**
     * Construye una calificacion a partir de los parametros recibidos.
     *
     * @param int $idRating
     *     Identificador de la calificacion, null si no creada previamente.
     * @param int $idProduct
     *     Identificador del producto, si se ha proporcionado $idRating no sera 
     *     necesario proporcionar esta, puesto que puede ser recuperada de la 
     *     BD.
     * @param String $login
     *     Login del usuario que realiza la calificacion. Si se ha 
     *     proporcionado $idRating no sera necesario proporcionar este, puesto 
     *     que puede ser recuperado desde la BD.
     */
    public function __construct($idRating = null, $idProduct = null, $login = null)
    {
        parent::__construct();

        $this->idRating   = $idRating;
        $this->idProduct  = $idProduct;
        $this->login      = $login;
        $this->rating     = null;
        $this->commentary = null;
    }

    /**
     * Devuelve un array de objetos Rating donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de Rating con todas las puntuaciones encontradas que cumplan 
     *     la condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los identificadores de calificaciones que cumplan la 
        // condicion dada
        $ids = \database\DAOFactory::getDAO("rating")->select(["idCalificacion"], $where);
        if (!$ids) return array();

        // genera un array de objetos Rating creandolos a partir de los 
        // identificadores anteriores y llamando a fill() para recuperar todos 
        // sus datos
        $found = array();
        foreach ($ids as $id) {
            $rating = new Rating($id["idCalificacion"]);
            if (!$rating->fill()) break;
            $found[ ] = $rating;
        }

        return $found;
    }

    /**
     * Rellena el objeto Rating actual con todos los datos, obteniendolos desde 
     * la base de datos. Es necesario que tenga su atributo "idRating" 
     * establecido (no nulo).
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos de la calificacion con el identificador 
        // asignado
        $rows = $this->dao->select(["*"], ["idCalificacion" => $this->idRating]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->idProduct  = $rows[0]["idProducto"];
        $this->login      = $rows[0]["login"];
        $this->rating     = $rows[0]["puntuacion"];
        $this->commentary = $rows[0]["comentario"];

        return true;
    }

    /**
     * Almacena la calificacion en la base de datos. Se encarga de comprobar si 
     * se trata de una nueva insrcion o una actualizacion en base a si el 
     * atributo "idRating" esta a nulll (insercion) o no (actualizacion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente. 
     *     False en caso contrario.
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = [
            "idProducto" => $this->idProduct,
            "login"      => $this->login
        ];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->rating))     $data["puntuacion"] = $this->rating;
        if (isset($this->commentary)) $data["comentario"] = $this->commentary;

        // si idRating no es null, entonces es un update
        if (isset($this->idRating))
            return $this->dao->update($data, ["idCalificacion" => $this->idRating]);

        // sino, es un insert
        return $this->dao->insert($data);
    }

    /**
     * Elimina la calificacion de la base de datos. El objeto debe hacer sido 
     * previamente inicializado con el atributo "idRating" a no nulo.
     *
     * @return boolean
     *     True si la eliminacion ha resultado satisfactoria, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["idCalificacion" => $this->idRating]);
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

        // valida que el producto exista
        $product= new Product($this->idProduct);
        if (!$product->fill()) return false;

        // valida que el usuario exista
        $user= new User($this->login);
        if (!$user->fill()) return false;

        // valida que la puntuacion este entre 0 y 5
        if (!filter_var ($this->rating, FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 0, "max_range" => 5]]))
            return false;

        // limpia comentario para prevencion de XSS
        $this->commentary = filter_var($this->commentary, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Comprueba si la calificacion es nueva, esto es: si no existe ya una 
     * calificacion para dicho producto realizada por el mismo usuario.
     *
     * @return boolean
     *     True si la calificacion aun no existe, False en caso contrario.
     */
    public function isNewRating()
    {
        // cuenta el numero de calificaciones que existen en la BD para dicho 
        // producto y realizadas por dicho usuario
        $count = $this->dao->select(["COUNT(idCalificacion)"],
                                    ["idProducto" => $this->idProduct, "login" => $this->login]);

        // si el contador es cero, entonces es una nueva calificacion
        return intval($count[0][0]) === 0;
    }

    /**
     * Devuelve el identificador de la calificacion de esta instancia.
     *
     * @return int
     *     Identificador de la calificacion a la que hace referencia este 
     *     objeto de modelo.
     */
    public function getId()
    {
        return $this->idRating;
    }

    /**
     * Devuelve el identificador del producto de esta instancia.
     *
     * @return int
     *     Identificador del producto al que esta asociada la calificacion a la 
     *     que hacer referencia este objeto de modelo.
     */
    public function getProductId()
    {
        return $this->idProduct;
    }

    /**
     * Devuelve el login del usuario de esta instancia.
     *
     * @return String
     *     Login del usuario al que esta asociada la calificacion a la que hace 
     *     referencia este objeto.
     */
    public function getLogin()
    {
        return $this->login;
    }

}

?>
