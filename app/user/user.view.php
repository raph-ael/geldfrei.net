<?php 
class UserView extends View
{
	public function listUser($users)
	{
		$table = new vTable('name','time');
		$table->setHeadRow(array(
			s('name'),
			s('options'),
		));
	
		foreach($users as $user)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/user/edit/'.$user['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/user/trash/'.$user['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $user['name']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,'140');
						
		$panel = new vPanel(s('user'));
		$panel->addElement($table);
		
		return 	$panel->render().
				$this->getPagination(count($users));
	}
	
	public function userRegForm($values = array(), $isAnbieter = false)
	{
		/*
		 * set default values
		*/
		$values = array_merge(array(
				'name' => '',
				'email' => '',
				'password' => '',
				'images' => '',
				'location' => '',
				'tags' => '',
				'about' => '',
				'org' => false
		),$values);
	
	
		/*
		 * set Form Elements
		*/
		$name = new vFormText('name',$values['name']);
		$name->setRequired();
		
		$email = new vFormText('email',$values['email']);
		$email->addChecker(s('wrong_email'),'email');
		$email->setRequired();
		
		$password = new vFormPassword('password',$values['password']);
	
		/*
		 * add elemnts to new Form
		*/
		$form = new vForm(array(
				$name,
				$email,
				$password,
				//$images,
				//$location,
				//$tags,
				//$about
		),array('id' => 'user'));
	
		if($isAnbieter)
		{
			$form->addHidden('isanbieter', true);
			$form->setSubmit(s('forward_step2'));
		}
		
		/*
		 *	Add everything to panel
		*/
		
		$title = s('register');
		if($isAnbieter)
		{
			$title = s('register_anbieter');
		}
		
		$panel = new vPanel($title);
		$panel->addElement($form);
	
		return $panel->render();
	}
	
	public function menu()
	{
		$menu = new vMenu(array(
			new vMenuItem(s('overview'),'/user/panel'),
			new vMenuItem(s('settings'),'/user/panel/settings'),
			new vMenuItem(s('anbieter_profiles'),'/user/panel/anbieter'),
			new vMenuItem(s('logout'),'/user/logout')
		));
		
		$out = '
		<div class="greenbox">
			<h3>'.s('options').'</h3>
			'.$menu->render().'
		</div>';
		
		return $out;
	}
	
	public function overview($ret,$ret_firstlogin)
	{
		
		
		$panel = new vPanel(s('panel_overview'));
		$panel->addElement('
			... hier kurzer überblick zu den user einstellungen		
		');
		$out = '';
		if(S::get('first_login'))
		{
			$out .= '
			<div class="whitebox">
				<h2>'.$ret_firstlogin['title'].'</h2>
				'.$ret_firstlogin['content'].'
			</div>';
		}
		
		return $out.'
			<div class="whitebox">
				<h2>'.$ret['title'].'</h2>
				'.$ret['content'].'
			</div>';
	}
	
	public function settings($user)
	{
		/*
		 * set default values
		*/
		$values = array_merge(array(
				'images' => array(),
				'location_lat' => '',
				'location_lng' => '',
				'location_street' => '',
				'location_street_number' => '',
				'location_zip' => '',
				'location_city' => '',
				'tags' => array(),
				'about' => ''
		),$user);	
		

		$images = new vFormImage('images',$values['images']);
		$images->setImageCount(1);
		$location = new vFormLocation('location',$values);
		$tags = new vFormTags('tags',$values['tags']);
		$about = new vFormTextarea('about',$values['about']);
		
		
		/*
		 * add elemnts to new Form
		*/
		$form = new vForm(array(

				$images,
				$location,
				$tags,
				$about
		),array('id' => 'user'));
		
		return '
		<div class="whitebox">
			<h2>Einstellungen</h2>
			'.$form->render().'
		</div>';
	}
	
	public function login($goto)
	{
		$form = new vForm(array(),array('submit'=>s('login')));
		
		$email = new vFormText('email');
		$pass = new vFormText('password');
		$pass->setPassword();
		
		$form->add($email);
		$form->add($pass);
		
		$form->addHidden('goto',$goto);
		
		
		$panel = new vPanel(s('user_login'));
		$panel->addElement($form);
		
		return $panel->render();
	}

	public function panel($values)
	{
		
	}
	
	public function userForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'name' => '',
			'email' => '',
			'password' => '',
			'images' => '',
			'location' => '',
			'tags' => '',
			'about' => '',
			'role' => array()
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$name = new vFormText('name',$values['name']);
		$email = new vFormNone('email',$values['email']);
		$images = new vFormImage('images',$values['images']);
		$location = new vFormLocation('location',$values);
		$tags = new vFormTags('tags',$values['tags']);
		$about = new vFormWysiwyg('about',$values['about']);
		$role = new vFormSelect('role', array(
			array('id' => 'user', 'name' => 'normale/r Benutzer/in'),
			array('id' => 'editor', 'name' => 'Redakteur/in'),
			array('id' => 'team', 'name' => 'TOH Team'),
			array('id' => 'admin', 'name' => 'Administrator/in')
		),$values['role']);

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$name,
			$role,
			$email,
			$images,
			$location,
			$tags,
			$about
		),array('id' => 'user'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_user'));
		$panel->addElement($form);
		
		return $panel->render();
	}
	
	public function listAnbieterProfiles($profiles)
	{
		$out = '
		<div class="whitebox">';
		foreach($profiles as $id => $p)
		{
			$out .= '
			<div class="anbieter">
				<a href="/anbieter/edit/'.$id.'" class="thumbnail pull-left" style="margin-right:15px;"><span style="display:block;height:100px;width:100px;background-image:url(/css/img/carrot.png);background-position:center;"></span></a>
				<h4 style="margin-bottom:8px;"><a href="/anbieter/edit/'.$id.'">'.$p['name'].'</a></h4>
				<p>'.$p['teaser'].'</p>
				<div class="clearfix"></div>
				<hr>
			</div>';
		}
		
		$out .= '</div>';
		
		return $out;
	}
}
