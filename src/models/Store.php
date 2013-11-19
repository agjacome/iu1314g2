<?php

namespace models;

class Store extends Model
{

    public $commission;

    public function __construct()
    {
        parent::__construct();
    }

    public static function findBy($where = null)
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

    public function fill()
    {
        $this->commission = $this->dao->select(["commission"])["commission"];
    }

    public function save()
    {
        $this->dao->update(["commission" => $this->commission]);
    }

    public function delete()
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

    public function validate()
    {
        return filter_var($this->commission, FILTER_VALIDATE_FLOAT);
    }

}


?>
