<?php
/* This script shows how much faster FetchRow is wrt fetch_array
	and FetchRow returns the values in UTF8 so we don't have to decode them!!
	my test install has a little over 15000 translation records
joe@joelinux /var/www/coreBOSwork $ php chkadbfetch.php 
 Server response time: 0.21 seconds.
 Server response time: 0.1 seconds.
joe@joelinux /var/www/coreBOSwork $ php chkadbfetch.php 
 Server response time: 0.21 seconds.
 Server response time: 0.1 seconds.
joe@joelinux /var/www/coreBOSwork $ php chkadbfetch.php 
 Server response time: 0.21 seconds.
 Server response time: 0.08 seconds.
joe@joelinux /var/www/coreBOSwork $ php chkadbfetch.php 
 Server response time: 0.21 seconds.
 Server response time: 0.08 seconds.
joe@joelinux /var/www/coreBOSwork $ php chkadbfetch.php 
 Server response time: 0.21 seconds.
 Server response time: 0.11 seconds.
*/
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$usr = new Users();
$current_user = Users::getActiveAdminUser();

//$rs = $adb->query('select translation_key,i18n from vtiger_cbtranslation where cbtranslationid between 28428 and 28439');
$rs = $adb->query('select translation_key,i18n from vtiger_cbtranslation');
$startTime = microtime(true);
$l1 = array();
while ($c = $adb->fetch_array($rs)) {
	$cl = $c['translation_key'];
	$l1[] = $c;
	//var_dump($c);
	//echo $cl."\n";
}
$endTime = microtime(true);
$deltaTime = round($endTime - $startTime, 2);
echo(' Server response time: '.$deltaTime.' seconds.'."\n");

//$rs = $adb->query('select translation_key,i18n from vtiger_cbtranslation where cbtranslationid between 28428 and 28439');
$rs = $adb->query('select translation_key,i18n from vtiger_cbtranslation');
$startTime = microtime(true);
$l2 = array();
while (!$rs->EOF) {
	$cl = $rs->FetchRow();
	$l2[] = $cl;
	//var_dump($cl);
}
$endTime = microtime(true);
$deltaTime = round($endTime - $startTime, 2);
echo(' Server response time: '.$deltaTime.' seconds.'."\n");

// if ($l1 === $l2) {
// 	echo "iguales";
// } else {
// 	echo "distintos";
// }
// var_dump($l1,$l2);
?>
