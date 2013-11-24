<?php

namespace database;

/**
 * Proporciona un acceso global a la base de datos a traves de un objeto de 
 * conexion PDO.
 *
 * Ver: http://php.net/manual/en/book.pdo.php
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class DatabaseConnection
{

    private static $connection;  // objeto PDO de conexion a la BD

    /**
     * Inicializa de forma estatica la conexion a la BD si no ha sido 
     * previamente creada, y la reorna.
     *
     * @return PDO
     *     Objeto de conexion a la BD.
     */
    public static function getConnection()
    {
        // si no ha sido previamente creado, crea la conexion
        if (!isset(self::$connection)) {
            // obtiene los parametros de conexion desde el fichero de 
            // configuracion
            $url  = \components\Configuration::getValue("database", "connection");
            $user = \components\Configuration::getValue("database", "username");
            $pass = \components\Configuration::getValue("database", "password");

            // prueba la conexion, si no es posible conectarse lanza un error 
            // al cliente y termina
            try {
                self::$connection = new \PDO($url, $user, $pass);
            } catch (\PDOException $pde) {
                print "<h1>Error de acceso a Base de Datos</h1>\n";
                print "<p>" . $pde->getMessage() . "</p>\n";
                die();
            }
        }

        // retorna el objeto de conexion a la BD
        return self::$connection;
    }

}

?>
