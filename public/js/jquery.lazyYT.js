/*! LazyYT (lazy load Youtube videos plugin) - v0.3.0 - 2014-03-07
* Usage: <div class="lazyYT" data-youtube-id="laknj093n" data-width="300" data-height="200" data-parameters="rel=0">loading...</div>
* Copyright (c) 2014 Tyler Pearson; Licensed MIT */


;(function ($) {
    'use strict';

    function setUp(el) {
    	var $el = el;
    	$el.children('img').load(function(){
    		 
             var width = parseInt($el.children('img').width()),
             height = parseInt((width/16)*9),
             
             id = $el.data('youtube-id'),
             youtubeParameters = $el.data('parameters') || '';
         
         $(window).resize(function(){
         	width = $el.width();
         	height = parseInt((width/16)*9);
         	$el.css({
         		'height': height+'px'
         	});
         	$el.children('span:first').css({
         		'top': ( $el.height()-parseInt(( $el.children('span:first').height()*2 )-3) )+'px'
             });
         });
         
         $el.css({
             'position': 'relative',
             'width': '100%',
             'height' : parseInt(($el.height()/16)*9)+'px',
             'text-align':'right',
             'cursor':'pointer',
             '-webkit-background-size': 'cover',
             '-moz-background-size': 'cover',
             '-o-background-size': 'cover',
             'background-size': 'cover',
             'background-image': 'url('+$el.children('img:first').attr('src')+')',
             'background-position':'center',
             'padding':'0'
         }).removeClass('thumbnail');
         
         $el.children('img').remove();
         $el.append('<span class="play glyphicon glyphicon-play"></span>');
         
         $el.children('span:first').css({
         	'position': 'relative',
         	'top': ( $el.height()-parseInt(( $el.children('span:first').height()*2 )-3) )+'px'
         });
         // glyphicon glyphicon-play
         
         setTimeout(function(){
          	width = $el.width();
          	height = parseInt((width/16)*9);
          	$el.css({
          		'height': height+'px'
          	});
          	$el.children('span:first').css({
          		'top': ( $el.height()-parseInt(( $el.children('span:first').height()*2 )-3) )+'px'
              });
          },200);
         
         $el.click(function(){
         	if (typeof width === 'undefined' || typeof height === 'undefined' || typeof id === 'undefined') {
                 throw new Error('lazyYT is missing a required data attribute.');
             }
             
             $el.addClass('lazyYT-image-loaded');

             $.getJSON('https://gdata.youtube.com/feeds/api/videos/' + id + '?v=2&alt=json', function (data) {
                 $('#lazyYT-title-' + id).text(data.entry.title.$t);
             });

             $el.removeClass('thumbnail');
             if (!$el.hasClass('lazyYT-video-loaded') && $el.hasClass('lazyYT-image-loaded')) {
                 $el.html('<iframe width="' + width + '" height="' + height + '" src="//www.youtube.com/embed/' + id + '?autoplay=1&' + youtubeParameters + '" frameborder="0" allowfullscreen></iframe>')
                     .removeClass('lazyYT-image-loaded')
                     .addClass('lazyYT-video-loaded');
             }

             setTimeout(function(){
               	width = $el.width();
               	height = parseInt((width/16)*9);
               	$el.css({
               		'height': height+'px'
               	});
               	$el.children('span:first').css({
               		'top': ( $el.height()-parseInt(( $el.children('span:first').height()*2 )-3) )+'px'
                   });
               },200);
         });
    	});
    }

    $.fn.lazyYT = function () {
        return this.each(function () {
            var $el = $(this).css('cursor', 'pointer');
            setUp($el);
        });
    };

}(jQuery));
