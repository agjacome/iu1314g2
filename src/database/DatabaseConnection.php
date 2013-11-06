<?php 

namespace database;

class DatabaseConnection
{

    private static $connection;

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
