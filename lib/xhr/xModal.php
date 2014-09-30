<?php
class xModal
{
	private $title;
	private $content;
	private $id;
	private $buttons;
	private $closeButton;
	
	public function __construct($title, $content = '')
	{
		$this->id = 'x'.uniqid();
		$this->content = $content;
		$this->title = $title;
		$this->buttons = '';
		$this->closeButton = '';
	}
	
	public function setContent($html)
	{
		$this->content = $html;
	}
	
	public function addButtonHtml($html)
	{
		$this->buttons .= $html;
	}
	
	public function addCloseButton()
	{
		$this->closeButton = '<button type="button" class="btn btn-default" data-dismiss="modal">'.s('close').'</button>';
	}
	
	public function out()
	{
		return array(
			'script' => '
				$("body").append(\'<div class="modal fade" id="'.$this->id.'" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'.T::jsSafe($this->title).'</h4></div><div class="modal-body">'.T::jsSafe($this->content).'</div><div class="modal-footer">'.$this->closeButton.$this->buttons.'</div></div></div></div>\');
				
				$("#'.$this->id.'").modal();'
		);

	}
}