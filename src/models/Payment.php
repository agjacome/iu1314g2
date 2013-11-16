<?php
	namespace models;

	class Payment extends Model
	{
		public $price;
		public $payMethod;

		public function __construct($rate,$commentary)
		{
		$this->rate=$rate;
		$this->commetary=$commetary;
		}
	}
?>
