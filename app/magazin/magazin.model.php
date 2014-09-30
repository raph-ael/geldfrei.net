<?php 
class MagazinModel extends Model
{
	public function listMagazin()
	{
		if($magazins = $this->pageList('magazin',array(),array('title')))
		{
			return $magazins;
			
		}
		return false;
	}
					
	public function getMagazin($id)
	{
		if($doc = $this->get('magazin',$id))
		{
			return $doc;
		}
		return false;
	}
	
	public function getMagazinByUri($uri)
	{
		return $this->findOne('magazin',array(
			'uri' => $uri
		));
	}
	
	public function listArticle($art = 'all')
	{
		$query = array('published'=>true);
		if($art == 'video')
		{
			$query = array('published'=>true,'videos' => array('$not'=>array('$size'=>0)));
		}
		
		return $this->pageList('magazin',$query,array('title','title_short','featured','images','videos','teaser','uri'));
	}
	
	public function getLastFeatured()
	{
		return $this->getLast('magazin',array('featured'=>true));
	}
		
	public function add($magazin)
	{
		if($uriname = $this->freeUri('magazin', $magazin['title'],$magazin['location_city']))
		{
			$this->addGlobalTags($magazin['tags'],'magazin');
			
			return $this->insert('magazin', array(
				'title' => $magazin['title'],
				'title_short' => $magazin['title_short'],
				'uri' => $uriname,
				'teaser' => $magazin['teaser'],
				'videos' => $magazin['videos'],
				'images' => $magazin['images'],
				'text' => $magazin['text'],
				'tags' => $magazin['tags'],
				'location_coords' => $magazin['location_coords'],
				'location_zip' => $magazin['location_zip'],
				'location_city' => $magazin['location_city'],
				'location_street' => $magazin['location_street'],
				'location_street_number' => $magazin['location_street_number'],
				'featured' => $magazin['featured'],
				'user_id' => new MongoId(S::id()),
				'user' => S::user('name'),
				'time' => new MongoDate()
			));
		}
		return false;
	}
					
	public function updateMagazin($id, $data)
	{
		
		/*
		 * prepare videos 
		 */
		if(isset($data['videos']))
		{
			$old_videos = $this->get('magazin', $id,array('videos'));
					
			if(isset($old_videos['videos']) && is_array($old_videos['videos']))
			{
				$data['videos'] = array_merge($old_videos['videos'], $data['videos'] );
			}
		}
		/*
		 * prepare images	
		 */
		if(isset($data['images']))
		{
			$old_images = $this->get('magazin', $id,array('images'));
					
			if(isset($old_images['images']) && is_array($old_images['images']))
			{
				$data['images'] = array_merge($old_images['images'], $data['images'] );
			}
		}
				
		if($this->update('magazin',$id, $data))
		{
			if(isset($data['tags']))
			{
				$this->addGlobalTags($data['tags'],'magazin');
			}
			return true;
		}
	}
					
	public function deleteMagazin($id)
	{
		return $this->delete('magazin',$id);
	}
}
