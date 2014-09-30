<?php 
class MainView extends View
{	
	public function kontaktbox($content)
	{
		return $this->greenbox('<h3>'.$content['title'].'</h3>'.$content['content']);
	}
	
	public function startMap($ret)
	{
		return $ret['content'];
	}
	
	public function kontakt()
	{
		$name = new vFormText('name');
		$msg = new vFormTextarea('message');
		$msg->addChecker('Du hast noch keine Nachricht geschrieben');
		
		$email = new vFormText('email');
		$email->addChecker('Mit der E-Mail Adresse stimmt etwas nicht','email');
		
		$form = new vForm(array(
			$name,
			$email,
			$msg
		));
		
		$form->setSubmit(s('send'));
		
		return $form->render();
	}
}