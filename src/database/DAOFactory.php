<?php

namespace database;

/**
 * Factoria de DAOs, utiliza el fichero de configuracion para determinar que 
 * DAO debe ser creado para cada entidad dada.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class DAOFactory
{

    /**
     * Crea un objeto del DAO apropiado segun el nombre de la entidad 
     * proporcionado. Utiliza para ello lo establecido dentro de la seccion 
     * "daos" del fichero de configuracion.
     *
     * @param String $entityName
     *     Nombre de la entidad de la que se desea obtener un DAO.
     *
     * @return DAO
     *     Objeto de una clase que implemente la interfaz DAO para la entidad 
     *     proporcionada.
     */
    public static function getDAO($entityName)
    {
        // extrae el nombre de la clase desde el fichero de configuracion
        $className = \components\Configuration::getValue("daos", strtolower($entityName));

        // crea y retorna un objeto de dicha clase
        if (isset($className)) {
            $className = "database\\" . $className;
            return new $className();
        }

        // en caso de no existir dicha entidad en el fichero de configuracion, 
        // retorna null
        return null;
    }

}


?>
