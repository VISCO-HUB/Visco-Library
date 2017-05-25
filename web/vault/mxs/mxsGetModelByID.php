<?php

	INCLUDE '../../vault/config.php';
	INCLUDE '../../vault/lib.php';
	
	
	$MYSQLI = DB::CONNECT();
	IF(!ISSET($_GET['id'])) DIE();
	$WHERE['id'] = $_GET['id'];
	
	$RESULT = DB::SELECT('models', $WHERE);	
	$MODEL = $RESULT->fetch_object();
	
	ECHO '<data>' . PHP_EOL;
	
		
	IF($MODEL) {		
		$GLOBS = GLOBS::GET();
		$PATH = DIRNAME(PRODUCTS::GETMODELPATH($MODEL)) . '\\';
		
		FOREACH($MODEL AS $K => $V) {
			IF($K == 'previews') CONTINUE;
			ECHO '<' . $K . ' value="' . $V . '"></' . $K . '>' . PHP_EOL;
		}
		
		$RESULT = DB::SELECT('category');
		$CATEGORIES = DB::TOARRAY($RESULT);
		$CATS = MXS::EXTRACTIDS($CATEGORIES, $MODEL->catid);
		$CATS = ARRAY_REVERSE(ARRAY_FILTER(EXPLODE(';', $CATS)));
		$CNT = 1;
		FOREACH($CATS AS $CAT) {
			
			$INF = EXPLODE('|', $CAT);
			ECHO '<cat' . $CNT . ' id="' . $INF[0] . '" name="' . $INF[1] . '"></cat' . $CNT . '>' . PHP_EOL;
			$CNT++;
		}
		
		$PREVIEWS = [];
		$PREVIEWS[] = $PATH . 'main.jpg';
		FOREACH(GLOB($PATH . 'preview\\*.jpg') AS $PREV) {
			$PREVIEWS[] = $PREV;
		}
		ECHO '<previews value="' . IMPLODE(';', $PREVIEWS). '"></previews>' . PHP_EOL;
		
	} 
	
	ECHO '<responce value="' . ($MODEL ? 'OK' : 'ERROR') . '"></responce>' . PHP_EOL;
	
	ECHO '</data>' . PHP_EOL;
	DB::CLOSE();
?>