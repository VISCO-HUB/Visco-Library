<?php

	///////////////////////////////////////////////////////
	// MYSQLI CLASS
	///////////////////////////////////////////////////////
	
	CLASS DB {
		PUBLIC STATIC FUNCTION STRIP($S) {																		
			$MYSQLI = $GLOBALS['MYSQLI'];		
			$S = TRIM($S);
			$S = HTMLSPECIALCHARS($S);										
			$S = $MYSQLI->real_escape_string($S);			
			RETURN $S;
		}
		
		PUBLIC STATIC FUNCTION CLEAR_NAME($NAME) {																		
			$NAME = STR_REPLACE('&', 'and', $NAME);
			$NAME = STR_REPLACE('"', '\'', $NAME);
			RETURN $NAME;
		}
						
		PUBLIC STATIC FUNCTION PARSE_VALUE($VALUE, $DELIM = ';') {
			RETURN ARRAY_FILTER(EXPLODE($DELIM, STR_REPLACE(' ', '', $VALUE)));
		}
		
		PUBLIC STATIC FUNCTION CONNECT() {					
			RETURN NEW MYSQLI(MYSQL_SERVER, MYSQL_USER, MYSQL_PWD, MYSQL_DB);	
		}
		
		PUBLIC STATIC FUNCTION CLOSE() {					
			$MYSQLI = $GLOBALS['MYSQLI'];
			$MYSQLI->CLOSE();	
		}
		
		PUBLIC STATIC FUNCTION GETWHERE($WHERE) {
			$W = [];
			
			FOREACH($WHERE AS $KEY => $VALUE) {							
				IF(!IS_ARRAY($VALUE)) {
					$VALUE = SELF::STRIP($VALUE);							
					$KEY = SELF::STRIP($KEY);
				
					$W[] = $KEY . "=" . "'" . $VALUE . "'";
				} ELSE
				{
					FOREACH($VALUE AS $KEY2 => $VALUE2){
						$VALUE2 = SELF::STRIP($VALUE2);							
						$KEY2 = SELF::STRIP($KEY2);
						
						$W[] = $KEY . "=" . "'" . $VALUE2 . "'";
					}
				}
			}
			
			RETURN $W;
		}
			
		PUBLIC STATIC FUNCTION SELECT($TABLE, $WHERE = [], $SORT = NULL, $AND = NULL, $LIMIT = NULL, $ACCESS = [], $REVERSE = NULL) {		
			$MYSQLI = $GLOBALS['MYSQLI'];
						
			$W = SELF::GETWHERE($WHERE);
			
			$A = [];
			IF(COUNT($ACCESS))
			{
				FOREACH($ACCESS AS $KEY => $VALUE)	{
					FOREACH($VALUE AS $V) {
						$A[] = $KEY . "=" . "'" . $V . "'";
					}
				}
			}
									
			$ATTACHWHERE = '';
			$BOOL = $AND ? 'AND' : 'OR';
			IF(COUNT($W)) $ATTACHWHERE = " WHERE (" . IMPLODE(' ' . $BOOL . ' ', $W) . ")";
	
			$ATTACHACCESS = '';
			IF(COUNT($A)) $ATTACHACCESS = (COUNT($W) ? " AND " : " WHERE") .  " (" . IMPLODE(' OR ', $A) . ")";
			
			$ATTACHSORT = '';
			IF($SORT) $ATTACHSORT = ' ORDER BY ' . SELF::STRIP($SORT);
			
			$ATTACHLIMIT = '';
			IF($LIMIT) $ATTACHLIMIT = ' LIMIT ' . SELF::STRIP($LIMIT['start']) . ", " . SELF::STRIP($LIMIT['end']);
			
			$QUERY = "SELECT SQL_CALC_FOUND_ROWS * FROM " . $TABLE . $ATTACHWHERE . $ATTACHACCESS . $ATTACHSORT . " " . ($REVERSE ? 'DESC' : '') . " " . $ATTACHLIMIT . ";";			
		
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION LAST_CNT() {
			$MYSQLI = $GLOBALS['MYSQLI'];
			$QUERY = "SELECT FOUND_ROWS() AS cnt;";			
			$RESULT = $MYSQLI->query($QUERY);
			$ROW = $RESULT->fetch_object();
		
			RETURN $ROW->cnt;
		}
		
		PUBLIC STATIC FUNCTION SELECTLIKE($TABLE, $COL, $FIND, $LIMIT = NULL, $SORT = NULL, $SORTTYPE = NULL, $CNT = NULL) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
			$ATTACHLIMIT = '';
			IF($LIMIT) $ATTACHLIMIT = ' LIMIT ' . SELF::STRIP($LIMIT['start']) . ", " . SELF::STRIP($LIMIT['end']);			

			$ATTACHSORT = '';
			IF($SORT) $ATTACHSORT = ' ORDER BY ' . SELF::STRIP($SORT) . ($SORTTYPE ? ' ' . $SORTTYPE : ' ');
			
			$M = '*';
			IF($CNT) $M = 'COUNT(*) AS cnt';
			
			$QUERY = "SELECT " . $M . " FROM `" . $TABLE . "` WHERE `" . SELF::STRIP($COL) . "` LIKE '%" . SELF::STRIP($FIND) ."%' " . $ATTACHSORT . " " . $ATTACHLIMIT . ";";			
				
			$RESULT = $MYSQLI->query($QUERY);
			
			IF($CNT) {
				$RESULT = $MYSQLI->query($QUERY);
				$ROW = mysqli_fetch_assoc($RESULT);
		
				RETURN $ROW['cnt'];
			}
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION UNIQUEID($LEN) {
			$TOKEN = '';
			$CODE = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$CODE .= "abcdefghijklmnopqrstuvwxyz";
			$CODE .= "0123456789";
			$MAX = STRLEN($CODE);
			
			FOR($I = 0; $I < $LEN; $I++) {
				$TOKEN .= $CODE[rand(0, $MAX - 1)];
			}

			RETURN  $TOKEN;
		}
		
		PUBLIC STATIC FUNCTION SELECTUNIQUE($COL, $TABLE, $WHERE = [], $COMP = '=') {
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$W = [];
			FOREACH($WHERE AS $KEY => $VALUE) {											
				$KEY = SELF::STRIP($KEY);
				$VALUE = SELF::STRIP($VALUE);							
				
				$W[] = $KEY . $COMP . "'" . $VALUE . "'";
			}
						
			IF(COUNT($W)) $ATTACHWHERE = " WHERE (" . IMPLODE(' AND ', $W) . ")";
			
			$QUERY = "SELECT DISTINCT " . SELF::STRIP($COL) . " FROM " . $TABLE . $ATTACHWHERE . ";";
			
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION CNT($TABLE, $WHERE = [], $AND = NULL, $ACCESS = []) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$W = SELF::GETWHERE($WHERE);
			
			$A = [];
			IF(COUNT($ACCESS))
			{
				FOREACH($ACCESS AS $KEY => $VALUE)	{
					FOREACH($VALUE AS $V) {
						$A[] = $KEY . "=" . "'" . $V . "'";
					}
				}
			}
			
			$ATTACHWHERE = '';
			$BOOL = $AND ? 'AND' : 'OR';
			IF(COUNT($W)) $ATTACHWHERE = " WHERE (" . IMPLODE(' ' . $BOOL . ' ', $W) . ")";	
			
			$ATTACHACCESS = '';
			IF(COUNT($A)) $ATTACHACCESS = (COUNT($W) ? " AND " : " WHERE") .  " (" . IMPLODE(' OR ', $A) . ")";
			
			$QUERY = "SELECT COUNT(*) AS cnt FROM " . $TABLE . $ATTACHWHERE . $ATTACHACCESS .";";			
			
			
			$RESULT = $MYSQLI->query($QUERY);
			$ROW = MYSQLI_FETCH_ASSOC($RESULT);
		
			RETURN $ROW['cnt'];
		}
		
		PUBLIC STATIC FUNCTION TOARRAY($RESULT) {
			$ROWS = [];
			WHILE($ROW = $RESULT->fetch_object()) {				
				$ROWS[] = $ROW;							
			}
			
			RETURN $ROWS;
		}
		
		// INSERT
		PUBLIC STATIC FUNCTION INSERT($TABLE, $DATA)
		{	
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$COLS = [];
			$VALUES = [];
			FOREACH($DATA AS $KEY => $VALUE)
			{
				$VALUE = SELF::STRIP($VALUE);							
				$KEY = SELF::STRIP($KEY);							
				
				$COLS[] = $KEY;						
				$VALUES[] = $VALUE == null ? 'NULL' : "'" .$VALUE . "'";
			}
				
			$QUERY = "INSERT IGNORE INTO " . $TABLE . "(" . IMPLODE(',', $COLS) . ") VALUES(" . IMPLODE(',', $VALUES) . ");";				
			$RESULT = $MYSQLI->query($QUERY);		
			
			RETURN $MYSQLI->affected_rows;
		}
		
		PUBLIC STATIC FUNCTION MULTIINSERT($TABLE, $DATA)
		{	
			$MYSQLI = $GLOBALS['MYSQLI'];
			$QUERY = '';
			
			FOREACH($DATA AS $ITEM)
			{
				$COLS = [];
				$VALUES = [];
				FOREACH($ITEM AS $KEY => $VALUE)
				{
					$VALUE = SELF::STRIP($VALUE);							
					$KEY = SELF::STRIP($KEY);							
					
					$COLS[] = $KEY;						
					$VALUES[] = $VALUE == null ? 'NULL' : "'" .$VALUE . "'";
				}
				
				$QUERY .= "INSERT IGNORE INTO " . $TABLE . "(" . IMPLODE(',', $COLS) . ") VALUES(" . IMPLODE(',', $VALUES) . ");";				
			}
			
			$RESULT = $MYSQLI->multi_query($QUERY);		
			
			RETURN $MYSQLI->affected_rows;
		}
		
		PUBLIC STATIC FUNCTION MULTIUPDATE($TABLE, $DATA, $WHERE)
		{	
			$MYSQLI = $GLOBALS['MYSQLI'];
			$QUERY = '';
			$ATTACHWHERE  = '';
						
			FOREACH($DATA AS $ITEM)
			{
				$I = 0;
				$COLS = '';
				$VALUES = '';
				$ATTACH = [];
				$ATTACHWHERE = " WHERE id=" . $WHERE[$I];
				$I++;
				
				FOREACH($ITEM AS $KEY => $VALUE)
				{
					$VALUE = SELF::STRIP($VALUE);							
					$KEY = SELF::STRIP($KEY);							
					
					$COLS = $KEY;						
					$VALUES = $VALUE == null ? 'NULL' : $VALUE;
					
					$ATTACH[] =  $KEY . "='" . $VALUES . "'";
				}	

				$QUERY .= "UPDATE " . $TABLE . " SET " . IMPLODE(',', $ATTACH) . $ATTACHWHERE . ";";				
			}
			ECHO $QUERY ;
			$RESULT = $MYSQLI->multi_query($QUERY);		
			
			RETURN $MYSQLI->affected_rows;
		}

		// DELETE
		PUBLIC STATIC FUNCTION DEL($TABLE, $DATA, $PARAM)
		{	
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$PARAM = SELF::STRIP($PARAM);							
				
			$VALUES = [];
			FOREACH($DATA AS $VALUE)
			{
				$VALUE = SELF::STRIP($VALUE);											
				$VALUES[] = $PARAM . "='" .$VALUE . "'";
			}
				
			$QUERY = "DELETE FROM " . $TABLE . " WHERE " . IMPLODE(' OR ', $VALUES) . ";";						
			$RESULT = $MYSQLI->query($QUERY);		
			
			RETURN $MYSQLI->affected_rows;
		}
		
		// UPDATE
		PUBLIC STATIC FUNCTION UPDATE($TABLE, $SET, $WHERE,  $AND = NULL)
		{			
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$V = [];
			FOREACH($SET AS $KEY => $VALUE)	{				
				$VALUE = SELF::STRIP($VALUE);							
				$KEY = SELF::STRIP($KEY);
				
				$V[] = $KEY . "=" . "'" . $VALUE . "'";
			}
			
			$W = [];
			FOREACH($WHERE AS $KEY => $VALUE) {													
				$VALUE = SELF::STRIP($VALUE);							
				$KEY = SELF::STRIP($KEY);
				
				$W[] = $KEY . "=" . "'" . $VALUE . "'";
			}
			
			$BOOL = $AND ? 'AND' : 'OR';
			$QUERY = "UPDATE " . $TABLE . " SET " . IMPLODE(',', $V) . " WHERE " . IMPLODE(' ' . $BOOL . ' ', $W) . ";";						
	
			$RESULT = $MYSQLI->query($QUERY);		
			
			RETURN $MYSQLI->affected_rows;
		}
		
		PUBLIC STATIC FUNCTION CONVERT($TEXT) {
			RETURN TRIM(ICONV(MB_DETECT_ENCODING($TEXT, MB_DETECT_ORDER(), TRUE), "UTF-8", $TEXT));
		}
	}
	
	///////////////////////////////////////////////////////
	// BACKUP
	///////////////////////////////////////////////////////
	
	CLASS BACKUP {
		PUBLIC STATIC FUNCTION DB() {
			$USER = $GLOBALS['AUTH']['user']->user;
			$FILE = 'sql@' . $USER . '@' . DATE('Y-m-d h.i.s');
			RETURN SELF::BACKUPDB($FILE);
		}
		
		PUBLIC STATIC FUNCTION BACKUPDB($FILE = NULL) {
			$ERROR = '{"responce": "BACKUPERROR"}';
			$SUCCESS = '{"responce": "BACKUPOK"}';
			$EXIST = '{"responce": "BACKUPEXIST"}';
			
			$BACKUPFILE = BACKUP_PATH . DATE('Y-m-d') . '.sql';
			IF($FILE) $BACKUPFILE = BACKUP_PATH . $FILE . '.sql';
			
			IF(FILE_EXISTS($BACKUPFILE)) RETURN $EXIST;
			
			$QUERY = '"' . MYSQL_DUMP . '" --host=' . MYSQL_SERVER . ' ' . MYSQL_DB . ' --user=' . MYSQL_USER . ' --password=' . MYSQL_PWD . ' > "' . $BACKUPFILE . '"';
			EXEC($QUERY);
			
			IF(FS::SIZE($BACKUPFILE) == 0) RETURN $ERROR;				
			
			$ZIP = NEW ZipArchive;			
			$ZIPNAME = $BACKUPFILE. '.zip';
				
			IF($ZIP->open($ZIPNAME, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
				$ZIP->addFile($BACKUPFILE, BASENAME($BACKUPFILE));
				$ZIP->close();				
			} ELSE {
				RETURN $ERROR;
			}
			
			FS::DEL($BACKUPFILE);
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION DELDB($DATA) {
			$ERROR = '{"responce": "BACKUPDELBAD"}';
			$SUCCESS = '{"responce": "BACKUPDELOK"}';
		
			IF(!ISSET($DATA->file)) RETURN $ERROR;
			
			$BACKUPFILE = BACKUP_PATH . $DATA->file;								
			IF(!FILE_EXISTS($BACKUPFILE)) RETURN $ERROR;
			
			FS::DEL($BACKUPFILE);
			IF(FILE_EXISTS($BACKUPFILE)) RETURN $ERROR;
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION GETLISTDB() {
			
			$FILES = GLOB(BACKUP_PATH . '*.zip');
			$FILES = ARRAY_REVERSE($FILES);
						
			$OUT = [];
			
			FOREACH($FILES AS $FILE) {
				$INFO['name'] = BASENAME($FILE);
				$INFO['date'] = DATE ("Y d F / H:i:s", FILEMTIME($FILE));
				$INFO['size'] = NUMBER_FORMAT(FS::SIZE($FILE) / 1048576, 2) . ' Mb';
				$INFO['path'] = HOSTNAME . BACKUP_ABSPATH . BASENAME($FILE);
								
				$OUT[] = $INFO;
			}
		
			RETURN JSON_ENCODE($OUT);
		}
	}
	
	///////////////////////////////////////////////////////
	// AUTH CLASS
	///////////////////////////////////////////////////////
	
	CLASS AUTH {
		PUBLIC STATIC FUNCTION TOKEN($USER, $PW) {
			RETURN MD5(AUTH_SALT . $USER . $PW . TIME());
		}
		
		PUBLIC STATIC FUNCTION GETINFO($ENTRIES) {
			$S = EXPLODE(',', $ENTRIES);
			 			
			$OUT['name'] = EXPLODE('=', $S[0])[1];
			$OUT['grp'] = EXPLODE('=', $S[1])[1];
			$OUT['office'] = EXPLODE('=', $S[2])[1];
			
			$OUT['name'] = DB::CONVERT($OUT['name']);
			$OUT['grp'] = DB::CONVERT($OUT['grp']);
			$OUT['office'] = DB::CONVERT($OUT['office']);
			
			RETURN $OUT;
		}
		
		PUBLIC STATIC FUNCTION SIGNIN($DATA) {						
			$ERROR = '{"responce": "USERBAD"}';
			$SUCCESS = '{"responce": "USEROK"}';
		
			IF(!ISSET($DATA->user) OR !ISSET($DATA->pwd)) RETURN $ERROR;
			
			$USER = $DATA->user . '@' . AUTH_DOMAIN;
			
			$TOKEN = SELF::TOKEN($USER, $DATA->pwd);
			
			$LDAP = LDAP_CONNECT(AUTH_SERVER);
			LDAP_SET_OPTION($LDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
			LDAP_SET_OPTION($LDAP, LDAP_OPT_REFERRALS, 0);
			
			IF(!@LDAP_BIND($LDAP, $USER, $DATA->pwd)) RETURN $ERROR; 			
			
			$DC = EXPLODE('.', AUTH_DOMAIN);
			$DN = 'DC=' . $DC[0] . ', DC=' . $DC[1];
			$FILTER = '(SAMAccountName=' . $DATA->user . ')';
				
			$SR = @LDAP_SEARCH($LDAP, $DN, $FILTER, ARRAY('ou'));
			IF($SR) $ENTRIES = LDAP_GET_ENTRIES($LDAP, $SR);
	
			$INFO = [];
			IF($ENTRIES[0]['dn']) $INFO = SELF::GETINFO($ENTRIES[0]['dn']);
			
			$_SESSION['token'] = $TOKEN;
			
			$SET = [];
			$WHERE = [];
						
			$SET['user'] = $WHERE['user'] = $DATA->user;
			$SET['token'] = $TOKEN;
			
			//RETURN FALSE;
							
			IF($INFO['office']) $SET['office'] = $INFO['office'];
			IF($INFO['name']) $SET['name'] = $INFO['name'];		
			IF($INFO['grp']) $SET['grp'] = $INFO['grp'];
			
			$RESULT = DB::SELECT('users', $WHERE);
			$ROWS = DB::TOARRAY($RESULT);
			IF($ROWS[0]){
				DB::UPDATE('users', $SET, $WHERE);												
			} ELSE {
				DB::INSERT('users', $SET);
			}
						
			RETURN $SUCCESS;			
		}
		
		PUBLIC STATIC FUNCTION CHECK() {							
			
			$AUTH = [];
			$AUTH['exist'] = FALSE;
			
			IF(!ISSET($_SESSION['token'])) RETURN $AUTH;
			$WHERE = [];
			$WHERE['token'] = $_SESSION['token'];
			$RESULT = DB::SELECT('users', $WHERE);
			
			$ROWS = MYSQLI_NUM_ROWS($RESULT);

			IF($ROWS != 1) RETURN $AUTH;			
			$ROW = $RESULT->fetch_object();
						
			$AUTH['exist'] = TRUE;
			$AUTH['user'] = $ROW;
			$AUTH['user']->browser = $_SESSION['browser'];
			
			RETURN $AUTH;
		}
		
		PUBLIC STATIC FUNCTION SIGNOUT() {
			SESSION_START();
			$_SESSION['token'] = '';
			SESSION_DESTROY();
			SESSION_UNSET();
			
			RETURN '{"responce": "SIGNEDOUT"}';
		}
		
		PUBLIC STATIC FUNCTION USER() {
			SESSION_START();
			
			$GLOBALS['MYSQLI'] = DB::CONNECT();
			$AUTH = SELF::CHECK();
								
			IF($AUTH['exist'] == TRUE AND $AUTH['user']->status == 1) {
				UNSET($AUTH['user']->token);				
				RETURN '{{setAuth(' . JSON_ENCODE($AUTH['user']) . ')}}';				
			}
			ELSE {
				HEADER("Location: " . HOSTNAME . 'login/');			
				EXIT;
				RETURN FALSE;
			}
		}
		
		PUBLIC STATIC FUNCTION ADMIN() {
			SESSION_START();
			
			$GLOBALS['MYSQLI'] = DB::CONNECT();
			$AUTH = SELF::CHECK();
								
			IF($AUTH['exist'] == TRUE AND $AUTH['user']->status == 1 AND $AUTH['user']->rights > 0) {
				UNSET($AUTH['user']->token);				
				RETURN '{{setAuth(' . JSON_ENCODE($AUTH['user']) . ')}}';				
			}
			ELSE {
				HEADER("Location: " . HOSTNAME . 'login/?norights');			
				EXIT;
				RETURN FALSE;
			}
		}
	}
	
	///////////////////////////////////////////////////////
	// GLOBAL CLASS
	///////////////////////////////////////////////////////
	
	CLASS GLOBS {
		PUBLIC STATIC FUNCTION GET() {
			$RES = DB::SELECT('global');
			$RESULT = DB::TOARRAY($RES);
			$OUT = [];
			
			FOREACH($RESULT AS $VALUE) {
				$OUT[$VALUE->name] = $VALUE->value;
			}
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION SETPARAM($DATA) {			
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->param) OR !ISSET($DATA->value)) RETURN $ERROR;
						
			$SET['value'] = $DATA->value;
			$WHERE['name'] = $DATA->param;
			
			$RESULT = DB::UPDATE('global', $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION ADD($DATA) {
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->path)) RETURN $ERROR;
						
			$SET['value'] = $DATA->path;
			$WHERE['name'] = 'path';
			
			$RESULT = DB::UPDATE('global', $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PARSE() {			
			RETURN JSON_DECODE($GLOBALS['GLOBS']);
		}
	}
	
	///////////////////////////////////////////////////////
	// FILE SYSTEM CLASS
	///////////////////////////////////////////////////////

	CLASS FS {
		PUBLIC STATIC FUNCTION CREATEDIR($DIR) {			
			RETURN @MKDIR($DIR, 0777, TRUE);			
		}
		
		PUBLIC STATIC FUNCTION UPDATEINI($INI, $SEC, $KEY, $VAL) {			
			IF(!FILE_EXISTS($INI)) RETURN FALSE;
			$INIFILE = NEW INIFILE($INI);
			$INIFILE->read($INI);
			$INIFILE->data[$SEC][$KEY] = $VAL;
			RETURN $INIFILE->write($INI);		
		}
			
		PUBLIC STATIC FUNCTION UPDATEZIP($PATH, $FILE, $NAME = NULL) {
			
			// !!! THIS FUNCTION NOT WORK. IMPOSSIBLE PACK FILES IN LAN. TRY TO FIND ANOTHER WAY!
			RETURN FALSE;
			$FILES = (GLOB($PATH . '*.zip'));
			$FNAME = $FILES[0];		
			IF(!$FNAME) RETURN FALSE;
			
			$ZIP = NEW ZipArchive;
			//$CONTENT = FILE_GET_CONTENTS($FILE);
					
			//IF($ZIP->open(REALPATH($FNAME), ZipArchive::OVERWRITE) !== TRUE) RETURN FALSE;	
			//$ZIP->addFromString($NAME, $CONTENT);
			
			//ECHO $ZIP->status;
			
			//$ZIP->addFile($FILE, $NAME);
			//$ZIP->close();			
			RETURN TRUE;
		}
		
		PUBLIC STATIC FUNCTION DIR_SIZE($DIR) {
			$SIZE = 0;
			FOREACH (GLOB(RTRIM($DIR, '/') . '/*', GLOB_NOSORT) AS $EACH) {
				$SIZE += IS_FILE($EACH) ? FILESIZE($EACH) : SELF::DIR_SIZE($EACH);
			}
			RETURN $SIZE;
		}
		
		PUBLIC STATIC FUNCTION ISDIREMPTY($DIR) {
			//IF (!IS_READABLE($DIR)) RETURN NULL; 
			RETURN (COUNT(SCANDIR($DIR)) == 2);		  
		}
		
		PUBLIC STATIC FUNCTION DELDIR($DIR) {	  
			RETURN @RMDIR($DIR);		  
		}
		
		PUBLIC STATIC FUNCTION DEL($FILE) {	  
			RETURN @UNLINK($FILE);
		}
		
		PUBLIC STATIC FUNCTION REN($DIR, $NAME) {	  
		  
			$D = EXPLODE('\\', $DIR);
		  			
			$N = COUNT($D) - 2;
			IF($N < 3) RETURN FALSE;
			$D[$N] = $NAME;
		  		
			$DIR2 = IMPLODE('\\', $D);
			
			RETURN @RENAME($DIR, $DIR2);		  
		}
		
		PUBLIC STATIC FUNCTION CLEAR($DIR, $PATTERN = "*") {	
			$FILES = GLOB($DIR . "/$PATTERN"); 
    
			FOREACH($FILES as $FILE){ 
			
				IF(IS_DIR($FILE) AND !IN_ARRAY($FILE, ARRAY('..', '.')))  {					
					SELF::CLEAR($FILE, $PATTERN);
								
					RMDIR($FILE);
				} 
				ELSE IF(IS_FILE($FILE) AND ($FILE != __FILE__)) {					
					UNLINK($FILE); 
				}
			}					
		}
		
		PUBLIC STATIC FUNCTION CLEAR_FILES_BY_PATTERN($DIR, $FILE_LIST) {
			FOREACH($FILE_LIST AS $FILE) {
				$F = $DIR . $FILE . '*';
				$FILES_TO_DEL = GLOB($F);
				FOREACH($FILES_TO_DEL AS $TO_DEL) {								
					UNLINK($TO_DEL);
				}					
			}
		}
		
		PUBLIC STATIC FUNCTION GET_LIST($DIR, $LIST = []) {
			
			$FILES = GLOB($DIR . '/*'); 
  
			FOREACH($FILES AS $FILE){ 
			
				IF(IS_DIR($FILE) AND !IN_ARRAY($FILE, ARRAY('..', '.')))  {					
					
					$LIST = SELF::GET_LIST($FILE, $LIST);
					
				} 
				ELSE IF(IS_FILE($FILE) AND ($FILE != __FILE__)) {					
					$LIST[] = $FILE;
				}
			}	
			
			RETURN $LIST;
		}
		
		
		PUBLIC STATIC FUNCTION GET_LIST_BY_EXT($PATH, $EXT = 'max', $OUT = 'path') {
			
			$DI = NEW RECURSIVEDIRECTORYITERATOR($PATH ,RECURSIVEDIRECTORYITERATOR::SKIP_DOTS);
			$IT = NEW RECURSIVEITERATORITERATOR($DI);
			
			$LIST = [];
			
			FOREACH($IT AS $FILE) {
				IF (PATHINFO($FILE, PATHINFO_EXTENSION) ==  $EXT) {
					IF($OUT == 'path') {
						$LIST[] = PATHINFO($FILE, PATHINFO_DIRNAME) . '\\';
					} ELSE {
						$LIST[] = PATHINFO($FILE, PATHINFO_DIRNAME) . '\\' . PATHINFO($FILE, PATHINFO_FILENAME) . '.' . PATHINFO($FILE, PATHINFO_EXTENSION);
					}
				}
			}
			
			RETURN $LIST;
		}
		
		
		PUBLIC STATIC FUNCTION MOVE($DIR1, $DIR2) {
			$DIR = OPENDIR($DIR1); 
			SELF::CREATEDIR($DIR2); 
			WHILE(FALSE !== ($FILE = READDIR($DIR))) { 
				IF(( $FILE != '.' ) && ( $FILE != '..' )) { 
					IF(IS_DIR($DIR1 . '/' . $FILE)) { 
						SELF::MOVE($DIR1 . '/' . $FILE, $DIR2 . '/' . $FILE); 
					} 
					ELSE { 
						COPY($DIR1 . '/' . $FILE, $DIR2 . '/' . $FILE); 
					} 
				} 
			} 
			CLOSEDIR($DIR); 
		} 
		
		PUBLIC STATIC FUNCTION CLEARCACHE($DIR, $EXCLUDE) {			
			$FILES = GLOB($DIR . "*"); 
    
			FOREACH($FILES as $FILE){ 
			
				IF(IS_DIR($FILE) AND !IN_ARRAY($FILE, ARRAY('..', '.')) AND STRPOS($FILE, $EXCLUDE) === FALSE)  {										
					SELF::CLEAR($FILE);
					RMDIR($FILE);
				} 
			}		
		}
		
		PUBLIC STATIC FUNCTION SIZE($FILE) {	
			RETURN @FILESIZE($FILE);
		}
		
		PUBLIC STATIC FUNCTION POP_DIR($PATH) {
			$TMP_PATH = EXPLODE('\\', $PATH);			
			ARRAY_POP($TMP_PATH);
			ARRAY_POP($TMP_PATH);
			RETURN IMPLODE('\\', $TMP_PATH) . '\\';
		}
	}
	
	///////////////////////////////////////////////////////
	// ACCESS CLASS
	///////////////////////////////////////////////////////
	
	CLASS ACCESS {
		PUBLIC STATIC FUNCTION EXTRACTIDS($CATEGORIES, $PARENT, &$IDS) {
			$TREE = [];			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $PARENT) {													
					FOREACH($CATEGORIES AS $SUBCAT) {
						IF($SUBCAT->parent == $CATEGORY->id)
						{
							$FLAG = TRUE;
							BREAK;
						}
					}
					
					IF($FLAG) {
						$I = SELF::EXTRACTIDS($CATEGORIES, $CATEGORY->id, $IDS);
						$IDS = ARRAY_MERGE($IDS, $I);
						$IDS = ARRAY_UNIQUE($IDS);
					}

					$IDS[] = $CATEGORY->id;
				}
			}
			
			RETURN $IDS;
		}
			
		PUBLIC STATIC FUNCTION GETACCESSID($RULE, $COL) {												
			$RESULT = DB::SELECT('category', [], 'sort');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$IDS = [];
			
			FOREACH($CATEGORIES AS $CAT) {			
				IF(IN_ARRAY($RULE, EXPLODE(';', $CAT->{$COL}))) {										
					$I = SELF::EXTRACTIDS($CATEGORIES, $CAT->id, $IDS);
					$IDS = ARRAY_MERGE($IDS, $I);
				}
			}

			$IDS = ARRAY_UNIQUE($IDS);
			IF(COUNT($IDS)) RETURN $IDS;
			RETURN [-1];
		}
	}
	
	///////////////////////////////////////////////////////
	// CATEGORIES CLASS
	///////////////////////////////////////////////////////

	CLASS CAT {
		PUBLIC STATIC FUNCTION CLEAR($S) {
			$S = TRIM($S);
			$S = STR_REPLACE(' ', '-', $S); 
			$S = PREG_REPLACE('/[^A-Za-z0-9\-]/', '', $S); 
			RETURN $S;
		}
		
		PUBLIC STATIC FUNCTION GETSUBIDS($ID, $CATEGORIES = [], $FIRST = TRUE) {
			$P = '';
			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $ID) {
			
					$P .= $CATEGORY->id . ';';
										
					FOREACH($CATEGORIES AS $SUBCAT) {
						IF($SUBCAT->parent == $CATEGORY->id)
						{
							$FLAG = TRUE;
							BREAK;
						}
					}
					
					IF($FLAG) $P .= SELF::GETSUBIDS($CATEGORY->id, $CATEGORIES, FALSE);
				}
			}
			
			IF($FIRST) RETURN DB::PARSE_VALUE($P);
			RETURN $P;
		}
		
		PUBLIC STATIC FUNCTION GETCATINFO($CATEGORIES, $ID) {																		
			FOREACH($CATEGORIES AS $CAT) {			
				IF($CAT->id == $ID) RETURN $CAT;
			}
			
			RETURN [];
		}	
		
		PUBLIC STATIC FUNCTION ADD($DATA) {
			$ERROR = '{"responce": "CATBAD"}';
			$SUCCESS = '{"responce": "CATOK"}';
		
			IF(!ISSET($DATA->name)) RETURN $ERROR;
			
			$PARENTID = 0;
			IF(ISSET($DATA->parentid)) $PARENTID = $DATA->parentid;
			
			$SET['name'] = $DATA->name;
			$SET['parent'] = $PARENTID;
			$SET['type'] = $DATA->type;
			$SET['path'] = SELF::CLEAR($DATA->name);
			$SET['candl'] = 1;
	
			$RESULT = DB::INSERT('category', $SET);
			$ID = $GLOBALS['MYSQLI']->insert_id;
			
			$PATH = SELF::BUILDPATH($ID);
					
			IF(!FS::CREATEDIR($PATH)) {
				$DEL[] = $ID;
				DB::DEL('category', $DEL, 'id');
				
				RETURN $ERROR;
			}
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION CATTOGGLEEDITOR($DATA) {
			$ERROR = '{"responce": "CATEDITORBAD"}';
			$SUCCESS = '{"responce": "CATEDITOROK"}';
			$EXIST = '{"responce": "CATEDITOREXIST"}';
			
			IF(!ISSET($DATA->id) OR !ISSET($DATA->user)) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::SELECT('category', $WHERE);
			$CAT = $RESULT->fetch_object();
					
			IF(!$CAT) RETURN $ERROR;
			
			$EDITORS = DB::PARSE_VALUE($CAT->editors);
			 
			IF(IN_ARRAY($DATA->user, $EDITORS)) {
				$EDITORS = ARRAY_DIFF($EDITORS, [$DATA->user]);
			} ELSE {
				$EDITORS[] = $DATA->user;
			}
				
			SORT($EDITORS);
			$SET['editors'] = IMPLODE(';', $EDITORS);
			
			DB::UPDATE('category', $SET, $WHERE, TRUE);
				
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION CATDELDITOR($DATA) {
			$ERROR = '{"responce": "CATEDITORDELBAD"}';
			$SUCCESS = '{"responce": "CATEDITORDELOK"}';
					
			IF(!ISSET($DATA->id) OR !ISSET($DATA->user)) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT('category', $WHERE);
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;			
				
			$E = STR_REPLACE(' ', '', $ROWS[0]->editors);
						
			$EDITORS = EXPLODE(';',  $E);
			
			$EDITORS = ARRAY_DIFF($EDITORS, [$DATA->user]);
				
			FOREACH($EDITORS AS $KEY => $VAL) IF(!STRLEN($VAL)) UNSET($EDITORS[$KEY]);
				
			$SET['editors'] = IMPLODE(';', $EDITORS) . ';';
			$RESULT = DB::UPDATE('category', $SET, $WHERE);
		
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION ASSOC_CAT($CATS, $KEY = 'id') {
			$TMP = [];
			FOREACH($CATS AS $CAT) $TMP[$CAT->{$KEY}] = $CAT;
			RETURN $TMP;
		}
		
		PUBLIC STATIC FUNCTION CATADDGRP($DATA) {
			$ERROR = '{"responce": "CATGRPBAD"}';
			$SUCCESS = '{"responce": "CATGRPOK"}';
			$EXIST = '{"responce": "CATGRPEXIST"}';
		
			IF(!ISSET($DATA->id) OR !ISSET($DATA->grp)) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT('category', $WHERE);
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;			
				
			$GRP = EXPLODE(';',  STR_REPLACE(' ', '', $ROWS[0]->premissions));
			FOREACH($GRP AS $KEY => $VAL) IF(!STRLEN($VAL)) UNSET($GRP[$KEY]);
			
			IF(IN_ARRAY($DATA->grp, $GRP)) RETURN $EXIST;
			
			$GRP[] = $DATA->grp;
						
			$SET['premissions'] = IMPLODE(';', $GRP);
			$RESULT = DB::UPDATE('category', $SET, $WHERE);		
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION CATTOGGLEGRP($DATA) {
			$ERROR = '{"responce": "CATGRPTOGGLEBAD"}';
			$SUCCESS = '{"responce": "CATGRPTOGGLEOK"}';
					
			IF(!ISSET($DATA->id) OR !ISSET($DATA->grp)) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::SELECT('category', $WHERE);
			$CAT = $RESULT->fetch_object();
					
			IF(!$CAT) RETURN $ERROR;
			
			$GROUPS = DB::PARSE_VALUE($CAT->premissions);
			 
			IF(IN_ARRAY($DATA->grp, $GROUPS)) {
				$GROUPS = ARRAY_DIFF($GROUPS, [$DATA->grp]);
			} ELSE {
				$GROUPS[] = $DATA->grp;
			}
				
			SORT($GROUPS);
			$SET['premissions'] = IMPLODE(';', $GROUPS);
			
			DB::UPDATE('category', $SET, $WHERE, TRUE);
				
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION GET($DATA) {			
			IF(!ISSET($DATA->parentid)) RETURN '[]';
		
			$WHERE['parent'] = $DATA->parentid;
		
			$RESULT = DB::SELECT('category', $WHERE);
			$RESULT = DB::TOARRAY($RESULT);
		
			RETURN JSON_ENCODE($RESULT);
		}
		
		PUBLIC STATIC FUNCTION CATSETPARAM($DATA) {			
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->param) OR !ISSET($DATA->value) OR !ISSET($DATA->id)) RETURN $ERROR;
						
			$SET[$DATA->param] = $DATA->value;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE('category', $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION REN($DATA) {			
			$ERROR = '{"responce": "RENBAD"}';
			$SUCCESS = '{"responce": "RENOK"}';
			
			IF(!ISSET($DATA->name) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$ID = $DATA->id;
			$P = SELF::CLEAR($DATA->name);
			
			$SET['name'] = $DATA->name;
			$SET['path'] = $P;
			$WHERE['id'] = $ID;
						
			$PATH = SELF::BUILDPATH($ID);
			
			IF(FS::REN($PATH, $P)) {
				$RESULT = DB::UPDATE('category', $SET, $WHERE);
				IF($RESULT > 0) RETURN $SUCCESS;
			}
						
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION GETLIB($ID) {			
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
			$CATS_ASSOC = SELF::ASSOC_CAT($CATEGORIES, 'id');
			
			$TMP_ID = $ID;
			$TMPCAT = $CATS_ASSOC[$TMP_ID];
			
			WHILE($TMP_ID = $TMPCAT->parent) {				
				$TMPCAT = $CATS_ASSOC[$TMP_ID];				
			}
			
			RETURN $TMPCAT->parent == 0 ?  $TMPCAT : -1;
		}
		
		PUBLIC STATIC FUNCTION GETPRODCAT($CATEGORIES, $ID = NULL) {
			$PRODCAT = [];
			FOREACH($CATEGORIES AS $CATEGORY) {							
				IF($CATEGORY->id == $ID) {													
					$NPC = SELF::GETPRODCAT($CATEGORIES, $CATEGORY->parent);
					
					$PRODCAT[$CATEGORY->id] = $CATEGORY->name;
					$PRODCAT = ARRAY_MERGE($PRODCAT, $NPC);
				}								
			}
			
			RETURN $PRODCAT;
		}
				
		PUBLIC STATIC FUNCTION BUILDTREE($CATEGORIES, $PARENT = 0, $GROUPS = NULL) {
			$TREE = [];
			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $PARENT) {
						
					$I['name'] = $CATEGORY->name;
					$I['id'] = $CATEGORY->id;
					$I['parent'] = $CATEGORY->parent;
					$I['status'] = $CATEGORY->status;
					$I['path'] = $CATEGORY->path;
					$I['desc'] = $CATEGORY->description;
					$I['type'] = $CATEGORY->type;
					$I['sort'] = $CATEGORY->sort;
					$I['editors'] = $CATEGORY->editors;
					$I['premissions'] = $CATEGORY->premissions;
					
					$I['candl'] = $CATEGORY->candl;
						
					IF($GROUPS) {
						$G = USERS::PARSE_GROUPS($CATEGORY->premissions, $GROUPS);
					
						$I['groups'] = [];
						FOREACH($G['name'] AS $K => $V) {
							$T['id'] = $K;
							$T['name'] = $V;
							$I['groups'][] = $T; 
						}						
					}
						
					FOREACH($CATEGORIES AS $SUBCAT) {
						IF($SUBCAT->parent == $CATEGORY->id)
						{
							$FLAG = TRUE;
							BREAK;
						}
					}
					
					IF($FLAG) $I['child'] = SELF::BUILDTREE($CATEGORIES, $I['id']);
					
					$TREE[$CATEGORY->id] = $I;
				}
			}
			
			RETURN $TREE;
		}
		
		PUBLIC STATIC FUNCTION BUILDPATH($ID = 0, $CATEGORIES = [], $FIRST = TRUE) {
			$P = '';
			IF($ID == 0) RETURN $P;
			
			IF($FIRST OR COUNT($CATEGORIES) == 0) {
				$RESULT = DB::SELECT('category');
				$CATEGORIES = DB::TOARRAY($RESULT);
			}
			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->id == $ID) {
			
					$P .= $CATEGORY->path . ';';
										
					FOREACH($CATEGORIES AS $SUBCAT) {
						IF($SUBCAT->id == $CATEGORY->parent)
						{
							$FLAG = TRUE;
							BREAK;
						}
					}
					
					IF($FLAG) $P .= SELF::BUILDPATH($CATEGORY->parent, $CATEGORIES, FALSE);
				}
			}

			$O = EXPLODE(';', $P);
			$O = ARRAY_REVERSE($O);
			$P = IMPLODE('\\', $O);
			$P = LTRIM($P, '\\');
			
			IF($FIRST) {
				$GLOBS = GLOBS::PARSE();
				RETURN $GLOBS->path . $P . '\\';
			}
			RETURN $P;
		}
		
		PUBLIC STATIC FUNCTION GETTREE() {								
			$RESULT = DB::SELECT('category', [], 'sort');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$GROUPS = USERS::GET_GROUPS();			
			$RESULT = SELF::BUILDTREE($CATEGORIES, 0, $GROUPS);
			
			RETURN JSON_ENCODE($RESULT);
		}
				
		PUBLIC STATIC FUNCTION CHANGESORT($A, $POS, $SHIFT) {
			$I = $A[$POS];
			UNSET($A[$POS]);
			ARRAY_SPLICE($A, $POS - $SHIFT , 0, [$I]);

			RETURN $A;
		}
				
		PUBLIC STATIC FUNCTION SORTORDER($DATA) {
			$ERROR = '{"responce": "CATSORTBAD"}';
			$SUCCESS = '{"responce": "CATSORTOK"}';
			
			IF(!ISSET($DATA->id) OR !ISSET($DATA->sort))RETURN $ERROR;
			$ID = $DATA->id;
			$SORT = $DATA->sort;
			
			$WHERE['id'] = $ID;
			$RESULT = DB::SELECT('category', $WHERE);
			$CAT = DB::TOARRAY($RESULT);
			
			$PARENT = -1;			
			IF($CAT[0]) $PARENT = $CAT[0]->parent;
			
			// GET ALL PARENT
			$WHERE['parent'] = $PARENT;
			$RESULT = DB::SELECT('category', $WHERE, 'sort');
			$CATEGORIES = DB::TOARRAY($RESULT);
			UNSET($WHERE);
			
			// GET POS IN CATEGORY
			$POS = -1;
			FOREACH($CATEGORIES AS $KEY => $VALUE) {
				IF($VALUE->id == $ID) {
					$POS = $KEY;
					BREAK;
				}				
			}
			
			IF($POS == -1) RETURN $ERROR;
			
			//	SORT
			$NEW = SELF::CHANGESORT($CATEGORIES, $POS, $SORT);
			// UPDATE
			$CNT = 0;
			FOREACH($NEW AS $VALUE) {				
				$SET['sort'] = $CNT; 				
				$WHERE['id'] = $VALUE->id;				
				DB::UPDATE('category', $SET, $WHERE);
				$CNT++;
			}
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION DEL($DATA) {
			$ERROR = '{"responce": "CATDELBAD"}';
			$SUCCESS = '{"responce": "CATDELOK"}';
			$WARN = '{"responce": "CATDELWARN"}';		
			IF(!ISSET($DATA->id))RETURN $ERROR;
			$ID = $DATA->id;
			
			$PATH = SELF::BUILDPATH($ID);
		
			IF(FS::ISDIREMPTY($PATH) OR !IS_DIR($PATH)) {
				FS::DELDIR($PATH);	
				$DEL[] = $ID;
				$RESULT = DB::DEL('category', $DEL, 'id');
					
				IF($RESULT > 0) RETURN $SUCCESS;							
			}
			ELSE {
				RETURN $WARN;
			}
						
			RETURN $ERROR;
		}
	}
	
	CLASS PREVIEW {		
		PUBLIC $BASE_SIZE = IMG_HUGE - (IMG_PADDING * 2);  
		PUBLIC $OUT_SIZE = IMG_HUGE;		
		PUBLIC $IMG = NULL; 
		PUBLIC $BIN_MATRIX = NULL; 
		PUBLIC $EXT = ARRAY('', 'gif', 'jpeg', 'png'); 
		PUBLIC $CUR_IMG_WIDTH = 1;  
		PUBLIC $CUR_IMG_HEIGHT = 1; 
		PUBLIC $UPLOAD_DIR = IMG_PATH;
		PUBLIC $CUR_EXT = '';
		PUBLIC $IMG_NAME = '';
				
		
		FUNCTION __CONSTRUCT($PATH, $NAME) {
			LIST($this->CUR_IMG_WIDTH, $this->CUR_IMG_HEIGHT, $TYPE) = GETIMAGESIZE($PATH);			
			$this->CUR_EXT = $this->EXT[$TYPE];
						
			IF($this->CUR_EXT) {
				
				$this->IMG_NAME = CAT::CLEAR($NAME);
				$CREATE = 'imagecreatefrom' . $this->CUR_EXT;
				$this->IMG = $CREATE($PATH);
				
				IF(!$this->IMG) RETURN FALSE;
			}
			
			$BIN = $this->MATRIX($this->IMG, FALSE);
			$RES = $this->MATRIXEXPLODE($BIN);
			$HEIGHT = $RES['size'];
			$CROPY = $RES['padding'];
			
			$BIN = $this->MATRIX($this->IMG, TRUE);
			$RES = $this->MATRIXEXPLODE($BIN);
			$WIDTH = $RES['size'];
			$CROPX = $RES['padding'];
			
			$BIGSIDE = $WIDTH > $HEIGHT ? $WIDTH : $HEIGHT;
			$this->IMG = $this->CROP($CROPX, $CROPY, $WIDTH, $HEIGHT);
			
			$this->CUR_IMG_WIDTH = $WIDTH;
			$this->CUR_IMG_HEIGHT = $HEIGHT;
						
			IF($this->BASE_SIZE < $BIGSIDE) {
				$N = $this->RESIZE($this->BASE_SIZE / $BIGSIDE);
			}
			ELSE
			{
				$N = $this->RESIZE(1);
			}
			
			IMAGEDESTROY($this->IMG);
			
			RETURN $N;
		}
		
		FUNCTION CROP($CROPX, $CROPY, $WIDTH, $HEIGHT) {
			// FIX CUT 1 PIXEL 
			$CROPY = $CROPY + 1;
			$HEIGHT = $HEIGHT + 1;
			
			RETURN IMAGECROP($this->IMG, ['x' => $CROPX, 'y' => $CROPY, 'width' => $WIDTH, 'height' => $HEIGHT]);
		}
		
		FUNCTION MATRIXEXPLODE($BIN) {
			FOR($I = 0; $I < COUNT($BIN); $I++) {
				$SUM = 0;
				FOR($J = 0; $J < COUNT($BIN[0]); $J++) {
					$SUM += $BIN[$I][$J];
				}
				$TEMP[] = $SUM ? 1 : 0;
			}
			
			$START = FALSE;
			$PARTS = 0;
			$INTERVALS = ARRAY();
			
			FOR($I = 0; $I < COUNT($TEMP); $I++) {
				IF($TEMP[$I] == 1) {
					$INTERVALS['start'] = $I;
					BREAK;
				}				
			}
			
			FOR($I = COUNT($TEMP); $I >= 0 ; $I--) {
				IF($TEMP[$I] == 1) {
					$INTERVALS['end'] = $I;
					BREAK;
				}				
			}
			
			/*FOREACH($TEMP AS $K => $V) {
				IF($V == 1 AND !$START) {
					$INTERVALS[$PARTS]['start'] = $K;
					$START = TRUE;
				}
				
				IF($V == 0 AND $START) {
					$INTERVALS[$PARTS]['end'] = $K - 1;
					$START = FALSE;
					$PARTS++;
				}
			}*/
			
			/*$W = 1;
			$S = 0;
			
			FOREACH($INTERVALS AS $K => $V) {
				IF($V['end'] - $V['start'] > 35) {
					$W = $V['end'] - $V['start'];
					$S = $V['start'];
				}
			}*/
			
			$W = $INTERVALS['end'] - $INTERVALS['start'];
			$S = $INTERVALS['start'];
			
			RETURN ARRAY('size' => $W, 'padding' => $S);
		}
		
		FUNCTION MATRIX($IMG, $HORIZ = FALSE) {
			$W = IMAGESX($IMG);
			$H = IMAGESY($IMG);
			
			IF($HORIZ) {
				$W = IMAGESY($IMG);
				$H = IMAGESX($IMG);
			}
			
			$BG = 0;
			$BIN = [];
			
			FOR($I = 0; $I < $H; $I++) {
				FOR($J = 0; $J < $W; $J++) {
					IF($HORIZ) {
						$RGB = IMAGECOLORAT($IMG, $I, $J);
					}
					ELSE {
						$RGB = IMAGECOLORAT($IMG, $J, $I);
					}
					
					LIST($R, $G, $B) = ARRAY_VALUES(IMAGECOLORSFORINDEX($IMG, $RGB));
										 
					IF($I == 0 AND $J == 0)  $BG = $R;
					$SENS = 15;
					
					IF($BG < 170) {
						$BIN[$I][$J] = 1;
					} ELSE {
						$BIN[$I][$J] = ($R > $BG - $SENS) ? 0 : 1;	
					}
					
					//IF($BG != 0) $BIN[$I][$J] = 1;
				}
			}
			
			RETURN $BIN;
		}
		
		FUNCTION MAKETHUMB($IMG, $NSIZE){						
			$IMG_P = IMAGECREATETRUECOLOR($NSIZE, $NSIZE);
			IMAGECOPYRESAMPLED($IMG_P, $IMG, 0, 0, 0, 0, $NSIZE, $NSIZE, $this->OUT_SIZE, $this->OUT_SIZE);
			$N = $this->IMG_NAME . '_' . $NSIZE . 'x' . $NSIZE . '.jpg';			
			IMAGEJPEG($IMG_P, $this->UPLOAD_DIR . $N, 100);
			IMAGEDESTROY($IMG_P);

			RETURN $N;
		}
		
		FUNCTION RESIZE($KOEF) {

			$NWIDTH = $KOEF * $this->CUR_IMG_WIDTH;
			$NHEIGHT = $KOEF * $this->CUR_IMG_HEIGHT; 
			
			$IMG_P = IMAGECREATETRUECOLOR($this->OUT_SIZE, $this->OUT_SIZE);			
			$CLR = IMAGECOLORALLOCATE($IMG_P, 255, 255, 255);
			IMAGEFILL($IMG_P, 0, 0, $CLR);
			IMAGECOPYRESAMPLED($IMG_P, $this->IMG, ($this->OUT_SIZE - $NWIDTH) / 2, ($this->OUT_SIZE - $NHEIGHT) / 2, 0, 0, $NWIDTH, $NHEIGHT, $this->CUR_IMG_WIDTH, $this->CUR_IMG_HEIGHT);
						
			$T1 = $this->IMG_NAME . '_' . $this->OUT_SIZE . 'x' . $this->OUT_SIZE . '.jpg';
			IMAGEJPEG($IMG_P, $this->UPLOAD_DIR . $T1, 99);
						
			$T2 = $this->MAKETHUMB($IMG_P, IMG_THUMB);
			$T3 = $this->MAKETHUMB($IMG_P, IMG_SMALL);
			
			IMAGEDESTROY($IMG_P);
			
			RETURN [$T1, $T2, $T3];
		}
	}
	
	CLASS PRODUCTS {
		
		PUBLIC STATIC FUNCTION TYPE($T) {				
			IF($T > 0 AND $T <= COUNT(LIBTYPES)) RETURN LIBTYPES[$T];
			RETURN NULL;
		}
				
		PUBLIC STATIC FUNCTION GET($DATA) {			
			$ERROR = '{"responce": "PRODBAD"}';
			$WHERE = [];
			$OUT['filter']['cat']['name'] = 'All';
			
			// GET USER
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
				
			
			$ACCESS = [];
			IF($AUTH->rights != 2) {
				$ACCESS_IDS = ACCESS::GETACCESSID($AUTH->user, 'editors');																
				$ACCESS['catid'] = $ACCESS_IDS;										 
			}
					
			IF(!ISSET($DATA->page)) RETURN '[]';
			IF(!ISSET($DATA->type)) RETURN '[]';
			IF(!ISSET($DATA->perpage)) RETURN '[]';
			
			IF(ISSET($DATA->filter->catid)) {				
				$CATIDS = CAT::GETSUBIDS($DATA->filter->catid, $CATEGORIES);
				IF(COUNT($ACCESS_IDS)) $CATIDS = ARRAY_INTERSECT($ACCESS_IDS, $CATIDS);
				IF(!COUNT($CATIDS))	$CATIDS = $DATA->filter->catid;
				$WHERE['catid'] = $CATIDS;				
			}
			
			IF(ISSET($DATA->filter->pending)) {				
				$WHERE['pending'] = $DATA->filter->pending;
			}
			
			IF(ISSET($DATA->filter->id)) {				
				$WHERE['id'] = $DATA->filter->id;
			}
			
			$TYPE = SELF::TYPE($DATA->type);
								
			IF(!$TYPE) RETURN $ERROR;
			
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
						
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
		
			$RESULT = DB::SELECT($TYPE, $WHERE, 'date', NULL, $LIMIT, $ACCESS, TRUE);
			$PRODUCTS = DB::TOARRAY($RESULT);
						
			//$ROWS = DB::CNT($TYPE, $WHERE, NULL, $ACCESS);						
			
			$NUMPAGES = DB::LAST_CNT();
			
			$ROWS = DB::CNT($TYPE, ['pending' => 1], TRUE, $ACCESS);						
			$PENDING = $ROWS;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			$OUT['pending'] = $PENDING;
			
			IF(ISSET($DATA->filter->catid)) 
			{
				$C = CAT::GETCATINFO($CATEGORIES, $DATA->filter->catid);
		
				$OUT['filter']['catid'] = $DATA->filter->catid;				
				$OUT['filter']['cat'] = $C;
			}
			
			IF(ISSET($DATA->filter->pending)) $OUT['filter']['cat']['name'] = 'Pending';
			
			$OUT['products'] = $PRODUCTS;
						
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION GET_PREV_NEXT_PROD($CATIDS, $ID, $TYPE, $P = '<', $RIGHTS, $PENDING) {
			
			$W = DB::GETWHERE(['catid' => $CATIDS]);
			$ATTACH_CATIDS = "(" . IMPLODE(' OR ', $W) . ") AND";
			$ATTACH_PENDING = $PENDING ? ' AND pending=1' : '';
			
			IF($RIGHTS < 2 AND !COUNT($CATIDS)) RETURN NULL;			
			IF($RIGHTS == 2 AND (!COUNT($CATIDS) OR $CATIDS[0] ==-1)) $ATTACH_CATIDS = '';
			
			$ORDER = $P == '<' ? 'DESC' : 'ASC';
			
			$MYSQLI = $GLOBALS['MYSQLI'];
			$QUERY = "SELECT * FROM " . $TYPE . " WHERE " . $ATTACH_CATIDS . " id" . $P . $ID . $ATTACH_PENDING . " ORDER BY ID " . $ORDER . " LIMIT 1;";					
			$RESULT = $MYSQLI->query($QUERY);
			
			$PROD = $RESULT->fetch_object();
					
			IF(!$PROD) RETURN NULL;
			RETURN $PROD->id;
		}
		
		PUBLIC STATIC FUNCTION PRODUCTINFO($DATA) {			
			$ERROR = '{"responce": "PRODBAD"}';
			$NOACCESS = '{"responce": "PRODNOACCESS"}';

			$AUTH = $GLOBALS['AUTH']['user'];				
				
			IF(!ISSET($DATA->id)) RETURN '[]';
			IF(!ISSET($DATA->type)) RETURN '[]';
			
			$WHERE['id'] = $DATA->id;					
			$TYPE = SELF::TYPE($DATA->type);
								
			IF(!$TYPE) RETURN $ERROR;
						
			$ACCESS_IDS = ACCESS::GETACCESSID($AUTH->user, 'editors');								
								
			$RESULT = DB::SELECT($TYPE, $WHERE);
			$PRODUCT = DB::TOARRAY($RESULT);
						
			$GLOBS = GLOBS::PARSE();
			
			$OUT['info'] = $PRODUCT[0];
			
			IF($AUTH->rights < 2 AND !IN_ARRAY($OUT['info']->catid, $ACCESS_IDS)) RETURN $NOACCESS;
			
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$CATIDS = [];
			IF(ISSET($DATA->filter->catid))
			{				
				$CATIDS = CAT::GETSUBIDS($DATA->filter->catid, $CATEGORIES);
				IF(COUNT($ACCESS_IDS)) $CATIDS = ARRAY_INTERSECT($ACCESS_IDS, $CATIDS);
				
				IF(!COUNT($CATIDS)) $CATIDS = [$DATA->filter->catid];
			} ELSE
			{
				$CATIDS = $ACCESS_IDS;
			}
														
			$OUT['list']['prev'] = SELF::GET_PREV_NEXT_PROD($CATIDS, $OUT['info']->id, $TYPE, '<', $AUTH->rights, $DATA->filter->pending);
			$OUT['list']['next'] = SELF::GET_PREV_NEXT_PROD($CATIDS, $OUT['info']->id, $TYPE, '>', $AUTH->rights, $DATA->filter->pending);
			$OUT['list']['curr'] = $OUT['info']->id;				
			
			
			IF($OUT['info']->catid) {		
				$OUT['cat'] = CAT::GETCATINFO($CATEGORIES, $OUT['info']->catid);
				
				$PATH = CAT::BUILDPATH($OUT['info']->catid);
				
				$OUT['dir'] = $PATH . CAT::CLEAR($OUT['info']->name) . '\\';
				$OUT['exist'] = IS_DIR($OUT['dir']);
			}
						
			RETURN JSON_ENCODE($OUT);
		}
			
		PUBLIC STATIC FUNCTION PRODMOVE($DATA) {			
			$ERROR = '{"responce": "MOVEBAD"}';
			$SUCCESS = '{"responce": "MOVEOK"}';
			
			IF(!ISSET($DATA->id) 
				OR !ISSET($DATA->cid) 
				OR !ISSET($DATA->type) 
				OR !IS_NUMERIC($DATA->type)
				OR !IS_NUMERIC($DATA->id) 
				OR !IS_NUMERIC($DATA->cid)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);
			
			$PROD = JSON_DECODE(SELF::PRODUCTINFO($DATA));
			
			IF(!$PROD->info) RETURN $ERROR;
			
			$WHERE = [];
			$WHERE['name'] = $PROD->info->name;
			$WHERE['render'] = $PROD->info->render;
			$WHERE['catid'] = $DATA->cid;
						
			$RESULT = DB::SELECT($TYPE, $WHERE, NULL, TRUE);
			$EXIST = DB::TOARRAY($RESULT);
			
			IF(COUNT($EXIST)) {
				$OUT['responce'] = 'MOVEEXIST';
				$OUT['prod'] = $EXIST[0];
				$OUT['url'] = '<a href="' . HOSTNAME . '#/' . PRODUCTPAGE[$DATA->type] . '/' . $OUT['prod']->id . '">View Item</a>';
				RETURN JSON_ENCODE($OUT);
			}
			
			$PATH2 = $DEST = CAT::BUILDPATH($DATA->cid) . CAT::CLEAR($PROD->info->name) . '\\';
			
			$PATH1 = SELF::PRODFULLPATH($PROD->info);			
			$PATH1 =FS::POP_DIR($PATH1);
			
			$LIST1 = FS::GET_LIST($PATH1);
			
			FS::CLEAR($PATH2);
			FS::MOVE($PATH1, $PATH2);
						
			$LIST2 = FS::GET_LIST($PATH2);
			
			$DIFF = ARRAY_DIFF($LIST1, $LIST1);
			
			IF(COUNT($DIFF)) {
				FS::CLEAR($PATH2);
				FS::DELDIR($PATH2);
				
				RETURN $ERROR;
			}
				
			$SET = [];
			$WHERE = [];
			
			$SET['catid'] = $DATA->cid;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) 
			{	
				FS::CLEAR($PATH1);
				FS::DELDIR($PATH1);
				FS::DELDIR($PATH1);
				RETURN $SUCCESS;	
			}
						
			RETURN $ERROR;
			
		}
		
		PUBLIC STATIC FUNCTION PRODSETPARAM($DATA) {			
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->param) OR !ISSET($DATA->type) OR !ISSET($DATA->value) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$SET[$DATA->param] = $DATA->value;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PRODTOGGLEPARAM($DATA) {			
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->param) OR !ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
						
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::SELECT($TYPE, $WHERE);
			$PROD = $RESULT->fetch_object();
					
			IF(!$PROD) RETURN $ERROR;
			
			$VAL = $PROD->{$DATA->param};					
			$VAL = ($VAL == '1' OR $VAL == 'Yes') ? 'No' : 'Yes';
			
			$SET[$DATA->param] = $VAL;
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PRODSETTEXTPARAM($DATA) {			
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
						
			IF(!ISSET($DATA->param) OR !ISSET($DATA->type) OR !ISSET($DATA->value) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$VAL = PREG_REPLACE("/[^A-Za-z0-9\.\&\$\%\#\(\)\!\_\-\+\s\/]/", '', $DATA->value);			
			IF(!STRLEN($VAL)) $VAL = 'N/A';
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$SET[$DATA->param] = $VAL;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PRODSETOVERVIEW($DATA) {			
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->overview) OR !ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$SET['overview'] = $DATA->overview;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PRODREMOVEWEBGL($DATA) {
			$ERROR = '{"responce": "REMOVEWEBGLBAD"}';
			$SUCCESS = '{"responce": "REMOVEWEBGLOK"}';
			$NOTEXIST = '{"responce": "NOTEXISTWEBGL"}';
			$CANTDEL = '{"responce": "CANTDELWEBGL"}';
			
			IF(!ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);
			IF(!$TYPE) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TYPE, $WHERE);
			IF(!$RESULT) RETURN $ERROR;
			
			$PROD = $RESULT->fetch_object();
			IF(!$PROD) RETURN $ERROR;
			
			IF(!$PROD->webgl) RETURN $NOTEXIST;
			
			$SET['webgl'] = NULL;
			DB::UPDATE($TYPE, $SET, $WHERE);
			
			$DIR = WEBGL_PATH . $PROD->webgl . '\\';
			IF(!IS_DIR($DIR)) RETURN $ERROR;
			
			FS::CLEAR($DIR);
			FS::DELDIR($DIR);
			
			IF(IS_DIR($DIR)) RETURN $CANTDEL;

			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION PRODREMOVETAG($DATA) {			
			$ERROR = '{"responce": "REMOVETAGBAD"}';
			$SUCCESS = '{"responce": "REMOVETAGOK"}';
						
			IF(!ISSET($DATA->tag) OR !ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TYPE, $WHERE);
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;
			
			$TAGS = DB::PARSE_VALUE($ROWS[0]->tags, ',');
			$TAGS = ARRAY_DIFF($TAGS, [$DATA->tag]);
			
			$TAGSROW = IMPLODE(',', $TAGS);
			
			$SET['tags'] = $TAGSROW ;
			$WHERE['id'] = $DATA->id;
						
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PRODADDTAGS($DATA) {			
			$ERROR = '{"responce": "ADDTAGBAD"}';
			$SUCCESS = '{"responce": "ADDTAGOK"}';
						
			IF(!ISSET($DATA->tags) OR !ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TYPE, $WHERE);
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;
						
			$OLDTAGS = DB::PARSE_VALUE($ROWS[0]->tags, ',');
			$NEWTAGS = DB::PARSE_VALUE($DATA->tags, ',');
			
			$TAGS = ARRAY_MERGE($OLDTAGS, $NEWTAGS);
			$TAGS = ARRAY_UNIQUE($TAGS);
			
			$SET['tags'] = IMPLODE(',', $TAGS);
			$WHERE['id'] = $DATA->id;
						
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION PRODSETMAINPREVIEW($DATA) {			
			$ERROR = '{"responce": "SETMAINPREVIEWBAD"}';
			$SUCCESS = '{"responce": "SETMAINPREVIEWOK"}';
						
			IF(!ISSET($DATA->name) OR !ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TYPE, $WHERE);
						
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;
		
			$PREVIEWS = EXPLODE(';', $ROWS[0]->previews);
			
			IF(ARRAY_SEARCH($DATA->name, $PREVIEWS) === FALSE) RETURN $ERROR;
			$PREVIEWS = ARRAY_DIFF($PREVIEWS, [$DATA->name]);
			ARRAY_UNSHIFT($PREVIEWS, $DATA->name);
						
			$SET['previews'] = IMPLODE(';', $PREVIEWS);
			$WHERE['id'] = $DATA->id;
						
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION GETPREVIEWPATH($NAME, $SIZE, $NOTROOT = NULL) {
			RETURN (!$NOTROOT ? IMG_PATH : '') . $NAME . '_' . $SIZE . 'x' . $SIZE . '.jpg';
		}
		
		PUBLIC STATIC FUNCTION PRODDELPREVIEW($DATA) {			
			$ERROR = '{"responce": "DELPREVIEWBAD"}';
			$SUCCESS = '{"responce": "DELPREVIEWOK"}';
			$LAST = '{"responce": "DELPREVIEWLAST"}';
						
			IF(!ISSET($DATA->name) OR !ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TYPE, $WHERE);
						
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;
		
			$PREVIEWS = DB::PARSE_VALUE($ROWS[0]->previews);
			IF(COUNT($PREVIEWS) < 2) RETURN $LAST;
						
			IF(ARRAY_SEARCH($DATA->name, $PREVIEWS) === FALSE) RETURN $ERROR;
			$PREVIEWS = ARRAY_DIFF($PREVIEWS, [$DATA->name]);
						
			$SET['previews'] = IMPLODE(';', $PREVIEWS);
			$WHERE['id'] = $DATA->id;
						
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE);
			
			IF($RESULT > 0) {			
				FS::DEL(SELF::GETPREVIEWPATH($DATA->name, IMG_HUGE));
				FS::DEL(SELF::GETPREVIEWPATH($DATA->name, IMG_THUMB));
				FS::DEL(SELF::GETPREVIEWPATH($DATA->name, IMG_SMALL));
				
				$UPLOADED_PREV = SELF::PRODFULLPATH($ROWS[0]) . 'preview\\' . $DATA->name . '.jpg';
				
				FS::DEL($UPLOADED_PREV);
				
				RETURN $SUCCESS;
			}
						
			RETURN $ERROR;
		}
		
		PUBLIC STATIC FUNCTION PRODPATH($ID, $CATEGORIES, $PROD = NULL, $MAIN = TRUE) {
			$P = '';
			IF($ID == 0) RETURN $P;
						
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->id == $ID) {
			
					$P .= $CATEGORY->path . ';';
										
					FOREACH($CATEGORIES AS $SUBCAT) {
						IF($SUBCAT->id == $CATEGORY->parent)
						{
							$FLAG = TRUE;
							BREAK;
						}
					}
					
					IF($FLAG) $P .= SELF::PRODPATH($CATEGORY->parent, $CATEGORIES, NULL, FALSE);
				}
			}
			
			IF($MAIN AND $PROD != NULL) {
				$O = EXPLODE(';', $P);
				$O = ARRAY_REVERSE($O);
				$P = IMPLODE('\\', $O);
				$P = LTRIM($P, '\\');
				
				$GLOBS = GLOBS::PARSE();
				
				RETURN $GLOBS->path . $P . '\\' . CAT::CLEAR($PROD->name) . '\\' . $PROD->render . '\\';
			}
			
			RETURN $P;
		}
		
		PUBLIC STATIC FUNCTION PRODFULLPATH($PROD) {
			$PATH = CAT::BUILDPATH($PROD->catid);
			
			$GLOBS = GLOBS::PARSE();
			$S = STR_REPLACE($GLOBS->path, '', $PATH);
			IF(STRLEN($S) < 3) RETURN NULL;	
			
			$PATH = $PATH . CAT::CLEAR($PROD->name) . '\\';
			$DIR = $PATH . $PROD->render . '\\';
			
			IF(FS::ISDIREMPTY($DIR)) RETURN NULL;
			
			RETURN $DIR;
		}
		
		PUBLIC STATIC FUNCTION PRODDELETE($DATA) {			
			$ERROR = '{"responce": "PRODDELBAD"}';
			$SUCCESS = '{"responce": "PRODDELOK"}';
			$NOTEXIST = '{"responce": "PRODDIRNOTEXIST"}';
			
						
			IF(!ISSET($DATA->type) OR !ISSET($DATA->id)) RETURN $ERROR;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TYPE, $WHERE);
						
			IF(!$RESULT) RETURN $ERROR;
			$ROWS = DB::TOARRAY($RESULT);
			IF(!$ROWS[0]) RETURN $ERROR;
		
			$PREVIEWS = DB::PARSE_VALUE($ROWS[0]->previews);
						
			$DEL[] = $DATA->id;
			
			
			$PATH = CAT::BUILDPATH($ROWS[0]->catid);
			$GLOBS = GLOBS::PARSE();
			$S = STR_REPLACE($GLOBS->path, '', $PATH);
			IF(STRLEN($S) < 3) RETURN $ERROR;			
			
			$PATH = $PATH . CAT::CLEAR($ROWS[0]->name) . '\\';
			$DIR = $PATH . $ROWS[0]->render . '\\';
						
			FS::CLEAR($DIR);
			FS::DELDIR($DIR);
			IF(FS::ISDIREMPTY($PATH)) FS::DELDIR($PATH);
			
			FS::CLEAR_FILES_BY_PATTERN(IMG_PATH, $PREVIEWS);
						
			SELF::PRODREMOVEWEBGL($DATA);
			
			$RESULT = DB::DEL($TYPE, $DEL, 'id');
			
			IF($RESULT > 0) RETURN $SUCCESS; 						
			RETURN $ERROR;
		}
			
		PUBLIC STATIC FUNCTION PRODSETNAME($DATA) {			
			$ERROR = '{"responce": "PODNAMEGBAD"}';
			$SUCCESS = '{"responce": "PODNAMEGOK"}';
			$INVALID = '{"responce": "PRODNAMEINVALID"}';
			$PRODNOTFOUND = '{"responce": "PRODNOTFOUNT"}';
			
			IF(!ISSET($DATA->type) OR !ISSET($DATA->name) OR !ISSET($DATA->id) OR !ISSET($DATA->catid) OR !ISSET($DATA->oldname)) RETURN $ERROR;
			
			IF(PREG_MATCH("/[^A-Za-z0-9\&\$\%\#\(\)\!\_\-\+\s]/", $DATA->name) OR STRLEN($DATA->name) < 4) RETURN $INVALID;
			
			$TYPE = SELF::TYPE($DATA->type);

			IF(!$TYPE) RETURN $ERROR;			
			
			$NAME = TRIM($DATA->name);
			
			$SET['name'] = $NAME;
			$WHERE['catid'] = $DATA->catid;
			$WHERE['name'] = $DATA->oldname;
			$PATH = CAT::BUILDPATH($DATA->catid);
			
			
			
			$GLOBS = GLOBS::PARSE();
			
			$S = STR_REPLACE($GLOBS->path, '', $PATH);
			IF(STRLEN($S) < 3) RETURN $ERROR;
			
			$RESULT1 = DB::SELECT($TYPE, $WHERE, NULL, TRUE);
			
			
			IF(!$RESULT1) RETURN $ERROR;
			
			$P = DB::TOARRAY($RESULT1);
		
			
			IF(!$P[0]) RETURN $ERROR;
				
				/*print_r($P);
				RETURN FALSE;*/
			$DIR1 = $PATH . CAT::CLEAR($P[0]->name) . '\\';
			$DIR2 = $PATH . CAT::CLEAR($NAME) . '\\';
			
			IF(!IS_DIR($DIR1)) RETURN $PRODNOTFOUND;		
			IF(!@RENAME($DIR1, $DIR2)) RETURN $ERROR;
						
			$RESULT = DB::UPDATE($TYPE, $SET, $WHERE, TRUE);
						
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR;
		}
	}
		
	CLASS USERS {
		
		PUBLIC STATIC FUNCTION PARSE_GROUPS($GRP, $GROUPS) {			
					
			$U = [];					
			$U['id'] = DB::PARSE_VALUE($GRP);
			
			$GROUPS_NAMES = [];
			FOREACH($U['id'] AS $ID) $GROUPS_NAMES[$ID] = $GROUPS[$ID]->name;
			$U['name'] = ARRAY_FILTER($GROUPS_NAMES);
			
			RETURN $U;
		}
		
		PUBLIC STATIC FUNCTION GET_GROUPS() {	
			$GROUPS = [];
			
			$RESULT = DB::SELECT('groups');
			$TMP = DB::TOARRAY($RESULT);
			FOREACH($TMP AS $G) {
				$GROUPS[$G->id] = $G;
			}
			
			RETURN $GROUPS;
		}
		
		PUBLIC STATIC FUNCTION GET($DATA) {			
			$ERROR = '{"responce": "USERSBAD"}';
			$WHERE = [];
				
			IF(!ISSET($DATA->page)) RETURN '[]';			
			IF(!ISSET($DATA->perpage)) RETURN '[]';
			
			$FILTER_GRP = (ISSET($DATA->filter->grp) AND $DATA->filter->grp != -1);
			$OUT['filter']['grpname'] = 'All';	
			$OUT['filter']['grp'] = $DATA->filter->grp;
			
			/*						
			
									
			IF(ISSET($DATA->filter->office) AND $DATA->filter->office != 'All') {				
				$WHERE['office'] = $DATA->filter->office;
				$OUT['filter']['office'] = $DATA->filter->office;
			} ELSE {
				 $OUT['filter']['office'] = 'All';
			}
			
			IF(ISSET($DATA->filter->status) AND $DATA->filter->status != 'All') {				
				$WHERE['status'] = $DATA->filter->status;
				$OUT['filter']['status'] = $DATA->filter->status;
			} ELSE {
				 $OUT['filter']['status'] = 'All';
			}
			
			IF(ISSET($DATA->filter->rights) AND $DATA->filter->rights != 'All') {				
				$WHERE['rights'] = $DATA->filter->rights;
				$OUT['filter']['rights'] = $DATA->filter->rights;
			} ELSE {
				 $OUT['filter']['rights'] = 'All';
			}*/
			
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
						
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
		
			
			$RESULT = DB::SELECT('users', $WHERE, NULL, TRUE, $LIMIT);
			$USERS = DB::TOARRAY($RESULT);
				
			//$ROWS = DB::CNT('users', $WHERE, TRUE);
						
			$NUMPAGES = DB::LAST_CNT();
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			
			$GROUPS = SELF::GET_GROUPS();
			
			$OUT['users'] = [];
			
			FOREACH($USERS AS $USER) {
				$U['id'] = $USER->id;
				$U['user'] = $USER->user;
				$U['rights'] = $USER->rights;
				$U['status'] = $USER->status;
				
				$G = SELF::PARSE_GROUPS($USER->grp, $GROUPS);
				
				IF($FILTER_GRP) IF(!IN_ARRAY($DATA->filter->grp, $G['id'])) CONTINUE;
								
				$U['grp'] = $G['id'];							
				$U['grpname'] = ARRAY_VALUES($G['name']);				
				$U['name'] = $USER->name;
				$U['office'] = $USER->office;
				
				$OUT['users'][] = $U;
			}
						
			IF($FILTER_GRP) $OUT['filter']['grpname'] = $GROUPS[$DATA->filter->grp]->name;
							
			RETURN JSON_ENCODE($OUT);
		}

		PUBLIC STATIC FUNCTION USERETPARAM($DATA) {							
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->param) OR !ISSET($DATA->value) OR !ISSET($DATA->id)) RETURN $ERROR;
						
			$SET[$DATA->param] = $DATA->value;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE('users', $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION USERINFO($DATA) {			
			$ERROR = '{"responce": "USERBAD"}';
						
			IF(!ISSET($DATA->id)) RETURN '[]';
						
			$WHERE['id'] = $DATA->id;					
											
			$RESULT = DB::SELECT('users', $WHERE);
			$USER = $RESULT->fetch_object();
			
			IF(!$USER) RETURN $ERROR;
					
			$GROUPS = SELF::GET_GROUPS();
			$G = SELF::PARSE_GROUPS($USER->grp, $GROUPS);			
			
			$OUT['info']['id'] = $USER->id;
			$OUT['info']['user'] = $USER->user;
			$OUT['info']['status'] = $USER->status;
			$OUT['info']['rights'] = $USER->rights;
			$OUT['info']['grp'] = $G['id'];
			$OUT['info']['grpname'] = ARRAY_VALUES($G['name']);
			$OUT['info']['name'] = $USER->name;
			$OUT['info']['office'] = $USER->office;
				
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION USERGETFILTER() {			
														
			$RESULT = DB::SELECT('groups');
			$GRP = DB::TOARRAY($RESULT);
			
			$RESULT = DB::SELECTUNIQUE('office', 'users');
			$OFFICE = DB::TOARRAY($RESULT);

			$RESULT = DB::SELECTUNIQUE('user', 'users', ['rights' => 1], '>=');
			$MODERATORS = DB::TOARRAY($RESULT);			
					
			$OUT['filter']['grp'] = $GRP;
			$OUT['filter']['office'] = $OFFICE;
			$OUT['filter']['moderators'] = $MODERATORS;
		
			RETURN JSON_ENCODE($OUT);
		}	
		
		PUBLIC STATIC FUNCTION GETGROUPS($DATA) {
			$ERROR = '{"responce": "GROUPSBAD"}';
						
			$RESULT = DB::SELECT('groups');
			$GROUPS = DB::TOARRAY($RESULT);
			
			RETURN JSON_ENCODE($GROUPS);
		}	
		
		PUBLIC STATIC FUNCTION GROUPADD($DATA) {
			$ERROR = '{"responce": "GROUPADDBAD"}';
			$SUCCESS = '{"responce": "GROUPADDOK"}';
			$WRONG = '{"responce": "GROUPWRONGFORMAT"}';
			$EXIST = '{"responce": "GROUPSEXIST"}';
			
			IF(!ISSET($DATA->name)) RETURN $ERROR;
				
			$GROUP = TRIM($DATA->name);	
				
			$WHERE['name'] = $GROUP;
			$RESULT = DB::SELECT('groups', $WHERE);
			$EXIST_GRP = DB::TOARRAY($RESULT);
			IF(COUNT($EXIST_GRP) > 0) RETURN $EXIST;	
							
			$SET['name'] = $GROUP;						
			DB::INSERT('groups', $SET);
					
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION GROUPRENAME($DATA) {
			$ERROR = '{"responce": "GROUPRENBAD"}';
			$SUCCESS = '{"responce": "GROUPRENOK"}';
						
			IF(!ISSET($DATA->name) OR !ISSET($DATA->id)) RETURN $ERROR;
				
			$GROUP = TRIM($DATA->name);	
						
			$SET['name'] = $GROUP;
			$WHERE['id'] = $DATA->id;
			DB::UPDATE('groups', $SET, $WHERE, TRUE);
					
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION GROUPDEL($DATA) {
			$ERROR = '{"responce": "GROUPDELBAD"}';
			$SUCCESS = '{"responce": "GROUPDELOK"}';
						
			IF(!ISSET($DATA->id)) RETURN $ERROR;
			
			$SET['grp'] = '';
			$WHERE['grp'] = $DATA->id;
			DB::UPDATE('users', $SET, $WHERE, TRUE);
										
			$DEL[] = $DATA->id;
			DB::DEL('groups', $DEL, 'id');
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION GROUPTOGGLE($DATA) {
			$ERROR = '{"responce": "GROUPCHANGEBAD"}';
			$SUCCESS = '{"responce": "GROUPCHANGEOK"}';
						
			IF(!ISSET($DATA->userid) OR !ISSET($DATA->groupid)) RETURN $ERROR;
						
			$WHERE['id'] = $DATA->userid;
			
			$RESULT = DB::SELECT('users', $WHERE);
			$USER = $RESULT->fetch_object();
					
			IF(!$USER) RETURN $ERROR;
			
			$GROUPS = DB::PARSE_VALUE($USER->grp);
			 
			IF(IN_ARRAY($DATA->groupid, $GROUPS)) {
				$GROUPS = ARRAY_DIFF($GROUPS, [$DATA->groupid]);
			} ELSE {
				$GROUPS[] = $DATA->groupid;
			}
				
			SORT($GROUPS);
			$SET['grp'] = IMPLODE(';', $GROUPS);
			
			DB::UPDATE('users', $SET, $WHERE, TRUE);
					
			RETURN $SUCCESS;
		}		
	}
	
	CLASS TAGS { 
		PUBLIC STATIC FUNCTION GET($DATA) {
			$RESULT = DB::SELECT('tags');
			$ROWS = DB::TOARRAY($RESULT);
									
			IF(!ISSET($DATA->page)) RETURN '[]';
			IF(!ISSET($DATA->perpage)) RETURN '[]';
						
			$CURPAGE = 1;
						
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
						
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
		
			IF(!$DATA->filter->search) {			
				$RESULT = DB::SELECT('tags', [], NULL, NULL, $LIMIT);
				$TAGS = DB::TOARRAY($RESULT);
						
				//$ROWS = DB::CNT('tags', []);
				$ROWS = DB::LAST_CNT();
			} ELSE {
				$RESULT = DB::SELECTLIKE('tags', 'name', $DATA->filter->search, $LIMIT, 'name', 'ASC');
				$TAGS = DB::TOARRAY($RESULT);
				
				$ROWS =  DB::SELECTLIKE('tags', 'name', $DATA->filter->search, NULL, 'name', 'ASC', TRUE);
			}
			
			$NUMPAGES = $ROWS;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;					
			$OUT['tags'] = $TAGS;		
			$OUT['filter'] = $DATA->filter;		
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION REFRESH($DATA) {
			$ERROR = '{"responce": "TAGSREFRESHBAD"}';
			$SUCCESS = '{"responce": "TAGSREFRESHOK"}';
			
			IF(!ISSET($DATA->type)) RETURN $ERROR;
			
			$TYPE  = '';
			IF($DATA->type == 'models') $TYPE = 'models';
			IF(!COUNT($TYPE)) RETURN $ERROR;
			
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$RESULT = DB::SELECT('models');
			$MODELS = DB::TOARRAY($RESULT);
					
			FOREACH($MODELS AS $MODEL) {
				$P = PRODUCTS::PRODPATH($MODEL->catid, $CATEGORIES, $MODEL);
				$INI = $P . INFOINI;
				IF(!FILE_EXISTS($INI)) CONTINUE;
				
				$CONTENT = FILE_GET_CONTENTS($INI);	
				$PARSEDINI = PARSE_INI_STRING($CONTENT, TRUE);
				$INFO = $PARSEDINI['INFO'];
				
				$SET['tags'] = '';
				$WHERE['id'] = $MODEL->id;	
				
				$ACTUALTAGS = COUNT($INFO['TAGS']) ? $INFO['TAGS'] : $MODEL->tags;
				
				$CATNAMES = CAT::GETPRODCAT($CATEGORIES, $MODEL->catid);
				$T = TAGS::PROCESSTAGS($ACTUALTAGS);					
				$SET['tags'] = IMPLODE(', ', $T);
				DB::UPDATE($TYPE, $SET, $WHERE, TRUE);
			}
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION DEL($DATA) {			
			$ERROR = '{"responce": "TAGSDELBAD"}';
			$SUCCESS = '{"responce": "TAGSDELOK"}';
			
			IF(!ISSET($DATA->tag)) RETURN $ERROR;
			
			$DEL[] = $DATA->tag;
			DB::DEL('tags', $DEL, 'name');
			
			FOREACH(LIBTYPES AS $TYPE) {			
				$RESULT = DB::SELECTLIKE($TYPE, 'tags', $DATA->tag);
				$PRODUCTS = DB::TOARRAY($RESULT);
				
				$SET = [];
				$WHERE = [];
				
				FOREACH($PRODUCTS AS $PROD) {				
					$TAGS = EXPLODE(',', $PROD->tags);
					$TAGS = ARRAY_DIFF($TAGS, [$DATA->tag]);
					$TAGSROW = IMPLODE(',', $TAGS);
					
					$SET['tags'] = $TAGSROW;
					$WHERE['id'] = $PROD->id;
					
					DB::UPDATE($TYPE, $SET, $WHERE);
				}
			}		
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION PROCESSTAGS($STR, $ADDITIONAL = []) {
			// EXCLUDE THIS SYMBOLS
			$SPECIALS = ['and', 'by', 'of', 'for', 'on', 'upon', 'the', 'off', 'in', 'into'];
			
			$T = [];
			IF(COUNT($STR)) {
				$TAGS = EXPLODE(',', $STR);
							
				FOREACH($TAGS AS $TAG) {
					$TAG = STRTOLOWER(TRIM($TAG));
					IF(STRLEN($TAG) > 1) $T[] = $TAG;
				}
				// ADD ADDITIONAL TAGS
				FOREACH($ADDITIONAL AS $V) 
				{
					$A = ARRAY_FILTER(EXPLODE(' ', $V));
					FOREACH($A AS $V2) $T[] = STRTOLOWER(TRIM($V2));															
				}
				$T = ARRAY_FILTER(ARRAY_UNIQUE($T));

				$T = ARRAY_DIFF($T, $SPECIALS);
			}
			
			RETURN $T;
		}
		
		PUBLIC STATIC FUNCTION CHANGE($DATA) {			
			$ERROR = '{"responce": "TAGSRENBAD"}';
			$SUCCESS = '{"responce": "TAGSRENOK"}';
			$WRONG = '{"responce": "TAGSWRONGFORMAT"}';
			$EXIST = '{"responce": "TAGSEXIST"}';
			
			IF(!ISSET($DATA->tag)) RETURN $ERROR;
			IF(!ISSET($DATA->newtag)) RETURN $ERROR;
			IF(STRLEN($DATA->newtag) < 2) RETURN $WRONG;
			
			$DEL[] = $DATA->tag;
			DB::DEL('tags', $DEL, 'name');
			
			$SET['name'] = TRIM($DATA->newtag);						
			$CNT = DB::INSERT('tags', $SET);
			IF($CNT == 0) RETURN $EXIST;
						
			//$RESULT = DB::SELECT('category');
			//$CATEGORIES = DB::TOARRAY($RESULT);
						
			FOREACH(LIBTYPES AS $TYPE) {			
				$RESULT = DB::SELECTLIKE($TYPE, 'tags', $DATA->tag);
				$PRODUCTS = DB::TOARRAY($RESULT);
				
				$SET = [];
				$WHERE = [];
				
				FOREACH($PRODUCTS AS $PROD) {				
					$TAGS = EXPLODE(',', $PROD->tags);
					$TAGS = ARRAY_DIFF($TAGS, [$DATA->tag]);
					$TAGS = ARRAY_FILTER($TAGS);
					$TAGS = ARRAY_DIFF($TAGS, ['', ' ', NULL]);
					$TAGS[] = $DATA->newtag;
					$TAGSROW = IMPLODE(',', $TAGS);
					
					$SET['tags'] = $TAGSROW;
					$WHERE['id'] = $PROD->id;
					
					// UPDATE INI
					//$P = PRODUCTS::PRODPATH($PROD->catid, $CATEGORIES, $PROD);
					//$INI = $P . INFOINI;
					
					//FS::UPDATEINI($INI, 'INFO', 'TAGS', $SET['tags']);
					//FS::UPDATEZIP($P, $INI, INFOINI);									
					DB::UPDATE($TYPE, $SET, $WHERE);
				}
			}		
			
			RETURN $SUCCESS;
		}
	}
	
	///////////////////////////////////////////////////////
	// STATISTIC CLASS
	///////////////////////////////////////////////////////
	
	CLASS STATISTIC {
		
		PUBLIC STATIC FUNCTION DISCSIZE($BYTES)
		{
			$TYPE = ARRAY("", "KB", "MG", "GB", "TB", "PB", "EB", "ZB", "YB");
			$I = 0;
			WHILE($BYTES >= 1024)
			{
				$BYTES /= 1024;
				$I++;
			  }
			
			RETURN(ROUND($BYTES, 2) . " " . $TYPE[$I]);
		}
		
		PUBLIC STATIC FUNCTION GETBYMONTH() {
			// STATISTIC BY MONTH
			
			$SET = [];			
			$Y = DATE('Y');
	
			$LIMIT['start'] = 0;
			$LIMIT['end'] = 12;
	
			$RESULT = DB::SELECT('statistic_downloads', [], 'date', NULL, $LIMIT, [], TRUE);
			$STAT = DB::TOARRAY($RESULT);
						
			RETURN ARRAY_REVERSE($STAT);
		}
		
		PUBLIC STATIC FUNCTION LIBSIZE($BYCAT = FALSE) {
			
			$WHERE['parent'] = 0;
			$RESULT = DB::SELECT('category', $WHERE);
			
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$SIZE = 0;
			$CAT_SIZE = [];
			
			$SIZES = [];
			
			FOREACH($CATEGORIES AS $CAT) {
				$SIZE += $CAT->size;
			}
			
			FOREACH($CATEGORIES AS $CAT) {
				@$CAT_SIZE[$CAT->name]->name = $CAT->name;
				@$CAT_SIZE[$CAT->name]->size = ROUND($CAT->size / $SIZE  * 100, 0);
				@$CAT_SIZE[$CAT->name]->disc_size = SELF::DISCSIZE($CAT->size);
			}
						
			IF($BYCAT) RETURN $CAT_SIZE;
			RETURN SELF::DISCSIZE($SIZE);
		}
		
		PUBLIC STATIC FUNCTION GETTOP() {
			// STATISTIC BY MONTH
						
			$LIMIT['start'] = 0;
			$LIMIT['end'] = 50;
	
			$RESULT = DB::SELECT('models', [], 'downloads', NULL, $LIMIT, [], TRUE);
			$STAT = DB::TOARRAY($RESULT);
				
			$T = [];	
							
			FOREACH($STAT AS $V) {
				$J['id'] = $V->id;
				$J['name'] = $V->name;
				$J['dwl'] = $V->downloads;
				$J['previews'] = $V->previews;
				$J['type'] = 'model';
				$T[] = $J;
			}
				
			RETURN $T;
		}
		
		PUBLIC STATIC FUNCTION GETTODAY() {
			// STATISTIC USERS
							
			$WHERE['day'] = DATE("Y-m-d", TIME());							
			RETURN DB::CNT('statistic_user_downloads', $WHERE);
		}
		
		PUBLIC STATIC FUNCTION GETUSER() {
			// STATISTIC USERS
									
			$LIMIT['start'] = 0;
			$LIMIT['end'] = 100;
	
			$WHERE['rights'] = [-1, 0];						
				
			$RESULT = DB::SELECT('users', $WHERE, 'downloads', NULL, $LIMIT, [], TRUE);
			$STAT = DB::TOARRAY($RESULT);
				
			$T = [];
			
			$CNT = 0;
			FOREACH($STAT AS $V) {			
				IF($CNT > 10) BREAK;
				
				IF(!$V->status) CONTINUE;
				
				$J['user'] = $V->user;
				$J['dwl'] = $V->downloads ? $V->downloads : 0;
				$T[] = $J;
				
				$CNT++;
			}
				
			RETURN $T;
		}
		
		PUBLIC STATIC FUNCTION SET_LIBSIZE($ID) {
			$LIB = CAT::GETLIB($ID);
			IF($LIB == -1 OR !$LIB->id) RETURN FALSE;
			
			IF(TIME() - $LIB->updated < 86400) RETURN FALSE;
			
			$GLOBS = GLOBS::PARSE();
			$DIR = $GLOBS->path . $LIB->path . '\\';
			
			$SIZE = FS::DIR_SIZE($DIR);
			
			IF(!$SIZE) RETURN FALSE;
			
			$SET['size'] = $SIZE;
			$SET['updated'] = TIME();
			$WHERE['id'] = $LIB->id;
			
			$RESULT = DB::UPDATE('category', $SET, $WHERE);						
		}
	}
	
	///////////////////////////////////////////////////////
	// DASHBOARD CLASS
	///////////////////////////////////////////////////////
	
	CLASS DASHBOARD {
				
		PUBLIC STATIC FUNCTION INFO() {
			$GLOBS = GLOBS::PARSE();
			$DIR = $GLOBS->path;
			
			$OUT = [];
			
			$WEB = 'C:\\';
				
			$LIB_SIZE = STATISTIC::LIBSIZE();
			$GRAPH_LIB = STATISTIC::LIBSIZE(TRUE);
			
						
			$OUT['space'] = (FLOOR(100 * DISK_FREE_SPACE($DIR) / DISK_TOTAL_SPACE($DIR))) . '%';			
			$OUT['free_space'] = STATISTIC::DISCSIZE(DISK_FREE_SPACE($DIR));			
			$OUT['total_space'] = STATISTIC::DISCSIZE(DISK_TOTAL_SPACE($DIR));
			$OUT['web_total'] = STATISTIC::DISCSIZE(DISK_TOTAL_SPACE($WEB));
			$OUT['web_free'] = STATISTIC::DISCSIZE(DISK_FREE_SPACE($WEB));
			$OUT['web_space'] = (FLOOR(100 * DISK_FREE_SPACE($WEB) / DISK_TOTAL_SPACE($WEB))) . '%';
			$OUT['mdl'] = DB::CNT('models');
			$OUT['tex'] = DB::CNT('textures');
			$OUT['urs'] = DB::CNT('users');
			$OUT['cmt'] = DB::CNT('comments');
			$OUT['today'] = STATISTIC::GETTODAY();
			$OUT['graph_month'] = STATISTIC::GETBYMONTH();
			$OUT['graph_top'] = STATISTIC::GETTOP();
			$OUT['graph_user'] = STATISTIC::GETUSER();
			$OUT['dist_path'] = $DIR;
			$OUT['lib_size'] = $LIB_SIZE;
			$OUT['graph_lib'] = $GRAPH_LIB;
			
			
			
			RETURN JSON_ENCODE($OUT);
		}

		PUBLIC STATIC FUNCTION DOWNLOADLOG($DATA){						
			IF(!ISSET($DATA->page)) RETURN '[]';
			IF(!ISSET($DATA->perpage)) RETURN '[]';
						
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
						
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
		
			$RESULT = DB::SELECT('statistic_user_downloads', [], 'date', NULL, $LIMIT, [], TRUE);
			$LOG = DB::TOARRAY($RESULT);
						
			$ROWS = DB::CNT('statistic_user_downloads', []);						
			$NUMPAGES = $ROWS;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;					
			$OUT['log'] = $LOG;		
						
			RETURN JSON_ENCODE($OUT);
		}
	}
	
	///////////////////////////////////////////////////////
	// COMMENTS CLASS
	///////////////////////////////////////////////////////
	
	CLASS COMMENTS {
		PUBLIC STATIC FUNCTION GET($DATA) {					
			IF(!ISSET($DATA->page)) RETURN '[]';
			IF(!ISSET($DATA->perpage)) RETURN '[]';
				
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
						
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
		
			$RESULT = DB::SELECT('comments', [], 'date', NULL, $LIMIT, [], TRUE);
			$COMMENTS = DB::TOARRAY($RESULT);
						
			//$ROWS = DB::CNT('comments', []);						
			$NUMPAGES = DB::LAST_CNT();
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;					
			$OUT['comments'] = $COMMENTS;		
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION DEL($DATA) {					
			$ERROR = '{"responce": "CMTDELBAD"}';
			$SUCCESS = '{"responce": "CMTDELOK"}';
			
			IF(!ISSET($DATA->id)) RETURN $ERROR;
			
			$DEL[] = $DATA->id;
			DB::DEL('comments', $DEL, 'id');
					
			RETURN $SUCCESS;		
		
		}				
	}
	
	///////////////////////////////////////////////////////
	// EMAILSYSTEM CLASS
	///////////////////////////////////////////////////////
	
	CLASS EMAILSYSTEM {
		PUBLIC STATIC FUNCTION SENDMAIL($DATA) {
			$ERROR = '{"responce": "EMAILERROR"}';
			$SUCCESS = '{"responce": "EMAILOK"}';
			$NOUSERS = '{"responce": "EMAILNOUSERS"}';
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			IF(!ISSET($DATA->content) OR !ISSET($DATA->subject)) RETURN $ERROR;
			
			$SUBJECT = $DATA->subject;
			$CONTENT = $DATA->content;
			$FORCE = $DATA->force;
			$EMAILS = [];
			$USERS = [];
						
			$USERSEL = $DATA->userSelect;
			
			$ATTACH = " AND `notification`=1";
			IF($FORCE === TRUE) $ATTACH = '';
			
			// SELECT USERS
			IF(IS_ARRAY($USERSEL) AND COUNT($USERSEL)) {
				$WHERE = [];
					
				FOREACH($USERSEL AS $U) IF($U->user) $WHERE[] = "`user`='" . $U->user . "'";
								
				$QUERY = "SELECT * FROM `users` WHERE (" . IMPLODE(' OR ', $WHERE) . ") " . $ATTACH . ";";					
				$RESULT = $MYSQLI->query($QUERY);
					
				$USERS = DB::TOARRAY($RESULT);							
			} ELSE IF(ISSET($DATA->filter->grp)) {
				$GRP = $DATA->filter->grp;
				
				$QUERY = "SELECT * FROM `users` WHERE `grp`='" . $GRP . "' " . $ATTACH . ";";					
								
				$RESULT = $MYSQLI->query($QUERY);
					
				$USERS = DB::TOARRAY($RESULT);
			} ELSE {							
				$QUERY = "SELECT * FROM `users` WHERE `user` IS NOT NULL " . $ATTACH . ";";					
					
				$RESULT = $MYSQLI->query($QUERY);
					
				$USERS = DB::TOARRAY($RESULT);
			}
			// // // // // // //
			
			FOREACH($USERS AS $T) $EMAILS[] = $T->user . MAILDOMAIN;
			
			IF(!COUNT($EMAILS)) RETURN $NOUSERS;
			
			RETURN SELF::SEND($SUBJECT, $CONTENT, $EMAILS);
		}
		
		PUBLIC STATIC FUNCTION GETLASTPRODUCT($DAYS, $ID, $CNT) {
			$TIME = TIME() - $DAYS * 86400;
			
			$MYSQLI = $GLOBALS['MYSQLI'];			
			$QUERY = "SELECT * FROM `" . LIBTYPES[$ID] . "` WHERE `date` > " . $TIME . " AND `pending`=0 ORDER BY `date` DESC;";					
			$RESULT = $MYSQLI->query($QUERY);			
			
			IF(!$RESULT) RETURN '';
			
			$PRODUCTS = DB::TOARRAY($RESULT);
			
			IF(!COUNT($PRODUCTS)) RETURN '';
			$ATTACH = '';
			
			IF(!$CNT) $ATTACH .= '<h1>Assets added for the last ' . $DAYS . ' days</h1>';
			
			FOREACH($PRODUCTS AS $PROD) {
				$PREVIEWS = EXPLODE(';', $PROD->previews);
				IF(!COUNT($PREVIEWS)) CONTINUE;
				
				$IMG = PRODUCTS::GETPREVIEWPATH($PREVIEWS[0], IMG_THUMB, TRUE);
				
				$ATTACH .= '<a href="' . HOSTNAME . '#/' . PRODUCTPAGE[1] . '/' . $PROD->id . '">';
				$ATTACH .= '<img src="' . HOSTNAME . 'images/' . $IMG . '" width="' . IMG_THUMB . '">';
				$ATTACH .= '</a>';							
			}
			
			RETURN $ATTACH;
		}
				
		PUBLIC STATIC FUNCTION GETIMG($URL, $CNT) {
			$ATTACH = '';
			IF(!$CNT) $ATTACH .= '<h1>Attached images</h1>';
			$ATTACH .= '<a href="' . $URL . '"><img src="' . $URL . '" width="' . IMG_THUMB . '"></a>';			
			RETURN $ATTACH;
		}
		
		PUBLIC STATIC FUNCTION GETFAVORITE($ID, $CNT) {
			$WHERE['shareid'] = $ID;
			$RESULT = DB::SELECT('favorites', $WHERE);
			
			IF(!$RESULT) RETURN '';
			
			$FAV = $RESULT->fetch_object();
			
			IF(!$FAV) RETURN '';
			
			$ATTACH = '';
			IF(!$CNT) $ATTACH .= '<h1>Shared Collection</h1>';
			$ATTACH .= '
				<table border="0" cellspacing="0" cellpadding="0" class="shared-collection">
					<tr>
						<td><a href="' . HOSTNAME . '#/favorite-share/' . $FAV->shareid . '">' . $FAV->name . ' (' . COUNT(EXPLODE(';', $FAV->products)) . ')</a></td>
						<td>' . $FAV->shareid . '</td>
					</tr>
				</table><br>';
			RETURN $ATTACH;
		}
		
		PUBLIC STATIC FUNCTION ATTACH($CONTENT) {
			$WORDS = [];
			$WORDS[0] = 'lastassets';
			$WORDS[1] = 'img';
			$WORDS[2] = 'favorite';
			
			$ATTACH = '';
			
			FOREACH($WORDS AS $W) {
				$OUT = [];
				PREG_MATCH_ALL('/\[' . $W . '\:.*\]/miU', $CONTENT, $OUT);
				IF(!COUNT($OUT)) CONTINUE;
				
				$LASTASSETS = 0;
				$IMG = 0;
				$FAVORITE = 0;
				
				FOREACH($OUT[0] AS $O) {
					$J = EXPLODE(':', $O);					
					$K = TRIM($J[0], '[');					
					UNSET($J[0]);
					
					$V = TRIM(IMPLODE(':', $J), ']');
					
					SWITCH($K) {				
						CASE $WORDS[0]: 
						{
							$ATTACH .= SELF::GETLASTPRODUCT($V, 1, $LASTASSETS);
							// ADD TEXTURES IN FUTURE;
							//$ATTACH .= SELF::GETLASTPRODUCT($V, 2, $LASTASSETS);
							$LASTASSETS++;
						}
						BREAK;
						BREAK;
						CASE $WORDS[1]: {
							$ATTACH .= SELF::GETIMG($V, $IMG);
							$IMG++;
						}
						BREAK;
						CASE $WORDS[2]: {
							$ATTACH .= SELF::GETFAVORITE($V, $FAVORITE);
							$FAVORITE++;
						}
						BREAK;
					}
				}
			}
			
			RETURN $ATTACH;
		}
		
		PUBLIC STATIC FUNCTION SEND($SUBJECT, $CONTENT, $USERS) {
			
			$ERROR = '{"responce": "EMAILERROR"}';
			$SUCCESS = '{"responce": "EMAILOK"}';
							
			$HEADERS = [];
			$HEADERS[] = "MIME-Version: 1.0";
			$HEADERS[] = "Content-type: text/html; charset=iso-8859-1";
			$HEADERS[] = "From: visco-assets.noreply@visco.no";
			$HEADERS[] = "Reply-To: " . IMPLODE(',', $USERS); 
			$HEADERS[] = "Subject: " . $SUBJECT;
			$HEADERS[] = "X-Mailer: PHP/" . PHPVERSION();
				
			
			$ATTACH = SELF::ATTACH($CONTENT);
			
			// REMOVE ALL IN BRACKETS
			$CONTENT = PREG_REPLACE('/\[[a-z]*\:.*\]/miU', '', $CONTENT);
			$CONTENT = TRIM($CONTENT);
			// REPLACE BRAKE LINE TO BR
			$CONTENT = PREG_REPLACE('/\\n/m', '<br />', $CONTENT);
					
			$BODY = '<!doctype html>
				<html>
				<head>
				<meta charset="utf-8">
				<title>Untitled Document</title>
				<style>
				body, html {
					padding: 0;
					margin: 0;
					font-family: Segoe, "Segoe UI", "DejaVu Sans", "Trebuchet MS", Verdana, sans-serif;
					color: #777;
					background: #F5F5F5
				}
				td {
					padding: 20px;
					background-color: #FFF;
				}
				.title-head {
					color: #888;
					font-size: 24px;
					margin: 20px;
					vertical-align: middle;
					font-weight: 100;
					display: inline-block;
				}
				img {
					vertical-align: middle;
					margin: 10px;
					outline: none;
				}
				.footer {
					background : #F5F5F5;
					color: #888;
					font-size: 11px;
					font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
					line-height: 16px;
				}
				a, a:visited {
					color: #8EC961;
					text-decoration: underline;
					outline: none;
				}
				a:hover {
					color: #66A338;
				}
				.attach, .attach td {
					padding: 0;
				}
				h1 {
					font-weight: 100;
					font-size: 24px;
				}
				.text-center {
					text-align: center;
				}
				.show-border {
					border-top: 5px solid #888;	
				}
				.shared-collection {
					border-collapse: collapse;
				}
				.shared-collection td {
					border: 1px solid #DADADA;
					padding: 12px;
				}
				</style>
				</head>

				<body>
				<br>
				<br>
				<div style="display: none">Please do not reply to this message</div>
				<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center" class="show-border">
					<tr valign="middle">
						<td><table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="middle" style="padding: 0"><img style="margin: 0" src="' . HOSTNAME . 'visco_logo.png" alt="logo" height="44px"></td>
							<td valign="middle" style="padding: 0; padding-left: 20px;"><span class="title-head">Assets Library</span></td>
						</tr>
				</table>

						</td>
					</tr>
					<tr>
						<td>
				' . $CONTENT .  '<br>' . $ATTACH . '</td>
				</tr>
				</table>
				<br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0" class="footer" align="center" style="background: #F5F5F5; color: #888; font-size: 11px; font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif; line-height: 16px;"><tbody><tr>
				<td style="padding: 20px; background-color: #FFF;">&copy; ' . DATE('Y') . ' - VISCO. ALL RIGHTS RESERVED.</td>
				   <td align="right" style="padding: 20px; background-color: #FFF;">
				<a href="' . HOSTNAME . '/#/profile/profile" style="color: #8EC961; text-decoration: underline;">Unsubscribe</a> of this newsletter instantly.</td>
				  </tr></tbody></table>

				</td></tr></table>
				</body>
				</html>
			';
			
			$MESSAGE = WORDWRAP($BODY, 70, "\r\n");
			$SEND = MAIL(IMPLODE(',', $USERS), $SUBJECT , $MESSAGE, IMPLODE("\r\n", $HEADERS)); 
			IF(!$SEND) RETURN $ERROR;
			RETURN $SUCCESS;						
		}
	}

	///////////////////////////////////////////////////////
	// MISSING CLASS
	///////////////////////////////////////////////////////
	
	CLASS MISSING {
		
		PUBLIC STATIC FUNCTION COMPARE_LISTS($LIST1, $LIST2) {
			
			$MISSING = [];
			
			FOREACH($LIST1 AS $L1) {
				$FOUND = FALSE;
				
				FOREACH($LIST2 AS $L2) {
					IF(STRIPOS($L1, $L2) !== FALSE) {						
						$FOUND = $L2;
						BREAK;
					}
				}
				
				IF(!$FOUND) {	
					$MISSING[] = $L1;
				}
			}
			
			RETURN $MISSING;
		}
		
		PUBLIC STATIC FUNCTION DEL($DATA) {
			$ERROR = '{"responce": "MISSINGDELBAD"}';
			$SUCCESS = '{"responce": "MISSINGDELOK"}';
			
			IF(!ISSET($DATA->path)) RETURN $ERROR;
			$GLOBS = GLOBS::PARSE();
			IF(STRIPOS($DATA->path, $GLOBS->path) === FALSE) RETURN $ERROR;

			IF(FS::ISDIREMPTY($DATA->path)) RETURN $ERROR;
			
			$MAX = GLOB($DATA->path . '*.max');
			
			IF(!COUNT($MAX)) RETURN $ERROR;
			
			$WHERE['status'] = 1;
			$RESULT = DB::SELECTUNIQUE('render', 'models', $WHERE);
			
			$RENDERS = [];
			
			FOREACH(DB::TOARRAY($RESULT) AS $ROW) IF(STRLEN($ROW->render)) $RENDERS[] = $ROW->render;
						
			$P = EXPLODE('\\', $DATA->path);
			
			$RENDER = (END(ARRAY_FILTER($P)));
			
			IF(IN_ARRAY($RENDER, $RENDERS)) {
				IF(!END($P)) ARRAY_POP($P);				
				ARRAY_POP($P);
				
				$UP = IMPLODE('\\', $P) . '\\';
				
				IF(COUNT(SCANDIR($UP)) == 3) $DATA->path = IMPLODE('\\', $P) . '\\';								
			}
					
			FS::CLEAR($DATA->path);
			FS::DELDIR($DATA->path);
					
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION FIND($DATA) {
			$ERROR = '{"responce": "MISSINGFINDBAD"}';
			
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT('category');
			$TMP = DB::TOARRAY($RESULT);
			
			$CATEGORIES = CAT::ASSOC_CAT($TMP, 'id');
						
			$ROW = $CATEGORIES[$DATA->id];
						
			IF(!$ROW) RETURN $ERROR;
			
			$GLOBS = GLOBS::PARSE();
			$PATH = $GLOBS->path . $ROW->path . '\\';
						
			
			$LIST1 = FS::GET_LIST_BY_EXT($PATH, 'max');
						
			
			$IDS = [];
			$IDS = ACCESS::EXTRACTIDS($CATEGORIES, $DATA->id, $IDS);
			
			$WHERE = [];
			$WHERE['catid'] = $IDS;
			$RESULT = DB::SELECT('models', $WHERE);
			
			$PRUDUCTS = DB::TOARRAY($RESULT);
			
			$LIST2 = [];
			
			FOREACH($PRUDUCTS AS $PROD) {
				
				$C = $CATEGORIES[$PROD->catid];
				$C = CAT::CLEAR($C->name);
				$LIST2[] = '\\' . $C . '\\' . CAT::CLEAR($PROD->name) . '\\' . $PROD->render . '\\';				
			}
			
			
			$MISSING = SELF::COMPARE_LISTS($LIST1, $LIST2);
			
			//print_r($LIST2);
			//print_r($LIST1);
			$OUT['missing'] = $MISSING;
			$OUT['missing_count'] = COUNT($MISSING);			
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION DELPREVIEW($DATA) {
			$ERROR = '{"responce": "MISSDELBAD"}';
			$SUCCESS = '{"responce": "MISSDELOK"}';
			
			IF(!ISSET($DATA->miss) OR !IS_ARRAY($DATA->miss)) RETURN $ERROR;
			
			FOREACH($DATA->miss AS $MISS) {								
				FS::DEL(IMG_PATH . $MISS);
			}
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION PREVIEW($DATA) {
			$ERROR = '{"responce": "MISSINGFINDBAD"}';
									
			$LIST1 = [];				
			$LIST1 = FS::GET_LIST_BY_EXT(IMG_PATH, 'jpg', 'file');
						
			$RESULT = DB::SELECT('models');			
			$PRUDUCTS = DB::TOARRAY($RESULT);
			
			$LIST2 = [];
			
			FOREACH($PRUDUCTS AS $PROD) {				
				$PREVIEWS = DB::PARSE_VALUE($PROD->previews);				
				$LIST2 = ARRAY_MERGE($LIST2, $PREVIEWS);
			}
			
								
			$MISSING = SELF::COMPARE_LISTS($LIST1, $LIST2);
			
			$MISSING_IMG = [];
			FOREACH($MISSING AS $M) {
				$MISSING_IMG[] = PATHINFO($M, PATHINFO_FILENAME) . '.' . PATHINFO($M, PATHINFO_EXTENSION);
			}
			
			//print_r($LIST2);
			//print_r($LIST1);
			$OUT['missing'] = $MISSING_IMG;		
			$OUT['missing_count'] = COUNT($MISSING);			
			
			RETURN JSON_ENCODE($OUT);
		}
	}
	
	///////////////////////////////////////////////////////
	// MSGSYSTEM CLASS
	///////////////////////////////////////////////////////
	
	CLASS MSGSYSTEM {
		PUBLIC STATIC FUNCTION GETCNT() {
					
			$ROWS = DB::CNT('msg', ['viewed' => 0]);	
			
			$OUT['cnt'] = $ROWS;
			
			RETURN JSON_ENCODE($OUT);
		}

		PUBLIC STATIC FUNCTION GET($DATA) {
							
			$ERROR = '{"responce": "MSGBAD"}';
			$WHERE = [];
				
			// GET USER
			$AUTH = $GLOBALS['AUTH']['user'];
								
			IF(!ISSET($DATA->page)) RETURN '[]';
			IF(!ISSET($DATA->perpage)) RETURN '[]';
						
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
						
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
		
			$RESULT = DB::SELECT('msg', $WHERE, 'date', TRUE, $LIMIT, [], TRUE);
			$MESSAGES = DB::TOARRAY($RESULT);
												
			$NUMPAGES = DB::LAST_CNT();
			
			$ROWS = DB::CNT('msg', ['viewed' => 0]);						
			$NOTVIEW = $ROWS;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			$OUT['notview'] = $NOTVIEW;						
			$OUT['messages'] = $MESSAGES;
			
						
			RETURN JSON_ENCODE($OUT);
		}

		PUBLIC STATIC FUNCTION SETPARAM($DATA) {
			$ERROR = '{"responce": "SETTINGBAD"}';
			$SUCCESS = '{"responce": "SETTINGOK"}';
			
			IF(!ISSET($DATA->param) OR !ISSET($DATA->value) OR !ISSET($DATA->id)) RETURN $ERROR;
					
			$SET[$DATA->param] = $DATA->value;
			$WHERE['id'] = $DATA->id;
			
			$RESULT = DB::UPDATE('msg', $SET, $WHERE);
			
			IF($RESULT > 0) RETURN $SUCCESS;
			RETURN $ERROR ;
		}
		
		PUBLIC STATIC FUNCTION DEL($DATA) {			
			$ERROR = '{"responce": "MSGDELBAD"}';
			$SUCCESS = '{"responce": "MSGDELOK"}';
							
			IF(!ISSET($DATA->id)) RETURN $ERROR;
					
			$DEL[] = $DATA->id;
						
			$RESULT = DB::DEL('msg', $DEL, 'id');
			
			IF($RESULT > 0) RETURN $SUCCESS; 						
			RETURN $ERROR;
		}
	}
?>