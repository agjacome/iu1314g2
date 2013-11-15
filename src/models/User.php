<?php

namespace models;

class User extends Model
{
private $login;
    private $hashedPass;
    public  $role;
    public  $email;
    public  $name;
    public  $address;
    public  $telephone;

    public function __construct()
    {
        parent::__construct();
    }

    public static function findBy($where)
    {
        // se necesita invocar a DAOFactory porque, al ser static, no sera 
        // llamado desde una instancia, y por tanto no tendra acceso a 
        // $this->dao definido en la superclase abstracta
        $rows = \database\DAOFactory::getDAO("user")->select(["*"], $where);

        if (count($rows) < 1)
            throw new exceptions\NotFoundException("Usuario no encontrado");

        $found = array();
        foreach ($rows as $row) {
            $user = new User();

            $user->login      = $row["login"];
            $user->hashedPass = $row["password"];
            $user->role       = $row["rol"];
            $user->email      = $row["email"];
            $user->name       = $row["nombre"];
            $user->address    = $row["direccion"];
            $user->telephone  = $row["telefono"];

            $found[] = $user;
        }

        return $found;
    }

    public function save()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function delete()
    {
        $this->dao->delete(["login" => $this->login]);
    }

    public function validate()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setPassword($cleanPass)
    {
        $this->hashedPass = \components\Password::hash($cleanPass, PASSWORD_DEFAULT);
    }

    public function checkPassword($cleanPass)
    {
        return \components\Password::verify($cleanPass, $this->hashedPass);
    }

}

?>
