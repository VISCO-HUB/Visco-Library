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
		
		PUBLIC STATIC FUNCTION SELECT($TABLE, $WHEREOR = [], $WHEREAND = [], $SORT = NULL, $LIMIT = NULL) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
						
			$ATTACHWHERE = '';
			
			$WOR = SELF::GETWHERE($WHEREOR);
			$WAND = SELF::GETWHERE($WHEREAND);
						
			IF(COUNT($WOR) OR COUNT($WAND)) $ATTACHWHERE = " WHERE ";
			IF(COUNT($WOR)) $ATTACHWHERE .= '(' . IMPLODE(' OR ', $WOR) . ')';
			IF(COUNT($WOR) AND COUNT($WAND)) $ATTACHWHERE .= " AND ";
			IF(COUNT($WAND)) $ATTACHWHERE .= '(' . IMPLODE(' AND ', $WAND) . ')';
			
			$ATTACHSORT = '';
			IF($SORT) $ATTACHSORT = ' ORDER BY ' . SELF::STRIP($SORT);
			
			$ATTACHLIMIT = '';
			IF($LIMIT) $ATTACHLIMIT = ' LIMIT ' . SELF::STRIP($LIMIT['start']) . ", " . SELF::STRIP($LIMIT['end']);
			
			$QUERY = "SELECT * FROM " . $TABLE . " " . $ATTACHWHERE . " " . $ATTACHSORT . " " . $ATTACHLIMIT . ";";			
		 
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION SELECTUNIQUE($COL, $TABLE) {
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$QUERY = "SELECT DISTINCT " . SELF::STRIP($COL) . " FROM " . $TABLE . ";";
			
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION CNT($TABLE, $WHEREOR = [], $WHEREAND = []) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$ATTACHWHERE = '';
			
			$WOR = SELF::GETWHERE($WHEREOR);
			$WAND = SELF::GETWHERE($WHEREAND);
						
			IF(COUNT($WOR) OR COUNT($WAND)) $ATTACHWHERE = " WHERE ";
			IF(COUNT($WOR)) $ATTACHWHERE .= '(' . IMPLODE(' OR ', $WOR) . ')';
			IF(COUNT($WOR) AND COUNT($WAND)) $ATTACHWHERE .= " AND ";
			IF(COUNT($WAND)) $ATTACHWHERE .= '(' . IMPLODE(' AND ', $WAND) . ')';	
			
			$QUERY = "SELECT COUNT(*) AS cnt FROM " . $TABLE . ' ' . $ATTACHWHERE .";";			
			
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
		
		PUBLIC STATIC FUNCTION TRUSTUSER($USER) {			
			$SUCCESS = 'TRUSTOK';
			$ERROR =  '';
			
			IF(!ISSET($USER)) {
				ECHO $ERROR;
				RETURN FALSE;
			}
			
			$WHERE['user'] = $USER;					
			$RESULT = DB::SELECT('users', $WHERE);			
				
			$ROWS = MYSQLI_NUM_ROWS($RESULT);

			IF($ROWS != 1) {
				ECHO $ERROR;
				RETURN FALSE;
			}
			
			$ROW = $RESULT->fetch_object();
				
			$_SESSION['browser'] = 'MXS';
			$_SESSION['token'] = $ROW->token;
			
			ECHO $SUCCESS;
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
			$AUTH['user']->browser = $_SESSION['browser'];
			
			RETURN $AUTH;
		}
		
		PUBLIC STATIC FUNCTION SIGNOUT() {
			SESSION_START();
			$_SESSION['token'] = '';
			$_SESSION['browser'] = '';
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
		
		PUBLIC STATIC FUNCTION PARSE() {			
			RETURN JSON_DECODE($GLOBALS['GLOBS']);
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
			$AUTH = $GLOBALS['AUTH'];	
						
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $PARENT) {
					
					// Check rights!
					$PREM = ARRAY_FILTER(EXPLODE(';', $CATEGORY->premissions));					
					IF(COUNT($PREM) AND !IN_ARRAY($AUTH['user']->grp, $PREM ) AND !$AUTH['user']->rights > 0) CONTINUE;
					
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
			$RESULT = DB::SELECT('category', [], [], 'sort');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$RESULT = SELF::BUILDTREE($CATEGORIES, 0);
			
			RETURN JSON_ENCODE($RESULT);
		}
		

		PUBLIC STATIC FUNCTION GETSUBIDS($ID, $CATEGORIES = []) {
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
					
					IF($FLAG) $P .= SELF::GETSUBIDS($CATEGORY->id, $CATEGORIES);
				}
			}
			
			RETURN $P;
		}
		
		PUBLIC STATIC FUNCTION GETPATHWAY($ID, $CATEGORIES = [], &$PATHWAY = []) {
						
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->id == $ID) {
								
					$T['id'] = $CATEGORY->id;
					$T['name'] = $CATEGORY->name;
					$PATHWAY[] = $T;
										
					FOREACH($CATEGORIES AS $SUBCAT) {
						IF($SUBCAT->id == $CATEGORY->parent)
						{
							$FLAG = TRUE;
							BREAK;
						}
					}
					
					IF($FLAG) $PATHWAY = SELF::GETPATHWAY($CATEGORY->parent, $CATEGORIES, $PATHWAY);
				}
			}
			
			RETURN $PATHWAY;
		}
		
		PUBLIC STATIC FUNCTION GETCATTYPE($ID, $CATEGORIES = []) {
						
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->id == $ID) {
					RETURN $CATEGORY->type;
				}
			}
			
			RETURN -1;
		}
	}
	
	CLASS PREVIEW {		
		PUBLIC STATIC FUNCTION GETPREVIEWPATH($NAME, $SIZE) {
			RETURN 'images/' . $NAME . '_' . $SIZE . 'x' . $SIZE . '.jpg';
		}
	}
	
	///////////////////////////////////////////////////////
	// PRODUCTS CLASS
	///////////////////////////////////////////////////////
	
	CLASS PRODUCTS {
		
		PUBLIC STATIC FUNCTION TYPE($T) {				
			IF($T > 0 AND $T <= COUNT(LIBTYPES)) RETURN LIBTYPES[$T];
			RETURN NULL;
		}
		
		PUBLIC STATIC FUNCTION GET($DATA) {
			$ERROR = '{"responce": "PRODBAD"}';
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			IF(!ISSET($DATA->page)) RETURN '[]';
			IF(!ISSET($DATA->perpage)) RETURN '[]';
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN '[]';
			
			$RESULT = DB::SELECT('category', [], [], 'sort');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$SUBIDS = CAT::GETSUBIDS($DATA->id, $CATEGORIES) . $DATA->id;
			$SUBIDS = ARRAY_FILTER(EXPLODE(';', $SUBIDS));
						
			$PATHWAY = CAT::GETPATHWAY($DATA->id, $CATEGORIES);			
			$PATHWAY = ARRAY_REVERSE($PATHWAY);
						
			$TYPE = CAT::GETCATTYPE($DATA->id, $CATEGORIES);
			$TYPE = SELF::TYPE($TYPE);
			IF(!$TYPE) RETURN '[]';
			
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
			
			$WHEREAND = [];
			IF($AUTH->rights < 1) {
				$WHEREAND['status'] = 1;
				$WHEREAND['pending'] = 0;
			}
			
			$WHEREOR['catid'] = $SUBIDS;
						
			$RESULT = DB::SELECT($TYPE, $WHEREOR, $WHEREAND, 'date', $LIMIT);			
			$PRODUCTS = DB::TOARRAY($RESULT);
			
			$ROWS = DB::CNT($TYPE, $WHEREOR, $WHEREAND);						
			$NUMPAGES = $ROWS;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			
			$OUT['products'] = $PRODUCTS;
			$OUT['pathway'] = $PATHWAY;
						
			RETURN JSON_ENCODE($OUT);
		}
	}
	
		
	///////////////////////////////////////////////////////
	// MXS CLASS
	///////////////////////////////////////////////////////
	
	CLASS MXS {
		PUBLIC STATIC FUNCTION ADDMODEL($DATA) {
			$ERROR = '{"responce": "MODELBAD"}';
			$NOTEXIST = '{"responce": "MODELNOTEXIST"}';
			
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT('models', $WHERE);
			
			$ROWS = MYSQLI_NUM_ROWS($RESULT);
			
			IF($ROWS != 1) {
				ECHO $ERROR;
				RETURN FALSE;
			}
						
			$RESULT = DB::TOARRAY($RESULT);	
			$MODEL = $RESULT[0];
			$PATH = CAT::BUILDPATH($MODEL->catid) ;		
			IF(!COUNT($PATH)) RETURN $ERROR;
			$PATH .= CAT::CLEAR($MODEL->name) . '\\' . CAT::CLEAR($MODEL->render)  . '\\';
			
			$FILES = (GLOB($PATH . '*.max'));
			$FILE = $FILES[0];
			IF(!$FILE) RETURN $NOTEXIST;
			
			
			$SET['downloads'] = $MODEL->downloads + 1;
			DB::UPDATE('models', $SET, $WHERE);
						
			$OUT['responce'] = "MODELOK";
			$OUT['file'] = $FILE;
			
			RETURN JSON_ENCODE($OUT);					
		}
	}
	
	///////////////////////////////////////////////////////
	// HOME CLASS
	///////////////////////////////////////////////////////
	
	CLASS HOME {
		PUBLIC STATIC FUNCTION EXTRACTIDS($CATEGORIES, $PARENT) {
			$IDS = [];			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $PARENT) {													
				
					$IDS[] = $CATEGORY->id;
				}
			}
			
			RETURN $IDS;
		}
		
		PUBLIC STATIC FUNCTION GETTHUMBS($PRODUCTS) {
			$THUMBS = []	;
			FOREACH($PRODUCTS AS $PROD) {			
				$P = EXPLODE(';', $PROD->previews)[0];
				 $THUMBS[] = PREVIEW::GETPREVIEWPATH($P, IMG_THUMB);
			}
			
			RETURN $THUMBS;
		}
		
		PUBLIC STATIC FUNCTION GET($DATA) {
			
			IF(!ISSET($DATA->type))RETURN '{}';
			$TYPE = PRODUCTS::TYPE($DATA->type);
			IF(!$TYPE) RETURN '{}';
			
			$WHERE['status'] = 1;
			$RESULT = DB::SELECT('category', $WHERE,[], 'sort');
			$CATEGORIES = DB::TOARRAY($RESULT);
			$IDS = [];
			$OUT = [];
			$LIMIT['start'] = 0;
			$LIMIT['end'] = 5;
						
			FOREACH($CATEGORIES AS $CAT) {															
				IF($CAT->parent == 0) {
					FOREACH($CATEGORIES AS $C) {
						IF($C->parent == $CAT->id) {							
							$IDS[$CAT->name]['id'] = $CAT->id;
							$IDS[$CAT->name]['type'] = $CAT->type;
							$IDS[$CAT->name]['sub'][$C->name]['id'] = $C->id;
							$IDS[$CAT->name]['sub'][$C->name]['ids'] = SELF::EXTRACTIDS($CATEGORIES, $C->id);
						}
					}
				}
			}
						
			FOREACH($IDS AS $K => $V) {			
				FOREACH($V['sub'] AS $K2 => $V2) {
					$WHERE = [];
					
					FOREACH($V2['ids'] AS $CATID) $WHERE['catid'][] = $CATID;
					
					$RESULT = DB::SELECT($TYPE, $WHERE, [], 'date', $LIMIT);
					$RESULT = DB::TOARRAY($RESULT);	
										
					$OUT[$K]['id'] = $V['id'];
					$OUT[$K]['type'] = $V['type'];
					$OUT[$K]['name'] = $K;
					$OUT[$K][$K2]['id'] = $V2['id'];
					$OUT[$K][$K2]['name'] = $K2;
					$OUT[$K][$K2]['previews'] = SELF::GETTHUMBS($RESULT);
				};												
			}
			
			ECHO JSON_ENCODE($OUT);	
		}
	}	
?>