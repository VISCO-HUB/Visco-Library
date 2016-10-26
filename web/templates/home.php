<?php

	INCLUDE '../vault/config.php';
	INCLUDE '../vault/lib.php';
	
	$MYSQLI = DB::CONNECT();
	AUTH::USER();		
?>
<div class="container">
	<h1>It is the hidden content!</h1>
	Yeah! You success logged!
	<br>
	<br>
	<a href="#/admin">Open Admin Panel</a>
	<br>
	<br>
	<br>
	<button class="btn btn-primary" ng-click="singOut()">Sign out</button>	
</div>
