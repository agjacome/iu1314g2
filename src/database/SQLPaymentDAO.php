<?php

namespace database;

class SQLPaymentDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("PAGO");
    }

}

?>
