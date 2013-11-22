<?php

namespace models;

class Rating extends Model
{

    private $idRating;
    private $idProduct;
    private $login;
    public  $rating;
    public  $commentary;

    public function __construct($idRating = null, $idProduct = null, $login = null)
    {
        parent::__construct();

        $this->idRating   = $idRating;
        $this->idProduct  = $idProduct;
        $this->login      = $login;
        $this->rating     = null;
        $this->commentary = null;
    }

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

    public function delete()
    {
        return $this->dao->delete(["idCalificacion" => $this->idRating]);
    }

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

    public function getId()
    {
        return $this->idRating;
    }

    public function getProductId()
    {
        return $this->idProduct;
    }

    public function getLogin()
    {
        return $this->login;
    }

}

?>
