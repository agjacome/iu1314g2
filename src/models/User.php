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
        if ($rows !== false) {
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

        $count = $this->dao->select(["COUNT(login)"], ["login" => $this->login])[0][0];

        if     ($count == 0) return $this->dao->insert($data);
        elseif ($count == 1) return $this->dao->update($data, ["login" => $this->login]);

        return false;
    }

    public function delete()
    {
        return $this->dao->delete(["login" => $this->login]);
    }

    public function validate()
    {
        // login solo permite minusculas, numero, guion y guion bajo
        if (!filter_var($this->login, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-z0-9\-_]+/"]]))
            return false;

        // login como minimo 4 caracteres y maximo 20
        if (strlen($this->login) < 4 || strlen($this->login) > 20)
            return false;

        // rol debe ser o "usuario" o "admin"
        if ($this->role !== "usuario" && $this->role !== "admin")
            return false;

        // email debe ser un email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
            return false;

        // limpia entidades HTML de nombre
        $this->name = filter_var($this->name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        // nombre debe tener como minimo 4 caracteres y maximo 255
        if (strlen($this->name) < 4 || strlen($this->name) > 255)
            return false;

        // limpia entidades HTML de direccion
        $this->address = filter_var($this->address, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        // direccion debe tener como minimo 4 caracteres y maximo 255
        if (strlen($this->address) < 4 || strlen($this->address) > 255)
            return false;

        // telefono solo permite enteros entre 600000000 y 999999999
        if (!filter_var($this->telephone, FILTER_VALIDATE_INT,
                        ["options" => ["min_range" => 600000000, "max_range" => 999999999]]))
            return false;

        return true;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function isNewLogin()
    {
        // cuenta el numero de usuarios con el mismo login y devuelve si existe 
        // o no alguno (el contador es 0 o no)
        return $this->dao->select(["COUNT(login)"], ["login" => $this->login])[0][0] == 0;
    }

    public function setPassword($cleanPass)
    {
        // contraseñas deben tener longitud 4 como minimo
        if (strlen($cleanPass) < 4)
            return false;

        // se cifra la contraseña recibida con password_hash
        $this->hashedPass = \components\Password::hash($cleanPass, PASSWORD_DEFAULT);
        return true;
    }

    public function checkPassword($cleanPass)
    {
        // comprueba con password_verify si la contraseña recibida es la misma 
        // al cifrado almacenado
        return \components\Password::verify($cleanPass, $this->hashedPass);
    }

}

?>
