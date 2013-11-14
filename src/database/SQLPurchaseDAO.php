<?php

namespace database;

class SQLPurchaseDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("COMPRA");
    }

}

?>
