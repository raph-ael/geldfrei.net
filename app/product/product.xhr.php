<?php
class ProductXhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new ProductModel();
	}
}
