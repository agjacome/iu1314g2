<?php

namespace database;

class SQLRatingDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("CALIFICACION");
    }

}

?>
