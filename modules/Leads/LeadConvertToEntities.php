<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************* */
global $current_user, $currentModule, $theme, $app_strings,$log;

require_once 'include/Webservices/ConvertLead.php';
require_once 'include/utils/VtlibUtils.php';
//Getting the Parameters from the ConvertLead Form
$recordId = vtlib_purify($_REQUEST["record"]);
$leadId = vtws_getWebserviceEntityId('Leads', $recordId);
$entityValues=array();
//make sure that either contacts or accounts is selected
if (!empty($_REQUEST['entities'])) {
	$entities=vtlib_purify($_REQUEST['entities']);

	$assigned_to = vtlib_purify($_REQUEST["c_assigntype"]);
	if ($assigned_to == "U") {
		$assigned_user_id = vtlib_purify($_REQUEST["c_assigned_user_id"]);
		$assignedTo = vtws_getWebserviceEntityId('Users', $assigned_user_id);
	} else {
		$assigned_user_id = vtlib_purify($_REQUEST["c_assigned_group_id"]);
		$assignedTo = vtws_getWebserviceEntityId('Groups', $assigned_user_id);
	}

	$transferRelatedRecordsTo = vtlib_purify($_REQUEST['transferto']);
	if (empty($transferRelatedRecordsTo)) {
		$transferRelatedRecordsTo = 'Contacts';
	}
	$entityValues['transferRelatedRecordsTo']=$transferRelatedRecordsTo;
	$entityValues['assignedTo']=$assignedTo;
	$entityValues['leadId']=$leadId;

	if (vtlib_isModuleActive('Accounts')&& in_array('Accounts', $entities)) {
		$entityValues['entities']['Accounts']['create']=true;
		$entityValues['entities']['Accounts']['name']='Accounts';
		$entityValues['entities']['Accounts']['accountname'] = vtlib_purify($_REQUEST['accountname']);
		$entityValues['entities']['Accounts']['industry']=vtlib_purify($_REQUEST['industry']);
	}

	if (vtlib_isModuleActive('Potentials')&& in_array('Potentials', $entities)) {
		$entityValues['entities']['Potentials']['create']=true;
		$entityValues['entities']['Potentials']['name']='Potentials';
		$entityValues['entities']['Potentials']['potentialname']=  vtlib_purify($_REQUEST['potentialname']);
		$entityValues['entities']['Potentials']['closingdate']=  vtlib_purify($_REQUEST['closingdate']);
		$entityValues['entities']['Potentials']['sales_stage']=  vtlib_purify($_REQUEST['sales_stage']);
		$entityValues['entities']['Potentials']['amount']=  vtlib_purify($_REQUEST['amount']);
	}

	if (vtlib_isModuleActive('Contacts')&& in_array('Contacts', $entities)) {
		$entityValues['entities']['Contacts']['create']=true;
		$entityValues['entities']['Contacts']['name']='Contacts';
		$entityValues['entities']['Contacts']['lastname']=  vtlib_purify($_REQUEST['lastname']);
		$entityValues['entities']['Contacts']['firstname']=  vtlib_purify($_REQUEST['firstname']);
		$entityValues['entities']['Contacts']['email']=  vtlib_purify($_REQUEST['email']);
	}

	try {
		$result = vtws_convertlead($entityValues, $current_user);
	} catch (Exception $e) {
		echo "<br><div style='margin:auto;text-align:center;font-weight:bold;'>".$e->message.'</div><br>';
		showError($entityValues);
		die();
	}

	if (isset($result['Accounts'])) {
		$accountIdComponents = vtws_getIdComponents($result['Accounts']);
		$accountId = $accountIdComponents[1];
	} else {
		$accountId = 0;
	}
	if (isset($result['Contacts'])) {
		$contactIdComponents = vtws_getIdComponents($result['Contacts']);
		$contactId = $contactIdComponents[1];
	} else {
		$contactId = 0;
	}
}

if (!empty($accountId)) {
	header("Location: index.php?action=DetailView&module=Accounts&record=$accountId");
} elseif (!empty($contactId)) {
	header("Location: index.php?action=DetailView&module=Contacts&record=$contactId");
} else {
	showError($entityValues);
}

