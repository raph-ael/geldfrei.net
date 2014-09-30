<?php
class Controller extends CoreController
{
	private $template;
	private $breadcrumps;
	
	public function __construct()
	{
		$this->setTemplate('default');
		$this->breadcrumps = array();
		
		addJs('
				$("a[href=\''.T::getSelf().'\']").parent("li").addClass("active");
		');
		$this->addBread(S('start'), '/');
		
		if(!S::getLocation())
		{
			addJs('
			$.getJSON("http://www.geoplugin.net/json.gp?ip='.$_SERVER['REMOTE_ADDR'].'&jsoncallback=?", function(data) {
			    if(data.geoplugin_status != undefined && data.geoplugin_status >= 200 && data.geoplugin_status < 300)
				{
					$.getJSON("http://www.geoplugin.net/extras/postalcode.gp?lat="+data.geoplugin_latitude+"&long="+data.geoplugin_longitude+"&format=json&jsoncallback=?", function(plz){
						if(plz.geoplugin_place != undefined)
						{
							ajreq({
								app:"karte",
								action:"setlocation",
								data: {
									lat: data.geoplugin_latitude,
									lng: data.geoplugin_longitude,
									city: plz.geoplugin_place,
									zip: plz.geoplugin_postCode
								}
							});
						}
					});
				}
			});
		');
			
		}
	}
	
	public function fileDownload($file,$name = null)
	{
		$file  = new fFile($file);
		
		$file->output(true,$name);
	}
	
	public function setTemplate($name)
	{
		$this->template = $name;
	}
	
	public function getTemplate()
	{
		return DIR_TEMPLATE.$this->template.'.php';
	}
	
	public function addBread($name,$url)
	{
		$this->breadcrumps[] = array(
			'url' => $url,
			'name' => $name
		);
	}
	
	public function getModToolbar()
	{
		$out = '';
		if(S::may('editor'))
		{
			$out .= '<li><a href="/magazin/manage">'.s('manage_article').'</a></li>';
		}
		if(S::may('team'))
		{
			$out .= '<li><a href="/content/manage">'.s('manage_content').'</a></li>';
			$out .= '<li><a href="/vertriebsmodell/manage">'.s('manage_distribution').'</a></li>';
			$out .= '<li><a href="/consumeraction/manage">'.s('manage_consumeraction').'</a></li>';
			$out .= '<li><a href="/product/manage">'.s('manage_product').'</a></li>';
			
			$out .= '<li><a href="/classification/manage">'.s('manage_classification').'</a></li>';
			$out .= '<li><a href="/downloads/manage">'.s('manage_downlaods').'</a></li>';
			$out .= '<li><a href="/presse/manage">'.s('manage_presse').'</a></li>';

			$out .= '<li><a href="/comment/manage">'.s('manage_comment').'</a></li>';
			$out .= '<li><a href="/anbieter/manage">'.s('manage_anbieter').'</a></li>';
			$out .= '<li><a href="/newsletter">'.s('manage_newsletter').'</a></li>';
		}
		if(S::may('admin'))
		{
			$out .= '<li><a href="/user/manage">'.s('manage_user').'</a></li>';	
			$out .= '<li><a href="/country/manage">'.s('manage_countrys').'</a></li>';
		}
		
		if(!empty($out))
		{
			$out = '
			<div id="modToolbar" class="dropdown">
			  <a class="btn btn-default" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-cog"></span></a>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			    '.$out.'
			  </ul>
			</div>';
		}
		return $out;
	}
	
	public function getBread()
	{
		return $this->view->breadcrumps($this->breadcrumps);
	}
	
	public function uriInt($index)
	{
		if(($val = (int)$this->uri($index)) !== false)
		{
			return $val;
		}
		return false;
	}
	
	public function uriStr($index)
	{
		if(($val = $this->uri($index)) !== false)
		{
			return preg_replace('/[^a-z0-9\-]/','',$val);
		}
		return false;
	}
	
	public function uriMongoId($index)
	{
		if(($val = $this->uri($index)) !== false)
		{
			return preg_replace('/[^a-z0-9]/','',$val);
		}
		return false;
	}
	
	public function isUri($name, $index = 3)
	{
		if($this->uri($index) == $name)
		{
			return true;
		}
		return false;
	}
	
	public function uri($index)
	{
		global $g_requestURI;
		if(isset($g_requestURI[$index]))
		{
			return $g_requestURI[$index];
		}
		return false;
	}
	
	public function addTitle($title)
	{
		global $g_config;
		$g_config['page']['title'][] = $title;
	}
	
	public function getTitle()
	{
		global $g_config;
		return implode($g_config['page']['title_seperator'], $g_config['page']['title']);
	}
	
	public function isSubmitted($form = false)
	{
		if(isset($_POST['submitted']))
		{
			if($form !== false && $_POST['submitted'] != $form)
			{
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function out($arr)
	{
		return $arr;
	}
	
	public function info($msg)
	{
		addJs('info(\''.T::jsSafe($msg).'\');');
	}
	
	public function error($msg)
	{
		addJs('error(\''.T::jsSafe($msg).'\');');
	}
}