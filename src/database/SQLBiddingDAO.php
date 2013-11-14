<?php

namespace database;

class SQLBiddingDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("SUBASTA");
    }

}

?>
