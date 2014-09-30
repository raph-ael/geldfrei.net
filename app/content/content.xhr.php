<?php
class ContentXhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new ContentModel();
	}
}
