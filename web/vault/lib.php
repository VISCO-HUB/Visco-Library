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
		
		PUBLIC STATIC FUNCTION CONNECT() {					
			RETURN NEW MYSQLI(MYSQL_SERVER, MYSQL_USER, MYSQL_PWD, MYSQL_DB);	
		}
		
		PUBLIC STATIC FUNCTION CLOSE() {					
			$MYSQLI = $GLOBALS['MYSQLI'];
			$MYSQLI->CLOSE();	
		}
			
		PUBLIC STATIC FUNCTION SELECT($TABLE, $WHERE = [], $SORT = NULL, $AND = NULL) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
						
			$W = [];
			FOREACH($WHERE AS $KEY => $VALUE) {							
				$VALUE = SELF::STRIP($VALUE);							
				$KEY = SELF::STRIP($KEY);
				
				$W[] = $KEY . "=" . "'" . $VALUE . "'";
			}
			$ATTACH = '';
			$BOOL = $AND ? 'AND' : 'OR';
			IF(COUNT($W)) $ATTACH = " WHERE " . IMPLODE(' ' . $BOOL . ' ', $W);
	
			$ATTACHSORT = '';
			IF($SORT) $ATTACHSORT = ' ORDER BY ' . SELF::STRIP($SORT);
			
			$QUERY = "SELECT * FROM " . $TABLE . $ATTACH . $ATTACHSORT . ";";			
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
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
	}
	
	///////////////////////////////////////////////////////
	// AUTH CLASS
	///////////////////////////////////////////////////////
	
	CLASS AUTH {
		PUBLIC STATIC FUNCTION TOKEN($USER, $PW) {
			RETURN MD5(AUTH_SALT . $USER . $PW . time());
		}
		
		PUBLIC STATIC FUNCTION SIGNIN($DATA) {						
			$ERROR = '{"responce": "USERBAD"}';
			$SUCCESS = '{"responce": "USEROK"}';
		
			IF(!ISSET($DATA->user) OR !ISSET($DATA->pwd)) RETURN $ERROR;
			
			$USER = $DATA->user . '@' . AUTH_DOMAIN;
			
			$TOKEN = SELF::TOKEN($USER, $DATA->pwd);
			
			$LDAP = LDAP_CONNECT(AUTH_SERVER);
			IF(!@LDAP_BIND($LDAP, $USER, $DATA->pwd)) RETURN $ERROR; 
						
			$_SESSION['token'] = $TOKEN;
			
			$SET = [];
			$WHERE = [];

			$SET['user'] = $WHERE['user'] = $DATA->user;
			$SET['token'] = $TOKEN;
					
			DB::INSERT('users', $SET);						
			DB::UPDATE('users', $SET, $WHERE);
			
			RETURN $SUCCESS;			
		}
		
		PUBLIC STATIC FUNCTION CHECK() {							
			
			IF(!ISSET($_SESSION['token'])) RETURN [FALSE];
			$WHERE = [];
			$WHERE['token'] = $_SESSION['token'];
			$RESULT = DB::SELECT('users', $WHERE);
			
			$ROWS = MYSQLI_NUM_ROWS($RESULT);
			
			IF($ROWS != 1) RETURN [FALSE];			
			$ROW = $RESULT->fetch_object();
			
			$AUTH = [];
			$AUTH['exist'] = TRUE;
			$AUTH['user'] = $ROW;
			
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
				ECHO '{{setAuth(' . JSON_ENCODE($AUTH['user']) . ')}}';				
			}
			ELSE {
				ECHO '{{goLogin()}}';				
				EXIT;
			}
		}
		
		PUBLIC STATIC FUNCTION ADMIN() {
			SESSION_START();
			
			$GLOBALS['MYSQLI'] = DB::CONNECT();
			$AUTH = SELF::CHECK();
								
			IF($AUTH['exist'] == TRUE AND $AUTH['user']->status == 1 AND $AUTH['user']->rights > 0) {
				UNSET($AUTH['user']->token);				
				ECHO '{{setAuth(' . JSON_ENCODE($AUTH['user']) . ')}}';				
			}
			ELSE {
				ECHO '{{goHome()}}';				
				EXIT;
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
				
		PUBLIC STATIC FUNCTION ISDIREMPTY($DIR) {
			IF (!IS_READABLE($DIR)) RETURN NULL; 
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
	}
	
	///////////////////////////////////////////////////////
	// CATEGORIES CLASS
	///////////////////////////////////////////////////////

	CLASS CAT {
		PUBLIC STATIC FUNCTION CLEAR($S) {
			$S = STR_REPLACE(' ', '-', $S); 
			$S = PREG_REPLACE('/[^A-Za-z0-9\-]/', '', $S); 
			RETURN $S;
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
		
		PUBLIC STATIC FUNCTION BUILDTREE($CATEGORIES, $PARENT = 0) {
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
			
			$RESULT = SELF::BUILDTREE($CATEGORIES, 0);
			
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
?>