<?php	
	SESSION_START();
	
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	INCLUDE 'config.php';
	INCLUDE 'lib.php';
	
	$MYSQLI = DB::CONNECT();
	$GLOBS = GLOBS::GET();	
	
	$AUTH = AUTH::CHECK();
	$USER = $AUTH['user']->user;
	
	$FTMP = $_FILES['file']['tmp_name'];
	$ONAME = $_FILES['file']['name'];	
	$EXT = END(EXPLODE('.', $ONAME));

	$ANAME = $USER . '-' . TIME() . '.' . $EXT;
	$FNAME = AVATAR_PATH . $ANAME ;
		
	$ERROR = '{"response": "FAILED", "name": "' . $ONAME . '"}';
	$SUCCESS = '{"response": "DONE", "name": "' . $ONAME . '"}';
	$BADUSER = '{"response": "BADUSER", "name": "' . $ONAME . '"}';
		
	// DEL OLD AVATARS
	
	$USERAVATARS = GLOB(AVATAR_PATH . $USER . '-*.jpg');
	
	IF(COUNT($USER)) {
		FOREACH($USERAVATARS AS $FILE) {
			@UNLINK($FILE);
		}
	}
		
	IF(!MOVE_UPLOADED_FILE($FTMP, $FNAME)) DIE($ERROR);
		
	AVATAR::RESIZE($FNAME, $FNAME, AVATAR_SIZE);
	
	$SET['avatar'] = $ANAME;
	$WHERE['user'] = $USER;
	DB::UPDATE('users', $SET, $WHERE, TRUE);

	DIE($SUCCESS);
?>