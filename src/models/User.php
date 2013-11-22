<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Usuarios.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class User extends Model
{

    private $login;
    private $hashedPass;
    public  $role;
    public  $email;
    public  $name;
    public  $address;
    public  $telephone;

    /**
     * Construye una nueva instancia de User a partir de los datos
     * recibidos como parámetros
     */
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

    /**
     * Devuelve un array con todos los usuarios que
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
        // se necesita invocar a DAOFactory porque, al ser static, no sera
        // llamado desde una instancia, y por tanto no tendra acceso a
        // $this->dao definido en la superclase abstracta
        // se recogen todos los logins que cumplan la condicion
        $logins = \database\DAOFactory::getDAO("user")->select(["login"], $where);
        if (!$logins) return array();

        // para cada login obtenido, se crea y rellenan datos de un objeto 
        // User, metiendolo dentro del array $found
        $found = array();
        foreach ($logins as $login) {
            $user = new User($login["login"]);
            if (!$user->fill()) break;
            $found[] = $user;
        }

        // y finalmente se retorna el array
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
        $rows = $this->dao->select(["*"], ["login" => $this->login]);
        if (!$rows) return false;

        $this->hashedPass = $rows[0]["password"];
        $this->role       = $rows[0]["rol"];
        $this->email      = $rows[0]["email"];
        $this->name       = $rows[0]["nombre"];
        $this->address    = $rows[0]["direccion"];
        $this->telephone  = $rows[0]["telefono"];

        return true;
    }

    /**
     * Guarda el usuario en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
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

    /**
     * Elimina el usuario de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        return $this->dao->delete(["login" => $this->login]);
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

    /**
     * Comprueba si ya existe algún usuario
     * con el mismo id
     *
     * @return boolean
     *     True si el id ya existe
     */
    public function isNewLogin()
    {
        // cuenta el numero de usuarios con el mismo login y devuelve si existe 
        // o no alguno (el contador es 0 o no)
        return $this->dao->select(["COUNT(login)"], ["login" => $this->login])[0][0] == 0;
    }

    /**
     * Guarda la contraseña (cifrada)
     * del usuario
     *
     * @param string $cleanPass
     *      Contraseña sin cifrar
     *
     * @return boolean
     *     True si se alamacena la contraseña
     */
    public function setPassword($cleanPass)
    {
        // contraseñas deben tener longitud 4 como minimo
        if (strlen($cleanPass) < 4)
            return false;

        // se cifra la contraseña recibida con password_hash
        $this->hashedPass = \components\Password::hash($cleanPass, PASSWORD_DEFAULT);
        return true;
    }

    /**
     * Comprueba que la contraseña cifrada
     * es la misma que la original
     *
     * @param string $cleanPass
     *      Contraseña sin cifrar
     *
     * @return boolean
     *     True si son la misma contraseña
     */
    public function checkPassword($cleanPass)
    {
        // comprueba con password_verify si la contraseña recibida es la misma 
        // al cifrado almacenado
        return \components\Password::verify($cleanPass, $this->hashedPass);
    }

}

?>
