<?php
	// GLOBAL ROOT PATH
	DEFINE('ROOT', REALPATH('../') . '\\');
		
	// MYSQL DATA
	DEFINE('MYSQL_DB', 'assets_library');
	DEFINE('MYSQL_SERVER', 'localhost');
	DEFINE('MYSQL_USER', 'assets_library');
	DEFINE('MYSQL_PWD', '*******');
	
	// LDAP ACTIVE DIRECTORY AUTH
	DEFINE('AUTH_SERVER', '192.168.0.10');
	DEFINE('AUTH_DOMAIN', 'visco.local');		
	DEFINE('AUTH_SALT', 'justsalt');
	
	// IMAGES			
	DEFINE('IMG_PATH',  ROOT . 'images\\');
	DEFINE('IMG_PADDING', 30);
	
	DEFINE('IMG_SIZE', 600);
	DEFINE('IMG_THUMB', 200);
	DEFINE('IMG_SMALL', 60);
?>