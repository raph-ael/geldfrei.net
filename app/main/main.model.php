<?php 
class MainModel extends Model
{
	
	public function getModels($models)
	{
		return $this->q('vertriebsmodell',array('uri' => array('$in' => $models)),array('teaser','uri','name'));
	}
	
	public function getProfiles()
	{
		return $this->q('anbieter',array(
			'active' => true,
			'images' => array('$not' => array('$size' => 0))
		));
	}
	
	public function addNlAbo($email)
	{
		if($this->findOne('newsletter', array('email' => $email)))
		{
			return false;
		}
		$this->insert('newsletter', array(
			'email' => $email		
		));
	}
	
}