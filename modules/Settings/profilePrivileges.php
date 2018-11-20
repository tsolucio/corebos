<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/utils.php';

global $app_strings, $mod_strings, $current_user, $currentModule, $adb, $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$profileId = (isset($_REQUEST['profileid']) ? vtlib_purify($_REQUEST['profileid']) : 0);
$profileName='';
$profileDescription='';

if (!empty($profileId)) {
	if (!profileExists($profileId) || !is_numeric($profileId)) {
		die(getTranslatedString('ERR_INVALID_PROFILE_ID', $currentModule));
	}
} elseif ($_REQUEST['mode'] != 'create' || vtlib_purify($_REQUEST['profile_name']) == '') {
	die(getTranslatedString('ERR_INVALID_PROFILE_ID', $currentModule));
}

$parentProfileId = isset($_REQUEST['parentprofile']) ? vtlib_purify($_REQUEST['parentprofile']) : '';
if ($_REQUEST['mode'] =='create' && $_REQUEST['radiobutton'] != 'baseprofile') {
	$parentProfileId = '';
}

$smarty = new vtigerCRM_Smarty;
if (isset($_REQUEST['selected_tab']) && vtlib_purify($_REQUEST['selected_tab']) != '') {
	$smarty->assign('SELECTED_TAB', vtlib_purify($_REQUEST['selected_tab']));
} else {
	$smarty->assign('SELECTED_TAB', 'global_privileges');
}

if (isset($_REQUEST['selected_module']) && vtlib_purify($_REQUEST['selected_module']) != '') {
	$smarty->assign('SELECTED_MODULE', vtlib_purify($_REQUEST['selected_module']));
} else {
	$smarty->assign('SELECTED_MODULE', 'field_Leads');
}

$smarty->assign('PARENTPROFILEID', $parentProfileId);
$smarty->assign('RADIOBUTTON', (isset($_REQUEST['radiobutton']) ? vtlib_purify($_REQUEST['radiobutton']) : ''));

$secondaryModule='';
$mode='';
$output ='';
$output1 ='';
$smarty->assign('PROFILEID', $profileId);
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('RETURN_ACTION', (isset($_REQUEST['return_action']) ? vtlib_purify($_REQUEST['return_action']) : ''));

if (isset($_REQUEST['profile_name']) && vtlib_purify($_REQUEST['profile_name']) != '' && $_REQUEST['mode'] == 'create') {
	$profileName= vtlib_purify($_REQUEST['profile_name']);
	$smarty->assign('PROFILE_NAME', to_html($profileName));
} else {
	$profileName=getProfileName($profileId);
	$smarty->assign('PROFILE_NAME', $profileName);
}

if (isset($_REQUEST['profile_description']) && vtlib_purify($_REQUEST['profile_description']) != '' && $_REQUEST['mode'] == 'create') {
	$profileDescription = vtlib_purify($_REQUEST['profile_description']);
} else {
	if ($profileId != null) {
		$profileDescription = getProfileDescription($profileId);
	}
}

$smarty->assign('PROFILE_DESCRIPTION', $profileDescription);

if (isset($_REQUEST['mode']) && vtlib_purify($_REQUEST['mode']) != '') {
	$mode = $_REQUEST['mode'];
	$smarty->assign('MODE', $mode);
}

//Initially setting the secondary selected tab
if ($mode == 'create') {
	$smarty->assign('ACTION', 'SaveProfile');
} elseif ($mode == 'edit') {
	$smarty->assign('ACTION', 'UpdateProfileChanges');
}

//Global Privileges
if ($mode == 'view') {
	$global_per_arry = getProfileGlobalPermission($profileId);
	$view_all_per = $global_per_arry[1];
	$edit_all_per = $global_per_arry[2];
	$privileges_global[]=getGlobalDisplayValue($view_all_per, 1);
	$privileges_global[]=getGlobalDisplayValue($edit_all_per, 2);
} elseif ($mode == 'edit') {
	$global_per_arry = getProfileGlobalPermission($profileId);
	$view_all_per = $global_per_arry[1];
	$edit_all_per = $global_per_arry[2];
	$privileges_global[]=getGlobalDisplayOutput($view_all_per, 1);
	$privileges_global[]=getGlobalDisplayOutput($edit_all_per, 2);
} elseif ($mode == 'create') {
	if ($parentProfileId != '') {
		$global_per_arry = getProfileGlobalPermission($parentProfileId);
		$view_all_per = $global_per_arry[1];
		$edit_all_per = $global_per_arry[2];
		$privileges_global[]=getGlobalDisplayOutput($view_all_per, 1);
		$privileges_global[]=getGlobalDisplayOutput($edit_all_per, 2);
	} else {
		$privileges_global[]=getGlobalDisplayOutput(0, 1);
		$privileges_global[]=getGlobalDisplayOutput(0, 2);
	}
}

