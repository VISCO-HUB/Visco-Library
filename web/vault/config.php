<?php
	// GLOBAL ROOT PATH
	DEFINE('ROOT', $_SERVER['DOCUMENT_ROOT'] . '\\');
	
	//SERVER
	DEFINE('HOSTNAME', ($_SERVER['HTTPS'] == off ?  'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . '/');
	
	// MYSQL DATA
	DEFINE('MYSQL_DB', 'assets_library');
	DEFINE('MYSQL_SERVER', 'localhost');
	DEFINE('MYSQL_USER', 'assets_library');
	DEFINE('MYSQL_PWD', '******');
	
	//E-MAIL
	DEFINE('MAILDOMAIN', '@visco.no');
	
	// INFO INI
	DEFINE('INFOINI', 'info.ini');
	
	// LDAP ACTIVE DIRECTORY AUTH
	DEFINE('AUTH_SERVER', '192.168.0.10');
	DEFINE('AUTH_DOMAIN', 'visco.local');		
	DEFINE('AUTH_SALT', 'justsalt');
	
	// IMAGES			
	DEFINE('IMG_PATH',  ROOT . 'images\\');
	DEFINE('IMG_PADDING', 30);
	
	DEFINE('IMG_HUGE', 600);
	DEFINE('IMG_THUMB', 200);
	DEFINE('IMG_SMALL', 60);
	
	// LIB TYPES (SQL TABLES MUST NAMED AS TYPES)
	DEFINE('LIBTYPES', ARRAY(1 => 'models', 2 => 'textures'));
	DEFINE('PRODUCTPAGE', ARRAY(1 => 'model', 2 => 'texture'));
	
	// AVATAR
	DEFINE('AVATAR_SIZE', 100);
	DEFINE('AVATAR_ABSPATH', 'avatars\\');
	DEFINE('AVATAR_PATH', ROOT . AVATAR_ABSPATH);
		
?>