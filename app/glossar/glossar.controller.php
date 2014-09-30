<?php 
class GlossarController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new GlossarView();
		$this->db = new GlossarModel();
		
		$this->addBread(s('glossar'), '/glossar');
	}
	
	public function index()
	{
		if($this->isUri('vertriebsmodell',2))
		{
			return $this->modell();
		}
		
		if($this->isUri('zertifizierung',2))
		{
			return $this->certi();
		}
		
		if($this->isUri('produkt',2))
		{
			return $this->product();
		}
		
		$modelle = $this->db->listModell();
		
		return $this->out(array(
			'main' => $this->view->index($modelle)
		));
	}
	
	public function certi()
	{
		if($name = $this->uriStr(3))
		{
			if($modell = $this->db->getClassification($name))
			{
				return $this->out(array(
						'main' => $this->view->certi($modell)
				));
			}
		}
	
		go('/glossar');
	
	}
	
	public function product()
	{
		if($name = $this->uriStr(3))
		{
			if($modell = $this->db->getProduct($name))
			{
				return $this->out(array(
						'main' => $this->view->product($modell)
				));
			}
		}
	
		go('/glossar');
	
	}
	
	public function modell()
	{
		if($name = $this->uriStr(3))
		{
			if($modell = $this->db->getModell($name))
			{
				return $this->out(array(
					'main' => $this->view->modell($modell)
				));
			}
		}

		go('/glossar');

	}			
	
}