$smarty->assign('GLOBAL_PRIV', $privileges_global);

//standard privileges
if ($mode == 'view') {
	$act_perr_arry = getTabsActionPermission($profileId);
	foreach ($act_perr_arry as $tabid => $action_array) {
		$stand = array();
		$entity_name = getTabModuleName($tabid);
		//Create Permission
		$tab_create_per_id = $action_array['7'];
		$tab_create_per = getDisplayValue($tab_create_per_id, $tabid, '7');
		//Edit Permission
		$tab_edit_per_id = $action_array['1'];
		$tab_edit_per_id = getDisplayValue($tab_edit_per_id, $tabid, '1');
		//Delete Permission
		$tab_delete_per_id = $action_array['2'];
		$tab_delete_per = getDisplayValue($tab_delete_per_id, $tabid, '2');
		//View Permission
		$tab_view_per_id = $action_array['4'];
		$tab_view_per = getDisplayValue($tab_view_per_id, $tabid, '4');

		$stand[]=$entity_name;
		$stand[]=$tab_edit_per_id;
		$stand[]=$tab_delete_per;
		$stand[]=$tab_view_per;
		$stand[]=$tab_create_per;
		$privileges_stand[$tabid]=$stand;
	}
}
if ($mode == 'edit') {
	$act_perr_arry = getTabsActionPermission($profileId);
	foreach ($act_perr_arry as $tabid => $action_array) {
		$stand = array();
		$entity_name = getTabModuleName($tabid);
		//Create Permission
		$tab_create_per_id = $action_array['7'];
		$tab_create_per = getDisplayOutput($tab_create_per_id, $tabid, '7');
		//Edit Permission
		$tab_edit_per_id = $action_array['1'];
		$tab_edit_per_id = getDisplayOutput($tab_edit_per_id, $tabid, '1');
		//Delete Permission
		$tab_delete_per_id = $action_array['2'];
		$tab_delete_per = getDisplayOutput($tab_delete_per_id, $tabid, '2');
		//View Permission
		$tab_view_per_id = $action_array['4'];
		$tab_view_per = getDisplayOutput($tab_view_per_id, $tabid, '4');

		$stand[]=$entity_name;
		$stand[]=$tab_edit_per_id;
		$stand[]=$tab_delete_per;
		$stand[]=$tab_view_per;
		$stand[]=$tab_create_per;
		$privileges_stand[$tabid]=$stand;
	}
}
if ($mode == 'create') {
	if ($parentProfileId != '') {
		$act_perr_arry = getTabsActionPermission($parentProfileId);
		foreach ($act_perr_arry as $tabid => $action_array) {
			$stand = array();
			$entity_name = getTabModuleName($tabid);
			//Create Permission
			$tab_create_per_id = $action_array['7'];
			$tab_create_per = getDisplayOutput($tab_create_per_id, $tabid, '7');
			//Edit Permission
			$tab_edit_per_id = $action_array['1'];
			$tab_edit_per_id = getDisplayOutput($tab_edit_per_id, $tabid, '1');
			//Delete Permission
			$tab_delete_per_id = $action_array['2'];
			$tab_delete_per = getDisplayOutput($tab_delete_per_id, $tabid, '2');
			//View Permission
			$tab_view_per_id = $action_array['4'];
			$tab_view_per = getDisplayOutput($tab_view_per_id, $tabid, '4');

			$stand[]=$entity_name;
			$stand[]=$tab_edit_per_id;
			$stand[]=$tab_delete_per;
			$stand[]=$tab_view_per;
			$stand[]=$tab_create_per;
			$privileges_stand[$tabid]=$stand;
		}
	} else {
		$act_perr_arry = getTabsActionPermission(1);
		foreach ($act_perr_arry as $tabid => $action_array) {
			$stand = array();
			$entity_name = getTabModuleName($tabid);
			//Create Permission
			$tab_create_per_id = $action_array['7'];
			$tab_create_per = getDisplayOutput(0, $tabid, '7');
			//Edit Permission
			$tab_edit_per_id = $action_array['1'];
			$tab_edit_per_id = getDisplayOutput(0, $tabid, '1');
			//Delete Permission
			$tab_delete_per_id = $action_array['2'];
			$tab_delete_per = getDisplayOutput(0, $tabid, '2');
			//View Permission
			$tab_view_per_id = $action_array['4'];
			$tab_view_per = getDisplayOutput(0, $tabid, '4');

			$stand[]=$entity_name;
			$stand[]=$tab_edit_per_id;
			$stand[]=$tab_delete_per;
			$stand[]=$tab_view_per;
			$stand[]=$tab_create_per;
			$privileges_stand[$tabid]=$stand;
		}
	}
}
$smarty->assign('STANDARD_PRIV', $privileges_stand);

