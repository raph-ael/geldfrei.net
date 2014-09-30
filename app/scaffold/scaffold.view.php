<?php 
class ScaffoldView extends View
{
	public function menu()
	{
		
	}
	
	public function scaffoldForm()
	{
		$form = new vForm(array(
			new vFormText('name')
		),array(
			'submit' => 'generate'
		));
		
		$panel = new vPanel('Scaffolding');
		$panel->addElement($form);
		
		return $panel->render();
	}
}