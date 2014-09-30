<?php
class CommentXhr extends Xhr
{
	public $db;
	private $view;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new CommentModel();
		$this->view = new CommentView();
	}
	
	public function load()
	{
		if(($col = $this->getPostString('c')) && ($id = $this->getPostString('id')))
		{
			if($comments = $this->db->getComments($col,$id))
			{
				
				$out = array();
				
				foreach ($comments as $c)
				{
					if(!isset($c['active']) || $c['active'] === true)
					{
						$out[] = $c;
					}
				}
				
				return $this->out($this->view->comments($out));
			}
		}
	}
	
	public function add()
	{
		if(!s::may() && $this->ipIsBlocked('add-comment'))
		{
			return $this->out(false,'alert("Du hast gerade erst einen Kommentar abgegeben und kannst aus Sicherheitsgründen leider nicht direkt noch einen schreiben.");');
		}
		
		$data = array(
			'text' => ''
		);
		
		if(($collection = $this->getPostString('c')) && ($id = $this->getPostString('id')))
		{
			if($this->db->docExists($collection,$id))
			{
				if($text = $this->getPostString('t'))
				{
					$data['text'] = substr($text,0,5000);
				}
				
				if($rate = $this->getPostInt('r'))
				{
					
					if($rate > 0 && $rate <= 5)
					{
						$data['rate'] = $rate;
					}
				}
				
				if(!S::may())
				{
					if($name = $this->getPostString('u'))
					{
						$data['name'] = $name;
					}
					
					$this->mailRole('team', 'Neuer Kommentar auf TOH Profil', "Neuer Kommentar wurde eingetragen\n\nFreischalten unter http://www.tasteofheimat.de/comment/manage\n\n" . $data['name'].' schrieb:'."\n".$data['text']);

				}
				else
				{
					$data['name'] = S::user('name');
				}
				
				if($this->db->add($collection,$id,$data))
				{
					
					$this->setIpTimeBlock(120,'add-comment');
					
					if(isset($data['rate']))
					{
						return $this->db->updateRating($collection,$id,$data['rate']);
					}
					else
					{
						return true;
					}
				}
			}
			else
			{
				echo $id;
				echo $col;
				echo '?';
			}
		}
		else
		{
			echo $_POST['c'];
		}
		
		return false;
	}
	
	public function setstate()
	{
		if(S::may())
		{
			if(isset($_POST['v']) && ($id = $this->getPost('id')))
			{
				if($_POST['v'] == 1)
				{
					$this->db->setState($id,true);
				}
				elseif($_POST['v'] == 0)
				{
					$this->db->setState($id,false);
				}
			}
		}
	}
}
