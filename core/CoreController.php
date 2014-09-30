<?php
class CoreController
{
	public function imageUpload($uploaded_file,$target_dir,$options = array())
	{
		if(isset($_POST['indb-image'][$uploaded_file]))
		{
			return $uploaded_file;
		}

		if(!is_dir($target_dir))
		{
			mkdir($target_dir);
			chmod($target_dir, '777');
		}
		
		$newname = explode('/', $uploaded_file);
		$newname = end($newname);
		
		$newname = strtolower($newname);
		str_replace(array('ä','ö','ü','ß',' '), array('ae','oe','ue','ss','_'), $newname);
		$ext = explode('.', $newname);
		$ext = end($ext);
		$newname = substr($newname, 0,(strlen($newname)-strlen($ext)-1));
		$newname = preg_replace('/[^a-z0-9_]/', '', $newname);
		$newname .= '.'.$ext;
		
		$tmp = $newname;$i=0;
		while (file_exists($target_dir.$tmp))
		{
			$i++;
			$tmp = $i.'-'.$newname;
		}
		$newname = $tmp;
		
		$options = array_merge(array(
			'crop' => array(
				100,150
			),
			'resize' => array(
				array(800,0),
				array(111,77),
				array(364,254)
			)
		),$options);
		
		foreach ($options['crop'] as $size)
		{
			$file = $target_dir.$size.'x'.$size.'-'.$newname;
			copy($uploaded_file, $file);
			$img = new fImage($file);
			$img->cropToRatio(1, 1);
			$img->resize($size, $size, true);
			$img->saveChanges();
		}
		
		foreach ($options['resize'] as $size)
		{
			try {
				$file = $target_dir.$size[0].'x'.$size[1].'-'.$newname;
				copy($uploaded_file, $file);
				$img = new fImage($file);
				
				if($size[1] > 0)
				{
					$img->cropToRatio($size[0],$size[1]);
					$img->resize($size[0],$size[1],true);
				}
				else
				{
					$img->resize($size[0],$size[1],true);
				}

				$img->saveChanges();
			}
			catch (Exception $e)
			{
				debug($e);
			}
		}
		
		copy($uploaded_file, $target_dir.$newname);
		$img = new fImage($target_dir.$newname);
		$img->resize(1000, 0);
		unlink($uploaded_file);
		
		return $newname;
	}
	
	public function getId($doc)
	{
		return $doc['_id']->{'$id'};
	}
	
	public function setPaginationCount($count)
	{
		global $g_config;
		$g_config['docs_per_page'] = $count;
	}
	
	public function strval($str)
	{
		require strip_tags($str);
	}
	
	public function intval($val)
	{
		return (int)$val;
	}
	
	public function htmlval($html)
	{
		return strip_tags($html,'<a><b><p><div><strong><table><th><td><tr><thead><tbody><tfoot><ul><li>');
	}
	
	public function getPostFile($file)
	{
		if(isset($_FILES[$file]) && (int)$_FILES[$file]['size'] > 0)
		{
			return true;
		}
		return false;
	}
	
	public function getPostLatLng($lat, $lng)
	{
		// later ;)
		if(($lat = $this->getPost($lat)) && ($lng = $this->getPost($lng)))
		{
			return array(floatval($lat),floatval($lng));
		}
		return false;
	}
	
	public function mailme($subject,$text,$from = false, $fromname = false)
	{
		global $g_config;
		
		if(!$from)
		{
			$from = $g_config['noreply'];
		}
		
		$mail = new fEmail();
		$mail->addRecipient($g_config['infomail'],$g_config['project_name']);
		$mail->setBody($text);
		$mail->setSubject($subject);
		
		if(!$fromname)
		{
			$fromname = $from;
		}
		$mail->setFromEmail($from,$fromname);
		
		if($mail->send())
		{
			return true;
		}
	}
	
	public function ipIsBlocked($context = 'default')
	{
		if($block = $this->db->qDoc('ipblock',array('ip' => T::getIp(),'context' => $context),array()))
		{
			if((time() - (int)$block['start']) > (int)$block['duration'])
			{
				$this->db->delete('ipblock',$block['id']);
				return false;
			}
			return true;
		}
			
		return false;
	}
	
