<?php

namespace database;

/**
 * Implementa la conexión a la base de datos.
 * 
 * @package  database
 */

class DatabaseConnection
{

    private static $connection;

    /**
     * Obtiene los parámetros necesarios del archivo de configuración de la base de datos y se conecta a ella 
     * devolviendo el PHP Data Object (PDO) que corresponda.
     * @return PDO PHP Data Object, objeto que permite la interacción con la base de datos.
     */
    public static function getConnection()
    {
        if (!isset(self::$connection)) {
            $url  = \components\Configuration::getValue("database", "connection");
            $user = \components\Configuration::getValue("database", "username");
            $pass = \components\Configuration::getValue("database", "password");

            try {
                self::$connection = new \PDO($url, $user, $pass);
            } catch (\PDOException $pde) {
                print "<h1>Error de acceso a Base de Datos</h1>\n";
                print "<p>" . $pde->getMessage() . "</p>\n";
                die();
            }
        }

        return self::$connection;
    }

}

?>
