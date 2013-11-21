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
        // TODO: validar que el login existe, validar que el producto existe
        // (apoyarse en modelos de usuario y producto)
        // TODO: validar que la puntuacion esta entre 1 y 5
        // TODO: limpiar comentario para prevencion de XSS
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function isNewRating()
    {
        $count = $this->dao->select(["COUNT(idCalificacion)"],
                                    ["idProducto" => $this->idProduct, "login" => $this->login]);
        return $count[0][0] === 0;
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
