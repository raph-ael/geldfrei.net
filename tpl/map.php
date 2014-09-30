<?php 
addJs('
	$(".bg.main").css("min-height",($(window).height()-400)+"px");		
	$(window).resize(function(){
		$(".bg.main").css("min-height",($(window).height()-400)+"px");
	});
	');
?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $app->getTitle(); ?></title>
<?= getHead(); ?>

<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
 <!-- Fixed navbar
 
 <nav class="navbar yamm navbar-default " role="navigation">
...
     <ul class="nav navbar-nav">
       <li class="dropdown">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown</a>
         <ul class="dropdown-menu">
           <li>
               <div class="yamm-content">
                  <div class="row"> 
                    ...
           </li>
         </ul>
       </li>
     </ul>
...

 
  -->
  	<?= getTemplate('menu');?>
    <nav id="map-nav" class="navbar yamm navbar-default " role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a style="margin-left:118px;" class="navbar-brand" href="/"><span style="visibility:hidden;" class="glyphicon glyphicon-home"></span></a>
        </div>
        <div id="map-menu" class="navbar-collapse collapse">
        <ul id="map-data" class="nav navbar-nav">
          <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Was suche ich?<b class="pull-right glyphicon glyphicon-chevron-down"></b></a>
          <ul class="dropdown-menu">
            <li>
            	<div class="yamm-content">
     				<div class="row"><?= getContent('products'); ?></div>
                </div>
            </li>
          </ul>
        </li>
        
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Was will ich?<b class="pull-right glyphicon glyphicon-chevron-down"></b></a>
          <ul class="dropdown-menu">
            <li>
            	<div class="yamm-content">
     				<div class="row"><?= getContent('consumeraction'); ?></div>
                </div>
            </li>
          </ul>
        </li>
        
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Wo soll es sein?<b class="pull-right glyphicon glyphicon-chevron-down"></b></a>
          <ul class="dropdown-menu">
            <li>
            	<div class="yamm-content">
     				<div class="row"><?= getContent('distance'); ?></div>
                </div>
            </li>
          </ul>
        </li>
        
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Verfügbarkeit<b class="pull-right glyphicon glyphicon-chevron-down"></b></a>
          <ul class="dropdown-menu">
            <li>
            	<div class="yamm-content">
     				<div class="row"><?= getContent('avail'); ?></div>
                </div>
            </li>
          </ul>
        </li>
          <!-- 
            <li><a href="/">Magazin</a></li>
            <li class="active"><a href="/karte">Karte</a></li>
            <li><a href="/anbieter">Anbieterprofile</a></li>
            <li><a href="/taste-o-mat">Taste-O-Mat</a></li>
			<li><a href="/glossar">Wissen</a></li>
			
			-->
          </ul>
          <div class="navbar-form navbar-left" role="search">
	        <div class="form-group">
	        	<div class="right-inner-addon ">
    			<i style="color:#A5AF28;" class="glyphicon glyphicon-search"></i><input id="map-searchpanel" type="text" class="form-control" placeholder="<?= s('search'); ?>">
	          	</div>
	        </div>
	      </div>
	      <div class="map-loader navbar-form navbar-right">
	        <img src="/css/img/map-loader.gif">
	      </div>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	<?= getContent('main'); ?>
	<?= $app->getModToolbar(); ?>
	<a id="logo" style="display:block;position:absolute;z-index:9900;top:-16px;left:40px;" href="/"><img style="height:80px" src="/css/img/logo.png"></a>
	<?= getFoot(); ?>
</body>
</html>