$.extend({
  jVideo: function( selector, options ){
	 
	var _id = selector.replace("#","");
	  
	function _addVideo()
	{
		var count = $(".jvideo-thumb").length
		var url = $(selector+"-input").val();

		if(url === null){ return false; }

	    var vid;
	    var results;

	    results = url.match("[\\?&]v=([^&#]*)");

	    if(results == null)
	    {
	    	return false;
	    }
	    
	    vid = ( results === null ) ? url : results[1];
	    
	    vid = {
	    	thumb : "http://img.youtube.com/vi/"+vid+"/0.jpg",
	    	thumb_small : "http://img.youtube.com/vi/"+vid+"/2.jpg",
	    	code : vid
	    };
		
		
		$(selector+"-input").val("");
		if(url != "")
		{
			if(vid.thumb != undefined)
			{
				$(".c-"+vid.code).remove();
				$(selector+"-thumbs").append('<div class="jvideo-thumb c-' + vid.code + ' col-xs-6 col-md-3"><a class="thumbnail" href="#"><span style="display:block;height:140px;background-image:url(\''+vid.thumb+'\');background-size:cover;background-position:center;text-align:right;"><button data-code="c-' + vid.code + '" type="button" class="vid-remove btn glyphicon glyphicon-remove" style=""></button></span></a></div>');	
				$(selector+"-videos").append(
					'<input class="c-' + vid.code + '" type="hidden" name="' + _id + '[' + count + '][code]" value="' + vid.code + '">' +
					'<input class="c-' + vid.code + '" type="hidden" name="' + _id + '[' + count + '][url]" value="' + url + '">' +
					'<input class="c-' + vid.code + '" type="hidden" name="' + _id + '[' + count + '][thumb]" value="' + vid.thumb + '">' +
					'<input class="c-' + vid.code + '" type="hidden" name="' + _id + '[' + count + '][thumb_small]" value="' + vid.thumb_small + '">'
				);
				$(".vid-remove").click(function(){
					$("."+$(this).attr("data-code")).remove();
				});
			}
		}
	}

	
	$(selector+"-button").bind("click",function(){
		_addVideo();
	});
	
	$(selector+"-input").bind("blur",function(){
		_addVideo();
	});
	
	$(selector+"-input").bind("blur",function(){
		_addVideo();
	});
	
	$(selector+"-input").keypress(function(e) {
	    if(e.which == 13) {
	    	e.preventDefault();
	    	_addVideo();
	    }
	});

  }
});