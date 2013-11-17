<?

namespace controllers;

class HomeController extends Controller
{

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function defaultAction()
    {
        // FIXME: hack temporal para pruebas
        $this->redirect("user");
    }

    public function changeLanguage()
    {
        $this->session->lang = $this->request->lang;
        $this->redirect();
    }

}

?>
