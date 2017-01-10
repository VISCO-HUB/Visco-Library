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
			
		PUBLIC STATIC FUNCTION SELECT($TABLE, $WHERE = [], $SORT = NULL, $AND = NULL, $LIMIT = NULL) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
						
			$W = [];
			FOREACH($WHERE AS $KEY => $VALUE) {							
				$VALUE = SELF::STRIP($VALUE);							
				$KEY = SELF::STRIP($KEY);
				
				$W[] = $KEY . "=" . "'" . $VALUE . "'";
			}
			$ATTACHWHERE = '';
			$BOOL = $AND ? 'AND' : 'OR';
			IF(COUNT($W)) $ATTACHWHERE = " WHERE " . IMPLODE(' ' . $BOOL . ' ', $W);
	
			$ATTACHSORT = '';
			IF($SORT) $ATTACHSORT = ' ORDER BY ' . SELF::STRIP($SORT);
			
			$ATTACHLIMIT = '';
			IF($LIMIT) $ATTACHLIMIT = ' LIMIT ' . SELF::STRIP($LIMIT['start']) . ", " . SELF::STRIP($LIMIT['end']);
			
			$QUERY = "SELECT * FROM " . $TABLE . $ATTACHWHERE . $ATTACHSORT . $ATTACHLIMIT . ";";			
		
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION SELECTUNIQUE($COL, $TABLE) {
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$QUERY = "SELECT DISTINCT " . SELF::STRIP($COL) . " FROM " . $TABLE . ";";
			
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION CNT($TABLE, $WHERE = [], $AND = NULL) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$W = [];
			FOREACH($WHERE AS $KEY => $VALUE) {							
				$VALUE = SELF::STRIP($VALUE);							
				$KEY = SELF::STRIP($KEY);
				
				$W[] = $KEY . "=" . "'" . $VALUE . "'";
			}
			
			$ATTACHWHERE = '';
			$BOOL = $AND ? 'AND' : 'OR';
			IF(COUNT($W)) $ATTACHWHERE = " WHERE " . IMPLODE(' ' . $BOOL . ' ', $W);	
			
			$QUERY = "SELECT COUNT(*) AS cnt FROM " . $TABLE . $ATTACHWHERE .";";			
			
			$RESULT = $MYSQLI->query($QUERY);
			$ROW = mysqli_fetch_assoc($RESULT);
		
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
	// AUTH CLASS
	///////////////////////////////////////////////////////
	
	CLASS AUTH {
		PUBLIC STATIC FUNCTION TOKEN($USER, $PW) {
			RETURN MD5(AUTH_SALT . $USER . $PW . time());
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
				
		
		PUBLIC STATIC FUNCTION GET($DATA) {			
			IF(!ISSET($DATA->parentid)) RETURN '[]';
		
			$WHERE['parent'] = $DATA->parentid;
		
			$RESULT = DB::SELECT('category', $WHERE);
			$RESULT = DB::TOARRAY($RESULT);
			
			RETURN JSON_ENCODE($RESULT);
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
					$I['editors'] = $CATEGORY->editors;
										
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
						
	}
	
	CLASS PREVIEW {		
		PUBLIC $BASE_SIZE = IMG_SIZE - (IMG_PADDING * 2);  
		PUBLIC $OUT_SIZE = IMG_SIZE;		
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
			
			FOREACH($TEMP AS $K => $V) {
				IF($V == 1 AND !$START) {
					$INTERVALS[$PARTS]['start'] = $K;
					$START = TRUE;
				}
				
				IF($V == 0 AND $START) {
					$INTERVALS[$PARTS]['end'] = $K - 1;
					$START = FALSE;
					$PARTS++;
				}
			}
			
			$W = 1;
			$S = 1;
			
			FOREACH($INTERVALS AS $K => $V) {
				IF($V['end'] - $V['start'] > 20) {
					$W = $V['end'] - $V['start'];
					$S = $V['start'];
				}
			}
			
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
					
					$BIN[$I][$J] = ($R > $BG - $SENS) ? 0 : 1;
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
	
?>