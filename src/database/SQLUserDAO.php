<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a los usuarios.
 *
 * @package  database
 */

class SQLUserDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("USUARIO");
    }

}

?>
