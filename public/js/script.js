function ajreq(options)
{
	if(options.data == undefined)
	{
		options.data = {};
	}
	var opt = options;
	$.ajax({
		url: "/xhr.php?a=" + options.app + "&m=" + options.action,
		type: "post",
		data: options.data,
		dataType: "json",
		success: function(data)
		{
			if(opt.success != undefined)
			{
				opt.success(data);
			}
			if(data.script != undefined)
			{
				jQuery.globalEval( data.script );
			}
		}
	});
}
function error(msg,title)
{
	$.growl({ 
		  icon: 'glyphicon glyphicon-remove-sign', 
		  title: title, 
		  message: msg,
		  type: 'danger'
	}, { 
		  template: { 
		    title_divider: '<hr class="separator" />' 
		  } 
		});
}
function info(msg,title)
{
	$.growl({ 
		  icon: 'glyphicon glyphicon-info-sign', 
		  title: title, 
		  message: msg
	}, { 
		  template: { 
		    title_divider: '<hr class="separator" />' 
		  } 
		});
}
function success(msg,title)
{

	$.growl({ 
		  icon: 'glyphicon glyphicon-ok-sign', 
		  title: title, 
		  message: msg,
		  type: 'success'
	},{ 
		template: { 
		    title_divider: '<hr class="separator" />' 
		} 
	});
}
function warn(msg,title)
{
	$.growl({ 
		  icon: 'glyphicon glyphicon-warning-sign', 
		  title: title, 
		  message: msg,
		  type: 'warning'
	},{ 
		template: { 
		    title_divider: '<hr class="separator" />' 
		} 
	});
}
// collapse fieldset
$(function () {
	
	$('table .btn-toolbar .btn').each(function(){
		$btn = $(this);
		if($btn.attr('href').indexOf('/trash/') > 0 || $btn.attr('href').indexOf('/delete/') > 0)
		{
			$btn.click(function(ev){
				if(!confirm("Wirlich löschen?"))
				{
					ev.preventDefault();
				}
			});
		}
	});
	
    $('fieldset.collapsible > legend').append(' (<span style="font-family: monospace;">+</span>)');
    $('fieldset.collapsible > legend').click(function () {
        var $divs = $(this).siblings();
        $divs.toggle();

        $(this).find('span').text(function () {
            return ($divs.is(':visible')) ? '-' : '+';
        });
    });
});