//tab Privileges
if ($mode == 'view') {
	$tab_perr_array = getTabsPermission($profileId);
	$tab_perr_array = orderByModule($tab_perr_array);
	$no_of_tabs = count($tab_perr_array);
	foreach ($tab_perr_array as $tabid => $tab_perr) {
		$tab=array();
		$entity_name = getTabModuleName($tabid);
		$tab_allow_per_id = $tab_perr_array[$tabid];
		$tab_allow_per = getDisplayValue($tab_allow_per_id, $tabid, '');
		$tab[]=$entity_name;
		$tab[]=$tab_allow_per;
		$privileges_tab[$tabid]=$tab;
	}
}
if ($mode == 'edit') {
	$tab_perr_array = getTabsPermission($profileId);
	$tab_perr_array = orderByModule($tab_perr_array);
	$no_of_tabs = count($tab_perr_array);
	foreach ($tab_perr_array as $tabid => $tab_perr) {
		$tab=array();
		$entity_name = getTabModuleName($tabid);
		$tab_allow_per_id = $tab_perr_array[$tabid];
		$tab_allow_per = getDisplayOutput($tab_allow_per_id, $tabid, '');
		$tab[]=$entity_name;
		$tab[]=$tab_allow_per;
		$privileges_tab[$tabid]=$tab;
	}
}
if ($mode == 'create') {
	if ($parentProfileId != '') {
		$tab_perr_array = getTabsPermission($parentProfileId);
		$tab_perr_array = orderByModule($tab_perr_array);
		$no_of_tabs = count($tab_perr_array);
		foreach ($tab_perr_array as $tabid => $tab_perr) {
			$tab=array();
			$entity_name = getTabModuleName($tabid);
			$tab_allow_per_id = $tab_perr_array[$tabid];
			$tab_allow_per = getDisplayOutput($tab_allow_per_id, $tabid, '');
			$tab[]=$entity_name;
			$tab[]=$tab_allow_per;
			$privileges_tab[$tabid]=$tab;
		}
	} else {
		$tab_perr_array = getTabsPermission(1);
		$tab_perr_array = orderByModule($tab_perr_array);
		$no_of_tabs = count($tab_perr_array);
		foreach ($tab_perr_array as $tabid => $tab_perr) {
			$tab=array();
			$entity_name = getTabModuleName($tabid);
			$tab_allow_per_id = $tab_perr_array[$tabid];
			$tab_allow_per = getDisplayOutput(0, $tabid, '');
			$tab[]=$entity_name;
			$tab[]=$tab_allow_per;
			$privileges_tab[$tabid]=$tab;
		}
	}
}
$smarty->assign('TAB_PRIV', $privileges_tab);

