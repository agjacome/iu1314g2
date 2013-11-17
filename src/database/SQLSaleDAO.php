<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a las ventas.
 *
 * @package  database
 */

class SQLSaleDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("VENTA");
    }

}

?>
