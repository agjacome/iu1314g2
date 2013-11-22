<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Calificaciones.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Rating extends Model
{

    private $idRating;
    private $idProduct;
    private $login;
    public  $rating;
    public  $commentary;

    /**
     * Construye una nueva instancia de Rating a partir de los datos
     * recibidos como parámetros
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
     * Devuelve un array con todas las calificaciones que
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
        $ids = \database\DAOFactory::getDAO("rating")->select(["idCalificacion"], $where);
        if (!$ids) return array();

        $found = array();
        foreach ($ids as $id) {
            $rating = new Rating($id["idCalificacion"]);
            if (!$rating->fill()) break;
            $found[ ] = $rating;
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
        $rows = $this->dao->select(["*"], ["idCalificacion" => $this->idRating]);
        if (!$rows) return false;

        $this->idProduct  = $rows[0]["idProducto"];
        $this->login      = $rows[0]["login"];
        $this->rating     = $rows[0]["puntuacion"];
        $this->commentary = $rows[0]["comentario"];

        return true;
    }

    /**
     * Guarda la calificación en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
    public function save()
    {
        $data = [
            "idProducto" => $this->idProduct,
            "login"      => $this->login
        ];

        if (isset($this->rating))     $data["puntuacion"] = $this->rating;
        if (isset($this->commentary)) $data["comentario"] = $this->commentary;

        if (isset($this->idRating))
            return $this->dao->update($data, ["idCalificacion" => $this->idRating]);
        else
            return $this->dao->insert($data);
    }

    /**
     * Elimina la calificacion de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["idCalificacion" => $this->idRating]);
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

        return true;
    }

    public function isNewRating()
    {
        $count = $this->dao->select(["COUNT(idCalificacion)"],
                                    ["idProducto" => $this->idProduct, "login" => $this->login]);
        return intval($count[0][0]) === 0;
    }

    /**
     * Devuelve el id de la calificacion
     *
     * @return string $idRating
     *     El id de la calificacion
     */
    public function getId()
    {
        return $this->idRating;
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
