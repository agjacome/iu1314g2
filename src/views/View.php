<?php

namespace views;

class View
{

    //private $template;
    private $data;

    public function __construct()
    {
        $this->data = array();
    }

    public  function render($template)
    {
        extract($this->data);
        ob_start();
        include $template;
        $renderedView = ob_get_clean();
        print $renderedView;
    }

    public function assign($key,$value)
    {
        $this->data[$key] = $value;
    }

}
?>
