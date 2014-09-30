<?php
class View
{
	public function breadcrumps($breadcrumps)
	{
		$out = '';
		
		if(!empty($breadcrumps))
		{
			$end = end($breadcrumps);
			$out = '<ol class="breadcrumb">';
			for($i=0;$i<(count($breadcrumps)-1);$i++)
			{
				$out .= '<li><a href="'.$breadcrumps[$i]['url'].'">'.$breadcrumps[$i]['name'].'</a></li>';
			}
			$out .= '<li>'.$end['name'].'</li>';
			$out .= '</ol>';
		}
		
		return $out;
	}
	
	public function getRating($doc,$counter = false)
	{
		if(isset($doc['rating']))
		{
			$count = '';
			if($counter)
			{
				$en = '';
				if($doc['rating_count'] > 1)
				{
					$en = 'en';
				}
				$count = $doc['rating_count'].' Bewertung'.$en;
			}
			$doc['rating'] = round($doc['rating']);
			return '<span class="g_rating"><span class="rating" style="width:'.($doc['rating']*20).'px"></span><span class="rating hold" style="width:'.((5-$doc['rating'])*20).'px"></span><span class="counter">'.$count.'</span></span>';
		}
		return '';
	}
	
	public function contentPanel($content)
	{
		return '
		<div class="whitebox">
			<h1>'.$content['title'].'</h1>
			'.$content['content'].'
		</div>';
	}
	
	public function anbieterIndex($anbieter,$count = 3)
	{
		$out = '
		<div class="row">';
		
		$col = 12 / $count;
		$i=0;
		foreach ($anbieter as $a)
		{
			
			$img = '/css/img/carrot.png';
			if(isset($a['images']) && !empty($a['images']))
			{
				$img = '/'.$a['images'][0]['folder'].'364x254-'.$a['images'][0]['file'];
			}
			
			$rating = '<span class="g_rating"><span class="rating" style="width:10px;background-image:none !important;"></span></span>';
			
			$r = $this->getRating($a);
			
			if(!empty($r))
			{
				$rating = $r;
			}
			
			$i++;
			$out .= '
			<div class="anbieter col-md-'.$col.'">
				<a class="thumbnail" href="/profil/'.$a['uri'].'">
					<img class="img-responsive" src="'.$img.'">
					'.$rating.'
				</a>
				
				<div class="text">
					<h3 class="green"><a href="/profil/'.$a['uri'].'">'.$a['name'].'</a></h3>
					<p>'.T::tt($a['teaser']).'</p>
				</div>
							
			</div>';
			
			if($i == $count)
			{
				break;
			}
		}
		
		$out .= '
		</div>';
		
		return $out;
	}
	
	public function modelButtons($models, $count = 3)
	{
		shuffle($models);
		$out = '';
		
		foreach ($models as $m)
		{
			$count--;
			$out .= '
			<a class="shadow1 modelButton" href="/glossar/vertriebsmodell/'.$m['uri'].'" class="corner-all btn btn-default">
				<img src="/'.DIR_FILES.$m['icon'].'"> 
				<h4>'.$m['name'].'</h4>
				'.$m['teaser'].'
				<span class="clearfix"></span>
			</a>';
			if($count == 0)
			{
				break;
			}
		}
		$out .= '';
		return $out;
	}
	
	public function tasteomapButton()
	{
		return '
		<div class="greenbox">
			<table width="100%">
				<tr>
					<td width="30%"><a style="display:block;width:40px;height:40px;-webkit-box-shadow: 0 0 8px #6F761F; -moz-box-shadow: 0 0 8px #6F761F; box-shadow: 0 0 8px #6F761F;" class="img-circle greengradient" href="/tasteomat"></a></td>
					<td>
					<h3><a href="/tasteomat">Taste-O-Mat</a></h3>
					<p>
						Machen Sie den Test. Was ist ihnen beim Einkauf besonders wichtig?
					</p>
				</td>
				</tr>
			</table>
		</div>';
	}
	
	public function defaultMap()
	{
		$map = new vMap();
		$map->setHomeMarker();
		$map->setSearchPanel('map-searchpanel');
		$out = '
		<div class="greenbox">
			<h3>Karte</h3>
			
			'.$map->render().'
			<div class="form-group">
	        	<div class="right-inner-addon ">
    			<i style="color:#A5AF28;" class="glyphicon glyphicon-search"></i><input id="map-searchpanel" type="text" class="form-control" placeholder="'.s('search').'">
	          	</div>
	        </div>
		</div>';
	
		return $out;
	}
	