	public function setIpTimeBlock($seconds,$context = 'default')
	{
		$this->db->insert('ipblock',array(
			'ip' => T::getIp(),
			'start' => time(),
			'duration' => (int)$seconds,
			'context' => $context
		),false);
	}
	
	public function mailRole($role,$subject, $message, $from = false, $from_name = false)
	{
		if($users = $this->db->listUsersByRole($role))
		{
			foreach ($users as $u)
			{
				$this->mail($subject, $message, $u['email'],false,$u['name']);
			}
		}
	}
	
	public function mail($subject, $message, $recipient, $from = false, $recipient_name = false, $from_name = false)
	{
		global $g_config;
		if(!$from)
		{
			$from = $g_config['noreply'];
		}
		if(!$from_name)
		{
			$from_name = $from;
		}
		if(!$recipient_name)
		{
			$recipient_name = $recipient;
		}
		
		$mail = new fEmail();
		$mail->addRecipient($recipient,$$recipient_name);
		$mail->setFromEmail($from,$from_name);
		$mail->setBody($message);
		$mail->setSubject($subject);
		
		try{
			if($mail->send())
			{
				return true;
			}
		}
		catch (Exception $e)
		{
			log($e->getCode().' '.$e->getMessage());
			return false;
		}
		
		return false;
	}
	
	public function upload($field,$collection)
	{
		try {
			$uploader = new fUpload();

			$uploader->setOptional();
			//$uploader->setMaxSize('100MB');
			$file = $uploader->move(DIR_FILES.$collection.'/'.$field, $field);
			
			return $collection.'/'.$field.'/'.$file->getName();
			
		}
		catch(Exception $e)
		{
			return '';
		}
		return '';
	}
	
	public function getPostZip($zip)
	{
		if($zip = $this->getPost($zip))
		{
			if((int)$zip > 100)
			{
				return preg_replace('/[^0-9]/', '', $zip);
			}
		}
		return false;
		
	}
	
	public function getPostHtml($name)
	{
		if($val = $this->getPost($name))
		{
			$val = strip_tags($val,'<p><ul><li><ol><strong><span><i><div><h1><h2><h3><h4><h5><br><img><table><thead><tbody><th><td><tr><i><a>');
			$val = trim($val);
			if(!empty($val))
			{
				return $val;
			}
		}
		return false;
	}
	
	public function getPostTags($name)
	{
		if($val = $this->getPost($name))
		{
			if($val = json_decode($val,true))
			{
				$out = array();
				foreach ($val as $v)
				{
					$v = trim($v);
					$v = strip_tags($v);
					if(strlen($v) > 2)
					{
						$out[$v] = $v;
					}
				}
				if(!empty($out))
				{
					$out2 = array();
					foreach ($out as $o)
					{
						$out2[] = $o;
					}
					return $out2;
				}
			}
		}

		return false;
	}
	
	public function getPostString($name)
	{
		if($val = $this->getPost($name))
		{
			$val = strip_tags($val);
			$val = trim($val);
			
			if(!empty($val))
			{
				return $val;
			}
		}
		return false;
	}
	
	public function getPostImages($name)
	{
		if(isset($_POST[$name]) && is_array($_POST[$name]))
		{
			$out = array();
			foreach ($_POST[$name] as $image)
			{
				if(isset($_POST[$name.'-exists'][$image]))
				{
					$image = $_POST[$name.'-exists'][$image];
					$image['exists'] = true;
				}
				else 
				{
					$image = array
					(
						'file' => $image,
						'exists' => false
					);
				}
					
				$out[] = $image;
			}
			
			return $out;
		}
		return false;
	}
	
	public function getPostFloat($name)
	{
		if($val = $this->getPost($name))
		{
			$val = trim($val);
			return floatval($var);
		}
		return false;
	}
	
	public function getPostInt($name)
	{
		if($val = $this->getPost($name))
		{
			$val = trim($val);
			return (int)$val;
		}
		return false;
	}
	
