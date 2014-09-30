<?php 
class ProductModel extends Model
{
	
	public function listProduct()
	{
		if($products = $this->pageList('product',array(),array('name')))
		{
			return $products;
			
		}
		return false;
	}
					
	public function getProduct($id)
	{
		if($doc = $this->get('product',$id))
		{
			return $doc;
		}
		return false;
	}
		
	public function add($product)
	{
		if($uriname = $this->freeUri('product', $product['name']))
		{
			if($this->insert('product', array(
				'name' => $product['name'],
				'uri' => $uriname,
				'desc' => $product['desc'],
				'videos' => $product['videos'],
				'images' => $product['images'],
				'time' => new MongoDate()
			)))
			{
				
				return true;
			}
		}
		return false;
	}
					
	public function updateProduct($id, $data)
	{
		
		/*
		 * prepare videos 
		 */
		if(isset($data['videos']))
		{
			$old_videos = $this->get('product', $id,array('videos'));
					
			if(isset($old_videos['videos']) && is_array($old_videos['videos']))
			{
				$data['videos'] = array_merge($old_videos['videos'], $data['videos'] );
			}
		}
		/*
		 * prepare images	
		 */
		if(isset($data['images']))
		{
			$old_images = $this->get('product', $id,array('images'));
					
			if(isset($old_images['images']) && is_array($old_images['images']))
			{
				$data['images'] = array_merge($old_images['images'], $data['images'] );
			}
		}
				
		if($this->update('product',$id, $data))
		{
			return true;
		}
		return false;
	}
					
	public function deleteProduct($id)
	{
		return $this->delete('product',$id);
	}
}
