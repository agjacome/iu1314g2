<?php

namespace database;

/**
 * Interfaz general para todos los DAOs de la aplicacion.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
interface DAO
{

    /**
     * Realiza una insercion en los datos de los datos proporcionados como 
     * parametro.
     *
     * @param array $data
     *     Array asociativo clave => valor, donde se insertara cada valor dado 
     *     asociado a la clave dada.
     *
     * @return boolean
     *     True si la insercion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public function insert($data);

    /**
     * Realiza una modificacion en los datos de los datos proporcionados como 
     * parametro.
     *
     * @param array $data
     *     Array asociativo clave => valor, donde se modificara cada clave dada 
     *     con el nuevo valor dado.
     * @param array $where
     *     Array asociativo clave => valor, se utilizara para seleccionar que 
     *     valores han de ser modificados.
     *
     * @return boolean
     *     True si la modificacion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public function update($data, $where);

    /**
     * Elimina unos datos seleccionados a traves del parametro proporcionado.
     *
     * @param array $where
     *     Array asociativo clave => valor, se utilizara para seleccionar que 
     *     valores han de ser eliminados.
     *
     * @return boolean
     *     True si la eliminacion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public function delete($where);

    /**
     * Selecciona un conjunto de datos proporcionados sus identificadores y una 
     * condicion de seleccion dada.
     *
     * @param array $data
     *     Array de strings proporcionando los identificadores de los valores a 
     *     seleccionar.
     * @param array $where
     *     Array asociativo clave => valor, se utilizara para seleccionar que 
     *     valores han de ser seleccionados.
     *
     * @return array
     *     Array asociativo clave => valor, donde las claves seran los 
     *     identificadores seleccionados y los valores seran los valores 
     *     asociados a los mismos.
     */
    public function select($data, $where);

    /**
     * Permite la ejecución de una consulta arbitraria sobre los datos.
     *
     * @param String $tatement
     *     La consulta a ejecutar
     *
     * @return mixed
     *     El resultado de la consulta
     */
    public function query($statement);

}

?>
