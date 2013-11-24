<?php

namespace database;

/**
 * DAO concreto para la tabla de calificaciones.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class SQLRatingDAO extends SQLDAO implements DAO
{

    /**
     * Crea una instancia del SQLDAO abstracto con el nombre de la tabla de 
     * calificaciones.
     */
    public function __construct()
    {
        parent::__construct("CALIFICACION");
    }

}

?>
