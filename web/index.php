<?php

	INCLUDE 'vault/config.php';
	INCLUDE 'vault/lib.php';
	
	$USER = AUTH::USER();	
	
?>

<!doctype html>
<html ng-app="app" ng-swipe-right="toggleOverlayMenu(true, $event)" ng-swipe-left="toggleOverlayMenu(false, $event)" ng-swipe-disable-mouse>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="Sat, 01 Dec 2001 00:00:00 GMT">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#7BC144">
<link rel="icon" href="/img/favicon.png" type="image/png">
<title>Assets Library</title>
<!--<link rel="stylesheet" type="text/css" href="css/template.css">-->
<script src="js/jquery-latest.js"></script>
<script type="text/javascript" src="js/angular.min.js"></script>
<script type="text/javascript" src="js/angular-route.min.js"></script>
<script type="text/javascript" src="js/angular-sanitize.min.js"></script>
<script type="text/javascript" src="js/angular-cookies.min.js"></script>
<script type="text/javascript" src="js/angular-file-upload.min.js"></script>
<script type="text/javascript" src="js/angular-touch.min.js"></script>
<script type="text/javascript" src="js/angular-animate.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/ui-bootstrap-tpls-2.1.3.min.js"></script>
<!--<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">-->
<link href="../css/dark-theme.min.css" rel="stylesheet" type="text/css">
<!--<link href="../css/template.css" rel="stylesheet" type="text/css">-->
<link href="../css/template-dark.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
		<script src="js/html5shiv.min.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
</head>

<body ng-style="{'overflow': showLightBox || webgl.length || showOverlayMenu ? 'hidden' : 'auto'}" >
<div preview></div>
<div class="header">
	<div class="container">
		<div class="padding-0-15">
			<div class="navbar-header hidden-xxs"> <a href="#/"><img src="visco_logo.svg" class="logo"></a> <span class="head-title hidden-xs">Assets Library <sup>BETA</sup></span> </div>
			<div id="navbar3" class="navbar-default float-left-xxs">
				<ul class="nav nav-buttons margin-left-minus-15-xxs">					
					<li class="hidden-xs"><a href="#/"><span class="glyphicon glyphicon-home"></span></a></li>					
					<li ng-show="auth.browser=='MXS'"><a href="" ng-click="mxsGoBack()"><span class="glyphicon glyphicon-arrow-left"></span></a></li>
					<li ng-show="auth.browser=='MXS'"><a href="" ng-click="mxsGoForward()"><span class="glyphicon glyphicon-arrow-right"></span></a></li>
					
					
					<li class="dropdown" ng-show="auth.browser=='MXS'"> <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-refresh"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="" ng-click="mxsForceRefresh()">Refresh Page</a></li>
							<li><a href="" ng-click="mxsClearIeCache()">Clear IE Cache</a></li>
						</ul>
					</li>
					
					<li class="visible-xs-inline" ng-show="!isHome"><a href="" ng-click="toggleOverlayMenu()" ><span class="glyphicon glyphicon-menu-hamburger"></span></a></li>										
					<li class="dropdown" ng-show="auth.rights >= 0"> <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li class="dropdown-header">Hi, {{auth.name || auth.user}}</li>
							<li class="divider"></li>
							<li ng-show="auth.rights > 0"><a href="/admin/">Admin Panel</a></li>
							<li><a href="#/profile/profile">Profile</a></li>
							<li><a href="" ng-click="hideShowFeedback(true)">Send Feedback</a></li>
						</ul>
					</li>
					<li class=""><a href="#/profile/favorites"><span class="glyphicon glyphicon-heart"></span></a></li>					
					<li class=""><a href="" ng-click="singOut()"><span class="glyphicon glyphicon-log-out"></span></a></li>
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
<div class="side-menu-popup col-xs-12 col-sm-6 col-md-4 col-lg-4" ng-show="showOverlayMenu">
	<div class="close"><span class="glyphicon glyphicon-remove" aria-hidden="true" ng-click="toggleOverlayMenu()"></span></div>
	<div menu></div>
</div>
<div class="overlay pointer" ng-show="showOverlayMenu" ng-click="toggleOverlayMenu()">
</div>

<div class="container">
	<div class="alert-global">
		<div ng-show="globals.status==0" class="alert alert-danger">
		{{globals.message}}
		</div>
	</div>

	<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xlg-4 col-xlg-offset-4 padding-0" search ng-if="isHome"></div>
	<div class="col-sm-4 col-md-4 col-lg-3 col-xlg-2" ng-controller="menuCtrl"  ng-class="isHome ? 'hidden visible-xs' : 'col-sm-4 col-md-4 col-lg-3 hidden-xs col-xlg-3 col-xxlg-2'">				
		<div menu></div>
	</div>
	<div class="" ng-class="isHome ? 'col-sm-12 col-md-12 col-lg-12' : 'col-sm-8 col-md-8 col-lg-9 col-xs-12 col-xlg-9 col-xxlg-10'">
		<div ng-view autoscroll="true" keep-scroll-pos></div>
	</div>
</div>
<br>
<div class="hidden">
	<?php ECHO $USER; ?>
</div>
<iframe id="download" ng-src="{{download}}" iframe-onload="downloadMsg()" class="hidden"></iframe>

<div lightbox></div>
<div feedback></div>
<div quickfavorites></div>
<div webglplayer></div>
</body>
</html>


