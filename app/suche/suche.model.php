<?php 
class SucheModel extends Model
{
	
	public function xhrSearch($lat,$lng,$distance)
	{
		
		if($anbieter = $this->q('anbieter',array(
				'location_coords' => array('$within' => array('$center' => array(array($lat,$lng),$distance)))
		),array('name','teaser','uri','images','rating','rating_count')))
		{
			$out = array();
			foreach ($anbieter as $doc)
			{
				$tmp = array(
					'title' => $doc['name'],
					'teaser' => $doc['teaser'],
					'url' => '/profil/'.$doc['uri'],
					'id' => $doc['_id']->{'$id'}
				);
				
				if(isset($doc['images']) && !empty($doc['images']))
				{
					$tmp['image'] = '/'.$doc['images'][0]['folder'].'100x100-'.$doc['images'][0]['file'];
				}
				
				if(isset($doc['rating']) && (int)$doc['rating'] > 0)
				{
					$tmp['rating'] = $doc['rating'];
					$tmp['rating_count'] = $doc['rating_count'];
				}
				
				$out[] = $tmp;
				
			}
			return array(
				'anbieter' => $out
			);
		}
		
		return false;
	}
	
	/**
	 * get Profiles near tu current users location
	 * 
	 * @return array resulser
	 * @return false is nothing found
	 * 
	 */
	public function getNearProfiles()
	{
		return $this->q('anbieter',array(
			'location_coords' => array('$within' => array('$center' => array(getLocation(),(50/111.12))))
		));
	}
	
	public function search($query,$loc_query = false)
	{		
		$query = trim($query);
		$query = strtolower($query);
		$query = explode(' ',$query);

		$tmp = array();
		if(count($query) > 5)
		{
			return false;
		}
		
		foreach ($query as $q)
		{
			if(strlen($q) > 2)
			{
				$tmp[] = $q;
			}
		}
		
		$query = $tmp;
		
		$out = array();
		
		if($res = $this->search_anbieter($query,$loc_query))
		{
			$out['anbieter'] = $res;
		}
		
		if($res = $this->search_artikel($query))
		{
			$out['artikel'] = $res;
		}
		
		if($res = $this->search_vertriebsmodell($query))
		{
			$out['modelle'] = $res;
		}
		
		if($res = $this->search_produkt($query))
		{
			$out['products'] = $res;
		}
		
		if(!empty($out))
		{
			return $out;
		}
		return false;
	}
	
	public function search_anbieter($query)
	{
		$cursor = $this->db->anbieter->find(array('active' => true),array('name','teaser','uri','images'));
		
		$out = array();
		
		foreach ($cursor as $doc)
		{
			if($this->strposa(array($doc['name'],$doc['teaser']),$query))
			{
				
				$tmp = array(
						'title' => $doc['name'],
						'teaser' => $doc['teaser'],
						'url' => '/profil/'.$doc['uri'],
						'id' => $doc['_id']->{'$id'}
				);
				
				if(isset($doc['images']) && !empty($doc['images']))
				{
					$tmp['image'] = '/'.$doc['images'][0]['folder'].'100x100-'.$doc['images'][0]['file'];
				}
				
				$out[] = $tmp;
			}
		}
		
		if(count($out) > 0)
		{
			return $out;
		}
		return false;
	}
	
	public function search_artikel($query)
	{
		$cursor = $this->db->magazin->find(array(),array('title','teaser','uri'));
		
		$out = array();
		
		foreach ($cursor as $doc)
		{
			if($this->strposa(array($doc['title'],$doc['teaser']),$query))
			{
				$out[] = array(
						'title' => $doc['title'],
						'teaser' => $doc['teaser'],
						'url' => '/magazin/artikel/'.$doc['uri'],
						'id' => $doc['_id']->{'$id'}
				);
			}
		}
		
		if(count($out) > 0)
		{
			return $out;
		}
		return false;
	}
	
	public function search_vertriebsmodell($query)
	{
		$cursor = $this->db->vertriebsmodell->find(array(),array('name','teaser','uri'));
		
		$out = array();
		
		foreach ($cursor as $doc)
		{
			if($this->strposa(array($doc['name'],$doc['teaser']),$query))
			{
				$out[] = array(
						'title' => $doc['name'],
						'teaser' => $doc['teaser'],
						'url' => '/glossar/vertriebsmodell/'.$doc['uri'],
						'id' => $doc['_id']->{'$id'}
				);
			}
		}
		
		if(count($out) > 0)
		{
			return $out;
		}
		return false;
	}
	
	public function search_produkt($query)
	{
		$cursor = $this->db->product->find(array(),array('name','desc','uri'));
	
		$out = array();
	
		foreach ($cursor as $doc)
		{
			if($this->strposa(array($doc['name']),$query))
			{
				$out[] = array(
						'title' => $doc['name'],
						'teaser' => $doc['desc'],
						'url' => '/glossar/produkt/'.$doc['uri'],
						'id' => $doc['_id']->{'$id'}
				);
			}
		}
	
		if(count($out) > 0)
		{
			return $out;
		}
		return false;
	}
	
	private function strposa($haystacks = array(), $needles=array(), $offset=0) 
	{
		foreach($needles as $needle) 
		{
			foreach ($haystacks as $hs)
			{
				$hs = strtolower($hs);
				if(strpos($hs,$needle,$offset) !== false)
				{
					return true;
					break;
				}
			}
		}
		
		return false;
	}
}
