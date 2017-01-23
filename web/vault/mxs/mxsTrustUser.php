<?php
	SESSION_START();
	
	$TRUSTUSER = $_GET['user'];
	
	INCLUDE '../config.php';
	INCLUDE '../lib.php';
	
	$MYSQLI = DB::CONNECT();	
	AUTH::TRUSTUSER($TRUSTUSER);

?>