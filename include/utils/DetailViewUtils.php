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
require_once 'vtlib/Vtiger/Language.php';
require_once 'modules/PickList/PickListUtils.php';

/** This function returns the detail view form vtiger_field and and its properties in array format.
 * Param $uitype - UI type of the vtiger_field
 * Param $fieldname - Form vtiger_field name
 * Param $fieldlabel - Form vtiger_field label name
 * Param $col_fields - array contains the vtiger_fieldname and values
 * Param $generatedtype - Field generated type (default is 1)
 * Param $tabid - vtiger_tab id to which the Field belongs to (default is "")
 * Return type is an array
 */
function getDetailViewOutputHtml($uitype, $fieldname, $fieldlabel, $col_fields, $generatedtype, $tabid = '', $module = '', $cbMapFI = array()) {
	global $log, $adb, $mod_strings, $app_strings, $current_user, $theme, $default_charset;
	$log->debug("Entering getDetailViewOutputHtml($uitype, $fieldname, $fieldlabel," . print_r($col_fields, true) . ", $generatedtype, $tabid) method ...");
	$theme_path = 'themes/' . $theme . '/';
	$image_path = $theme_path . 'images/';
	$value = '';
	$label_fld = array();
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';
	require 'user_privileges/sharing_privileges_' . $current_user->id . '.php';

	// vtlib customization: New uitype to handle relation between modules
	if ($uitype == '10') {
		$fieldlabel = getTranslatedString($fieldlabel, $module);
		$parent_id = $col_fields[$fieldname];
		if (!empty($parent_id)) {
			$parent_module = '';
			$fldrs = $adb->pquery(
				'select relmodule
				from vtiger_fieldmodulerel
				inner join vtiger_field on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid
				where vtiger_field.fieldname=? and vtiger_field.tabid=?',
				array($fieldname, $tabid)
			);
			if ($adb->num_rows($fldrs)==1) {
				$parent_module = $adb->query_result($fldrs, 0, 0);
			}
			if ($parent_module=='') {
				$parent_module = getSalesEntityType($parent_id);
			}
			$valueTitle = getTranslatedString($parent_module, $parent_module);
			$displayValueArray = getEntityName($parent_module, $parent_id);
			$displayValue = '';
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $value) {
					$displayValue = $value;
				}
			}
			// vtlib customization: For listview javascript triggers
			$modMetaInfo=getEntityFieldNames($parent_module);
			$modEName=(is_array($modMetaInfo['fieldname']) ? $modMetaInfo['fieldname'][0] : $modMetaInfo['fieldname']);
			$vtlib_metainfo = "<span type='vtlib_metainfo' vtrecordid='$parent_id' vtfieldname='$modEName' vtmodule='$parent_module' style='display:none;'></span>";
			$label_fld = array($fieldlabel,
				"<a href='index.php?module=$parent_module&action=DetailView&record=$parent_id' title='$valueTitle'>$displayValue</a>$vtlib_metainfo");
		} else {
			$moduleSpecificMessage = 'MODULE_NOT_SELECTED';
			if (!empty($mod_strings[$moduleSpecificMessage])) {
				$moduleSpecificMessage = $mod_strings[$moduleSpecificMessage];
			}
			$label_fld = array($fieldlabel, '');
			$parent_id = '';
		}
	} elseif ($uitype == 99) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
		if ($fieldname == 'confirm_password') {
			return null;
		}
	} elseif ($uitype == 117) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getCurrencyName($col_fields[$fieldname]);
		$pick_query = "select currency_name, id from vtiger_currency_info where currency_status = 'Active' and deleted=0";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue = $adb->query_result($pickListResult, $j, 'currency_name');
			$currency_id = $adb->query_result($pickListResult, $j, 'id');
			if ($col_fields[$fieldname] == $currency_id) {
				$chk_val = 'selected';
				$found = true;
			} else {
				$chk_val = '';
			}
			$options[$currency_id] = array($pickListValue => $chk_val);
		}
		$label_fld ['options'] = $options;
	} elseif ($uitype == 13) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 16) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getTranslatedString($col_fields[$fieldname], $module);

		$fieldname = $adb->sql_escape_string($fieldname);
		$pick_query = "select $fieldname from vtiger_$fieldname order by sortorderid";
		$params = array();
		$pickListResult = $adb->pquery($pick_query, $params);
		$noofpickrows = $adb->num_rows($pickListResult);

		$options = array();
		$count = 0;
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue = decode_html($adb->query_result($pickListResult, $j, strtolower($fieldname)));
			$col_fields[$fieldname] = decode_html($col_fields[$fieldname]);

			if ($col_fields[$fieldname] == $pickListValue) {
				$chk_val = 'selected';
				$count++;
				$found = true;
			} else {
				$chk_val = '';
			}
			$pickListValue = to_html($pickListValue);
			$options[] = array(getTranslatedString($pickListValue), $pickListValue, $chk_val);
		}

		$label_fld ['options'] = $options;
	} elseif ($uitype == 1613 || $uitype == 1614 || $uitype == 1615) {
		require_once 'modules/PickList/PickListUtils.php';
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getTranslatedString($col_fields[$fieldname], $module);
		//get All the modules the current user is permitted to Access.
		$label_fld ['options'] = getPicklistValuesSpecialUitypes($uitype, $fieldname, $col_fields[$fieldname], 'DetailView');
	} elseif ($uitype == 1616) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$cvrs = $adb->pquery('select viewname, entitytype from vtiger_customview where cvid=?', array($col_fields[$fieldname]));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			$cv = $adb->fetch_array($cvrs);
			$label_fld[] = $cv['viewname'].' ('.getTranslatedString($cv['entitytype'], $cv['entitytype']).')';
		} else {
			$label_fld[] = getTranslatedString($col_fields[$fieldname], $module);
		}
	} elseif ($uitype == 15) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$col_fields[$fieldname] = trim(html_entity_decode($col_fields[$fieldname], ENT_QUOTES, $default_charset));
		$label_fld[] = $col_fields[$fieldname];
		$roleid = $current_user->roleid;

		$valueArr = trim($col_fields[$fieldname]);
		$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);

		$options = array();
		if (!empty($picklistValues)) {
			$pickcount = 0;
			foreach ($picklistValues as $order => $pickListValue) {
				if (trim($pickListValue) == $valueArr) {
					$chk_val = 'selected';
					$pickcount++;
				} else {
					$chk_val = '';
				}
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($pickListValue, $module), ENT_QUOTES, $default_charset), $pickListValue, $chk_val);
				} else {
					$options[] = array(getTranslatedString($pickListValue, $module), $pickListValue, $chk_val);
				}
			}

			if ($pickcount == 0) { // current value not found so this role does not have permission to it > we force it
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($valueArr, $module), ENT_QUOTES, $default_charset), $valueArr, 'selected');
				} else {
					$options[] = array(getTranslatedString($valueArr, $module), $valueArr, 'selected');
				}
			}
		}
		$label_fld ['options'] = $options;
	} elseif ($uitype == 1024 || $uitype == 1025) {
		require_once 'modules/PickList/PickListUtils.php';
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$content = getPicklistValuesSpecialUitypes($uitype, $fieldname, $col_fields[$fieldname], 'DetailView');
		$label_fld[] = implode(', ', $content);
	} elseif ($uitype == 115) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getTranslatedString($col_fields[$fieldname]);

		$pick_query = 'select * from vtiger_' . $adb->sql_escape_string($fieldname);
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);
		$options = array();
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue = $adb->query_result($pickListResult, $j, strtolower($fieldname));

			if ($col_fields[$fieldname] == $pickListValue) {
				$chk_val = 'selected';
				$found = true;
			} else {
				$chk_val = '';
			}
			$options[] = array($pickListValue => $chk_val);
		}
		$label_fld ['options'] = $options;
	} elseif ($uitype == 33) {
		$roleid = $current_user->roleid;
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = str_ireplace(' |##| ', ', ', $col_fields[$fieldname]);

		$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);

		$options = array();
		$selected_entries = array();
		$selected_entries = explode(' |##| ', $col_fields[$fieldname]);
		//$selected_entries = array_combine($selected_entries, $selected_entries);
		//$picklistValues = array_merge($picklistValues, $selected_entries);

		if (!empty($picklistValues)) {
			foreach ($picklistValues as $order => $pickListValue) {
				foreach ($selected_entries as $selected_entries_value) {
					if (trim($selected_entries_value) == trim(htmlentities($pickListValue, ENT_QUOTES, $default_charset))) {
						$chk_val = 'selected';
						break;
					} else {
						$chk_val = '';
					}
				}
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($pickListValue), ENT_QUOTES, $default_charset), $pickListValue, $chk_val);
				} else {
					$options[] = array(getTranslatedString($pickListValue), $pickListValue, $chk_val);
				}
			}
		}
		$label_fld ['options'] = $options;
	} elseif ($uitype == 3313 || $uitype == 3314) {
		require_once 'modules/PickList/PickListUtils.php';
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = str_ireplace(' |##| ', ', ', $col_fields[$fieldname]);
		$label_fld ['options'] = getPicklistValuesSpecialUitypes($uitype, $fieldname, $col_fields[$fieldname], 'DetailView');
	} elseif ($uitype == 17) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$matchPattern = '^[\w]+:\/\/^';
		$value = $col_fields[$fieldname];
		preg_match($matchPattern, $value, $matches);
		if (!empty($matches[0])) {
			$fieldValue = str_replace($matches, '', $value);
			$label_fld[] = $value;
		} else {
			if ($value != null) {
				$label_fld[] = 'http://'.$value;
			} else {
				$label_fld[] = '';
			}
		}
	} elseif ($uitype == 19) {
		$col_fields[$fieldname] = decode_html($col_fields[$fieldname]); // undo database encoding
		if ($fieldname == 'notecontent' || $module=='Emails' || (isset($cbMapFI['RTE']) && $cbMapFI['RTE'] && vt_hasRTE())) {
			//$col_fields[$fieldname] = htmlentities($col_fields[$fieldname]); // prepare for output
			$col_fields[$fieldname] = from_html($col_fields[$fieldname]);
		} else {
			//$col_fields[$fieldname] = preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $col_fields[$fieldname]);
			$col_fields[$fieldname] = htmlentities($col_fields[$fieldname], ENT_QUOTES, $default_charset); // prepare for output
		}
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 20 || $uitype == 21 || $uitype == 22 || $uitype == 24) {
		if ($uitype == 20) { //Fix the issue #4680
			$col_fields[$fieldname] = $col_fields[$fieldname];
		} else {
			$col_fields[$fieldname] = nl2br($col_fields[$fieldname]);
		}
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 51 || $uitype == 73) {
		$account_id = $col_fields[$fieldname];
		if ($account_id != '') {
			$account_name = getAccountName($account_id);
		}
		// vtlib customization: For listview javascript triggers
		$modMetaInfo=getEntityFieldNames('Accounts');
		$modEName=(is_array($modMetaInfo['fieldname']) ? $modMetaInfo['fieldname'][0] : $modMetaInfo['fieldname']);
		$vtlib_metainfo = "<span type='vtlib_metainfo' vtrecordid='$account_id' vtfieldname='$modEName' vtmodule='Accounts' style='display:none;'></span>";
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $account_name.$vtlib_metainfo;
		$label_fld['secid'] = $account_id;
		$label_fld['link'] = 'index.php?module=Accounts&action=DetailView&record=' . $account_id;
		//Account Name View
	} elseif ($uitype == 52 || $uitype == 77 || $uitype == 101) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$user_id = $col_fields[$fieldname];
		$user_name = getOwnerName($user_id);
		if ($user_id != '') {
			$assigned_user_id = $user_id;
		} else {
			$assigned_user_id = $current_user->id;
		}
		if (is_admin($current_user)) {
			$label_fld[] = '<a href="index.php?module=Users&action=DetailView&record=' . $user_id . '">' . $user_name . '</a>';
		} else {
			$label_fld[] = $user_name;
		}
		$tabidmodule = getTabid($module);
		if ($is_admin == false && $profileGlobalPermission[2] == 1 && isset($defaultOrgSharingPermission[$tabidmodule])
			&& ($defaultOrgSharingPermission[$tabidmodule] == 3 || $defaultOrgSharingPermission[$tabidmodule] == 0)
		) {
			$ua = get_user_array(false, 'Active', $assigned_user_id, 'private');
			$users_combo = get_select_options_array($ua, $assigned_user_id);
		} else {
			$ua = get_user_array(false, 'Active', $user_id);
			$users_combo = get_select_options_array($ua, $assigned_user_id);
		}
		$label_fld ['options'] = $users_combo;
	} elseif ($uitype == 11) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 53) {
		global $noof_group_rows, $adb;
		$owner_id = $col_fields[$fieldname];

		$user = 'no';
		$result = $adb->pquery('SELECT count(*) as count from vtiger_users where id = ?', array($owner_id));
		if ($adb->query_result($result, 0, 'count') > 0) {
			$user = 'yes';
		}

		$owner_name = getOwnerName($owner_id);
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $owner_name;

		if (is_admin($current_user)) {
			$label_fld['secid'][] = $owner_id;
			if ($user == 'no') {
				$label_fld['link'][] = 'index.php?module=Settings&action=GroupDetailView&groupId=' . $owner_id;
			} else {
				$label_fld['link'][] = 'index.php?module=Users&action=DetailView&record=' . $owner_id;
			}
			//$label_fld["secid"][] = $groupid;
			//$label_fld["link"][] = "index.php?module=Settings&action=GroupDetailView&groupId=".$groupid;
		}

		//Security Checks
		$tabidmodule = getTabid($module);
		if ($fieldname == 'assigned_user_id' && $is_admin == false && $profileGlobalPermission[2] == 1
			&& ($defaultOrgSharingPermission[$tabidmodule] == 3 || $defaultOrgSharingPermission[$tabidmodule] == 0)
		) {
			$result = get_current_user_access_groups($module);
		} else {
			$result = get_group_options();
		}
		if ($result) {
			$nameArray = $adb->fetch_array($result);
		}

		global $current_user;
		//$value = $user_id;
		if ($owner_id != '') {
			if ($user == 'yes') {
				$label_fld ['options'][] = 'User';
				$assigned_user_id = $owner_id;
				$user_checked = 'checked';
				$team_checked = '';
				$user_style = 'display:block';
				$team_style = 'display:none';
			} else {
				//$record = $col_fields["record_id"];
				//$module = $col_fields['record_module'];
				$label_fld ['options'][] = 'Group';
				//$assigned_group_id = $owner_id;
				$assigned_user_id = '';
				$user_checked = '';
				$team_checked = 'checked';
				$user_style = 'display:none';
				$team_style = 'display:block';
			}
		} else {
			$label_fld ['options'][] = 'User';
			$assigned_user_id = $current_user->id;
			$user_checked = 'checked';
			$team_checked = '';
			$user_style = 'display:block';
			$team_style = 'display:none';
		}

		if ($fieldname == 'assigned_user_id' && $is_admin == false && $profileGlobalPermission[2] == 1
			&& ($defaultOrgSharingPermission[getTabid($module)] == 3 || $defaultOrgSharingPermission[getTabid($module)] == 0)
		) {
			$user_array = get_user_array(false, 'Active', $current_user->id, 'private');
		} else {
			$user_array = get_user_array(false, 'Active', $current_user->id);
		}
		$users_combo = get_select_options_array($user_array, $assigned_user_id);

		$groups_combo = '';
		if ($noof_group_rows != 0) {
			if ($fieldname == 'assigned_user_id' && $is_admin == false && $profileGlobalPermission[2] == 1
				&& ($defaultOrgSharingPermission[getTabid($module)] == 3 || $defaultOrgSharingPermission[getTabid($module)] == 0)
			) {
				$group_array = get_group_array(false, 'Active', $current_user->id, 'private');
			} else {
				$group_array = get_group_array(false, 'Active', $current_user->id);
			}
			$groups_combo = get_select_options_array($group_array, $current_user->id);
		}
		if (GlobalVariable::getVariable('Application_Group_Selection_Permitted', 1)!=1) {
			$groups_combo = '';
		}
		$label_fld ['options'][] = $users_combo;
		$label_fld ['options'][] = $groups_combo;
	} elseif ($uitype == 55 || $uitype == 255) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$value = $col_fields[$fieldname];
		if ($uitype == 255) {
			global $currentModule;
			$fieldpermission = getFieldVisibilityPermission($currentModule, $current_user->id, 'firstname');
		}
		if ($uitype == 255 && $fieldpermission == 0 && $fieldpermission != '') {
			$fieldvalue[] = '';
		} else {
			$roleid = $current_user->roleid;
			$subrole = getRoleSubordinates($roleid);
			if (count($subrole) > 0) {
				$roleids = implode("','", $subrole);
				$roleids = $roleids . "','" . $roleid;
			} else {
				$roleids = $roleid;
			}
			if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
				$pick_query = 'select salutationtype from vtiger_salutationtype order by salutationtype';
				$params = array();
			} else {
				$pick_query = "select salutationtype
					from vtiger_salutationtype
					left join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid=vtiger_salutationtype.picklist_valueid
					where picklistid in (select picklistid from vtiger_picklist where name='salutationtype') and roleid=?
					order by salutationtype";
				$params = array($current_user->roleid);
			}
			$pickListResult = $adb->pquery($pick_query, $params);
			$noofpickrows = $adb->num_rows($pickListResult);
			$sal_value = $col_fields['salutationtype'];
			$salcount = 0;
			for ($j = 0; $j < $noofpickrows; $j++) {
				$pickListValue = $adb->query_result($pickListResult, $j, 'salutationtype');

				if ($sal_value == $pickListValue) {
					$chk_val = 'selected';
					$salcount++;
				} else {
					$chk_val = '';
				}
			}
			$notacc = '';
			if ($salcount == 0 && $sal_value != '') {
				$notacc = $app_strings['LBL_NOT_ACCESSIBLE'];
			}
			$sal_value = $col_fields['salutationtype'];
			if ($sal_value == '--None--') {
				$sal_value = '';
			}
			$label_fld['salut'] = getTranslatedString($sal_value);
			$label_fld['notaccess'] = $notacc;
		}
		$label_fld[] = $value;
	} elseif ($uitype == 56) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$value = $col_fields[$fieldname];
		if ($value == 1) {
			//Since "yes" is not been translated it is given as app strings here..
			$displayValue = $app_strings['yes'];
		} else {
			$displayValue = $app_strings['no'];
		}
		$label_fld[] = $displayValue;
	} elseif ($uitype == 156) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$value = $col_fields[$fieldname];
		if ($value == 'on') {
			//Since "yes" is not been translated it is given as app strings here..
			$displayValue = $app_strings['yes'];
		} else {
			$displayValue = $app_strings['no'];
		}
		$label_fld[] = $displayValue;
	} elseif ($uitype == 57) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$contact_id = $col_fields[$fieldname];
		$contact_name = '';
		if ($contact_id != '') {
			$displayValueArray = getEntityName('Contacts', $contact_id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $field_value) {
					$contact_name = $field_value;
				}
			}
		}
		$label_fld[] = $contact_name;
		$label_fld['secid'] = $contact_id;
		$label_fld['link'] = 'index.php?module=Contacts&action=DetailView&record=' . $contact_id;
	} elseif ($uitype == 61) {
		global $adb;
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$custfldval = '';
		if ($tabid == 10) {
			$attach_result = $adb->pquery('select * from vtiger_seattachmentsrel where crmid = ?', array($col_fields['record_id']));
			for ($ii = 0; $ii < $adb->num_rows($attach_result); $ii++) {
				$attachmentid = $adb->query_result($attach_result, $ii, 'attachmentsid');
				if ($attachmentid != '') {
					$rs = $adb->pquery('select * from vtiger_attachments where attachmentsid=?', array($attachmentid));
					$attachmentsname = $adb->query_result($rs, 0, 'name');
					if ($attachmentsname != '') {
						$custfldval = '<a href = "index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=downloadfile&return_module='
							. $col_fields['record_module'] . '&fileid=' . $attachmentid . '&entityid=' . $col_fields['record_id'] . '">' . $attachmentsname . '</a>';
					}
				}
				$label_fld['options'][] = $custfldval;
			}
		} else {
			$rsatt = $adb->pquery('select * from vtiger_seattachmentsrel where crmid = ?', array($col_fields['record_id']));
			$attachmentid = $adb->query_result($rsatt, 0, 'attachmentsid');
			if ($col_fields[$fieldname] == '' && $attachmentid != '') {
				$rsatt = $adb->pquery('select * from vtiger_attachments where attachmentsid=?', array($attachmentid));
				$col_fields[$fieldname] = $adb->query_result($rsatt, 0, 'name');
			}

			//This is added to strip the crmid and _ from the file name and show the original filename
			//$org_filename = ltrim($col_fields[$fieldname],$col_fields['record_id'].'_');
			/* Above line is not required as the filename in the database is stored as it is and doesn't have crmid attached to it.
			  This was the cause for the issue reported in ticket #4645 */
			$org_filename = $col_fields[$fieldname];
			// For Backward Compatibility version < 5.0.4
			$filename_pos = strpos($org_filename, $col_fields['record_id'] . '_');
			if ($filename_pos === 0) {
				$start_idx = $filename_pos + strlen($col_fields['record_id'] . '_');
				$org_filename = substr($org_filename, $start_idx);
			}
			if ($org_filename != '') {
				if ($col_fields['filelocationtype'] == 'E') {
					if ($col_fields['filestatus'] == 1) {//&& strlen($col_fields['filename']) > 7  ){
						$custfldval = '<a target="_blank" href =' . $col_fields['filename'] . ' onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id']
							. ');\'>' . $col_fields[$fieldname] . '</a>';
					} else {
						$custfldval = $col_fields[$fieldname];
					}
				} elseif ($col_fields['filelocationtype'] == 'I') {
					if ($col_fields['filestatus'] == 1) {
						$custfldval = '<a href = "index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=downloadfile&return_module='
							. $col_fields['record_module'] . '&fileid=' . $attachmentid . '&entityid=' . $col_fields['record_id']
							.'" onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>' . $col_fields[$fieldname] . '</a>';
					} else {
						$custfldval = $col_fields[$fieldname];
					}
				}
			}
		}
		$label_fld[] = $custfldval;
	} elseif ($uitype == 28) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$rs = $adb->pquery('select * from vtiger_seattachmentsrel where crmid = ?', array($col_fields['record_id']));
		$attachmentid = $adb->query_result($rs, 0, 'attachmentsid');
		if ($col_fields[$fieldname] == '' && $attachmentid != '') {
			$rs = $adb->pquery('select name from vtiger_attachments where attachmentsid=?', array($attachmentid));
			$col_fields[$fieldname] = $adb->query_result($rs, 0, 'name');
		}
		$org_filename = $col_fields[$fieldname];
		// For Backward Compatibility version < 5.0.4
		$filename_pos = strpos($org_filename, $col_fields['record_id'] . '_');
		if ($filename_pos === 0) {
			$start_idx = $filename_pos + strlen($col_fields['record_id'] . '_');
			$org_filename = substr($org_filename, $start_idx);
		}
		$custfldval = '';
		if ($org_filename != '') {
			if ($col_fields['filelocationtype'] == 'E') {
				if ($col_fields['filestatus'] == 1) {//&& strlen($col_fields['filename']) > 7  ){
					$custfldval = '<a target="_blank" href =' . $col_fields['filename'] . ' onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>'
						. $col_fields[$fieldname] . '</a>';
				} else {
					$custfldval = $col_fields[$fieldname];
				}
			} elseif ($col_fields['filelocationtype'] == 'I') {
				if ($col_fields['filestatus'] == 1) {
					$custfldval = '<a href = "index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=downloadfile&return_module='
						. $col_fields['record_module'] . '&fileid=' . $attachmentid . '&entityid=' . $col_fields['record_id']
						. '" onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>' . $col_fields[$fieldname] . '</a>';
					$image_res = $adb->pquery('SELECT path,name FROM vtiger_attachments WHERE attachmentsid = ?', array($attachmentid));
					$image_path = $adb->query_result($image_res, 0, 'path');
					$image_name = decode_html($adb->query_result($image_res, 0, 'name'));
					$imgpath = $image_path . $attachmentid . '_' . urlencode($image_name);
					if (stripos($col_fields['filetype'], 'image') !== false) {
						$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('Image');
						$custfldval .= '<br/><img src="' . $imgpath . '" alt="' . $imgtxt . '" title= "' . $imgtxt . '" style="max-width:300px; max-height:300px">';
					} elseif (stripos($col_fields['filetype'], 'video') !== false) {
						$custfldval .= '<br/><video width="300px" height="300px" controls><source src="' . $imgpath . '" type="' . $col_fields['filetype'] . '"></video>';
					}
				} else {
					$custfldval = $col_fields[$fieldname];
				}
			}
		}
		$label_fld[] = $custfldval;
	} elseif ($uitype == '69m') {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($tabid == 14) {
			$images = array();
			$query = 'select productname, vtiger_attachments.path, vtiger_attachments.attachmentsid, vtiger_attachments.name,vtiger_crmentity.setype
				from vtiger_products
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid
				inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
				where vtiger_crmentity.setype="Products Image" and productid=?';
			$result_image = $adb->pquery($query, array($col_fields['record_id']));
			$image_array = array();
			for ($image_iter = 0; $image_iter < $adb->num_rows($result_image); $image_iter++) {
				$image_id_array[] = $adb->query_result($result_image, $image_iter, 'attachmentsid');

				//decode_html  - added to handle UTF-8   characters in file names
				//urlencode    - added to handle special characters like #, %, etc.,
				$image_array[] = urlencode(decode_html($adb->query_result($result_image, $image_iter, 'name')));
				$image_orgname_array[] = decode_html($adb->query_result($result_image, $image_iter, 'name'));

				$imagepath_array[] = $adb->query_result($result_image, $image_iter, 'path');
			}
			if (count($image_array) > 1) {
				if (count($image_array) < 4) {
					$sides = count($image_array) * 2;
				} else {
					$sides=8;
				}

				$image_lists = '<div id="Carousel" style="position:relative;vertical-align: middle;">
					<img src="modules/Products/placeholder.gif" width="571" height="117" style="position:relative;">
					</div><script>var Car_NoOfSides=' . $sides . '; Car_Image_Sources=new Array(';

				for ($image_iter = 0, $image_iterMax = count($image_array); $image_iter < $image_iterMax; $image_iter++) {
					$images[] = '"' . $imagepath_array[$image_iter] . $image_id_array[$image_iter] . "_" . $image_array[$image_iter] . '","'
						. $imagepath_array[$image_iter] . $image_id_array[$image_iter] . "_" . $image_array[$image_iter] . '"';
				}
				$image_lists .= implode(',', $images) . ');</script>';
				$image_lists .= '<script type="text/javascript" src="modules/Products/Productsslide.js"></script><script type="text/javascript">Carousel();</script>';
				$label_fld[] = $image_lists;
			} elseif (count($image_array) == 1) {
				$label_fld[] = '<img src="' . $imagepath_array[0] . $image_id_array[0] .'_'. $image_array[0] . '" border="0" style="max-width:300px; max-height:300px">';
			} else {
				$label_fld[] = '';
			}
		}
	} elseif ($uitype == 69) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($module == 'Contacts') {
			$imageattachment = 'Image';
		} else {
			$imageattachment = 'Attachment';
		}
		$sql = "select vtiger_attachments.*,vtiger_crmentity.setype
		 from vtiger_attachments
		 inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
		 inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
		 where vtiger_crmentity.setype='$module $imageattachment'
		  and vtiger_attachments.name = ? and vtiger_seattachmentsrel.crmid=?";
		$image_res = $adb->pquery($sql, array(str_replace(' ', '_', decode_html($col_fields[$fieldname])),$col_fields['record_id']));
		if ($adb->num_rows($image_res)==0) {
			$sql = 'select vtiger_attachments.*,vtiger_crmentity.setype
			 from vtiger_attachments
			 inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			 inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
			 where vtiger_attachments.name = ? and vtiger_seattachmentsrel.crmid=?';
			$image_res = $adb->pquery($sql, array(str_replace(' ', '_', $col_fields[$fieldname]),$col_fields['record_id']));
		}
		if ($adb->num_rows($image_res)>0) {
			$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
			$image_path = $adb->query_result($image_res, 0, 'path');
			//decode_html  - added to handle UTF-8   characters in file names
			//urlencode    - added to handle special characters like #, %, etc.,
			$image_name = decode_html($adb->query_result($image_res, 0, 'name'));
			$imgpath = $image_path . $image_id . "_" . urlencode($image_name);
			if ($image_name != '') {
				$ftype = $adb->query_result($image_res, 0, 'type');
				$isimage = stripos($ftype, 'image') !== false;
				$isvideo = stripos($ftype, 'video') !== false;
				if ($isimage) {
					$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('Image');
					$label_fld[] = '<img src="' . $imgpath . '" alt="' . $imgtxt . '" title= "' . $imgtxt . '" style="max-width:300px; max-height:300px">';
				} elseif ($isvideo) {
					$label_fld[] = '<video width="300px" height="300px" controls><source src="' . $imgpath . '" type="' . $ftype . '"></video>';
				} else {
					$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('SINGLE_Documents');
					$label_fld[] = '<a href="' . $imgpath . '" alt="' . $imgtxt . '" title= "' . $imgtxt . '" target="_blank">'.$image_name.'</a>';
				}
			} else {
				$label_fld[] = '';
			}
		} else {
			$label_fld[] = '';
		}
	} elseif ($uitype == 62) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == "Leads") {
				$label_fld[] = $app_strings['LBL_LEAD_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$lead_name = $field_value;
					}
				}

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
			} elseif ($parent_module == "Accounts") {
				$label_fld[] = $app_strings['LBL_ACCOUNT_NAME'];
				$sql = "select accountname from vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$account_name = $adb->query_result($result, 0, "accountname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $account_name . '</a>';
			} elseif ($parent_module == "Potentials") {
				$label_fld[] = $app_strings['LBL_POTENTIAL_NAME'];
				$sql = "select potentialname from vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$potentialname = $adb->query_result($result, 0, "potentialname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $potentialname . '</a>';
			} elseif ($parent_module == "Products") {
				$label_fld[] = $app_strings['LBL_PRODUCT_NAME'];
				$sql = "select productname from vtiger_products where productid=?";
				$result = $adb->pquery($sql, array($value));
				$productname = $adb->query_result($result, 0, "productname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $productname . '</a>';
			} elseif ($parent_module == "PurchaseOrder") {
				$label_fld[] = $app_strings['LBL_PORDER_NAME'];
				$sql = "select subject from vtiger_purchaseorder where purchaseorderid=?";
				$result = $adb->pquery($sql, array($value));
				$pordername = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $pordername . '</a>';
			} elseif ($parent_module == "SalesOrder") {
				$label_fld[] = $app_strings['LBL_SORDER_NAME'];
				$sql = "select subject from vtiger_salesorder where salesorderid=?";
				$result = $adb->pquery($sql, array($value));
				$sordername = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $sordername . '</a>';
			} elseif ($parent_module == "Invoice") {
				$label_fld[] = $app_strings['LBL_INVOICE_NAME'];
				$sql = "select subject from vtiger_invoice where invoiceid=?";
				$result = $adb->pquery($sql, array($value));
				$invoicename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $invoicename . '</a>';
			} elseif ($parent_module == "Quotes") {
				$label_fld[] = $app_strings['LBL_QUOTES_NAME'];
				$sql = "select subject from vtiger_quotes where quoteid=?";
				$result = $adb->pquery($sql, array($value));
				$quotename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $quotename . '</a>';
			} elseif ($parent_module == "HelpDesk") {
				$label_fld[] = $app_strings['LBL_HELPDESK_NAME'];
				$sql = "select title from vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$title = $adb->query_result($result, 0, "title");
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $title . '</a>';
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 105) {//Added for user image
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$sql = 'select vtiger_attachments.attachmentsid, vtiger_attachments.path, vtiger_attachments.name
			from vtiger_attachments
			left join vtiger_salesmanattachmentsrel on vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			where vtiger_salesmanattachmentsrel.smid=?';
		$image_res = $adb->pquery($sql, array($col_fields['record_id']));
		$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
		$image_path = $adb->query_result($image_res, 0, 'path');
		$image_name = $adb->query_result($image_res, 0, 'name');
		$imgpath = $image_path . $image_id . "_" . $image_name;
		if ($image_name != '') {
			//Added the following check for the image to retain its in original size.
			list($pro_image_width, $pro_image_height) = getimagesize(decode_html($imgpath));
			$label_fld[] = '<a href="' . $imgpath . '" target="_blank"><img src="' . $imgpath . '" width="' . $pro_image_width . '" height="' . $pro_image_height
				. '" alt="' . $col_fields['user_name'] . '" title="' . $col_fields['user_name'] . '" border="0"></a>';
		} else {
			$label_fld[] = '';
		}
	} elseif ($uitype == 66) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == 'Leads') {
				$label_fld[] = $app_strings['LBL_LEAD_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$lead_name = $field_value;
					}
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
			} elseif ($parent_module == "Accounts") {
				$label_fld[] = $app_strings['LBL_ACCOUNT_NAME'];
				$sql = "select accountname from vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$account_name = $adb->query_result($result, 0, "accountname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $account_name . '</a>';
			} elseif ($parent_module == "Potentials") {
				$label_fld[] = $app_strings['LBL_POTENTIAL_NAME'];
				$sql = "select potentialname from vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$potentialname = $adb->query_result($result, 0, "potentialname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $potentialname . '</a>';
			} elseif ($parent_module == "Quotes") {
				$label_fld[] = $app_strings['LBL_QUOTE_NAME'];
				$sql = "select subject from vtiger_quotes where quoteid=?";
				$result = $adb->pquery($sql, array($value));
				$quotename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $quotename . '</a>';
			} elseif ($parent_module == "PurchaseOrder") {
				$label_fld[] = $app_strings['LBL_PORDER_NAME'];
				$sql = "select subject from vtiger_purchaseorder where purchaseorderid=?";
				$result = $adb->pquery($sql, array($value));
				$pordername = $adb->query_result($result, 0, 'subject');

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $pordername . '</a>';
			} elseif ($parent_module == "SalesOrder") {
				$label_fld[] = $app_strings['LBL_SORDER_NAME'];
				$sql = "select subject from vtiger_salesorder where salesorderid=?";
				$result = $adb->pquery($sql, array($value));
				$sordername = $adb->query_result($result, 0, 'subject');

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $sordername . '</a>';
			} elseif ($parent_module == "Invoice") {
				$label_fld[] = $app_strings['LBL_INVOICE_NAME'];
				$sql = "select subject from vtiger_invoice where invoiceid=?";
				$result = $adb->pquery($sql, array($value));
				$invoicename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $invoicename . '</a>';
			} elseif ($parent_module == "Campaigns") {
				$label_fld[] = $app_strings['LBL_CAMPAIGN_NAME'];
				$sql = "select campaignname from vtiger_campaign where campaignid=?";
				$result = $adb->pquery($sql, array($value));
				$campaignname = $adb->query_result($result, 0, "campaignname");
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $campaignname . '</a>';
			} elseif ($parent_module == "HelpDesk") {
				$label_fld[] = $app_strings['LBL_HELPDESK_NAME'];
				$sql = "select title from vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$tickettitle = $adb->query_result($result, 0, 'title');
				if (strlen($tickettitle) > 25) {
					$tickettitle = substr($tickettitle, 0, 25) . '...';
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $tickettitle . '</a>';
			} elseif ($parent_module == "Vendors") { //MSL
				$label_fld[] = $app_strings['LBL_VENDOR_NAME'];
				$sql = "select vendorname from vtiger_vendor where vendorid=?";
				$result = $adb->pquery($sql, array($value));
				$vendor_name = $adb->query_result($result, 0, 'vendorname');
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $vendor_name . '</a>';
			} else {
				$label_fld[] = '';
				$label_fld[] = '';
			}
			// vtlib customization: For listview javascript triggers
			$modMetaInfo=getEntityFieldNames($parent_module);
			$modEName=(is_array($modMetaInfo['fieldname']) ? $modMetaInfo['fieldname'][0] : $modMetaInfo['fieldname']);
			$vtlib_metainfo = "<span type='vtlib_metainfo' vtrecordid='$value' vtfieldname='$modEName' vtmodule='$parent_module' style='display:none;'></span>";

			$last_lbl_fld = count($label_fld) - 1;
			$label_fld[$last_lbl_fld] .= $vtlib_metainfo;
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 67) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == 'Leads') {
				$label_fld[] = $app_strings['LBL_LEAD_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$lead_name = $field_value;
					}
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
			} elseif ($parent_module == 'Contacts') {
				$label_fld[] = $app_strings['LBL_CONTACT_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$contact_name = $field_value;
					}
				} else {
					$contact_name='';
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $contact_name . '</a>';
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 357) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_id = '';
			$myemailid = vtlib_purify($_REQUEST['record']);
			$myresult = $adb->pquery('select crmid from vtiger_seactivityrel where activityid=?', array($myemailid));
			$mycount = $adb->num_rows($myresult);
			if ($mycount > 1) {
				$label_fld[] = $app_strings['LBL_RELATED_TO'];
				$label_fld[] = $app_strings['LBL_MULTIPLE'];
			} else {
				$value = substr($value, 0, strpos($value, '@'));
				$parent_module = getSalesEntityType($value);
				$label_fld[] = getTranslatedString($parent_module, $parent_module);
				$ename = getEntityName($parent_module, $value);
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $ename[$value] . '</a>';
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 63) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname] . 'h&nbsp; ' . $col_fields['duration_minutes'] . 'm';
	} elseif ($uitype == 6) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($col_fields[$fieldname] == '0') {
			$col_fields[$fieldname] = '';
		}
		if ($col_fields['time_start'] != '') {
			$start_time = $col_fields['time_start'];
		}
		$dateValue = $col_fields[$fieldname];
		if ($col_fields[$fieldname] == '0000-00-00' || empty($dateValue)) {
			$displayValue = '';
		} else {
			if (empty($start_time) && strpos($col_fields[$fieldname], ' ') == false) {
				$displayValue = DateTimeField::convertToUserFormat($col_fields[$fieldname]);
			} else {
				if (!empty($start_time)) {
					$date = new DateTimeField($col_fields[$fieldname].' '.$start_time);
				} else {
					$date = new DateTimeField($col_fields[$fieldname]);
				}
				$displayValue = $date->getDisplayDateTimeValue();
			}
		}
		$label_fld[] = $displayValue;
	} elseif ($uitype == 5 || $uitype == 23 || $uitype == 70) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$dateValue = $col_fields[$fieldname];
		if (empty($dateValue) || $dateValue == '0000-00-00') {
			$displayValue = '';
		} else {
			if (strpos($dateValue, ' ') == false) {
				$displayValue = DateTimeField::convertToUserFormat($col_fields[$fieldname]);
			} else {
				$date = new DateTimeField($col_fields[$fieldname]);
				$displayValue = $date->getDisplayDateTimeValue();
			}
		}
		$label_fld[] = $displayValue;
	} elseif ($uitype == 50) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$dateValue = $col_fields[$fieldname];
		$user_format = ($current_user->hour_format=='24' ? '24' : '12');
		if (empty($dateValue) || $dateValue == '0000-00-00 00:00') {
			$displayValue = '';
			$time_format = $user_format;
		} else {
			$date = new DateTimeField($col_fields[$fieldname]);
			$displayValue = substr($date->getDisplayDateTimeValue(), 0, 16);
			if ($user_format != '24') {
				$curr_time = DateTimeField::formatUserTimeString($displayValue, '12');
				$time_format = substr($curr_time, -2);
				$curr_time = substr($curr_time, 0, 5);
				list($dt,$tm) = explode(' ', $displayValue);
				$displayValue = $dt . ' ' . $curr_time;
			} else {
				$time_format = '24';
			}
		}
		$label_fld[] = $displayValue;
		$label_fld['options'] = array($user_format => $time_format);
	} elseif ($uitype == 9 || $uitype == 7) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$fldrs = $adb->pquery('select typeofdata from vtiger_field where vtiger_field.fieldname=? and vtiger_field.tabid=?', array($fieldname, $tabid));
		$typeofdata = $adb->query_result($fldrs, 0, 0);
		$typeinfo = explode('~', $typeofdata);
		if ($typeinfo[0]=='I') {
			$label_fld[] = $col_fields[$fieldname];
		} else {
			$currencyField = new CurrencyField($col_fields[$fieldname]);
			$decimals = CurrencyField::getDecimalsFromTypeOfData($typeofdata);
			$currencyField->initialize($current_user);
			$currencyField->setNumberofDecimals(min($decimals, $currencyField->getCurrencyDecimalPlaces()));
			$label_fld[] = $currencyField->getDisplayValue(null, true, true);
		}
	} elseif ($uitype == 71 || $uitype == 72) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$currencyField = new CurrencyField($col_fields[$fieldname]);
		$fldrs = $adb->pquery('select typeofdata from vtiger_field where vtiger_field.fieldname=? and vtiger_field.tabid=?', array($fieldname, $tabid));
		$typeofdata = $adb->query_result($fldrs, 0, 0);
		$typeinfo = explode('~', $typeofdata);
		if ($uitype == 72) {
			// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
			if ($fieldname == 'unit_price') {
				$rate_symbol = getCurrencySymbolandCRate(getProductBaseCurrency($col_fields['record_id'], $module));
				$label_fld[] = $currencyField->getDisplayValue(null, true);
				$label_fld['cursymb'] = $rate_symbol['symbol'];
			} else {
				$currency_info = getInventoryCurrencyInfo($module, $col_fields['record_id']);
				$label_fld[] = $currencyField->getDisplayValue(null, true);
				$label_fld['cursymb'] = $currency_info['currency_symbol'];
			}
		} else {
			$decimals = CurrencyField::getDecimalsFromTypeOfData($typeofdata);
			$currencyField->initialize($current_user);
			$currencyField->setNumberofDecimals(min($decimals, $currencyField->getCurrencyDecimalPlaces()));
			$label_fld[] = $currencyField->getDisplayValue(null, false, true);
			$label_fld['cursymb'] = $currencyField->getCurrencySymbol();
		}
	} elseif ($uitype == 76) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$potential_id = $col_fields[$fieldname];
		$potential_name = (empty($potential_id) ? '' : getPotentialName($potential_id));
		$label_fld[] = $potential_name;
		$label_fld['secid'] = $potential_id;
		$label_fld['link'] = 'index.php?module=Potentials&action=DetailView&record=' . $potential_id;
	} elseif ($uitype == 78) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$quote_id = $col_fields[$fieldname];
		$quote_name = (empty($quote_id) ? '' : getQuoteName($quote_id));
		$label_fld[] = $quote_name;
		$label_fld['secid'] = $quote_id;
		$label_fld['link'] = 'index.php?module=Quotes&action=DetailView&record=' . $quote_id;
	} elseif ($uitype == 79) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$purchaseorder_id = $col_fields[$fieldname];
		$purchaseorder_name = (empty($purchaseorder_id) ? '' : getPoName($purchaseorder_id));
		$label_fld[] = $purchaseorder_name;
		$label_fld['secid'] = $purchaseorder_id;
		$label_fld['link'] = 'index.php?module=PurchaseOrder&action=DetailView&record=' . $purchaseorder_id;
	} elseif ($uitype == 80) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$salesorder_id = $col_fields[$fieldname];
		$salesorder_name = (empty($salesorder_id) ? '' : getSoName($salesorder_id));
		$label_fld[] = $salesorder_name;
		$label_fld['secid'] = $salesorder_id;
		$label_fld['link'] = 'index.php?module=SalesOrder&action=DetailView&record=' . $salesorder_id;
	} elseif ($uitype == 30) {
		if ($col_fields[$fieldname]) {
			$rem_days = floor($col_fields[$fieldname] / (24 * 60));
			$rem_hrs = floor(($col_fields[$fieldname] - $rem_days * 24 * 60) / 60);
			$rem_min = ($col_fields[$fieldname] - $rem_days * 24 * 60) % 60;
			$reminder_str = $rem_days . '&nbsp;' . getTranslatedString('LBL_DAYS', 'Calendar') . '&nbsp;' . $rem_hrs . '&nbsp;'
				. getTranslatedString('LBL_HOURS', 'Calendar') . '&nbsp;' . $rem_min . '&nbsp;' . getTranslatedString('LBL_MINUTES', 'Calendar') . '&nbsp;&nbsp;'
				. getTranslatedString('LBL_BEFORE_EVENT', 'Calendar');
		} else {
			$rem_days = 0;
			$rem_hrs = 0;
			$rem_min = 0;
			$reminder_str = '';
		}
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = '&nbsp;' . $reminder_str;
	} elseif ($uitype == 98) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if (is_admin($current_user)) {
			$label_fld[] = '<a href="index.php?module=Settings&action=RoleDetailView&roleid='.$col_fields[$fieldname].'">'.getRoleName($col_fields[$fieldname]).'</a>';
		} else {
			$label_fld[] = getRoleName($col_fields[$fieldname]);
		}
	} elseif ($uitype == 85) { //Added for Skype by Minnie
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 26) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$result = $adb->pquery('select foldername from vtiger_attachmentsfolder where folderid = ?', array($col_fields[$fieldname]));
		$folder_name = $adb->query_result($result, 0, 'foldername');
		$label_fld[] = $folder_name;
	} elseif ($uitype == 27) {
		if ($col_fields[$fieldname] == 'I') {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $mod_strings['LBL_INTERNAL'];
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $mod_strings['LBL_EXTERNAL'];
		}
	} elseif ($uitype == 31) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
		$options = array();
		$themeList = get_themes();
		foreach ($themeList as $theme) {
			if ($current_user->theme == $theme) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$options[] = array(getTranslatedString($theme), $theme, $selected);
		}
		$label_fld ['options'] = $options;
	} elseif ($uitype == 32) {
		$options = array();
		$languageList = Vtiger_Language::getAll();
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = isset($languageList[$col_fields[$fieldname]]) ? $languageList[$col_fields[$fieldname]] : $col_fields[$fieldname];
		foreach ($languageList as $prefix => $label) {
			if ($current_user->language == $prefix) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$options[] = array(getTranslatedString($label), $prefix, $selected);
		}
		$label_fld ['options'] = $options;
	} else {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($col_fields[$fieldname] == '0' && $fieldname != 'filedownloadcount' && $fieldname != 'filestatus' && $fieldname != 'filesize') {
			$col_fields[$fieldname] = '';
		}

		//code for Documents module :start
		if ($tabid == 8) {
			$downloadtype = $col_fields['filelocationtype'];
			if ($fieldname == 'filename') {
				if ($downloadtype == 'I') {
					//$file_value = $mod_strings['LBL_INTERNAL'];
					$fld_value = $col_fields['filename'];
					$ext_pos = strrpos($fld_value, ".");
					$ext = substr($fld_value, $ext_pos + 1);
					$ext = strtolower($ext);
					if ($ext == 'bin' || $ext == 'exe' || $ext == 'rpm') {
						$fileicon = "<img src='" . vtiger_imageurl('fExeBin.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					} elseif ($ext == 'jpg' || $ext == 'gif' || $ext == 'bmp') {
						$fileicon = "<img src='" . vtiger_imageurl('fbImageFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					} elseif ($ext == 'txt' || $ext == 'doc' || $ext == 'xls') {
						$fileicon = "<img src='" . vtiger_imageurl('fbTextFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					} elseif ($ext == 'zip' || $ext == 'gz' || $ext == 'rar') {
						$fileicon = "<img src='" . vtiger_imageurl('fbZipFile.gif', $theme) . "' hspace='3' align='absmiddle'	border='0'>";
					} else {
						$fileicon="<img src='" . vtiger_imageurl('fbUnknownFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					}
				} else {
					$fld_value = $col_fields['filename'];
					$fileicon = "<img src='" . vtiger_imageurl('fbLink.gif', $theme) . "' alt='" . $mod_strings['LBL_EXTERNAL_LNK']
						. "' title='" . $mod_strings['LBL_EXTERNAL_LNK'] . "' hspace='3' align='absmiddle' border='0'>";
				}
				$label_fld[] = $fileicon . $fld_value;
			}
			if ($fieldname == 'filesize') {
				if ($col_fields['filelocationtype'] == 'I') {
					$filesize = $col_fields[$fieldname];
					if ($filesize < 1024) {
						$label_fld[] = $filesize . ' B';
					} elseif ($filesize > 1024 && $filesize < 1048576) {
						$label_fld[] = round($filesize / 1024, 2) . ' KB';
					} elseif ($filesize > 1048576) {
						$label_fld[] = round($filesize / (1024 * 1024), 2) . ' MB';
					}
				} else {
					$label_fld[] = ' --';
				}
			}
			if ($fieldname == 'filetype' && $col_fields['filelocationtype'] == 'E') {
				$label_fld[] = ' --';
			}
			/* if($fieldname == 'filestatus')
			  {
			  $filestatus = $col_fields[$fieldname];
			  if($filestatus == 0)
			  $label_fld[]=$mod_strings['LBL_ACTIVE'];
			  else
			  $label_fld[]=$mod_strings['LBL_INACTIVE'];
			  } */
		}
		//code for Documents module :end
		$label_fld[] = $col_fields[$fieldname];
	}
	$label_fld[] = $uitype;

	//sets whether the currenct user is admin or not
	if (is_admin($current_user)) {
		$label_fld['isadmin'] = 1;
	} else {
		$label_fld['isadmin'] = 0;
	}

	$log->debug('Exiting getDetailViewOutputHtml method ...');
	return $label_fld;
}

/** This function returns a HTML output of associated products for a given entity (Quotes,Invoice,Sales order or Purchase order)
 * Param $module - module name
 * Param $focus - module object
 * Return type string
 */
function getDetailAssociatedProducts($module, $focus) {
	global $log, $adb, $theme, $log, $app_strings;
	$log->debug('Entering getDetailAssociatedProducts(' . $module . ',' . get_class($focus) . ') method ...');

	if (vtlib_isModuleActive('Products')) {
		$hide_stock = 'no';
	} else {
		$hide_stock = 'yes';
	}
	if ($module != 'PurchaseOrder') {
		if (GlobalVariable::getVariable('Application_B2B', '1')=='1') {
			$acvid = (isset($focus->column_fields['account_id']) ? $focus->column_fields['account_id'] : (isset($focus->column_fields['accid']) ? $focus->column_fields['accid'] : 0));
		} else {
			$acvid = (isset($focus->column_fields['contact_id']) ? $focus->column_fields['contact_id'] : (isset($focus->column_fields['ctoid']) ? $focus->column_fields['ctoid'] : 0));
		}
	} else {
		$acvid = $focus->column_fields['vendor_id'];
	}

	$cbMap = cbMap::getMapByName($module.'InventoryDetails', 'MasterDetailLayout');
	$MDMapFound = ($cbMap!=null);
	if ($MDMapFound) {
		$cbMapFields = $cbMap->MasterDetailLayout();
	}

	//Get the taxtype of this entity
	$taxtype = getInventoryTaxType($module, $focus->id);
	$currencytype = getInventoryCurrencyInfo($module, $focus->id);

	$output = '';
	//Header Rows
	$output .= '
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable detailview_inventory_table" id="proTab">
	<tr valign="top" class="detailview_inventory_header">
		<td colspan="2" class="dvInnerHeader"><b>' . $app_strings['LBL_ITEM_DETAILS'] . '</b></td>
		<td class="dvInnerHeader" align="center" colspan="2"><b>' .
			$app_strings['LBL_CURRENCY'] . ' : </b>' . getTranslatedCurrencyString($currencytype['currency_name']) . ' (' . $currencytype['currency_symbol'] . ')
		</td>
		<td class="dvInnerHeader" align="center" colspan="2"><b>' .
			$app_strings['LBL_TAX_MODE'] . ' : </b>' . $app_strings[$taxtype] . '
		</td>
	</tr>
	<tr valign="top" class="detailview_inventory_subheader">
		<td width=40% class="lvtCol"><font color="red">*</font>
			<b>' . $app_strings['LBL_ITEM_NAME'] . '</b>
		</td>';

	//Additional information column
	$output .= '<td width=20% class="lvtCol"><b>' . $app_strings['LBL_INFORMATION'] . '</b></td>';

	$output .= '
		<td width=10% class="lvtCol"><b>' . $app_strings['LBL_QTY'] . '</b></td>
		<td width=10% class="lvtCol" align="right"><b>' . $app_strings['LBL_LIST_PRICE'] . '</b></td>
		<td width=10% nowrap class="lvtCol" align="right"><b>' . $app_strings['LBL_TOTAL'] . '</b></td>
		<td width=10% valign="top" class="lvtCol" align="right"><b>' . $app_strings['LBL_NET_PRICE'] . '</b></td>
	</tr>';

	if (in_array($module, getInventoryModules())) {
		$query = "select case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname," .
			" case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype," .
			" case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price," .
			" case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock, vtiger_inventoryproductrel.* " .
			" from vtiger_inventoryproductrel" .
			" left join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid " .
			" left join vtiger_service on vtiger_service.serviceid=vtiger_inventoryproductrel.productid " .
			" where id=? ORDER BY sequence_no";
	}

	$result = $adb->pquery($query, array($focus->id));
	$num_rows = $adb->num_rows($result);
	$netTotal = '0.00';
	for ($i = 1; $i <= $num_rows; $i++) {
		$sub_prod_query = $adb->pquery('SELECT productid from vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?', array($focus->id, $i));
		$subprodname_str = '';
		if ($adb->num_rows($sub_prod_query) > 0) {
			for ($j = 0; $j < $adb->num_rows($sub_prod_query); $j++) {
				$sprod_id = $adb->query_result($sub_prod_query, $j, 'productid');
				$sprod_name = getProductName($sprod_id);
				$str_sep = '';
				if ($j > 0) {
					$str_sep = ':';
				}
				$subprodname_str .= $str_sep . ' - ' . $sprod_name;
			}
		}
		$subprodname_str = str_replace(':', '<br>', $subprodname_str);

		$productid = $adb->query_result($result, $i - 1, 'productid');
		$entitytype = $adb->query_result($result, $i - 1, 'entitytype');
		$productname = $adb->query_result($result, $i - 1, 'productname');
		$productname = '<a href="index.php?action=DetailView&record='.$productid.'&module='.$entitytype.'">'.$productname.'</a>';
		$productname.= "<span type='vtlib_metainfo' vtrecordid='{$productid}' vtfieldname='".($entitytype=='Products' ? 'productname' : 'servicename')."' vtmodule='$entitytype' style='display:none;'></span>";
		if ($subprodname_str != '') {
			$productname .= "<br/><span style='color:#C0C0C0;font-style:italic;'>" . $subprodname_str . "</span>";
		}
		$comment = $adb->query_result($result, $i - 1, 'comment');
		$qtyinstock = $adb->query_result($result, $i - 1, 'qtyinstock');
		$qty = $adb->query_result($result, $i - 1, 'quantity');
		$qty = number_format($qty, 2, '.', ''); //Convert to 2 decimals
		//$unitprice = $adb->query_result($result, $i - 1, 'unit_price');
		$listprice = $adb->query_result($result, $i - 1, 'listprice');
		$total = $qty * $listprice;

		//Product wise Discount calculation - starts
		$discount_percent = $adb->query_result($result, $i - 1, 'discount_percent');
		$discount_amount = $adb->query_result($result, $i - 1, 'discount_amount');
		$totalAfterDiscount = $total;

		$productDiscount = '0.00';
		if ($discount_percent != 'NULL' && $discount_percent != '') {
			$productDiscount = $total * $discount_percent / 100;
			$totalAfterDiscount = $total - $productDiscount;
			//if discount is percent then show the percentage
			$discount_info_message = "$discount_percent % " . $app_strings['LBL_LIST_OF'] . ' '.
										CurrencyField::convertToUserFormat($total, null, true)." = ".
										CurrencyField::convertToUserFormat($productDiscount, null, true);
		} elseif ($discount_amount != 'NULL' && $discount_amount != '') {
			$productDiscount = $discount_amount;
			$totalAfterDiscount = $total - $productDiscount;
			$discount_info_message = $app_strings['LBL_DIRECT_AMOUNT_DISCOUNT'] . " = ". CurrencyField::convertToUserFormat($productDiscount, null, true);
		} else {
			$discount_info_message = $app_strings['LBL_NO_DISCOUNT_FOR_THIS_LINE_ITEM'];
		}
		//Product wise Discount calculation - ends

		$netprice = $totalAfterDiscount;
		//Calculate the individual tax if taxtype is individual
		if ($taxtype == 'individual') {
			$taxtotal = '0.00';
			$tax_info_message = $app_strings['LBL_TOTAL_AFTER_DISCOUNT'] . " = ".CurrencyField::convertToUserFormat($totalAfterDiscount, null, true)." \\n";
			foreach (getTaxDetailsForProduct($productid, 'all', $acvid) as $taxItem) {
				$tax_name = $taxItem['taxname'];
				$tax_label = $taxItem['taxlabel'];
				$tax_value = getInventoryProductTaxValue($focus->id, $productid, $tax_name);
				$individual_taxamount = $totalAfterDiscount * $tax_value / 100;
				$taxtotal = $taxtotal + $individual_taxamount;
				$tax_info_message .= "$tax_label : $tax_value % = ".CurrencyField::convertToUserFormat($individual_taxamount, null, true)." \\n";
			}
			$tax_info_message .= "\\n " . $app_strings['LBL_TOTAL_TAX_AMOUNT'] . " = ". CurrencyField::convertToUserFormat($taxtotal, null, true);
			$netprice = $netprice + $taxtotal;
		}

		$sc_image_tag = '';
		if ($module == 'Invoice') {
			switch ($entitytype) {
				case 'Services':
					if (vtlib_isModuleActive('ServiceContracts')) {
						$sc_image_tag = '<a href="index.php?module=ServiceContracts&action=EditView&service_id=' . $productid . '&sc_related_to='
							. $focus->column_fields['account_id'] . '&start_date=' . DateTimeField::convertToUserFormat($focus->column_fields['invoicedate'])
							. '&return_module=' . $module . '&return_id=' . $focus->id . '"><img border="0" src="' . vtiger_imageurl('handshake.gif', $theme)
							. '" title="' . getTranslatedString('LBL_ADD_NEW', $module)." ".getTranslatedString('ServiceContracts', 'ServiceContracts')
							. '" style="cursor: pointer;" align="absmiddle" /></a>';
					}
					break;
				case 'Products':
					if (vtlib_isModuleActive('Assets')) {
						$sc_image_tag = '<a onclick="return window.open(\'index.php?module=Assets&return_module=Invoice&action=Popup&popuptype=detailview&select=enable'
							. '&form=EditView&form_submit=false&return_action=DetailView&productid='.$productid.'&invoiceid='.$focus->id.'&return_id=' . $focus->id
							. '&recordid='.$focus->id.'\',\'test\',\'width=640,height=602,resizable=0,scrollbars=0\')"'
							. ' onmouseout="vtlib_listview.trigger(\'invoiceasset.onmouseout\', this)" onmouseover="vtlib_listview.trigger(\'cell.onmouseover\', this)">'
							. '<img border="0" src="' . vtiger_imageurl('barcode.png', $theme) . '" title="' . getTranslatedString('LBL_ADD_NEW', $module)
							. ' '.getTranslatedString('Assets', 'Assets'). '" style="cursor: pointer;" align="absmiddle" /><span style="display:none;" '
							. 'vtmodule="Assets" vtfieldname="invoice_product" vtrecordid="'.$focus->id.'::'.$productid.'::'.$i.'" type="vtlib_metainfo"></span></a>';
					}
					break;
				default:
					$sc_image_tag = '';
			}
		}

		//For Product Name
		$output .= '
			<tr valign="top" class="detailview_inventory_row">
				<td class="crmTableRow small lineOnTop detailview_inventory_namecell" '
					.'onmouseover="vtlib_listview.trigger(\'cell.onmouseover\', this);" onmouseout="vtlib_listview.trigger(\'cell.onmouseout\', this);">'
					. $productname . '&nbsp;' . $sc_image_tag . '<br>' . $comment
					. '</td>';
		//Upto this added to display the Product name and comment

		$output .= '<td class="crmTableRow small lineOnTop detailview_inventory_stockcell">';
		if ($module != 'PurchaseOrder' && $hide_stock == 'no') {
			$output .= '<b>'.$app_strings['LBL_QTY_IN_STOCK'].':</b>&nbsp;'.$qtyinstock;
		}
		if ($MDMapFound) {
			$invdTabid = getTabid('InventoryDetails');
			foreach ($cbMapFields['detailview']['fields'] as $mdfield) {
				$output .= '<br>';
				$output .= '<b>'.$mdfield['fieldinfo']['label'].'</b>:&nbsp;';
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
					$foutput = getDetailViewOutputHtml($mdfield['fieldinfo']['uitype'], $mdfield['fieldinfo']['name'], $mdfield['fieldinfo']['label'], $col_fields, 0, $invdTabid, $module);
					if ($foutput[2]==69) { // image
						$foutput = str_replace('style="max-width: 500px;"', 'style="max-width: 100px;"', $foutput[1]);
					} else {
						$foutput = $foutput[1];
					}
					$output .= $foutput;
				}
			}
		}
		$output .= '</td>';
		$output .= '<td class="crmTableRow small lineOnTop detailview_inventory_qtycell">' . $qty . '</td>';
		$output .= '
			<td class="crmTableRow small lineOnTop detailview_inventory_lpricecell" align="right">
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
				   <tr>
				    <td align="right">' . CurrencyField::convertToUserFormat($listprice, null, true) . '</td>
				   </tr>
				   <tr>
						<td align="right">
							(-)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $discount_info_message . '\'); ">' . $app_strings['LBL_DISCOUNT'] . ' : </a></b>
						</td>
				   </tr>
				   <tr>
				    <td align="right" nowrap>' . $app_strings['LBL_TOTAL_AFTER_DISCOUNT'] . ' : </td>
				   </tr>';
		if ($taxtype == 'individual') {
			$output .= '
				<tr>
					<td align="right" nowrap>
						(+)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $tax_info_message . '\');">' . $app_strings['LBL_TAX'] . ' : </a></b>
					</td>
				</tr>';
		}
		$output .= '
				</table>
			</td>';

		$output .= '
			<td class="crmTableRow small lineOnTop detailview_inventory_totalscell" align="right">
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
				   <tr><td align="right">' . CurrencyField::convertToUserFormat($total, null, true) . '</td></tr>
				   <tr><td align="right">' . CurrencyField::convertToUserFormat($productDiscount, null, true) . '</td></tr>
				   <tr><td align="right" nowrap>' . CurrencyField::convertToUserFormat($totalAfterDiscount, null, true) . '</td></tr>';

		if ($taxtype == 'individual') {
			$output .= '<tr><td align="right" nowrap>' . CurrencyField::convertToUserFormat($taxtotal, null, true) . '</td></tr>';
		}

		$output .= '
				</table>
			</td>';
		$output .= '<td class="crmTableRow small lineOnTop detailview_inventory_npricecell" valign="bottom" align="right">';
		$output .= CurrencyField::convertToUserFormat($netprice, null, true) . '</td>';
		$output .= '</tr>';

		$netTotal = $netTotal + $netprice;
	}

	$output .= '</table>';

	//$netTotal should be equal to $focus->column_fields['hdnSubTotal']
	$netTotal = $focus->column_fields['hdnSubTotal'];

	//Display the total, adjustment, S&H details
	$output .= '<table width="100%" border="0" cellspacing="0" cellpadding="5" class="crmTable detailview_inventory_totals">';
	$output .= '<tr id="detailview_inventory_subtotalrow">';
	$output .= '<td width="88%" class="crmTableRow small" align="right"><b>' . $app_strings['LBL_NET_TOTAL'] . '</td>';
	$output .= '<td width="12%" class="crmTableRow small" align="right"><b>' . CurrencyField::convertToUserFormat($netTotal, null, true) . '</b></td>';
	$output .= '</tr>';

	//Decide discount
	$finalDiscount = '0.00';
	$final_discount_info = '0';
	if ($focus->column_fields['hdnDiscountPercent'] != '0') {
		$finalDiscount = ($netTotal * $focus->column_fields['hdnDiscountPercent'] / 100);
		$final_discount_info = $focus->column_fields['hdnDiscountPercent'] . ' % ' . $app_strings['LBL_LIST_OF'] . ' '
			.CurrencyField::convertToUserFormat($netTotal, null, true) . ' = '. CurrencyField::convertToUserFormat($finalDiscount, null, true);
	} elseif ($focus->column_fields['hdnDiscountAmount'] != '0') {
		$finalDiscount = $focus->column_fields['hdnDiscountAmount'];
		$final_discount_info = CurrencyField::convertToUserFormat($finalDiscount, null, true);
	}

	//Alert the Final Discount amount even it is zero
	$final_discount_info = $app_strings['LBL_FINAL_DISCOUNT_AMOUNT'] . " = $final_discount_info";
	$final_discount_info = 'onclick="alert(\'' . $final_discount_info . '\');"';

	$output .= '<tr id="detailview_inventory_totaldiscrow">';
	$output .= '<td align="right" class="crmTableRow small lineOnTop">';
	$output .= '(-)&nbsp;<b><a href="javascript:;" ' . $final_discount_info . '>' . $app_strings['LBL_DISCOUNT'] . '</a></b></td>';
	$output .= '<td align="right" class="crmTableRow small lineOnTop">' . CurrencyField::convertToUserFormat($finalDiscount, null, true) . '</td>';
	$output .= '</tr>';

	if ($taxtype == 'group') {
		$taxtotal = '0.00';
		$final_totalAfterDiscount = $netTotal - $finalDiscount;
		$tax_info_message = $app_strings['LBL_TOTAL_AFTER_DISCOUNT'] . " = ". CurrencyField::convertToUserFormat($final_totalAfterDiscount, null, true)." \\n";
		//First we should get all available taxes and then retrieve the corresponding tax values
		$ipr_cols = $adb->getColumnNames('vtiger_inventoryproductrel');
		//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
		foreach (getAllTaxes('available', '', 'edit', $focus->id) as $taxItem) {
			$tax_name = $taxItem['taxname'];
			$tax_label = $taxItem['taxlabel'];
			if (in_array($tax_name, $ipr_cols)) {
				$tax_value = $adb->query_result($result, 0, $tax_name);
			} else {
				$tax_value = $taxItem['percentage'];
			}
			if ($tax_value == '' || $tax_value == 'NULL') {
				$tax_value = '0.00';
			}

			$taxamount = ($netTotal - $finalDiscount) * $tax_value / 100;
			$taxtotal = $taxtotal + $taxamount;
			$tax_info_message .= "$tax_label : $tax_value % = ". CurrencyField::convertToUserFormat($taxamount, null, true) ." \\n";
		}
		$tax_info_message .= "\\n " . $app_strings['LBL_TOTAL_TAX_AMOUNT'] . " = ". CurrencyField::convertToUserFormat($taxtotal, null, true);

		$output .= '<tr id="detailview_inventory_taxtotalrow">';
		$output .= '<td align="right" class="crmTableRow small">';
		$output .= '(+)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $tax_info_message . '\');">' . $app_strings['LBL_TAX'] . '</a></b></td>';
		$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($taxtotal, null, true) . '</td>';
		$output .= '</tr>';
	}

	$shAmount = ($focus->column_fields['hdnS_H_Amount'] != '') ? $focus->column_fields['hdnS_H_Amount'] : '0.00';
	if (GlobalVariable::getVariable('Inventory_Show_ShippingHandlingCharges', 1, $module)) {
		$output .= '<tr id="detailview_inventory_shippingrow">';
		$output .= '<td align="right" class="crmTableRow small">(+)&nbsp;<b>' . $app_strings['LBL_SHIPPING_AND_HANDLING_CHARGES'] . '</b></td>';
		$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($shAmount, null, true) . '</td>';
		$output .= '</tr>';
	}

	//calculate S&H tax
	$shtaxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
	$shtax_info_message = $app_strings['LBL_SHIPPING_AND_HANDLING_CHARGE'] . " = ". CurrencyField::convertToUserFormat($shAmount, null, true) ."\\n";
	$shtaxexist = false;
	foreach (getAllTaxes('available', 'sh', 'edit', $focus->id) as $taxItem) {
		$shtaxexist = true;
		$shtax_name = $taxItem['taxname'];
		$shtax_label = $taxItem['taxlabel'];
		$shtax_percent = getInventorySHTaxPercent($focus->id, $shtax_name);
		$shtaxamount = $shAmount * $shtax_percent / 100;
		$shtaxtotal = $shtaxtotal + $shtaxamount;
		$shtax_info_message .= "$shtax_label : $shtax_percent % = ". CurrencyField::convertToUserFormat($shtaxamount, null, true) ." \\n";
	}
	$shtax_info_message .= "\\n " . $app_strings['LBL_TOTAL_TAX_AMOUNT'] . " = ". CurrencyField::convertToUserFormat($shtaxtotal, null, true);

	if ($shtaxexist) {
		$output .= '<tr id="detailview_inventory_shiptaxrow">';
		$output .= '<td align="right" class="crmTableRow small">(+)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $shtax_info_message . '\')">';
		$output .= $app_strings['LBL_TAX_FOR_SHIPPING_AND_HANDLING'] . '</a></b></td>';
		$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($shtaxtotal, null, true) . '</td>';
		$output .= '</tr>';
	}

	$adjustment = ($focus->column_fields['txtAdjustment'] != '') ? $focus->column_fields['txtAdjustment'] : '0.00';
	$output .= '<tr id="detailview_inventory_adjustrow">';
	$output .= '<td align="right" class="crmTableRow small">&nbsp;<b>' . $app_strings['LBL_ADJUSTMENT'] . '</b></td>';
	$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($adjustment, null, true) . '</td>';
	$output .= '</tr>';

	$grandTotal = ($focus->column_fields['hdnGrandTotal'] != '') ? $focus->column_fields['hdnGrandTotal'] : '0.00';
	$output .= '<tr id="detailview_inventory_grandtotrow">';
	$output .= '<td align="right" class="crmTableRow small lineOnTop"><b>' . $app_strings['LBL_GRAND_TOTAL'] . '</b></td>';
	$output .= '<td align="right" class="crmTableRow small lineOnTop">' . CurrencyField::convertToUserFormat($grandTotal, null, true) . '</td>';
	$output .= '</tr>';
	$output .= '</table>';

	$log->debug('Exiting getDetailAssociatedProducts method ...');
	return $output;
}

/** This function returns the related vtiger_tab details for a given entity or a module.
 * Param $module - module name
 * Param $focus - module object
 * Return type is an array
 * @deprecated
 */
function getRelatedListsInformation($module, $focus) {
	global $log, $adb, $current_user;
	$log->debug('Entering getRelatedListsInformation(' . $module . ',' . get_class($focus) . ') method ...');
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';

	$cur_tab_id = getTabid($module);

	// vtlib customization: Do not picklist module which are set as in-active
	$result = $adb->pquery(
		'select * from vtiger_relatedlists where tabid=? and related_tabid not in (SELECT tabid FROM vtiger_tab WHERE presence = 1) order by sequence',
		array($cur_tab_id)
	);
	$num_row = $adb->num_rows($result);
	$focus_list = array();
	for ($i = 0; $i < $num_row; $i++) {
		$rel_tab_id = $adb->query_result($result, $i, 'related_tabid');
		$function_name = $adb->query_result($result, $i, 'name');
		$label = $adb->query_result($result, $i, 'label');
		$actions = $adb->query_result($result, $i, 'actions');
		//$relationId = $adb->query_result($result, $i, 'relation_id');
		if ($rel_tab_id != 0) {
			if ($is_admin || $profileTabsPermission[$rel_tab_id] == 0) {
				if ($is_admin || $profileActionPermission[$rel_tab_id][3] == 0) {
					// vtlib customization: Send more information (from module, related module) to the callee
					$focus_list[$label] = $focus->$function_name($focus->id, $cur_tab_id, $rel_tab_id, $actions);
				}
			}
		} else {
			// vtlib customization: Send more information (from module, related module) to the callee
			$focus_list[$label] = $focus->$function_name($focus->id, $cur_tab_id, $rel_tab_id, $actions);
		}
	}
	$log->debug('Exiting getRelatedListsInformation method ...');
	return $focus_list;
}

/** This function returns the related vtiger_tab details for a given entity or a module.
 * Param $module - module name
 * Param $focus - module object
 * Param $restrictedRelations - array of related list IDs that you want to access
 * Return type is an array
 */
function getRelatedLists($module, $focus, $restrictedRelations = null) {
	global $log, $adb, $current_user;
	$log->debug('Entering getRelatedLists(' . $module . ') method ...');
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';

	$cur_tab_id = getTabid($module);

	//To select several specific Lists
	$sel_list = '';
	if (is_array($restrictedRelations) && count($restrictedRelations)>0) {
		$comma_list = implode(',', $restrictedRelations);
		$sel_list = " AND relation_id IN ($comma_list) ";
	}

	// vtlib customization: Do not picklist module which are set as in-active
	$sql1 = "select * from vtiger_relatedlists where tabid=? and related_tabid not in (SELECT tabid FROM vtiger_tab WHERE presence = 1) $sel_list order by sequence";
	$result = $adb->pquery($sql1, array($cur_tab_id));
	$num_row = $adb->num_rows($result);
	$focus_list = array();
	for ($i = 0; $i < $num_row; $i++) {
		$rel_tab_id = $adb->query_result($result, $i, 'related_tabid');
		//$function_name = $adb->query_result($result, $i, 'name');
		$label = $adb->query_result($result, $i, 'label');
		$actions = $adb->query_result($result, $i, 'actions');
		$relationId = $adb->query_result($result, $i, 'relation_id');
		if ($rel_tab_id != 0) {
			if ($is_admin || $profileTabsPermission[$rel_tab_id] == 0) {
				if ($is_admin || $profileActionPermission[$rel_tab_id][3] == 0) {
					// vtlib customization: Send more information (from module, related module) to the callee
					$focus_list[$label] = array('related_tabid' => $rel_tab_id, 'relationId' => $relationId, 'actions' => $actions);
				}
			}
		} else {
			// vtlib customization: Send more information (from module, related module) to the callee
			$focus_list[$label] = array('related_tabid' => $rel_tab_id, 'relationId' => $relationId, 'actions' => $actions);
		}
	}
	$log->debug('Exiting getRelatedLists method ...');
	return $focus_list;
}

/** This function returns whether related lists block is present for this particular module or not
 * Param $module - module name
 * Return true if at least one block exists, false otherwise
 */
function isPresentRelatedListBlock($module) {
	global $adb;
	$brs = $adb->pquery('select 1 from vtiger_blocks where tabid=? and isrelatedlist>0', array(getTabid($module)));
	return ($brs && $adb->num_rows($brs)>0);
}

/** This function returns whether a related lists block is present for this particular module with another or not
 * Param $originModule - origin module name
 * Param $relatedModule - related module name
 * Return true if related list block exists between origin and related modules, false otherwise
 */
function isPresentRelatedListBlockWithModule($originModule, $relatedModule) {
	global $adb;
	$brs = $adb->pquery(
		'select 1
		from vtiger_blocks
		INNER JOIN vtiger_relatedlists ON vtiger_blocks.isrelatedlist=vtiger_relatedlists.relation_id
		where vtiger_blocks.tabid=? and vtiger_relatedlists.related_tabid=?',
		array(getTabid($originModule),getTabid($relatedModule))
	);
	return ($brs && $adb->num_rows($brs)>0);
}

/** This function returns whether related lists is present for this particular module or not
 * Param $module - module name
 * Param $activity_mode - mode of activity
 * Return type list of related modules or false
 */
function isPresentRelatedLists($module, $activity_mode = '') {
	static $moduleRelatedListCache = array();

	global $adb, $current_user;
	$retval = array();
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
		include 'tabdata.php';
	}
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';
	$tab_id = getTabid($module);
	// We need to check if there is at least 1 relation, no need to use count(*)
	$result = $adb->pquery('select relation_id,related_tabid,label from vtiger_relatedlists where tabid=? order by sequence', array($tab_id));
	$count = $adb->num_rows($result);
	if ($count < 1 || ($module == 'Calendar' && $activity_mode == 'task')) {
		$retval = 'false';
	} elseif (empty($moduleRelatedListCache[$module])) {
		for ($i = 0; $i < $count; ++$i) {
			$relatedId = $adb->query_result($result, $i, 'relation_id');
			$relationLabel = $adb->query_result($result, $i, 'label');
			$relatedTabId = $adb->query_result($result, $i, 'related_tabid');
			//check for module disable.
			if (empty($relatedTabId)) {
				$retval[$relatedId] = $relationLabel;
			} else {
				if (isset($tab_seq_array[$relatedTabId]) && $tab_seq_array[$relatedTabId] === 0) {
					if ($is_admin || $profileTabsPermission[$relatedTabId] === 0) {
						$retval[$relatedId] = $relationLabel;
					}
				}
			}
		}
		$moduleRelatedListCache[$module] = $retval;
	}
	return isset($moduleRelatedListCache[$module]) ? $moduleRelatedListCache[$module] : false;
}

/** This function returns the detailed block information of a record in a module.
 * Param $module - module name
 * Param $block - block id
 * Param $col_fields - column vtiger_fields array for the module
 * Param $tabid - vtiger_tab id
 * Return type is an array
 */
function getDetailBlockInformation($module, $result, $col_fields, $tabid, $block_label) {
	global $log, $adb;
	$log->debug("Entering getDetailBlockInformation($module, $result," . print_r($col_fields, true) . ", $tabid, " . print_r($block_label, true) . ') method ...');
	$label_data = array();

	$bmapname = $module.'_FieldInfo';
	$cbMapFI = array();
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$cbMapFI = $cbMap->FieldInfo();
		$cbMapFI = $cbMapFI['fields'];
	}
	$noofrows = $adb->num_rows($result);
	for ($i = 0; $i < $noofrows; $i++) {
		$fieldtablename = $adb->query_result($result, $i, 'tablename');
		//$fieldcolname = $adb->query_result($result, $i, 'columnname');
		$uitype = $adb->query_result($result, $i, 'uitype');
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		$fieldid = $adb->query_result($result, $i, 'fieldid');
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		//$maxlength = $adb->query_result($result, $i, 'maximumlength');
		$block = $adb->query_result($result, $i, 'block');
		$generatedtype = $adb->query_result($result, $i, 'generatedtype');
		$tabid = $adb->query_result($result, $i, 'tabid');
		$displaytype = $adb->query_result($result, $i, 'displaytype');
		$readonly = $adb->query_result($result, $i, 'readonly');
		if (isset($cbMapFI[$fieldname])) {
			$custfld = getDetailViewOutputHtml($uitype, $fieldname, $fieldlabel, $col_fields, $generatedtype, $tabid, $module, $cbMapFI[$fieldname]);
			if (isset($cbMapFI[$fieldname]['RTE']) && $cbMapFI[$fieldname]['RTE'] && vt_hasRTE()) {
				$readonly = '1'; // no inline edit for RTE edit fields
			}
		} else {
			$custfld = getDetailViewOutputHtml($uitype, $fieldname, $fieldlabel, $col_fields, $generatedtype, $tabid, $module);
		}
		if (is_array($custfld)) {
			$extendedfieldinfo = '';
			if (isset($custfld[2]) && $custfld[2]==10) {
				$fldmod_result = $adb->pquery(
					'SELECT relmodule, status
						FROM vtiger_fieldmodulerel
						INNER JOIN vtiger_tab ON vtiger_fieldmodulerel.relmodule=vtiger_tab.name and vtiger_tab.presence=0
						WHERE fieldid=
						(SELECT fieldid
							FROM vtiger_field, vtiger_tab
							WHERE vtiger_field.tabid=vtiger_tab.tabid AND fieldname=? AND name=? and vtiger_field.presence in (0,2) and vtiger_tab.presence=0)
						order by sequence',
					array($fieldname, $module)
				);
				$entityTypes = array();
				$parent_id = $col_fields[$fieldname];
				for ($index = 0; $index < $adb->num_rows($fldmod_result); ++$index) {
					$entityTypes[] = $adb->query_result($fldmod_result, $index, 'relmodule');
				}
				if (count($entityTypes)==0) {
					continue;
				}
				if (!empty($parent_id)) {
					if ($adb->num_rows($fldmod_result)==1) {
						$valueType = $adb->query_result($fldmod_result, 0, 0);
					} else {
						$valueType = getSalesEntityType($parent_id);
					}
					$displayValueArray = getEntityName($valueType, $parent_id);
					$displayValue='';
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $val) {
							$displayValue = $val;
						}
					}
				} else {
					$displayValue='';
					$valueType='';
					$parent_id='';
				}
				$extendedfieldinfo = array('options'=>$entityTypes, 'selected'=>$valueType, 'displayvalue'=>$displayValue, 'entityid'=>$parent_id);
			}
			if (isset($cbMapFI[$fieldname])) {
				if (is_array($extendedfieldinfo)) {
					$extendedfieldinfo = array_merge($cbMapFI[$fieldname], $extendedfieldinfo);
				} else {
					$extendedfieldinfo = $cbMapFI[$fieldname];
				}
			}
			$label_data[$block][] = array($custfld[0] => array(
				'value' => $custfld[1], "ui" => $custfld[2], 'options' => isset($custfld['options']) ? $custfld['options'] : '',
				'secid' => isset($custfld['secid']) ? $custfld['secid'] : '', 'link' => isset($custfld['link']) ? $custfld['link'] : '',
				'cursymb' => isset($custfld['cursymb']) ? $custfld['cursymb'] : '',
				'salut' => isset($custfld['salut']) ? $custfld['salut'] : '', 'notaccess' => isset($custfld['notaccess']) ? $custfld['notaccess'] : '',
				'cntimage' => isset($custfld['cntimage']) ? $custfld['cntimage'] : '', "isadmin" => $custfld["isadmin"],
				'tablename' => $fieldtablename, "fldname" => $fieldname, "fldid" => $fieldid,
				'displaytype' => $displaytype, "readonly" => $readonly, 'extendedfieldinfo'=>$extendedfieldinfo));
		}
	}
	foreach ($label_data as $headerid => $value_array) {
		$detailview_data = array();
		for ($i = 0, $j = 0, $iMax = count($value_array); $i < $iMax; $j++) {
			$key2 = null;
			$keys = array_keys($value_array[$i]);
			$key1 = $keys[0];
			if (isset($value_array[$i + 1]) && is_array($value_array[$i + 1]) && ($value_array[$i][$key1]['ui'] != 19 && $value_array[$i][$key1]['ui'] != 20)) {
				$keys = array_keys($value_array[$i + 1]);
				$key2 = $keys[0];
			}
			// Added to avoid the unique keys
			$use_key1 = $key1;
			if ($key1 == $key2) {
				$use_key1 = " " . $key1;
			}

			if ($value_array[$i][$key1]['ui'] != 19 && $value_array[$i][$key1]['ui'] != 20 && !empty($key2)) {
				$detailview_data[$j] = array($use_key1 => $value_array[$i][$key1], $key2 => $value_array[$i + 1][$key2]);
				$i+=2;
			} else {
				$detailview_data[$j] = array($use_key1 => $value_array[$i][$key1]);
				$i++;
			}
		}
		$label_data[$headerid] = $detailview_data;
	}
	$returndata = array();
	foreach ($block_label as $blockid => $label) {
		if ($label == '') {
			$i18nidx = getTranslatedString($curBlock, $module);
			if (!isset($returndata[$i18nidx])) {
				$returndata[$i18nidx] = array();
			}
			if (!isset($label_data[$blockid])) {
				$label_data[$blockid] = array();
			}
			$returndata[$i18nidx]=array_merge((array)$returndata[$i18nidx], (array)$label_data[$blockid]);
		} else {
			$curBlock = $label;
			if (isset($label_data[$blockid]) && is_array($label_data[$blockid])) {
				$i18nidx = getTranslatedString($curBlock, $module);
				if (!isset($returndata[$i18nidx])) {
					$returndata[$i18nidx] = array();
				}
				$returndata[$i18nidx]=array_merge((array)$returndata[$i18nidx], (array)$label_data[$blockid]);
			} elseif (file_exists("Smarty/templates/modules/$module/{$label}_detail.tpl")) {
				$i18nidx = getTranslatedString($curBlock, $module);
				if (!isset($returndata[$i18nidx])) {
					$returndata[$i18nidx] = array();
				}
				$returndata[$i18nidx]=array_merge((array)$returndata[$i18nidx], array($label=>array()));
			} else {
				$brs = $adb->pquery('select isrelatedlist from vtiger_blocks where blockid=?', array($blockid));
				if ($brs && $adb->num_rows($brs)>0) {
					$rellist = $adb->query_result($brs, 0, 'isrelatedlist');
					if ($rellist>0) {
						if (!isset($returndata[$curBlock])) {
							$returndata[$curBlock] = array();
						}
						$returndata[$curBlock]=array_merge((array)$returndata[$curBlock], array($label=>array(),'relatedlist'=>$rellist));
					}
				}
			}
		}
	}
	$log->debug("Exiting getDetailBlockInformation method ...");
	return $returndata;
}

