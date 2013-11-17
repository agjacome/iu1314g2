<?php

namespace database;

/**
 * Clase que se encarga de las tablas correspondientes a las puntuaciones.
 *
 * @package  database
 */

class SQLRatingDAO extends SQLDAO implements DAO
{

    public function __construct()
    {
        parent::__construct("CALIFICACION");
    }

}

?>
