<?php 
class KarteController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new KarteView();
		$this->db = new KarteModel();
		
		$this->addBread(s('start'), '/');
	}
	
	public function index()
	{
		
		$this->setTemplate('map');
		$this->addBread(s('map'), '/karte');
	
		addCss('/css/yamm.css');
		addCss('/css/map.css');
		
		$products = $this->db->getProducts();
		$actions = $this->db->getActions();
		
		
		return $this->out(array(
			'main' => $this->view->map(),
			'products' => $this->view->productList($products),
			'consumeraction' => $this->view->actionList($actions),
			'avail' => $this->view->availList(),
			'distance' => $this->view->distance()
		));
	}
	
	private function initLocation()
	{
		
	}
}
