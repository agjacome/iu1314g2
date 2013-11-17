<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a las compras.
 *
 * @package  database
 */

class SQLPurchaseDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("COMPRA");
    }

}

?>
