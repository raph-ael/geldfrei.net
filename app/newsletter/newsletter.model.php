<?php 
class NewsletterModel extends Model
{
	
	public function listNewsletter()
	{
		if($newsletters = $this->pageList('newsletter',array(),array('email')))
		{
			return $newsletters;
			
		}
		return false;
	}
					
	public function getNewsletter($id)
	{
		if($doc = $this->get('newsletter',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($newsletter)
	{
		if($this->insert('newsletter', array(
			'name' => $newsletter['name'],
			'code' => $newsletter['code'],
			'time' => new MongoDate()
		)))
		{
			
			return true;
		}
		return false;
	}
					
	public function updateNewsletter($id, $data)
	{
		
		if($this->update('newsletter',$id, $data))
		{
			return true;
		}
		return false;
	}
					
	public function deleteNewsletter($id)
	{
		return $this->delete('newsletter',$id);
	}
}
