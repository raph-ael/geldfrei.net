<?php
$g_script = array();
$g_css_files = array();
$g_js = '';
$g_js_func = '';

function addScriptTop($src,$compress = false)
{
	global $g_script;
	$new = array();
	$new[$src] = $compress;
	foreach ($g_script as $src => $v)
	{
		$new[$src] = $v;
	}
	$g_script = $new;
}

function getListingCount()
{
	global $g_config;
	return $g_config['docs_listing_count'];
}

function addCssTop($src,$compress = false)
{
	global $g_css_files;
	$new = array();
	$new[$src] = $compress;
	foreach ($g_css_files as $src => $v)
	{
		$new[$src] = $v;
	}
	$g_css_files = $new;
}
function addScript($src,$compress = false)
{
	global $g_script;
	$g_script[$src] = $compress;
}
function addCss($src,$compress = false)
{
	global $g_css_files;
	$g_css_files[$src] = $compress;
}
function go($url)
{
	fURL::redirect($url);
}
function addJs($js)
{
	global $g_js;
	$g_js .= ' '.$js;
}

function addJsFunc($js)
{
	global $g_js_func;
	$g_js_func .= ' '.$js;
}
function getLocation()
{
	if($loc = S::getLocation())
	{
		return $loc;
	}
	return array(50.75592,10.283203);
}

function info($msg,$title = false)
{
	//addJs('info(\''.T::jsSafe($msg).'\');');
	S::addMsg($msg, 'info',$title);
}

function goLogin()
{
	go('/user/login?ref='.urldecode(T::getSelf()));
}

function error($msg,$title = false)
{
	S::addMsg($msg, 'error',$title);
}
function success($msg,$title = false)
{
	S::addMsg($msg, 'success',$title);
}

function getHead()
{
	global $g_config;
	global $g_js;
	global $g_js_func;
	global $g_script;
	global $g_css_files;
	
	$out = '';
	
	foreach ($g_css_files as $src => $compress)
	{
		if(!$compress)
		{
			$out .= '<link rel="stylesheet" href="'.$src.'">
		';
		}
	}
	$out .= '<link rel="stylesheet" href="/css/style.min.css">
		';
	
	foreach ($g_script as $src => $compress)
	{
		if(!$compress)
		{
			$out .= '<script src="'.$src.'"></script>
		';
		}
	}
	$out .= '<script src="/js/script.min.js"></script>
		';
	$out .= '
	<style type="text/css">
		'.CssMin::minify(file_get_contents(DIR_APP.$g_config['app']['folder'].'/'.$g_config['app']['folder'].'.style.css')).'
	</style>';
	$out .= '
	<script type="text/javascript">
		'.JSMin::minify(
				
		$g_js_func.'
		$(document).ready(function(){
			'.$g_js.'
		});
				
		').'	
	</script>';
	return $out;
}

function getFoot()
{
	
	$out = '
<script type="text/javascript">
 	var _paq = _paq || [];  _paq.push(["trackPageView"]);  _paq.push(["enableLinkTracking"]);  (function() {    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://piwik.tasteofheimat.de/"; _paq.push(["setTrackerUrl", u+"piwik.php"]);    _paq.push(["setSiteId", "1"]);    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript"; g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);  })();
	var _urq = _urq || [];  _urq.push([\'initSite\', \'3260660c-60ce-4056-b2ba-45cec224046d\']);(function() {var ur = document.createElement(\'script\'); ur.type = \'text/javascript\'; ur.async = true;ur.src = (\'https:\' == document.location.protocol ? \'https://cdn.userreport.com/userreport.js\' : \'http://cdn.userreport.com/userreport.js\');var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ur, s);})();
</script>
';
	
	return $out;
}

function getContent($index)
{
	global $g_content;
	if(isset($g_content[$index]))
	{
		return $g_content[$index];
	}
	return '';
}

function loadApp($folder)
{
	require_once DIR_APP.$folder.'/'.$folder.'.model.php';
	require_once DIR_APP.$folder.'/'.$folder.'.view.php';
	require_once DIR_APP.$folder.'/'.$folder.'.controller.php';
	include DIR_LANG.$folder.'.lang.'.LANG.'.php';
	addJsFunc(file_get_contents(DIR_APP.$folder.'/'.$folder.'.script.js'));
	
	$class = ucfirst($folder).'Controller';
	
	return new $class();
}

// function to get String in curent language
function s($index)
{
	global $g_lang;
	global $g_config;
	
	if(!isset($g_lang[$index]))
	{
		//file_put_contents(DIR_LANG.$g_config['app']['folder'].'.lang.'.$g_config['lang'].'.php', "\n".'$g_lang[\''.$index.'\'] = \''.$index.'\';',FILE_APPEND);
		return $index;
	}
	else
	{
		return $g_lang[$index];
	}
}

// get string in current lang with vars
function sv($index,$var)
{
	if(!is_array($var))
	{
		$var = array($var);
	}
	global $g_lang;
	global $g_config;
	
	if(!isset($g_lang[$index]))
	{
		$cnt = '';
		foreach ($var as $i => $v)
		{
			$cnt .= '{'.$i.'} ';
		}
		//file_put_contents(DIR_LANG.$g_config['app']['folder'].'.lang.'.$g_config['lang'].'.php', "\n".'$g_lang[\''.$index.'\'] = \''.$index.' '.$cnt.'\';',FILE_APPEND);
		return $index;
	}
	else
	{
		$search = array();
		$replace = array();
		foreach ($var as $name => $value)
		{
			$search[] = '{'.$name.'}';
			$replace[] = $value;
		}
		return str_replace($search, $replace, $g_lang[$index]);
	}
}

function debug($obj,$out = true)
{
	global $g_debug;
	
	$out = '';
	if(is_array($obj) || is_object($obj))
	{
		$g_debug[] = '<pre style="font-size:11px;font-family:Arial;color:yellow;">'.print_r($obj,true).'</pre><hr>';
	}
	else
	{
		$g_debug[] = '<pre style="font-size:11px;font-family:Arial;color:yellow;">'.$obj.'</pre><hr>';
	}
	if($out)
	{
		printDebugging();
	}
}

function getTemplate($tpl)
{
	if(file_exists(DIR_TEMPLATE.$tpl.'.php'))
	{
		include DIR_TEMPLATE.$tpl.'.php';
	}
	else
	{
		return $tpl.' template not found';
	}
}

function printDebugging()
{
	global $g_debug;
	if(!empty($g_debug))
	{
		echo '<div style="position:absolute;top:0px;left:0px;height:100%;width:100%;background-color:#000;overflow:auto;padding:30px;">'.implode("\n", $g_debug).'</div>';
		die();
	}
}
