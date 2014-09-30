<?php 
class UserModel extends Model
{
	public function listUser()
	{
		if($users = $this->pageList('user',array(),array('name')))
		{
			return $users;
			
		}
		return false;
	}
					
	public function getUser($id)
	{
		if($doc = $this->get('user',$id))
		{
			return $doc;
		}
		return false;
	}
	
	public function login($email,$pass)
	{
		return $this->findOne('user', array(
			'email' => $email,
			'password' => $this->encrypt($email, $pass)
		));	
	
	}
		
	public function add($user)
	{
		return $this->insert('user', array(
			'name' => $user['name'],
			'email' => $user['email'],
			'password' => $this->encrypt($user['email'], $user['password']),
			'groups' => array('user'),
			'role' => 'user',
			'images' => $user['images'],
			'location_coords' => $user['location_coords'],
			'location_zip' => $user['location_zip'],
			'location_city' => $user['location_city'],
			'location_street' => $user['location_street'],
			'location_street_number' => $user['location_street_number'],
			'tags' => $user['tags'],
			'about' => $user['about'],
			'time' => new MongoDate()
		));
	}
	
	public function encrypt($email,$pass)
	{
		return sha1(strtolower($email).'GZKWXrn{d0QU}?oz'.$pass);
	}
	
	public function updateUser($id, $data)
	{
		/*
		 * prepare images	
		 */
		if(isset($data['images']))
		{
			$old_images = $this->get('user', $id,array('images'));
					
			if(isset($old_images['images']) && is_array($old_images['images']))
			{
				$data['images'] = array_merge($old_images['images'], $data['images'] );
			}
		}
				
		if($this->update('user',$id, $data))
		{
			return true;
		}
		return false;
	}
	
	public function listAnbieterProfiles($ids)
	{
		return $this->q('anbieter',array('_id' => array('$in' => $ids)));
	}
}
