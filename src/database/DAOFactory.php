<?php

namespace database;

/**
 * Ṕatrón factoría que permite la creación de una base de datos concreta según la entidad que se le pase.
 *
 * @package  database
 */

class DAOFactory
{
	/**
	 * Crea la base de datos que corresponda según la entidad que se pase.
	 * @param  string $entityName Nombre de la base de datos que debe ser creada. Por ejemplo: Usuario o Producto.
	 * @return SQLXXXDAO que modela la base de datos.
	 */
    public static function getDAO($entityName)
    {
        $className = \components\Configuration::getValue("daos", strtolower($entityName));

        if (isset($className)) {
            $className = "database\\" . $className;
            return new $className();
        }

        return null;
    }

}


?>
