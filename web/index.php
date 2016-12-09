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
   	<script type="text/javascript" src="http://nervgh.github.io/pages/angular-file-upload/dist/angular-file-upload.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>   
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/ui-bootstrap-tpls-2.1.3.min.js"></script>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="css/template.css" rel="stylesheet" type="text/css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>

<br>
<div class="container" alerts></div>	

<div ng-view></div>  

</body>

</html>

