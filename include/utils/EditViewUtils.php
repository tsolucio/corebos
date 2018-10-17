<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/ComboUtil.php';
require_once 'include/utils/CommonUtils.php';
require_once 'modules/PickList/DependentPickListUtils.php';

/** This function returns the field details for a given fieldname.
  * Param $uitype - UI type of the field
  * Param $fieldname - Form field name
  * Param $fieldlabel - Form field label name
  * Param $maxlength - maximum length of the field
  * Param $col_fields - array contains the fieldname and values
  * Param $generatedtype - Field generated type (default is 1)
  * Param $module_name - module name
  * Return type is an array
  */
function getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields, $generatedtype, $module_name, $mode = '', $typeofdata = null, $cbMapFI = array()) {
	global $log,$app_strings, $adb,$default_charset, $current_user;
	$log->debug('getOutputHtml('.$uitype.",". $fieldname.",". $fieldlabel.",". $maxlength.",". print_r($col_fields, true).",".$generatedtype.",".$module_name.')');

	require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';

	$fieldvalue = array();
	$final_arr = array();
	$value = $col_fields[$fieldname];
	$ui_type[]= $uitype;
	$editview_fldname[] = $fieldname;

	// vtlib customization: Related type field
	if ($uitype == '10') {
		$fldmod_result = $adb->pquery(
			'SELECT relmodule, status
			FROM vtiger_fieldmodulerel
			INNER JOIN vtiger_tab ON vtiger_fieldmodulerel.relmodule=vtiger_tab.name and vtiger_tab.presence=0
			WHERE fieldid=
				(SELECT fieldid FROM vtiger_field, vtiger_tab
				WHERE vtiger_field.tabid=vtiger_tab.tabid AND fieldname=? AND name=? and vtiger_field.presence in (0,2) and vtiger_tab.presence=0)
			order by sequence',
			array($fieldname, $module_name)
		);
		$entityTypes = array();
		$parent_id = $value;
		for ($index = 0; $index < $adb->num_rows($fldmod_result); ++$index) {
			$entityTypes[] = $adb->query_result($fldmod_result, $index, 'relmodule');
		}

		if (!empty($value)) {
			if (strpos($value, 'x')) {
				list($wsid, $value) = explode('x', $value);
			}
			if ($adb->num_rows($fldmod_result)==1) {
				$valueType = $adb->query_result($fldmod_result, 0, 0);
			} else {
				$valueType = getSalesEntityType($value);
			}
			$displayValueArray = getEntityName($valueType, $value);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $value) {
					$displayValue = $value;
				}
			} else {
				$displayValue='';
				$valueType='';
				$value='';
				$parent_id = '';
			}
		} else {
			$displayValue='';
			$valueType='';
			$value='';
			$parent_id = '';
		}

		$editview_label[] = array('options'=>$entityTypes, 'selected'=>$valueType, 'displaylabel'=>getTranslatedString($fieldlabel, $module_name));
		$fieldvalue[] = array('displayvalue'=>$displayValue,'entityid'=>$parent_id);
	} elseif ($uitype == 5 || $uitype == 6 || $uitype ==23) {
		$log->info("uitype is ".$uitype);
		$curr_time = '';
		if ($value == '') {
			if ($fieldname != 'birthday' && $generatedtype != 2 && getTabid($module_name) != 14) {
				$disp_value = getNewDisplayDate();
			}

			//Added to display the Contact - Support End Date as one year future instead of
			//today's date -- 30-11-2005
			if ($fieldname == 'support_end_date' && $_REQUEST['module'] == 'Contacts') {
				$addyear = strtotime("+1 year");
				$disp_value = DateTimeField::convertToUserFormat(date('Y-m-d', $addyear));
			} elseif ($fieldname == 'validtill' && $_REQUEST['module'] == 'Quotes') {
				$disp_value = '';
			}
		} else {
			if ($uitype == 6) {
				$curr_time = date('H:i', strtotime('+5 minutes'));
			}
			$date = new DateTimeField($value);
			$isodate = $date->convertToDBFormat($value);
			$date = new DateTimeField($isodate);
			$disp_value = $date->getDisplayDate();
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);

		if (empty($disp_value)) {
			$disp_value = '';
		}
		$fieldvalue[] = array($disp_value => $curr_time);
		if ($uitype == 5 || $uitype == 23) {
			$fieldvalue[] = array($date_format=>$current_user->date_format);
		} else {
			$fieldvalue[] = array($date_format=>$current_user->date_format.' '.$app_strings['YEAR_MONTH_DATE']);
		}
	} elseif ($uitype == 50) {
		$user_format = ($current_user->hour_format=='24' ? '24' : '12');
		if (empty($value)) {
			if ($generatedtype != 2) {
				$date = new DateTimeField();
				$disp_value = substr($date->getDisplayDateTimeValue(), 0, 16);
				$curr_time = DateTimeField::formatUserTimeString($disp_value, $user_format);
				if (strlen($curr_time)>5) {
					$time_format = substr($curr_time, -2);
					$curr_time = substr($curr_time, 0, 5);
				} else {
					$time_format = '24';
				}
				list($dt,$tm) = explode(' ', $disp_value);
				$disp_value12 = $dt . ' ' . $curr_time;
			} else {
				$disp_value = $disp_value12 = $curr_time = $time_format = '';
			}
		} else {
			$date = new DateTimeField($value);
			$disp_value = substr($date->getDisplayDateTimeValue(), 0, 16);
			$curr_time = DateTimeField::formatUserTimeString($disp_value, $user_format);
			if (strlen($curr_time)>5) {
				$time_format = substr($curr_time, -2);
				$curr_time = substr($curr_time, 0, 5);
			} else {
				$time_format = '24';
			}
			list($dt,$tm) = explode(' ', $disp_value);
			$disp_value12 = $dt . ' ' . $curr_time;
		}
		$value = $disp_value;
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']).' '.($current_user->hour_format=='24' ? '%H' : '%I').':%M';
		$fieldvalue[] = array($disp_value => $disp_value12);
		$fieldvalue[] = array($date_format=>$current_user->date_format.' '.($current_user->hour_format=='24' ? '24' : 'am/pm'));
		$fieldvalue[] = array($user_format => $time_format);
	} elseif ($uitype == 16) {
		require_once 'modules/PickList/PickListUtils.php';
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);

		$fieldname = $adb->sql_escape_string($fieldname);
		$pick_query="select $fieldname from vtiger_$fieldname order by sortorderid";
		$params = array();
		$pickListResult = $adb->pquery($pick_query, $params);
		$noofpickrows = $adb->num_rows($pickListResult);

		$options = array();
		$pickcount=0;
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$value = decode_html($value);
			$pickListValue=decode_html($adb->query_result($pickListResult, $j, strtolower($fieldname)));
			if ($value == trim($pickListValue)) {
				$chk_val = "selected";
				$pickcount++;
				$found = true;
			} else {
				$chk_val = '';
			}
			$pickListValue = to_html($pickListValue);
			if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
				$options[] = array(htmlentities(getTranslatedString($pickListValue), ENT_QUOTES, $default_charset),$pickListValue,$chk_val );
			} else {
				$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val );
			}
		}
		$fieldvalue [] = $options;
	} elseif ($uitype == 1613 || $uitype == 1614 || $uitype == 1615 || $uitype == 1616) {
		require_once 'modules/PickList/PickListUtils.php';
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = getPicklistValuesSpecialUitypes($uitype, $fieldname, $value);
	} elseif ($uitype == 15 || $uitype == 33) {
		require_once 'modules/PickList/PickListUtils.php';
		$roleid=$current_user->roleid;
		$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);
		if (!empty($value)) {
			$valueArr = explode('|##|', $value);
		} else {
			$valueArr = array();
		}
		foreach ($valueArr as $key => $value) {
			$valueArr[$key] = trim(html_entity_decode($value, ENT_QUOTES, $default_charset));
		}
		if ($uitype == 15) {
			if (count($valueArr)>0) {
				$valueArr = array_combine($valueArr, $valueArr);
			}
			$picklistValues = array_merge($picklistValues, $valueArr);
		}
		$options = array();
		if (!empty($picklistValues)) {
			foreach ($picklistValues as $pickListValue) {
				if (in_array(trim($pickListValue), $valueArr)) {
					$chk_val = "selected";
				} else {
					$chk_val = '';
				}
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($pickListValue), ENT_QUOTES, $default_charset),$pickListValue,$chk_val );
				} else {
					$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val );
				}
			}
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name, $value);
		$fieldvalue[] = $options;
	} elseif ($uitype == 3313 || $uitype == 3314) {
		require_once 'modules/PickList/PickListUtils.php';
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = getPicklistValuesSpecialUitypes($uitype, $fieldname, $value);
	} elseif ($uitype == 1024) {
		require_once 'modules/PickList/PickListUtils.php';
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = getPicklistValuesSpecialUitypes($uitype, $fieldname, $value);
	} elseif ($uitype == 1025) {
		$entityTypes = array();
		$parent_id = $value;
		$values = explode(' |##| ', $value);
		foreach ($cbMapFI['searchfields'] as $k => $value) {
			$entityTypes[] = $k;
		}

		if (!empty($value) && !empty($values[0])) {
			$valueType= getSalesEntityType($values[0]);

			$response=array();
			$shown_val='';
			foreach ($values as $val) {
				$displayValueArray = getEntityName($valueType, $val);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $value2) {
						$shown_val = $value2;
					}
				}
				$response[]=html_entity_decode($shown_val, ENT_QUOTES, $default_charset);
			}
			$displayValue=implode(',', $response).',';
		} else {
			$displayValue='';
			$valueType='';
			$value='';
		}
		$editview_label[] = array('options'=>$entityTypes, 'selected'=>$valueType, 'displaylabel'=>getTranslatedString($fieldlabel, $module_name));
		$fieldvalue[] = array('displayvalue'=>$displayValue,'entityid'=>$parent_id);
	} elseif ($uitype == 17) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	} elseif ($uitype == 85) { //added for Skype by Minnie
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	} elseif ($uitype == 14) { //added for Time Field
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	} elseif ($uitype == 19 || $uitype == 20) {
		if (isset($_REQUEST['body'])) {
			$value = ($_REQUEST['body']);
		}

		if ($fieldname == 'terms_conditions') {//for default Terms & Conditions
		//Assign the value from focus->column_fields (if we create Invoice from SO the SO's terms and conditions will be loaded to Invoice's terms and conditions, etc.,)
			$value = $col_fields['terms_conditions'];

			//if the value is empty then we should get the default Terms and Conditions
			if ($value == '' && $mode != 'edit') {
				$value=getTermsandConditions($module_name);
			}
		}

		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	} elseif ($uitype == 21 || $uitype == 24) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	} elseif ($uitype == 22) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
	} elseif ($uitype == 52 || $uitype == 77) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		global $current_user;
		if ($value != '') {
			$assigned_user_id = $value;
		} else {
			$assigned_user_id = $current_user->id;
		}
		if ($uitype == 52) {
			$combo_lbl_name = 'assigned_user_id';
		} elseif ($uitype == 77) {
			$combo_lbl_name = 'assigned_user_id1';
		}

		if ($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 || $defaultOrgSharingPermission[getTabid($module_name)] == 0)) {
			$ua = get_user_array(false, 'Active', $assigned_user_id, 'private');
		} else {
			$ua = get_user_array(false, 'Active', $assigned_user_id);
		}
		$users_combo = get_select_options_array($ua, $assigned_user_id);
		$fieldvalue [] = $users_combo;
	} elseif ($uitype == 53) {
		global $noof_group_rows;
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		// Get Group calculations > $noof_group_rows
		if ($fieldname == 'assigned_user_id' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 || $defaultOrgSharingPermission[getTabid($module_name)] == 0)) {
			$result = get_current_user_access_groups($module_name);
		} else {
			$result = get_group_options();
		}

		$assigned_user_id = empty($value) ? $current_user->id : $value;

		if ($fieldname == 'assigned_user_id' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 || $defaultOrgSharingPermission[getTabid($module_name)] == 0)) {
			$ua = get_user_array(false, 'Active', $assigned_user_id, 'private');
		} else {
			$ua = get_user_array(false, 'Active', $assigned_user_id);
		}
		$users_combo = get_select_options_array($ua, $assigned_user_id);
		$groups_combo = '';
		if ($noof_group_rows!=0) {
			if ($fieldname == 'assigned_user_id' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 || $defaultOrgSharingPermission[getTabid($module_name)] == 0)) {
				$ga = get_group_array(false, 'Active', $assigned_user_id, 'private');
			} else {
				$ga = get_group_array(false, 'Active', $assigned_user_id);
			}
			$groups_combo = get_select_options_array($ga, $assigned_user_id);
		}
		if (GlobalVariable::getVariable('Application_Group_Selection_Permitted', 1)!=1) {
			$groups_combo = '';
		}
		$fieldvalue[]=$users_combo;
		$fieldvalue[] = $groups_combo;
	} elseif ($uitype == 51 || $uitype == 73) {
		if (!isset($_REQUEST['convertmode']) || ($_REQUEST['convertmode'] != 'update_quote_val' && $_REQUEST['convertmode'] != 'update_so_val')) {
			if (isset($_REQUEST['account_id']) && $_REQUEST['account_id'] != '') {
				$value = vtlib_purify($_REQUEST['account_id']);
			}
		}
		if ($value != '') {
			$account_name = getAccountName($value);
		} else {
			$account_name = '';
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $account_name;
		$fieldvalue[] = $value;
	} elseif ($uitype == 54) {
		$options = array();
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$pick_query="select name from vtiger_groups";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue=$adb->query_result($pickListResult, $j, "name");

			if ($value == $pickListValue) {
				$chk_val = "selected";
			} else {
				$chk_val = '';
			}
			$options[] = array($pickListValue => $chk_val );
		}
		$fieldvalue[] = $options;
	} elseif ($uitype == 55 || $uitype == 255) {
		require_once 'modules/PickList/PickListUtils.php';
		if ($uitype==255) {
			$fieldpermission = getFieldVisibilityPermission($module_name, $current_user->id, 'firstname', 'readwrite');
		}
		if ($uitype == 255 && $fieldpermission == '0') {
			$fieldvalue[] = '';
		} else {
			$fieldpermission = getFieldVisibilityPermission($module_name, $current_user->id, 'salutationtype', 'readwrite');
			if ($fieldpermission == '0') {
				$roleid=$current_user->roleid;
				$picklistValues = getAssignedPicklistValues('salutationtype', $roleid, $adb);
				$pickcount = 0;
				$salt_value = (isset($col_fields['salutationtype']) ? $col_fields['salutationtype'] : '');
				foreach ($picklistValues as $pickListValue) {
					if ($salt_value == trim($pickListValue)) {
						$chk_val = "selected";
						$pickcount++;
					} else {
						$chk_val = '';
					}
					if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
						$options[] = array(htmlentities(getTranslatedString($pickListValue, 'Contacts'), ENT_QUOTES, $default_charset),$pickListValue,$chk_val );
					} else {
						$options[] = array(getTranslatedString($pickListValue, 'Contacts'),$pickListValue,$chk_val);
					}
				}
				if ($pickcount == 0 && $salt_value != '') {
					$options[] = array($app_strings['LBL_NOT_ACCESSIBLE'],$salt_value,'selected');
				}
				$fieldvalue [] = $options;
			} else {
				$fieldvalue[] = '';
			}
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
	} elseif ($uitype == 63) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		if ($value=='') {
			$value=1;
		}
		$options = array();
		$pick_query="select duration_minutes from vtiger_duration_minutes order by sortorderid";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);
		$salt_value = $col_fields["duration_minutes"];
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue=$adb->query_result($pickListResult, $j, "duration_minutes");

			if ($salt_value == $pickListValue) {
				$chk_val = "selected";
			} else {
				$chk_val = '';
			}
			$options[$pickListValue] = $chk_val;
		}
		$fieldvalue[]=$value;
		$fieldvalue[]=$options;
	} elseif ($uitype == 64) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		$fieldvalue[] = $value;
	} elseif ($uitype == 156) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
		$fieldvalue[] = is_admin($current_user);
	} elseif ($uitype == 56) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
	} elseif ($uitype == 57) {
		$contact_name = '';
		if ($value != '') {
			$displayValueArray = getEntityName('Contacts', $value);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $field_value) {
					$contact_name = $field_value;
				}
			}
		} elseif (isset($_REQUEST['contact_id']) && $_REQUEST['contact_id'] != '') {
			if ($_REQUEST['module'] == 'Contacts' && $fieldname = 'contact_id') {
				$contact_name = '';
			} else {
				$value = $_REQUEST['contact_id'];
				$displayValueArray = getEntityName('Contacts', $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$contact_name = $field_value;
					}
				} else {
					$contact_name='';
				}
			}
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $contact_name;
		$fieldvalue[] = $value;
	} elseif ($uitype == 61) {
		if ($value != '') {
			$assigned_user_id = $value;
		} else {
			$assigned_user_id = $current_user->id;
		}
		if ($module_name == 'Emails' && !empty($col_fields['record_id'])) {
			$attach_result = $adb->pquery('select * from vtiger_seattachmentsrel where crmid = ?', array($col_fields['record_id']));
			//to fix the issue in mail attachment on forwarding mails
			if (isset($_REQUEST['forward']) && $_REQUEST['forward'] != '') {
				global $att_id_list;
			}
			$attachquery = 'select * from vtiger_attachments where attachmentsid=?';
			for ($ii=0; $ii < $adb->num_rows($attach_result); $ii++) {
				$attachmentid = $adb->query_result($attach_result, $ii, 'attachmentsid');
				if ($attachmentid != '') {
					$rsatt = $adb->pquery($attachquery, array($attachmentid));
					$attachmentsname = $adb->query_result($rsatt, 0, 'name');
					if ($attachmentsname != '') {
						$fieldvalue[$attachmentid] = '[ '.$attachmentsname.' ]';
					}
					if (isset($_REQUEST['forward']) && $_REQUEST['forward'] != '') {
						$att_id_list .= $attachmentid.';';
					}
				}
			}
		} else {
			if (!empty($col_fields['record_id'])) {
				$rsatt = $adb->pquery('select * from vtiger_seattachmentsrel where crmid = ?', array($col_fields['record_id']));
				$attachmentid=$adb->query_result($rsatt, 0, 'attachmentsid');
				if ($col_fields[$fieldname] == '' && $attachmentid != '') {
					$attachquery = 'select name from vtiger_attachments where attachmentsid=?';
					$rsattn = $adb->pquery($attachquery, array($attachmentid));
					$value = $adb->query_result($rsattn, 0, 'name');
				}
			}
			if ($value!='') {
				$filename=' [ '.$value. ' ]';
			}
			if (!empty($filename)) {
				$fieldvalue[] = $filename;
			}
			if ($value != '') {
				$fieldvalue[] = $value;
			}
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
	} elseif ($uitype == 28) {
		if (!(empty($col_fields['record_id']))) {
			$attrs = $adb->pquery('select attachmentsid from vtiger_seattachmentsrel where crmid = ?', array($col_fields['record_id']));
			$attachmentid=$adb->query_result($attrs, 0, 'attachmentsid');
			if ($col_fields[$fieldname] == '' && $attachmentid != '') {
				$attachquery = "select name from vtiger_attachments where attachmentsid=?";
				$attqrs = $adb->pquery($attachquery, array($attachmentid));
				$value = $adb->query_result($attqrs, 0, 'name');
			}
		}
		if ($value!='' && $module_name != 'Documents') {
			$filename=' [ '.$value. ' ]';
		} elseif ($value != '' && $module_name == 'Documents') {
			$filename= $value;
		}
		if (!empty($filename)) {
			$fieldvalue[] = $filename;
		}
		if ($value != '') {
			$fieldvalue[] = $value;
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
	} elseif ($uitype == 69 || $uitype == '69m') {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
			if ($uitype == '69') {
				$fieldvalue[] = array('name'=>$col_fields[$fieldname],'path'=>'','orgname'=>$col_fields[$fieldname]);
			} else {
				$fieldvalue[] = '';
			}
		} elseif (!empty($col_fields['record_id'])) {
			if ($uitype == '69m') { // module_name == 'Products'
				$query = 'select vtiger_attachments.path, vtiger_attachments.attachmentsid, vtiger_attachments.name ,vtiger_crmentity.setype
					from vtiger_products
					left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid
					inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
					where vtiger_crmentity.setype="Products Image" and productid=?';
				$params = array($col_fields['record_id']);
			} else {
				if ($module_name == 'Contacts' && $fieldname=='imagename') {
					$imageattachment = 'Image';
				} else {
					$imageattachment = 'Attachment';
				}
				$query="select vtiger_attachments.*,vtiger_crmentity.setype
					from vtiger_attachments
					inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
					where vtiger_crmentity.setype='$module_name $imageattachment'
						and vtiger_attachments.name = ?
						and vtiger_seattachmentsrel.crmid=?";
				global $upload_badext;
				$params = array(sanitizeUploadFileName(decode_html($col_fields[$fieldname]), $upload_badext), $col_fields['record_id']);
			}
			$result_image = $adb->pquery($query, $params);
			$image_array = array();
			for ($image_iter=0, $img_itrMax = $adb->num_rows($result_image); $image_iter < $img_itrMax; $image_iter++) {
				$image_id_array[] = $adb->query_result($result_image, $image_iter, 'attachmentsid');

				//decode_html  - added to handle UTF-8   characters in file names
				//urlencode    - added to handle special characters like #, %, etc.,
				$image_array[] = urlencode(decode_html($adb->query_result($result_image, $image_iter, 'name')));
				$image_orgname_array[] = decode_html($adb->query_result($result_image, $image_iter, 'name'));

				$image_path_array[] = $adb->query_result($result_image, $image_iter, 'path');
			}
			if (count($image_array)>0) {
				for ($img_itr=0, $img_itrMax = count($image_array); $img_itr< $img_itrMax; $img_itr++) {
					$fieldvalue[] = array('name'=>$image_array[$img_itr],'path'=>$image_path_array[$img_itr].$image_id_array[$img_itr]."_","orgname"=>$image_orgname_array[$img_itr]);
				}
			} else {
				$fieldvalue[] = '';
			}
		} else {
			$fieldvalue[] = '';
		}
	} elseif ($uitype == 62) {
		if (isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '') {
			$value = vtlib_purify($_REQUEST['parent_id']);
		}
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
		}
		if (isset($_REQUEST['account_id']) && $_REQUEST['account_id'] != '') {
			$parent_module = "Accounts";
			$value = $_REQUEST['account_id'];
		}
		if ($parent_module != 'Contacts') {
			if ($parent_module == "Leads") {
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$parent_name = $field_value;
					}
				}
				$lead_selected = "selected";
			} elseif ($parent_module == "Accounts") {
				$sql = "select accountname from vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result, 0, "accountname");
				$account_selected = "selected";
			} elseif ($parent_module == "Potentials") {
				$sql = "select potentialname from vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result, 0, "potentialname");
				$potential_selected = "selected";
			} elseif ($parent_module == "Products") {
				$sql = "select productname from vtiger_products where productid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result, 0, "productname");
				$product_selected = "selected";
			} elseif ($parent_module == "PurchaseOrder") {
				$sql = "select subject from vtiger_purchaseorder where purchaseorderid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result, 0, "subject");
				$porder_selected = "selected";
			} elseif ($parent_module == "SalesOrder") {
				$sql = "select subject from vtiger_salesorder where salesorderid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result, 0, "subject");
				$sorder_selected = "selected";
			} elseif ($parent_module == "Invoice") {
				$sql = "select subject from vtiger_invoice where invoiceid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result, 0, "subject");
				$invoice_selected = "selected";
			} elseif ($parent_module == "Quotes") {
				$sql = "select subject from vtiger_quotes where quoteid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result, 0, "subject");
				$quote_selected = "selected";
			} elseif ($parent_module == "HelpDesk") {
				$sql = "select title from vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result, 0, "title");
				$ticket_selected = "selected";
			}
		}
		$editview_label[] = array($app_strings['COMBO_LEADS'],
			$app_strings['COMBO_ACCOUNTS'],
			$app_strings['COMBO_POTENTIALS'],
			$app_strings['COMBO_PRODUCTS'],
			$app_strings['COMBO_INVOICES'],
			$app_strings['COMBO_PORDER'],
			$app_strings['COMBO_SORDER'],
			$app_strings['COMBO_QUOTES'],
			$app_strings['COMBO_HELPDESK']
		);
		$editview_label[] = array($lead_selected,
			$account_selected,
			$potential_selected,
			$product_selected,
			$invoice_selected,
			$porder_selected,
			$sorder_selected,
			$quote_selected,
			$ticket_selected
		);
		$editview_label[] = array("Leads&action=Popup","Accounts&action=Popup","Potentials&action=Popup","Products&action=Popup","Invoice&action=Popup","PurchaseOrder&action=Popup","SalesOrder&action=Popup","Quotes&action=Popup","HelpDesk&action=Popup");
		$fieldvalue[] =$parent_name;
		$fieldvalue[] =$value;
	} elseif ($uitype == 66) {
		if (!empty($_REQUEST['parent_id'])) {
			$value = vtlib_purify($_REQUEST['parent_id']);
		}
		$parent_module = '';
		if (!empty($value)) {
			$parent_module = getSalesEntityType($value);
			if ($parent_module != "Contacts") {
				$entity_names = getEntityName($parent_module, $value);
				$parent_name = $entity_names[$value];

				$fieldvalue[] = $parent_name;
				$fieldvalue[] = $value;
			}
		}
		$act_mode = $_REQUEST['activity_mode'];

		$parentModulesList = array(
			'Leads' => $app_strings['COMBO_LEADS'],
			'Accounts' => $app_strings['COMBO_ACCOUNTS'],
			'Potentials' => $app_strings['COMBO_POTENTIALS'],
			'HelpDesk' => $app_strings['COMBO_HELPDESK'],
			'Campaigns' => $app_strings['COMBO_CAMPAIGNS'],
			'Vendors' => $app_strings['COMBO_VENDORS'] //MSL
		);
		if ($act_mode == "Task") {
			$parentModulesList['Quotes'] = $app_strings['COMBO_QUOTES'];
			$parentModulesList['PurchaseOrder'] = $app_strings['COMBO_PORDER'];
			$parentModulesList['SalesOrder'] = $app_strings['COMBO_SORDER'];
			$parentModulesList['Invoice'] = $app_strings['COMBO_INVOICES'];
		}
		$parentModuleNames = array_keys($parentModulesList);
		$parentModuleLabels = array_values($parentModulesList);

		$editview_label[0] = $parentModuleLabels;
		$editview_label[1] = array_fill(0, count($parentModulesList), '');
		$selectedModuleIndex = array_search($parent_module, $parentModuleNames);
		if ($selectedModuleIndex > -1) {
			$editview_label[1][$selectedModuleIndex] = 'selected';
		}

		$parentModulePopupUrl = array();
		foreach ($parentModuleNames as $parentModule) {
			$parentModulePopupUrl[] = $parentModule.'&action=Popup';
		}

		$editview_label[2] = $parentModulePopupUrl;
	} //added by rdhital/Raju for better email support
	elseif ($uitype == 357) {
		$pmodule = isset($_REQUEST['pmodule']) ? $_REQUEST['pmodule'] : (isset($_REQUEST['par_module']) ? $_REQUEST['par_module'] : null);
		if (isset($_REQUEST['emailids']) && $_REQUEST['emailids'] != '') {
			$parent_id = $_REQUEST['emailids'];
			$parent_name='';

			$myids=explode("|", $parent_id);
			for ($i=0; $i<(count($myids)-1); $i++) {
				$realid=explode("@", $myids[$i]);
				$entityid=$realid[0];
				$nemail=count($realid);

				if ($pmodule=='Accounts') {
					require_once 'modules/Accounts/Accounts.php';
					$myfocus = new Accounts();
					$myfocus->retrieve_entity_info($entityid, "Accounts");
					$fullname=br2nl($myfocus->column_fields['accountname']);
				} elseif ($pmodule=='Contacts') {
					require_once 'modules/Contacts/Contacts.php';
					$myfocus = new Contacts();
					$myfocus->retrieve_entity_info($entityid, "Contacts");
					$fname=br2nl($myfocus->column_fields['firstname']);
					$lname=br2nl($myfocus->column_fields['lastname']);
					$fullname=$lname.' '.$fname;
				} elseif ($pmodule=='Leads') {
					require_once 'modules/Leads/Leads.php';
					$myfocus = new Leads();
					$myfocus->retrieve_entity_info($entityid, "Leads");
					$fname=br2nl($myfocus->column_fields['firstname']);
					$lname=br2nl($myfocus->column_fields['lastname']);
					$fullname=$lname.' '.$fname;
				} elseif ($pmodule=='Project') {
					require_once 'modules/Project/Project.php';
					$myfocus = new Project();
					$myfocus->retrieve_entity_info($entityid, "Project");
					$fname=br2nl($myfocus->column_fields['projectname']);
					$fullname=$fname;
				} elseif ($pmodule=='ProjectTask') {
					require_once 'modules/ProjectTask/ProjectTask.php';
					$myfocus = new ProjectTask();
					$myfocus->retrieve_entity_info($entityid, "ProjectTask");
					$fname=br2nl($myfocus->column_fields['projecttaskname']);
					$fullname=$fname;
				} elseif ($pmodule=='Potentials') {
					require_once 'modules/Potentials/Potentials.php';
					$myfocus = new Potentials();
					$myfocus->retrieve_entity_info($entityid, "Potentials");
					$fname=br2nl($myfocus->column_fields['potentialname']);
					$fullname=$fname;
				} elseif ($pmodule=='HelpDesk') {
					require_once 'modules/HelpDesk/HelpDesk.php';
					$myfocus = new HelpDesk();
					$myfocus->retrieve_entity_info($entityid, "HelpDesk");
					$fname=br2nl($myfocus->column_fields['title']);
					$fullname=$fname;
				} else {
					$ename = getEntityName($pmodule, array($entityid));
					$fullname = br2nl($ename[$entityid]);
				}
				for ($j=1; $j<$nemail; $j++) {
					$querystr='select columnname from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)';
					$result=$adb->pquery($querystr, array($realid[$j]));
					$temp=$adb->query_result($result, 0, 'columnname');
					$temp1=br2nl($myfocus->column_fields[$temp]);

					//Modified to display the entities in red which don't have email id
					if (!empty($temp_parent_name) && strlen($temp_parent_name) > 150) {
						$parent_name .= '<br>';
						$temp_parent_name = '';
					}

					if ($temp1 != '') {
						$parent_name .= $fullname.'&lt;'.$temp1.'&gt;; ';
						$temp_parent_name .= $fullname.'&lt;'.$temp1.'&gt;; ';
					} else {
						$parent_name .= "<b style='color:red'>".$fullname.'&lt;'.$temp1.'&gt;; '."</b>";
						$temp_parent_name .= "<b style='color:red'>".$fullname.'&lt;'.$temp1.'&gt;; '."</b>";
					}
				}
			}
		} else {
			$parent_name='';
			$parent_id='';
			if (!empty($_REQUEST['record'])) {
				$myemailid= vtlib_purify($_REQUEST['record']);
				$mysql = "select crmid from vtiger_seactivityrel where activityid=?";
				$myresult = $adb->pquery($mysql, array($myemailid));
				$mycount=$adb->num_rows($myresult);
				if ($mycount >0) {
					for ($i=0; $i<$mycount; $i++) {
						$mycrmid=$adb->query_result($myresult, $i, 'crmid');
						$parent_module = getSalesEntityType($mycrmid);
						if ($parent_module == "Leads") {
							$sql = "select firstname,lastname,email from vtiger_leaddetails where leadid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$full_name = getFullNameFromQResult($result, 0, "Leads");
							$myemail=$adb->query_result($result, 0, "email");
							$parent_id .=$mycrmid.'@0|' ;
							$parent_name .= $full_name.'<'.$myemail.'>; ';
						} elseif ($parent_module == "Contacts") {
							$sql = "select * from vtiger_contactdetails where contactid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$full_name = getFullNameFromQResult($result, 0, "Contacts");
							$myemail=$adb->query_result($result, 0, "email");
							$parent_id .=$mycrmid.'@0|';
							$parent_name .= $full_name.'<'.$myemail.'>; ';
						} elseif ($parent_module == "Accounts") {
							$sql = "select accountname, email1 from vtiger_account where accountid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$account_name = $adb->query_result($result, 0, "accountname");
							$myemail=$adb->query_result($result, 0, "email1");
							$parent_id .=$mycrmid.'@0|';
							$parent_name .= $account_name.'<'.$myemail.'>; ';
						} elseif ($parent_module == "Users") {
							$sql = "select user_name,email1 from vtiger_users where id=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$account_name = $adb->query_result($result, 0, "user_name");
							$myemail=$adb->query_result($result, 0, "email1");
							$parent_id .=$mycrmid.'@0|';
							$parent_name .= $account_name.'<'.$myemail.'>; ';
						} elseif ($parent_module == "Vendors") {
							$sql = "select vendorname, email from vtiger_vendor where vendorid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$vendor_name = $adb->query_result($result, 0, "vendorname");
							$myemail=$adb->query_result($result, 0, "email");
							$parent_id .=$mycrmid.'@0|';
							$parent_name .= $vendor_name.'<'.$myemail.'>; ';
						} else {
							$emailfield = getFirstEmailField($pmodule);
							if ($emailfield != '') {
								$qg = new QueryGenerator($pmodule, $current_user);
								$qg->setFields(array($emailfield));
								$qg->addCondition('id', $mycrmid, 'e');
								$query = $qg->getQuery();
								$result = $adb->query($query);
								$myemail = $adb->query_result($result, 0, $emailfield);
							} else {
								$myemail = '';
							}
							$parent_id .=$mycrmid.'@0|';
							$minfo = getEntityName($pmodule, array($mycrmid));
							$parent_name .= $minfo[$mycrmid] . '<'.$myemail.'>; ';
						}
					}
				}
			}
			$emailmodules = modulesWithEmailField();
			$evlbl = array();
			foreach ($emailmodules as $mod) {
				$evlbl[$mod] = ($pmodule == $mod ? 'selected' : '');
			}
			$editview_label[] = $evlbl;
			$fieldvalue[] = $parent_name;
			$fieldvalue[] = $parent_id;
		}
	} elseif ($uitype == 9 || $uitype == 7) {
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fldrs = $adb->pquery('select typeofdata from vtiger_field
			where vtiger_field.fieldname=? and vtiger_field.tabid=?', array($fieldname, getTabid($module_name)));
		$typeofdata = $adb->query_result($fldrs, 0, 0);
		$typeinfo = explode('~', $typeofdata);
		if ($typeinfo[0]=='I') {
			$fieldvalue[] = $value;
		} else {
			$currencyField = new CurrencyField($value);
			$decimals = CurrencyField::getDecimalsFromTypeOfData($typeofdata);
			$currencyField->initialize($current_user);
			$currencyField->setNumberofDecimals(min($decimals, $currencyField->getCurrencyDecimalPlaces()));
			$fieldvalue[] = $currencyField->getDisplayValue(null, true, true);
		}
	} elseif ($uitype == 71 || $uitype == 72) {
		$currencyField = new CurrencyField($value);
		// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
		if (!empty($col_fields['record_id']) && $uitype == 72) {
			if ($fieldname == 'unit_price') {
				$rate_symbol = getCurrencySymbolandCRate(getProductBaseCurrency($col_fields['record_id'], $module_name));
				$currencySymbol = $rate_symbol['symbol'];
			} else {
				$currency_info = getInventoryCurrencyInfo($module_name, $col_fields['record_id']);
				$currencySymbol = $currency_info['currency_symbol'];
			}
			$fieldvalue[] = $currencyField->getDisplayValue(null, true);
		} else {
			$decimals = CurrencyField::getDecimalsFromTypeOfData($typeofdata);
			$currencyField->initialize($current_user);
			$currencyField->setNumberofDecimals(min($decimals, $currencyField->getCurrencyDecimalPlaces()));
			$fieldvalue[] = $currencyField->getDisplayValue(null, false, true);
			$currencySymbol = $currencyField->getCurrencySymbol();
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name).': ('.$currencySymbol.')';
	} elseif ($uitype == 76) {
		if ($value != '') {
			$potential_name = getPotentialName($value);
		} elseif (isset($_REQUEST['potential_id']) && $_REQUEST['potential_id'] != '') {
			$value = $_REQUEST['potental_id'];
			$potential_name = getPotentialName($value);
		} else {
			$potential_name = '';
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $potential_name;
		$fieldvalue[] = $value;
	} elseif ($uitype == 78) {
		if ($value != '') {
			$quote_name = getQuoteName($value);
		} elseif (isset($_REQUEST['quote_id']) && $_REQUEST['quote_id'] != '') {
			$value = $_REQUEST['quote_id'];
			$quote_name = getQuoteName($value);
		} else {
			$quote_name = '';
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $quote_name;
		$fieldvalue[] = $value;
	} elseif ($uitype == 79) {
		if ($value != '') {
			$purchaseorder_name = getPoName($value);
		} elseif (isset($_REQUEST['purchaseorder_id']) && $_REQUEST['purchaseorder_id'] != '') {
			$value = $_REQUEST['purchaseorder_id'];
			$purchaseorder_name = getPoName($value);
		} else {
			$purchaseorder_name = '';
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $purchaseorder_name;
		$fieldvalue[] = $value;
	} elseif ($uitype == 80) {
		$salesorder_name = '';
		if ($value != '') {
			$salesorder_name = getSoName($value);
		} elseif (isset($_REQUEST['salesorder_id']) && $_REQUEST['salesorder_id'] != '') {
			$value = $_REQUEST['salesorder_id'];
			$salesorder_name = getSoName($value);
		} else {
			$salesorder_name = '';
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $salesorder_name;
		$fieldvalue[] = $value;
	} elseif ($uitype == 30) {
		if ($value!='') {
			$SET_REM = 'CHECKED';
		} else {
			$SET_REM = '';
		}
		if (empty($col_fields[$fieldname])) {
			$col_fields[$fieldname] = 0;
		}
		$rem_days = floor($col_fields[$fieldname]/(24*60));
		$rem_hrs = floor(($col_fields[$fieldname]-$rem_days*24*60)/60);
		$rem_min = ($col_fields[$fieldname]-$rem_days*24*60)%60;
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$day_options = getReminderSelectOption(0, 31, 'remdays', $rem_days);
		$hr_options = getReminderSelectOption(0, 23, 'remhrs', $rem_hrs);
		$min_options = getReminderSelectOption(10, 59, 'remmin', $rem_min);
		$fieldvalue[] = array(array(0,32,'remdays',getTranslatedString('LBL_DAYS', 'Calendar'),$rem_days),array(0,24,'remhrs',getTranslatedString('LBL_HOURS', 'Calendar'),$rem_hrs),array(10,60,'remmin',getTranslatedString('LBL_MINUTES', 'Calendar').'&nbsp;&nbsp;'.getTranslatedString('LBL_BEFORE_EVENT', 'Calendar'),$rem_min));
		$fieldvalue[] = array($SET_REM,getTranslatedString('LBL_YES'),getTranslatedString('LBL_NO'));
		$SET_REM = '';
	} elseif ($uitype == 115) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$pick_query='select * from vtiger_' . $adb->sql_escape_string($fieldname);
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);

		$options = array();
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue=$adb->query_result($pickListResult, $j, strtolower($fieldname));

			if ($value == $pickListValue) {
				$chk_val = 'selected';
				$found = true;
			} else {
				$chk_val = '';
			}
			$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val );
		}
		$fieldvalue [] = $options;
		$fieldvalue [] = is_admin($current_user);
	} elseif ($uitype == 117) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$pick_query="select * from vtiger_currency_info where currency_status = 'Active' and deleted=0";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);

		$options = array();
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue=$adb->query_result($pickListResult, $j, 'currency_name');
			$currency_id=$adb->query_result($pickListResult, $j, 'id');
			if ($value == $currency_id) {
				$chk_val = 'selected';
				$found = true;
			} else {
				$chk_val = '';
			}
			$options[$currency_id] = array($pickListValue=>$chk_val );
		}
		$fieldvalue [] = $options;
		$fieldvalue [] = is_admin($current_user);
	} elseif ($uitype ==98) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[]=$value;
		$fieldvalue[]=getRoleName($value);
		$fieldvalue[]=is_admin($current_user);
	} elseif ($uitype == 105) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		if (isset($col_fields['record_id']) && $col_fields['record_id'] != '') {
			$query = 'select vtiger_attachments.path, vtiger_attachments.name
				from vtiger_contactdetails
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_contactdetails.contactid
				inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
				where vtiger_contactdetails.imagename=vtiger_attachments.name and contactid=?';
			$result_image = $adb->pquery($query, array($col_fields['record_id']));
			for ($image_iter=0; $image_iter < $adb->num_rows($result_image); $image_iter++) {
				$image_array[] = $adb->query_result($result_image, $image_iter, 'name');
				$image_path_array[] = $adb->query_result($result_image, $image_iter, 'path');
			}
		}
		if (isset($image_array) && is_array($image_array)) {
			for ($img_itr=0, $img_itrMax = count($image_array); $img_itr< $img_itrMax; $img_itr++) {
				$fieldvalue[] = array('name'=>$image_array[$img_itr],'path'=>$image_path_array[$img_itr]);
			}
		} else {
			$fieldvalue[] = '';
		}
	} elseif ($uitype == 101) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = getOwnerName($value);
		$fieldvalue[] = $value;
	} elseif ($uitype == 26) {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$folderid=$col_fields['folderid'];
		$res = $adb->pquery('select foldername from vtiger_attachmentsfolder where folderid=?', array($folderid));
		$foldername = $adb->query_result($res, 0, 'foldername');
		if ($foldername != '' && $folderid != '') {
			$fldr_name[$folderid]=$foldername;
		}
		$res=$adb->pquery('select foldername,folderid from vtiger_attachmentsfolder order by foldername', array());
		for ($i=0; $i<$adb->num_rows($res); $i++) {
			$fid=$adb->query_result($res, $i, 'folderid');
			$fldr_name[$fid]=$adb->query_result($res, $i, 'foldername');
		}
		$fieldvalue[] = $fldr_name;
	} elseif ($uitype == 27) {
		if ($value == 'E') {
			$external_selected = 'selected';
			$internal_selected = '';
			$filename = $col_fields['filename'];
		} else {
			$external_selected = '';
			$internal_selected = 'selected';
			$filename = $col_fields['filename'];
		}
		$editview_label[] = array(getTranslatedString('Internal'), getTranslatedString('External'));
		$editview_label[] = array($internal_selected, $external_selected);
		$editview_label[] = array('I','E');
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
		$fieldvalue[] = $filename;
	} elseif ($uitype == '31') {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$options = array();
		$themeList = get_themes();
		foreach ($themeList as $theme) {
			if ($value == $theme) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$options[] = array(getTranslatedString($theme), $theme, $selected);
		}
		$fieldvalue [] = $options;
	} elseif ($uitype == '32') {
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$options = array();
		$languageList = Vtiger_Language::getAll();
		foreach ($languageList as $prefix => $label) {
			if ($value == $prefix) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$options[] = array(getTranslatedString($label), $prefix, $selected);
		}
		$fieldvalue [] = $options;
	} else {
		//Added condition to set the subject if click Reply All from web mail
		if ($_REQUEST['module'] == 'Emails' && !empty($_REQUEST['mg_subject'])) {
			$value = $_REQUEST['mg_subject'];
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		if ($fieldname == 'fileversion') {
			if (empty($value)) {
				$value = '';
			} else {
				$fieldvalue[] = $value;
			}
		} else {
			$fieldvalue[] = $value;
		}
	}
	$final_arr[]=$ui_type;
	$final_arr[]=$editview_label;
	$final_arr[]=$editview_fldname;
	$final_arr[]=$fieldvalue;
	if (!empty($typeofdata)) {
		$type_of_data = explode('~', $typeofdata);
		$final_arr[] = $type_of_data[1];
	} else {
		$final_arr[] = 'O';
	}
	$log->debug('Exiting getOutputHtml method ...');
	return $final_arr;
}

