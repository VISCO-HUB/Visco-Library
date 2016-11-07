<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
	
	
	$MYSQLI = DB::CONNECT();
	
	$RESULT = DB::SELECT('users');
	$USERS = DB::TOARRAY($RESULT);
	FOREACH($USERS AS $USER) {		
		ECHO $USER->user;
		ECHO ';';		
	}
	
	DB::CLOSE();
?>