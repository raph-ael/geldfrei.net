<?php 
class ScaffoldController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		if(!S::may('admin'))
		{
			go('/');
		}
		parent::__construct();
		
		$this->view = new ScaffoldView();
		$this->db = new ScaffoldModel();
		
		$this->addBread(s('start'), '/');
	}
	
	public function compress()
	{
		$this->compressScripts();
		$this->compressStyles();
	}
	
	private function compressStyles()
	{
		global $g_css_files;
		
		file_put_contents('css/style.min.css','');
		exec('rm '.DIR_ROOT.'tmp/yui/css/*');
		
		foreach ($g_css_files as $src => $compress)
		{
			if($compress)
			{
				$file = explode('/',$src);
				$file = end($file);
				
				exec('/usr/bin/java -jar '.DIR_ROOT.'jar/yuicompressor-2.4.7.jar '.DIR_ROOT.'public'.$src.' -o '.DIR_ROOT.'tmp/yui/css/'.$file);
				
				file_put_contents('css/style.min.css',"\n\n".'/* '.$file.' */'."\n".file_get_contents(DIR_ROOT . 'tmp/yui/css/'.$file),FILE_APPEND);
				
			}
		}
	}
	
	private function compressScripts()
	{
		global $g_script;
		
		exec('rm '.DIR_ROOT.'tmp/yui/js/*');
		file_put_contents('js/script.min.js','');
		
		foreach ($g_script as $src => $compress)
		{
			if($compress)
			{
				$file = explode('/',$src);
				$file = end($file);
				
				exec('/usr/bin/java -jar '.DIR_ROOT.'jar/yuicompressor-2.4.7.jar '.DIR_ROOT.'public'.$src.' -o '.DIR_ROOT.'tmp/yui/js/'.$file.' 2>&1', $output, $return);  
				
				file_put_contents('js/script.min.js',"\n\n".'/* '.$file.' */'."\n".file_get_contents(DIR_ROOT . 'tmp/yui/js/'.$file),FILE_APPEND);
				//file_put_contents('js/script.min.js',"\n\n".'/* '.$file.' */'."\n".file_get_contents('/var/www/TOH/public/js/'.$file),FILE_APPEND);
			}
		}
		
		// java -jar /path/to/yuicompressor-2.4.2.jar main.css -o main.min.css
		
	}
	
	public function index()
	{
		if($this->isSubmitted())
		{
			$scheme = array(

				'name' => array('type' => 'text'),
				'desc' => array('type' => 'textarea'),
				'file' => array('type' => 'file')
			
				/*
				'desc' => array('type' => 'tinymce'),
				'videos' => array('type' => 'video'),
				'images' => array('type' => 'image'),
				'users' => array(
						'type' => 'colref',
						'collection' => 'product',
						'element' => 'checkbox', 
						'display_field' => 'name'
				)
				*/
			);
			
			if($name = $this->getPostRegEx('name'))
			{
				$mod_dir = DIR_APP.$name;
				if(!is_dir($mod_dir))
				{
					$name = strtolower($name);
					mkdir($mod_dir);
					
					file_put_contents($mod_dir.'/'.$name.'.model.php', $this->genModel($name,$scheme));
					file_put_contents($mod_dir.'/'.$name.'.view.php', $this->genView($name,$scheme));
					file_put_contents($mod_dir.'/'.$name.'.controller.php', $this->genController($name,$scheme));
					file_put_contents($mod_dir.'/'.$name.'.xhr.php', $this->genXhr($name,$scheme));
					file_put_contents($mod_dir.'/'.$name.'.script.js', '');
					file_put_contents($mod_dir.'/'.$name.'.style.css', '');
					file_put_contents(DIR_LANG.$name.'.lang.de.php', '');
					success('Modul wurde generiert');
				}
				else
				{
					$this->error('Das Modul existiert schon');
				}
			}
		}
		
		return $this->out(array(
			'main' => $this->view->scaffoldForm(),
			'sidebar_left' => $this->view->menu()
		));
	}
	
	private function genModel($name,$scheme)
	{
		$name = strtolower($name);
		$class = ucfirst($name);
		
		$beforeupdate = '';
		$after_update = '';
		$after_add = '';
		$hastime = false;
		$add_methods = '';
		
		$values = array();
		foreach ($scheme as $field => $attr)
		{
			if($attr['type'] == 'location')
			{
				$val = '$'.$name.'[\''.$field.'_coords\']';
				$values[] = '\''.$field.'_coords\' => '.$val;
				
				$val = '$'.$name.'[\''.$field.'_zip\']';
				$values[] = '\''.$field.'_zip\' => '.$val;
				
				$val = '$'.$name.'[\''.$field.'_city\']';
				$values[] = '\''.$field.'_city\' => '.$val;
				
				$val = '$'.$name.'[\''.$field.'_street\']';
				$values[] = '\''.$field.'_street\' => '.$val;
				
				$val = '$'.$name.'[\''.$field.'_street_number\']';
				$values[] = '\''.$field.'_street_number\' => '.$val;
			}
			else if ($attr['type'] == 'time')
			{
				$hastime = true;
				// todo...
			}
			else if ($attr['type'] == 'video')
			{
				$beforeupdate .= '
		/*
		 * prepare '.$field.' 
		 */
		if(isset($data[\''.$field.'\']))
		{
			$old_'.$field.' = $this->get(\''.$name.'\', $id,array(\''.$field.'\'));
					
			if(isset($old_'.$field.'[\''.$field.'\']) && is_array($old_'.$field.'[\''.$field.'\']))
			{
				$data[\''.$field.'\'] = array_merge($old_'.$field.'[\''.$field.'\'], $data[\''.$field.'\'] );
			}
		}';
				$val = '$'.$name.'[\''.$field.'\']';
				$values[] = '\''.$field.'\' => '.$val;
			}
			else if ($attr['type'] == 'file')
			{
				$val = '$'.$name.'[\''.$field.'\']';
				$values[] = '\''.$field.'\' => '.$val;
			}
			else if ($attr['type'] == 'image') 
			{
				$beforeupdate .= '
		/*
		 * prepare '.$field.'	
		 */
		if(isset($data[\''.$field.'\']))
		{
			$old_'.$field.' = $this->get(\''.$name.'\', $id,array(\''.$field.'\'));
					
			if(isset($old_'.$field.'[\''.$field.'\']) && is_array($old_'.$field.'[\''.$field.'\']))
			{
				$data[\''.$field.'\'] = array_merge($old_'.$field.'[\''.$field.'\'], $data[\''.$field.'\'] );
			}
		}
				';
				
				$val = '$'.$name.'[\''.$field.'\']';
				$values[] = '\''.$field.'\' => '.$val;
			}
			else if($attr['type'] == 'colref')
			{
				$val = '$'.$name.'[\''.$field.'\']';
				$values[] = '\''.$field.'\' => '.$val;
			
				$add_methods .= '
	public function list'.ucfirst($field).'()
	{
		return $this->refList(\''.$attr['collection'].'\',\''.$attr['display_field'].'\');
	}';
			}
			else if($attr['type'] == 'tags')
			{
				$val = '$'.$name.'[\''.$field.'\']';
				$values[] = '\''.$field.'\' => '.$val;
				
				$after_update .= '
			$this->addGlobalTags('.$val.',\''.$name.'\');';
				$after_add .= '
			$this->addGlobalTags('.$val.',\''.$name.'\');';
			}
			else
			{
				$val = '$'.$name.'[\''.$field.'\']';
				$values[] = '\''.$field.'\' => '.$val;
			}
			
		}
		if(!$hastime)
		{
			$values[] = '\'time\' => new MongoDate()';
		}
		return 
'<?php 
class '.$class.'Model extends Model
{
	'.$add_methods.'
	public function list'.$class.'()
	{
		if($'.$name.'s = $this->pageList(\''.$name.'\',array(),array(\'name\')))
		{
			return $'.$name.'s;
			
		}
		return false;
	}
					
	public function get'.$class.'($id)
	{
		if($doc = $this->get(\''.$name.'\',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($'.$name.')
	{
		if($this->insert(\''.$name.'\', array(
			'.implode(",\n\t\t\t", $values).'
		)))
		{
			'.$after_add.'
			return true;
		}
		return false;
	}
					
	public function update'.$class.'($id, $data)
	{
		'.$beforeupdate.'
		if($this->update(\''.$name.'\',$id, $data))
		{
			'.$after_update.'
			return true;
		}
		return false;
	}
					
	public function delete'.$class.'($id)
	{
		return $this->delete(\''.$name.'\',$id);
	}
}
';
	}
	private function genView($name,$scheme)
	{
		$name = strtolower($name);
		$class = ucfirst($name);
		$view_params = '';
		$list = array();
		$form = array();
		$defaultval = array();
		$formelvars = array();
		foreach ($scheme as $field => $attr)
		{			
			if(!isset($attr['type']) || $attr['type'] == 'text')
			{
				$formelements[] = $this->genVText($field,$attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'textarea')
			{
				$formelements[] = $this->genVTextarea($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'wysiwyg')
			{
				$formelements[] = $this->genVWysiwyg($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'tinymce')
			{
				$formelements[] = $this->genVTinymce($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'tags')
			{
				$formelements[] = $this->genTags($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'switch')
			{
				$formelements[] = $this->genSwitch($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'image')
			{
				$formelements[] = $this->genVImage($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'colref')
			{
				$formelements[] = $this->genVColRef($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
				$view_params .= '$'.$field.'s, ';
			}
			elseif($attr['type'] == 'list')
			{
				$formelements[] = $this->genVListList($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'video')
			{
				$formelements[] = $this->genVVideo($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'file')
			{
				$formelements[] = $this->genVFile($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
			elseif($attr['type'] == 'location')
			{
				$formelements[] = $this->genVLocation($field, $attr);
				$formelvars[] = '$'.$field;
				$defaultval[] = "'".$field."' => ''";
			}
		}
		
		return 
'<?php 
class '.$class.'View extends View
{
	
	public function index($'.$name.'s)
	{
		return \'<pre>\'.print_r($'.$name.'s,true).\'</pre>\';
	}
		
	public function list'.$class.'($'.$name.'s)
	{
		'.$this->genVList($name,$scheme).'
	}
				
	public function '.$name.'Form('.$view_params.'$values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'.implode(",\n\t\t\t", $defaultval).'
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		'.implode("\n\t\t", $formelements).'

		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			'.implode(",\n\t\t\t", $formelvars).'
		),array(\'id\' => \''.$name.'\'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s(\'new_'.$name.'\'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
';
	}
	
	private function genVList($name,$scheme)
	{
		$out = '$table = new vTable(\'name\',\'time\');
		$table->setHeadRow(array(
			s(\'name\'),
			s(\'options\'),
		));
	
		foreach($'.$name.'s as $'.$name.')
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				\'icon\' => \'pencil\',
				\'href\' => \'/'.$name.'/edit/\'.$'.$name.'[\'_id\'],
				\'title\' => s(\'edit\')
			));
			$toolbar->addButton(array(
				\'icon\' => \'trash\',
				\'href\' => \'/'.$name.'/delete/\'.$'.$name.'[\'_id\'],
				\'title\' => s(\'delete\')
			));
			
			$table->addRow(array(
				array(\'cnt\' => $'.$name.'[\'name\']),
				array(\'cnt\' => $toolbar->render())
			));
		}
		
		$table->setWidth(1,\'140\');
						
		$panel = new vPanel(s(\'' . $name . '\'));
		$panel->addElement($table);
		$panel->addButton(s(\'add_'.$name.'\'),\'/'.$name.'/add\',\'plus-sign\');
				
				
		return 	$panel->render().
				$this->getPagination($'.$name.'s->count());';
		return $out;
	}
	
	private function genVLocation($field,$attributes)
	{
		return '$'.$field.' = new vFormLocation(\''.$field.'\',$values);';
	}
	
	private function genVListList($field,$attributes)
	{
		foreach ($attributes['items'] as $item)
		{
			$vals[] = 'array(\'name\'=>s(\''.$item.'\'),\'id\'=>\''.$item.'\')';
		}
		
		return '$'.$field.' = new vFormCheckbox(\''.$field.'\',array('.implode(',', $vals).'),$values[\''.$field.'\']);';
	}
	
	private function genVTinymce($field,$attributes)
	{
		return '$'.$field.' = new vFormTinymce(\''.$field.'\',$values[\''.$field.'\']);';
	}
	private function genVWysiwyg($field,$attributes)
	{
		return '$'.$field.' = new vFormWysiwyg(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genVVideo($field,$attributes)
	{
		return '$'.$field.' = new vFormVideo(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genVImage($field,$attributes)
	{
		return '$'.$field.' = new vFormImage(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genTags($field,$attributes)
	{
		return '$'.$field.' = new vFormTags(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genSwitch($field,$attributes)
	{
		return '$'.$field.' = new vFormSwitch(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genVColRef($field,$attributes)
	{
		if($attributes['element'] == 'select')
		{
			
		}
		else
		{
			return '$'.$field.' = new vFormCheckbox(\''.$field.'\',$'.$field.'s,$values[\''.$field.'\']);';
		}
	}
	
	private function genVFile($field,$attributes)
	{
		return '$'.$field.' = new vFormFile(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genVText($field,$attributes)
	{
		return '$'.$field.' = new vFormText(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genVTextarea($field,$attributes)
	{
		return '$'.$field.' = new vFormTextarea(\''.$field.'\',$values[\''.$field.'\']);';
	}
	
	private function genController($name,$scheme)
	{
		$name = strtolower($name);
		$class = ucfirst($name);
		
		$index = array();
		$add = array();
		$delete = array();
		$edit = array();
		
		$default_values= array();
		$prepare = '';
		$afteradd = '';
		$beforeupdate = '';
		$before_output = '';
		$form_params_add = '';
		$form_params_edit = '';
		
		foreach ($scheme as $field => $attr)
		{
			$prepare .= '
					
		/*
		 * validate '.$field.'
		 */';
					
			if($attr['type'] == 'image')
			{
				$default_values[] = '\''.$field.'\' => array()';
				$beforeupdate .= '
				$this->db->clearField(\''.$name.'\',$id,\''.$field.'\',\'file\');
				';
				
				if(!is_dir(DIR_IMG.$name))
				{
					mkdir(DIR_IMG.$name);
					chmod(DIR_IMG.$name, 0777);
				}
				if(!is_dir(DIR_IMG.$name.'/'.$field))
				{
					mkdir(DIR_IMG.$name.'/'.$field);
					chmod(DIR_IMG.$name.'/'.$field, 0777);
				}
				
				$prepare .= '
		if($images = $this->getPostImages(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = array();
			foreach($images as $i => $image)
			{
					
				if(!$image[\'exists\'] && file_exists(DIR_UPLOAD.$image[\'file\']))
				{
					$image_name = $this->imageUpload(DIR_UPLOAD.$image[\'file\'],DIR_IMG.\''.$name.'/'.$field.'/\');

					$data[\''.$field.'\'][] = array
					(
						\'file\' => $image_name,
						\'folder\' => DIR_IMG.\''.$name.'/'.$field.'/\',
						\'time\' => new MongoDate(),
						\'name\' => \'\',
						\'desc\' => \'\'
					);
				}
				else if($image[\'exists\'])
				{
					$data[\''.$field.'\'][] = $image;
				}
			}
		}';
			}
			elseif ($attr['type'] == 'file')
			{
				$default_values[] = '\''.$field.'\' => \'\'';
				$prepare .= '
		if($value = $this->getPostFile(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $this->upload(\''.$field.'\',\''.$name.'\');
		}
				';
			}
			elseif ($attr['type'] == 'wysiwyg')
			{
				$default_values[] = '\''.$field.'\' => \'\'';
				$prepare .= '
		if($value = $this->getPostHtml(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}		
				';
			}
			elseif ($attr['type'] == 'tinymce')
			{
				$default_values[] = '\''.$field.'\' => \'\'';
				$prepare .= '
		if($value = $this->getPostHtml(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}
				';
			}
			elseif ($attr['type'] == 'location')
			{
				$default_values[] = '\''.$field.'_lat\' => \'\'';
				$default_values[] = '\''.$field.'_lng\' => \'\'';
				$default_values[] = '\''.$field.'_street\' => \'\'';
				$default_values[] = '\''.$field.'_street_number\' => \'\'';
				$default_values[] = '\''.$field.'_zip\' => \'\'';
				$default_values[] = '\''.$field.'_city\' => \'\'';
				
				$prepare .= '

		if($value = $this->getPostLatLng(\''.$field.'_lat\',\''.$field.'_lng\'))
		{
			$data[\''.$field.'_coords\'] = $value;
		}
		if($value = $this->getPostString(\''.$field.'_street\'))
		{
			$data[\''.$field.'_street\'] = $value;
		}
		if($value = $this->getPostString(\''.$field.'_street_number\'))
		{
			$data[\''.$field.'_street_number\'] = $value;
		}
		if($value = $this->getPostZip(\''.$field.'_zip\'))
		{
			$data[\''.$field.'_zip\'] = $value;
		}
		if($value = $this->getPostString(\''.$field.'_city\'))
		{
			$data[\''.$field.'_city\'] = $value;
		}
		';
			}
			elseif ($attr['type'] == 'tags')
			{
				$default_values[] = '\''.$field.'\' => array()';
				$prepare .= '
		if($value = $this->getPostTags(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}';
				
			}
			elseif ($attr['type'] == 'switch')
			{
				$default_values[] = '\''.$field.'\' => false';
				$prepare .= '
		if($this->getPost(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = true;
		}
		else
		{
			$data[\''.$field.'\'] = false;
		}';
				
			}
			elseif ($attr['type'] == 'video')
			{
				$default_values[] = '\''.$field.'\' => array()';
				$beforeupdate .= '
				$this->db->clearField(\''.$name.'\',$id,\''.$field.'\',\'code\');';
				$prepare .= '
		if($value = $this->getPostVideos(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}';
			
			}
			elseif ($attr['type'] == 'list')
			{
				$default_values[] = '\''.$field.'\' => array()';
				
				$prepare .= '
		if($value = $this->getPostArray(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}';
			}
			elseif ($attr['type'] == 'colref')
			{
				$default_values[] = '\''.$field.'\' => array()';
			
				$prepare .= '
		if($value = $this->getPostMongoIdArray(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}';
				$before_output .= '
				$'.$field.' = $this->db->list'.ucfirst($field).'();';
				$form_params_add .= ',$'.$field;
				$form_params_edit .= '$'.$field.', ';
			}
			else
			{
				$default_values[] = '\''.$field.'\' => \'\'';
				$prepare .= '
		if($value = $this->getPostString(\''.$field.'\'))
		{
			$data[\''.$field.'\'] = $value;
		}
		else
		{
			$check = false;	
		}';
			}
		}
		if(!empty($form_params_add))
		{
			$form_params_add = substr($form_params_add, 1);
		}
		return 
'<?php 
class '.$class.'Controller extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new '.$class.'View();
		$this->db = new '.$class.'Model();
		
		$this->addBread(s(\'start\'), \'/'.$name.'\');
	}
	
	public function index()
	{
		
		$'.$name.'s = $this->db->list'.$class.'();
		return $this->out(array(
			\'main\' => $this->view->index($'.$name.'s)
		));
	}
				
	public function manage()
	{
		
		$'.$name.'s = $this->db->list'.$class.'();
		return $this->out(array(
			\'main\' => $this->view->list'.$class.'($'.$name.'s)
		));
	}
					
	public function add()
	{		
		if($this->isSubmitted() && ($values = $this->validate'.$class.'()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'.implode(",\n\t\t\t\t", $default_values).'
			),$values);	
			
			$values[\'time\'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				'.$afteradd.'
				info(s(\''.$name.'_add_success\'));
				go(\'/'.$name.'/edit/\'.$id);
			}
		}
		'.$before_output.'
		return $this->out(array(
			\'main\' => $this->view->'.$name.'Form('.$form_params_add.')
		));
	}
					
	public function edit()
	{
		if($id = $this->uriMongoId(3))
		{
			if($this->isSubmitted() && ($values = $this->validate'.$class.'()))
			{
				'.$beforeupdate.'
				if($this->db->update'.$class.'($id,$values))
				{
					$this->info(s(\''.$name.'_edit_success\'));
				}
			}
			
			if($'.$name.' = $this->db->get'.$class.'($id))
			{
				'.$before_output.'
				return $this->out(array(
					\'main\' => $this->view->'.$name.'Form('.$form_params_edit.'$'.$name.')
				));
			}
			else
			{
				go(\'/'.$name.'\');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may(\'user\'))
			{
				$this->db->delete'.$class.'($id);
			}
		}
		go(\'/'.$name.'\');
	}
					
	public function validate'.$class.'()
	{
		$check = true;
		$data = array();
		'.$prepare.'
		
		if($check)
		{
			return $data;
		}
		return false;
	}
}
';
	}
	
	private function genXhr($name,$scheme)
	{
		$name = strtolower($name);
		$class = ucfirst($name);
		
		return 
'<?php
class '.$class.'Xhr extends Xhr
{
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->db = new '.$class.'Model();
	}
}
';
	}
	
	private function genHelper($name)
	{
		
	}
}