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
<script src="../js/jquery-latest.js"></script>
<script type="text/javascript" src="../js/angular.min.js"></script>
<script type="text/javascript" src="../js/angular-route.min.js"></script>
<script type="text/javascript" src="../js/angular-sanitize.min.js"></script>
<script type="text/javascript" src="../js/angular-cookies.min.js"></script>
<script type="text/javascript" src="../js/angular-file-upload.min.js"></script>
<script type="text/javascript" src="../js/chart/Chart.min.js"></script>
<script type="text/javascript" src="../js/angular-chart.min.js"></script>
<script type="text/javascript" src="../js/angular-animate.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/ui-bootstrap-tpls-2.1.3.min.js"></script>
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/admin-template.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
		<script src="../js/html5shiv.min.js"></script>
		<script src="../js/respond.min.js"></script>
	<![endif]-->
</head>

<body>
<div class="header">
	<div class="container">
		<div class="padding-0-15">
			<div class="navbar-header hidden-xxs"> <a href="/"><img src="../visco_logo.svg" class="logo"></a> <span class="head-title hidden-xs">Assets Library</span> </div>
			<div id="navbar3" class="navbar-default">
				<ul class="nav nav-buttons">
					<li class="visible-xs-inline visible-sm-inline"><a href="" ng-click="toggleOverlayMenu()" ><span class="glyphicon glyphicon-menu-hamburger"></span></a></li>	
					<li class="active"><a href="#/" tooltip-popup-delay="200" uib-tooltip="Home" tooltip-placement="bottom"><span class="glyphicon glyphicon-home"></span></a></li>
					<li ng-show="auth.browser=='MXS'"><a href="" ng-click="mxsGoBack()" tooltip-popup-delay="200" uib-tooltip="Back"><span class="glyphicon glyphicon-arrow-left"></span></a></li>
					<li ng-show="auth.browser=='MXS'"><a href="" ng-click="mxsGoForward()" tooltip-popup-delay="200" uib-tooltip="Forward"><span class="glyphicon glyphicon-arrow-right"></span></a></li>
					<li ng-show="auth.browser=='MXS'"><a href="" ng-click="mxsForceRefresh()" tooltip-popup-delay="200" uib-tooltip="Refresh"><span class="glyphicon glyphicon-refresh"></span></a></li>
					<li class="active"><a href="#/msg/1" tooltip-popup-delay="200" uib-tooltip="Messages" tooltip-placement="bottom">
						<span class="glyphicon glyphicon-envelope"></span><span class="badge badge-admin" ng-show="msgCnt.cnt > 0">{{msgCnt.cnt}}</span></a></li>
					<li class="dropdown" ng-show="auth.rights > 0 && auth.browser != 'MXS'"> <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" tooltip-popup-delay="200" uib-tooltip="User" tooltip-placement="bottom"><span class="glyphicon glyphicon-user"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li class="dropdown-header">Hi, {{auth.name || auth.user}}</li>
							<li class="divider"></li>
							<li><a ng-href="/#/profile/profile">Profile</a></li>
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
		<div class="padding-0-15">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#/">Home</a></li>
				<li class="breadcrumb-item" ng-repeat="crumb in breadcrumbs" ng-class="{active: !$last}"> <a ng-if="!$last" ng-href="{{crumb.url}}">{{crumb.name}}</a> <span ng-if="$last">{{crumb.name}}</span> </li>
			</ol>
		</div>
	</div>
</div>
<div class="container" alerts></div>
<div class="container">
	<div class="col-sm-3 col-md-3 col-lg-3 hidden-xs hidden-sm"> <br>
		<div menu></div>	
	</div>
	<div class="col-sm-12 col-md-9 col-lg-9 col-xs-12">
		<div ng-view></div>
	</div>
</div>
<br>
<div class="overlay pointer" ng-show="showOverlayMenu" ng-click="toggleOverlayMenu()">
</div>

<div class="side-menu-popup col-xs-12 col-sm-6 col-md-4 col-lg-4" ng-show="showOverlayMenu">
	<div class="close"><span class="glyphicon glyphicon-remove" aria-hidden="true" ng-click="toggleOverlayMenu()"></span></div>
	<div menu class="margin-top-40"></div>
</div>

<?php ECHO $USER; ?>
</body>
</html>
