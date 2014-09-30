<?php 
class MagazinView extends View
{
	public function listMagazin($magazins)
	{
		$table = new vTable();
		$table->setHeadRow(array(
			s('date'),
			s('title'),
			s('options'),
		));
	
		foreach($magazins as $magazin)
		{
			$toolbar = new vButtonToolbar();
			$toolbar->addButton(array(
				'icon' => 'pencil',
				'href' => '/magazin/edit/'.$magazin['_id'],
				'title' => s('edit')
			));
			$toolbar->addButton(array(
				'icon' => 'trash',
				'href' => '/magazin/delete/'.$magazin['_id'],
				'title' => s('delete')
			));
			
			$table->addRow(array(
				array('cnt' => T::date($magazin['time'])),
				array('cnt' => $magazin['title']),
				array('cnt' => $toolbar->render())
			));
		}
		
		$table->setWidth(2,'140');
		$table->setWidth(0,'100');
		
		$panel = new vPanel(s('magazin'));

		$panel->addElement($table);
		
		$panel->addButton(s('add_article'), '/magazin/add','plus-sign');
		
		return $panel->render().
		$this->getPagination(count($magazins));
	}
	
	public function articleIndex($article)
	{
		$thumb = '';
		
		$teaser_lenth = 240;
		if(!empty($article['videos']))
		{
			$teaser_lenth = 120;
			$thumb = '
				<a class="thumbnail" href="/magazin/artikel/'.$article['uri'].'">
					<span class="thumbimg" style="height:100px;background-image:url('.$article['videos'][0]['thumb'].');" alt="'.$article['title'].'">
				</a>
			';
		}
		if(!empty($article['images']))
		{
			$teaser_lenth = 120;
			$thumb = '
				<a class="thumbnail" href="/magazin/artikel/'.$article['uri'].'">
					<span class="thumbimg" style="height:100px;background-image:url(/'.$article['images'][0]['folder'].$article['images'][0]['file'].');" alt="'.$article['title'].'">
				</a>
			';
		}
		
		$teaser_lenth = $teaser_lenth - strlen($article['title_short'].$article['title']);
		
		return '
		<div class="col-md-6">
			<div class="whitebox plus athumb">
				<h3><a href="/magazin/artikel/'.$article['uri'].'">'.$article['title'].'</a></h3>
				'.$thumb.'
				<h4><a href="/magazin/artikel/'.$article['uri'].'">'.$article['title_short'].'</a></h4>
				<p>'.T::tt($article['teaser'],$teaser_lenth).'</p>
			</div>
		</div>';
	}
	
	public function featured($article)
	{

		$img = '';
	
		if(!empty($article['videos']))
		{
			$img = '
			<a href="/magazin/artikel/'.$article['uri'].'" class="thumbnail">
		      <span style="display:block;height:270px;background-image:url('.$article['videos'][0]['thumb'].');" alt="'.$article['title'].'">
		    </a>';
		}
		else if(!empty($article['images']))
		{
			$img = '
			<a href="/magazin/artikel/'.$article['uri'].'" class="thumbnail">
		      <span style="display:block;height:270px;background-image:url(/'.$article['images'][0]['folder'].'800x0-'.$article['images'][0]['file'].');" alt="'.$article['title'].'">
		    </a>';
		}
		
		return '
		<div class="col-md-12">
			<div class="whitebox plus">
			    <div class="row">
					<div class="col-md-8">
						'.$img.'
					</div>
					<div class="col-md-4 farticle">
						<p class="small">'.T::date($article['time']).'</p>
						<h2>'.$article['title'].'</h2>
						<strong class="mbottom">
							'.$article['teaser'].'
						</strong>
						<p>
							<a href="/magazin/artikel/'.$article['uri'].'" class="btn btn-default pull-right">'.s('go_to_desc').' &nbsp; <span class="glyphicon glyphicon-chevron-right"></span></a>		
						</p>
					</div>
		 		</div>
			</div>
		</div>	
		';
	}
	
	public function article($article,$modelle)
	{
		$videos = '';
		
		if(!empty($article['videos']))
		{
			addJs('$(".lazy-yt").lazyYT();');
			foreach ($article['videos'] as $v)
			{
				$videos .= '
				<div class="row">
					<div class="thumbnail">
				      	<div data-youtube-id="'.$v['code'].'" class="lazy-yt">
							<img style="width:100%;" src="'.$v['thumb'].'">
						</div>
				    </div>
				</div>';
			}
			
		}
		
		$gallery = new vGallery($article['images']);
		
		$comment = new vComment('magazin', $article['id']);
		
		return '
		<div class="row">
			
			<div class="col-md-8">
				<div class="whitebox">
					<div class="row">
						<small>'.T::date($article['time']).'</small>
						<h2>'.$article['title'].'</h2>
						<p><strong>
							'.$article['teaser'].'
						</strong></p>
					</div>
					'.$videos.'
					<div class="row">
					'.$article['text'].'
					</div>
					'.$gallery->render().'
							
					<hr>
							
					'.$comment->render().'
				</div>
			</div>

			<div class="col-md-4">
				'.$this->tasteomapButton().'
				'.$this->modelButtons($modelle).'
						
				<a style="margin-bottom:15px;display:block;float:right;width:180px" class="corner-all btn btn-default" href="/vertriebsmodell"><h4 style="text-align:left;float:left;">Alle Modelle</h4> <span style="text-align:right;float:right;"><span class="glyphicon glyphicon-chevron-down"></span></span></a><br>
				<a style="display:block;margin-bottom:15px;float:right;width:180px" class="corner-all btn btn-default" href="/suche"><h4 style="text-align:left;float:left;">Anbietersuche</h4> <span style="text-align:right;float:right;"><span class="glyphicon glyphicon-chevron-down"></span></span></a>
				<div class="clearfix"></div>
						
			</div>
		</div>
		
		';
	}
	
	public function magazinForm($values = array())
	{
		/*
		 * set default values
		 */
		$values = array_merge(array(
			'title' => '',
			'title_short' => '',
			'teaser' => '',
			'miniteaser' => '',
			'videos' => '',
			'images' => '',
			'text' => '',
			'tags' => '',
			'location' => '',
			'featured' => false,
			'published' => false
		),$values);
		
		
		/*
		 * set Form Elements
		 */		
		$title = new vFormText('title',$values['title']);
		$teaser = new vFormTextarea('teaser',$values['teaser']);
		$miniteaser = new vFormTextarea('miniteaser',$values['miniteaser']);
		$videos = new vFormVideo('videos',$values['videos']);
		$images = new vFormImage('images',$values['images']);
		$text = new vFormTinymce('text',$values['text']);
		$tags = new vFormTags('tags',$values['tags']);
		$location = new vFormLocation('location',$values);
		$featured = new vFormSwitch('featured',$values['featured']);
		$published = new vFormSwitch('published',$values['published']);
		
		/*
		 * add elemnts to new Form
		 */	
		$form = new vForm(array(
			$title,
			new vFormText('title_short',$values['title_short']),
			$teaser,
			$miniteaser,
			$videos,
			$images,
			$text,
			$tags,
			$location,
			$featured,
			$published
		),array('id' => 'magazin'));
				
		/*
		 *	Add everything to panel	
		 */
		$panel = new vPanel(s('new_magazin'));
		$panel->addElement($form);
		
		return $panel->render();
	}
}
