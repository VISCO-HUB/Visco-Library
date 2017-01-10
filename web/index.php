<?php

	INCLUDE 'vault/config.php';
	INCLUDE 'vault/lib.php';
	
	$USER = AUTH::USER();	
	
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
<title>Assets Library</title>
<!--<link rel="stylesheet" type="text/css" href="css/template.css">-->
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular-route.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular-sanitize.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.5.8/angular-cookies.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/ui-bootstrap-tpls-2.1.3.min.js"></script>
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="../css/template.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<script type="text/ng-template" id="treeList">
	<a href="" ng-click="subcat.show=!subcat.show">{{subcat.name}}</a> 
		
    <ul ng-if="subcat.child" ng-show="subcat.show">
        <li ng-repeat="subcat in subcat.child" ng-include="'treeList'" ng-if="subcat.status==1">           
        </li>
    </ul>
</script>

<script type="text/ng-template" id="sideMenu">
	<ul class="nav nav-tabs">
	  <li class="size-17" ng-class="{active: type==1}"><a href="" ng-click="changeTab(1)">Models</a></li>
	  <li class="size-17" ng-class="{active: type==2}"><a href="" ng-click="changeTab(2)">Textures</a></li>
	</ul>
	<ul class="side-menu">
		<li ng-repeat="cat in categories" ng-if="type==cat.type && cat.status==1">
			<a href="" ng-class="{active: isSubCatActive(categories[catId].id)}" ng-click="cat.show=!cat.show">{{cat.name}}</a>
			<span class="glyphicon glyphicon-info-sign pointer size-15 padding-left-5" aria-hidden="true" tooltip-popup-delay="200" uib-tooltip="Moderators: {{cat.editors.replace(';', ', ')}}"></span>			
			<span class="glyphicon glyphicon-chevron-down size-20 float-right pointer" aria-hidden="true" ng-show="!cat.show" ng-click="cat.show=!cat.show"></span>
			<span class="glyphicon glyphicon-chevron-up size-20 float-right pointer" aria-hidden="true" ng-show="cat.show" ng-click="cat.show=!cat.show"></span>
			<ul ng-show="cat.show">
				<li ng-repeat="subcat in cat.child" ng-include="'treeList'" ng-class="subcat.show ? 'no' : 'yes'" ng-if="subcat.status==1"> </li>
			</ul>  				
		</li>
	</ul>
</script>

<body>
<div class="header">
	<div class="container">
		<div class="padding-0-15">
			<div class="navbar-header hidden-xxs"> <a href="#/"><img src="visco_logo.svg" class="logo"></a> <span class="head-title hidden-xs">Assets Library</span> </div>
			<div id="navbar3" class="navbar-default float-left-xxs">
				<ul class="nav nav-buttons margin-left-minus-15-xxs">					
					<li class="hidden-xxs" ng-show="auth.rights > 0"><a href="#/" tooltip-popup-delay="200" uib-tooltip="Home" tooltip-placement="bottom"><span class="glyphicon glyphicon-home"></span></a></li>					
					<li class="visible-xs-inline"><a href="#/" tooltip-popup-delay="200" uib-tooltip="Menu" tooltip-placement="bottom"><span class="glyphicon glyphicon-menu-hamburger"></span></a></li>					
					<li class=""><a href="#/" tooltip-popup-delay="200" uib-tooltip="Favorites" tooltip-placement="bottom"><span class="glyphicon glyphicon-heart"></span></a></li>					
					<li class="dropdown" ng-show="auth.rights > 0 && auth.browser != 'MXS'"> <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" tooltip-popup-delay="200" uib-tooltip="User" tooltip-placement="bottom"><span class="glyphicon glyphicon-user"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li class="dropdown-header">Hi, {{auth.name || auth.user}}</li>
							<li class="divider"></li>
							<li><a href="" ng-click="">Profile</a></li>
						</ul>
					</li>
					<li class="" ng-show="auth.rights > 0"><a href="/admin/" tooltip-popup-delay="200" uib-tooltip="AdminPanel" tooltip-placement="bottom"><span class="glyphicon glyphicon-cog"></span></a></li>
					<li class=""><a href="" ng-click="singOut()" tooltip-popup-delay="200" uib-tooltip="SignOut" tooltip-placement="bottom"><span class="glyphicon glyphicon-log-out"></span></a></li>
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
	<div class="col-sm-4 col-md-4 col-lg-4" ng-controller="menuCtrl" ng-include="'sideMenu'">		
	</div>
	<div class="col-sm-8 col-md-8 col-lg-8">
		<div ng-view></div>
	</div>
</div>
<br>
<?php ECHO $USER; ?>
</body>
</html>


