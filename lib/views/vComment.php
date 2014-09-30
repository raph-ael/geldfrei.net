<?php
class vComment extends vCore
{
	private $id;
	private $rating;
	private $colletion;
	private $mongo_id;
	
	public function __construct($collection,$mongo_id)
	{
		$this->id = $this->id('comments');
		$this->rating = false;
		
		$this->colletion = $collection;
		$this->mongo_id = $mongo_id;
	}
	
	public function setRating($from,$to)
	{
		$this->rating = array($from,$to);
	}
	
	public function render()
	{
		
		addJsFunc('
				
		function loadComment_'.$this->id.'()
		{
			ajreq({
				app: "comment",
				action: "load",
				data: {
					c: "'.$this->colletion.'",
					id: "'.$this->mongo_id.'"
				},
				success: function(ret)
				{
					if(ret.status == 1)
					{
						$("#'.$this->id.'-comments").html(ret.data);
					}
				}
			});	
		}	
		');
		addJs('loadComment_'.$this->id.'();$("#'.$this->id.'").autosize();');
		$out = '
			<div id="'.$this->id.'-comments">
				
			</div>';
		
		
		
		if(/*S::may()*/true)
		{
			addCss('/css/jquery.rating.css');
			addScript('/js/jquery.MetaData.js');
			addScript('/js/jquery.rating.js');
			
			$message = s('comment_add_success');
			
			if(!S::may())
			{
				$message = s('comment_add_confirm');
			}
			
			$out .= '
			<div class="comment-post-wrapper" id="'.$this->id.'-wrapper">	
				<input id="'.$this->id.'-rating" type="hidden" name="'.$this->id.'-rating" value="-1">';
			
			addJsFunc('
				function commentAdd_'.$this->id.'()
				{
					var data = {
						c: "'.$this->colletion.'",
						id: "'.$this->mongo_id.'",
						t: $("#'.$this->id.'").val(),
						u: $("#'.$this->id.'-name").val()
					};
								
					if(parseInt($("#'.$this->id.'-rating").val()) > -1)
					{
						data.r = parseInt($("#'.$this->id.'-rating").val());
					}
								
					ajreq({
						app: "comment",
						action: "add",
						data: data,
						success: function(ret){
							if(ret.data !== false)
							{
								$("#'.$this->id.'-wrapper").animate({
									height: "1px",
									opacity: 0
								},700,function(){
									$("#'.$this->id.'-wrapper").remove();
									loadComment_'.$this->id.'();
									success("'.T::jsSafe($message).'");
								});
							}
						}
					});
				}
			');
			
			addJs('
	
				$("#'.$this->id.'-submit").hide();
				$("#'.$this->id.'").focus(function(){
					$("#'.$this->id.'-submit").show();
				});
			');
			
			if($this->rating)
			{
				addJs('
					$("#'.$this->id.'-submit").click(function(){

						if($("#'.$this->id.'-name").length > 0 && $("#'.$this->id.'-name").val() == "")
						{
							alert("Bitte Deinen Namen eingeben!");
						}
						else if(parseInt($("#'.$this->id.'-rating").val()) <= 0)
						{
							alert("Bitte noch eine Bewertung angeben!");
						}
						else if($("#'.$this->id.'").val() == "")
						{
							alert("Bitte noch einen Kommentar eingeben!");
						}
						else
						{
							commentAdd_'.$this->id.'();
						}
					});
					$(".'.$this->id.'-star").rating({ 
						callback: function(value, link)
						{ 
							$("#'.$this->id.'-rating").val(value);
							$("#'.$this->id.'-submit").show();
						} 
					}); 	
				');
				$out .= '
					<div class="form-group" id="'.$this->id.'-rating-wrapper">
						<div class="clearfix"></div>';
				for($i=$this->rating[0];$i<=$this->rating[1];$i++)
				{
					$out .= '<input name="'.$this->id.'-star" value="'.$i.'" type="radio" class="'.$this->id.'-star">';
				}
				
				$out .= '
						<div class="clearfix"></div>
					</div>';
			}
			else
			{
				addJs('
					$("#'.$this->id.'-submit").click(function(){
						commentAdd_'.$this->id.'();
					});	
				');
			}
			
			$namefield = '';
			if(!S::may())
			{
				$namefield = '
					<div class="form-group">
						<input type="text" id="'.$this->id.'-name" name="'.$this->id.'-name" class="form-control" placeholder="Dein Name">
					</div>';
			}
			
			$out .= '
					'.$namefield.'
					<div class="form-group">
						<textarea maxlength="5000" id="'.$this->id.'" name="'.$this->id.'" class="form-control" rows="3" placeholder="'.s('add_comment').'"></textarea>
					</div>
					<div id="'.$this->id.'-submit" class="form-group">
						<button type="button" class="btn btn-primary">'.s('send_comment').'</button>
					</div>
				</div>';
		}
		return $out;
	}
}