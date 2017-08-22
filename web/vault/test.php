<?php
$array = array(0 => 'blue', 1 => 'red', 2 => 'green string', 3 => 'red');


$a = ARRAY_FILTER($array, FUNCTION($V) {RETURN  STRPOS($V, 'green') !== FALSE;});

print_r($a);


?>