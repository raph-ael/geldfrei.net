<?php 
class DownloadsController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new DownloadsView();
		$this->db = new DownloadsModel();
		
		$this->addBread(s('downloads'), '/downloads');
	}
	
	public function index()
	{
		$downloadss = $this->db->listDownloads();
		return $this->out(array(
			'main' => $this->view->index($downloadss)
		));
	}
	
	public function manage()
	{
		if(!S::may('team'))
		{
			go('/');
		}
		$downloadss = $this->db->listDownloads();
		return $this->out(array(
			'main' => $this->view->listDownloads($downloadss)
		));
	}
					
	public function add()
	{	
		if(!S::may('team'))
		{
			go('/');
		}
		if($this->isSubmitted() && ($values = $this->validateDownloads()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'name' => '',
				'desc' => '',
				'file' => ''
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				
				info(s('downloads_add_success'));
				go('/downloads/edit/'.$id);
			}
		}
		
		return $this->out(array(
			'main' => $this->view->downloadsForm()
		));
	}
					
	public function edit()
	{
		if(!S::may('team'))
		{
			go('/');
		}
		if($id = $this->uriMongoId(3))
		{
			if($this->isSubmitted() && ($values = $this->validateDownloads()))
			{
				
				if($this->db->updateDownloads($id,$values))
				{
					$this->info(s('downloads_edit_success'));
				}
			}
			
			if($downloads = $this->db->getDownloads($id))
			{
				
				return $this->out(array(
					'main' => $this->view->downloadsForm($downloads)
				));
			}
			else
			{
				go('/downloads');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteDownloads($id);
			}
		}
		go('/downloads');
	}
					
	public function validateDownloads()
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
		 * validate desc
		 */
		if($value = $this->getPostString('desc'))
		{
			$data['desc'] = $value;
		}
		else
		{
			$check = false;	
		}
					
		/*
		 * validate file
		 */
		if($this->getPostFile('file'))
		{
			$data['file'] = $this->upload('file','downloads');
		}
				
		
		if($check)
		{
			return $data;
		}
		return false;
	}
}
