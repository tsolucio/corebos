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
		case Field_Metadata::UITYPE_LINEITEMS_CURRENCY_AMOUNT:
		case Field_Metadata::UITYPE_NUMERIC:
		case Field_Metadata::UITYPE_PERCENTAGE:
			return 'text'; // implement number
			break;
		default:
			return false;
	}
}

function getEmptyDataGridResponse() {
	return json_encode(
		array(
			'data' => array(
				'contents' => [],
				'pagination' => array(
					'page' => 1,
					'totalCount' => 0,
				),
			),
			'result' => false,
		)
	);
}

function getDataGridResponse($mdmap) {
	global $adb, $current_user;
	$qg = new QueryGenerator($mdmap['targetmodule'], $current_user);
	$qg->setFields(array_merge(['id'], $mdmap['listview']['fieldnames']));
	$qg->addReferenceModuleFieldCondition($mdmap['originmodule'], $mdmap['linkfields']['targetfield'], 'id', vtlib_purify($_REQUEST['pid']), 'e', QueryGenerator::$AND);
	$sql = $qg->getQuery(); // No conditions
	$countsql = mkCountQuery($sql);
	$rs = $adb->query($countsql);
	$count = $rs->fields['count'];
	// if we have to support filtering we would have to add the condtions to $qg here
	if (!empty($mdmap['sortfield'])) {
		$sql .= ' ORDER BY '.$qg->getOrderByColumn($mdmap['sortfield']). ' asc';
	}
	$rs = $adb->query($sql);
	$ret = array();
	while ($row = $adb->fetch_array($rs)) {
		$r = array(
			'record_module' => $mdmap['targetmodule'],
			'record_id' => $row[$mdmap['targetmoduleidfield']],
		);
		foreach ($mdmap['listview']['fields'] as $finfo) {
			$r[$finfo['fieldinfo']['name']] = getDataGridValue($mdmap['targetmodule'], $row[$mdmap['targetmoduleidfield']], $finfo, $row[$finfo['fieldinfo']['columnname']]);
		}
		$ret[] = $r;
	}
	return json_encode(
		array(
			'data' => array(
				'contents' => $ret,
				'pagination' => array(
					'page' => 1,
					'totalCount' => $count,
				),
			),
			'result' => true,
		)
	);
}

