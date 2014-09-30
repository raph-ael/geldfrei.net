<?php
class KarteXhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new KarteModel();
	}
	
	public function setLocation()
	{
		if($latlng = $this->getPostLatLng('lat', 'lng'))
		{
			S::set('g_location', $latlng);
			if($city = $this->getPostString('city'))
			{
				S::set('g_location_city', $city);
			}
			if($zip = $this->getPostZip('zip'))
			{
				S::set('g_location_zip', $zip);
			}
		}
	}
	
	public function mapinput()
	{
		$query = array();
		$now_open = false;
		$anbieter = array();
		
		$no_query = true;
		
		$distance = false;
		
		if(isset($_POST['distance']) && (int)$_POST['distance'] > 0)
		{
			$distance = ((int)$_POST['distance']/111.12);
				
			$location = getLocation();
			
			if(isset($_POST['latlng']))
			{
				if($loc = json_decode($_POST['latlng']))
				{
					$location = array(floatval($loc[0]),floatval($loc[1]));
				}
				
			}
			
			$query['location_coords'] = array('$within' => array('$center' => array($location,$distance)));
		}
		
		if(isset($_POST['products']))
		{	
			$query['products'] = array('$in' => $_POST['products']);
			
			$no_query = false;
			if($a = $this->db->loadAnbieter($query))
			{
				$anbieter = array_merge($anbieter,$a);
			}
			
		}
		if(isset($_POST['actions']))
		{
			$query['consumeraction'] = array('$in' => $_POST['actions']);
			$no_query = false;
			if($a = $this->db->loadAnbieter($query))
			{
				$anbieter = array_merge($anbieter,$a);
			}
		}
		if(isset($_POST['avail']))
		{
			
			$avail = $_POST['avail'];
			foreach ($avail as $i => $v)
			{
				if($v == 'now_open')
				{
					unset($avail[$i]);
					$now_open = true;
				}
			}
			if(!empty($avail))
			{
				$no_query = false;
				$query['availability'] = array('$in' => $avail);
				if($a = $this->db->loadAnbieter($query))
				{
					$anbieter = array_merge($anbieter,$a);
				}
			}
		}
		
		if($no_query)
		{
			$anbieter = $this->db->loadAnbieter($query);
		}
		
		if(!empty($anbieter))
		{
			$out = array();
			foreach ($anbieter as $a)
			{
				$out[] = $a;
			}
			return $this->outDirect($out);
		}
		
		return $this->outDirect(false);
	}
}
