<?php 
class ProductView extends View
{
	public function listProduct($products)
	{
		$table = new vTable('name','time');
		$table->setHeadRow(array(
			s('name'),
			s('options'),
		));
	
		foreach($products as $product)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/product/edit/'.$product['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/product/delete/'.$product['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $product['name']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,'140');
						
		$panel = new vPanel(s('product'));
		$panel->addElement($table);
		
		$panel->addButton(s('add_product'), '/product/add','plus-sign');
		
		return 	$panel->render().
				$this->getPagination(count($products));
	}
				
	public function productForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'name' => '',
			'desc' => '',
			'videos' => '',
			'images' => ''
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$name = new vFormText('name',$values['name']);
		$desc = new vFormTinymce('desc',$values['desc']);
		$videos = new vFormVideo('videos',$values['videos']);
		$images = new vFormImage('images',$values['images']);

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$name,
			$desc,
			$videos,
			$images
		),array('id' => 'product'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_product'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
