<?php

namespace models;

abstract class Model
{

    protected $dao;

    public function __construct()
    {
        $entity = substr(strchr(get_called_class(), "\\"), 1);
        $this->dao = \database\DAOFactory::getDAO($entity);
    }

    public abstract function delete();

    public abstract function validate();

}

?>
