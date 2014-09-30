<?php 
class DownloadsView extends View
{
	public function index($downloads)
	{
		$out = '<h1 class="line"><span></span>Downloads<span></span></h1>';
		foreach ($downloads as $dl)
		{
			$out .= '
			<div class="whitebox">
				<h2 class="green">'.$dl['name'].'</h2>
				<p>'.$dl['desc'].'</p>
				<p><a target="_blank" class="btn btn-default" href="/files/'.$dl['file'].'">'.s('download').' <span class="glyphicon glyphicon-download"></span></a></p>
			</div>';
		}
		
		$out .= $this->getPagination(count($downloads));
		
		return $out;
		
	}
	
	public function listDownloads($downloadss)
	{
		$table = new vTable('name','time');
		$table->setHeadRow(array(
			s('name'),
			s('options'),
		));
	
		foreach($downloadss as $downloads)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/downloads/edit/'.$downloads['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/downloads/delete/'.$downloads['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => $downloads['name']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,'140');
						
		$panel = new vPanel(s('downloads'));
		$panel->addElement($table);
		$panel->addButton(s('add_downloads'),'/downloads/add','plus-sign');
				
				
		return 	$panel->render().
				$this->getPagination(count($downloadss));
	}
				
	public function downloadsForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'name' => '',
			'desc' => '',
			'file' => ''
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$name = new vFormText('name',$values['name']);
		$desc = new vFormTextarea('desc',$values['desc']);
		$file = new vFormFile('file',$values['file']);

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$name,
			$desc,
			$file
		),array('id' => 'downloads'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_downloads'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
