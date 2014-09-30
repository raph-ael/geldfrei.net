<?php
class GlossarXhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new GlossarModel();
	}
}
