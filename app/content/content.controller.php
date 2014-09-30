<?php 
class ContentController extends Controller
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
		
		$this->view = new ContentView();
		$this->db = new ContentModel();
		
		$this->addBread(s('contents'), '/content');
	}
	
	public function index()
	{
		return $this->manage();
	}
	
	public function manage()
	{
		$contents = $this->db->listContent();
		return $this->out(array(
				'main' => $this->view->listContent($contents)
		));
	}
					
	public function add()
	{		
		if($this->isSubmitted() && ($values = $this->validateContent()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'name' => '',
				'title' => '',
				'content' => ''
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				
				info(s('content_add_success'));
				go('/content/edit/'.$id);
			}
		}
		
		
		
		return $this->out(array(
			'main' => $this->view->contentForm()
		));
	}
					
	public function edit()
	{
		if($id = $this->uriMongoId(3))
		{
			
			if($this->isSubmitted() && ($values = $this->validateContent()))
			{
				
				if($this->db->updateContent($id,$values))
				{
					$this->info(s('content_edit_success'));
				}
			}
			
			if($content = $this->db->getContent($id))
			{
				$this->addBread(sv('edit_name',array('name' => $content['name'])), '/content/edit/'.$id);
				return $this->out(array(
					'main' => $this->view->contentFormEdit($content)
				));
			}
			else
			{
				go('/content');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteContent($id);
			}
		}
		go('/content');
	}
					
	public function validateContent()
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
		 * validate title
		*/
		if($value = $this->getPostString('title'))
		{
			$data['title'] = $value;
		}
					
		/*
		 * validate content
		 */
		if($value = $this->getPostHtml('content'))
		{
			$data['content'] = $value;
		}
				
		
		if($check)
		{
			return $data;
		}
		return false;
	}
}
