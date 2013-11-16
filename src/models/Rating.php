<?php
	namespace models;

	class Rating extends Model
	{
		private $rate;
		private $commentary;

		public function __construct($rate,$commentary)
		{
			$this->rate=$rate;
			$this->commentary=$commentary;
		}
	}

?>