function VT_detailViewNavigation($smarty, $recordNavigationInfo, $currrentRecordId) {
	$pageNumber = 0;
	$smarty->assign('privrecord', '');
	$smarty->assign('nextrecord', 0);
	foreach ($recordNavigationInfo as $start => $recordIdList) {
		$pageNumber++;
		foreach ($recordIdList as $index => $recordId) {
			if ($recordId === $currrentRecordId) {
				if ($index == 0) {
					$smarty->assign('privrecordstart', $start - 1);
					if (isset($recordNavigationInfo[$start - 1])) {
						$smarty->assign('privrecord', $recordNavigationInfo[$start - 1][count($recordNavigationInfo[$start - 1]) - 1]);
					} else {
						$smarty->assign('privrecord', '');
					}
				} else {
					$smarty->assign('privrecordstart', $start);
					$smarty->assign('privrecord', $recordIdList[$index - 1]);
				}
				if ($index == count($recordIdList) - 1) {
					$smarty->assign('nextrecordstart', $start + 1);
					$smarty->assign('nextrecord', isset($recordNavigationInfo[$start + 1]) ? $recordNavigationInfo[$start + 1][0] : 0);
				} else {
					$smarty->assign('nextrecordstart', $start);
					$smarty->assign('nextrecord', $recordIdList[$index + 1]);
				}
			}
		}
	}
}

function getRelatedListInfoById($relationId) {
	static $relatedInfoCache = array();
	if (isset($relatedInfoCache[$relationId])) {
		return $relatedInfoCache[$relationId];
	}
	$adb = PearDatabase::getInstance();
	$result = $adb->pquery('select * from vtiger_relatedlists where relation_id=?', array($relationId));
	$rowCount = $adb->num_rows($result);
	$relationInfo = array();
	if ($rowCount > 0) {
		$relationInfo['relatedTabId'] = $adb->query_result($result, 0, 'related_tabid');
		$relationInfo['functionName'] = $adb->query_result($result, 0, 'name');
		$relationInfo['label'] = $adb->query_result($result, 0, 'label');
		$relationInfo['actions'] = $adb->query_result($result, 0, 'actions');
		$relationInfo['relationId'] = $adb->query_result($result, 0, 'relation_id');
	}
	$relatedInfoCache[$relationId] = $relationInfo;
	return $relatedInfoCache[$relationId];
}
?>
