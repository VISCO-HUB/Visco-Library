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
	$BADZIP = '{"response": "BADIMG", "name": "' . $ONAME . '"}';
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
	
	$PATH = PRODUCTS::PRODFULLPATH($PROD);
	
	$NAME = DB::UNIQUEID(20);
	NEW PREVIEW($FTMP, $NAME);
	
	$PREVIEWS = DB::PARSE_VALUE($PROD->previews);
	$PREVIEWS[] = $NAME;
	
	
	$SET['previews'] =  IMPLODE(';', $PREVIEWS);
	$WHERE['id'] = $ID;
			
	$RESULT = DB::UPDATE($TABLE, $SET, $WHERE);
		
	$FNAME = $PATH . 'preview\\';
	FS::CREATEDIR($FNAME);
	
	$FNAME .= $NAME . '.' . $EXT;
	
	IF(!MOVE_UPLOADED_FILE($FTMP, $FNAME)) DIE($MOVEERROR);
	
	DIE($SUCCESS);
?>