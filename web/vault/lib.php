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
		
		PUBLIC STATIC FUNCTION SELECT($TABLE, $WHEREOR = [], $WHEREAND = [], $SORT = NULL, $LIMIT = NULL, $REVERSE=NULL) {		
			
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
			
			$QUERY = "SELECT * FROM " . $TABLE . " " . $ATTACHWHERE . " " . $ATTACHSORT . " " . ($REVERSE ? 'DESC' : '') . " " . $ATTACHLIMIT . ";";			
		 
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION SELECTUNIQUE($COL, $TABLE) {
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$QUERY = "SELECT DISTINCT " . SELF::STRIP($COL) . " FROM " . $TABLE . ";";
			
			$RESULT = $MYSQLI->query($QUERY);
		
			RETURN $RESULT;
		}
		
		PUBLIC STATIC FUNCTION SEARCH($TABLE, $FIND = [], $COL = [], $FILTER = NULL, $LIMIT = NULL, $CNT = NULL) {
			
			$MYSQLI = $GLOBALS['MYSQLI'];
				
			// FILTER 
			$ATTACH_FILTER_CAT = '';
			IF(ISSET($FILTER['cat']['ids']) AND COUNT($FILTER['cat']['ids'])) {
				$F_CAT = [];
				FOREACH($FILTER['cat']['ids'] AS $V) $F_CAT[] = "catid='" . $V . "'";
				$F_CAT = IMPLODE(' OR ', $F_CAT);
				IF(COUNT($F_CAT)) $ATTACH_FILTER_CAT = " (" . $F_CAT . ") AND ";
			}
			
			$F = '';
			
			$SPECIALS = ['-', '"', '+', '~', '*'];
			FOREACH($FIND AS $V) {				
				$PREFIX = IN_ARRAY($V[0], $SPECIALS) ? $V[0] : '+';
				$POSTFIX = '* ';
				
				//$V = STR_REPLACE($SPECIALS, '', $V);
				$F .= $PREFIX . SELF::STRIP($V) . $POSTFIX;
			}
			
			$M = [];
			$PRIORITY = 0;
			FOREACH($COL AS $V) {
				$P = COUNT($COL) - $PRIORITY;
				$M[] = $V . '_match * ' . ($P ? $P : '');
				$PRIORITY++;
			}
			
			$MATCH = [];
			FOREACH($COL AS $V){								
				$VAL = SELF::STRIP($V);
				$MATCH [] = "MATCH (`" . $VAL . "`) AGAINST ('" . $F . "' IN BOOLEAN MODE) AS " . $VAL . "_match";
			}
				
			$ATTACHLIMIT = '';
			IF($LIMIT) $ATTACHLIMIT = ' LIMIT ' . SELF::STRIP($LIMIT['start']) . ", " . SELF::STRIP($LIMIT['end']);
			
			$SEL = $CNT ? "SELECT COUNT(*) AS cnt " : "SELECT * ";			
			$QUERY = $SEL . " ," . IMPLODE(',', $MATCH) . " FROM `" . $TABLE . "` WHERE " . $ATTACH_FILTER_CAT . " MATCH (" . IMPLODE(',', $COL) . ") AGAINST ('" . $F . "' IN BOOLEAN MODE) ORDER BY (" . IMPLODE(' + ', $M) . ") DESC " . $ATTACHLIMIT . " ;";
						
			$RESULT = $MYSQLI->query($QUERY);
						
			IF($CNT) {
				$ROW = MYSQLI_FETCH_ASSOC($RESULT);
				RETURN $ROW['cnt'];				
			}
			
			RETURN $RESULT;
		}
		
		
		PUBLIC STATIC FUNCTION SELECTLIKE($TABLE, $COL = [], $FIND = [], $LIMIT = NULL, $WHERE = NULL, $CNT = NULL, $FILTER = NULL) {		
			
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$S = '';
			$I = 1;
			
			FOREACH($FIND AS $F){
				$AND = FALSE;
				IF($I > 1) $S .= ' AND ';
				$S .= "(";
				$II = 1;
				FOREACH($COL AS $C) {
					IF($II > 1) $S .= ' OR ';
					
					$S .= "(`" . SELF::STRIP($C) . "` LIKE '%" . SELF::STRIP($F) ."%')";
					$II++;
				}
				$S .= ")";
				$I++;
			}
						
			$ATTACHLIMIT = '';
			IF($LIMIT) $ATTACHLIMIT = ' LIMIT ' . SELF::STRIP($LIMIT['start']) . ", " . SELF::STRIP($LIMIT['end']);
			
			$ATTACHWAND = '';
			IF($WHERE) {
				FOREACH($WHERE AS $K => $V){
					$ATTACHWAND .= ' AND ' . SELF::STRIP($K) . '=' . "'" . $V . "'";
				}
			}
			
			// FILTER CAT
			$ATTACH_FILTER_CAT = '';
			IF(ISSET($FILTER['cat']['ids']) AND COUNT($FILTER['cat']['ids'])) {
				$F_CAT = [];
				FOREACH($FILTER['cat']['ids'] AS $V) $F_CAT[] = "catid='" . $V . "'";
				$F_CAT = IMPLODE(' OR ', $F_CAT);
				IF(COUNT($F_CAT)) $ATTACH_FILTER_CAT = " AND (" . $F_CAT . ")";
			}
							
			$SEL = $CNT ? "SELECT COUNT(*) AS cnt " : "SELECT * ";
			$QUERY = $SEL . " FROM `" . $TABLE . "` WHERE (" . $S . ")" . $ATTACHWAND . $ATTACH_FILTER_CAT . $ATTACHLIMIT  . ";";			
				
			$RESULT = $MYSQLI->query($QUERY);

			IF($CNT) {
				$ROW = MYSQLI_FETCH_ASSOC($RESULT);
				RETURN $ROW['cnt'];
			}
			
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
		PUBLIC STATIC FUNCTION INSERT($TABLE, $DATA, $TRUST = NULL)
		{	
			$MYSQLI = $GLOBALS['MYSQLI'];
			
			$COLS = [];
			$VALUES = [];
			FOREACH($DATA AS $KEY => $VALUE)
			{
				IF(!$TRUST) {
					$VALUE = SELF::STRIP($VALUE);							
					$KEY = SELF::STRIP($KEY);							
				} ELSE
				{											
					$VALUE = $MYSQLI->real_escape_string($VALUE);
					$KEY = $MYSQLI->real_escape_string($KEY);
				}
				
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
		
		PUBLIC STATIC FUNCTION GETUSERPROFILE($DATA) {
			$ERROR = '{"responce": "USERPROFILEBAD"}';
			$SUCCESS = '{"responce": "USERPROFILEOK"}';
		
			IF(!ISSET($DATA->user)) RETURN $ERROR;
			
			$WHERE['user'] = $DATA->user;
			$RESULT = DB::SELECT('users', $WHERE);
			
			$PROFILE = $RESULT->fetch_object();
			IF(!$PROFILE) RETURN $ERROR;
			
			$AUTH['user'] = $PROFILE;
			
			UNSET($PROFILE->token);		
			$PROFILE->avatar = SELF::GETAVATAR($AUTH);
			$PROFILE->email = $PROFILE->user . MAILDOMAIN;
			
			$OUT['profile'] = $PROFILE;
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION CHANGEPARAM($DATA) {
			$ERROR = '{"responce": "PROFILECHANGEPARAMBAD"}';
			$SUCCESS = '{"responce": "PROFILECHANGEPARAMOK"}';
		
			IF(!ISSET($DATA->param) OR !ISSET($DATA->value)) RETURN $ERROR;			
			IF($DATA->value != 1 AND $DATA->value != 0) RETURN $ERROR;
			IF($DATA->param != 'notification') RETURN $ERROR;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$SET[$DATA->param] = $DATA->value;
			$WHERE['id'] = $AUTH->id;
			DB::UPDATE('users', $SET, $WHERE);
			
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
		
		PUBLIC STATIC FUNCTION GETAUTH() {
			$AUTH = SELF::CHECK();
			UNSET($AUTH['user']->token);		
			$AUTH['user']->avatar = SELF::GETAVATAR($AUTH);
			
			RETURN JSON_ENCODE($AUTH['user']);
		}
		
		PUBLIC STATIC FUNCTION SIGNOUT() {
			SESSION_START();
			$_SESSION['token'] = '';
			$_SESSION['browser'] = '';
			SESSION_DESTROY();
			SESSION_UNSET();
			
			RETURN '{"responce": "SIGNEDOUT"}';
		}
		
		PUBLIC STATIC FUNCTION GETAVATAR($AUTH) {
			$A = $AUTH['user']->avatar;
			$AVATAR = AVATAR_PATH . $A;
			$AVATARFILE = AVATAR_ABSPATH . $A;
			$OUT = ($A AND FILE_EXISTS($AVATAR)) ? $AVATARFILE : 'img/noavatar.svg';
			
			RETURN $OUT;
		}
		
		PUBLIC STATIC FUNCTION USER() {
			SESSION_START();
			
			$GLOBALS['MYSQLI'] = DB::CONNECT();
			$AUTH = SELF::CHECK();
								
			IF($AUTH['exist'] == TRUE AND $AUTH['user']->status == 1) {
				UNSET($AUTH['user']->token);	
				$AUTH['user']->avatar = SELF::GETAVATAR($AUTH);
				
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
				$AUTH['user']->avatar = SELF::GETAVATAR($AUTH);
				
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
		
		PUBLIC STATIC FUNCTION GETCATINFO($CATEGORIES, $ID) {																		
			FOREACH($CATEGORIES AS $CAT) {			
				IF($CAT->id == $ID) RETURN $CAT;
			}
			
			RETURN [];
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
					$I['editors'] = IMPLODE(';', ARRAY_FILTER(EXPLODE(';', $CATEGORY->editors)));
										
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
		
		PUBLIC STATIC FUNCTION TYPE($T, $S = NULL) {				
			IF(!$S) $S = LIBTYPES;
			IF($T > 0 AND $T <= COUNT($S)) RETURN $S[$T];
			RETURN NULL;
		}
			
		
		PUBLIC STATIC FUNCTION CHECKPENDING($DATA) {
			$ERROR = '{"responce": "PENDINGBAD"}';
			$SUCCESS = '{"responce": "PENDINGOK"}';
		
			$AUTH = $GLOBALS['AUTH']['user'];

			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			IF(!ISSET($DATA->type) OR !IS_NUMERIC($DATA->type)) RETURN $ERROR;
			
			$TABLE = SELF::TYPE($DATA->type);
			
			$SET['pending'] = 0;
			$WHERE['id'] = $DATA->id;
			DB::UPDATE($TABLE, $SET, $WHERE);
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION RATE($DATA) {
			$ERROR = '{"responce": "RATEBAD"}';
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			IF(!ISSET($DATA->type) OR !IS_NUMERIC($DATA->type)) RETURN $ERROR;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$TABLE = SELF::TYPE($DATA->type);
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT($TABLE, $WHERE);
			IF(!$RESULT) RETURN $ERROR;
			
			$PROD = $RESULT->fetch_object();			
			$RATING = ARRAY_FILTER(EXPLODE(';', $PROD->rating));
			
			IF(IN_ARRAY($AUTH->user, $RATING)) 
			{	
				$RATING = ARRAY_DIFF($RATING, [$AUTH->user]);
			} ELSE {
				$RATING[] = $AUTH->user;
			}
									
			$SET['rating'] = IMPLODE(';', $RATING);
			
			DB::UPDATE($TABLE, $SET, $WHERE);
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION INFO($DATA) {
			$ERROR = '{"responce": "PRODINFOBAD"}';
			$NOACCESS = '{"responce": "PRODINFONOACCESS"}';
			$STATUSOFF = '{"responce": "PRODINFOOFF"}';
			$AUTH = $GLOBALS['AUTH']['user'];
			$ISNOADMIN = $AUTH->rights < 1 OR $AUTH->status == 0;
			
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			IF(!ISSET($DATA->type)) RETURN $ERROR;
			
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$WHERE = [];
			$WHERE['id'] = $DATA->id;
			
			$TABLE = SELF::TYPE($DATA->type);
			IF(!$TABLE) RETURN $ERROR;
			
			$RESULT = DB::SELECT($TABLE, $WHERE);	
			IF(!$RESULT) RETURN $ERROR;
			$INFO = $RESULT->fetch_object();
				
			// PATHWAY
			$PATHWAY = CAT::GETPATHWAY($INFO->catid, $CATEGORIES);			
			$PATHWAY = ARRAY_REVERSE($PATHWAY);
			$PATHWAY[] = ARRAY('name' => $INFO->name, 'id' => $INFO->id);
			
			// ACCESS
			$ID = $PATHWAY[0]['id'];
			$CAT = CAT::GETCATINFO($CATEGORIES, $ID);	

			IF($CAT->parent != 0) RETURN $ERROR;	
			IF($CAT->status == 0 AND $ISNOADMIN) RETURN $NOACCESS;
			$GRP = ARRAY_FILTER(EXPLODE(';', $CAT->premissions));
			IF(!IN_ARRAY($AUTH->grp, $GRP) AND COUNT($GRP) AND $AUTH->rights < 1) RETURN $NOACCESS;
			IF($INFO->status == 0 AND $ISNOADMIN) RETURN $STATUSOFF;
				
			
			// RATING
			$RATING = ARRAY_FILTER(EXPLODE(';', $INFO->rating));
			$RATINGCNT = COUNT($RATING);
			
			$OUT['pathway'] = $PATHWAY;
			$OUT['type'] = $TABLE;
			$OUT['product'] = $INFO;
			$OUT['rating'] = $RATINGCNT ;
			$OUT['userrate'] = IN_ARRAY($AUTH->user, $RATING);
			$OUT['comments'] = SELF::GETCOMMENTS($DATA, TRUE);
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION SENDCOMMENT($DATA) {
			$ERROR = '{"responce": "COMMENTBAD"}';
			$SUCCESS = '{"responce": "COMMENTOK"}';
			$NOACCESS = '{"responce": "COMMENTNOACCESS"}';
			$NOTEXT = '{"responce": "COMMENTNOTEXT"}';
			$AUTH = $GLOBALS['AUTH']['user'];
			
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			IF(!ISSET($DATA->type) OR !IS_NUMERIC($DATA->type)) RETURN $ERROR;
			IF(!ISSET($AUTH->user) OR $AUTH->rights < 0) RETURN $NOACCESS;
			IF(!ISSET($DATA->txt) OR !COUNT($DATA->txt) OR EMPTY($DATA->txt)) RETURN $NOTEXT;
			
			$TABLE = SELF::TYPE($DATA->type);
			
			$TXT = STRIP_TAGS($DATA->txt);
			$TXT = STR_REPLACE('\n\n', '\n', $TXT);
			IF(!COUNT($TXT) OR EMPTY($TXT)) RETURN $NOTEXT;
			
			$BUG = $DATA->bug ? 1 : 0;
			
			$SET['comment'] = $TXT;
			$SET['prodid'] = $DATA->id;
			$SET['date'] = TIME();
			$SET['user'] = $AUTH->user;
			$SET['bug'] = $BUG;
			DB::INSERT('comments', $SET);
								
			MSGSYSTEM::ADDEDCOMMENT($TXT, $DATA->id, $TABLE, $BUG);
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION GETCOMMENTS($DATA, $RAW = NULL) {
			$ERROR = '{"responce": "COMMENTGETBAD"}';
			$SUCCESS = '{"responce": "COMMENTGETOK"}';						
			$AUTH = $GLOBALS['AUTH']['user'];
			
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			IF(!ISSET($DATA->type) OR !IS_NUMERIC($DATA->type)) RETURN $ERROR;
			
			$TABLE = SELF::TYPE($DATA->type);
			
			$WHERE['prodid'] = $DATA->id;
			$RESULT = DB::SELECT('comments', $WHERE, [], 'date', NULL, TRUE);
			$CMT = DB::TOARRAY($RESULT);
			
			$OUT = [];
			FOREACH($CMT AS $V){
				$WHERE = [];
				$WHERE['user'] = $V->user;
				$RES = DB::SELECT('users', $WHERE);
				$U = $RES->fetch_object();
				$V->avatar = AVATAR_ABSPATH . $U->avatar;
				$OUT[] = $V;
			}
			
			IF($RAW) RETURN $OUT;
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION DELCOMMENT($DATA) {
			$ERROR = '{"responce": "COMMENTDELBAD"}';
			$SUCCESS = '{"responce": "COMMENTDELOK"}';						
			$AUTH = $GLOBALS['AUTH']['user'];
			
			IF(!ISSET($DATA->id) OR !IS_NUMERIC($DATA->id)) RETURN $ERROR;
			IF(!ISSET($DATA->type) OR !IS_NUMERIC($DATA->type)) RETURN $ERROR;
			
			$TABLE = SELF::TYPE($DATA->type);
			
			$WHERE['id'] = $DATA->id;
			$RESULT = DB::SELECT('comments', $WHERE);
			$COMMENT = $RESULT->fetch_object();
			IF(!$COMMENT) RETURN $ERROR;
			
			IF($COMMENT->user != $AUTH->user AND $AUTH->rights < 1) RETURN $ERROR;
			
			$DEL[] = $DATA->id;
			$RESULT = DB::DEL('comments', $DEL, 'id');
			
			IF(!$RESULT) RETURN $ERROR;
			RETURN $SUCCESS;
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
			$TABLE = SELF::TYPE($TYPE);
			$PRODPAGE = PRODUCTS::TYPE($TYPE, PRODUCTPAGE);
			
			
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
						
			$RESULT = DB::SELECT($TABLE, $WHEREOR, $WHEREAND, 'date', $LIMIT);			
			$P = DB::TOARRAY($RESULT);
			$PRODUCTS = [];
			FOREACH($P AS $V) {
				$V->productpage = $PRODPAGE;
				$PRODUCTS[] = $V;				
			}
			
			$ROWS = DB::CNT($TABLE, $WHEREOR, $WHEREAND);						
			$NUMPAGES = $ROWS;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			
			$OUT['products'] = $PRODUCTS;
			$OUT['pathway'] = $PATHWAY;
			$OUT['productpage'] = $PRODPAGE;
						
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION GETMODELPATH($MODEL) {
			$PATH = CAT::BUILDPATH($MODEL->catid);
			IF(!COUNT($PATH)) RETURN -1;
			$PATH .= CAT::CLEAR($MODEL->name) . '\\' . CAT::CLEAR($MODEL->render)  . '\\';
			$FILES = (GLOB($PATH . '*.max'));
			$FILE = $FILES[0];
			IF(!$FILE) RETURN -1;
			
			RETURN $FILE;
		}
		
		PUBLIC STATIC FUNCTION DOWNLOAD($FILE) {
			IF(FILE_EXISTS($FILE)) {
				HEADER('Content-Description: File Transfer');
				HEADER('Content-Type: application/octet-stream');
				HEADER('Content-Disposition: attachment; filename="' . BASENAME($FILE)) . '"';
				HEADER('Content-Transfer-Encoding: binary');
				HEADER('Expires: 0');
				HEADER('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				HEADER('Pragma: public');
				HEADER('Content-Length: ' . FILESIZE($FILE));
				OB_CLEAN();
				FLUSH();
				READFILE($FILE);
				EXIT;
			}
		}
					
		PUBLIC STATIC FUNCTION MODELDOWNLOAD($ID, $TYPE) {
			
			$ERROR = '{"responce": "MODELBAD"}';
			$NOTEXIST = '{"responce": "MODELNOTEXIST"}';
			$NORIGHTS = '{"responce": "NORIGHTS"}';
			
			$TYPE = PRODUCTS::TYPE($TYPE);
			IF(!$TYPE) {
				ECHO $ERROR;
				RETURN FALSE;
			}
								
			$WHERE['id'] = $ID;
			$RESULT = DB::SELECT($TYPE, $WHERE);
			
			$ROWS = MYSQLI_NUM_ROWS($RESULT);
			
			IF($ROWS != 1) {
				ECHO $ERROR;
				RETURN FALSE;
			}
			
			$RESULT = DB::TOARRAY($RESULT);	
			$PROD = $RESULT[0];
			$DIR = '';
				
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
	
			$ACCESS = ACCESS::DOWNLOADACCESS($PROD);
									
			IF(!$ACCESS) {
				ECHO $NORIGHTS;
				RETURN FALSE;
			}
				
			IF($TYPE == 'models') {
										
				$FILE = PRODUCTS::GETMODELPATH($PROD);
				IF($FILE == -1) {
					ECHO $NOTEXIST;
					RETURN FALSE;
				}
								
				$DIR = DIRNAME($FILE) . '\\';	
			}
			
			$FILES = GLOB($DIR . '*.zip');
						
			IF(!COUNT($FILES)) {
				ECHO $NOTEXIST;
				RETURN FALSE;
			}
			
			STATISTIC::DOWNLOADMODEL($PROD);
					
			$FILE = $FILES[0];
			SELF::DOWNLOAD($FILE);		
		}
	}
	
	///////////////////////////////////////////////////////
	// STATISTIC CLASS
	///////////////////////////////////////////////////////
	
	CLASS STATISTIC {				
		PUBLIC STATIC FUNCTION DOWNLOADMODEL($MODEL) {
			$AUTH = $GLOBALS['AUTH']['user'];
			$USER = $AUTH->user;
			
			// STATISTIC BY MODEL
			$WHERE = [];
			$SET = [];
			$WHERE['id'] = $MODEL->id;
			$SET['downloads'] = $MODEL->downloads + 1;
			DB::UPDATE('models', $SET, $WHERE);
			
			// STATISTIC FOR USER
			$WHERE = [];
			$SET = [];
			$WHERE['user'] = $USER;
			
			$RESULT = DB::SELECT('users', $WHERE);
			$U = $RESULT->fetch_object();
			$SET['downloads'] = $U->downloads + 1;
			DB::UPDATE('users', $SET, $WHERE);
			
			// STATISTIC BY USER
			$WHERE = [];
			$SET = [];
			
			$SET['date'] = TIME();
			$SET['user'] = $AUTH->user;
			$SET['type'] = 1;
			$SET['prodid'] = $MODEL->id;
			$SET['prodname'] = $MODEL->name;
			$SET['day'] = DATE("Y-m-d", TIME());
			
			DB::INSERT('statistic_user_downloads', $SET);
			
			// STATISTIC BY MONTH
			$WHERE = [];
			$SET = [];
			
			$Y = DATE('Y');
			$M = DATE('m');
			
			$WHERE['year'] = $Y;
			$WHERE['month'] = $M;
			$RESULT = DB::SELECT('statistic_downloads', $WHERE);
			$R = $RESULT->fetch_object();
			$CNT = 1;
			
			IF($R) {
				$CNT = $R->cnt + 1;
			}
			
			$WHERE = [];
			$SET = [];
			
			$WHERE['id'] = $R->id;
			
			$SET['year'] = $Y;
			$SET['month'] = $M;
			$SET['cnt'] = $CNT;
			$SET['date'] = TIME();
						
			IF(!$R) {
				DB::INSERT('statistic_downloads', $SET);
			} ELSE {
				DB::UPDATE('statistic_downloads', $SET, $WHERE);
			}
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
						 
			$MODEL = $RESULT->fetch_object();
						
			$FILE = PRODUCTS::GETMODELPATH($MODEL);
			IF($FILE == -1) RETURN $NOTEXIST;
						
			STATISTIC::DOWNLOADMODEL($MODEL);
						
			$OUT['responce'] = "MODELOK";
			$OUT['file'] = $FILE;
			
			RETURN JSON_ENCODE($OUT);					
		}
	}
	
	///////////////////////////////////////////////////////
	// ACCESS CLASS
	///////////////////////////////////////////////////////
	
	CLASS ACCESS {
		PUBLIC STATIC FUNCTION GETSUBIDS($CATEGORIES, $PARENT) {				
			$IDS = [];
			$IDS[] = $PARENT;
			$SIDS = [];
			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $PARENT) {													
					$SIDS = SELF::GETSUBIDS($CATEGORIES, $CATEGORY->id);
					
					$IDS[] = $CATEGORY->id;
					$IDS = ARRAY_MERGE($IDS, $SIDS);
				}								
			}
			
			RETURN ARRAY_UNIQUE($IDS);
		}
		
		PUBLIC STATIC FUNCTION GETACCESSID($CATEGORIES, $RULE, $COL, $ID) {																
			$IDS = [];
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$SUBIDS = SELF::GETSUBIDS($CATEGORIES, $ID);
			
			IF($AUTH->rights > 0) RETURN ARRAY_UNIQUE($SUBIDS);
				
			FOREACH($CATEGORIES AS $CAT) {			
				IF($CAT->parent == 0) {
					$GRP = ARRAY_FILTER(EXPLODE(';', $CAT->{$COL}));
					IF(IN_ARRAY($RULE, $GRP) OR !COUNT($GRP)) {										
						$I = SELF::GETSUBIDS($CATEGORIES, $CAT->id);
						$IDS = ARRAY_MERGE($IDS, $I);
					}
				}
			}

			$IDS = ARRAY_UNIQUE($IDS);
			RETURN ARRAY_INTERSECT($IDS, $SUBIDS);
		}
				
		
		PUBLIC STATIC FUNCTION GETACCESS($ID = -1, $CATEGORIES) {
			$AUTH = $GLOBALS['AUTH']['user'];
			$FILTER = [];			
			$NOACCESS = FALSE;
			IF($ID == -1) $ID = 0;
			
			$FILTER['ids'] = SELF::GETACCESSID($CATEGORIES, $AUTH->grp, 'premissions', $ID);
			$NOACCESS = !COUNT($FILTER['ids']);				
			IF($NOACCESS) RETURN FALSE;						
			
			RETURN $FILTER['ids'];
		}
		
		PUBLIC STATIC FUNCTION DOWNLOADACCESS($PROD) {
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$RESULT = DB::SELECT('category');
			$CATEGORIES = DB::TOARRAY($RESULT);
	
			$ACCESSID = SELF::GETACCESS(-1, $CATEGORIES);			
			$PATHWAY = CAT::GETPATHWAY($PROD->catid, $CATEGORIES);			
			$PATHWAY = ARRAY_REVERSE($PATHWAY);
			$LIBID = $PATHWAY[0]['id'];
			$LIB = CAT::GETCATINFO($CATEGORIES, $LIBID);
			IF(!$AUTH OR !COUNT($AUTH->user)) RETURN FALSE;
			IF((!$LIB->candl AND $AUTH->rights < 1) OR !IN_ARRAY($PROD->catid, $ACCESSID)) RETURN FALSE;
			RETURN TRUE;
		}
	}
	
	///////////////////////////////////////////////////////
	// SEARCH CLASS
	///////////////////////////////////////////////////////
	
	CLASS SEARCH {
		PUBLIC STATIC FUNCTION FASTSEARCH($DATA) {
									
			/*$OUT = [];
			
			$TYPE = PRODUCTS::TYPE($DATA->type);
			$PRODPAGE = PRODUCTS::TYPE($DATA->type, PRODUCTPAGE);
			
			$LIMIT['start'] = 0;
			$LIMIT['end'] = 7;
			
			$COL = [];
			$COL[] = 'name';
			$COL[] = 'tags';
			
			$WHERE['status'] = 1;
			$WHERE['pending'] = 0;
			
			$FIND = ARRAY_FILTER(EXPLODE(' ', $DATA->query));
			
			$RESULT = DB::SELECTLIKE($TYPE, $COL, $FIND, $LIMIT, $WHERE);			
			$OUT['result'] = DB::TOARRAY($RESULT);
			$OUT['productpage'] = $PRODPAGE;
			
			RETURN JSON_ENCODE($OUT);*/
			
			RETURN SELF::GLOBALSEARCH($DATA, TRUE);
		}
		/*
		PUBLIC STATIC FUNCTION GETSUBIDS($CATEGORIES, $PARENT) {				
			$IDS = [];
			$IDS[] = $PARENT;
			$SIDS = [];
			
			FOREACH($CATEGORIES AS $CATEGORY) {			
				IF($CATEGORY->parent == $PARENT) {													
					$SIDS = SELF::GETSUBIDS($CATEGORIES, $CATEGORY->id);
					
					$IDS[] = $CATEGORY->id;
					$IDS = ARRAY_MERGE($IDS, $SIDS);
				}								
			}
			
			RETURN ARRAY_UNIQUE($IDS);
		}
		
		PUBLIC STATIC FUNCTION GETACCESSID($CATEGORIES, $RULE, $COL, $ID) {																
			$IDS = [];
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$SUBIDS = SELF::GETSUBIDS($CATEGORIES, $ID);
			
			IF($AUTH->rights > 0) RETURN ARRAY_UNIQUE($SUBIDS);
				
			FOREACH($CATEGORIES AS $CAT) {			
				IF($CAT->parent == 0) {
					$GRP = ARRAY_FILTER(EXPLODE(';', $CAT->{$COL}));
					IF(IN_ARRAY($RULE, $GRP) OR !COUNT($GRP)) {										
						$I = SELF::GETSUBIDS($CATEGORIES, $CAT->id);
						$IDS = ARRAY_MERGE($IDS, $I);
					}
				}
			}

			$IDS = ARRAY_UNIQUE($IDS);
			RETURN ARRAY_INTERSECT($IDS, $SUBIDS);
		}
		
		
		
		PUBLIC STATIC FUNCTION GETACCESS($ID = -1, $CATEGORIES) {
			$AUTH = $GLOBALS['AUTH']['user'];
			$FILTER = [];			
			$NOACCESS = FALSE;
			IF($ID == -1) $ID = 0;
			
			$FILTER['ids'] = SELF::GETACCESSID($CATEGORIES, $AUTH->grp, 'premissions', $ID);
			$NOACCESS = !COUNT($FILTER['ids']);				
			IF($NOACCESS) RETURN FALSE;						
			
			RETURN $FILTER['ids'];
		}*/
						
		PUBLIC STATIC FUNCTION GLOBALSEARCH($DATA, $FASTSEARCH = NULL) {
			
			IF(!ISSET($DATA->type) OR !ISSET($DATA->query))RETURN '{}';
			IF(!ISSET($DATA->page) AND !$FASTSEARCH) RETURN '{}';
			IF(!ISSET($DATA->perpage) AND !$FASTSEARCH) RETURN '{}';			
			
			$AUTH = $GLOBALS['AUTH']['user'];			
						
			$FILTER = [];
			
			$RESULT = DB::SELECT('category', [],[], 'sort', NULL, TRUE);
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$FILTERCATID = -1;
			IF(ISSET($DATA->filter->cat->id)) $FILTERCATID = $DATA->filter->cat->id;
			$FILTERGLOBAL = FALSE;
			IF(ISSET($DATA->filter->global)) 
			{	
				$FILTERCATID = $DATA->filter->global < 1 ? -1 : $FILTERCATID;
				//$FILTERGLOBAL = $DATA->filter->global;
			}
						
			// FILTER CATID
			$ACCESS = ACCESS::GETACCESS($FILTERCATID, $CATEGORIES);
			IF($ACCESS === FALSE) RETURN FALSE;
			$FILTER['cat']['ids'] = $ACCESS;
	
			$OUT = [];
			
			$TYPE = PRODUCTS::TYPE($DATA->type);
			$PRODPAGE = PRODUCTS::TYPE($DATA->type, PRODUCTPAGE);
						
			$COL = [];
			$COL[] = 'name';
			$COL[] = 'tags';
			
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
			
			IF($FASTSEARCH) {
				$LIMIT['start'] = 0;
				$LIMIT['end'] = 7;
			}
			
			$WHERE['status'] = 1;
			$WHERE['pending'] = 0;
			
			$FIND = ARRAY_FILTER(EXPLODE(' ', $DATA->query));
			
			$RESULT = DB::SEARCH($TYPE, $FIND, $COL, $FILTER, $LIMIT);
			
			$PRODUCTS = [];
			
			FOREACH(DB::TOARRAY($RESULT) AS $V) {
				$V->productpage = $PRODPAGE;
				$PRODUCTS[] = $V;				
			}
			
			$OUT['result'] = $PRODUCTS;
						
			// STATISTIC				
			IF(!$FASTSEARCH) {
				$WHERE = [];
				$SET = [];
				
				$WHERE['query'] = $SET['query'] = TRIM($DATA->query);
				$RES = DB::SELECT('statistic_search', $WHERE);
				$QUERIES = DB::TOARRAY($RES);
				
				$SET['date'] = TIME();
				$SET['user'] = $AUTH->user;	
								
				IF(COUNT($QUERIES) == 1) {									
					$SET['count'] = $QUERIES[0]->count + 1;													
					$WHERE = [];
					$WHERE['id'] = $QUERIES[0]->id;		
	
					DB::UPDATE('statistic_search', $SET, $WHERE);					
				} ELSE
				{
					$SET['count'] = 1;
					DB::INSERT('statistic_search', $SET);
				}								
			}
			
			$NUMPAGES = 0;
			
			IF(!$FASTSEARCH) {
				$ROWS = DB::SEARCH($TYPE, $FIND, $COL, $FILTER, NULL, TRUE);			
				$NUMPAGES = $ROWS;
			}
						
			$OUT['productpage'] = $PRODPAGE;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			$OUT['filter']['cat'] = CAT::GETCATINFO($CATEGORIES, $FILTERCATID);
			$OUT['filter']['global'] = $FILTERGLOBAL;
			
						
			RETURN JSON_ENCODE($OUT);
		}
		
		/*PUBLIC STATIC FUNCTION GLOBALSEARCH($DATA) {
			IF(!ISSET($DATA->type) OR !ISSET($DATA->query))RETURN '{}';
			IF(!ISSET($DATA->page)) RETURN '{}';
			IF(!ISSET($DATA->perpage)) RETURN '{}';			
												
			$FILTER = [];
			
			$RESULT = DB::SELECT('category', [],[], 'sort', NULL, TRUE);
			$CATEGORIES = DB::TOARRAY($RESULT);
			
			$FILTERCATID = -1;
			IF(ISSET($DATA->filter->cat->id)) $FILTERCATID = $DATA->filter->cat->id;
			$FILTERGLOBAL = FALSE;
			IF(ISSET($DATA->filter->global)) $FILTERCATID = $DATA->filter->global ? -1 : $FILTERCATID;
			
			// FILTER CATID
			$ACCESS = SELF::GETACCESS($FILTERCATID, $CATEGORIES);
			IF($ACCESS === FALSE) RETURN FALSE;
			$FILTER['cat']['ids'] = $ACCESS ;
	
			$OUT = [];
			
			$TYPE = PRODUCTS::TYPE($DATA->type);
			$PRODPAGE = PRODUCTS::TYPE($DATA->type, PRODUCTPAGE);
			
			$COL = [];
			$COL[] = 'name';
			$COL[] = 'tags';
			
			$CURPAGE = 1;
						
			IF($DATA->page > 0) $CURPAGE = $DATA->page;
			$LIMIT['start'] = ($CURPAGE - 1) * $DATA->perpage;
			$LIMIT['end'] = $DATA->perpage;
			
			$WHERE['status'] = 1;
			$WHERE['pending'] = 0;
			
			$FIND = ARRAY_FILTER(EXPLODE(' ', $DATA->query));

			$RESULT = DB::SELECTLIKE($TYPE, $COL, $FIND, $LIMIT, $WHERE, NULL, $FILTER);			
			$OUT['result'] = DB::TOARRAY($RESULT);
			
			$ROWS = DB::SELECTLIKE($TYPE, $COL, $FIND, NULL, $WHERE, TRUE, $FILTER);						
			$NUMPAGES = $ROWS;
						
			$OUT['productpage'] = $PRODPAGE;
			
			$OUT['currpage'] = $CURPAGE;
			$OUT['totalitems'] = $NUMPAGES;
			$OUT['perpage'] = $DATA->perpage;
			$OUT['filter']['cat'] = SELF::GETCATINFO($CATEGORIES, $FILTERCATID);
			$OUT['filter']['global'] = $FILTERGLOBAL;
			
						
			RETURN JSON_ENCODE($OUT);
		}*/
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
						
			$RESULT = DB::SELECT('category', $WHERE,[], 'sort', NULL, TRUE);
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
							$IDS[$CAT->name]['sort'] = $CAT->sort;
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
					$T = SELF::GETTHUMBS($RESULT);

					IF(COUNT($T)) {			
						$OUT[$K]['child'][$K2]['id'] = $V2['id'];
						$OUT[$K]['child'][$K2]['name'] = $K2;
						$OUT[$K]['child'][$K2]['sort'] = $RESULT[0]->date;
						$OUT[$K]['child'][$K2]['previews'] = $T;
						$OUT[$K]['id'] = $V['id'];
						$OUT[$K]['type'] = $V['type'];
						$OUT[$K]['sort'] = $V['sort'];
						$OUT[$K]['name'] = $K;
					}
				};												
			}
			
			ECHO JSON_ENCODE($OUT);	
		}
	}
	
	///////////////////////////////////////////////////////
	// MSGSYSTEM CLASS
	///////////////////////////////////////////////////////

	CLASS MSGSYSTEM {
		PUBLIC STATIC FUNCTION ADD($SUBJECT, $MSG, $BUG = 0, $IMG = NULL) {
			IF(EMPTY($MSG)) RETURN FALSE;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$SET['msg'] = $MSG;
			$SET['subject'] = $SUBJECT;
			$SET['bug'] = $BUG;
			$SET['viewed'] = 0;	
			$SET['date'] = TIME();
			$SET['user'] = $AUTH->user;
			$SET['img'] = $IMG;
						
			DB::INSERT('msg', $SET, TRUE);
		}	
		
		PUBLIC STATIC FUNCTION ADDEDCOMMENT($TXT, $ID, $TABLE, $BUG = 0) {
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $ID;
			$RESULT = DB::SELECT($TABLE, $WHERE);
			$PROD = $RESULT->fetch_object();
						
			$MSG = '<a href="' . HOSTNAME . '#/' . RTRIM($TABLE, 's') . '/' . $ID . '">' . $PROD->name . '</a><br>';
			$MSG .= 'Added comment by ' . $AUTH->user . '<br><br>';						
			$MSG .= '<pre>' . $TXT. '</pre><br>';
			
			$IMG = $PROD->previews;
			
			$TITLE = $BUG ? 'Bug Report' : 'Comment';
			MSGSYSTEM::ADD($TITLE, $MSG, $BUG, $IMG);
		}
		
		PUBLIC STATIC FUNCTION FEEDBACK($DATA) {
			$ERROR = '{"responce": "FEEDBACKBAD"}';
			$SUCCESS = '{"responce": "FEEDBACKOK"}';						
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			IF(!ISSET($DATA->txt) OR !ISSET($DATA->subject)) RETURN $ERROR;
						
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$TXT = STRIP_TAGS($DATA->txt);
			$TXT = STR_REPLACE('\n\n', '\n', $TXT);
			IF(!COUNT($TXT) OR EMPTY($TXT)) RETURN $ERROR;
						
			$MSG = 'Feedback send by ' . $AUTH->user . '<br><br>';
			$MSG .= '<b>' . DB::STRIP($DATA->subject) . '</b><br><br>';
			$MSG .= '<pre>' . $TXT. '</pre><br>';
			$MSG .= '<div>
				<a href="mailto:' . $AUTH->user . MAILDOMAIN . '">Send Reply</a>				
			</div>';
			
			$BUG = $DATA->bug ? 1 : 0;
			
			MSGSYSTEM::ADD('Feedback', $MSG, $BUG);
			
			RETURN $SUCCESS;
		}		
	}
	
	///////////////////////////////////////////////////////
	// AVATAR CLASS
	///////////////////////////////////////////////////////
	
	CLASS AVATAR {
		PUBLIC STATIC FUNCTION RESIZE($TARGET, $NEW, $SIZE) {
			LIST($W_ORIG, $H_ORIG) = GETIMAGESIZE($TARGET);
			
			$OFFSET_X = 0;  
			$OFFSET_Y = 0; 
			$SQR = $SIZE;
			
			IF($W_ORIG > $H_ORIG) {
				$SQR = $H_ORIG;             
				$OFFSET_X = ($W_ORIG - $H_ORIG) / 2;  
				$OFFSET_Y = 0;              
			} ELSE IF($H_ORIG > $W_ORIG) {
				$SQR = $W_ORIG;             
				$OFFSET_X = 0;  
				$OFFSET_Y = ($H_ORIG - $W_ORIG) / 2;              
			} ELSE
			{
				$SQR = $W_ORIG;             
				$OFFSET_X = 0;  
				$OFFSET_Y = 0; 
			}
			
			$IMG = IMAGECREATEFROMJPEG($TARGET);
			$TCI = IMAGECREATETRUECOLOR($SIZE, $SIZE);
			
			IMAGECOPYRESAMPLED($TCI, $IMG, 0, 0, $OFFSET_X, $OFFSET_Y, $SIZE, $SIZE, $SQR, $SQR);
			IMAGEJPEG($TCI, $NEW, 99);
		}
		
		PUBLIC STATIC FUNCTION CLEAR() {
			$AUTH = $GLOBALS['AUTH']['user'];
			$USER = $AUTH->user;
			
			$SET['avatar'] = NULL;
			$WHERE['user'] = $USER;
			DB::UPDATE('users', $SET, $WHERE, TRUE);
		}
	}
	
	///////////////////////////////////////////////////////
	// FAVORITES CLASS
	///////////////////////////////////////////////////////
	
	CLASS FAVORITES {
		
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
		
		PUBLIC STATIC FUNCTION GET($DATA) {
			IF(!ISSET($DATA->type))RETURN '{}';
						
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['user'] = $AUTH->user;
			$WHERE['type'] = $DATA->type;
			$RESULT = DB::SELECT('favorites', [], $WHERE, 'date', NULL, TRUE);			
			$FAV = DB::TOARRAY($RESULT);
			
			$TABLE = PRODUCTS::TYPE($DATA->type);
			$PRODPAGE = PRODUCTS::TYPE($DATA->type, PRODUCTPAGE);
						
			$OUT = [];
			FOREACH($FAV AS $V) {
				$W['id'] = EXPLODE(';', $V->products);
				
				$RES = DB::SELECT($TABLE, $W);
				
				$PRODS = [];
				FOREACH(DB::TOARRAY($RES) AS $VV) {
					$VV->productpage = $PRODPAGE;
					$PRODS[$VV->id] = $VV;
				}
				$V->products = $PRODS;
								
				$OUT[] = $V;
			}
			
			RETURN JSON_ENCODE($OUT);
		}
		
		PUBLIC STATIC FUNCTION GETSHARED($DATA) {
			$SHAREOFF['responce'] = 'FAVGETSHAREOFF';
			$ERROR['responce'] = 'FAVGETSHAREBAD';
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			
			IF(!ISSET($DATA->shareid))RETURN '{}';
						
			$WHERE['shareid'] = $DATA->shareid;
			$RESULT = DB::SELECT('favorites', [], $WHERE, 'date', NULL, TRUE);			
			$SHARE = $RESULT->fetch_object();
			
			$SHAREOFF['user'] = $SHARE->user;
			$SHAREOFF['name'] = $SHARE->name;
			
			IF(!$SHARE) RETURN JSON_ENCODE($ERROR);
			IF($SHARE->shared != 1) RETURN JSON_ENCODE($SHAREOFF);
						
			$TABLE = PRODUCTS::TYPE($SHARE->type);
						
			$W['id'] = EXPLODE(';', $SHARE->products);			
			$RES = DB::SELECT($TABLE, $W);
			
			$PRODPAGE = PRODUCTS::TYPE($SHARE->type, PRODUCTPAGE);
			
			$PRODS = [];
			FOREACH(DB::TOARRAY($RES) AS $V) {
				$V->productpage = $PRODPAGE;
				$PRODS[$V->id] = $V;
			}
			
			$SHARE->products = $PRODS;
					
			RETURN JSON_ENCODE($SHARE);
		}
		
		PUBLIC STATIC FUNCTION GETCOLLECTION($DATA) {
			IF(!ISSET($DATA->id))RETURN '{}';
			$ERROR = '{"responce": "FAVCOLLECTIONBAD"}';
						
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $DATA->id;
			$WHERE['user'] = $AUTH->user;
			$RESULT = DB::SELECT('favorites', [], $WHERE, 'date', NULL, TRUE);			
			$COLLECTOIN = $RESULT->fetch_object();
					
			IF(!$COLLECTOIN) RETURN $ERROR;
			
			$TABLE = PRODUCTS::TYPE($COLLECTOIN->type);
			$PRODPAGE = PRODUCTS::TYPE($COLLECTOIN->type, PRODUCTPAGE);
						
			$W['id'] = EXPLODE(';', $COLLECTOIN->products);			
			$RES = DB::SELECT($TABLE, $W);
			
			$PRODS = [];
			FOREACH(DB::TOARRAY($RES) AS $V) {
				$V->productpage = $PRODPAGE;
				$PRODS[] = $V;
			}
			
			$COLLECTOIN->products = $PRODS;
						
			RETURN JSON_ENCODE($COLLECTOIN);
		}
				
		PUBLIC STATIC FUNCTION SHARECOLLECTION($DATA) {
			$ERROR = '{"responce": "FAVSHAREBAD"}';
			$SHAREON = '{"responce": "FAVSHAREON"}';
			$SHAREOFF = '{"responce": "FAVSHAREOFF"}';
					
			IF(!ISSET($DATA->id))RETURN $ERROR;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $DATA->id;
			$WHERE['user'] = $AUTH->user;
			$RESULT = DB::SELECT('favorites', [], $WHERE);
			
			IF(!$RESULT->fetch_object()) RETURN $ERROR;
			
			$SET['shared'] = $DATA->status == 1 ? 1 : 0;
			
			DB::UPDATE('favorites', $SET, $WHERE, TRUE);
			
			RETURN $SET['shared'] ? $SHAREON : $SHAREOFF;
		}
		
		PUBLIC STATIC FUNCTION NEWCOLLECTION($DATA) {
			$ERROR = '{"responce": "FAVNEWCOLLECTIONBAD"}';
			$SUCCESS = '{"responce": "FAVNEWCOLLECTIONOK"}';
			$EXIST = '{"responce": "FAVNEWCOLLECTIONEXIST"}';
			$ITEMADDED = '{"responce": "FAVNEWCOLLECTIONITEMADDED"}';
						
			IF(!ISSET($DATA->type) OR !ISSET($DATA->name))RETURN $ERROR;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['name'] = $DATA->name;
			$RESULT = DB::SELECT('favorites', $WHERE);
			
			IF($RESULT->fetch_object()) RETURN $EXIST;
			
			$SET['user'] = $AUTH->user;
			$SET['name'] = $DATA->name;
			$SET['type'] = $DATA->type;
			$SET['date'] = TIME();
			$SET['shareid'] = SELF::UNIQUEID(8);
			DB::INSERT('favorites', $SET);
			
			IF(ISSET($DATA->prodid)) {
				$MYSQLI = $GLOBALS['MYSQLI'];				
				$DATA->id = $MYSQLI->insert_id;
				
				SELF::ADDITEM($DATA);
				
				RETURN $ITEMADDED;
			}
			
			RETURN $SUCCESS;
		}
		
		PUBLIC STATIC FUNCTION ADDITEM($DATA) {
			$ERROR['responce'] = 'FAVADDITEMBAD';
			$SUCCESS['responce'] = 'FAVADDITEMOK';
									
			IF(!ISSET($DATA->id) OR !ISSET($DATA->prodid))RETURN JSON_ENCODE($ERROR);
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $DATA->id;
			$WHERE['user'] = $AUTH->user;
			$RESULT = DB::SELECT('favorites', [], $WHERE);
			$COLLECTOIN = $RESULT->fetch_object();
			IF(!$COLLECTOIN) RETURN $ERROR;
			
			$ITEMS = ARRAY_FILTER(EXPLODE(';', $COLLECTOIN->products));			
			$ITEMS = ARRAY_DIFF($ITEMS, [$DATA->prodid]);			
			$ITEMS[] = $DATA->prodid;
			
			$SET['products'] = IMPLODE(';', $ITEMS);
			DB::UPDATE('favorites', $SET, $WHERE, TRUE);
			
			$SUCCESS['name'] = $COLLECTOIN->name;
			RETURN JSON_ENCODE($SUCCESS);
		}
		
		PUBLIC STATIC FUNCTION REMOVEITEM($DATA) {
			$ERROR['responce'] = 'FAVREMITEMBAD';
			$SUCCESS['responce'] = 'FAVREMITEMOK';
									
			IF(!ISSET($DATA->id) OR !ISSET($DATA->prodid))RETURN JSON_ENCODE($ERROR);
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $DATA->id;
			$WHERE['user'] = $AUTH->user;
			$RESULT = DB::SELECT('favorites', [], $WHERE);
			$COLLECTOIN = $RESULT->fetch_object();
			IF(!$COLLECTOIN) RETURN $ERROR;
			
			$ITEMS = ARRAY_FILTER(EXPLODE(';', $COLLECTOIN->products));									
			$ITEMS = ARRAY_DIFF($ITEMS, [$DATA->prodid]);			
			$SET['products'] = IMPLODE(';', $ITEMS);
			DB::UPDATE('favorites', $SET, $WHERE, TRUE);
			
			$SUCCESS['name'] = $COLLECTOIN->name;
			RETURN JSON_ENCODE($SUCCESS);
		}
		
		PUBLIC STATIC FUNCTION RENCOLLECTION($DATA) {
			$ERROR = '{"responce": "FAVRENBAD"}';
			$SUCCESS = '{"responce": "FAVRENOK"}';
									
			IF(!ISSET($DATA->id) OR !ISSET($DATA->name))RETURN $ERROR;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $DATA->id;
			$WHERE['user'] = $AUTH->user;
						
			$RESULT = DB::SELECT('favorites', [], $WHERE);
			
			IF(!$RESULT->fetch_object()) RETURN $ERROR;
			
			$SET['name'] = $DATA->name;
			DB::UPDATE('favorites', $SET, $WHERE, TRUE);
			
			RETURN $SUCCESS;
		}

		PUBLIC STATIC FUNCTION DELCOLLECTION($DATA) {
			$ERROR = '{"responce": "FAVDELBAD"}';
			$SUCCESS = '{"responce": "FAVDELOK"}';
									
			IF(!ISSET($DATA->id))RETURN $ERROR;
			
			$AUTH = $GLOBALS['AUTH']['user'];
			
			$WHERE['id'] = $DATA->id;
			$WHERE['user'] = $AUTH->user;
			$RESULT = DB::SELECT('favorites', [], $WHERE);
					
			IF(!$RESULT->fetch_object()) RETURN $ERROR;
			
			$DEL[] = $DATA->id;
			$RESULT = DB::DEL('favorites', $DEL, 'id');
						
			RETURN $SUCCESS;
		}		
	}
?>