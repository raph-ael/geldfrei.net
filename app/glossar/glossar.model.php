<?php 
class GlossarModel extends Model
{
	public function getModell($name)
	{
		if($mod = $this->getByUri('vertriebsmodell',$name))
		{
			return $mod;
		}
		else
		{
			return false;
		}
	}
	
	public function listModell()
	{
		return $this->q('vertriebsmodell',array(),array('name','teaser','images','videos','desc','icon','uri'));
	}
	
	public function getCerti($name)
	{
		if($mod = $this->getByUri('classification',$name))
		{
			return $mod;
		}
		else
		{
			return false;
		}
	}
	
	public function getProduct($name)
	{
		if($mod = $this->getByUri('product',$name))
		{
			return $mod;
		}
		else
		{
			return false;
		}
	}
	
	public function getClassification($name)
	{
		if($mod = $this->getByUri('classification',$name))
		{
			return $mod;
		}
		else
		{
			return false;
		}
	}
	
	public function getConsumeraction($name)
	{
		if($mod = $this->getByUri('consumeraction',$name))
		{
			return $mod;
		}
		else
		{
			return false;
		}
	}
}