/** This function returns the invoice object populated with the details from sales order object.
* Param $focus - Invoice object
* Param $so_focus - Sales order focus
* Param $soid - sales order id
* Return type is an object array
*/
function getConvertSoToInvoice($focus, $so_focus, $soid) {
	global $log,$current_user;
	$log->debug('Entering getConvertSoToInvoice('.get_class($focus).','.get_class($so_focus).','.$soid.') method ...');
	$fields = array(
		'bill_street','bill_city','bill_code','bill_pobox','bill_country','bill_state',
		'ship_street','ship_city','ship_code','ship_pobox','ship_country','ship_state'
	);
	foreach ($fields as $fieldname) {
		if (getFieldVisibilityPermission('SalesOrder', $current_user->id, $fieldname) == '0') {
			$so_focus->column_fields[$fieldname] = $so_focus->column_fields[$fieldname];
		} else {
			$so_focus->column_fields[$fieldname] = '';
		}
	}
	$focus->column_fields['salesorder_id'] = $soid;
	$focus->column_fields['subject'] = isset($so_focus->column_fields['subject']) ? $so_focus->column_fields['subject'] : '';
	$focus->column_fields['customerno'] = isset($so_focus->column_fields['customerno']) ? $so_focus->column_fields['customerno'] : '';
	$focus->column_fields['duedate'] = isset($so_focus->column_fields['duedate']) ? $so_focus->column_fields['duedate'] : '';
	$focus->column_fields['contact_id'] = isset($so_focus->column_fields['contact_id']) ? $so_focus->column_fields['contact_id'] : '';
	$focus->column_fields['account_id'] = isset($so_focus->column_fields['account_id']) ? $so_focus->column_fields['account_id'] : '';
	$focus->column_fields['exciseduty'] = isset($so_focus->column_fields['exciseduty']) ? $so_focus->column_fields['exciseduty'] : '';
	$focus->column_fields['salescommission'] = isset($so_focus->column_fields['salescommission']) ? $so_focus->column_fields['salescommission'] : '';
	$focus->column_fields['purchaseorder'] = isset($so_focus->column_fields['vtiger_purchaseorder']) ? $so_focus->column_fields['vtiger_purchaseorder'] : '';
	$focus->column_fields['bill_street'] = isset($so_focus->column_fields['bill_street']) ? $so_focus->column_fields['bill_street'] : '';
	$focus->column_fields['ship_street'] = isset($so_focus->column_fields['ship_street']) ? $so_focus->column_fields['ship_street'] : '';
	$focus->column_fields['bill_city'] = isset($so_focus->column_fields['bill_city']) ? $so_focus->column_fields['bill_city'] : '';
	$focus->column_fields['ship_city'] = isset($so_focus->column_fields['ship_city']) ? $so_focus->column_fields['ship_city'] : '';
	$focus->column_fields['bill_state'] = isset($so_focus->column_fields['bill_state']) ? $so_focus->column_fields['bill_state'] : '';
	$focus->column_fields['ship_state'] = isset($so_focus->column_fields['ship_state']) ? $so_focus->column_fields['ship_state'] : '';
	$focus->column_fields['bill_code'] = isset($so_focus->column_fields['bill_code']) ? $so_focus->column_fields['bill_code'] : '';
	$focus->column_fields['ship_code'] = isset($so_focus->column_fields['ship_code']) ? $so_focus->column_fields['ship_code'] : '';
	$focus->column_fields['bill_country'] = isset($so_focus->column_fields['bill_country']) ? $so_focus->column_fields['bill_country'] : '';
	$focus->column_fields['ship_country'] = isset($so_focus->column_fields['ship_country']) ? $so_focus->column_fields['ship_country'] : '';
	$focus->column_fields['bill_pobox'] = isset($so_focus->column_fields['bill_pobox']) ? $so_focus->column_fields['bill_pobox'] : '';
	$focus->column_fields['ship_pobox'] = isset($so_focus->column_fields['ship_pobox']) ? $so_focus->column_fields['ship_pobox'] : '';
	$focus->column_fields['description'] = isset($so_focus->column_fields['description']) ? $so_focus->column_fields['description'] : '';
	$focus->column_fields['terms_conditions'] = isset($so_focus->column_fields['terms_conditions']) ? $so_focus->column_fields['terms_conditions'] : '';
	$focus->column_fields['currency_id'] = isset($so_focus->column_fields['currency_id']) ? $so_focus->column_fields['currency_id'] : '';
	$focus->column_fields['conversion_rate'] = isset($so_focus->column_fields['conversion_rate']) ? $so_focus->column_fields['conversion_rate'] : '';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_SalesOrder2Invoice', cbMap::getMapIdByName('SalesOrder2Invoice'));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$focus->column_fields = $cbMap->Mapping($so_focus->column_fields, $focus->column_fields);
	}
	$log->debug('Exiting getConvertSoToInvoice method ...');
	return $focus;
}

