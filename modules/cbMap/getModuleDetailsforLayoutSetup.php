<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'vtlib/Vtiger/Field.php';

if (!empty($_REQUEST['selmodule']) && !empty($_REQUEST['query'])) {
	$selectedModule = $_REQUEST['selmodule'];
	if ($_REQUEST['query'] == 'blocks') {
		$result = retrieveBlockInfoByModule($selectedModule);
		echo json_encode($result);
	}
	if ($_REQUEST['query'] == 'fields') {
		$fields = retrieveModuleFields($selectedModule);
		echo json_encode($fields);
	}
	if ($_REQUEST['query'] == 'relatedlist') {
		$rellist = getRelatedListDetails($selectedModule);
		$response  = !empty($rellist) ? $rellist : array();
		echo json_encode($response);
	}
}

function retrieveBlockInfoByModule($selmodule) {
	global $adb;
	$lockInfoArr = array();
	$result = $adb->pquery('select blockid, blocklabel,sequence from vtiger_blocks where tabid=? and visible=0', array(getTabid($selmodule)));
	$noofrows = $adb->num_rows($result);
	for ($i = 0; $i < $noofrows; $i++) {
		$translatedBlocklabel = $adb->query_result($result, $i, 'blocklabel');
		$translatedBlocklabel = getTranslatedString($translatedBlocklabel, $selmodule);
		$blockInfo = array(
			'blockid' => $adb->query_result($result, $i, 'blockid'),
			'blocklabel' => $translatedBlocklabel,
			'sequence' => $adb->query_result($result, $i, 'sequence')
		);
		$lockInfoArr[] = $blockInfo;
	}
	return $lockInfoArr;
}

function retrieveModuleFields($selmodule) {
	global $adb;
	$fieldsArr = array();
	$result = $adb->pquery('select fieldid, fieldname, fieldlabel from vtiger_field where tabid=?', array(getTabid($selmodule)));
	$noofrows = $adb->num_rows($result);
	for ($i = 0; $i < $noofrows; $i++) {
		$translatedfieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$translatedfieldlabel = getTranslatedString($translatedfieldlabel, $selmodule);
		$fieldInfo = array(
			'fieldid' => $adb->query_result($result, $i, 'fieldid'),
			'fieldname' => $adb->query_result($result, $i, 'fieldname'),
			'fieldlabel' => $translatedfieldlabel
		);
		$fieldsArr[] = $fieldInfo;
	}
	return $fieldsArr;
}

function getRelatedListDetails($selmodule) {
	global $adb;
	$relinfo = $adb->pquery(
		'select name,label,related_tabid
		from vtiger_relatedlists
		left join vtiger_tab on vtiger_relatedlists.related_tabid=vtiger_tab.tabid and vtiger_tab.presence=0
		where vtiger_relatedlists.tabid=? order by sequence',
		array(getTabid($selmodule))
	);
	$noofrows = $adb->num_rows($relinfo);
	$rellistinfo = array();
	for ($i=0; $i<$noofrows; $i++) {
		$rellistinfo[$i]['name'] = $adb->query_result($relinfo, $i, 'name');
		$label = $adb->query_result($relinfo, $i, 'label');
		$relatedModule = getTabname($adb->query_result($relinfo, $i, 'related_tabid'));
		$rellistinfo[$i]['label'] = (empty($relatedModule) ? getTranslatedString($label, $selmodule) : getTranslatedString($label, $relatedModule));
	}
	return $rellistinfo;
}
?>