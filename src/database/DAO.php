<?php

namespace database;

/**
 * Interfaz para la definición de inserciones, actualizaciones, borrados y consultas a la base de datos.
 *
 * @package  database;
 * 
 */

interface DAO
{

    public function insert($data);

    public function update($data, $where);

    public function delete($where);

    public function select($data, $where);

    public function query($statement);

}

?>
