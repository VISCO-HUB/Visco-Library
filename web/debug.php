<?php
	INI_SET("display_errors","1");
	INI_SET("display_startup_errors","1");
	INI_SET('error_reporting', E_ALL);
	
	INCLUDE($_GET['f']); 

?>