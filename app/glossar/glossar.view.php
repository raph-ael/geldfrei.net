<?php 
class GlossarView extends View
{
	
	public function index($modelle)
	{
		$out = '';
		foreach ($modelle as $m)
		{
			$out .= $this->modellBox($m);
		}
		
		return $out;
	}
	
	public function modellBox($article)
	{
		$thumb = '';
		
		$icon = '';
		if(isset($article['icon']) && !empty($article['icon']))
		{
			$icon = '<img height="70" src="/files/'.$article['icon'].'" style="float:right;margin-left:-50px;position:relative;top:-30px;left:30px;">';
		}
		
		$article['desc'] = strip_tags($article['desc']);
	
		$teaser_lenth = 500;
		if(!empty($article['videos']))
		{
			$teaser_lenth = 400;
			$thumb = '
				<a class="thumbnail" href="/glossar/vertriebsmodell/'.$article['uri'].'">
					<span class="thumbimg" style="height:100px;background-image:url('.$article['videos'][0]['thumb'].');" alt="'.$article['title'].'">
				</a>
			';
		}
		elseif(!empty($article['images']))
		{
			$teaser_lenth = 400;
			$thumb = '
				<a class="thumbnail" href="/glossar/vertriebsmodell/'.$article['uri'].'">
					<span class="thumbimg" style="height:100px;background-image:url(/'.$article['images'][0]['folder'].$article['images'][0]['file'].');" alt="'.$article['title'].'">
				</a>
			';
		}
	
		$teaser_lenth = $teaser_lenth - strlen($article['name']);
	
		return '
		<div class="col-md-6">
			<div class="whitebox plus athumb">
				'.$icon.'
				<h3><a href="/glossar/vertriebsmodell/'.$article['uri'].'">'.$article['name'].'</a></h3>
				'.$thumb.'
				<h4><a href="/glossar/vertriebsmodell/'.$article['uri'].'">'.$article['teaser'].'</a></h4>
				<p>'.T::tt($article['desc'],$teaser_lenth).'</p>
			</div>
		</div>';
	}
	
	public function modell($modell)
	{
		$content = '
		<div class="whitebox">
			<img class="icon pull-right" src="/files/'.$modell['icon'].'">
			<p class="small">Vertriebsmodell</p>
			<h1>'.$modell['name'].'</h1>
			
			<strong class="green">
				'.$modell['teaser'].'		
			</strong>
			'.$modell['desc'].'
			<hr>
			<h2 class="green">Vorteile</h2>
			'.$modell['vorteile'].'
			<hr>
			<h2 class="green">Nachteile</h2>
			'.$modell['nachteile'].'
			<hr>
			<h2 class="green">Tips & Links</h2>
			'.$modell['tips'].'
		</div>';
		
		$map = new vMap();
		$map->setHomeMarker();
		$right = $this->defaultMap();
		$right .= $this->tasteomapButton();
		$right .= $this->gotoButtons();
		
		return '<h1 class="line"><span></span>Glossar<span></span></h1>'.$this->sidebarRight($content,$right);
	}
	
	public function product($modell)
	{
		$content = '
		<div class="whitebox">
			<p class="small">Produktkategorie</p>
			<h1>'.$modell['name'].'</h1>
			'.$modell['desc'].'
			<hr>
		</div>';
	
		$map = new vMap();
		$map->setHomeMarker();
		$right = $this->defaultMap();
		$right .= $this->tasteomapButton();
		$right .= $this->gotoButtons();
	
		return '<h1 class="line"><span></span>Glossar<span></span></h1>'.$this->sidebarRight($content,$right);
	}
	
	public function certi($modell)
	{
		$content = '
		<div class="whitebox">
			<p class="small">Zertifizierung</p>
			<h1>'.$modell['name'].'</h1>
		
			<strong class="green">
				'.$modell['teaser'].'
			</strong>
			'.$modell['desc'].'
		</div>';
	
		$map = new vMap();
		$map->setHomeMarker();
		$right = $this->defaultMap();
		$right .= $this->tasteomapButton();
		$right .= $this->gotoButtons();
	
		return '<h1 class="line"><span></span>Glossar<span></span></h1>'.$this->sidebarRight($content,$right);
	}
}
