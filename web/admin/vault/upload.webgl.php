<?php	
	SESSION_START();
	
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	INCLUDE '../../vault/config.php';
	INCLUDE 'lib.php';
	
	$MYSQLI = DB::CONNECT();
	$GLOBS = GLOBS::GET();	
	
	$AUTH = AUTH::CHECK();
	
	$FTMP = $_FILES['file']['tmp_name'];
	$ONAME = $_FILES['file']['name'];
	$EXT = END(EXPLODE('.', $ONAME));
	
	$ERROR = '{"response": "FAILED", "name": "' . $ONAME . '"}';
	$MOVEERROR = '{"response": "MOVEERROR", "name": "' . $ONAME . '"}';
	$SUCCESS = '{"response": "DONE", "name": "' . $ONAME . '"}';
	$BADZIP = '{"response": "BADZIP", "name": "' . $ONAME . '"}';
	$BADUSER = '{"response": "BADUSER", "name": "' . $ONAME . '"}';
	
	IF($AUTH['user']->rights < 1) DIE($BADUSER);
	
	//$NAME = DB::UNIQUEID(20);
	//NEW PREVIEW($FTMP, $NAME);
	
	IF(!ISSET($_GET['id']) || !IS_NUMERIC($_GET['id'])) DIE($ERROR);
	IF(!ISSET($_GET['type']) || !IS_NUMERIC($_GET['type'])) DIE($ERROR);
	
	$ID = $_GET['id'];
	$TYPE = $_GET['type'];
	
	
	$TABLE = PRODUCTS::TYPE($TYPE);
	$WHERE['id'] = $ID;
	$RESULT = DB::SELECT($TABLE, $WHERE);
	$PROD = $RESULT->fetch_object();
		
	IF(!$PROD) DIE($ERROR);
	
	$NAME = DB::UNIQUEID(10);
	
	$PATH = WEBGL_PATH . $NAME . '\\';
	
		
	FS::CREATEDIR($PATH);
	
	$FNAME = $PATH . $NAME . '.' . $EXT;
	
	FS::CLEAR($PATH);
	
	IF(!MOVE_UPLOADED_FILE($FTMP, $FNAME)) DIE($MOVEERROR);
	
	$ZIP = NEW ZipArchive;
	IF($ZIP->open($FNAME) !== TRUE) DIE($ERROR);	
	$ZIP->extractTo($PATH);
	$ZIP->close();
	
	$INI = $PATH . '\\info.ini';
	
	$CONTENT = FILE_GET_CONTENTS($INI);	
	$PARSEDINI = PARSE_INI_STRING($CONTENT, TRUE);
	
	$INFO = $PARSEDINI['INFO'];	
	IF($INFO['TYPE'] != 'webgl') {
		FS::CLEAR($PATH);
		FS::DELDIR($PATH);
		DIE($BADZIP);	
	}
	
	FS::DEL($FNAME);
	
	$WHERE['id'] = $ID;
	$SET['webgl'] =  $NAME;				
	$RESULT = DB::UPDATE($TABLE, $SET, $WHERE);
	
	DIE($SUCCESS);
?>