<?php
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	
	INCLUDE 'config.php';
	INCLUDE 'lib.php';
	
	SESSION_START();
	
	$QUERY = @$_GET['query'];
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	$MYSQLI = DB::CONNECT();
	
	IF($MYSQLI->connect_errno) {
		DIE('{"responce": "ERROR"}');
	}
	
	$GLOBS = GLOBS::GET();
			
	$NORIGHTS = '{"responce": "NORIGHTS"}';
	$RESTRICTED = '{"responce": "RESTRICTED"}';
	$UNKNOWN = '{"responce": "UNKNOWN"}';
	
	IF(!ISSET($_GET['query'])) {
		ECHO $RESTRICTED;
		EXIT;
	}
		
	
	// SIGN IN
	IF($QUERY == 'SIGNIN') {			
		ECHO AUTH::SIGNIN($DATA);					
		EXIT;
	}
	
	// CHEK USER
	$AUTH = AUTH::CHECK();
	$ISUSER = $AUTH['exist'] == TRUE AND $AUTH['user']->status == 1 AND $AUTH['user']->rights >= 0;
	$ISADMIN = $AUTH['user']->rights >= 1;
	$ISSUPERADMIN = $AUTH['user']->rights == 2;

	IF(!$ISUSER) {
		ECHO $RESTRICTED;
		EXIT;
	}
		
	SWITCH($QUERY) {				
		// LOGIN
		CASE 'SIGNOUT': ECHO AUTH::SIGNOUT();
		BREAK;		
		CASE 'CATGET': ECHO CAT::GETTREE();
		BREAK;				
		// GLOBAL
		CASE 'GLOBALGET': ECHO $GLOBS;
		BREAK;
		//	HOME
		CASE 'HOMEGET': ECHO HOME::GET($DATA);
		BREAK;
		// PRODUCTS
		CASE 'PRODGET': ECHO PRODUCTS::GET($DATA);
		BREAK;
		// MXS
		CASE 'ADDMODEL': ECHO $ISUSER ? MXS::ADDMODEL($DATA) : $NORIGHTS;
		BREAK;
		// DEF
		DEFAULT: ECHO $UNKNOWN;
		BREAK;
	}					
		
	DB::CLOSE();
?>