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
	$ISUSER = $AUTH['exist'] == TRUE AND $AUTH['user']->status == 1;
	$ISADMIN = $AUTH['user']->rights == 1;
	$ISSUPERADMIN = $AUTH['user']->rights == 2;

	IF(!$ISUSER) {
		ECHO $RESTRICTED;
		EXIT;
	}
		
	SWITCH($QUERY) {				
		// LOGIN
		CASE 'SIGNOUT': ECHO AUTH::SIGNOUT();
		BREAK;
		// CAT
		CASE 'CATGET': ECHO $ISSUPERADMIN ? CAT::GET() : $NORIGHTS;
		BREAK;
		CASE 'CATDEL': ECHO $ISSUPERADMIN ? CAT::DEL($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATADD': ECHO $ISSUPERADMIN ? CAT::ADD($DATA) : $NORIGHTS;
		BREAK;
		// GLOBAL
		CASE 'GLOBALGET': ECHO $GLOBS;
		BREAK;
		CASE 'GLOBALCHANGE': ECHO $ISSUPERADMIN ? GLOBS::ADD($DATA) : $NORIGHTS;
		BREAK;		
		// DEF
		DEFAULT: ECHO $UNKNOWN;
		BREAK;
	}					
		
	DB::CLOSE();
?>