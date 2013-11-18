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
			if(!isset($this->payment)) $this->payment=new Payment($price,$payMethod);
		}

		public rate()
		{
			if(!isset($this->rate)) $this->rate=new Rate($rate,$commentary);
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
			$data = [ "id" => $this->id ];
			if(isset($this->product))	$data["product"]	=$this->product;
			if(isset($this->purchaser))	$data["purchaser"]	=$this->purchaser;
			if(isset($this->quantily))	$data["quantily"]	=$this->quantily;
			if(isset($this->payment))	$data["payment"]	=$this->payment;
			if(isset($this->rating))	$data["rating"]		=$this->rating;

			$count = $this->dao->select(["COUNT(id)"],["id" => $this->id]) [0] [0];

			if	($count == 0) return $this->dao->insert($data);
			else	($count == 1) return $this->dao->update($data, ["id" => $this->id]);

			return false;
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
