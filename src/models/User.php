<?php

namespace models;

/**
 * Modelo para Usuarios. Soporta todas las operaciones basicas que se 
 * realizaran con un usuario.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class User extends Model
{

    private $login;       // login del usuario (string, unico)
    private $hashedPass;  // contraseña cifrada con BCrypt
    public  $role;        // rol del usuario ("usuario" o "admin")
    public  $email;       // email del usuario
    public  $name;        // nombre real del usuario
    public  $address;     // direccion postal del usuario
    public  $telephone;   // telefono del usuario

    /**
     * Construye un usuario a partir del login recibido.
     *
     * @param String $login
     *     Login del usuario.
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
     * Devuelve un array de objetos User donde todos ellos cumplen una 
     * condicion de busqueda dada.
     *
     * @param array $where
     *     Array asociativo clave => valor para condiciones de busqueda.
     *
     * @return array
     *     Array de User con todos los usuarios encontrados que cumplan la 
     *     condicion establecida.
     */
    public static function findBy($where)
    {
        // obtiene todos los logins de usuarios que cumplan la condicion
        $logins = \database\DAOFactory::getDAO("user")->select(["login"], $where);
        if (!$logins) return array();

        // genera un array de objetos User creandolos con los logins anteriores 
        // y llamando a fill() para recuperar todos sus datos
        $found = array();
        foreach ($logins as $login) {
            $user = new User($login["login"]);
            if (!$user->fill()) break;
            $found[] = $user;
        }

        return $found;
    }

    /**
     * Rellena el objeto User actual con todos los datos, obteniendolos desde 
     * la base de datos.
     *
     * @return boolean
     *     True si se han podido obtener los datos, False en caso contrario.
     */
    public function fill()
    {
        // obtiene todos los datos del usuario con el login asignado
        $rows = $this->dao->select(["*"], ["login" => $this->login]);
        if (!$rows) return false;

        // rellena todos los atributos con los datos obtenidos
        $this->hashedPass = $rows[0]["password"];
        $this->role       = $rows[0]["rol"];
        $this->email      = $rows[0]["email"];
        $this->name       = $rows[0]["nombre"];
        $this->address    = $rows[0]["direccion"];
        $this->telephone  = $rows[0]["telefono"];

        return true;
    }

    /**
     * Almacena el usuario en la base de datos. Se encarga de comprobar si se 
     * trata de una nueva insercion o una actualizacion en base a si el login 
     * ya existe en la base de datos (actualizacion) o no (insercion).
     *
     * @return boolean
     *     True si la insercion/modificacion se ha realizado correctamente, 
     *     False en caso contrario
     */
    public function save()
    {
        // datos obligatorios para la insercion/modificacion
        $data = [ "login" => $this->login ];

        // datos opcionales, que pueden no estar establecidos para la 
        // insercion/modificacion
        if (isset($this->hashedPass)) $data["password"]  = $this->hashedPass;
        if (isset($this->email))      $data["email"]     = $this->email;
        if (isset($this->role))       $data["rol"]       = $this->role;
        if (isset($this->name))       $data["nombre"]    = $this->name;
        if (isset($this->address))    $data["direccion"] = $this->address;
        if (isset($this->telephone))  $data["telefono"]  = $this->telephone;

        // cuenta el numero de usuarios con el login proporcionado
        $count = $this->dao->select(["COUNT(login)"], ["login" => $this->login])[0][0];

        // si el contador es 0, entonces es un nuevo usuario y hace una 
        // insercion, si es 1, entonces el usuario existe y realiza una 
        // modificacion, si es distinto a 0 o 1 entonces se ha producido un 
        // error
        if     ($count == 0) return $this->dao->insert($data);
        elseif ($count == 1) return $this->dao->update($data, ["login" => $this->login]);
        return false;
    }

    /**
     * Elimina el usuario de la base de datos.
     *
     * @return boolean
     *     True si la eliminacion ha resultado satisfactoria, False en caso 
     *     contrario.
     */
    public function delete()
    {
        return $this->dao->delete(["login" => $this->login]);
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

        // todas las condiciones se han cumplido, retorna true
        return true;
    }

    /**
     * Devuelve el login del usuario de esta instancia.
     *
     * @return String
     *     Login del usuario al que hace referencia este objeto de modelo.
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Determina si el usuario ya existe o no en la base de datos (el login 
     * esta almacenado).
     *
     * @return boolean
     *     True si no existe un usuario con dicho login, False si sí que 
     *     existe.
     */
    public function isNewLogin()
    {
        // cuenta el numero de usuarios con el mismo login y devuelve si existe 
        // o no alguno (el contador es 0 o no)
        return $this->dao->select(["COUNT(login)"], ["login" => $this->login])[0][0] == 0;
    }

    /**
     * Almacena la contraseña cifrada en el objeto de usuario. Recibe una 
     * contraseña en texto plano y la cifra con BCrypt.
     *
     * @param String $cleanPass
     *     Contraseña en texto plano a almacenar.
     *
     * @return boolean
     *     True si la contraseña se ha podido almacenar, False en caso 
     *     contrario
     */
    public function setPassword($cleanPass)
    {
        // contraseñas deben tener longitud 4 como minimo
        if (strlen($cleanPass) < 4) return false;

        // se cifra la contraseña recibida con password_hash
        $this->hashedPass = \components\Password::hash($cleanPass, PASSWORD_DEFAULT);
        return true;
    }

    /**
     * Valida la contraseña cifrada almacenada en el objeto contra una 
     * contraseña en texto plano proporcionada.
     *
     * @param String $cleanPass
     *     Contraseña en texto plano a validar.
     *
     * @return boolean
     *     True si las contraseñas coinciden, False en caso contrario.
     */
    public function checkPassword($cleanPass)
    {
        // comprueba con password_verify si la contraseña recibida es la misma 
        // al cifrado almacenado
        return \components\Password::verify($cleanPass, $this->hashedPass);
    }

}

?>