/** This function returns the invoice object populated with the details from quote object.
* Param $focus - Invoice object
* Param $quote_focus - Quote order focus
* Param $quoteid - quote id
* Return type is an object array
*/
function getConvertQuoteToInvoice($focus, $quote_focus, $quoteid) {
	global $log,$current_user;
	$log->debug('Entering getConvertQuoteToInvoice('.get_class($focus).','.get_class($quote_focus).','.$quoteid.') method ...');
	$fields = array(
		'bill_street','bill_city','bill_code','bill_pobox','bill_country','bill_state',
		'ship_street','ship_city','ship_code','ship_pobox','ship_country','ship_state'
	);
	foreach ($fields as $fieldname) {
		if (getFieldVisibilityPermission('Quotes', $current_user->id, $fieldname) == '0') {
			$quote_focus->column_fields[$fieldname] = $quote_focus->column_fields[$fieldname];
		} else {
			$quote_focus->column_fields[$fieldname] = '';
		}
	}
	$focus->column_fields['subject'] = isset($quote_focus->column_fields['subject']) ? $quote_focus->column_fields['subject'] : '';
	$focus->column_fields['account_id'] = isset($quote_focus->column_fields['account_id']) ? $quote_focus->column_fields['account_id'] : '';
	$focus->column_fields['contact_id'] = isset($quote_focus->column_fields['contact_id']) ? $quote_focus->column_fields['contact_id'] : '';
	$focus->column_fields['bill_street'] = isset($quote_focus->column_fields['bill_street']) ? $quote_focus->column_fields['bill_street'] : '';
	$focus->column_fields['ship_street'] = isset($quote_focus->column_fields['ship_street']) ? $quote_focus->column_fields['ship_street'] : '';
	$focus->column_fields['bill_city'] = isset($quote_focus->column_fields['bill_city']) ? $quote_focus->column_fields['bill_city'] : '';
	$focus->column_fields['ship_city'] = isset($quote_focus->column_fields['ship_city']) ? $quote_focus->column_fields['ship_city'] : '';
	$focus->column_fields['bill_state'] = isset($quote_focus->column_fields['bill_state']) ? $quote_focus->column_fields['bill_state'] : '';
	$focus->column_fields['ship_state'] = isset($quote_focus->column_fields['ship_state']) ? $quote_focus->column_fields['ship_state'] : '';
	$focus->column_fields['bill_code'] = isset($quote_focus->column_fields['bill_code']) ? $quote_focus->column_fields['bill_code'] : '';
	$focus->column_fields['ship_code'] = isset($quote_focus->column_fields['ship_code']) ? $quote_focus->column_fields['ship_code'] : '';
	$focus->column_fields['bill_country'] = isset($quote_focus->column_fields['bill_country']) ? $quote_focus->column_fields['bill_country'] : '';
	$focus->column_fields['ship_country'] = isset($quote_focus->column_fields['ship_country']) ? $quote_focus->column_fields['ship_country'] : '';
	$focus->column_fields['bill_pobox'] = isset($quote_focus->column_fields['bill_pobox']) ? $quote_focus->column_fields['bill_pobox'] : '';
	$focus->column_fields['ship_pobox'] = isset($quote_focus->column_fields['ship_pobox']) ? $quote_focus->column_fields['ship_pobox'] : '';
	$focus->column_fields['description'] = isset($quote_focus->column_fields['description']) ? $quote_focus->column_fields['description'] : '';
	$focus->column_fields['terms_conditions'] = isset($quote_focus->column_fields['terms_conditions']) ? $quote_focus->column_fields['terms_conditions'] : '';
	$focus->column_fields['currency_id'] = isset($quote_focus->column_fields['currency_id']) ? $quote_focus->column_fields['currency_id'] : '';
	$focus->column_fields['conversion_rate'] = isset($quote_focus->column_fields['conversion_rate']) ? $quote_focus->column_fields['conversion_rate'] : '';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_Quotes2Invoice', cbMap::getMapIdByName('Quotes2Invoice'));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$focus->column_fields = $cbMap->Mapping($quote_focus->column_fields, $focus->column_fields);
	}
	$log->debug('Exiting getConvertQuoteToInvoice method ...');
	return $focus;
}

