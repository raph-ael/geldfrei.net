<?php 

if(isset($_GET['c']) && (int)$_GET['c'] > 0)
{
	$g_config['docs_per_page'] = (int)$_GET['c'];
}

/*
 * Add Js Libs
 */
addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');

addScript('/js/jquery.autosize.js',true);
addScript('/js/jquery.ui.widget.js',true);
addScript('/js/jquery.iframe-transport.js',true);
addScript('/js/jquery.fileupload.js',true);
addScript('/js/jquery.switch.js',true);
addScript('/js/jquery.lazyYT.js',true);
addScript('/js/bootstrap-growl.js',true);
addScript('/js/bootstrap.min.js',true);
addScript('/js/bootstrap.nod.js',true);
addScript('/js/jquery.nouislider.min.js',true);
addScript('/js/script.js',true);
addScript('/js/jquery.steps.js',true);
addScript('/js/ekko-lightbox.js',true);
addScript('/js/jquery.jvideo.js',true);
addScript('/js/textext/textext.core.js',true);
addScript('/js/textext/textext.plugin.tags.js',true);
addScript('/js/textext/textext.plugin.autocomplete.js',true);
addScript('/js/bootstrap-tagsinput.js',true);
addScript('/js/textext/textext.plugin.ajax.js',true);
addScript('/js/jquery.geocomplete.js',true);


/*
 * Add css libs
 */
addCss('/css/jquery.fileupload.css',true);
addCss('/css/jquery.switch.css',true);
addCss('/css/jquery.lazyYT.css',true);
addCSs('/css/jquery.nouislider.min.css',true);
addCSs('/css/bootstrap.css',true);
addCss('/css/bootstrap-theme.css',true);
addCss('/css/style.css',false);
addCss('/css/ekko-lightbox.css',true);
addCss('/css/bootstrap-tagsinput.css',true);
addCss('/css/textext/textext.core.css',true);
addCss('/css/textext/textext.plugin.tags.css',true);
addCss('/css/textext/textext.plugin.autocomplete.css',true);

/*
 * base routing
 */ 
$g_content = array();


$uri = explode('?', $_SERVER['REQUEST_URI']);
$uri = $uri[0];

$g_requestURI = explode('/', $uri);

$class = $g_requestURI[1];
$pages = array(
	'hilfe' => array('main','hilfe'),
	'ueber-uns' => array('main','ueberuns'),
	'impressum' => array('main','impressum'),
	'glossar' => array('glossar','index'),

	'kontakt' => array('main','kontakt'),
	'danke' => array('main','danke'),
	'profil' => array('anbieter','index'),
	'suche' => array('suche','index')
);


if(isset($pages[$class]))
{
	$method = $pages[$class][1];
	$class = $pages[$class][0];
}
else if(count($g_requestURI) == 2)
{
	$method = 'index';
}
else
{
	$method = $g_requestURI[2];
}

if(empty($class))
{
	$class = 'main';
}
if(empty($method))
{
	$method = 'index';
}

$folder = $class;
$class = ucfirst($class);

require_once DIR_LANG.LANG.'.php';
if(is_dir(DIR_APP.$folder))
{
	require_once DIR_APP.$folder.'/'.$folder.'.model.php';
	require_once DIR_APP.$folder.'/'.$folder.'.view.php';
	require_once DIR_APP.$folder.'/'.$folder.'.controller.php';
	include DIR_LANG.$folder.'.lang.'.LANG.'.php';
	addJsFunc(file_get_contents(DIR_APP.$folder.'/'.$folder.'.script.js'));
}
else
{
	T::go('/');
}

/*
 * boot the controller
 */
$class = $class.'Controller';

$g_config['app'] = array(
		'folder' => $folder,
		'class' => $class,
		'method' => $method
);

$app = new $class();
if(method_exists($app,$method))
{
	$g_content = $app->$method();
	
	/*
	 * get messages
	*/
	$msg = fSession::get('g_message',array());
	foreach ($msg as $type => $list)
	{
		foreach ($list as $l)
		{
			$t = '';
			if($l['title'] !== null)
			{
				$t = ",'".$l['title']."'";
			}
			addJs($type.'(\''.T::jsSafe($l['msg']).'\''.$t.');');
		}
	}
	fSession::set('g_message', array());
	
	include $app->getTemplate();
}
else
{
	T::go('/');
}
?>