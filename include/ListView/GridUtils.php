<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by vtiger are Copyright (C) coreBOS.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'include/fields/metainformation.php';
require_once 'modules/PickList/PickListUtils.php';

function gridGetEditor($module, $fieldname, $uitype) {
	global $current_user, $adb, $noof_group_rows;
	$userprivs = $current_user->getPrivileges();
	switch ($uitype) {
		case Field_Metadata::UITYPE_CHECKBOX:
			return [
				'type' => 'radio',
				'options' => [
					'listItems' => [
						[ 'text' => getTranslatedString('yes'), 'value' => '1' ],
						[ 'text' => getTranslatedString('no'), 'value' => '0' ],
					]
				]
			];
			break;
		case Field_Metadata::UITYPE_RECORD_NO:
		case Field_Metadata::UITYPE_RECORD_RELATION:
		case Field_Metadata::UITYPE_INTERNAL_TIME:
			return false;
			break;
		case Field_Metadata::UITYPE_PICKLIST:
		case Field_Metadata::UITYPE_ROLE_BASED_PICKLIST:
			$listItems = [];
			foreach (getAssignedPicklistValues($fieldname, $current_user->roleid, $adb) as $key => $value) {
				$listItems[] = [
					'text' => $value,
					'value' => $key,
				];
			}
			return [
				'type' => 'select',
				'options' => [
					'listItems' => $listItems,
				]
			];
			break;
		case Field_Metadata::UITYPE_DATE_TIME:
			//($current_user->hour_format=='24' ? '24' : 'am/pm');
			return [
				'type' => 'datePicker',
				'options' => [
					'format' => strtr($current_user->date_format, 'm', 'M').' HH:mm A',
					'timepicker' => true
				]
			];
			break;
		case Field_Metadata::UITYPE_DATE:
			return [
				'type' => 'datePicker',
				'options' => [
					'format' => strtr($current_user->date_format, 'm', 'M')
				]
			];
			break;
		case Field_Metadata::UITYPE_ASSIGNED_TO_PICKLIST:
			$ga = array();
			if ($fieldname == 'assigned_user_id' && !$userprivs->hasGlobalWritePermission() && !$userprivs->hasModuleWriteSharing(getTabid($module))) {
				get_current_user_access_groups($module); // calculate global variable $noof_group_rows
				if ($noof_group_rows!=0) {
					$ga = get_group_array(false, 'Active', $current_user->id, 'private');
				}
				$ua = get_user_array(false, 'Active', $current_user->id, 'private');
			} else {
				get_group_options();// calculate global variable $noof_group_rows
				if ($noof_group_rows!=0) {
					$ga = get_group_array(false, 'Active', $current_user->id);
				}
				$ua = get_user_array(false, 'Active', $current_user->id);
			}
			$listItems = [];
			foreach (array_merge($ua, $ga) as $key => $value) {
				$listItems[] = [
					'text' => $value,
					'value' => $key,
				];
			}
			return [
				'type' => 'select',
				'options' => [
					'listItems' => $listItems
				]
			];
			break;
		case Field_Metadata::UITYPE_EMAIL:
		case Field_Metadata::UITYPE_LASTNAME:
		case Field_Metadata::UITYPE_NAME:
		case Field_Metadata::UITYPE_PHONE:
		case Field_Metadata::UITYPE_SKYPE:
		case Field_Metadata::UITYPE_TEXT:
		case Field_Metadata::UITYPE_TIME:
		case Field_Metadata::UITYPE_URL:
			return 'text';
			break;
		case Field_Metadata::UITYPE_CURRENCY_AMOUNT:
		case Field_Metadata::UITYPE_NUMERIC:
		case Field_Metadata::UITYPE_PERCENTAGE:
			return 'number';
			break;
		default:
			return '';
	}
}

function gridGetActionColumn($renderer, $actions) {
	if ($actions['moveup'] || $actions['movedown'] || $actions['delete'] || $actions['edit']) {
		return [
			'name' => 'cblvactioncolumn',
			'header' => getTranslatedString('LBL_ACTION'),
			'sortable' => false,
			'whiteSpace' => 'normal',
			'width' => 140,
			'renderer' => [
				'type' => $renderer, // 'ActionRender', 'mdActionRender',
				'options' => [
					'moveup' => $actions['moveup'],
					'movedown' => $actions['movedown'],
					'edit' => $actions['edit'],
					'delete' => $actions['delete'],
				]
			],
		];
	}
	return '';
}

function gridDeleteRow($adb, $request) {
}

function gridMoveRowUpDown($adb, $request) {
	$direction = $request['movedirection'];
	$task_id = $request['wftaskid'];
	$wfrs = $adb->pquery('select workflow_id,executionorder from com_vtiger_workflowtasks where task_id=?', array($task_id));
	$wfid = $adb->query_result($wfrs, 0, 'workflow_id');
	$order = $adb->query_result($wfrs, 0, 'executionorder');
	$chgtsk = 'update com_vtiger_workflowtasks set executionorder=? where executionorder=? and workflow_id=?';
	$movtsk = 'update com_vtiger_workflowtasks set executionorder=? where task_id=?';
	if ($direction=='UP') {
		$chgtskparams = array($order,$order-1, $wfid);
		$adb->pquery($chgtsk, $chgtskparams);
		$adb->pquery($movtsk, array($order-1, $task_id));
	} else {
		$chgtskparams = array($order,$order+1 ,$wfid);
		$adb->pquery($chgtsk, $chgtskparams);
		$adb->pquery($movtsk, array($order+1, $task_id));
	}
	echo 'ok';
}
