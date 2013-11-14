<?php

namespace database;

abstract class SQLDAO implements DAO
{

    protected $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

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

    public function update($data, $where)
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


    public function delete($where)
    {
        $db = DatabaseConnection::getConnection();

        $delete = "DELETE FROM " . $this->tableName;

        if (isset($where)) {
            $delete .= " WHERE ";
            foreach($where as $key => $value){
                $delete .= $key." = ?";
                if(next($where)) $delete .= " AND ";
            }
        }

        $query = $db->prepare($delete);

        $i = 1;
        if(isset($where)) {
            foreach($where as $key => $value)
                $query->bindParam($i++, $where[$key]);
        }

        $query->execute();
    }

    public function select($data, $where)
    {
        $db = DatabaseConnection::getConnection();

        foreach ($data as $key) {
            $select = "SELECT ? ";
            if (next($data)) $select .= ", ";
        }

        $select .= " FROM " . $this->tableName . " WHERE ";
        if(isset($where)) {
            foreach ($where as $key => $value) {
                $select .= $key . " = ?";
                if (next($where)) $select .= " AND ";
            }
        }
        $query = $db->prepare($select);

        $i = 1;
        foreach ($data as $key)
            $query->bindParam($i++, $data[$key]);

        if (isset($where)) {
            foreach ($where as $key => $value)
                $query->bindParam($i++, $where[$key]);
        }
        
        $query->execute();

        if ($row = $query->fetch()) {
            $result = [$row];
            while ($row = $query->fetch())
                $result[] = $row;
        }
        return $result;
    }

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