function showError($entityValues) {
	require_once 'include/utils/VtlibUtils.php';
	global $current_user, $currentModule, $theme;
	$theme = vtlib_purify($theme);
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='" . vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>". getTranslatedString('SINGLE_'.$currentModule, $currentModule)." ".
			getTranslatedString('CANNOT_CONVERT', $currentModule) ."
		<br>
		<ul> ". getTranslatedString('LBL_FOLLOWING_ARE_POSSIBLE_REASONS', $currentModule) .":
			<li>". getTranslatedString('LBL_LEADS_FIELD_MAPPING_INCOMPLETE', $currentModule) ."</li>
			<li>". getTranslatedString('LBL_MANDATORY_FIELDS_ARE_EMPTY', $currentModule) ."</li>
		</ul>
		</span>
		</td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>";
	showMandatoryFieldsAndValues($entityValues);
	echo "</td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>";
	if (is_admin($current_user)) {
		echo "<a href='index.php?module=Settings&action=CustomFieldList&parenttab=Settings&formodule=Leads'>".
			getTranslatedString('LBL_LEADS_FIELD_MAPPING', $currentModule) . '</a><br>';
	}
	echo "<a href='javascript:window.history.back();'>". getTranslatedString('LBL_GO_BACK', $currentModule) ."</a><br>";
	echo "</td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
}

function showMandatoryFieldsAndValues($entityValues) {
	global $log,$adb,$current_user;
	$yes = getTranslatedString('LBL_YES');
	$no = getTranslatedString('LBL_NO');
	echo '<table width=100% border=0>';
	echo '<tr><td>'.getTranslatedString('LBL_MANDATORY_FIELDS', 'Settings').'</td></tr>';
	$availableModules = array('Accounts','Contacts','Potentials');
	$leadObject = VtigerWebserviceObject::fromName($adb, 'Leads');
	$handlerPath = $leadObject->getHandlerPath();
	$handlerClass = $leadObject->getHandlerClass();
	require_once $handlerPath;
	$leadHandler = new $handlerClass($leadObject, $current_user, $adb, $log);
	$leadInfo = vtws_retrieve($entityValues['leadId'], $current_user);
	foreach ($availableModules as $entityName) {
		if (!isset($entityValues['entities'][$entityName])) {
			continue;
		}
		$tabid = getTabid($entityName);
		$entityvalue = $entityValues['entities'][$entityName];
		$entityObject = VtigerWebserviceObject::fromName($adb, $entityvalue['name']);
		$handlerPath = $entityObject->getHandlerPath();
		$handlerClass = $entityObject->getHandlerClass();
		require_once $handlerPath;
		$entityHandler = new $handlerClass($entityObject, $current_user, $adb, $log);
		$entityObjectValues = array();
		$entityObjectValues['assigned_user_id'] = $entityValues['assignedTo'];
		$entityObjectValues = vtws_populateConvertLeadEntities($entityvalue, $entityObjectValues, $entityHandler, $leadHandler, $leadInfo);

		echo "<tr><td colspan=3><b>".getTranslatedString($entityvalue['name'], $entityvalue['name'])."</b></td></tr>";
		echo "<tr><td><b>".getTranslatedString('FieldName', 'Settings').
			"</b></td><td><b>".getTranslatedString('LBL_MANDATORY_FIELD', 'Settings').
			"</b></td><td><b>".getTranslatedString('Values', 'Settings')."</b></td></tr>";
		foreach ($entityObjectValues as $fname => $value) {
			if ($fname == 'create' || $fname == 'name') {
				continue;
			}
			echo '<tr>';
			$frs = $adb->pquery('select fieldlabel,typeofdata from vtiger_field where tabid=? and fieldname=?', array($tabid, $fname));
			$finfo = $adb->fetch_array($frs);
			$flbl = getTranslatedString($finfo['fieldlabel'], $entityName);
			echo "<td>$flbl ($fname)</td>";
			echo '<td>'.(strpos($finfo['typeofdata'], '~M') > 0 ? $yes : $no).'</td>';
			echo "<td>$value</td>";
			echo '</tr>';
		}
	}
	echo '</table>';
}
?>
