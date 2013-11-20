<?php

namespace controllers;

/**
 * Controlador para Subastas y Pujas con sus Pagos asociados.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class BiddingsController extends Controller
{

    private $bidding; // modelo de subasta, se instanciara cuando resulte necesario
    private $product  // modelo de producto, se instanciara cuando resulte necesario

    /**
     * Constructor, construye la instancia de Controller a partir de la 
     * peticion recibida.
     *
     * @param \components\Request $request
     *     Peticion HTTP recibida, encapsulada dentro de un objeto Request (ver 
     *     en namespace components).
     */
    public function __construct($request)
    {
        parent::__construct($request);
    }

    /**
     * Accion por defecto para controlador de subastas
     */
    public function defaultAction()
    {
        // FIXME: decidir accion por defecto para /index.php?controller=bidding
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Crea una nueva subasta asociada a un producto dado. Solo se permite 
     * creacion de subasta al usuario en posesion del producto y/o al 
     * administrador.
     */
    public function create()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Modifica los datos almacenados de una subasta. Solo se permite la 
     * modificacion si no existen pujas para la subasta.
     */
    public function update()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Elimina una subasta, estableciendo el producto de nuevo a pendiente. 
     * Solo se permite la eliminacion de la subasta si no existen pujas o si 
     * asi lo decide el administrador (eliminando todas las pujas asociadas).
     */
    public function delete()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona los datos concretos de una subasta de producto.
     */
    public function get()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en subasta en el sistema.
     */
    public function listing()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en subasta por parte del 
     * usuario identificado o un usuario dado si invocado por administrador.
     */
    public function owned()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en subasta por los cuales 
     * el usuario identificado, o bien uno dado si invocado por administrador, 
     * ha pujado.
     */
    public function bidded()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Realiza una pija de un producto en subasta. Debe comprobar que la puja 
     * sea mas alta a la ultima almacenada en dicha subasta o superior a la 
     * puja minima fijada si no hay pujas. Solo se permitira pujar a usuarios 
     * identificados.
     */
    public function makeBid()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Realiza el pago de una puja dada. Debera realizarse el pago una vez la 
     * subasta ha terminado (se ha llegado a la fecha limite), y solo debera 
     * permitirse el pago de la puja ganadora.
     */
    public function payBid()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todas las subastas ganadas por el usuario 
     * identificado, cuya puja aun no ha sido pagada.
     */
    public function pendingPayments()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}

?>
