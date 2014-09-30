$(function(){
	
	$('.mgallery a.big').click(function(ev){ev.preventDefault()});
	
	$('.mgallery').each(function(){
		
		var id = $(this).attr('id');
		var height = $("#" + id + " .small-wrapper").height();
		
		$("#" + id + " .small-wrapper").css({
			height: "97px"
		});
		
		$("#" + id + " a.small").click(function(ev){
			ev.preventDefault();
			
			var file = $(this).data("big");
			
			$("#" + id + " .big").css({
				"background-image":"url("+file+")"
			});
			
		});
		
		$("#" + id + " .foot a").click(function(ev){
			ev.preventDefault();
			
			
			$("#" + id + " .small-wrapper").animate({
				height: height
			},300,function(){
				$("#"+id+" .foot a").animate({
					opacity:0
				});
			});
			
			
		});
		
	});
});