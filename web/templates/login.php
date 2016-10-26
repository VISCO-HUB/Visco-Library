<?php

	SESSION_START();
	
?>

<br>
<div class="container">
	<div class="signin">
		<form ng-submit="signIn()">
			<h2>Sign in</h2>
			<input type="text" id="name" class="form-control" placeholder="User name" required="" autofocus="" ng-model="userName" ng-class="{warning: badUserName}">
			<input type="password" id="password" class="form-control" placeholder="Password" required="" ng-model="userPassword" ng-class="{warning: badUserPassword}">
			<br>
			<button class="btn btn-lg btn-default btn-block" type="submit">Sign in</button>
		</from>
	</div>
	
</div>