/** This function returns the sales order object populated with the details from quote object.
* Param $focus - Sales order object
* Param $quote_focus - Quote order focus
* Param $quoteid - quote id
* Return type is an object array
*/
function getConvertQuoteToSoObject($focus, $quote_focus, $quoteid) {
	global $log,$current_user;
	$log->debug('Entering getConvertQuoteToSoObject('.get_class($focus).','.get_class($quote_focus).','.$quoteid.') method ...');
	$fields = array(
		'bill_street','bill_city','bill_code','bill_pobox','bill_country','bill_state',
		'ship_street','ship_city','ship_code','ship_pobox','ship_country','ship_state'
	);
	foreach ($fields as $fieldname) {
		if (getFieldVisibilityPermission('Quotes', $current_user->id, $fieldname) == '0') {
			$quote_focus->column_fields[$fieldname] = $quote_focus->column_fields[$fieldname];
		} else {
			$quote_focus->column_fields[$fieldname] = '';
		}
	}
	$focus->column_fields['quote_id'] = $quoteid;
	$focus->column_fields['subject'] = isset($quote_focus->column_fields['subject']) ? $quote_focus->column_fields['subject'] : '';
	$focus->column_fields['contact_id'] = isset($quote_focus->column_fields['contact_id']) ? $quote_focus->column_fields['contact_id'] : '';
	$focus->column_fields['potential_id'] = isset($quote_focus->column_fields['potential_id']) ? $quote_focus->column_fields['potential_id'] : '';
	$focus->column_fields['account_id'] = isset($quote_focus->column_fields['account_id']) ? $quote_focus->column_fields['account_id'] : '';
	$focus->column_fields['carrier'] = isset($quote_focus->column_fields['carrier']) ? $quote_focus->column_fields['carrier'] : '';
	$focus->column_fields['bill_street'] = isset($quote_focus->column_fields['bill_street']) ? $quote_focus->column_fields['bill_street'] : '';
	$focus->column_fields['ship_street'] = isset($quote_focus->column_fields['ship_street']) ? $quote_focus->column_fields['ship_street'] : '';
	$focus->column_fields['bill_city'] = isset($quote_focus->column_fields['bill_city']) ? $quote_focus->column_fields['bill_city'] : '';
	$focus->column_fields['ship_city'] = isset($quote_focus->column_fields['ship_city']) ? $quote_focus->column_fields['ship_city'] : '';
	$focus->column_fields['bill_state'] = isset($quote_focus->column_fields['bill_state']) ? $quote_focus->column_fields['bill_state'] : '';
	$focus->column_fields['ship_state'] = isset($quote_focus->column_fields['ship_state']) ? $quote_focus->column_fields['ship_state'] : '';
	$focus->column_fields['bill_code'] = isset($quote_focus->column_fields['bill_code']) ? $quote_focus->column_fields['bill_code'] : '';
	$focus->column_fields['ship_code'] = isset($quote_focus->column_fields['ship_code']) ? $quote_focus->column_fields['ship_code'] : '';
	$focus->column_fields['bill_country'] = isset($quote_focus->column_fields['bill_country']) ? $quote_focus->column_fields['bill_country'] : '';
	$focus->column_fields['ship_country'] = isset($quote_focus->column_fields['ship_country']) ? $quote_focus->column_fields['ship_country'] : '';
	$focus->column_fields['bill_pobox'] = isset($quote_focus->column_fields['bill_pobox']) ? $quote_focus->column_fields['bill_pobox'] : '';
	$focus->column_fields['ship_pobox'] = isset($quote_focus->column_fields['ship_pobox']) ? $quote_focus->column_fields['ship_pobox'] : '';
	$focus->column_fields['description'] = isset($quote_focus->column_fields['description']) ? $quote_focus->column_fields['description'] : '';
	$focus->column_fields['terms_conditions'] = isset($quote_focus->column_fields['terms_conditions']) ? $quote_focus->column_fields['terms_conditions'] : '';
	$focus->column_fields['currency_id'] = isset($quote_focus->column_fields['currency_id']) ? $quote_focus->column_fields['currency_id'] : '';
	$focus->column_fields['conversion_rate'] = isset($quote_focus->column_fields['conversion_rate']) ? $quote_focus->column_fields['conversion_rate'] : '';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_Quotes2SalesOrder', cbMap::getMapIdByName('Quotes2SalesOrder'));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$focus->column_fields = $cbMap->Mapping($quote_focus->column_fields, $focus->column_fields);
	}
	$log->debug('Exiting getConvertQuoteToSoObject method ...');
	return $focus;
}

