<?php

namespace database;

class DAOFactory
{

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
