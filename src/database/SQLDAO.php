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
                if (next($where)) $update .= ", ";
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

    }

    public function select($where)
    {

    }

}


?>