/** This function returns the detailed list of products associated to a given entity or a record.
* Param $module - module name
* Param $focus - module object
* Param $seid - sales entity id
* Return type is an object array
*/
function getAssociatedProducts($module, $focus, $seid = '') {
	global $log, $adb, $currentModule;
	$log->debug("Entering getAssociatedProducts(".$module.",".get_class($focus).",".$seid."='') method ...");

	$product_Detail = array();
	$acvid = 0;
	if (in_array($module, getInventoryModules())) {
		$query="SELECT
			case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname,
			case when vtiger_products.productid != '' then vtiger_products.productcode else vtiger_service.service_no end as productcode,
			case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price,
			case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock,
			case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype,
			vtiger_inventoryproductrel.listprice,
			vtiger_inventoryproductrel.description AS product_description,
			vtiger_inventoryproductrel.*
			FROM vtiger_inventoryproductrel
			LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_service ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid
			WHERE id=? ORDER BY sequence_no";
			$params = array($focus->id);
		if ($module != 'PurchaseOrder' && $module != 'Receiptcards') {
			if (GlobalVariable::getVariable('Application_B2B', '1')=='1') {
				if ($module == 'Issuecards') {
					$acvid = $focus->column_fields['accid'];
				} else {
					$acvid = $focus->column_fields['account_id'];
				}
			} else {
				if ($module == 'Issuecards') {
					$acvid = $focus->column_fields['ctoid'];
				} else {
					$acvid = $focus->column_fields['contact_id'];
				}
			}
		} else {
			$acvid = $focus->column_fields['vendor_id'];
		}
	} elseif ($module == 'Potentials') {
		$query="SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode,
			vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.description AS product_description,
			'Products' AS entitytype
			FROM vtiger_products
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.productid=vtiger_products.productid
			WHERE vtiger_seproductsrel.crmid=?";
		$query.=" UNION SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname,
			'NA' AS productcode, vtiger_service.unit_price AS unit_price, 'NA' AS qtyinstock,
			vtiger_crmentity.description AS product_description, 'Services' AS entitytype
			FROM vtiger_service
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_service.serviceid
			INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid=vtiger_service.serviceid
			WHERE vtiger_crmentityrel.crmid=?";
			$params = array($seid,$seid);
	} elseif ($module == 'Products') {
		$query="SELECT vtiger_products.productid, vtiger_products.productcode, vtiger_products.productname,
			vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.description AS product_description,
			'Products' AS entitytype
			FROM vtiger_products
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
			WHERE vtiger_crmentity.deleted=0 AND productid=?";
			$params = array($seid);
	} elseif ($module == 'Services') {
		$query="SELECT vtiger_service.serviceid AS productid, 'NA' AS productcode, vtiger_service.servicename AS productname,
			vtiger_service.unit_price AS unit_price, 'NA' AS qtyinstock, vtiger_crmentity.description AS product_description,
			'Services' AS entitytype
			FROM vtiger_service
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_service.serviceid
			WHERE vtiger_crmentity.deleted=0 AND serviceid=?";
			$params = array($seid);
	} else {
		$query = "SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode,
			vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.description AS product_description,
			'Products' AS entitytype
			FROM vtiger_products
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
			INNER JOIN vtiger_crmentityrel ON (
				(vtiger_crmentityrel.crmid=vtiger_products.productid and vtiger_crmentityrel.relcrmid=?) or
				(vtiger_crmentityrel.crmid=? and vtiger_crmentityrel.relcrmid=vtiger_products.productid)
			)
			WHERE vtiger_crmentity.deleted=0";
		$query.=" UNION SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname,
			'NA' AS productcode, vtiger_service.unit_price AS unit_price, 'NA' AS qtyinstock,
			vtiger_crmentity.description AS product_description, 'Services' AS entitytype
			FROM vtiger_service
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_service.serviceid
			INNER JOIN vtiger_crmentityrel ON (
				(vtiger_crmentityrel.crmid=vtiger_service.serviceid and vtiger_crmentityrel.relcrmid=?) or
				(vtiger_crmentityrel.crmid=? and vtiger_crmentityrel.relcrmid=vtiger_service.serviceid)
			)
			WHERE vtiger_crmentity.deleted=0";
			$params = array($seid, $seid, $seid, $seid);
	}
	if ($module != $currentModule && in_array($currentModule, getInventoryModules())) {
		$cbMap = cbMap::getMapByName($currentModule.'InventoryDetails', 'MasterDetailLayout');
		$MDMapFound = ($cbMap!=null);
		if ($MDMapFound) {
			$cbMapFields = $cbMap->MasterDetailLayout();
		}
	} else {
		$cbMap = cbMap::getMapByName($module.'InventoryDetails', 'MasterDetailLayout');
		$MDMapFound = ($cbMap!=null);
		if ($MDMapFound) {
			$cbMapFields = $cbMap->MasterDetailLayout();
		}
	}

	$result = $adb->pquery($query, $params);
	$num_rows=$adb->num_rows($result);
	for ($i=1; $i<=$num_rows; $i++) {
		$hdnProductId = $adb->query_result($result, $i-1, 'productid');
		$hdnProductcode = $adb->query_result($result, $i-1, 'productcode');
		$productname=$adb->query_result($result, $i-1, 'productname');
		$productdescription=$adb->query_result($result, $i-1, 'product_description');
		$comment=$adb->query_result($result, $i-1, 'comment');
		$qtyinstock=$adb->query_result($result, $i-1, 'qtyinstock');
		$qty=$adb->query_result($result, $i-1, 'quantity');
		$unitprice=$adb->query_result($result, $i-1, 'unit_price');
		$listprice=$adb->query_result($result, $i-1, 'listprice');
		$entitytype=$adb->query_result($result, $i-1, 'entitytype');
		if (!empty($entitytype)) {
			$product_Detail[$i]['entityType'.$i]=$entitytype;
		}
		if ($module==$currentModule) {
			$product_Detail[$i]['lineitem_id'.$i]=$adb->query_result($result, $i-1, 'lineitem_id');
		} else {
			$product_Detail[$i]['lineitem_id'.$i]=0;
		}

		if ($listprice == '') {
			$listprice = $unitprice;
		}
		if ($qty =='') {
			$qty = 1;
		}

		//calculate productTotal
		$productTotal = $qty*$listprice;

		//Delete link in First column
		if ($i != 1) {
			$product_Detail[$i]['delRow'.$i]="Del";
		}
		if (empty($focus->mode) && $seid!='') {
			$sub_prod_query = $adb->pquery("SELECT crmid as prod_id from vtiger_seproductsrel WHERE productid=? AND setype='Products'", array($seid));
		} else {
			$sub_prod_query = $adb->pquery("SELECT productid as prod_id from vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?", array($focus->id,$i));
		}
		$subprodid_str='';
		$subprodname_str='';
		$subProductArray = array();
		if ($adb->num_rows($sub_prod_query)>0) {
			for ($j=0; $j<$adb->num_rows($sub_prod_query); $j++) {
				$sprod_id = $adb->query_result($sub_prod_query, $j, 'prod_id');
				$sprod_name = $subProductArray[] = getProductName($sprod_id);
				$str_sep = "";
				if ($j>0) {
					$str_sep = ":";
				}
				$subprodid_str .= $str_sep.$sprod_id;
				$subprodname_str .= $str_sep." - ".$sprod_name;
			}
		}

		$subprodname_str = str_replace(":", "<br>", $subprodname_str);

		$product_Detail[$i]['subProductArray'.$i] = $subProductArray;
		$product_Detail[$i]['hdnProductId'.$i] = $hdnProductId;
		$product_Detail[$i]['productName'.$i]= $productname;
		/* Added to fix the issue Product Pop-up name display*/
		if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'CreateSOPDF' || $_REQUEST['action'] == 'CreatePDF' || $_REQUEST['action'] == 'SendPDFMail')) {
			$product_Detail[$i]['productName'.$i]= htmlspecialchars($product_Detail[$i]['productName'.$i]);
		}
		$product_Detail[$i]['hdnProductcode'.$i] = $hdnProductcode;
		$product_Detail[$i]['productDescription'.$i]= $productdescription;
		if ($module == 'Potentials' || $module == 'Products' || $module == 'Services') {
			$product_Detail[$i]['comment'.$i]= $productdescription;
		} else {
			$product_Detail[$i]['comment'.$i]= $comment;
		}
		if ($MDMapFound) {
			foreach ($cbMapFields['detailview']['fields'] as $mdfield) {
				$mdrs = $adb->pquery(
					'select '.$mdfield['fieldinfo']['name'].',vtiger_inventorydetails.inventorydetailsid from vtiger_inventorydetails
						inner join vtiger_crmentity on crmid=vtiger_inventorydetails.inventorydetailsid
						inner join vtiger_inventorydetailscf on vtiger_inventorydetailscf.inventorydetailsid=vtiger_inventorydetails.inventorydetailsid
						where deleted=0 and related_to=? and lineitem_id=?',
					array($focus->id,$adb->query_result($result, $i - 1, 'lineitem_id'))
				);
				if ($mdrs) {
					$col_fields = array();
					$col_fields[$mdfield['fieldinfo']['name']] = $adb->query_result($mdrs, 0, $mdfield['fieldinfo']['name']);
					$col_fields['record_id'] = $adb->query_result($mdrs, 0, 'inventorydetailsid');
					$foutput = getOutputHtml($mdfield['fieldinfo']['uitype'], $mdfield['fieldinfo']['name'], $mdfield['fieldinfo']['label'], 100, $col_fields, 0, 'InventoryDetails', 'edit', $mdfield['fieldinfo']['typeofdata']);
					$product_Detail[$i]['moreinfo'.$i][] = $foutput;
				}
			}
		}

		if ($module != 'PurchaseOrder') {
			$product_Detail[$i]['qtyInStock'.$i]=$qtyinstock;
		}
		$qty = number_format($qty, 2, '.', ''); //Convert to 2 decimals
		$product_Detail[$i]['qty'.$i]=$qty;
		$product_Detail[$i]['listPrice'.$i]=CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($listprice, null, true), null, true);
		$product_Detail[$i]['unitPrice'.$i]=CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($unitprice, null, true), null, true);
		$product_Detail[$i]['productTotal'.$i]=CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($productTotal, null, true), null, true);
		$product_Detail[$i]['subproduct_ids'.$i]=$subprodid_str;
		$product_Detail[$i]['subprod_names'.$i]=$subprodname_str;
		$discount_percent=$adb->query_result($result, $i-1, 'discount_percent');
		$discount_amount=$adb->query_result($result, $i-1, 'discount_amount');
		$discount_amount = (is_numeric($discount_amount) ? $discount_amount : 0);
		$discountTotal = '0.00';
		//Based on the discount percent or amount we will show the discount details

		//To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(for Each Product)
		$product_Detail[$i]['discount_percent'.$i] = 0;
		$product_Detail[$i]['discount_amount'.$i] = 0;

		if ($discount_percent != 'NULL' && $discount_percent != '') {
			$product_Detail[$i]['discount_type'.$i] = "percentage";
			$product_Detail[$i]['discount_percent'.$i] = $discount_percent;
			$product_Detail[$i]['checked_discount_percent'.$i] = ' checked';
			$product_Detail[$i]['style_discount_percent'.$i] = ' style="visibility:visible"';
			$product_Detail[$i]['style_discount_amount'.$i] = ' style="visibility:hidden"';
			$discountTotal = $productTotal*$discount_percent/100;
		} elseif ($discount_amount != 'NULL' && $discount_amount != '') {
			$product_Detail[$i]['discount_type'.$i] = "amount";
			$product_Detail[$i]['discount_amount'.$i] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($discount_amount, null, true), null, true);
			$product_Detail[$i]['checked_discount_amount'.$i] = ' checked';
			$product_Detail[$i]['style_discount_amount'.$i] = ' style="visibility:visible"';
			$product_Detail[$i]['style_discount_percent'.$i] = ' style="visibility:hidden"';
			$discountTotal = $discount_amount;
		} else {
			$product_Detail[$i]['checked_discount_zero'.$i] = ' checked';
		}
		$totalAfterDiscount = $productTotal-$discountTotal;
		$product_Detail[$i]['discountTotal'.$i] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($discountTotal, null, true), null, true);
		$product_Detail[$i]['totalAfterDiscount'.$i] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($totalAfterDiscount, null, true), null, true);

		$taxTotal = '0.00';
		$product_Detail[$i]['taxTotal'.$i] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($taxTotal, null, true), null, true);

		//Calculate netprice
		$netPrice = $totalAfterDiscount+$taxTotal;
		//if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
		if (in_array($module, getInventoryModules())) {
			$taxtype = getInventoryTaxType($module, $focus->id);
			if ($taxtype == 'individual') {
				//Add the tax with product total and assign to netprice
				$netPrice = $netPrice+$taxTotal;
			}
		} else {
			$taxtype = GlobalVariable::getVariable('Inventory_Tax_Type_Default', 'individual', $currentModule);
		}
		$product_Detail[$i]['netPrice'.$i] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($netPrice, null, true), null, true);

		//First we will get all associated taxes as array
		$tax_details = getTaxDetailsForProduct($hdnProductId, 'all', $acvid);
		//Now retrieve the tax values from the current query with the name
		for ($tax_count=0, $tax_countMax = count($tax_details); $tax_count< $tax_countMax; $tax_count++) {
			$tax_name = $tax_details[$tax_count]['taxname'];
			$tax_label = $tax_details[$tax_count]['taxlabel'];
			$tax_value = '0.00';

			//condition to avoid this function call when create new PO/SO/Quotes/Invoice from Product module
			if ($focus->id != '') {
				if ($taxtype == 'individual') { //if individual then show the entered tax percentage
					$tax_value = getInventoryProductTaxValue($focus->id, $hdnProductId, $tax_name);
				} else { //if group tax then we have to show the default value when change to individual tax
					$tax_value = $tax_details[$tax_count]['percentage'];
				}
			} else { //if the above function not called then assign the default associated value of the product
				$tax_value = $tax_details[$tax_count]['percentage'];
			}

			$product_Detail[$i]['taxes'][$tax_count]['taxname'] = $tax_name;
			$product_Detail[$i]['taxes'][$tax_count]['taxlabel'] = $tax_label;
			$product_Detail[$i]['taxes'][$tax_count]['percentage'] = $tax_value;
		}
	}
	if ($num_rows==0) {
		$product_Detail[1] = array();
	}
	$product_Detail[1]['final_details'] = array();

	//set the taxtype
	if (!isset($taxtype)) {
		$taxtype = GlobalVariable::getVariable('Inventory_Tax_Type_Default', 'individual', $currentModule);
	}
	$product_Detail[1]['final_details']['taxtype'] = $taxtype;

	//Get the Final Discount, S&H charge, Tax for S&H and Adjustment values
	//To set the Final Discount details
	$finalDiscount = '0.00';
	$product_Detail[1]['final_details']['discount_type_final'] = 'zero';

	$subTotal = (!empty($focus->column_fields['hdnSubTotal']))?$focus->column_fields['hdnSubTotal']:'0.00';

	$product_Detail[1]['final_details']['hdnSubTotal'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($subTotal, null, true), null, true);
	$discountPercent = (!empty($focus->column_fields['hdnDiscountPercent']))?$focus->column_fields['hdnDiscountPercent']:'0.00';
	$discountAmount = (!empty($focus->column_fields['hdnDiscountAmount']))?$focus->column_fields['hdnDiscountAmount']:'0.00';

	//To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(For Final Discount)
	$product_Detail[1]['final_details']['discount_percentage_final'] = 0;
	$product_Detail[1]['final_details']['discount_amount_final'] = 0;

	if (!empty($focus->column_fields['hdnDiscountPercent']) && $focus->column_fields['hdnDiscountPercent'] != '0') {
		$finalDiscount = ($subTotal*$discountPercent/100);
		$product_Detail[1]['final_details']['discount_type_final'] = 'percentage';
		$product_Detail[1]['final_details']['discount_percentage_final'] = $discountPercent;
		$product_Detail[1]['final_details']['checked_discount_percentage_final'] = ' checked';
		$product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:visible"';
		$product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:hidden"';
	} elseif (!empty($focus->column_fields['hdnDiscountAmount']) && $focus->column_fields['hdnDiscountAmount'] != '0') {
		$finalDiscount = $focus->column_fields['hdnDiscountAmount'];
		$product_Detail[1]['final_details']['discount_type_final'] = 'amount';
		$product_Detail[1]['final_details']['discount_amount_final'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($discountAmount, null, true), null, true);
		$product_Detail[1]['final_details']['checked_discount_amount_final'] = ' checked';
		$product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:visible"';
		$product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:hidden"';
	}
	$product_Detail[1]['final_details']['discountTotal_final'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($finalDiscount, null, true), null, true);

	//To set the Final Tax values
	//we will get all taxes. if individual then show the product related taxes only else show all taxes
	//suppose user want to change individual to group or vice versa in edit time the we have to show all taxes.
	//so that here we will store all the taxes and based on need we will show the corresponding taxes

	$taxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	$tax_details = getAllTaxes('available', '', 'edit', $focus->id);
	$ipr_cols = $adb->getColumnNames('vtiger_inventoryproductrel');

	for ($tax_count=0, $tax_countMax = count($tax_details); $tax_count< $tax_countMax; $tax_count++) {
		$tax_name = $tax_details[$tax_count]['taxname'];
		$tax_label = $tax_details[$tax_count]['taxlabel'];

		//if taxtype==individual and want to change to group during edit then we have to show the all available taxes and their default values
		//if taxtype==group and want to change to individual during edit then we have to provide the associated taxes and their default tax values for individual products
		if ($taxtype == 'group') {
			if (in_array($tax_name, $ipr_cols)) {
				$tax_percent = $adb->query_result($result, 0, $tax_name);
			} else {
				$tax_percent = $tax_details[$tax_count]['percentage'];
			}
		} else {
			$tax_percent = $tax_details[$tax_count]['percentage'];//$adb->query_result($result,0,$tax_name);
		}

		if ($tax_percent == '' || $tax_percent == 'NULL') {
			$tax_percent = '0.00';
		}
		$taxamount = ($subTotal-$finalDiscount)*$tax_percent/100;
		$taxtotal = $taxtotal + $taxamount;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['taxname'] = $tax_name;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['taxlabel'] = $tax_label;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['percentage'] = $tax_percent;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['amount'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($taxamount, null, true), null, true);
	}
	$product_Detail[1]['final_details']['tax_totalamount'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($taxtotal, null, true), null, true);

	//To set the Shipping & Handling charge
	$shCharge = (!empty($focus->column_fields['hdnS_H_Amount']))?$focus->column_fields['hdnS_H_Amount']:'0.00';
	$product_Detail[1]['final_details']['shipping_handling_charge'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($shCharge, null, true), null, true);

	//To set the Shipping & Handling tax values
	//calculate S&H tax
	$shtaxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	$shtax_details = getAllTaxes('available', 'sh', 'edit', $focus->id);

	//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
	for ($shtax_count=0, $shtax_countMax = count($shtax_details); $shtax_count< $shtax_countMax; $shtax_count++) {
		$shtax_name = $shtax_details[$shtax_count]['taxname'];
		$shtax_label = $shtax_details[$shtax_count]['taxlabel'];
		$shtax_percent = '0.00';
		//if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
		if (in_array($module, getInventoryModules())) {
			$shtax_percent = getInventorySHTaxPercent($focus->id, $shtax_name);
		}
		$shtaxamount = $shCharge*$shtax_percent/100;
		$shtaxtotal = $shtaxtotal + $shtaxamount;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxname'] = $shtax_name;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxlabel'] = $shtax_label;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['percentage'] = $shtax_percent;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['amount'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($shtaxamount, null, true), null, true);
	}
	$product_Detail[1]['final_details']['shtax_totalamount'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($shtaxtotal, null, true), null, true);

	//To set the Adjustment value
	$adjustment = (!empty($focus->column_fields['txtAdjustment']))?$focus->column_fields['txtAdjustment']:'0.00';
	$product_Detail[1]['final_details']['adjustment'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($adjustment, null, true), null, true);

	//To set the grand total
	$grandTotal = (!empty($focus->column_fields['hdnGrandTotal']))?$focus->column_fields['hdnGrandTotal']:'0.00';
	$product_Detail[1]['final_details']['grandTotal'] = CurrencyField::convertToDBFormat(CurrencyField::convertToUserFormat($grandTotal, null, true), null, true);

	$log->debug('Exiting getAssociatedProducts method ...');
	return $product_Detail;
}

