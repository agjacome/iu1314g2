<?php
	namespace models;

	class Product extends Model
	{
		private $id;
		public $name;
		public $description;
		public $owner;
		public $stock;
		public $type;

		public function __construct()
		{
			parent::__construct();
		}

		public function getId()
		{
			return $this->id;
		}

		public static function findby($where)
		{
			$rows = \database\DAOFactory::getDAO("product")->select(["*"], $where);

			$found=array();
			if($rows !== false)
			{
				foreach($rows as $row)
				{
					$product=new Product($row["id"]);

					$product->name	=$row["name"];
					$product->description	=row["description"];
					$product->owner	=$row["owner"];
					$product->stock	=$row["stock"];
					$product->type	=$row["type"];

					$found[]=$product;
				}
			}
			return $found;
		}

		public function save()
		{
			$data = ["id" => $this->login];
			if(isset($this->name))	$data["name"]	=$this->name;
			if(isset($this->description))	$data["description"]	=$this->description;
			if(isset($this->owner))	$data["owner"]	=$this->owner;
			if(isset($this->stock))	$data["stock"]	=$this->stock;
			if(isset($this->type))	$data["type"]	=$this->type;

			$count = $this->dao->select(["COUNT(id)"],["id" => $this->id]) [0] [0];

			if	($count == 0) return $this->dao->insert($data);
			else	($count == 1) return $this->dao->update($data, ["id"=>$this->id]);

			return false;
		}

		public function delete()
		{
			$this->dao->delete(["id"=>$this->id[);
		}

		public function validate()
		{
			//name puede tener letras, números y guiones
			if(!filter_var($this->name, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-zA-Z0-9\-]+/"]]))
				return false;

			//descripcion puede tener letras, numeros, guiones y guiones bajos
			if(!filter_var($this->description, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-zA-Z0-9\-_]+/"]]))
				return false;

			//stock debe ser como mínimo=1
			if(!filter_var($this->stock, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) 
				return false;

			//type  puede contener sólo letras
			if(!filter_var($this->type, FILTERVALIDATE_REGEXP, ["options" => "/[a-zA-Z]/"]]))
				return false;
		}
	}
?>
