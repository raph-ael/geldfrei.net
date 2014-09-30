<?php 
class CommentController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new CommentView();
		$this->db = new CommentModel();
		
		$this->addBread(s('start'), '/comment');
	}
	
	public function index()
	{
		if(!S::may('team'))
		{
			go('/');
		}
		$comments = $this->db->listComment();
		return $this->out(array(
			'main' => $this->view->listComment($comments)
		));
	}
	
	public function add()
	{	
		if($this->isSubmitted() && ($values = $this->validateComment()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'text' => '',
				'rank' => ''
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				
				info(s('comment_add_success'));
				go('/comment/edit/'.$id);
			}
		}
		
		return $this->out(array(
			'main' => $this->view->commentForm()
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
			if($this->isSubmitted() && ($values = $this->validateComment()))
			{
				
				if($this->db->updateComment($id,$values))
				{
					$this->info(s('comment_edit_success'));
				}
			}
			
			if($comment = $this->db->getComment($id))
			{
				
				return $this->out(array(
					'main' => $this->view->commentForm($comment)
				));
			}
			else
			{
				go('/comment');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteComment($id);
			}
		}
		go('/comment/manage');
	}
	
	public function validateComment()
	{
		$check = true;
		$data = array();
		
					
		/*
		 * validate text
		 */
		if($value = $this->getPostString('text'))
		{
			$data['text'] = $value;
		}
		else
		{
			$check = false;	
		}
					
		/*
		 * validate rank
		 */
		if($value = $this->getPostInt('rank'))
		{
			$data['rank'] = $value;
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
	
	public function manage()
	{
		if(S::may('team'))
		{
			if($comments = $this->db->listComment())
			{
				return $this->out(array(
					'main' => $this->view->listNotActive($comments)
				));
			}
		}
		else
		{
			go('/user/login');
		}
	}
}
