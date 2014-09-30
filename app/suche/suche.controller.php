<?php 
class SucheController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new SucheView();
		$this->db = new SucheModel();
		
		$this->addBread(s('search'), '/suche');
	}
	
	public function index()
	{
		if(isset($_GET['q']))
		{
			
			return $this->search($_GET['q']);
		}
		
		
		
		return $this->out(array(
			'main' => $this->view->search($this->db->getContent('suche')) . $this->nearProfiles()
		));
	}
	
	public function nearProfiles()
	{
		$profiles = $this->db->getNearProfiles();
		
		return $this->view->nearProfiles($profiles);
	}
	
	public function search($query)
	{
		$query = strip_tags($_GET['q']);
		
		$content = '';
		
		if($result = $this->db->search($query))
		{
			$content .= $this->view->results($result,false);
		}
		else
		{
			 $content .= $this->view->contentPanel($this->db->getContent('suche_kein_ergebnis'));
		}
		
		return array(
			'main' => $this->view->search($this->db->getContent('suche'),$content)
		);
	}
}
