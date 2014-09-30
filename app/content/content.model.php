<?php 
class ContentModel extends Model
{
	public function listContent()
	{
		if($contents = $this->pageList('content',array(),array('name')))
		{
			return $contents;
			
		}
		return false;
	}
					
	public function getContent($id)
	{
		if($doc = $this->get('content',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($content)
	{
		if($this->insert('content', array(
			'name' => $content['name'],
			'content' => $content['content'],
			'time' => new MongoDate()
		)))
		{
			
			return true;
		}
		return false;
	}
					
	public function updateContent($id, $data)
	{
		
		if($this->update('content',$id, $data))
		{
			return true;
		}
		return false;
	}
					
	public function deleteContent($id)
	{
		return $this->delete('content',$id);
	}
}
