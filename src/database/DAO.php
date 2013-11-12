<?php

namespace database;

interface DAO
{

    public function insert($data);

    public function update($data, $where);

    public function delete($where);

    public function select($where);

}

?>
