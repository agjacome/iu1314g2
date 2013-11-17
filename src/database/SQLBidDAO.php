<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a las pujas.
 *
 * @package  database
 */

class SQLBidDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("PUJA");
    }

}

?>
