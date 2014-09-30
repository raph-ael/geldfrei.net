<div class="row">
	<div class="col1">
	<h3>Newsletter</h3>
	<form class="form-inline" role="form">

	  <div class="form-group">
	    <div class="input-group">
	      <input id="newsletter-abo" class="form-control" type="email" placeholder="Deine E-Mail Adresse">
	    </div>
	  </div>
	  <button onclick="ajreq({app:'main',action:'nlabo',data:{email:$('#newsletter-abo').val()}});return false;" type="submit" class="btn btn-default">Abbonieren</button>
	      
	</form>
	</div>
	<div class="col2">
	</div>
	<div style="width:40%" class="col3 pull-right">
		<ul class="footnav">
			<li><a href="/user/login">Login</a></li>
			<li><a href="/kontakt">Kontakt</a></li>
			<li class="last"><a href="/impressum">Impressum</a></li>
		</ul>
	</div>
</div>