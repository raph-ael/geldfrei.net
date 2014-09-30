<?php
class SucheXhr extends Xhr
{
	private $db;
	private $view;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new SucheModel();
		$this->view = new SucheView();
	}
	
	public function search()
	{
		if(isset($_POST['l']) && count($_POST['l']) == 2 && isset($_POST['d']))
		{
			$lat = floatval($_POST['l'][0]);
			$lng = floatval($_POST['l'][1]);
			
			$distance = floatval(((int)$_POST['d']/111.12));
			
			
			if($results = $this->db->xhrSearch($lat,$lng,$distance))
			{
				return array(
					'html' => $this->view->results($results,false)
				);
			}
			else 
			{
				$ret = $this->db->getContent('suche_kein_ergebnis');
				return array(
					'html' => '<h3>'.$ret['title'].'</h3>'.$ret['content']
				);
			}
		}
	}
}
