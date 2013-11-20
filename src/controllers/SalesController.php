<?php

namespace controllers;

/**
 * Controlador para Ventas y Compras con sus Pagos asociados.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class SalesController extends Controller
{

    private $product  // modelo de producto, se instanciara cuando resulte necesario
    private $sale;    // modelo de venta, se instanciara cuando resulte necesario

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
     * Accion por defecto del controlador de ventas
     */
    public function defaultAction()
    {
        // FIXME: decidir accion por defecto para /index.php?controller=sale
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Crea una nueva venta asociada a un producto dado. Solo se permite 
     * creacion de venta al usuario propietario del producto y/o al 
     * administrador.
     */
    public function create()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Modifica los datos almacenados de una venta. Solo se permite 
     * modificacion al propietario del producto en venta y/o al administrador.
     */
    public function update()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Elimina una venta, estableciendo del producto de nuevo a pendiente. Solo 
     * se permite la eliminacion de la venta al propietario del producto y/o al 
     * administrador.
     */
    public function delete()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona los datos concretos de una venta de producto.
     */
    public function get()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en venta en el sistema.
     */
    public function listing()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en venta por parte del 
     * usuario identificado o un usuario dado si invocado por administrador.
     */
    public function owned()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Proporciona un listado de todos los productos en venta por los cuales el 
     * usuario identificado, o bien uno dado si invocado por administrador, ha 
     * pujado.
     */
    public function purchased()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Realiza una compra de un producto en venta y su pago asociado. Si la 
     * compra no es pagada, no se almacenara en la BD y, por tanto, quedara 
     * descartada. No se consideran "compras pendientes de pago".
     */
    public function purchase()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    /**
     * Metodo privado para manejar el pago de compras.
     */
    private function pay()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}

?>
