<?php
class Model
{
	public $db;
	public $skip;
	public $docs_per_page;
	public $page;
	public $limit;
	
	public function __construct()
	{
		global $g_db;
		global $g_config;
		$this->db = $g_db;
		
		
		/*
		 * init pagination
		 */
		$this->docs_per_page = $g_config['docs_per_page'];
		$this->page = 1;
		
		if(isset($_GET['p']))
		{
			$this->page = (int)$_GET['p'];
			if($this->page == 0)
			{
				$this->page = 1;
			}
		}
		$this->skip = (int)$this->docs_per_page * ($this->page-1);
		$this->limit = $this->docs_per_page;
		
	}
	
	public function makeMongoIds($ids)
	{
		$out = array();
		foreach ($ids as $id)
		{
			$out[] = new MongoId($id);
		}
		
		return $out;
	}
	
	public function getContent($name)
	{
		if($doc = $this->db->content->findOne(array('name'=>$name),array('title','content')))
		{
			return $doc;
		}
	}
	
	public function listUsersByRole($role)
	{
		return $this->q('user', array('role' => $role));
	}
	
	public function q($collection,$query,$fields = array())
	{
		try
		{
			if($cursor = $this->db->$collection->find($query,$fields))
			{
				if(count($cursor) > 0)
				{
					$out = array();
					$i=0;
					foreach ($cursor as $doc)
					{
						$out[$doc['_id']->{'$id'}] = $doc;
						$out[$doc['_id']->{'$id'}]['id'] = $doc['_id']->{'$id'};
						$i++;
					}
					return $out;
				}
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		return false;
	}
	
	public function freeUri($collection,$value,$prefix = false, $field = 'uri')
	{
		$uriname = T::cleanUriName($value);
		
		if($prefix !== false && !empty($prefix))
		{
			$uriname = T::cleanUriName($prefix).'/'.$uriname;
		}
		
		$tmp = $uriname;
		$i=0;
		$safe = 100;
		
		while ($this->exists($collection, $field, $tmp))
		{
			$safe--;
			$i++;
			$tmp = $uriname.'-'.$i;
			if($safe <= 0)
			{
				return false;
				break;
			}
		}
		
		if(!empty($tmp))
		{
			return $tmp;
		}
		return false;
	}
	
	public function exists($collection,$field,$value)
	{
		$check = false;
		try
		{
			$col = $this->db->$collection->find(array($field=>$value));
			foreach ($col as $c)
			{
				$check = true;
			}

			return $check;
		}
		catch (Exception $e)
		{
			return false;
		}
		return false;
	}
	
	public function mayOrg($id)
	{
		if($this->db->user->find(array('_id' => new MongoId(S::id()),'org' => array('$in' => new MongoId($id)))))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getLast($collection,$query = array())
	{
		$doc = $this->db->$collection->find($query)->sort(array('time'=>-1))->limit(1);
		
		
		foreach ($doc as $d)
		{
			$d['id'] = $d['_id']->{'$id'};
			return $d;
		}
	}
	
	public function addGlobalTags($tags,$types = array())
	{
		if(is_string($types))
		{
			$types = array($types);
		}
		if(!empty($tags))
		{
			foreach ($tags as $t)
			{
				$data = array('name' => $t,'types' => array());
				$this->db->tags->insert($data);
				if(!empty($types))
				{
					// add tag types to list$addToSet
					foreach ($types as $type)
					{
						$this->db->tags->update(array('name'=>$t),array('$addToSet' => array('types'=>$type)));
					}
					
				}
			}
		}
	}
	
	public function getTags()
	{
		$tags = $this->db->tags->find();
		
		$out = array();
		foreach ($tags as $t)
		{
			$out[] = $t['name'];
		}
		return $out;
	}
	
	public function findByIds($collection,$fields = array('name'),$ids = array())
	{
		$tmp = array();
		foreach ($ids as $id)
		{
			$tmp[] = new MongoId($id);
		}

		if( $cursor = $this->db->$collection->find(array('_id' => array('$in'=>$tmp)),$fields) )
		{
			$out = array();
			$i=0;
			foreach ($cursor as $doc)
			{
				$out[$i] = $doc;
				$out[$i]['id'] = $doc['_id']->{'$id'};
				$i++;
			}
			return $out;
		}
	}
	
	public function updateRating($collection,$id,$rate)
	{
		$rating = $rate;
		$rating_count = 1;
		
		if($rating_comments = $this->qCol(
			'comment',
			array( 
				'ref.$id' => new MongoId($id),
				'ref.$ref' => $collection
			), 
			'rate' 
		))
		{
			$rating_count = count($rating_comments);
			$tmp = 0;
			foreach ($rating_comments as $r)
			{
				$tmp += (int)$r;
			}
			$rating = floatval(($tmp/$rating_count));
		}
		
		return $this->update($collection, $id, array('rating' => $rating, 'rating_count' => $rating_count));
	}
	
	/**
	 * 
	 * @param String $collection
	 * @param MongoId(String) $id
	 * @param String $ref_collection
	 * @param array $fields
	 * @param string $ref_field
	 * @return Result Array|boolean
	 */
	public function qDbRef($collection,$id,$ref_collection,$fields = array(),$ref_field = 'ref')
	{
		try {
			if($cursor = $this->db->$ref_collection->find(
				array(
					$ref_field.'.$id' => new MongoId($id),
					$ref_field.'.$ref' => $collection
				),
				$fields
			))
			{
				$out = array();
				$i=0;
				foreach ($cursor as $doc)
				{
					$out[$i] = $doc;
					$out[$i]['id'] = $doc['_id']->{'$id'};
					$i++;
				}
				return $out;
			}
		}
		catch (Exception $e)
		{
			T::debug($e);
			return false;
		}
		return false;
	}
	
	public function qCol($collection,$query,$field)
	{
		if( $cursor = $this->db->$collection->find($query,array($field)) )
		{
			$out = array();
			foreach ($cursor as $doc)
			{
				if(isset($doc[$field]))
				{
					$out[] = $doc[$field];
				}
			}
			
			if(count($out) > 0)
			{
				return $out;
			}
		}
		return false;
	}
	
	public function docExists($collection,$id)
	{
		try {
			if($doc = $this->db->$collection->findOne(array('_id' => new MongoId($id)),array('_id')))
			{
				return true;
			}
		}
		catch (Exception $e)
		{
			T::debug($e);
			return false;
		}
		return false;
	}
	
	public function refList($collection,$field = 'name')
	{
		if( $cursor = $this->db->$collection->find(array(),array('name')) )
		{
			$out = array();
			foreach ($cursor as $doc)
			{
				$out[] = array(
					'id' => $doc['_id']->{'$id'},
					'name' => $doc[$field]
				);
			}
			return $out;
		}
	}
	
	public function pageList($collection, $query = array(),$fields = array(),$sort = false)
	{
		global $g_config;
		
		$fields = array_merge(array('time'),$fields);
		if($cursor = $this->db->$collection->find($query, $fields)->limit($this->limit)->skip($this->skip))
		{
			$cursor->sort(array('time' => -1));
			if(isset($_GET['o']))
			{
				$order = preg_replace( '/[^a-z_]/', '', $_GET['o']);
				if(!empty($order))
				{
					$l = 1;
					if(isset($_GET['l']) && $_GET['l'] == 1)
					{
						$l=-1;
					}
					$cursor->sort(array($order=>$l));
				}
			}
			else if($sort)
			{
				$cursor->sort($sort);
			}
			
			$g_config['docs_listing_count'] = $cursor->count();
			$out = array();
			$i=0;
			foreach ($cursor as $c)
			{
				$out[$i] = $c;
				$out[$i]['id'] = $c['_id']->{'$id'};
				$i++;
			}
			return $out;
		}
		return false;
	}
	
	public function setDocsPerPage($count)
	{
		$this->docs_per_page = $count;
	}
	
	public function clearField($collection,$id,$field,$identifier = '_id')
	{
		if(isset($_POST[$field.'-delete']) && is_array($_POST[$field.'-delete']))
		{
			foreach ($_POST[$field.'-delete'] as $delfield)
			{
				$this->db->$collection->update(
					array('_id' => new MongoId($id)),
					array(
							'$pull'=> array($field => array($identifier => $delfield))
					)
				);
			}
		}
	}
	
	public function listAll($collection,$fields = array('name'))
	{
		$fields = array_merge($fields,array('_id'));
		$pr = $this->db->$collection->find(array(),$fields);
		
		if(count($pr) > 0)
		{
			$out = array();
			$i=0;
			foreach ($pr as $p)
			{
				$out[$i] = array();
				foreach ($fields as $f)
				{
					if(isset($p[$f]))
					{
						$out[$i][$f] = $p[$f];
					}
					else
					{
						$out[$i][$f] = false;
					}
				}
				$out[$i]['id'] = $p['_id']->{'$id'};
				$i++;
			}
			return $out;
		}
		return false;
	}
	
	public function findOne($collection,$query)
	{
		try {
			if($doc = $this->db->$collection->findOne($query))
			{
				$doc['id'] = $doc['_id']->{'$id'};
				return $doc;
			}
			
		}
		catch (Exception $e)
		{
			return false;
		}
		return false;
	}
	
	public function qOne($collection,$query,$field)
	{
		try {
			if($doc = $this->db->$collection->findOne($query,array($field)))
			{
				return $doc[$field];
			}
	
		}
		catch (Exception $e)
		{
			return false;
		}
		return false;
	}
	
	public function qDoc($collection,$query,$fields = array())
	{
		try {
			if($doc = $this->db->$collection->findOne($query,$fields))
			{
				$doc['id'] = $doc['_id']->{'$id'};
				return $doc;
			}
				
		}
		catch (Exception $e)
		{
			return false;
		}
		return false;
	}
	
	public function clearImages($collection,$id,$field)
	{
		$delete_arr = array();
		
		$doc = $this->get($collection, $id, array($field));
		
		foreach ($doc[$field] as $img)
		{
			// check is file already in database
			if(!isset($_POST[$field.'-indb'][$img['file']]))
			{
				$delete_arr[] = $img['file'];
				$result = $this->db->$collection->update(
						array('_id' => new MongoId($id)),
						array(
								'$pull'=> array($field => array('file' => $img['file']))
						)
				);
			}
		}
	}
	
	public function update($collection,$id,$doc)
	{	
		try 
		{
			$this->db->$collection->update(
				array('_id' => new MongoId($id)),
				array('$set' => $doc)
			);
			return true;
		}
		catch (Exception $e)
		{
			T::debug($e);
			return false;
		}
		
	}
	
	public function addImage($collection,$id,$field,$images = array())
	{
		foreach ($images as $img)
		{
			$this->db->$collection->update(
				array("_id" => new MongoId($id)),
				array('$push' => array($field => $img))
			);
		}
	}
	
	public function insert($collection,$data,$safe = true)
	{
		try {
			$this->db->$collection->insert($data,array('safe' => $safe));
			return $data['_id'];
		}
		catch (Exception $e)
		{
			T::debug($e);
		}
		return false;
	}
	
	public function delete($collection,$id)
	{
		try {
			$this->db->$collection->remove(array('_id' => new MongoId($id)), array('justOne' => true));
		}
		catch(Exception $e)
		{
			T::debug($e);
		}
		return false;
	}
	
	public function getByUri($collection,$uri,$fields = array())
	{
		try {
			if($doc = $this->db->$collection->findOne(array('uri' => $uri),$fields))
			{
				$doc['id'] = $doc['_id']->{'$id'};
				return $doc;
			}
		}
		catch (Exception $e)
		{
			T::debug($e);
			return false;
		}
		return false;
	}
	
	public function get($collection,$id,$fields = array())
	{
		try {
			if($doc = $this->db->$collection->findOne(array('_id' => new MongoId($id)),$fields))
			{
				$doc['id'] = $doc['_id']->{'$id'};
				return $doc;
			}
		}
		catch (Exception $e)
		{
			T::debug($e);
			return false;
		}
		return false;
	}
}
