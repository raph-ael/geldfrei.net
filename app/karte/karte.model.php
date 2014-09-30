<?php 
class KarteModel extends Model
{
	public function getProducts()
	{
		return $this->listAll('product');
	}
	
	public function getActions()
	{
		return $this->listAll('consumeraction');
	}
	
	public function loadAnbieter($query)
	{
		$query['active'] = true;
		
		$cursor = $this->db->anbieter->find($query,array('location_coords'));
	
		$out = array();
		foreach ($cursor as $doc)
		{
			$out[$doc['_id']->{'$id'}] = array(
				'c' => $doc['location_coords'],
				'id' => $doc['_id']->{'$id'}
			);
		}
		if(!empty($out))
		{
			return $out;
		}
		return false;
	}
}