//utilities privileges
if ($mode == 'view') {
	$act_utility_arry = getTabsUtilityActionPermission($profileId);
	foreach ($act_utility_arry as $tabid => $action_array) {
		$util=array();
		$entity_name = getTabModuleName($tabid);
		$no_of_actions = count($action_array);
		foreach ($action_array as $action_id => $act_per) {
			$action_name = getActionname($action_id);
			$tab_util_act_per = $action_array[$action_id];
			$tab_util_per = getDisplayValue($tab_util_act_per, $tabid, $action_id);
			$util[]=$action_name;
			$util[]=$tab_util_per;
		}
		$util=array_chunk($util, 2);
		$util=array_chunk($util, 3);
		$privilege_util[$tabid] = $util;
	}
} elseif ($mode == 'edit') {
	$act_utility_arry = getTabsUtilityActionPermission($profileId);
	foreach ($act_utility_arry as $tabid => $action_array) {
		$util=array();
		$entity_name = getTabModuleName($tabid);
		$no_of_actions = count($action_array);
		foreach ($action_array as $action_id => $act_per) {
			$action_name = getActionname($action_id);
			$tab_util_act_per = $action_array[$action_id];
			$tab_util_per = getDisplayOutput($tab_util_act_per, $tabid, $action_id);
			$util[]=$action_name;
			$util[]=$tab_util_per;
		}
		$util=array_chunk($util, 2);
		$util=array_chunk($util, 3);
		$privilege_util[$tabid] = $util;
	}
} elseif ($mode == 'create') {
	if ($parentProfileId != '') {
		$act_utility_arry = getTabsUtilityActionPermission($parentProfileId);
		foreach ($act_utility_arry as $tabid => $action_array) {
			$util=array();
			$entity_name = getTabModuleName($tabid);
			$no_of_actions = count($action_array);
			foreach ($action_array as $action_id => $act_per) {
				$action_name = getActionname($action_id);
				$tab_util_act_per = $action_array[$action_id];
				$tab_util_per = getDisplayOutput($tab_util_act_per, $tabid, $action_id);
				$util[]=$action_name;
				$util[]=$tab_util_per;
			}
			$util=array_chunk($util, 2);
			$util=array_chunk($util, 3);
			$privilege_util[$tabid] = $util;
		}
	} else {
		$act_utility_arry = getTabsUtilityActionPermission(1);
		foreach ($act_utility_arry as $tabid => $action_array) {
			$util=array();
			$entity_name = getTabModuleName($tabid);
			$no_of_actions = count($action_array);
			foreach ($action_array as $action_id => $act_per) {
				$action_name = getActionname($action_id);
				$tab_util_act_per = $action_array[$action_id];
				$tab_util_per = getDisplayOutput(0, $tabid, $action_id);
				$util[]=$action_name;
				$util[]=$tab_util_per;
			}
			$util=array_chunk($util, 2);
			$util=array_chunk($util, 3);
			$privilege_util[$tabid] = $util;
		}
	}
}
$smarty->assign('UTILITIES_PRIV', $privilege_util);

//Field privileges
$modArr=getModuleAccessArray();

$no_of_mod = count($modArr);
for ($i=0; $i<$no_of_mod; $i++) {
	$fldModule=key($modArr);
	$lang_str=$modArr[$fldModule];
	$privilege_fld[]=$fldModule;
	next($modArr);
}
$smarty->assign('PRI_FIELD_LIST', $privilege_fld);

$disable_field_array = array();
$result = $adb->pquery('select * from vtiger_def_org_field', array());
$noofrows=$adb->num_rows($result);
for ($i=0; $i<$noofrows; $i++) {
	$FieldId = $adb->query_result($result, $i, 'fieldid');
	$Visible = $adb->query_result($result, $i, 'visible');
	$disable_field_array[$FieldId] = $Visible;
}

