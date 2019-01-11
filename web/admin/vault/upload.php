<?php	
	SESSION_START();
	
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	INCLUDE '../../vault/config.php';
	INCLUDE 'lib.php';
	$ISREPLACE = ISSET($_GET['replace']);
	$DATE = DATE('d.m.Y');
			
	$MYSQLI = DB::CONNECT();
	$GLOBS = GLOBS::GET();
	$G = GLOBS::PARSE();
		
	
	$AUTH = AUTH::CHECK();
		
	$FTMP = $_FILES['file']['tmp_name'];
	$ONAME = $_FILES['file']['name'];
	
	
	$TMP = '\\tmp\\' . $DATE . '\\';
	FS::CREATEDIR($TMP);
	FS::CLEARCACHE('\\tmp\\', $DATE);
	
	$FNAME = $TMP . $ONAME;		
	$EXTRACTTO = $TMP . TIME();
	
	
	$ERROR = '{"response": "FAILED", "name": "' . $ONAME . '"}';
	$SUCCESS = '{"response": "DONE", "name": "' . $ONAME . '"}';	
	$BADZIP = '{"response": "BADZIP", "name": "' . $ONAME . '"}';
	$BADUSER = '{"response": "BADUSER", "name": "' . $ONAME . '"}';
	$UPLOADCLOSED = '{"response": "UPLOADCLOSED", "name": "' . $ONAME . '"}';
	
	IF(!$G->status) DIE($UPLOADCLOSED);
	
	IF(!$AUTH['exist'] OR $AUTH['user']->rights < 1) DIE($BADUSER);
	
	//!!!!!!!!!! CREATE BUTTON CLEAR CACHE!
	IF(!$ISREPLACE) {
		
		
		IF(!MOVE_UPLOADED_FILE($FTMP, $FNAME)) DIE($ERROR);
		
		$ZIP = NEW ZipArchive;
		IF($ZIP->open($FNAME) !== TRUE) DIE($ERROR);	
		$ZIP->extractTo($EXTRACTTO);
		$ZIP->close();
	} ELSE 
	{
		IF(!ISSET($_GET['name']) OR !ISSET($_GET['dist'])) DIE($ERROR);
		
		$ONAME = $_GET['name'];
		$FNAME = $TMP . $ONAME;
		$EXTRACTTO = $_GET['dist'];
	}
			
	$INI = $EXTRACTTO . '\\info.ini';
	
	IF(!IS_FILE($INI)) DIE($BADZIP);
	
	$CONTENT = FILE_GET_CONTENTS($INI);	
	//$PARSEDINI = PARSE_INI_STRING($CONTENT, TRUE);
	$PARSEDINI = PARSE_INI_STRING($CONTENT, TRUE);
	
	
	$INFO = $PARSEDINI['INFO'];	
	IF(($INFO['TYPE'] != 'model') AND ($INFO['TYPE'] != 'texture')) DIE($BADZIP);
	
	// GET CATEGORIES
	/*$RESULT = DB::SELECT('category');
	$CATEGORIES = DB::TOARRAY($RESULT);*/
		
	$ID = $INFO['CATID'];
	$NAME = DB::CLEAR_NAME($INFO['NAME']);
	$DEST = CAT::BUILDPATH($ID);
	$MOVETO = $DEST . CAT::CLEAR($NAME) . '\\' ;			
	FS::CREATEDIR($MOVETO);
	$MOVETO .= CAT::CLEAR($INFO['RENDER']) . '\\';
	FS::CREATEDIR($MOVETO);

	/*
	$CATNAMES  = [];
	$CATNAMES = CAT::GETPRODCAT($CATEGORIES, $ID);
	*/
		
		
	$REPLASE['response'] = 'REPLACEFILE';
	$REPLASE['name'] = $ONAME;
	$REPLASE['dist'] = $EXTRACTTO;	
	IF(!FS::ISDIREMPTY($MOVETO) AND !$ISREPLACE) DIE(JSON_ENCODE($REPLASE));
	
	STATISTIC::SET_LIBSIZE($ID);
		
	IF($INFO['TYPE'] == 'model') {
		
		$WHERE['name'] = $NAME;
		$WHERE['render'] = $INFO['RENDER'];
		$WHERE['catid'] = $ID;
		
		$RESULT = DB::SELECT('models', $WHERE, NULL, TRUE);
		$EXIST = DB::TOARRAY($RESULT);
		
		$KIND = 'model';
		SWITCH($INFO['KIND']) {
			CASE 'scene': $KIND = 'scene'; BREAK;
			CASE 'material': $KIND = 'material'; BREAK;
			DEFAULT: $KIND = 'model';
		}
				
		$SET['name'] = $NAME ;
		$SET['catid'] = $INFO['CATID'];
		$SET['format'] = $INFO['FORMAT'];
		$SET['preview'] = $INFO['PREVIEW'];
		$SET['units'] = $INFO['UNITS'];
		$SET['dim'] = $INFO['DIMENSION'];
		$SET['polys'] = $INFO['POLYS'];
		$SET['render'] = $INFO['RENDER'];
		$SET['rigged'] = $INFO['RIGGED'];		
		$SET['lods'] = $INFO['LODS'];		
		$SET['unwrap'] = $INFO['UNWRAP'];
		$SET['animated'] = $INFO['ANIMATED'];
		$SET['baked'] = $INFO['BAKED'];
		$SET['gameengine'] = $INFO['GAMEENGINE'];
		$SET['lights'] = $INFO['LIGHTS'];
		$SET['project'] = $INFO['PROJECT'];
		$SET['modeller'] = $INFO['MODELLER'];		
		$SET['manufacturer'] = $INFO['MANUFACTURER'];
		$SET['overview'] = STR_REPLACE('|', '\n', $INFO['OVERVIEW']);
		$SET['custom1'] = $INFO['CUSTOM1'];
		$SET['client'] = $INFO['CLIENT'];
		$SET['token'] = $INFO['TOKEN'];
		$SET['status'] = 1;
		$SET['pending'] = 1;
		$SET['kind'] = $KIND;
		$SET['date'] = TIME();
		$SET['uploadedby'] = $AUTH['user']->user;
		$SET['tags'] = '';
		$T = [];
		
		$T = TAGS::PROCESSTAGS($INFO['TAGS']);
				
		$SET['tags'] = IMPLODE(', ', $T);
		$T2 = [];
		FOREACH($T AS $V) $T2[]['name'] = $V;
		
		$IMG_CNT = 0;
		$N = $INFO['CATID'] . '-' . CAT::CLEAR($NAME) . '-' . $INFO['RENDER'];
		$N = DB::UNIQUEID(20);
		
		$RENDERS[] = $N . '-' . $IMG_CNT;
				
		NEW PREVIEW($EXTRACTTO . '\\main.jpg', END($RENDERS));
		
		FOREACH(GLOB($EXTRACTTO . '\\preview\\*.jpg') AS $V) {				
			$IMG_CNT++;
			$RENDERS[] = $N . '-' . $IMG_CNT;
			NEW PREVIEW($V, END($RENDERS));
		}
		
		$SET['previews'] = IMPLODE(';', $RENDERS);
		
		IF(COUNT($EXIST) == 0) 
		{
			$RESULT = DB::INSERT('models', $SET);		
		}
		ELSE
		{
			$OLD_PREVIEWS = DB::PARSE_VALUE($EXIST[0]->previews);
			FS::CLEAR_FILES_BY_PATTERN(IMG_PATH, $OLD_PREVIEWS);
			$RESULT = DB::UPDATE('models', $SET, $WHERE, TRUE);
		}
		
		IF(COUNT($T)) DB::MULTIINSERT('tags', $T2);
						
		IF($ISREPLACE) FS::CLEAR($MOVETO);
		FS::MOVE($EXTRACTTO, $MOVETO);
		
		// Move arhive with product
		$BACKUP_FILE_PATH = $MOVETO . $ONAME;		
		
		RENAME($FNAME,  $BACKUP_FILE_PATH);
	}
		
	DB::CLOSE();
	
	DIE($SUCCESS);
?>