/** This function returns the no of products associated to the given entity or a record.
* Param $module - module name
* Param $focus - module object
* Param $seid - sales entity id
* Return type is an object array
*/
function getNoOfAssocProducts($module, $focus, $seid = '') {
	global $log, $adb;
	$log->debug('Entering getNoOfAssocProducts('.$module.','.get_class($focus).','.$seid.')');
	if ($module == 'Quotes') {
		$query='select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.*
			from vtiger_inventoryproductrel
			inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid
			where id=?';
		$params = array($focus->id);
	} elseif ($module == 'PurchaseOrder') {
		$query='select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.*
			from vtiger_inventoryproductrel
			inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid
			where id=?';
		$params = array($focus->id);
	} elseif ($module == 'SalesOrder') {
		$query='select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.*
			from vtiger_inventoryproductrel
			inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid
			where id=?';
		$params = array($focus->id);
	} elseif ($module == 'Invoice') {
		$query='select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.*
			from vtiger_inventoryproductrel
			inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid
			where id=?';
		$params = array($focus->id);
	} elseif ($module == 'Potentials') {
		$query='select vtiger_products.productname,vtiger_products.unit_price,vtiger_seproductsrel.*
			from vtiger_products
			inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid
			where crmid=?';
		$params = array($seid);
	} elseif ($module == 'Products') {
		$query="select vtiger_products.productname,vtiger_products.unit_price, vtiger_crmentity.*
			from vtiger_products
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid
			where vtiger_crmentity.deleted=0 and productid=?";
		$params = array($seid);
	}

	$result = $adb->pquery($query, $params);
	$num_rows=$adb->num_rows($result);
	$log->debug('Exiting getNoOfAssocProducts');
	return $num_rows;
}

