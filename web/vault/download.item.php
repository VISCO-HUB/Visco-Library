<?php
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	
	INCLUDE 'config.php';
	INCLUDE 'lib.php';
	
	SESSION_START();
	
	$ID = @$_GET['id'];
	$TYPE = @$_GET['type'];
	$FILE = @$_GET['file'];
	
	$MYSQLI = DB::CONNECT();
	
	IF($MYSQLI->connect_errno) {
		DIE('{"responce": "ERROR"}');
	}
	
	$GLOBS = GLOBS::GET();
			
	$NORIGHTS = '{"responce": "NORIGHTS"}';
	$RESTRICTED = '{"responce": "RESTRICTED"}';
	$UNKNOWN = '{"responce": "UNKNOWN"}';
	
	IF(!ISSET($_GET['id']) OR !ISSET($_GET['type']) OR !ISSET($_GET['file'])) {
		ECHO $RESTRICTED;
		EXIT;
	}
	
	$AUTH = AUTH::CHECK();
	$ISALLOW = $AUTH['user']->rights >= 0;
	
	IF(!$ISALLOW) {
		ECHO $RESTRICTED;
		EXIT;
	}

	SET_TIME_LIMIT(0);
	PRODUCTS::ITEMDOWNLOAD($ID, $TYPE, $FILE);
?>