<?php

namespace models;

/**
 * Modelo abstracto. Clase generica para ser extedida por todos los modelos 
 * concretos de la aplicacion.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
abstract class Model
{

    protected $dao;  // dao asociado al modelo concreto

    /**
     * Constructor abstracto. Inicia el dao al DAO correspondiente (a traves de 
     * DAOFactory) a la entidad del modelo concreto. Si el modelo concreto se 
     * llama, por ejemplo, Product, se asociara a un DAO para la entidad 
     * "product".
     */
    public function __construct()
    {
        // obtiene el nombre de la entidad a partir del nombre de la clase que 
        // herede de este modelo abstracto
        $entity = substr(strchr(get_called_class(), "\\"), 1);

        // obtiene el dao pasandole el nombre de la entidad a DAOFactory
        $this->dao = \database\DAOFactory::getDAO($entity);
    }

    /**
     * Metodo abstracto a ser implementado por los modelos concretos. Realiza 
     * una busqueda en la BD y devuelve un array de objetos del modelo concreto.
     *
     * @param array $where
     *     Array clave => valor describiendo la condicion de busqueda.
     *
     * @return array
     *     Array de Models concretos que cumplen la condicion de busqueda 
     *     especificada.
     */
    public static abstract function findBy($where);

    /**
     * Obtiene desde la base de datos (desde el DAO) todos los atributos del 
     * modelo concreto.
     *
     * @return boolean
     *     True si se han podido obtener correctamente los datos, False en caso 
     *     contrario.
     */
    public abstract function fill();

    /**
     * Almacena en la base de datos el estado actual del modelo concreto, 
     * apoyandose para ello en el uso del DAO. Realizara un insert() o un 
     * update() segun convenga en la implementacion concreta.
     *
     * @return boolean
     *     True si el guardado se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public abstract function save();

    /**
     * Elimina de la base de datos (a traves del DAO) los datos relacionados 
     * con la instancia del modelo concreto que reciba la invocacion del 
     * metodo.
     *
     * @return boolean
     *     True si la eliminacion se ha realizado correctamente, False en caso 
     *     contrario.
     */
    public abstract function delete();

    /**
     * Valida que los datos almacenados en los atributos del modelo concreto 
     * sean correctos en base a una serie de condiciones definidas.
     *
     * @return boolean
     *     True si la validacion ha resultado correcta, False en caso 
     *     contrario.
     */
    public abstract function validate();

}

?>
