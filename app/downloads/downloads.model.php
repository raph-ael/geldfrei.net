<?php 
class DownloadsModel extends Model
{
	
	public function listDownloads()
	{
		if($downloadss = $this->pageList('downloads',array(),array('name','file','desc')))
		{
			return $downloadss;
			
		}
		return false;
	}
					
	public function getDownloads($id)
	{
		if($doc = $this->get('downloads',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($downloads)
	{
		if($this->insert('downloads', array(
			'name' => $downloads['name'],
			'desc' => $downloads['desc'],
			'file' => $downloads['file'],
			'time' => new MongoDate()
		)))
		{
			
			return true;
		}
		return false;
	}
					
	public function updateDownloads($id, $data)
	{
		
		if($this->update('downloads',$id, $data))
		{
			
			return true;
		}
		return false;
	}
					
	public function deleteDownloads($id)
	{
		return $this->delete('downloads',$id);
	}
}
