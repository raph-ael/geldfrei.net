<?php 
class CommentView extends View
{
	
	public function comments($comments)
	{
		$out = '
		<h4>'.count($comments).' '.s('comments').'</h4>
		<div class="comments">';
		foreach ($comments as $comment)
		{
			$rate = '';
			
			if(isset($comment['rate']))
			{
				$comment['rate'] = round($comment['rate']);
				$rate = '<span class="rating" style="width:'.($comment['rate']*20).'px"></span><span class="rating hold" style="width:'.((5-$comment['rate'])*20).'px"></span>';
			}
			
			$out .= '
			<div class="comment">
				<h4>'.$rate.'<span class="h">'.$comment['user'].' | '.T::date($comment['time']).'</span></h4>
				<p>'.nl2br(T::linkify($comment['text'])).'</p>	
			</div>';
		}
		$out .= '
		</div>';
		
		return $out;
	}
	
	public function listComment($comments)
	{
		$table = new vTable('name','time');
		$table->setHeadRow(array(
			'Referenz',
			s('comment'),
			s('options'),
		));
	
		foreach($comments as $comment)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/comment/edit/'.$comment['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/comment/delete/'.$comment['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $comment['ref']['$ref']),
				array('cnt' => '<strong>von '.$comment['user'].'</strong><br />'.$comment['text']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(0,'100');
		$table->setWidth(2,'100');
						
		$panel = new vPanel(s('comment'));
		$panel->addElement($table);
				
				
		return 	$panel->render().
				$this->getPagination(count($comments));
	}
	
	public function listNotActive($comments)
	{
		
		$table = new vTable('name','time');
		$table->setHeadRow(array(
				'Referenz',
				'Aktiv',
				s('comment'),
				s('options'),
		));
	
		foreach($comments as $comment)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
					'icon' => 'pencil',
					'href' => '/comment/edit/'.$comment['_id'],
					'title' => s('edit')
			));
			$toolbar->addButton(array(
					'icon' => 'trash',
					'href' => '/comment/delete/'.$comment['_id'],
					'title' => s('delete')
			));
			
			$link = $this->linkWrapper($comment['ref']['$ref'], $comment['ref']['$id']);
			
			$as = new vFormSwitch('active');
			$as->setWrapper(false);
			$as->setOffCallBack('ajreq({
				app:"comment",
				action:"setstate",
				data:{v:0,id:"'.$comment['id'].'"}
			});');
			$as->setOnCallBack('ajreq({
					app:"comment",
					action:"setstate",
					data:{v:1,id:"'.$comment['id'].'"}
			});');
			
			if($comment['active'])
			{
				$as->setValue(true);
			}
			
			$activeSwitch = $as->render();
			
			$table->addRow(array(
					array('cnt' => $link),
					array('cnt' => $activeSwitch),
					array('cnt' => '<strong>'.$comment['rate'].' Kartoffeln von '.$comment['user'].'</strong><br />'.$comment['text']),
					array('cnt' => $toolbar->render())
			));
		}
	
		$table->setWidth(0,'100');
		$table->setWidth(1,'100');
		$table->setWidth(3,'100');
	
		$panel = new vPanel(s('comment'));
		$panel->addElement($table);
	
	
		return 	$panel->render().
		$this->getPagination(count($comments));
	}
				
	public function commentForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'text' => '',
			'rank' => ''
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$text = new vFormTextarea('text',$values['text']);
		$rank = new vFormText('rank',$values['rank']);

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$text,
			$rank
		),array('id' => 'comment'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_comment'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
