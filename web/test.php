<?php



$PATH = 'Z:\\';

$isFolder = is_dir($PATH);
var_dump($isFolder); //TRUE	
$dir = '\\\\visco.local\\data\\Library\\';

FUNCTION IS_DIR_EMPTY($DIR) {
  IF (!IS_READABLE($DIR)) RETURN NULL; 
  print_r(SCANDIR($DIR));
  //RETURN (COUNT(SCANDIR($DIR)) == 2);
  
}

$a = is_dir($dir);
IF($a) DIE('EEEE');
ECHO 'NOT ';
?>

