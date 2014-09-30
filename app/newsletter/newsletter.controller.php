<?php 
class NewsletterController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		if(!S::may('team'))
		{
			go('/');
		}
		parent::__construct();
		
		$this->view = new NewsletterView();
		$this->db = new NewsletterModel();
		
		$this->addBread(s('newsletter'), '/newsletter');
	}
	
	public function index()
	{
		
		return $this->manage();
	}
	
	public function manage()
	{
	
		$newsletter = $this->db->listNewsletter();
		return $this->out(array(
				'main' => $this->view->listNewsletter($newsletter)
		));
	}
	
	public function export()
	{
		$newsletter = $this->db->listNewsletter();
		//print_r($newsletter);
		$out = array();
		foreach ($newsletter as $n)
		{
			$out[] = $n['email'];
		}
		echo implode(',', $out);
		exit();
	}
					
	public function add()
	{		
		if($this->isSubmitted() && ($values = $this->validateNewsletter()))
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
				
				info(s('newsletter_add_success'));
				go('/newsletter/edit/'.$id);
			}
		}
		
		return $this->out(array(
			'main' => $this->view->newsletterForm()
		));
	}
					
	public function edit()
	{
		if($id = $this->uriMongoId(3))
		{
			if($this->isSubmitted() && ($values = $this->validateNewsletter()))
			{
				
				if($this->db->updateNewsletter($id,$values))
				{
					$this->info(s('newsletter_edit_success'));
				}
			}
			
			if($newsletter = $this->db->getNewsletter($id))
			{
				
				return $this->out(array(
					'main' => $this->view->newsletterForm($newsletter)
				));
			}
			else
			{
				go('/newsletter');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteNewsletter($id);
			}
		}
		go('/newsletter');
	}
					
	public function validateNewsletter()
	{
		$check = true;
		$data = array();
		
					
		/*
		 * validate name
		 */
		if($value = $this->getPostEmail('email'))
		{
			$data['email'] = $value;
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
