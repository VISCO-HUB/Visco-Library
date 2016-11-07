<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
	
	$MYSQLI = DB::CONNECT();
	
	$PID = '0';
	IF(ISSET($_GET['pid'])) $PID = $_GET['pid'];
	IF($PID < 0) DIE('');
	
	$WHERE['parent'] = $PID;	
	$WHERE['status'] = 1;	
	$WHERE['type'] = 1;	
	$RESULT = DB::SELECT('category', $WHERE, 'sort', TRUE);
	$CATEGORIES = DB::TOARRAY($RESULT);
	FOREACH($CATEGORIES AS $CAT) {		
		ECHO $CAT->id;
		ECHO '|';
		ECHO $CAT->name;
		ECHO ';';		
	}
	
	DB::CLOSE();
?>