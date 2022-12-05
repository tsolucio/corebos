<?php
/*************************************************************************************************
 * Copyright 2022 AT Consulting. -- This file is a part of coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. AT Consulting. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
require_once 'Smarty_setup.php';
global $adb;

$good = false;
if (!empty($_REQUEST['minfo']) && is_numeric($_REQUEST['minfo'])) {
	$check = $adb->pquery(
		'select * from vtiger_cbprocessinfo
		inner join vtiger_crmobject on crmid=cbprocessinfoid
		where crmid=? and setype=? and deleted=0 and active=1',
		[$_REQUEST['minfo'], 'cbProcessInfo']
	);
	if ($check && $adb->num_rows($check)==1) {
		$saveinfo = [
			'tostate' => isset($_REQUEST['tostate']) ? $_REQUEST['tostate'] : '',
			'fieldName' => isset($_REQUEST['fieldName']) ? $_REQUEST['fieldName'] : '',
			'bpmmodule' => isset($_REQUEST['bpmmodule']) ? $_REQUEST['bpmmodule'] : '',
			'uitype' => isset($_REQUEST['uitype']) ? $_REQUEST['uitype'] : '',
			'editmode' => isset($_REQUEST['editmode']) ? $_REQUEST['editmode'] : '',
			'pflowid' => isset($_REQUEST['pflowid']) ? $_REQUEST['pflowid'] : '',
			'bpmrecord' => isset($_REQUEST['bpmrecord']) ? $_REQUEST['bpmrecord'] : '',
			'formName' => isset($_REQUEST['formName']) ? $_REQUEST['formName'] : '',
			'actionName' => isset($_REQUEST['actionName']) ? $_REQUEST['actionName'] : '',
			'originField' => '',
		];
		$MapObject = new cbMap();
		$Mapid = $check->fields['fieldmap'];
		$refrenceField = '';
		if ($check->fields['fieldmap']) {
			$MapObject->id = $Mapid;
			$MapObject->mode = '';
			$MapObject->retrieve_entity_info($Mapid, 'cbMap');
			$xml = simplexml_load_string($MapObject->column_fields['content']);
			$refrenceField = (string)$xml->linkfields->targetfield;
			$saveinfo['originField'] = (string)$xml->linkfields->originfield;
		}
		$saveinfo = json_encode($saveinfo);
		$recordID = '';
		$qg = new QueryGenerator($check->fields['semodule'], $current_user);
		$qg->setFields(array('id'));
		$qg->addReferenceModuleFieldCondition($_REQUEST['bpmmodule'], $refrenceField, 'id', $_REQUEST['bpmrecord'], 'e');
		$sql = $qg->getQuery();
		$rs = $adb->query($sql);
		$fields = $rs->fields;
		if ($fields) {
			$recordID = $fields['0'];
		}
		$FFMName = getEntityName('cbMap', $check->fields['fieldmap']);
		$FFMName = $FFMName[$check->fields['fieldmap']];
		$saveAction = (empty($_REQUEST['pflowid']) ? 'finishProcessInfo' : 'bpmsaveinfo');
		$url = 'module='.$check->fields['semodule'].'&action=EditView&Module_Popup_Edit=1&MDCurrentRecord='.$_REQUEST['bpmrecord'];
		$url.= '&record='.$recordID.'&FILTERFIELDSMAP='.$FFMName.'&FILTERVALMAP='.$check->fields['valmap'].'&'.$refrenceField.'='.$_REQUEST['bpmrecord'];
		$url.= '&FILTERDEPMAP='.$check->fields['depmap'].'&Module_Popup_Save='.$saveAction.'&Module_Popup_Save_Param='.urlencode($saveinfo);
		header('Location: index.php?' . $url);
		die();
	}
}
if (!$good) {
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APMSG_LOADLDS', 1);
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('MoreInfoNotFound', 'cbProcessInfo'));
	$smarty->display('applicationmessage.tpl');
}
?>