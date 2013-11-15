<?php

namespace views;

class View
{

    private $template;
    private $data;

    public function __contruct($template)
    {
        $this->template = $template;
        $this->data = array();
    }

    public  function render()
    {
        extract($this->data);
        ob_start();
        include $this->template;
        $renderedView = ob_get_clean();
        print $renderedView;
    }

    public function assign($key,$value)
    {
        $this->data[$key] = $value;
    }

}

?>
