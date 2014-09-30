<?php 
class NewsletterView extends View
{
	public function listNewsletter($newsletters)
	{
		$table = new vTable('email','time');
		$table->setHeadRow(array(
			s('email'),
			s('options'),
		));
	
		foreach($newsletters as $newsletter)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/newsletter/edit/'.$newsletter['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/newsletter/delete/'.$newsletter['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $newsletter['email']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,'140');
						
		$panel = new vPanel(s('newsletter'));
		$panel->addElement($table);
		$panel->addButton(s('add_newsletter'), '/newsletter/add','plus-sign');
		$panel->addButton(s('export'), '/newsletter/export','send');
		return 	$panel->render().
				$this->getPagination(count($newsletters));
	}
				
	public function newsletterForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'email' => ''
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$email = new vFormText('email',$values['email']);
		$email->addChecker(s('email_check_invalid'),'email');
		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$email
		),array('id' => 'newsletter'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('edit_newsletter'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
