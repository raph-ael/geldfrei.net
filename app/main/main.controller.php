<?php 
class MainController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new MainView();
		$this->db = new MainModel();
		
	}
	
	public function glossar()
	{
		return $this->out(array(
			'main' => 'Hallo Welt'
		));
	}
	
	public function index()
	{	
		return $this->out(array(
			'main' => 'Hallo Welt'
		));
	}
	
	public function impressum()
	{
		$this->addBread('Impressum','/impressum');
		$content = $this->db->getContent('impressum');
		return $this->out(array(
			'main' => $this->view->contentPanel($content)
		));
	}
	
	public function danke()
	{
		$this->addBread('Kontakt','/kontakt');
		$content = $this->db->getContent('danke');
		return $this->out(array(
				'main' => $this->view->contentPanel($content)
		));
	}
	
	public function ueberuns()
	{
		$this->addBread('Über uns','/ueber-uns');
		$content = $this->db->getContent('ueber-uns');
		return $this->out(array(
			'main' => $this->view->contentPanel($content)
		));
	}
	
	public function kontakt()
	{
		$this->addBread('Kontakt','/kontakt');
		
		if($this->isSubmitted())
		{
			$from = $this->getPostEmail('email');
			$fromname = $this->getPostString('name');
			$text = $this->getPostString('message');
			
			
			if($this->mailme('TOH Kontaktformular', $text,$from,$fromname))
			{
				success('Deine Nachricht wurde versendet!');
				go('/danke');
			}
			
		}
		
		$content = $this->db->getContent('kontaktformular');
		$content = '<h1>'.$content['title'].'</h1>'.$content['content'];
		
		return $this->out(array(
			'main' => $this->view->sidebarRight(
				$this->view->whitebox($content.$this->view->kontakt()),
				$this->view->kontaktbox($this->db->getContent('kontakt'))
			)
		));
	}
	
	public function hilfe()
	{
		$this->addBread('Hilfe','/hilfe');
		$content = $this->db->getContent('hilfe');
		return $this->out(array(
			'main' => $this->view->contentPanel($content)
		));
	}
}