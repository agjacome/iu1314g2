<?php

namespace database;

class SQLUserDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("USUARIO");
    }

}

?>