/** This function returns the detail block information of a record for given block id.
* Param $module - module name
* Param $block - block name
* Param $mode - view type (detail/edit/create)
* Param $col_fields - fields array
* Param $tabid - tab id
* Param $info_type - information type (basic/advance) default ""
* Return type is an object array
*/
function getBlockInformation($module, $result, $col_fields, $tabid, $block_label, $mode) {
	global $log, $adb;
	$log->debug("Entering getBlockInformation(".$module.",". print_r($col_fields, true).",".$tabid.",".print_r($block_label, true).')');
	$isduplicate = isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : false;
	$editview_arr = array();

	$bmapname = $module.'_FieldInfo';
	$cbMapFI = array();
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$cbMapFI = $cbMap->FieldInfo();
		$cbMapFI = $cbMapFI['fields'];
	}
	$noofrows = $adb->num_rows($result);
	for ($i=0; $i<$noofrows; $i++) {
		$fieldtablename = $adb->query_result($result, $i, 'tablename');
		$fieldcolname = $adb->query_result($result, $i, 'columnname');
		$uitype = $adb->query_result($result, $i, 'uitype');
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$block = $adb->query_result($result, $i, 'block');
		$maxlength = $adb->query_result($result, $i, 'maximumlength');
		$generatedtype = $adb->query_result($result, $i, 'generatedtype');
		$typeofdata = $adb->query_result($result, $i, 'typeofdata');
		$defaultvalue = $adb->query_result($result, $i, 'defaultvalue');
		if (($mode == '' || $mode == 'create') && empty($col_fields[$fieldname]) && !$isduplicate) {
			$col_fields[$fieldname] = $defaultvalue;
		}
		if (isset($cbMapFI[$fieldname])) {
			$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields, $generatedtype, $module, $mode, $typeofdata, $cbMapFI[$fieldname]);
			$custfld['extendedfieldinfo'] = $cbMapFI[$fieldname];
		} else {
			$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields, $generatedtype, $module, $mode, $typeofdata);
		}
		if ($uitype==10 && count($custfld[1][0]['options'])==0) {
			continue;
		}
		$editview_arr[$block][]=$custfld;
	}
	foreach ($editview_arr as $headerid => $editview_value) {
		$editview_data = array();
		for ($i=0, $j=0, $iMax = count($editview_value); $i< $iMax; $j++) {
			$key1=$editview_value[$i];
			if (isset($editview_value[$i+1]) && is_array($editview_value[$i+1]) && ($key1[0][0]!=19 && $key1[0][0]!=20)) {
				$key2=$editview_value[$i+1];
			} else {
				$key2 =array();
			}
			if ($key1[0][0]!=19 && $key1[0][0]!=20) {
				$editview_data[$j]=array(0 => $key1,1 => $key2);
				$i+=2;
			} else {
				$editview_data[$j]=array(0 => $key1);
				$i++;
			}
		}
		$editview_arr[$headerid] = $editview_data;
	}
	$returndata = array();
	$curBlock = '';
	foreach ($block_label as $blockid => $label) {
		if (isset($editview_arr[$blockid]) && $editview_arr[$blockid] != null) {
			if ($label == '') {
				$i18nidx = getTranslatedString($curBlock, $module);
				if (!isset($returndata[$i18nidx])) {
					$returndata[$i18nidx] = array();
				}
				$returndata[$i18nidx]=array_merge((array)$returndata[$i18nidx], (array)$editview_arr[$blockid]);
			} else {
				$curBlock = $label;
				if (is_array($editview_arr[$blockid])) {
					$i18nidx = getTranslatedString($curBlock, $module);
					if (!isset($returndata[$i18nidx])) {
						$returndata[$i18nidx] = array();
					}
					$returndata[$i18nidx]=array_merge((array)$returndata[$i18nidx], (array)$editview_arr[$blockid]);
				}
			}
		} elseif (file_exists("Smarty/templates/modules/$module/{$label}_edit.tpl")) {
			$i18nidx = getTranslatedString($label, $module);
			if (!isset($returndata[$i18nidx])) {
				$returndata[$i18nidx] = array();
			}
			$returndata[$i18nidx]=array_merge((array)$returndata[$i18nidx], array($label=>array()));
		}
	}
	$log->debug('Exiting getBlockInformation');
	return $returndata;
}

