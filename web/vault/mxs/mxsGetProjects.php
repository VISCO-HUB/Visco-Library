<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
		
	$MYSQLI = DB::CONNECT();
	
	$RESULT = DB::SELECT('projects');
	$PROJECTS = DB::TOARRAY($RESULT);
	FOREACH($PROJECTS AS $PROJECT) {		
		ECHO $PROJECT->name;		
		ECHO ';';		
	}
	
	DB::CLOSE();
?>