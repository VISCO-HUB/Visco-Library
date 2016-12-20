<?php

	INCLUDE '../vault/config.php';
	INCLUDE '../vault/lib.php';
	
	$USER = AUTH::ADMIN();	
	
?>

<!doctype html>
<html ng-app="app">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="Sat, 01 Dec 2001 00:00:00 GMT">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="img/favicon.ico" type="image/x-icon">
<title>Assets Library Admin</title>
<!--<link rel="stylesheet" type="text/css" href="css/template.css">-->
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular-route.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular-sanitize.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular-cookies.min.js"></script>
<script type="text/javascript" src="http://nervgh.github.io/pages/angular-file-upload/dist/angular-file-upload.min.js"></script>
<script type="text/javascript" src="../js/canvasjs.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/ui-bootstrap-tpls-2.1.3.min.js"></script>
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="../css/template.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>
<div class="header">
	<div class="container">
		<div class="padding-15">
			<div class="navbar-header hidden-xxs"> <a href="#/"><img src="../visco_logo.svg" class="logo"></a> <span class="head-title hidden-xs">Assets Library</span> </div>
			<div id="navbar3" class="navbar-default">
				<ul class="nav nav-buttons">
					<li class="active"><a href="#/" tooltip-popup-delay="200" uib-tooltip="Home" tooltip-placement="bottom"><span class="glyphicon glyphicon-home"></span></a></li>
					<li class="active"><a href="#/" tooltip-popup-delay="200" uib-tooltip="Messages" tooltip-placement="bottom"><span class="glyphicon glyphicon-envelope"></span><span class="badge badge-admin">5</span></a></li>
					<li class="dropdown" ng-show="auth.rights > 0 && auth.browser != 'MXS'"> <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" tooltip-popup-delay="200" uib-tooltip="User" tooltip-placement="bottom"><span class="glyphicon glyphicon-user"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li class="dropdown-header">Hi, {{auth.user}}</li>
							<li class="divider"></li>
							<li><a href="" ng-click="">Profile</a></li>
						</ul>
					</li>
					<li class="active"><a href="" ng-click="singOut()" tooltip-popup-delay="200" uib-tooltip="SignOut" tooltip-placement="bottom"><span class="glyphicon glyphicon-log-out"></span></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="breadcrumbs">
	<div class="container">
		<div class="padding-15">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#/">Home</a></li>
				<li class="breadcrumb-item" ng-repeat="crumb in breadcrumbs" ng-class="{active: !$last}"> <a ng-if="!$last" ng-href="{{crumb.url}}">{{crumb.name}}</a> <span ng-if="$last">{{crumb.name}}</span> </li>
			</ol>
		</div>
	</div>
</div>
<div class="container" alerts></div>
<div class="container">
	<div class="col-sm-3 col-md-3 col-lg-3"> <br>
		<ul class="nav nav-pills nav-stacked">
			<li ng-class="{active: section=='/dashboard'}"><a href="#/dashboard">Dashboard</a></li>
			<li ng-class="{active: section=='/category'}"><a href="#/category" >Categories</a></li>
			<li ng-class="{active: section=='/models'}"><a href="#/models/1" >Models</a></li>
			<li ng-class="{active: section=='/upload'}"><a href="#/upload" >Upload</a></li>
			<li ng-class="{active: section=='/users'}"><a href="#/users" >Users</a></li>
			<li ng-class="{active: section=='/settings'}"><a href="#/settings" >Settings</a></li>
		</ul>
	</div>
	<div class="col-sm-9 col-md-9 col-lg-9">
		<div ng-view></div>
	</div>
</div>
<br>
<?php ECHO $USER; ?>
</body>
</html>