/** This function returns the data type of the fields, with field label, which is used for javascript validation.
* Param $validationData - array of fieldnames with datatype
* Return type array
*/
function split_validationdataArray($validationData) {
	global $log;
	$log->debug('Entering split_validationdataArray('.print_r($validationData, true).')');
	$fieldName = '';
	$fieldLabel = '';
	$fldDataType = '';
	foreach ($validationData as $fldName => $fldLabel_array) {
		if ($fieldName == '') {
			$fieldName="'".$fldName."'";
		} else {
			$fieldName .= ",'".$fldName ."'";
		}
		foreach ($fldLabel_array as $fldLabel => $datatype) {
			if ($fieldLabel == '') {
				$fieldLabel = "'".addslashes($fldLabel)."'";
			} else {
				$fieldLabel .= ",'".addslashes($fldLabel)."'";
			}
			if ($fldDataType == '') {
				$fldDataType = "'".$datatype ."'";
			} else {
				$fldDataType .= ",'".$datatype ."'";
			}
		}
	}
	$data['fieldname'] = $fieldName;
	$data['fieldlabel'] = $fieldLabel;
	$data['datatype'] = $fldDataType;
	$log->debug('Exiting split_validationdataArray');
	return $data;
}

/**
 * Get field validation information
 */
function getDBValidationData($tablearray, $tabid = '') {
	if ($tabid != '') {
		global $adb, $default_charset;
		$fieldModuleName = getTabModuleName($tabid);
		$fieldres = $adb->pquery(
			"SELECT fieldlabel,fieldname,typeofdata FROM vtiger_field
			WHERE displaytype IN (1,3) AND presence in (0,2) AND tabid=?",
			array($tabid)
		);
		$fieldinfos = array();
		while ($fieldrow = $adb->fetch_array($fieldres)) {
			$fieldlabel = getTranslatedString(html_entity_decode($fieldrow['fieldlabel'], ENT_QUOTES, $default_charset), $fieldModuleName);
			$fieldname = $fieldrow['fieldname'];
			$typeofdata= $fieldrow['typeofdata'];
			$fieldinfos[$fieldname] = array($fieldlabel => $typeofdata);
		}
		return $fieldinfos;
	} else {
		return array();
	}
}
?>
