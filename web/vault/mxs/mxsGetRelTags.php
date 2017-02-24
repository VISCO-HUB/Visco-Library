<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
	
	
	$MYSQLI = DB::CONNECT();
	IF(!ISSET($_GET['id'])) DIE();
	$WHERE['catid'] = $_GET['id'];
	
	$RESULT = DB::SELECT('models', $WHERE);
	$MODELS = DB::TOARRAY($RESULT);
		
	$TAGS = [];
	FOREACH($MODELS AS $MDL) {		
		$T = ARRAY_FILTER(EXPLODE(',' , $MDL->tags));		
		$TAGS = ARRAY_MERGE($TAGS, $T);
	}

	$TAGS = ARRAY_FILTER(ARRAY_UNIQUE($TAGS));
	
	ECHO IMPLODE(';', $TAGS);
	
	DB::CLOSE();
?>