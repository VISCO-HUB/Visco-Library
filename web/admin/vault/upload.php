<?php	
	GLOBAL $GLOBS;
	GLOBAL $MYSQLI;
	INCLUDE '../../vault/config.php';
	INCLUDE 'lib.php';
	$ISREPLACE = ISSET($_GET['replace']);
	$DATE = DATE('d.m.Y');
		
	$MYSQLI = DB::CONNECT();
	$GLOBS = GLOBS::GET();	
	
	$FTMP = $_FILES['file']['tmp_name'];
	$ONAME = $_FILES['file']['name'];
	
	$ERROR = '{"response": "FAILED", "name": "' . $ONAME . '"}';
	$SUCCESS = '{"response": "DONE", "name": "' . $ONAME . '"}';
	$REPLASE = '{"response": "REPLACEFILE", "name": "' . $ONAME . '"}';
	$BADZIP = '{"response": "BADZIP", "name": "' . $ONAME . '"}';
	
	$TMP = '\\tmp\\' . $DATE . '\\';
	FS::CREATEDIR($TMP);
	FS::CLEARCACHE('\\tmp\\', $DATE);
	
	$FNAME = $TMP . $ONAME;		
	$EXTRACTTO = $TMP . TIME();
	
	//!!!!!!!!!! CREATE BUTTON CLEAR CACHE!
	
	IF(!MOVE_UPLOADED_FILE($FTMP, $FNAME)) DIE($ERROR);
	
	$ZIP = NEW ZipArchive;
	IF($ZIP->open($FNAME) !== TRUE) DIE($ERROR);	
	$ZIP->extractTo($EXTRACTTO);
	$ZIP->close();
	
	$INI = $EXTRACTTO . '\\info.ini';
	
	IF(!IS_FILE($INI)) DIE($BADZIP);
	
	$INI_UTF8 = MB_CONVERT_ENCODING(FILE_GET_CONTENTS($INI), 'UTF-8', 'UCS-2LE');
	$PARSEDINI = PARSE_INI_STRING($INI_UTF8, TRUE);
	
	$INFO = $PARSEDINI['INFO'];	
	IF(($INFO['TYPE'] != 'model') AND ($INFO['TYPE'] != 'texture')) DIE($BADZIP);
	
	// !!!! MUST ADD CHEK FOR ALL FILES!
	$ID = $INFO['CATID'];
	$NAME = $INFO['NAME'];
	$DEST = CAT::BUILDPATH($ID);
	$MOVETO = $DEST . CAT::CLEAR($NAME) . '\\' ;			
	FS::CREATEDIR($MOVETO);
	$MOVETO .= CAT::CLEAR($INFO['RENDER']) . '\\';
	FS::CREATEDIR($MOVETO);
		
	IF(!FS::ISDIREMPTY($MOVETO) AND !$ISREPLACE) DIE($REPLASE);
	
		
	IF($INFO['TYPE'] == 'model') {
		$WHERE['name'] = $NAME;
		$WHERE['render'] = $INFO['RENDER'];
		$WHERE['catid'] = $ID;
		
		$RESULT = DB::SELECT('models', $WHERE, NULL, TRUE);
		$EXIST = DB::TOARRAY($RESULT);
		
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
		$SET['lights'] = $INFO['LIGHTS'];
		$SET['project'] = $INFO['PROJECT'];
		$SET['modeller'] = $INFO['MODELLER'];
		$SET['tags'] = TRIM(STR_REPLACE(' ', '', $INFO['TAGS']), ',') . ',';
		$SET['manufacturer'] = $INFO['MANUFACTURER'];
		$SET['overview'] = $INFO['OVERVIEW'];
		$SET['custom1'] = $INFO['CUSTOM1'];
		$SET['client'] = $INFO['CLIENT'];
		$SET['status'] = 0;
		$SET['pending'] = 1;
		$SET['date'] = TIME();
		
		$IMG_CNT = 0;
		$N = $INFO['CATID'] . '-' . CAT::CLEAR($NAME) . '-' . $INFO['RENDER'];
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
			$RESULT = DB::UPDATE('models', $SET, $WHERE, TRUE);
		}
		
		IF($SET['tags']) {
			$TAGS = EXPLODE(',', $SET['tags']);
			
			FOREACH($TAGS AS $TAG) {
				IF(STRLEN($TAG) > 2) $T[]['name'] = TRIM($TAG);
			}
			DB::MULTIINSERT('tags', $T);
		}
				
		IF($ISREPLACE) FS::CLEAR($MOVETO);
		FS::MOVE($EXTRACTTO, $MOVETO);
		
		$BACKUP_FILE_PATH = $MOVETO . $ONAME;		
		RENAME($FNAME,  $BACKUP_FILE_PATH);
	}
		
	DB::CLOSE();
	
	DIE($SUCCESS);
?>