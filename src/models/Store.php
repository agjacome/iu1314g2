<?php

namespace models;

/**
 * Clase que proporciona soporte para manejar Tiendas.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Store extends Model
{

    public $commission;

    /**
     * Construye una nueva instancia de Store a partir de los datos
     * recibidos como parámetros
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * No se debería llamar a este método porque no es aplicable
     */
    public static function findBy($where = null)
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

    /**
     * Rellena el objeto con los datos obtenidos
     * de la base de datos
     *
     * @return boolean
     *     True si se encuentran los datos en la
     *      base de datos
     */
    public function fill()
    {
        $this->commission = $this->dao->select(["commission"])["commission"];
    }

    /**
     * Guarda la tienda en la base de datos ya sea
     * una nueva inserción o una actualización
     *
     * @return boolean
     *     True si se consiguen guardar los datos en
     * la base de datos
     */
    public function save()
    {
        $this->dao->update(["commission" => $this->commission]);
    }

    /**
     * Elimina la tienda de la base de datos
     *
     * @return boolean
     *     True si se consiguen eliminar los datos de
     * la base de datos
     */
    public function delete()
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

    /**
     * Valida los datos que introduce el usuario
     *
     * @return boolean
     *     False si alguno de los datos es incorrecto
     *      o no cumple los requisitos requeridos
     */
    public function validate()
    {
        return filter_var($this->commission, FILTER_VALIDATE_FLOAT);
    }

}


?>
