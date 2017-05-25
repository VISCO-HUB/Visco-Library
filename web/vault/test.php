
<?php

INCLUDE 'config.php';
INCLUDE 'lib.php';

$MYSQLI = DB::CONNECT();
$GLOBS = GLOBS::GET();	

$SET = [];
$SET['user'][] = 'i.samoylenko';
$SET['user'][] = 'm.tiutiunnyk';
$SET['user'][] = 'e.chernopiskyi';
$SET['user'][] = 'n.rizak';
$SET['user'][] = 't.chobitko';
$SET['user'][] = 'v.kucher';
$SET['user'][] = 'v.melnykovych';
$SET['user'][] = 'rkhomyn';
$SET['user'][] = 'i.sereda';
$SET['user'][] = 'a.samson';
$SET['user'][] = 'd.klishch';
$SET['user'][] = 'o.kravchuk';
$SET['user'][] = 'o.olshanskyi';
$SET['user'][] = 'i.bodak';
$SET['user'][] = 'a.chupryna';
$SET['user'][] = 'n.kit';
$SET['user'][] = 'o.labintsev';
$SET['user'][] = 'v.kamenetskiy';
$SET['user'][] = 'a.mikitjuk';
$SET['user'][] = 'o.kosyanchuk';
$SET['user'][] = 'y.bozhyk';
$SET['user'][] = 'v.lukyanenko';
$SET['user'][] = 'a.pavlyuk';
$SET['user'][] = 'g.kupriychuk';
$SET['user'][] = 'i.kit';
$SET['user'][] = 'k.shvets';
$SET['user'][] = 'o.stasevych';
$SET['user'][] = 'p.pukha';
$SET['user'][] = 's.timonov';
$SET['user'][] = 's.shevchyk';
$SET['user'][] = 'v.goral';
$SET['user'][] = 'a.chivilikhin';
$SET['user'][] = 'o.vasiuk';
$SET['user'][] = 'e.astafiev';
$SET['user'][] = 'v.terletskyi';
$SET['user'][] = 'k.marev';
$SET['user'][] = 'o.chapran';
$SET['user'][] = 'v.zabolotnyi';
$SET['user'][] = 'i.mukutij';
$SET['user'][] = 'v.shovkoplyas';
$SET['user'][] = 'b.bendiksen';
$SET['user'][] = 'o.brahammar';
$SET['user'][] = 'o.berget';
$SET['user'][] = 'o.solberg';
$SET['user'][] = 'p.baxter';
$SET['user'][] = 'r.stray';
$SET['user'][] = 'k.molteberg';
$SET['user'][] = 'le.eide';
$SET['user'][] = 'c.monnich';
$SET['user'][] = 'h.sporaland';
$SET['user'][] = 'h.finnesand';
$SET['user'][] = 't.kvam';
$SET['user'][] = 'a.hevalo';
$SET['user'][] = 'a.havrylin';
$SET['user'][] = 'v.antal';
$SET['user'][] = 'k.krasnonosova';
$SET['user'][] = 'i.koziy';
$SET['user'][] = 'c.teilman';
$SET['user'][] = 'm.ellen';
$SET['user'][] = 'e.nguyen';
$SET['user'][] = 'g.kovalchuk';
$SET['user'][] = 'e.roraas';

FOREACH($SET['user'] AS $U) {
	IF(!$U) CONTINUE;
	$WHERE = [];
	$WHERE['user'] = $U;
	$RESULT = DB::SELECT('users', $WHERE);
	
	IF($RESULT->fetch_object()) CONTINUE;	
	ECHO $U . PHP_EOL;
	
	$SETDB['user'] = $U;	
	DB::INSERT('users', $SETDB);
}

?>

