<?php
class Xhr extends CoreController
{
	public function __construct()
	{
		
	}
	
	public function out($data,$script = false)
	{
		return array(
			'data' => $data,
			'script' => $script
		);
	}
	
	public function outDirect($data)
	{
		header('Content-type: application/json');
		echo json_encode($data);
		exit();
	}
}