<?php
class NewsletterXhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new NewsletterModel();
	}
}
