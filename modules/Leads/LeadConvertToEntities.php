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
$recordId = vtlib_purify($_REQUEST['record']);
$leadId = vtws_getWebserviceEntityId('Leads', $recordId);
$entityValues=array();
//make sure that either contacts or accounts is selected
if (!empty($_REQUEST['entities'])) {
	$entities=(array)vtlib_purify($_REQUEST['entities']);

	$assigned_to = vtlib_purify($_REQUEST['c_assigntype']);
	if ($assigned_to == 'U') {
		$assigned_user_id = vtlib_purify($_REQUEST['c_assigned_user_id']);
		$assignedTo = vtws_getWebserviceEntityId('Users', $assigned_user_id);
	} else {
		$assigned_user_id = vtlib_purify($_REQUEST['c_assigned_group_id']);
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
		showError($entityValues, $e->message);
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

function showError($entityValues, $errmsg = '') {
	require_once 'include/utils/VtlibUtils.php';
	global $current_user, $currentModule, $theme, $default_charset;
	$theme = vtlib_purify($theme);
	$convlead = getTranslatedString('CANNOT_CONVERT', $currentModule);
	$companyDetails = retrieveCompanyDetails();
	$favicon = $companyDetails['favicon'];
	echo <<< EOT
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset={$default_charset}">
			<meta name="robots" content="noindex">
			<title>{$convlead} Error</title>
			<link REL="SHORTCUT ICON" HREF="{$favicon}">
			<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />';
			<script type="text/javascript" src="include/js/general.js"></script>
		</head>
		<body>
		<div class="slds-card" style="width: 55%;margin:auto;">
		<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_error" role="alert">
		  <header class="slds-expression__title">
			<span class="slds-icon_container slds-icon-utility-error slds-m-right_x-small">
			<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#error"></use>
			</svg>
			</span>
EOT;
		echo '<h2 class="slds-modal__title">'.getTranslatedString('SINGLE_'.$currentModule, $currentModule).' '.$convlead.'</h2>
		  </header>
		  <div class="slds-expression__title slds-p-around_medium">'
			.getTranslatedString('LBL_FOLLOWING_ARE_POSSIBLE_REASONS', $currentModule) .':'
			.'<ul class="slds-list_dotted slds-text-align_left">'
			.(empty($errmsg) ? '' : '<li>'.$errmsg.'</li>')
			.'<li>'. getTranslatedString('LBL_LEADS_FIELD_MAPPING_INCOMPLETE', $currentModule) .'</li>
			<li>'. getTranslatedString('LBL_MANDATORY_FIELDS_ARE_EMPTY', $currentModule) .'</li>
			</ul>
		  </div>
		</div>';
	showMandatoryFieldsAndValues($entityValues);
	echo '<div class="slds-align_absolute-center slds-p-around_large">';
	if (is_admin($current_user)) {
		echo "<button class='slds-button slds-button_outline-brand' type='button' onclick='gotourl(\"index.php?module=Settings&action=CustomFieldList&formodule=Leads\")'>"
			.getTranslatedString('LBL_LEADS_FIELD_MAPPING', $currentModule) . '</button>';
	}
	echo "<button class='slds-button slds-button_outline-brand' type='button' onclick='window.history.back();'>". getTranslatedString('LBL_GO_BACK', $currentModule) .'</button><br>';
	echo '</div></div></body></html>';
}

function showMandatoryFieldsAndValues($entityValues) {
	global $log,$adb,$current_user;
	$yes = getTranslatedString('LBL_YES');
	$no = getTranslatedString('LBL_NO');
	echo '<h2 class="slds-modal__title slds-p-around_small">'.getTranslatedString('LBL_MANDATORY_FIELDS', 'Settings').'</h2>';
	echo '<table class="slds-table slds-table_cell-buffer slds-table_bordered">';
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

		echo '<tr><td colspan=3><b>'.getTranslatedString($entityvalue['name'], $entityvalue['name']).'</b></td></tr>';
		echo '<tr><td><b>'.getTranslatedString('FieldName', 'Settings').
			'</b></td><td><b>'.getTranslatedString('LBL_MANDATORY_FIELD', 'Settings').
			'</b></td><td><b>'.getTranslatedString('Values', 'Settings').'</b></td></tr>';
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
