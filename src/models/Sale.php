<?php
	namespace models;

	class Sale extends Model
	{
		private $id;
		public $product;
		public $purchaser;
		public $quantity;
		public $payment;
		public $rating;

		public function __construct()
		{
			parent::__construct();
		}

		public getId()
		{
			return $this->id;
		}

		public makePayment()
		{
			//FALTA POR IMPLEMENTAR
		}

		public rate()
		{
			//FALTA POR IMPLEMENTAR
		}

		public static function findBy($where)
		{
			$rows=\database\DAOFactory::getDAO("sale")->select(["*"],$where);

			if(count($rows)<1) throw new exceptions\NotFoundException("Venta no encontrada");

			$found=array();
			foreach($rows as $row)
			{
				$sale=new Sale();

				$sale->id	=$row["id"];
				$sale->product	=$row["product"];
				$sale->purchaser=$row["purchaser"];
				$sale->quantity	=$row["quantity"];
				$sale->payment	=$row["payment"];
				$sale->rating	=$row["rating"];

				$found[]=$sale;
			}
			return $found;
		}

		public function save()
		{
			//FALTA POR IMPLEMENTAR
		}

		public function delete()
		{
			$this->dao->delete(["id => $this->id]);
		}

		public function validate()
		{
			//FALTA POR IMPLEMENTAR
		}
	}

?>
