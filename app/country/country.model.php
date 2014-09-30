<?php 
class CountryModel extends Model
{
	
	public function listCountry()
	{
		if($countrys = $this->pageList('country',array(),array('name')))
		{
			return $countrys;
			
		}
		return false;
	}
					
	public function getCountry($id)
	{
		if($doc = $this->get('country',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($country)
	{
		if($this->insert('country', array(
			'name' => $country['name'],
			'code' => $country['code'],
			'time' => new MongoDate()
		)))
		{
			
			return true;
		}
		return false;
	}
					
	public function updateCountry($id, $data)
	{
		
		if($this->update('country',$id, $data))
		{
			return true;
		}
		return false;
	}
					
	public function deleteCountry($id)
	{
		return $this->delete('country',$id);
	}
}
