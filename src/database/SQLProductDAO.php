<?php

namespace database;

/**
 * DAO concreto para la tabla de productos.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class SQLProductDAO extends SQLDAO implements DAO
{

    /**
     * Crea una instancia del SQLDAO abstracto con el nombre de la tabla de 
     * productos.
     */
    public function __construct()
    {
        parent::__construct("PRODUCTO");
    }

}

?>
