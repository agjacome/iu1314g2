<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes al Pago
 *
 * @package  database
 */

class SQLPaymentDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("PAGO");
    }

}

?>