	public function getPost($name)
	{
		if(isset($_POST[$name]) && !empty($_POST[$name]))
		{
			return $_POST[$name];
		}
		return false;
	}
	
	public function getPostRegEx($name,$pattern = '/[^a-z0-9A-Z]/')
	{
		if($val = $this->getPost($name))
		{
			$val = trim($val);
			$val = preg_replace($pattern, '', $val);
			if(!empty($val))
			{
				return $val;
			}
		}
		return false;
	}
	
	public function getPostInternUrl($name)
	{
		if($val = $this->getPost($name))
		{
			if(empty($val))
			{
				$val = '/';
			}
			
			if(substr($val, 0,1) == '/')
			{
				return $val;
			}
		}
		return false;
	}
	
	public function getPostZeiten($name)
	{
		if($zeiten = $this->getPostArray($name))
		{			
			$out = array();
			foreach ($zeiten as $dow => $times)
			{
				if($dow >= 0 && $dow <= 6)
				{
					$out[$dow] = array();
					foreach ($times as $t)
					{
						
						if(count($t) == 2)
						{
							$i=0;
							
							$from = $t[0];
							$to = $t[1];
							
							$from = explode(':',$from);
							$hour_from = (int)$from[0];
							$min_from = (int)$from[1];
							
							$to = explode(':',$to);
							$hour_to = (int)$to[0];
							$min_to = (int)$to[1];
							
							if(
								$hour_to >= 0 && $hour_to <= 23 && $min_to >= 0 && $min_to <= 59
								&&
								$hour_from >= 0 && $hour_from <= 23 && $min_from >= 0 && $min_from <= 59
							)
							{
								$out[$dow][] = array(
									'from' => array($hour_from,$min_from),
									'to' => array($hour_to,$min_to)
								);
							}							
						}
						
					}
				}
			}
			if(!empty($out))
			{
				return $out;
			}
		}
		return false;
	}
	
	public function getPostArray($name)
	{
		$ids = array();
		if(isset($_POST[$name]) && is_array($_POST[$name]))
		{
			return $_POST[$name];
		}
		return false;
	}
	
	public function getPostTimeTable($name)
	{
		$ids = array();
		if(isset($_POST[$name]) && is_array($_POST[$name]))
		{
			return $_POST[$name];
		}
		return false;
	}
	
	public function getPostMongoIdArray($name)
	{
		$ids = array();
		if(isset($_POST[$name]) && is_array($_POST[$name]))
		{
			foreach ($_POST[$name] as $id)
			{
				$ids[] = $id;
			}
		}
		return $ids;
	}
	
	public function getPostEmail($name)
	{
		if($val = $this->getPost($name))
		{
			$val = trim($val);
			
			if(filter_var($val,FILTER_VALIDATE_EMAIL))
			{
				return $val;
			}
		}
		return false;
	}
	
	public function getPostUrl($name)
	{
		if($val = $this->getPost($name))
		{
			$val = trim($val);
			if(substr($val, 0,4) != 'http')
			{
				$val = 'http://'.$val;
			}
			if(filter_var($val,FILTER_VALIDATE_URL))
			{
				return $val;
			}
		}
		return false;
	}
	
	public function getPostVideos($name)
	{
		$videos = array();
		if(isset($_POST[$name]) && is_array($_POST[$name]))
		{
			foreach ($_POST[$name] as $v)
			{
				if(is_array($v) && isset($v['code']) && isset($v['url']))
				{
					$v['code'] = trim($v['code']);
					$v['url'] = trim($v['url']);
					
					$videos[] = $v;	
				}
			}
		}
		return $videos;
	}
	
	public function addRequired($fields = array())
	{
		global $g_validation;
		foreach ($fields as $f)
		{
			$g_validation->addRequiredFields($f);
		}
	}
	
	//public function 
	
	public function validate()
	{
		global $g_validation;
		try {
			$g_validation->validate();
			return true;
		}
		catch(Exception $e)
		{
			
		}
		return false;
	}
}