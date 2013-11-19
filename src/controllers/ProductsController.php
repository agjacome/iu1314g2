<?php

namespace controllers;

class ProductsController extends Controller
{

    private $product;

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function defaultAction()
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

}

?>