if ($mode=='view') {
	$fieldListResult = getProfile2AllFieldList($modArr, $profileId);
	for ($i=0; $i<count($fieldListResult); $i++) {
		$field_module=array();
		$module_name=key($fieldListResult);
		$module_id = getTabid($module_name);
		for ($j=0; $j<count($fieldListResult[$module_name]); $j++) {
			$field=array();
			if ($fieldListResult[$module_name][$j][1] == 0) {
				if ($fieldListResult[$module_name][$j][3] == 1) {
					$visible = "<img src='".vtiger_imageurl('locked.png', $theme)."'>";
				} else {
					$visible = "<img src='".vtiger_imageurl('unlocked.png', $theme)."'>";
				}
				//$visible = "<img src='".vtiger_imageurl('prvPrfSelectedTick.gif', $theme)."'>";
			} else {
				$visible = "<img src='".vtiger_imageurl('no.gif', $theme)."'>";
			}
			if ($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1) {
				$visible = "<img src='".vtiger_imageurl('no.gif', $theme)."'>";
			}

			$field_position = $fieldListResult[$module_name][$j][7];
			if ($field_position == 'H') {
				$summary = "<img src='".vtiger_imageurl('field_position_H.png', $theme)."'>";
			} elseif ($field_position == 'T') {
				$summary = "<img src='".vtiger_imageurl('field_position_T.png', $theme)."'>";
			} elseif ($field_position == 'B') {
				$summary = "<img src='".vtiger_imageurl('field_position_B.png', $theme)."'>";
			} else {
				$summary = "<img src='".vtiger_imageurl('no.gif', $theme)."'>";
			}

			$field[] = getTranslatedString($fieldListResult[$module_name][$j][0], $module_name);
			$field[]=$visible;
			$field[]=$summary;
			$field_module[]=$field;
		}
		$privilege_field[$module_id] = array_chunk($field_module, 3);
		next($fieldListResult);
	}
} elseif ($mode=='edit') {
	$fieldListResult = getProfile2AllFieldList($modArr, $profileId);
	for ($i=0; $i<count($fieldListResult); $i++) {
		$field_module=array();
		$module_name=key($fieldListResult);
		$module_id = getTabid($module_name);
		for ($j=0; $j<count($fieldListResult[$module_name]); $j++) {
			$fldLabel= $fieldListResult[$module_name][$j][0];
			$uitype = $fieldListResult[$module_name][$j][2];
			$displaytype = $fieldListResult[$module_name][$j][5];
			$typeofdata = $fieldListResult[$module_name][$j][6];
			$fieldtype = explode("~", $typeofdata);
			$mandatory = '';
			$readonly = '';
			$field=array();
			$fieldAccessMandatory = false;
			$fieldAccessRestricted = false;
			if ($fieldListResult[$module_name][$j][1] == 0) {
				$visible = 'checked';
			} else {
				$visible = '';
			}
			if (isset($fieldtype[1]) && $fieldtype[1] == 'M') {
				$mandatory = '<font color="red">*</font>';
				$readonly = 'disabled';
				$visible = 'checked';
				$fieldAccessMandatory = true;
			}
			if ($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1) {
				$mandatory = '<font color="blue">*</font>';
				$readonly = 'disabled';
				$visible = '';
				$fieldAccessRestricted = true;
			}

			$field[] = $mandatory.' '.getTranslatedString($fldLabel, $module_name);

			$field[]='<input id="'.$module_id.'_field_'.$fieldListResult[$module_name][$j][4].'" onClick="selectUnselect(this);" type="checkbox" name="'
				.$fieldListResult[$module_name][$j][4].'" '.$visible.' '.$readonly.'>';

			// Check for Read-Only or Read-Write Access for the field.
			$fieldReadOnlyAccess = $fieldListResult[$module_name][$j][3];
			if ($fieldReadOnlyAccess == 1) {
				$display_locked = "inline";
				$display_unlocked = "none";
			} else {
				$display_locked = "none";
				$display_unlocked = "inline";
			}
			if (!$fieldAccessMandatory && !$fieldAccessRestricted) {
				$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
					.'_readonly" value="'.$fieldReadOnlyAccess.'" /><a href="javascript:void(0);" onclick="toogleAccess(\''.$module_id.'_readonly_'
					.$fieldListResult[$module_name][$j][4].'\');"><img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_unlocked" src="'
					.vtiger_imageurl('unlocked.png', $theme).'" style="display:'.$display_unlocked.'" border="0"><img id="'.$module_id.'_readonly_'
					.$fieldListResult[$module_name][$j][4].'_locked" src="'.vtiger_imageurl('locked.png', $theme).'" style="display:'.$display_locked.'" border="0"></a>';
			} elseif ($fieldAccessMandatory) {
				$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
					.'_readonly" value="0" /><img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
			} else {
				$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
					.'_readonly" value="'.$fieldReadOnlyAccess.'" /><img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
			}

			$position = $fieldListResult[$module_name][$j][7];

			$display_title_position = 'none';
			$display_header_position = 'none';
			$display_body_position = 'none';
			$display_no_show_position = 'none';

			if ($position == 'T') {
				$display_title_position = 'inline';
			} elseif ($position == 'H') {
				$display_header_position = 'inline';
			} elseif ($position == 'B') {
				$display_body_position = 'inline';
			} else {
				$display_no_show_position = 'inline';
			}

			$currentFieldId = $fieldListResult[$module_name][$j][4];

			$field[] = '<input type="hidden" id="'.$module_id.'_position_'.$currentFieldId.'" name="'.$currentFieldId.'_position" value="'.$position.'" />
				<a href="javascript:void(0);" onclick="tooglePosition(\''.$module_id.'_position_'.$currentFieldId.'\')">
					<img id="'.$module_id.'_position_'.$currentFieldId.'_position_title"
						class="'.$module_id.'_position_'.$currentFieldId.'position_image"
						src="'.vtiger_imageurl('field_position_T.png', $theme).'"
						style="display: '.$display_title_position.'"
						border="0"
					/>
					<img id="'.$module_id.'_position_'.$currentFieldId.'_position_header"
						class="'.$module_id.'_position_'.$currentFieldId.'position_image"
						src="'.vtiger_imageurl('field_position_H.png', $theme).'"
						style="display: '.$display_header_position.'"
						border="0"
					/>
					<img id="'.$module_id.'_position_'.$currentFieldId.'_position_body"
						class="'.$module_id.'_position_'.$currentFieldId.'position_image"
						src="'.vtiger_imageurl('field_position_B.png', $theme).'"
						style="display: '.$display_body_position.'"
						border="0"
					/>
					<img id="'.$module_id.'_position_'.$currentFieldId.'_position_no_show"
						class="'.$module_id.'_position_'.$currentFieldId.'position_image"
						src="'.vtiger_imageurl('no.gif', $theme).'"
						style="display: '.$display_no_show_position.'"
						border="0"
					/>
				</a>';
			$field_module[]=$field;
		}
		$privilege_field[$module_id] = array_chunk($field_module, 3);
		next($fieldListResult);
	}
} elseif ($mode=='create') {
	if ($parentProfileId != '') {
		$fieldListResult = getProfile2AllFieldList($modArr, $parentProfileId);
		for ($i=0; $i<count($fieldListResult); $i++) {
			$field_module=array();
			$module_name=key($fieldListResult);
			$module_id = getTabid($module_name);
			for ($j=0; $j<count($fieldListResult[$module_name]); $j++) {
				$fldLabel= $fieldListResult[$module_name][$j][0];
				$uitype = $fieldListResult[$module_name][$j][2];
				$displaytype = $fieldListResult[$module_name][$j][5];
				$typeofdata = $fieldListResult[$module_name][$j][6];
				$fieldtype = explode("~", $typeofdata);
				$mandatory = '';
				$readonly = '';
				$field=array();

				$fieldAccessMandatory = false;
				$fieldAccessRestricted = false;
				if (isset($fieldtype[1]) && $fieldtype[1] == 'M') {
					$mandatory = '<font color="red">*</font>';
					$readonly = 'disabled';
					$fieldAccessMandatory = true;
				}
				if ($fieldListResult[$module_name][$j][1] == 0) {
					$visible = 'checked';
				} else {
					$visible = "";
				}
				if ($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1) {
					$mandatory = '<font color="blue">*</font>';
					$readonly = 'disabled';
					$visible = "";
					$fieldAccessRestricted = true;
				}
				$field[] = $mandatory.' '.getTranslatedString($fldLabel, $module_name);

				$field[]='<input type="checkbox" id="'.$module_id.'_field_'.$fieldListResult[$module_name][$j][4].'" onClick="selectUnselect(this);" name="'
					.$fieldListResult[$module_name][$j][4].'" '.$visible.' '.$readonly.'>';

				// Check for Read-Only or Read-Write Access for the field.
				$fieldReadOnlyAccess = $fieldListResult[$module_name][$j][3];
				if ($fieldReadOnlyAccess == 1) {
					$display_locked = "inline";
					$display_unlocked = "none";
				} else {
					$display_locked = "none";
					$display_unlocked = "inline";
				}
				if (!$fieldAccessMandatory && !$fieldAccessRestricted) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
						.'_readonly" value="'.$fieldReadOnlyAccess.'" /><a href="javascript:void(0);" onclick="toogleAccess(\''.$module_id.'_readonly_'
						.$fieldListResult[$module_name][$j][4].'\');"><img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_unlocked" src="'
						.vtiger_imageurl('unlocked.png', $theme).'" style="display:'.$display_unlocked.'" border="0"><img id="'.$module_id.'_readonly_'
						.$fieldListResult[$module_name][$j][4].'_locked" src="'.vtiger_imageurl('locked.png', $theme).'" style="display:'.$display_locked
						.'" border="0"></a>';
				} elseif ($fieldAccessMandatory) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
						.'_readonly" value="0" /><img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				} else {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
						.'_readonly" value="'.$fieldReadOnlyAccess.'" /><img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				}

				$position = $fieldListResult[$module_name][$j][7];

				$display_title_position = 'none';
				$display_header_position = 'none';
				$display_body_position = 'none';
				$display_no_show_position = 'none';

				if ($position == 'T') {
					$display_title_position = 'inline';
				} elseif ($position == 'H') {
					$display_header_position = 'inline';
				} elseif ($position == 'B') {
					$display_body_position = 'inline';
				} else {
					$display_no_show_position = 'inline';
				}

				$currentFieldId = $fieldListResult[$module_name][$j][4];

				$field[] = '<input type="hidden" id="'.$module_id.'_position_'.$currentFieldId.'" name="'.$currentFieldId.'_position" value="'.$position.'" />
					<a href="javascript:void(0);" onclick="tooglePosition(\''.$module_id.'_position_'.$currentFieldId.'\')">
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_title"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('field_position_T.png', $theme).'"
							style="display: '.$display_title_position.'"
							border="0"
						/>
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_header"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('field_position_H.png', $theme).'"
							style="display: '.$display_header_position.'"
							border="0"
						/>
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_body"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('field_position_B.png', $theme).'"
							style="display: '.$display_body_position.'"
							border="0"
						/>
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_no_show"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('no.gif', $theme).'"
							style="display: '.$display_no_show_position.'"
							border="0"
						/>
					</a>';

				$field_module[]=$field;
			}
			$privilege_field[$module_id] = array_chunk($field_module, 3);
			next($fieldListResult);
		}
	} else {
		$fieldListResult = getProfile2AllFieldList($modArr, 1);
		for ($i=0; $i<count($fieldListResult); $i++) {
			$field_module=array();
			$module_name=key($fieldListResult);
			$module_id = getTabid($module_name);
			for ($j=0; $j<count($fieldListResult[$module_name]); $j++) {
				$fldLabel= $fieldListResult[$module_name][$j][0];
				$uitype = $fieldListResult[$module_name][$j][2];
				$displaytype = $fieldListResult[$module_name][$j][5];
				$typeofdata = $fieldListResult[$module_name][$j][6];
				$fieldtype = explode("~", $typeofdata);
				$mandatory = '';
				$readonly = '';
				$field=array();

				$fieldAccessMandatory = false;
				$fieldAccessRestricted = false;
				if ($fieldtype[1] == "M") {
					$mandatory = '<font color="red">*</font>';
					$readonly = 'disabled';
					$fieldAccessMandatory = true;
				}

				if ($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1) {
					$mandatory = '<font color="blue">*</font>';
					$readonly = 'disabled';
					$visible = "";
					$fieldAccessRestricted = true;
				} else {
					$visible = "checked";
				}
				$field[] = $mandatory.' '.getTranslatedString($fldLabel, $module_name);
				$field[]='<input type="checkbox" id="'.$module_id.'_field_'.$fieldListResult[$module_name][$j][4].'" onClick="selectUnselect(this);" name="'
					.$fieldListResult[$module_name][$j][4].'" '.$visible.' '.$readonly.'>';

				// Check for Read-Only or Read-Write Access for the field.
				$fieldReadOnlyAccess = $fieldListResult[$module_name][$j][3];
				if ($fieldReadOnlyAccess == 1) {
					$display_locked = 'inline';
					$display_unlocked = 'none';
				} else {
					$display_locked = 'none';
					$display_unlocked = 'inline';
				}
				if (!$fieldAccessMandatory && !$fieldAccessRestricted) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
						.'_readonly" value="'.$fieldReadOnlyAccess.'" /><a href="javascript:void(0);" onclick="toogleAccess(\''.$module_id.'_readonly_'
						.$fieldListResult[$module_name][$j][4].'\');"><img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_unlocked" src="'
						.vtiger_imageurl('unlocked.png', $theme).'" style="display:'.$display_unlocked.'" border="0"><img id="'.$module_id.'_readonly_'
						.$fieldListResult[$module_name][$j][4].'_locked" src="'.vtiger_imageurl('locked.png', $theme).'" style="display:'.$display_locked
						.'" border="0"></a>';
				} elseif ($fieldAccessMandatory) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
					.'_readonly" value="0" /><img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				} else {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4]
						.'_readonly" value="'.$fieldReadOnlyAccess.'" /><img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				}

				$currentFieldId = $fieldListResult[$module_name][$j][4];

				$field[] = '<input type="hidden" id="'.$module_id.'_position_'.$currentFieldId.'" name="'.$currentFieldId.'_position" value="B" />
					<a href="javascript:void(0);" onclick="tooglePosition(\''.$module_id.'_position_'.$currentFieldId.'\')">
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_title"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('field_position_T.png', $theme).'"
							style="display: none"
							border="0"
						/>
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_header"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('field_position_H.png', $theme).'"
							style="display: none"
							border="0"
						/>
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_body"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('field_position_B.png', $theme).'"
							style="display: inline"
							border="0"
						/>
						<img id="'.$module_id.'_position_'.$currentFieldId.'_position_no_show"
							class="'.$module_id.'_position_'.$currentFieldId.'position_image"
							src="'.vtiger_imageurl('no.gif', $theme).'"
							style="display: none"
							border="0"
						/>
					</a>';
				$field_module[]=$field;
			}
			$privilege_field[$module_id] = array_chunk($field_module, 3);
			next($fieldListResult);
		}
	}
}

