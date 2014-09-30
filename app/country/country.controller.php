<?php 
class CountryController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new CountryView();
		$this->db = new CountryModel();
		
		$this->addBread(s('start'), '/country');
	}
	
	public function index()
	{
		
		return $this->manage();
	}
	
	public function manage()
	{
	
		$countrys = $this->db->listCountry();
		return $this->out(array(
				'main' => $this->view->listCountry($countrys)
		));
	}
					
	public function add()
	{		
		if($this->isSubmitted() && ($values = $this->validateCountry()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'name' => '',
				'code' => ''
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				
				info(s('country_add_success'));
				go('/country/edit/'.$id);
			}
		}
		
		return $this->out(array(
			'main' => $this->view->countryForm()
		));
	}
					
	public function edit()
	{
		if($id = $this->uriMongoId(3))
		{
			if($this->isSubmitted() && ($values = $this->validateCountry()))
			{
				
				if($this->db->updateCountry($id,$values))
				{
					$this->info(s('country_edit_success'));
				}
			}
			
			if($country = $this->db->getCountry($id))
			{
				
				return $this->out(array(
					'main' => $this->view->countryForm($country)
				));
			}
			else
			{
				go('/country');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteCountry($id);
			}
		}
		go('/country');
	}
					
	public function validateCountry()
	{
		$check = true;
		$data = array();
		
					
		/*
		 * validate name
		 */
		if($value = $this->getPostString('name'))
		{
			$data['name'] = $value;
		}
		else
		{
			$check = false;	
		}
					
		/*
		 * validate code
		 */
		if($value = $this->getPostString('code'))
		{
			$data['code'] = $value;
		}
		else
		{
			$check = false;	
		}
		
		if($check)
		{
			return $data;
		}
		return false;
	}
}
