<?php
	SESSION_START();
	
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;

	IF(!ISSET($_GET['id'])) DIE();
	IF(!ISSET($_GET['user'])) DIE();
		
	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
		
	$MYSQLI = DB::CONNECT();
	$GLOBS = GLOBS::GET();
		
	$OUT = JSON_DECODE(MXS::MODELADD($_GET['id'], $_GET['user']));
		
	ECHO '<data>' . PHP_EOL;
	ECHO '	<responce value="' . $OUT->responce . '"></responce>' . PHP_EOL;
	ECHO '	<file value="' . $OUT->file . '"></file>' . PHP_EOL;
	ECHO '	<id value="' . $OUT->id . '"></id>' . PHP_EOL;
	ECHO '	<user value="' . $_GET['user'] . '"></user>' . PHP_EOL;
	ECHO '</data>' . PHP_EOL;	
	
	DB::CLOSE();
	
?>