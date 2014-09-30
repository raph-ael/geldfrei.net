function mHideLoader()
{
	$(".map-loader").css('visibility','hidden');
}
function mShowLoader()
{
	$(".map-loader").css('visibility','visible');
}

function loadProfile(id)
{
	ajreq({
		app: 'anbieter',
		action: 'loadprofile',
		data: {id:id}
	});
}

function setMarker(marker)
{
	map_clearCluster();
	$.each(marker,function(i,marker){
		map_addMarker({
			lat: marker.c[0],
			lng: marker.c[1],
			click: function(){
				loadProfile(marker.id);
			}
		});
	});

	map_commitCluster();
}
	
function loadMapInput()
{
	mShowLoader();
	ajreq({
		app: 'karte',
		action: 'mapinput',
		data: $('#map-data .map-input, #map-latLng').serialize(),
		success: function(ret){
			mHideLoader();
			setMarker(ret);		
		}
	});
}
$(document).ready(function(){
	mHideLoader();
	$("input.map-input").change(function(){
		loadMapInput();
	});
	
	$(document).on('click', '.yamm .dropdown-menu', function(e) {
		  e.stopPropagation()
	});

	$("#map-menu .dropdown").hover(function() {
        $(this).addClass('open');
    }, function() {
        $(this).removeClass('open');
    });
	
	$('.map-list-avail input').change(function(){
		var $this = $(this);
		$(".map-list-avail input[value!='"+$this.attr('value')+"']").attr('checked', false);
	});
	
	
	
	$("#map-go").unbind('click').click(function(ev){
		ev.preventDefault();
		loadMapInput();
	});	
	loadMapInput();
});
