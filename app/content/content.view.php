<?php 
class ContentView extends View
{
	public function listContent($contents)
	{
		$table = new vTable('name','time');
		$table->setHeadRow(array(
			s('name'),
			s('options'),
		));
	
		foreach($contents as $content)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/content/edit/'.$content['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/content/delete/'.$content['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $content['name']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,'140');
						
		$panel = new vPanel(s('content'));
		$panel->addElement($table);
		
		return 	$panel->render().
				$this->getPagination(count($contents));
	}

	public function contentFormEdit($values = array())
	{
		/*
		 * set default values
		*/
		$values = array_merge(array(
				'name' => '',
				'title' => '',
				'content' => ''
		),$values);
	
	
		/*
		 * set Form Elements
		*/
		$name = new vFormHidden('name',$values['name']);
		$title = new vFormText('title',$values['title']);
		$content = new vFormTinymce('content',$values['content']);
	
		/*
		 * add elemnts to new Form
		*/
		$form = new vForm(array(
				$name,
				$title,
				$content
		),array('id' => 'content'));
	
		/*
		 *	Add everything to panel
		*/
		$panel = new vPanel(sv('edit_content',array('name'=>$values['name'])));
		$panel->addElement($form);
	
		return $panel->render();
	}
	
	public function contentForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'name' => '',
			'title' => '',
			'content' => ''
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$name = new vFormText('name',$values['name']);
		$title = new vFormText('title',$values['title']);
		$content = new vFormTinymce('content',$values['content']);

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$name,
			$title,
			$content
		),array('id' => 'content'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_content'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
