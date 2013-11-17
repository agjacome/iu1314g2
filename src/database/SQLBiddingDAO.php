<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a las subastas.
 *
 * @package  database
 */

class SQLBiddingDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("SUBASTA");
    }

}

?>
