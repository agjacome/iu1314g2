<?

namespace controllers;

/**
 * Controlador por defecto, invocado en caso de no especificar ningun 
 * controlador (ejemplo "/index.php").
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class HomeController extends Controller
{

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
     * Accion por defecto del controlador por defecto. Muestra la plantilla 
     * "home.php", despues de pasarle datos de productos en venta y en subasta 
     * (por separado).
     */
    public function defaultAction()
    {
        // obtiene los arrays de productos
        $onSale    = \models\Product::findBy(["estado" => "venta"]);
        $onAuction = \models\Product::findBy(["estado" => "subasta"]);

        // envia los arrays de productos a la vista
        $this->view->assign("sales"    , $onSale);
        $this->view->assign("biddings" , $onAuction);

        // y renderiza la vista concreta
        $this->view->render("home");
    }

    /**
     * Cambia el idioma y redirige de forma acorde a lo especificado.
     */
    public function changeLanguage()
    {
        // cambia el identificador de idioma al especificado en la peticion
        $this->session->lang = $this->request->lang;

        // redirige a lo especificado por la peticio o a la ruta por defecto 
        // "/" si no se ha especificado nada
        if (isset($this->request->redirect))
            $this->redirect(null, null, $this->request->redirect);
        else
            $this->redirect();
    }

}

?>
