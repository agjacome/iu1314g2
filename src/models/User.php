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

    public function __construct($login)
    {
        parent::__construct();

        $this->login      = strtolower($login);
        $this->hashedPass = null;
        $this->role       = null;
        $this->email      = null;
        $this->name       = null;
        $this->address    = null;
        $this->telephone  = null;
    }

    public static function findBy($where)
    {
        // se necesita invocar a DAOFactory porque, al ser static, no sera 
        // llamado desde una instancia, y por tanto no tendra acceso a 
        // $this->dao definido en la superclase abstracta
        $rows = \database\DAOFactory::getDAO("user")->select(["*"], $where);

        $found = array();
        foreach ($rows as $row) {
            $user = new User($row["login"]);

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
        $data = [ "login" => $this->login ];
        if (isset($this->hashedPass)) $data["password"]  = $this->hashedPass;
        if (isset($this->email))      $data["email"]     = $this->email;
        if (isset($this->role))       $data["rol"]       = $this->role;
        if (isset($this->name))       $data["nombre"]    = $this->name;
        if (isset($this->address))    $data["direccion"] = $this->address;
        if (isset($this->telephone))  $data["telefono"]  = $this->telephone;

        $count = count($this->dao->select(["login"], ["login" => $this->login]));

        if ($count === 0)
            $this->dao->insert($data);
        else
            $this->dao->update($data, ["login" => $this->login]);
    }

    public function delete()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function validate()
    {
        // TODO: http://php.net/manual/en/function.filter-var.php
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function isNewLogin()
    {
        return count($this->dao->select(["login"], ["login" => $this->login])) === 0;
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
