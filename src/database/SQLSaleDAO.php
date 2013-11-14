<?php

namespace database;

class SQLSaleDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("VENTA");
    }

}

?>
