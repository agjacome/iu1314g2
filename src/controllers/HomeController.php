<?

namespace controllers;

/**
 * Controlador por defecto, en caso de que ningún otro controlador sea llamado en una petición, se llamará a este.
 * También se utiliza para cambiar el idioma de la sesión de usuario.
 * 
 * @package controllers
 */

class HomeController extends Controller
{

    public function __construct($request)
    {
        parent::__construct($request);
    }

    /**
     * Acción por defecto en caso de no encontrarse la pedida. Redirige a la lista de usuarios.
     */
    public function defaultAction()
    {
        $this->view->render("home");
    }

    /**
     * Cambia el lenguaje de la sesión y redirige al índice.
     */
    public function changeLanguage()
    {
        $this->session->lang = $this->request->lang;
        $this->redirect();
    }

}

?>