$smarty->assign('FIELD_PRIVILEGES', $privilege_field);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
if ($mode == 'view') {
	$smarty->display('ProfileDetailView.tpl');
} else {
	$smarty->display('EditProfile.tpl');
}

/** returns html image code based on the input id
 * @param $id -- Role Name:: Type varchar
 * @returns $value -- html image code:: Type varcha:w
 */
function getGlobalDisplayValue($id, $actionid) {
	global $theme;
	if ($id == '') {
		$value = '&nbsp;';
	} elseif ($id == 0) {
		$value = '<img src="' . vtiger_imageurl('prvPrfSelectedTick.gif', $theme) . '">';
	} elseif ($id == 1) {
		$value = '<img src="' . vtiger_imageurl('no.gif', $theme) . '">';
	} else {
		$value = '&nbsp;';
	}
	return $value;
}

/** returns html check box code based on the input id
 * @param $id -- Role Name:: Type varchar
 * @returns $value -- html check box code:: Type varcha:w
 */
function getGlobalDisplayOutput($id, $actionid) {
	if ($actionid == '1') {
		$name = 'view_all';
	} elseif ($actionid == '2') {
		$name = 'edit_all';
	}

	if ($id == '' && $id != 0) {
		$value = '';
	} elseif ($id == 0) {
		$value = '<input type="checkbox" id="'.$name.'_chk" onClick="invoke'.$name.'();" name="'.$name.'" checked>';
	} elseif ($id == 1) {
		$value = '<input type="checkbox" id="'.$name.'_chk" onClick="invoke'.$name.'();" name="'.$name.'">';
	}
	return $value;
}

