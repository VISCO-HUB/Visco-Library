<?php
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	
	INCLUDE $_SERVER['DOCUMENT_ROOT'] . '/vault/config.php';
	INCLUDE ROOT . 'admin/vault/lib.php';
		
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
		// CAT
		//CASE 'CATGET': ECHO $ISSUPERADMIN ? CAT::GET($DATA) : $NORIGHTS;
		CASE 'CATGET': ECHO CAT::GETTREE();
		BREAK;
		CASE 'GETTREE': ECHO $ISADMIN ? CAT::GETTREE() : $NORIGHTS;
		BREAK;	
		CASE 'CATSETPARAM': ECHO $ISADMIN ? CAT::CATSETPARAM($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATDEL': ECHO $ISADMIN ? CAT::DEL($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATADD': ECHO $ISADMIN ? CAT::ADD($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATSORT': ECHO $ISSUPERADMIN ? CAT::SORTORDER($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATRENAME': ECHO $ISADMIN ? CAT::REN($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATADDEDITOR': ECHO $ISSUPERADMIN ? CAT::CATADDEDITOR($DATA) : $NORIGHTS;
		BREAK;
		CASE 'CATDELDITOR': ECHO $ISSUPERADMIN ? CAT::CATDELDITOR($DATA) : $NORIGHTS;
		BREAK;
		// PROD
		CASE 'PRODGET': ECHO $ISADMIN ? PRODUCTS::GET($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODINFO': ECHO $ISADMIN ? PRODUCTS::PRODUCTINFO($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODSETPARAM': ECHO $ISADMIN ? PRODUCTS::PRODSETPARAM($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODSETNAME': ECHO $ISADMIN ? PRODUCTS::PRODSETNAME($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODSETOVERVIEW': ECHO $ISADMIN ? PRODUCTS::PRODSETOVERVIEW($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODREMOVETAG': ECHO $ISADMIN ? PRODUCTS::PRODREMOVETAG($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODADDTAGS': ECHO $ISADMIN ? PRODUCTS::PRODADDTAGS($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODSETMAINPREVIEW': ECHO $ISADMIN ? PRODUCTS::PRODSETMAINPREVIEW($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODDELPREVIEW': ECHO $ISADMIN ? PRODUCTS::PRODDELPREVIEW($DATA) : $NORIGHTS;
		BREAK;
		CASE 'PRODDELETE': ECHO $ISSUPERADMIN ? PRODUCTS::PRODDELETE($DATA) : $NORIGHTS;
		BREAK;
		//USERS
		CASE 'USERSGET': ECHO USERS::GET($DATA);
		BREAK;
		CASE 'USERSETPARAM': ECHO $ISSUPERADMIN ? USERS::USERETPARAM($DATA) : $NORIGHTS;
		BREAK;
		CASE 'USERISINFO': ECHO $ISSUPERADMIN ? USERS::USERINFO($DATA) : $NORIGHTS;
		BREAK;
		CASE 'USERGETFILTER': ECHO $ISADMIN ? USERS::USERGETFILTER() : $NORIGHTS;
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