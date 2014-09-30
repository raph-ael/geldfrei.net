<?php 
class UserController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new UserView();
		$this->db = new UserModel();
	}
	
	public function index()
	{
		
		
	}

	public function manage()
	{
		if(S::may('admin'))
		{
			$this->addBread(s('manage_user'), '/user/manage');
			
			$users = $this->db->listUser();
			return $this->out(array(
					'main' => $this->view->listUser($users)
			));
		}
		else
		{
			go('/');
		}
	}
	
	public function registerorg()
	{
		$org = loadApp('anbieter');
		
		return $org->add();
	}
	
	public function register()
	{	
		$this->addBread(s('register'), '/user/register');
		
		if(S::may())
		{
			info(s('still_logged_in'));
			go('/user/panel');
		}
		if($this->isSubmitted() && ($values = $this->validateUser()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'name' => '',
				'email' => '',
				'password' => '',
				'images' => array(),
				'location_lat' => '',
				'location_lng' => '',
				'location_street' => '',
				'location_street_number' => '',
				'location_zip' => '',
				'location_city' => '',
				'tags' => array(),
				'about' => ''
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				
				//$this->db->addTags($values['tags']);
				
				
				if($user = $this->db->login($values['email'], $values['password']))
				{
					S::login($user);
					info(s('user_add_success'));
					S::set('first_login', true);
					
					if(isset($_POST['isanbieter']))
					{
						go('/anbieter/register');
					}
					
					go('/user/panel');
				}
				
			}
		}
		
		return $this->out(array(
			'main' => $this->view->userRegForm(array(),$this->isUri('anbieter'))
		));
	}

	public function panel()
	{		
		$this->addBread(s('user_panel_title'),'/user/panel');
		if(S::may())
		{
			
			if($this->isSubmitted() && ($values = $this->validateUser()))
			{
				$this->db->clearField('user',S::id(),'images','file');
			
				if($this->db->updateUser(S::id(),$values))
				{
					$this->info(s('user_edit_success'));
				}
			}
			
			if($user = $this->db->getUser(S::id()))
			{
				$main = '';
				$sidebar = $this->view->menu();
				
				if($this->isUri('settings'))
				{
					$this->addBread(s('settings'),'/user/panel/settings');
					$main = $this->view->settings($user);
				}
				else if($this->isUri('anbieter'))
				{
					if(isset($user['org']) && !empty($user['org']))
					{
						if(count($user['org']) == 1)
						{
							go('/anbieter/edit/'.$user['org'][0]->{'$id'});
						}
						else if($profiles = $this->db->listAnbieterProfiles($user['org'])) 
						{
							$main = $this->view->listAnbieterProfiles($profiles);
						}
					}
					$this->addBread(s('anbieter'),'/user/panel/anbieter');
					$anbieter = loadApp('anbieter');
				}
				else
				{
					$ret_first = $this->db->getContent('user_erster_login');
					$ret = $this->db->getContent('user_panel');
					$main = $this->view->overview($ret,$ret_first);
				}
				
				return $this->out(array(
					'main' => '<h1 class="line"><span></span>'.s('user_panel_title').'<span></span></h1>'.$this->view->sidebarRight($main, $sidebar)
				));
			}
			
		}
		else
		{
			goLogin();
		}
		// hmm !? go to startpage
		go('/');
	}
	
	public function logout()
	{
		S::logout();
		info(s('logout_success'));
		go('/');
	}
	
	public function login()
	{
		$this->addBread(s('user_login'), '/user/login');
		
		if($this->isSubmitted() && ($user = $this->db->login($this->getPost('email'), $this->getPost('password'))) && ($goto = $this->getPostInternUrl('goto')))
		{
			if(S::login($user))
			{
				go($goto);
			}
			else
			{
				error(s('init_session_error'));
			}
		}
		elseif ($this->isSubmitted() && ($goto = $this->getPostInternUrl('goto')))
		{
			error(s('login_wrong'));
			go('/user/login?ref='.$goto);
		}
		
		if(!S::may())
		{
			$goto = '/user/panel';
			if(isset($_GET['ref']))
			{
				$url = strip_tags($_GET['ref']);
				if(!empty($url))
				{
					$goto = ($url);
				}
			}
			return $this->out(array(
				'main' => $this->view->login($goto)
			));
		}
		else
		{
			go('/user/panel');
		}
	}
	
	public function edit()
	{
		if(S::may('admin'))
		{
			$this->addBread(s('manage_user'), '/user/manage');
			
			if($id = $this->uriMongoId(3))
			{
				if($this->isSubmitted() && ($values = $this->validateUser()))
				{
					$this->db->clearField('user',$id,'images','file');
					
					if($this->db->updateUser($id,$values))
					{
						info(s('user_edit_success'));
					}
				}
				
				if($user = $this->db->getUser($id))
				{
					$this->addBread(sv('edit_user',array('name'=>$user['name'])),'/user/edit/'.$id);
					return $this->out(array(
						'main' => $this->view->userForm($user)
					));
				}
			}
		}
		go('/');
	}
					
	public function validateUser()
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
					
		/*
		 * validate email
		 */
		if($value = $this->getPostString('email'))
		{
			$data['email'] = $value;
		}
					
		/*
		 * validate password
		 */
		if($value = $this->getPostString('password'))
		{
			$data['password'] = $value;
		}

		/*
		 * validate role
		*/
		if($value = $this->getPostString('role'))
		{
			$data['role'] = $value;
		}
		
		/*
		 * validate images
		 */
		if($images = $this->getPostImages('images'))
		{
			$data['images'] = array();
			foreach($images as $i => $image)
			{
					
				if(!$image['exists'] && file_exists(DIR_UPLOAD.$image['file']))
				{
					$image_name = $this->imageUpload(DIR_UPLOAD.$image['file'],DIR_IMG.'user/images/');

					$data['images'][] = array
					(
						'file' => $image_name,
						'folder' => DIR_IMG.'user/images/',
						'time' => new MongoDate(),
						'name' => '',
						'desc' => ''
					);
				}
				else if($image['exists'])
				{
					$data['images'][] = $image;
				}
			}
		}
					
		/*
		 * validate location
		 */

		if($value = $this->getPostLatLng('location_lat','location_lng'))
		{
			$data['location_coords'] = $value;
		}
		if($value = $this->getPostString('location_street'))
		{
			$data['location_street'] = $value;
		}
		if($value = $this->getPostString('location_street_number'))
		{
			$data['location_street_number'] = $value;
		}
		if($value = $this->getPostZip('location_zip'))
		{
			$data['location_zip'] = $value;
		}
		if($value = $this->getPostString('location_city'))
		{
			$data['location_city'] = $value;
		}
		
					
		/*
		 * validate tags
		 */
		if($value = $this->getPostTags('tags'))
		{
			$data['tags'] = $value;
		}
					
		/*
		 * validate about
		 */
		if($value = $this->getPostHtml('about'))
		{
			$data['about'] = $value;
		}		
	
		/*
		 * 
		 */
		$this->addRequired(array(
			'name',
			'email',
			'password'
		));
		
		if($check)
		{
			return $data;
		}


		return false;
	}
}
