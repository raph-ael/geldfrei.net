<?php
class MainXhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new MainModel();

	}
	
	public function tags()
	{
		if($tags = $this->db->getTags())
		{
			return $this->outDirect($tags);
		}
	}
	
	public function nlabo()
	{
		if($email = $this->getPostEmail('email'))
		{
			$this->db->addNlAbo($email);
			return $this->out(array('status' => 1),'$("#newsletter-abo").val("");info("Danke Dir, Ab jetzt bkommst Du unseren Newsletter!");');
		}
		
		return $this->out(array(
			'status' => 0		
		),'error("Da stimmt etwas mit Deiner E-Mail Adresse nicht!");');
	}
}