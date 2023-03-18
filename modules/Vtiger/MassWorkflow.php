<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: TSolucio Open Source
 * The Initial Developer of the Original Code is TSolucio.
 * Portions created by TSolucio are Copyright (C) TSolucio.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'include/Webservices/ExecuteWorkflow.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
set_time_limit(0);

global $app_strings;

function send_message($id, $message, $progress, $processed, $total, $refreshLV = false) {
	$d = array('message' => $message , 'progress' => $progress, 'processed' => $processed, 'total' => $total, 'refreshLV' => $refreshLV);
	echo "id: $id" . PHP_EOL;
	echo 'data:'. json_encode($d) . PHP_EOL;
	echo PHP_EOL;
	ob_flush();
	flush();
}

$params = json_decode(vtlib_purify($_REQUEST['params']), true);
$module = $params['module'];
$msginfo = json_decode(vtlib_purify(coreBOS_Settings::getSetting('runBAScript'.$params['corebos_browsertabID'], null)), true);
$idlist = $msginfo['ids'];
$viewid = isset($msginfo['viewname']) ? vtlib_purify($msginfo['viewname']) : '';
$ListViewSSEParameters = isset($msginfo['ListViewSSEParameters']) ? json_decode(vtlib_purify($msginfo['ListViewSSEParameters']), true) : [];
$recordcount = 0;
if (!empty($idlist) && !empty($ListViewSSEParameters) && is_numeric($ListViewSSEParameters[0])) {
	$_REQUEST['action'] = 'MassWorkflow';
	$wfid = $ListViewSSEParameters[0];
	// Replacing params action value
	$_REQUEST['params'] = preg_replace('/"action":""/', '"action":"MassWorkflow"', $_REQUEST['params']);
	$idlist = trim($idlist, ';');
	$recordids = explode(';', $idlist);
	$recordcount = count($recordids);
	$reclink = '<a target="_blank" href="index.php?module='.$module.'&action=DetailView&record=';
	$id = 1;
	$recordprocessed = 0;
	$wsid = vtws_getEntityId($module).'x';
	for ($index = 0; $index < $recordcount; ++$index) {
		$recordid = $recordids[$index];
		if ($recordid == '') {
			continue;
		}
		$recname = getEntityName($module, $recordid);
		$recname = $reclink.$recordid.'">'.$recname[$recordid].'</a>';
		try {
			cbwsExecuteWorkflowWithContext($wfid, json_encode([$wsid.$recordid]), '[]', $current_user);
			$msg = $app_strings['record'].' '.$recname.' '.$app_strings['processed'];
		} catch (Exception $e) {
			$msg = $app_strings['record'].' '.$recname.' '.$app_strings['notprocessed'].':'.$e->getMessage();
		}
		$recordprocessed++;
		$progress = round($recordprocessed / $recordcount * 100, 0);
		send_message($id++, $msg, $progress, $recordprocessed, $recordcount);
	}
}
send_message('CLOSE', $app_strings['processcomplete'], 100, $recordcount, $recordcount, true);
coreBOS_Settings::delSetting('runBAScript'.$params['corebos_browsertabID']);
?>
