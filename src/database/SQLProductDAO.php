<?php

namespace database;

class SQLProductDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("PRODUCTO");
    }

}

?>