	public function gotoButtons()
	{
		return '<a href="/glossar/vertriebsmodell" class="corner-all btn btn-default" style="margin-bottom:15px;display:block;float:right;width:180px"><h4 style="text-align:left;float:left;">Alle Modelle</h4> <span style="text-align:right;float:right;"><span class="glyphicon glyphicon-chevron-down"></span></span></a>
				<br>
				<a href="/suche" class="corner-all btn btn-default" style="display:block;margin-bottom:15px;float:right;width:180px"><h4 style="text-align:left;float:left;">Anbietersuche</h4> <span style="text-align:right;float:right;"><span class="glyphicon glyphicon-chevron-down"></span></span></a>';
	}
	
	public function col3($col1,$col2,$col3)
	{
		return '';
	}
	public function sidebarRight($content,$sidebar,$cnt_width = 8)
	{
		return '
			<div class="row">
				<div class="col-md-'.$cnt_width.'">
					'.$content.'
				</div>
				<div class="col-md-'.(12-$cnt_width).'">
					'.$sidebar.'
				</div>
			</div>';
	}
	
	public function whitebox($content)
	{
		return '<div class="whitebox">'.$content.'</div>';
	}
	
	public function greenbox($content)
	{
		return '<div class="greenbox">'.$content.'</div>';
	}
	
	public function layout1($left,$right)
	{
		return '
			<div class="row">
				<div class="col-md-9">'.$left.'</div>
				<div class="col-md-3">'.$right.'</div>
			</div>';
	}
	
	public function getPagination($count)
	{
		global $g_config;
		
		$page = 1;
		if(isset($_GET['p']) && (int)$_GET['p'] > 0)
		{
			$page = (int)$_GET['p'];
		}
		
		$list = '';
		if(isset($_GET['o']))
		{
			$list .= '&o='.preg_replace('/[^a-z_]/','',$_GET['o']);
			if(isset($_GET['l']))
			{
				$list .= '&l='.(int)$_GET['l'];
			}
		}
		
		
		$out = '';
		
		$count = getListingCount();
		
		if($count > $g_config['docs_per_page'])
		{
			$out = '
			<ul class="pagination">';
			
			if($page == 1)
			{
				$out .= '
				<li class="disabled"><span>&laquo;</span></li>';
			}
			else
			{
				$out .= '
				<li><a href="'.T::getSelf().'?p='.($page-1).'&c='.(int)$g_config['docs_per_page'].$list.'">&laquo;</a></li>';
			}
			
			$count_pages = ceil($count / $g_config['docs_per_page']);
			
			for($i=1;$i<=$count_pages;$i++)
			{
				if($i==$page)
				{
					$out .= '
				<li class="active"><span>1</span></a></li>';
				}
				else
				{
					$out .= '
				<li><a href="'.T::getSelf().'?p='.$i.'&c='.(int)$g_config['docs_per_page'].$list.'">'.$i.'</a></li>';
				}
			}
			
			if($page == $count_pages)
			{
				$out .= '
				<li class="disabled"><span>&raquo;</span></li>';
			}
			else
			{
				$out .= '
				<li><a href="'.T::getSelf().'?p='.($page+1).'&c='.(int)$g_config['docs_per_page'].$list.'">&raquo;</a></li>';
			}
			
			$out .= '
			</ul>';
		}
		
		return $out;
		
		
		$out = '
  <li><a href="#">&laquo;</a></li>
  <li><a href="#">1</a></li>
  <li><a href="#">2</a></li>
  <li><a href="#">3</a></li>
  <li><a href="#">4</a></li>
  <li><a href="#">5</a></li>
  <li><a href="#">&raquo;</a></li>
</ul>
	';
		
	}
	
	public function linkWrapper($type,$id)
	{
		switch($type)
		{
			case 'anbieter' : return '<a href="/anbieter/go/' . $id . '">Zum Anbieter</a>'; break;
			case 'magazin' : return '<a href="/magazin/go/' . $id . '">Zum Artikel</a>'; break;
			default : return '#'.$type . ':' . $id; break;
		}
	} 
}