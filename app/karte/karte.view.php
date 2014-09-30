<?php 
class KarteView extends View
{
	public function map()
	{
		$map = new vMap();
		$map->setSearchPanel('map-searchpanel');
		$map->setMarkerCluster(true);
		
		$map->setHomeMarker();
		
		//$map->setProviderMapbox();
		
		return $map->render();
	}
	
	public function productList($products)
	{
		
		$out = '<ul class="map-list map-list-product list-unstyled">
		';
		foreach ($products as $p)
		{
			$out .= '
			<li class="checkbox">
			<label><input class="map-input" name="products[]" type="checkbox" value="'.$p['id'].'"><span>'.$p['name'].'</span><span class="clearfix"></span></label>
			</li>';
		}
		$out .= '<li class="clearfix"></li></ul>';
		return $out;
	}
	
	public function distance()
	{
		$dis =array(
			array('id' => 1, 'name' => 'vor meiner Haustür'),
			array('id' => 2, 'name' => 'in einem Umkreis von 2 KM'),
			array('id' => 10, 'name' => 'in einem Umkreis von 10 KM'),
			array('id' => 30, 'name' => 'mit dem öffentlichen Nahverkehr erreichbar'),
			array('id' => 0, 'name' => 'egal')
		);
		
		$out = '<ul class="map-list map-list-distance list-unstyled">
		';
		foreach ($dis as $p)
		{
			$out .= '
			<li class="radio">
			<label><input class="map-input" name="distance" type="radio" value="'.$p['id'].'"><span>'.$p['name'].'</span><span class="clearfix"></span></label>
			</li>';
		}
		$out .= '<li class="clearfix"></li></ul>';
		return $out;
	}
	
	public function availList()
	{
	
		$out = '
		<ul class="map-list map-list-avail list-unstyled">
			<li class="checkbox">
				<label><input class="map-input" name="avail[]" type="checkbox" value="daily"><span>'.s('daily').'</span><span class="clearfix"></span></label>
			</li>
			<li class="checkbox">
				<label><input class="map-input" name="avail[]" type="checkbox" value="weekly"><span>'.s('weekly').'</span><span class="clearfix"></span></label>
			</li>
			<li class="checkbox">
				<label><input class="map-input" name="avail[]" type="checkbox" value="now_open"><span>'.s('now_open').'</span><span class="clearfix"></span></label>
			</li>
		</ul>';
		return $out;
	}
	
	public function actionList($actions)
	{
	
		$out = '<ul class="map-list map-list-action list-unstyled">
		';
		foreach ($actions as $p)
		{
			$out .= '
			<li class="checkbox">
			<label><input class="map-input" name="actions[]" type="checkbox" value="'.$p['id'].'"><span>'.s($p['name'].'_map').'</span><span class="clearfix"></span></label>
			</li>';
		}
		$out .= '</ul>';
		return $out;
	}
}
