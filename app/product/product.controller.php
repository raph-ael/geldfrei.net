<?php 
class ProductController extends Controller
{	
	public $view;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new ProductView();
		$this->db = new ProductModel();
		
		$this->addBread(s('start'), '/product');
	}
	
	public function index()
	{
		
		return $this->manage();
	}
	
	public function manage()
	{
	
		$products = $this->db->listProduct();
		return $this->out(array(
				'main' => $this->view->listProduct($products)
		));
	}
					
	public function add()
	{		
		if($this->isSubmitted() && ($values = $this->validateProduct()))
		{
			/*
			 * default values
			 */
			$values = array_merge(array(
				'name' => '',
				'desc' => '',
				'videos' => array(),
				'images' => array()
			),$values);	
			
			$values['time'] = new mongoDate();
			if($id = $this->db->add($values))
			{
				
				info(s('product_add_success'));
				go('/product/edit/'.$id);
			}
		}
		
		return $this->out(array(
			'main' => $this->view->productForm()
		));
	}
					
	public function edit()
	{
		if($id = $this->uriMongoId(3))
		{
			if($this->isSubmitted() && ($values = $this->validateProduct()))
			{
				
				$this->db->clearField('product',$id,'videos','code');
				$this->db->clearField('product',$id,'images','file');
				
				if($this->db->updateProduct($id,$values))
				{
					$this->info(s('product_edit_success'));
				}
			}
			
			if($product = $this->db->getProduct($id))
			{
				
				return $this->out(array(
					'main' => $this->view->productForm($product)
				));
			}
			else
			{
				go('/product');
			}
		}
	}
						
	public function delete()
	{
		if($id = $this->uriMongoId(3))
		{
			if(S::may('user'))
			{
				$this->db->deleteProduct($id);
			}
		}
		go('/product');
	}
					
	public function validateProduct()
	{
		$check = true;
		$data = array();
		
					
		/*
		 * validate name
		 */
		if($value = $this->getPostString('name'))
		{
			$data['name'] = $value;
		}
		else
		{
			$check = false;	
		}
					
		/*
		 * validate desc
		 */
		if($value = $this->getPostHtml('desc'))
		{
			$data['desc'] = $value;
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
					$image_name = $this->imageUpload(DIR_UPLOAD.$image['file'],DIR_IMG.'product/images/');

					$data['images'][] = array
					(
						'file' => $image_name,
						'folder' => DIR_IMG.'product/images/',
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
		
		if($check)
		{
			return $data;
		}
		return false;
	}
}
