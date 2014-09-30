<?php
class vFormSwitch extends vFormElement
{
	
	private $off_callback;
	private $on_callback;
	
	public function __construct($id, $value = false, $option = array())
	{
		$option = array_merge(array(
			'on_label' => s('yes'),
			'off_label' => s('no')
		),$option);
		
		$this->off_callback = 'undefined';
		$this->on_callback = 'undefined';
		
		parent::__construct($id,$value,$option);
		
	}
	
	public function setOnCallback($script)
	{
		$this->on_callback = 'function(){'.$script.'}';
	}
	
	public function setOffCallBack($script)
	{
		$this->off_callback = 'function(){'.$script.'}';
	}
	
	public function render()
	{
		addJs('$("#'.$this->id.'").switchButton({
				labels_placement: "right",
				on_label: "'.$this->option['on_label'].'",
				off_label: "'.$this->option['off_label'].'",
				on_callback: '.$this->on_callback.',
				off_callback: '.$this->off_callback.'
		});');
		
		$chk = '';
		if($this->value)
		{
			$chk = ' checked';
		}
		return $this->wrapper('<div><input type="checkbox" name="'.$this->id.'" id="'.$this->id.'"'.$chk.'></div>');
	}
}