/** returns html image code based on the input id
 * @param $id -- Role Name:: Type varchar
 * @returns $value -- html image code:: Type varcha:w
 */
function getDisplayValue($id) {
	global $theme;
	if ($id == '') {
		$value = '&nbsp;';
	} elseif ($id == 0) {
		$value = '<img src="' . vtiger_imageurl('prvPrfSelectedTick.gif', $theme) .'">';
	} elseif ($id == 1) {
		$value = '<img src="' . vtiger_imageurl('no.gif', $theme) .'">';
	} else {
		$value = '&nbsp;';
	}
	return $value;
}

/** returns html check box code based on the input id
 * @param $id -- Role Name:: Type varchar
 * @returns $value -- html check box code:: Type varcha:w
 */
function getDisplayOutput($id, $tabid, $actionid) {
	if ($actionid == '') {
		$name = $tabid.'_tab';
		$ckbox_id = 'tab_chk_com_'.$tabid;
		$jsfn = 'hideTab('.$tabid.')';
	} else {
		$temp_name = getActionname($actionid);
		$name = $tabid.'_'.$temp_name;
		$ckbox_id = 'tab_chk_'.$actionid.'_'.$tabid;
		if ($actionid == 1) {
			$jsfn = 'unSelectCreate('.$tabid.')';
		} elseif ($actionid == 4) {
			$jsfn = 'unSelectView('.$tabid.')';
		} elseif ($actionid == 2) {
			$jsfn = 'unSelectDelete('.$tabid.')';
		} else {
			$ckbox_id = $tabid.'_field_util_'.$actionid;
			$jsfn = 'javascript:';
		}
	}
	if ($id == '' && $id != 0) {
		$value = '';
	} elseif ($id == 0) {
		$value = '<input type="checkbox" onClick="'.$jsfn.';" id="'.$ckbox_id.'" name="'.$name.'" checked>';
	} elseif ($id == 1) {
		$value = '<input type="checkbox" onClick="'.$jsfn.';" id="'.$ckbox_id.'" name="'.$name.'">';
	}
	return $value;
}

function profileExists($profileId) {
	global $adb;
	$result = $adb->pquery('SELECT 1 FROM vtiger_profile WHERE profileid = ?', array($profileId));
	if ($adb->num_rows($result) > 0) {
		return true;
	}
	return false;
}

function orderByModule($tab_perr_array) {
	$mnames = array();
	foreach ($tab_perr_array as $tabid => $tperm) {
		$mname = getTabModuleName($tabid);
		$mnames[$tabid] = getTranslatedString($mname, $mname);
	}
	asort($mnames);
	$tpsorted = array();
	foreach ($mnames as $tabid => $mname) {
		$tpsorted[$tabid] = $tab_perr_array[$tabid];
	}
	return $tpsorted;
}
?>