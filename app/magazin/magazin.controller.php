<?php 
class MagazinController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new MagazinView();
		$this->db = new MagazinModel();
		
		$this->addBread(s('magazin'), '/magazin');
	}
	
	public function videos()
	{
		$this->addBread(s('videos'), '/magazin/videos');
		return $this->index('video');
	}
	
	public function index($art = false)
	{		
		$out = '';
		/*
		if($featured = $this->db->getLastFeatured())
		{
			$out .= $this->view->featured($featured);
		}
		
		if($article = $this->db->listArticle(6))
		{
			
		}
		*/
		$tmp = '';
		if($articles = $this->db->listArticle($art))
		{
			
			foreach ($articles as $doc)
			{
				$doc['id'] = $this->getId($doc);
				if($doc['featured'] && $art === false)
				{
					$out .= $this->view->featured($doc);
				}
				else
				{
					$tmp .= $this->view->articleIndex($doc);
				}
			}
			if(!$art)
			{
				$out .= $this->view->sidebarRight($tmp, '');
				$out .= '<div style="width:597px;margin-left:10px;">'.$this->view->defaultMap().'</div>';
			}
			else
			{
				$out = $tmp;
			}
		}
		
		
		
		return $this->out(array(
			'main' => $out
		));
	}
	
	public function startpage()
	{
		$out = '';
		$tmp = '';
		if($articles = $this->db->listArticle())
		{
			$i=3;
			foreach ($articles as $doc)
			{
				$i--;
				$doc['id'] = $this->getId($doc);
				if($doc['featured'])
				{
					$out .= $this->view->featured($doc);
				}
				else
				{
					$tmp .= $this->view->articleIndex($doc);
				}
			}
			$out .= $this->view->sidebarRight($tmp, '');
			
			//$out = $tmp;
			/*
			
			if(!$art)
			{
				$out .= $this->view->sidebarRight($tmp, '');
			}
			else
			{
				
			}
			*/
			
			return $out;
		}
		
		return '';
	}
	
	public function manage()
	{
		if(S::may('editor'))
		{
			$this->addBread(s('management'), '/magazin/artikelliste');
			
			$magazins = $this->db->listMagazin();
			return $this->out(array(
					'main' => $this->view->listMagazin($magazins)
			));
		}
		else
		{
			go('/');
		}
	}
					
	public function add()
	{	
		$this->addBread(s('management'), '/magazin/artikelliste');
		$this->addBread(s('add'), '/magazin/add');
		
		if($this->isSubmitted() && ($values = $this->validateMagazin()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'title' => '',
				'title_short' => $values['title'],
				'teaser' => '',
				'videos' => array(),
				'images' => array(),
				'text' => '',
				'tags' => array(),
				'location_lat' => '',
				'location_lng' => '',
				'location_street' => '',
				'location_street_number' => '',
				'location_zip' => '',
				'location_city' => '',
				'featured' => false,
				'published' => false
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				info(s('magazin_add_success'));
				go('/magazin/edit/'.$id);
			}
		}
		
		return $this->out(array(
			'main' => $this->view->magazinForm()
		));
	}
					
	public function edit()
	{
		if($id = $this->uriMongoId(3))
		{
			$this->addBread(s('management'), '/magazin/artikelliste');
			$this->addBread(s('edit'), '/magazin/edit/'.$id);
			
			if($this->isSubmitted() && ($values = $this->validateMagazin()))
			{
				
				$this->db->clearField('magazin',$id,'videos','code');
				$this->db->clearField('magazin',$id,'images','file');
				
				if($this->db->updateMagazin($id,$values))
				{
					$this->info(s('magazin_edit_success'));
				}
			}
			
			if($magazin = $this->db->getMagazin($id))
			{
				return $this->out(array(
					'main' => $this->view->magazinForm($magazin)
				));
			}
			else
			{
				go('/magazin');
			}
		}
	}

	public function artikel()
	{
		if($uri = $this->uriStr(3))
		{
			if($magazin = $this->db->getMagazinByUri($uri))
			{
				$this->addBread($magazin['title'], '/magazin/artikel/'.$uri);
				
				$modelle = $this->db->listAll('vertriebsmodell',array('uri','name','teaser','icon'));
				
				return $this->out(array(
						'main' => $this->view->article($magazin,$modelle)
				));
			}
		}
		else
		{
			$this->addBread(s('article'), '/magazin/artikel');
			return $this->index('artikel');
		}
		go('/magazin');
	}
	
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteMagazin($id);
			}
		}
		go('/magazin');
	}
					
	public function validateMagazin()
	{
		$check = true;
		$data = array();
		
					
		/*
		 * validate title
		 */
		if($value = $this->getPostString('title'))
		{
			$data['title'] = $value;
		}
		else
		{
			$check = false;	
		}
		if($value = $this->getPostString('title_short'))
		{
			$data['title_short'] = $value;
		}
					
		/*
		 * validate teaser
		 */
		if($value = $this->getPostString('teaser'))
		{
			$data['teaser'] = $value;
		}
		
		/*
		 * validate teaser
		*/
		if($value = $this->getPostString('miniteaser'))
		{
			$data['miniteaser'] = $value;
		}
					
		/*
		 * validate videos
		 */
		if($value = $this->getPostVideos('videos'))
		{
			$data['videos'] = $value;
		}
					
		/*
		 * validate images
		 */
		if($images = $this->getPostImages('images'))
		{
			$data['images'] = array();
			foreach($images as $i => $image)
			{
					
				if(!$image['exists'] && file_exists(DIR_UPLOAD.$image['file']))
				{
					$image_name = $this->imageUpload(DIR_UPLOAD.$image['file'],DIR_IMG.'magazin/images/');

					$data['images'][] = array
					(
						'file' => $image_name,
						'folder' => DIR_IMG.'magazin/images/',
						'time' => new MongoDate(),
						'name' => '',
						'desc' => ''
					);
				}
				else if($image['exists'])
				{
					$data['images'][] = $image;
				}
			}
		}
					
		/*
		 * validate text
		 */
		if($value = $this->getPostHtml('text'))
		{
			$data['text'] = $value;
		}		
				
					
		/*
		 * validate tags
		 */
		if($value = $this->getPostTags('tags'))
		{
			$data['tags'] = $value;
		}
					
		/*
		 * validate location
		 */

		if($value = $this->getPostLatLng('location_lat','location_lng'))
		{
			$data['location_coords'] = $value;
		}
		if($value = $this->getPostString('location_street'))
		{
			$data['location_street'] = $value;
		}
		if($value = $this->getPostString('location_street_number'))
		{
			$data['location_street_number'] = $value;
		}
		if($value = $this->getPostZip('location_zip'))
		{
			$data['location_zip'] = $value;
		}
		if($value = $this->getPostString('location_city'))
		{
			$data['location_city'] = $value;
		}
		
					
		/*
		 * validate featured
		 */
		if($this->getPost('featured'))
		{
			$data['featured'] = true;
		}
		else
		{
			$data['featured'] = false;
		}
		
		
		/*
		 * validate published
		*/
		if($this->getPost('published'))
		{
			$data['published'] = true;
		}
		else
		{
			$data['published'] = false;
		}
		
		
		if($check)
		{
			return $data;
		}
		return false;
	}
	
	public function go()
	{
		if($id = $this->uriMongoId(3))
		{
			//if($anbieter = $this->db->findOne('anbieter', array('$id' => new MongoId($id))))
			if($uri = $this->db->qOne('magazin', array('_id' => new MongoId($id)), 'uri'))
			{
				go('/magazin/artikel/' . $uri);
			}
		}
	
		go('/');
	}
}
