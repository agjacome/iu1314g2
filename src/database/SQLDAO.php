<?php

namespace database;

/**
 * Clase que implementa los métodos comunes para interactuar con la base de datos. Se usa el patrón DAOs (Data Abstract Object) para aislar
 * para aislar la interactuación con la base de datos de forma que, si se tiene una base de datos MySQL esta pueda ser facilmente sustituida
 * por una de Postgre o cualquier otro tipo.
 *
 * @package  database
 */

abstract class SQLDAO implements DAO
{
    /**
     * Nombre de la tabla sobre la que se trabaja.
     * @var [type]
     */
    protected $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Inserta datos en la base de datos @tablename.
     * @param  array $data Datos a insertar en la base de datos.
     */
    public function insert($data)
    {
        $db = DatabaseConnection::getConnection();

        $insert = "INSERT INTO " . $this->tableName . " (";
        foreach ($data as $key => $_) {
            $insert .= $key;
            if (next($data)) $insert .= ", ";
        }
        $insert .= ") VALUES(" . str_repeat("?, ", count($data) - 1) . " ?)";

        $query = $db->prepare($insert);

        $i = 1;
        foreach ($data as $key => $_)
            $query->bindParam($i++, $data[$key]);

        $query->execute();
    }

    /**
     * Actualiza los datos de una @tablename.
     * @param  array $data datos a asignar en los atributos del @tablename.
     * @param  array $where condición a cumplir. 
     */
    public function update($data, $where = null)
    {
        $db = DatabaseConnection::getConnection();

        $update = "UPDATE " . $this->tableName . " SET ";
        foreach ($data as $key => $value) {
            $update .= $key . " = ?";
            if (next($data)) $update .= ", ";
        }

        if (isset($where)) {
            $update .= " WHERE ";
            foreach ($where as $key => $valor) {
                $update .= $key . " = ?";
                if (next($where)) $update .= " AND ";
            }
        }

        $query = $db->prepare($update);

        $i = 1;
        foreach ($data as $key => $value)
            $query->bindParam($i++, $data[$key]);

        if (isset($where)) {
            foreach ($where as $key => $value)
                $query->bindParam($i++, $where[$key]);
        }

        $query->execute();
    }

    /**
     * Elimina tablas de la base de datos en base a una o varias condiciones pasadas como parámetro.
     * @param array $where array con las condiciones necesarias para realizar el borrado.
     */
    public function delete($where)
    {
        $db = DatabaseConnection::getConnection();

        $delete = "DELETE FROM " . $this->tableName;

        if (isset($where)) {
            $delete .= " WHERE ";
            foreach($where as $key => $_) {
                $delete .= $key." = ?";
                if (next($where)) $delete .= " AND ";
            }
        }

        $query = $db->prepare($delete);

        $i = 1;
        if(isset($where)) {
            foreach($where as $key => $_)
                $query->bindParam($i++, $where[$key]);
        }

        $query->execute();
    }

    /**
     * Consulta de datos en la base de datos.
     * @param  array $data  columnas a mostrar.
     * @param  array $where condiciones impuestas.
     * @return array con las tuplas resultantes de la consulta.
     */
    public function select($data, $where = null)
    {
        $db = DatabaseConnection::getConnection();

        $select = "SELECT ";
        foreach ($data as $column) {
            $select .= $column;
            if (next($data)) $select .= ", ";
        }

        $select .= " FROM " . $this->tableName;

        if (isset($where)) {
            $select .= " WHERE ";

            foreach ($where as $key => $_) {
                $select .= $key . " = ?";
                if (next($where)) $select .= " AND ";
            }
        }

        $query = $db->prepare($select);

        if (isset($where)) {
            $i = 1;
            foreach ($where as $key => $_)
                $query->bindParam($i++, $where[$key]);
        }

        $query->execute();

        $result = array();
        while ($row = $query->fetch())
            $result[] = $row;

        return $result;
    }

    /**
     * Realiza una consulta sobre la sentencia pasada como parámetro.
     * @param  array $statement Sentencia pasada como parámetro.
     * @return array tuplas resultantes de la consulta.
     */
    public function query($statement)
    {
        $db = DatabaseConnection::getConnection();
        $query = $db->prepare($statement);

        $args = func_get_args();
        for ($i = 1; $i < count($args); $i++)
            $query->bindParam($i + 1, $args[$i]);

        $query->execute();

        if ($row = $query->fetch()) {
            $result = [$row];

            while ($row = $query->fetch())
                $result[] = $row;

            return $result;
        }
    }

}


?>
