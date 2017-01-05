<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
	
	
	$MYSQLI = DB::CONNECT();
	
	$RESULT = DB::SELECT('models');
	$MODELS = DB::TOARRAY($RESULT);
	FOREACH($MODELS AS $MODEL) {		
		ECHO $MODEL->name;
		ECHO '|';
		ECHO $MODEL->manufacturer;	
		ECHO '|';
		ECHO $MODEL->project;			
		ECHO ';';		
	}
	
	DB::CLOSE();
?>