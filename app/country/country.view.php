<?php 
class CountryView extends View
{
	public function listCountry($countrys)
	{
		$table = new vTable('name','time');
		$table->setHeadRow(array(
			s('name'),
			s('options'),
		));
	
		foreach($countrys as $country)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/country/edit/'.$country['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/country/delete/'.$country['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $country['name']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,'140');
						
		$panel = new vPanel(s('country'));
		$panel->addElement($table);
		$panel->addButton(s('add_country'), '/country/add','plus-sign');
		return 	$panel->render().
				$this->getPagination(count($countrys));
	}
				
	public function countryForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'name' => '',
			'code' => ''
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$name = new vFormText('name',$values['name']);
		$code = new vFormText('code',$values['code']);

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$name,
			$code
		),array('id' => 'country'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_country'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
