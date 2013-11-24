<?php

namespace database;

/**
 * Clase abstracta que implementa la interfaz DAO proporcionando acceso 
 * generico a datos mediante consultas SQL.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
abstract class SQLDAO implements DAO
{

    protected $tableName;  // nombre de la tabla de la BD SQL con que trabajar

    /**
     * Constructor abstracto, almacena el nombre de la tabla proporcionado para 
     * trabajar con ella.
     *
     * @param String $tableName
     *     Nombre de la tabla de la BD SQL con la que esta instancia de SQLDAO 
     *     trabajara.
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Implementa la insercion de datos de la interfaz DAO.
     *
     * @see DAO
     */
    public function insert($data)
    {
        // obtiene la conexion a la base da datos
        $db = DatabaseConnection::getConnection();

        // crea la string de consulta a la BD mediante los datos proporcionados
        $insert = "INSERT INTO " . $this->tableName . " (";
        $iter = new \CachingIterator(new \ArrayIterator($data));
        foreach ($iter as $key => $_) {
            $insert .= $key;
            if ($iter->hasNext()) $insert .= ", ";
        }
        $insert .= ") VALUES(" . str_repeat("?, ", count($data) - 1) . " ?)";

        // indica que se utilizara la string anteriormente creada para la 
        // parametrizacion de datos siguiente y la ejecucion de la consulta
        $query = $db->prepare($insert);

        // parametriza la consulta con los datos proporcionados, evitando asi 
        // inyecciones SQL en los datos
        $i = 1;
        foreach ($data as $key => $_)
            $query->bindParam($i++, $data[$key]);

        // ejecuta la consulta y retorna el resultado de la ejecucion
        return $query->execute();
    }

    /**
     * Implementa la modificacion de datos de la interfaz DAO.
     *
     * @see DAO
     */
    public function update($data, $where = null)
    {
        // obtiene la conexion a la base da datos
        $db = DatabaseConnection::getConnection();

        // crea la string de consulta a la BD mediante los datos proporcionados
        $update = "UPDATE " . $this->tableName . " SET ";
        $iter = new \CachingIterator(new \ArrayIterator($data));
        foreach ($iter as $key => $value) {
            $update .= $key . " = ?";
            if ($iter->hasNext()) $update .= ", ";
        }
        if (isset($where)) {
            $update .= " WHERE ";
            $iter = new \CachingIterator(new \ArrayIterator($where));
            foreach ($iter as $key => $valor) {
                $update .= $key . " = ?";
                if ($iter->hasNext()) $update .= " AND ";
            }
        }

        // indica que se utilizara la string anteriormente creada para la 
        // parametrizacion de datos siguiente y la ejecucion de la consulta
        $query = $db->prepare($update);

        // parametriza la consulta con los datos proporcionados, evitando asi 
        // inyecciones SQL en los datos
        $i = 1;
        foreach ($data as $key => $value)
            $query->bindParam($i++, $data[$key]);
        if (isset($where)) {
            foreach ($where as $key => $value)
                $query->bindParam($i++, $where[$key]);
        }

        // ejecuta la consulta y retorna el resultado de la ejecucion
        return $query->execute();
    }

    /**
     * Implementa la eliminacion de datos de la interfaz DAO.
     *
     * @see DAO
     */
    public function delete($where)
    {
        // obtiene la conexion a la base da datos
        $db = DatabaseConnection::getConnection();

        // crea la string de consulta a la BD mediante los datos proporcionados
        $delete = "DELETE FROM " . $this->tableName;
        if (isset($where)) {
            $delete .= " WHERE ";
            $iter = new \CachingIterator(new \ArrayIterator($where));
            foreach($iter as $key => $_) {
                $delete .= $key." = ?";
                if ($iter->hasNext()) $delete .= " AND ";
            }
        }

        // indica que se utilizara la string anteriormente creada para la 
        // parametrizacion de datos siguiente y la ejecucion de la consulta
        $query = $db->prepare($delete);

        // parametriza la consulta con los datos proporcionados, evitando asi 
        // inyecciones SQL en los datos
        $i = 1;
        if(isset($where)) {
            foreach($where as $key => $_)
                $query->bindParam($i++, $where[$key]);
        }

        // ejecuta la consulta y retorna el resultado de la ejecucion
        return $query->execute();
    }

    /**
     * Implementa la consulta de datos de la interfaz DAO.
     *
     * @see DAO
     */
    public function select($data, $where = null)
    {
        // obtiene la conexion a la base da datos
        $db = DatabaseConnection::getConnection();

        // crea la string de consulta a la BD mediante los datos proporcionados
        $select = "SELECT ";
        $iter = new \CachingIterator(new \ArrayIterator($data));
        foreach ($iter as $column) {
            $select .= $column;
            if ($iter->hasNext()) $select .= ", ";
        }
        $select .= " FROM " . $this->tableName;

        if (isset($where)) {
            $select .= " WHERE ";

            $iter = new \CachingIterator(new \ArrayIterator($where));
            foreach ($iter as $key => $_) {
                $select .= $key . " = ?";
                if ($iter->hasNext()) $select .= " AND ";
            }
        }

        // indica que se utilizara la string anteriormente creada para la 
        // parametrizacion de datos siguiente y la ejecucion de la consulta
        $query = $db->prepare($select);

        // parametriza la consulta con los datos proporcionados, evitando asi 
        // inyecciones SQL en los datos
        if (isset($where)) {
            $i = 1;
            foreach ($where as $key => $_)
                $query->bindParam($i++, $where[$key]);
        }

        // crea un array con todas las tuplas obtenidas en la consulta
        $result = array();
        if ($query->execute()) {
            while ($row = $query->fetch())
                $result[] = $row;
        }

        // retorna el array de tuplas
        return $result;
    }

    /**
     * Implementa la ejecucion de consultas arbitrarias de la interfaz DAO.
     *
     * @see DAO
     */
    public function query($statement)
    {
        // obtiene la conexion a la BD
        $db = DatabaseConnection::getConnection();

        // indica que se utilizara el string proporcionado comp parametro para 
        // la parametrizacion de datos siguiente y la ejecucion de la consulta
        $query = $db->prepare($statement);

        // obtiene, si existen, el resto de argumentos proporcionados al 
        // metodo, y parametriza la consulta con todos ellos
        // ver: http://us3.php.net/func_get_args
        $args = func_get_args();
        for ($i = 1; $i < count($args); $i++)
            $query->bindParam($i, $args[$i]);

        // ejecuta la consulta, almacenando el resultado de la ejecucion
        $result = $query->execute();

        // si la consulta ejecutada proporciona tuplas de resultado (es un 
        // SELECT), guarda todas las mismas en un array
        if ($row = $query->fetch()) {
            $result = [$row];

            while ($row = $query->fetch())
                $result[] = $row;
        }

        // retorna bien el resultado o, si es un SELECT, el array de tuplas 
        // obtenido
        return $result;
    }

}

?>
