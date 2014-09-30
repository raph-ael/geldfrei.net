<?php 
class SucheView extends View
{
	
	public function search($ret,$result = '')
	{
		$out = '
			<h1 class="line"><span></span>Anbietersuche<span></span></h1>
			<div class="whitebox">';
		
		$location = new vFormLocDistance('location',array(
			1 => '+ 1 Km',
			2 => '+ 2 Km',
			5 => '+ 5 Km',
			10 => '+ 10 Km',
			20 => '+ 20 Km',
			50 => '+ 50 Km'
		));
		
		addJsFunc('
			function fireSearch(latLng,distance)
			{				
				ajreq({
					app: "suche",
					action: "search",
					data:{
						l: latLng,
						d: distance
					},
					success: function(ret)
					{
						if(ret.status == 1)
						{
							$("#result").html(ret.html);
						}
					}
				});
			}
		');
		
		$location->onChange('
			fireSearch(latLng,distance);	
		');
		
		$out .= '
				<div class="row">
					<div class="col-md-12">
						<h3>'.$ret['title'].'</h3>
						'.$ret['content'].'
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon glyphicon glyphicon-question-sign"></span>
						    <input type="text" name="q" id="what" class="form-control" placeholder="Was suchst Du?">
						</div>
					</div>
					<div class="col-md-6">
						'.$location->render().'
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<hr>
						<div id="result">
							'.$result.'
						</div>
					</div>
				</div>
			</div>';
		
		return $out;
	}
	
	/**
	 * Display Results near to the user
	 */
	public function nearProfiles($profiles)
	{
		$out = '<h1 class="line"><span></span>Anbieter in Deiner Nähe<span></span></h1>';
		$out .= $this->anbieterIndex($profiles);
		
		return $out;
	}
	
	public function results($collections,$wrapper = true)
	{
		$out = '';
		
		foreach ($collections as $col => $results)
		{
			$tmp = '
				<h2>'.sv('results_in_'.$col,array('count'=>count($results))).'</h2>';
			foreach ($results as $r)
			{
				$img = '<span style="display:block;height:100px;width:100px;background-image:url(/css/img/carrot.png);background-position:center;"></span>';
				if(isset($r['image']) && !empty($r['image']))
				{
					$img = '<img src="'.$r['image'].'">';
				}
				$rate = '';
				if(isset($r['rating']))
				{
					$rate = $this->getRating($r,true);
				}
				
				$tmp .= '
				<a style="margin-right:15px;" class="thumbnail pull-left" href="'.$r['url'].'" class="thumbnail">'.$img.'</a>
				<h4 style="margin-bottom:8px;"><a href="'.$r['url'].'">'.$r['title'].'</a></h4>
				<p>'.T::tt($r['teaser'],300).'</p>
				'.$rate.'
				<div class="clearfix"></div>
				<hr>';
			}
			
			if($wrapper)
			{
				$tmp = '<div class="whitebox">'.$tmp.'</div>';
			}
			$out .= $tmp;
			
		}
		
		return $out;
	}
	
	private function shorten($str)
	{
		return substr($str,0,120).' ...';
	}
}
