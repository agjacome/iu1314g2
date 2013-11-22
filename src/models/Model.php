<?php

namespace models;

/**
 * Representa un modelo del mundo real y se ocupa de relacionarlo con la base de datos.
 *
 * @package  models;
 */

abstract class Model
{
    /**
     * Objeto correspondiente la misma instancia que se haga de la clase heredada.
     * @var SQLXDAO
     */
    protected $dao;

    public function __construct()
    {
        $entity = substr(strchr(get_called_class(), "\\"), 1);
        $this->dao = \database\DAOFactory::getDAO($entity);
    }

    /**
     * Interactúa con la base de datos para realizar búsquedas.
     * @param  array $where restricción en la que basar la búsqueda o consulta
     * @return array Array con el resultado de las tuplas obtenidas.
     */
    public static abstract function findBy($where);

    /**
     * Rellena el objeto con los datos obtenidos de la base de datos
     */
    public abstract function fill();

    /**
     * Interactúa con la base de datos para almacenar objetos.
     */
    public abstract function save();

    /**
     * Interactúa con la base de datos para realizar un borrado.
     */
    public abstract function delete();

    /**
     * Valida los datos introducidos por el usuario
     */
    public abstract function validate();

}

?>
