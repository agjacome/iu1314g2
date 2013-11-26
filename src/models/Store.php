<?php

namespace models;

/**
 * Modelo para parametros de tienda. Soporta todas las operaciones basicas que 
 * se realizaran con los parametros de la tienda.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Store extends Model
{

    public $commission;  // comision cobrada por la tienda en toda transaccion economica

    /**
     * Construye un nuevo modelo de parametros de tienda.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Metodo no aplicable a Store. No tienen sentido una busqueda, solo hay 
     * un unico resultado posible el objeto de parametros de tienda.
     */
    public static function findBy($where = null)
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

    /**
     * Rellena el objeto Store actual con todos los datos, obteniendolos desde 
     * el DAO correspondiente.
     * 
     * @return boolean
     *     True si los datos han podido ser obtenidos correctamente, False en 
     *     caso contrario.
     */
    public function fill()
    {
        $this->commission = $this->dao->select(["commission"])["commission"];
        return true;
    }

    /**
     * Almacena los parametros de la tienda en la base de datos (fichero XML). 
     * Siempre se tratara de una actualizacion, puesto que el fichero se 
     * asumira creado.
     *
     * @return boolean
     *     True si la modificacion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public function save()
    {
        return $this->dao->update(["commission" => $this->commission]);
    }

    /**
     * Metodo no aplicable a parametros de tienda. No tiene sentido borrar la 
     * tienda.
     */
    public function delete()
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

    /**
     * Valida los datos existentes en el objeto, para comprobar que cumplan una 
     * serie de condiciones concretas.
     *
     * @return boolean
     *     True si todas las condiciones necesarias han sido cumplidas, False 
     *     en caso contrario.
     */
    public function validate()
    {
        // valida que la comision sea un valor numerico en coma flotante
        return filter_var($this->commission, FILTER_VALIDATE_FLOAT);
    }

}

?>
