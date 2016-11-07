<?php



$ARR = array('11111', '22222', '33333');


FUNCTION CHANGESORT($A, $POS, $SHIFT) {
	$I = $A[$POS];
	UNSET($A[$POS]);
	ARRAY_SPLICE($A, $POS + $SHIFT , 0, $I);

	RETURN $A;
}

$A = CHANGESORT($ARR, 1, -1);

print_r($A);
?>

