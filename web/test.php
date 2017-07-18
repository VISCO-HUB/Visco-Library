<?php



$a['img'][] = 'aaa';
$a['img'][] = 'bbb';
$a['img'][] = 'ccc';
$a['img'][] = 'dddd';

$b['img'][] = 'ffff';



print_r(array_merge_recursive($a, $b));
?>

