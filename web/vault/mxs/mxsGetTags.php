<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
	
	
	$MYSQLI = DB::CONNECT();
	
	$RESULT = DB::SELECT('tags');
	$TAGS = DB::TOARRAY($RESULT);
	FOREACH($TAGS AS $TAG) {		
		ECHO $TAG->name;
		ECHO ';';		
	}
	
	DB::CLOSE();
?>