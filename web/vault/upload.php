<?php
	INCLUDE 'config.php';
	INCLUDE 'lib.php';
	
	$ERROR = 'Failed';
	$SUCCESS = 'Done';
	
	$MYSQLI = DB::CONNECT();
	$GLOBS = GLOBS::GET();
	$GLOBS = GLOBS::PARSE();
	$PATH = $GLOBS->path;

	$FTMP = $_FILES['file']['tmp_name'];
	$ONAME = $_FILES['file']['name'];
	
	$TMP = $PATH . 'tmp\\';
	$FNAME = $TMP . $ONAME;
	$EXTRACTTO = $TMP . time();
	
	FS::CLEAR($TMP);
		
	FUNCTION CHECKVAR($VAR) {
		IF(!COUNT($VAR)) DIE($ERRROR);
	}
	
	IF(!MOVE_UPLOADED_FILE($FTMP, $FNAME)) DIE($ERROR);
	
	$ZIP = NEW ZipArchive;
	IF($ZIP->open($FNAME) !== TRUE) DIE($ERROR);
	
	$ZIP->extractTo($EXTRACTTO);
	$ZIP->close();
	
	$INI = $EXTRACTTO . '\\info.txt';
	IF(!IS_FILE($INI)) DIE($ERRROR);
	
	$PARSEDINI = PARSE_INI_FILE($INI, TRUE);
	$INFO = $PARSEDINI['INFO'];
	
	IF($INFO['TYPE'] != 'model') DIE($ERRROR);
	
	// !!!! MUST ADD CHEK FOR ALL FILES!
	$ID = $INFO['CATID'];
	$NAME = $INFO['NAME'];
	$DEST = $PATH . CAT::BUILDPATH($ID);
	$MOVETO = $DEST . CAT::CLEAR($NAME) . '\\';	
		
	FS::MOVE($EXTRACTTO, $MOVETO);	

	IF($INFO['TYPE'] == 'model') {
		$SET['name'] = $NAME ;
		$SET['catid'] = $INFO['CATID'];
		$SET['format'] = $INFO['FORMAT'];
		$SET['preview'] = $INFO['PREVIEW'];
		$SET['units'] = $INFO['UNITS'];
		$SET['dim'] = $INFO['DIMENSION'];
		$SET['polys'] = $INFO['POLYS'];
		$SET['render'] = $INFO['RENDER'];
		$SET['rigged'] = $INFO['RIGGED'];
		$SET['uvw'] = $INFO['UVW'];
		$SET['unwrap'] = $INFO['UNWRAP'];
		$SET['projectid'] = $INFO['PROJECT'];
		$SET['modeller'] = $INFO['MODELLER'];
		$SET['tags'] = $INFO['TAGS'];

		$RESULT = DB::INSERT('models', $SET);
	}
	file_put_contents($TMP .'debug.txt', $INFO['lol']);
	
	DB::CLOSE();
?>