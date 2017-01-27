<?php
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	
	INCLUDE 'config.php';
	INCLUDE 'lib.php';
	
	SESSION_START();
	
	$ID = @$_GET['id'];
	$TYPE = @$_GET['type'];
	
	$MYSQLI = DB::CONNECT();
	
	IF($MYSQLI->connect_errno) {
		DIE('{"responce": "ERROR"}');
	}
	
	$GLOBS = GLOBS::GET();
			
	$NORIGHTS = '{"responce": "NORIGHTS"}';
	$RESTRICTED = '{"responce": "RESTRICTED"}';
	$UNKNOWN = '{"responce": "UNKNOWN"}';
	
	IF(!ISSET($_GET['id']) OR !ISSET($_GET['type'])) {
		ECHO $RESTRICTED;
		EXIT;
	}
	
	$AUTH = AUTH::CHECK();
	$ISADMIN = $AUTH['user']->rights >= 1;
	
	IF(!$ISADMIN) {
		ECHO $RESTRICTED;
		EXIT;
	}
	
	SET_TIME_LIMIT(0);
	PRODUCTS::MODELDOWNLOAD($ID, $TYPE);
?>