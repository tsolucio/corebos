<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'modules/Calendar4You/GoogleSync4You.php';
global $current_user, $mod_strings;

$GoogleSync4You = new GoogleSync4You();
$apikey = $_REQUEST['apikey'];
$keyfile = $_REQUEST['keyfile'];
$clientid = $_REQUEST['clientid'];
$refresh = $_REQUEST['refresh_token'];
$googleinsert = $_REQUEST['googleinsert'];
$login = $_REQUEST['login'];
$pass = $_REQUEST['pass'];

$GoogleSync4You->setAccessData($current_user->id, $login, $apikey, $keyfile, $clientid, $refresh, $googleinsert);

$GoogleSync4You->connectToGoogle();

if ($GoogleSync4You->isLogged()) {
	$data['status'] = 'ok';
	$data['text'] = $mod_strings['LBL_CONNECTING_WORK_CORRECT'];
} else {
	$data['status'] = 'error';
	$data['text'] = $GoogleSync4You->getStatus();
}

echo json_encode($data);
?>
