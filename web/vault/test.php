<?php

$DIR = "\\\\visco.local\\data\\Library\\IKEA\\Accessories\\Kitchen-Accessories\\RISATORP-basket\\VRay\\";
IF (!IS_READABLE($DIR)) ECHO NULL; 
if((COUNT(SCANDIR($DIR)) == 2)) ECHO "EEEEEEEEEEE";	

?>