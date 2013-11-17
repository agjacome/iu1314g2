<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a los productos.
 *
 * @package  database
 */

class SQLProductDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("PRODUCTO");
    }

}

?>