function getDataGridValue($module, $recordID, $fieldinfo, $fieldValue) {
	global $current_user, $adb;
	static $ownerNameList = array();
	$fieldtype = $fieldinfo['fieldtype'];
	$fieldinfo = $fieldinfo['fieldinfo'];
	$fieldName = $fieldinfo['name'];
	switch ($fieldinfo['uitype']) {
		case Field_Metadata::UITYPE_CHECKBOX:
			if ($fieldValue == 1) {
				$return = getTranslatedString('yes', $module);
			} elseif ($fieldValue == 0) {
				$return = getTranslatedString('no', $module);
			} else {
				$return = '--';
			}
			break;
		case Field_Metadata::UITYPE_DOWNLOAD_TYPE:
			if ($fieldValue == 'I') {
				$return = getTranslatedString('LBL_INTERNAL', $module);
			} elseif ($fieldValue == 'E') {
				$return = getTranslatedString('LBL_EXTERNAL', $module);
			} else {
				$return = '--';
			}
			break;
		case Field_Metadata::UITYPE_IMAGE:
			if ($module == 'Contacts' && $fieldinfo['columnname']=='imagename') {
				$imageattachment = 'Image';
			} else {
				$imageattachment = 'Attachment';
			}
			//$imgpath = getModuleFileStoragePath('Contacts').$col_fields[$fieldname];
			$sql = "select vtiger_attachments.*,vtiger_crmentity.setype
				from vtiger_attachments
				inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
				where vtiger_crmentity.setype='$module $imageattachment' and vtiger_attachments.name=? and vtiger_seattachmentsrel.crmid=?";
			$image_res = $adb->pquery($sql, array(str_replace(' ', '_', $fieldValue), $recordID));
			$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
			$image_path = $adb->query_result($image_res, 0, 'path');
			$image_name = decode_html($adb->query_result($image_res, 0, 'name'));
			$imgpath = $image_path . $image_id . '_' . urlencode($image_name);
			if ($image_name != '') {
				$ftype = $adb->query_result($image_res, 0, 'type');
				$isimage = stripos($ftype, 'image') !== false;
				if ($isimage) {
					$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('Image');
					$return = '<div style="width:100%;text-align:center;"><img src="' . $imgpath . '" alt="' . $imgtxt . '" title= "'.
						$imgtxt . '" style="max-width: 50px;"></div>';
				} else {
					$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('SINGLE_Documents');
					$return = '<a href="' . $imgpath . '" alt="' . $imgtxt . '" title= "' . $imgtxt . '" target="_blank">'.$image_name.'</a>';
				}
			} else {
				$return = '';
			}
			break;
		case Field_Metadata::UITYPE_RECORD_RELATION:
			$field10Value = '';
			if (!empty($fieldValue)) {
				$parent_module = getSalesEntityType($fieldValue);
				$displayValueArray = getEntityName($parent_module, $fieldValue);
				if (!empty($displayValueArray)) {
					$field10Value = '<a href="index.php?action=DetailView&module='.$parent_module.'&record='.$fieldValue.'">'.$displayValueArray[$fieldValue].'</a>';
				}
				$linkRow[$fieldName] = array($parent_module, $fieldValue, $field10Value);
			}
			$return = $field10Value;
			break;
		case Field_Metadata::UITYPE_PICKLIST:
		case Field_Metadata::UITYPE_ROLE_BASED_PICKLIST:
			$return = textlength_check(getTranslatedString($fieldValue, $module));
			break;
		case Field_Metadata::UITYPE_MULTI_SELECT:
			$return = textlength_check(($fieldValue != '') ? str_replace(' |##| ', ', ', $fieldValue) : '');
			break;
		case Field_Metadata::UITYPE_PICKLIST_MODS:
		case Field_Metadata::UITYPE_PICKLIST_MODEXTS:
			$return = textlength_check(getTranslatedString($fieldValue, $fieldValue));
			break;
		case Field_Metadata::UITYPE_MULTI_SELECT_MODS:
		case Field_Metadata::UITYPE_MULTI_SELECT_MODEXTS:
			$modlist = explode(' |##| ', $fieldValue);
			$modlist = array_map(
				function ($m) {
					return getTranslatedString($m, $m);
				},
				$modlist
			);
			$return = textlength_check(($fieldValue != '') ? implode(', ', $modlist) : '');
			break;
		case Field_Metadata::UITYPE_PICKLIST_ROLES:
			$return = textlength_check(($fieldValue != '') ? implode(', ', array_map('getRoleName', explode(' |##| ', $fieldValue))) : '');
			break;
		case Field_Metadata::UITYPE_INTERNAL_TIME:
		case Field_Metadata::UITYPE_DATE:
		case Field_Metadata::UITYPE_DATE_TIME:
			if (!empty($fieldValue) && $fieldValue != '0000-00-00' && $fieldValue != '0000-00-00 00:00') {
				$date = new DateTimeField($fieldValue);
				$return = $date->getDisplayDate();
				if ($fieldinfo['uitype'] != Field_Metadata::UITYPE_DATE) {
					$return .= ' ' . $date->getDisplayTime();
					$user_format = ($current_user->hour_format=='24' ? '24' : '12');
					if ($user_format != '24') {
						$curr_time = DateTimeField::formatUserTimeString($return, '12');
						$time_format = substr($curr_time, -2);
						$curr_time = substr($curr_time, 0, 5);
						list($dt,$tm) = explode(' ', $return);
						$return = $dt . ' ' . $curr_time . $time_format;
					}
				}
			} elseif (empty($fieldValue) || $fieldValue == '0000-00-00' || $fieldValue == '0000-00-00 00:00') {
				$return = '';
			}
			break;
		case Field_Metadata::UITYPE_TIME:
			$date = new DateTimeField($fieldValue);
			$return = $date->getDisplayTime($current_user);
			break;
		case Field_Metadata::UITYPE_ASSIGNED_TO_PICKLIST:
		case Field_Metadata::UITYPE_USER_REFERENCE:
			if (!isset($ownerNameList[$fieldValue])) {
				$ownerName = getOwnerNameList([$fieldValue]);
				$ownerNameList[$fieldValue] = $ownerName[$fieldValue];
			}
			$return = textlength_check($ownerNameList[$fieldValue]);
			break;
		case Field_Metadata::UITYPE_CURRENCY_AMOUNT:
		case Field_Metadata::UITYPE_LINEITEMS_CURRENCY_AMOUNT:
			if ($fieldName == 'unit_price') {
				$currencyId = getProductBaseCurrency($recordID, $module);
				$cursym_convrate = getCurrencySymbolandCRate($currencyId);
				$currencySymbol = $cursym_convrate['symbol'];
			} else {
				$currencyInfo = getInventoryCurrencyInfo($module, $recordID);
				$currencySymbol = $currencyInfo['currency_symbol'];
			}
			$currencyValue = CurrencyField::convertToUserFormat($fieldValue, null, true);
			$return = CurrencyField::appendCurrencySymbol($currencyValue, $currencySymbol);
			break;
		case Field_Metadata::UITYPE_NUMERIC:
		case Field_Metadata::UITYPE_PERCENTAGE:
			$return = CurrencyField::convertToUserFormat($fieldValue);
			break;
		case Field_Metadata::UITYPE_EMAIL:
			if ($_SESSION['internal_mailer'] == 1) {
				$return = "<a href=\"javascript:InternalMailer($recordID, ".$fieldinfo['fieldid']. ','
					."'$fieldName','$module','record_id');\">".textlength_check($fieldValue).'</a>';
			} else {
				$return = '<a href="mailto:'.$fieldValue.'">'.textlength_check($fieldValue).'</a>';
			}
			break;
		case Field_Metadata::UITYPE_URL:
			preg_match('^[\w]+:\/\/^', $fieldValue, $matches);
			if (!empty($matches[0])) {
				$return = '<a href="'.$fieldValue.'" target="_blank">'.textlength_check($fieldValue).'</a>';
			} else {
				$return = '<a href="http://'.$fieldValue.'" target="_blank">'.textlength_check($fieldValue).'</a>';
			}
			break;
		case Field_Metadata::UITYPE_RECORD_NO:
		case Field_Metadata::UITYPE_LASTNAME:
		case Field_Metadata::UITYPE_NAME:
		case Field_Metadata::UITYPE_PHONE:
		case Field_Metadata::UITYPE_SKYPE:
		case Field_Metadata::UITYPE_TEXT:
		default:
			$return = $fieldValue;
	}
	return $return;
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
