<?php 
class CommentModel extends Model
{
	
	public function listComment()
	{
		if($comments = $this->pageList('comment',array(),array('ref','text','active','user','rate'),array('active' => 1, 'name' => 1)))
		{
			return $comments;
			
		}
		return false;
	}
	
	public function listNotActive()
	{
		if($comments = $this->pageList('comment',array('active' => false),array('ref','text')))
		{
			return $comments;
				
		}
		return false;
	}

	public function getComments($collection,$id)
	{
		if($comments = $this->qDbRef($collection,$id,'comment'))
		{			
			return $comments;
		}
		return false;
	}
	
	public function getComment($id)
	{
		if($doc = $this->get('comment',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($collection,$id,$comment)
	{
		$active = false;
		$user = '';
		
		if(S::may())
		{
			$active = true;
			$user = S::user('name');
		}
		else
		{
			$user = $comment['name'];
		}
		
		$data = array(
			'text' => $comment['text'],
			'time' => new MongoDate(),
			'ref' => MongoDBRef::create($collection, new MongoId($id)),
			'user_id' => new MongoId(S::id()),
			'user' => $user,
			'active' => $active
		);
		
		if(isset($comment['rate']))
		{
			$data['rate'] = $comment['rate'];
		}
		
		if($this->insert('comment', $data))
		{
			
			return true;
		}
		return false;
	}
					
	public function updateComment($id, $data)
	{
		if($this->update('comment',$id, $data))
		{
			success(s('comment_update_success'));
			return true;
		}
		return false;
	}

	public function setState($id,$active)
	{
		$this->update('comment', $id, array(
			'active' => $active		
		));
	}
	
	public function deleteComment($id)
	{
		return $this->delete('comment',$id);
	}
}
