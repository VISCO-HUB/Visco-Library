<?php

	SESSION_START();
	
?>
<html ng-app="app">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="Sat, 01 Dec 2001 00:00:00 GMT">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <title>Assets Library Login</title>
    <!--<link rel="stylesheet" type="text/css" href="css/template.css">-->	   
	<script src="../js/jquery-latest.js"></script>
	<script type="text/javascript" src="../js/angular.min.js"></script>	
    <script type="text/javascript" src="../js/angular-route.min.js"></script>
    <script type="text/javascript" src="../js/angular-sanitize.min.js"></script>    
   	<script type="text/javascript" src="../js/angular-animate.min.js"></script>
   	<script type="text/javascript" src="../js/angular-cookies.min.js"></script>
	<script type="text/javascript" src="../js/app.js"></script>   
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../js/ui-bootstrap-tpls-2.1.3.min.js"></script>
    <link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/template.css" rel="stylesheet" type="text/css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
		<script src="../js/html5shiv.min.js"></script>
		<script src="../js/respond.min.js"></script>
	<![endif]-->
</head>
<body ng-controller="loginCtrl">
<br>
<div class="container" alerts></div>
<div class="container">
	<div class="signin">
		<form ng-submit="signIn()">
			<h2>Sign in</h2>
			<input type="text" id="name" class="form-control" placeholder="User name" required="" autofocus ng-model="userName" ng-class="{warning: badUserName}">
			<input type="password" id="password" class="form-control" placeholder="Password" required="" ng-model="userPassword" ng-class="{warning: badUserPassword}">
			<br>
			<button class="btn btn-lg btn-native btn-block" type="submit">Sign in</button>
		</form>
	</div>
	<?php 
		IF(ISSET($_GET['norights'])) {
			ECHO "{{sendMessage('You have no rights to view this content!', 'error')}}"; 
		}
	?>
</div>
</body>
</html>