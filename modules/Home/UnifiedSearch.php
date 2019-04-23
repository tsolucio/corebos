<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/logging.php';
require_once 'modules/CustomView/CustomView.php';
require_once 'include/utils/utils.php';
require_once 'Smarty_setup.php';
global $mod_strings, $current_language, $default_charset, $app_strings;

require_once 'modules/Home/language/'.$current_language.'.lang.php';
// we eliminate order and sort by clause to avoid QueryGenerator errors on detail view
foreach ($_SESSION as $key => $value) {
	if (substr($key, -9)=='_Order_By') {
		coreBOS_Session::delete($key);
		$cmod = substr($key, 0, strlen($key)-9);
		coreBOS_Session::delete($cmod.'_Sort_Order');
	}
}
$total_record_count = 0;

$query_string = trim($_REQUEST['query_string']);
if (substr($query_string, 0, 5)=='tag::') {
	$query_string = substr($query_string, 5);
	$_REQUEST['search_tag'] = 'tag_search';
	$_REQUEST['search_module'] = 'All';
	unset($_REQUEST['search_onlyin']);
}
$curModule = vtlib_purify($_REQUEST['module']);
$search_tag = isset($_REQUEST['search_tag']) ? vtlib_purify($_REQUEST['search_tag']) : '';

if (isset($query_string) && $query_string != '') {
	// Was the search limited by user for specific modules?
	$search_onlyin = isset($_REQUEST['search_onlyin']) ? vtlib_purify($_REQUEST['search_onlyin']) : '';
	if (!empty($search_onlyin) && $search_onlyin != '--USESELECTED--') {
		$search_onlyin = explode(',', $search_onlyin);
	} elseif ($search_onlyin == '--USESELECTED--') {
		$search_onlyin = (isset($_SESSION['__UnifiedSearch_SelectedModules__']) ? $_SESSION['__UnifiedSearch_SelectedModules__'] : array());
	} else {
		$search_onlyin = array();
	}
	// Save the selection for futur use (UnifiedSearchModules.php)
	coreBOS_Session::set('__UnifiedSearch_SelectedModules__', $search_onlyin);

	$object_array = getSearchModules($search_onlyin);
	uasort($object_array, function ($a, $b) {
		return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
	});
	$topmodules = GlobalVariable::getVariable('Application_Global_Search_TopModules', '');
	if ($topmodules != '') {
		$userorderedmodules = array();
		$usertopmodules = explode(',', $topmodules);
		foreach ($usertopmodules as $mod) {
			$userorderedmodules[$mod] = $mod;
			unset($object_array[$mod]);
		}
		$object_array = array_merge($userorderedmodules, $object_array);
	}
	global $adb, $current_user, $theme;
	$image_path="themes/$theme/images/";

	$search_val = $query_string;
	$search_module = isset($_REQUEST['search_module']) ? $_REQUEST['search_module'] : '';

	if ($curModule=='Home') {
		getSearchModulesComboList($search_module);
	}
	$i = 0;
	$moduleRecordCount = array();
	foreach ($object_array as $module => $object_name) {
		if ($curModule == 'Home' || ($curModule == $module && !empty($_REQUEST['ajax']))) {
			$focus = CRMEntity::getInstance($module);
			if (isPermitted($module, 'index') == 'yes') {
				$smarty = new vtigerCRM_Smarty;

				if (!file_exists("modules/$module/language/".$current_language.".lang.php")) {
					$current_language = 'en_us';
				}
				require_once "modules/$module/language/".$current_language.".lang.php";

				$smarty->assign('MOD', $mod_strings);
				$smarty->assign('APP', $app_strings);
				$smarty->assign('THEME', $theme);
				$smarty->assign('IMAGE_PATH', $image_path);
				$smarty->assign('MODULE', $module);
				$smarty->assign('TAG_SEARCH', $search_tag);
				$smarty->assign('SEARCH_MODULE', $search_module);
				$smarty->assign('SINGLE_MOD', $module);
				$smarty->assign('SEARCH_STRING', htmlentities($search_val, ENT_QUOTES, $default_charset));

				if ($module=='Calendar') {
					$listquery = 'SELECT vtiger_activity.activityid as act_id,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.setype,
						vtiger_activity.*
						FROM vtiger_activity
						LEFT JOIN vtiger_activitycf ON vtiger_activitycf.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
						LEFT OUTER JOIN vtiger_activity_reminder ON vtiger_activity_reminder.activity_id = vtiger_activity.activityid
						LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid';
					$listquery .= getNonAdminAccessControlQuery($module, $current_user);
					$listquery .= ' where vtiger_crmentity.deleted=0 ';
				} else {
					$listquery = getListQuery($module);
				}
				$oCustomView = '';

				$oCustomView = new CustomView($module);
				//Instead of getting current customview id, use cvid of All so that all entities will be found
				//$viewid = $oCustomView->getViewId($module);
				$cv_res = $adb->pquery("select cvid from vtiger_customview where viewname='All' and entitytype=?", array($module));
				$viewid = $adb->query_result($cv_res, 0, 'cvid');
				$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);

				$listquery = $oCustomView->getModifiedCvListQuery($viewid, $listquery, $module);
				if ($module == 'Calendar') {
					if (!isset($oCustomView->list_fields['Close'])) {
						$oCustomView->list_fields['Close']=array ('activity' => 'status');
					}
					if (!isset($oCustomView->list_fields_name['Close'])) {
						$oCustomView->list_fields_name['Close']='status';
					}
					$listquery = str_replace(',vtiger_contactdetails.contactid', '', $listquery);
					$listquery = str_ireplace('select ', 'select distinct ', $listquery);
				}

				if ($search_module != '' || $search_tag != '') {//This is for Tag search
					$where = getTagWhere($search_val, $current_user->id);
					$search_msg = $app_strings['LBL_TAG_SEARCH'];
					$search_msg .= "<b>".to_html($search_val)."</b>";
				} else { //This is for Global search
					$where = getUnifiedWhere($listquery, $module, $search_val);
					$search_msg = $app_strings['LBL_SEARCH_RESULTS_FOR'];
					$search_msg .=	"<b>".htmlentities($search_val, ENT_QUOTES, $default_charset)."</b>";
				}

				if ($where != '') {
					$listquery .= ' and ('.$where.')';
				}
				if (!(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')) {
					$count_result = $adb->query($listquery);
					$noofrows = $adb->num_rows($count_result);
				} else {
					$noofrows = vtlib_purify($_REQUEST['recordCount']);
				}
				$moduleRecordCount[$module]['count'] = $noofrows;

				$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module);
				if (!empty($_REQUEST['start'])) {
					$start = $_REQUEST['start'];
					if ($start == 'last') {
						$count_result = $adb->query(mkCountQuery($listquery));
						$noofrows = $adb->query_result($count_result, 0, 'count');
						if ($noofrows > 0) {
							$start = ceil($noofrows/$list_max_entries_per_page);
						}
					}
					if (!is_numeric($start)) {
						$start = 1;
					} elseif ($start < 0) {
						$start = 1;
					}
					$start = ceil($start);
				} else {
					$start = 1;
				}

				$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);
				$limitStartRecord = ($navigation_array['start'] - 1) * $list_max_entries_per_page;

				$listquery = $listquery. " LIMIT $limitStartRecord, $list_max_entries_per_page";
				$list_result = $adb->query($listquery);

				$moduleRecordCount[$module]['recordListRangeMessage'] = getRecordRangeMessage($list_result, $limitStartRecord, $noofrows);

				$info_message='&recordcount='.(isset($_REQUEST['recordcount']) ? $_REQUEST['recordcount'] : 0)
					.'&noofrows='.(isset($_REQUEST['noofrows']) ? $_REQUEST['noofrows'] : 0).'&message='.(isset($_REQUEST['message']) ? $_REQUEST['message'] : '').
					'&skipped_record_count='.(isset($_REQUEST['skipped_record_count']) ? $_REQUEST['skipped_record_count'] : 0);
				$url_string = '&modulename='.(isset($_REQUEST['modulename']) ? $_REQUEST['modulename'] : '').'&nav_module='.$module.$info_message;
				$viewid = '';

				$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $module, 'UnifiedSearch', $viewid);
				$listview_header = getListViewHeader($focus, $module, '', '', '', 'global', $oCustomView);
				$listview_entries = getListViewEntries($focus, $module, $list_result, $navigation_array, '', '', '', '', $oCustomView);

				//Do not display the Header if there are no entires in listview_entries
				if (count($listview_entries) > 0) {
					$display_header = 1;
					if (vtlib_isModuleActive('ListViewColors') && count($listview_entries) == 2) {
						$listview_entries_for1 = $listview_entries;
					} elseif (!vtlib_isModuleActive('ListViewColors') && count($listview_entries) == 1) {
						$listview_entries_for1 = $listview_entries;
					}
				} else {
					$display_header = 0;
				}
				$smarty->assign('NAVIGATION', $navigationOutput);
				$smarty->assign('LISTHEADER', $listview_header);
				$smarty->assign('LISTENTITY', $listview_entries);
				$smarty->assign('DISPLAYHEADER', $display_header);
				$smarty->assign('HEADERCOUNT', count($listview_header));
				$smarty->assign('ModuleRecordCount', $moduleRecordCount);
				$total_record_count = $total_record_count + $noofrows;
				$smarty->assign('SEARCH_CRITERIA', "( $noofrows )".$search_msg);
				$smarty->assign('MODULES_LIST', $object_array);
				$smarty->assign('CUSTOMVIEW_OPTION', $customviewcombo_html);

				if (($i != 0 && empty($_REQUEST['ajax'])) || !(empty($_REQUEST['ajax']))) {
					$smarty->display('UnifiedSearchAjax.tpl');
				} else {
					$smarty->display('UnifiedSearchDisplay.tpl');
				}
				coreBOS_Session::delete('lvs^'.$module);
				$i++;
			}
		}
	}
	if ($total_record_count == 1) {
		// we have just one record in one module > we go there directly
		$modwith1 = array_filter($moduleRecordCount, function ($e) {
			return ($e['count']==1);
		});
		$modfound = array_keys($modwith1);
		$modfound = $modfound[0];
		$recfound = array_keys($listview_entries_for1);
		$recfound = $recfound[0];
		if ($recfound != '') {
			echo "<script type='text/javascript'>gotourl('index.php?module=$modfound&record=$recfound&action=DetailView');</script>";
		}
	}

	//Added to display the Total record count
	if (empty($_REQUEST['ajax'])) {
?>
	<script>
document.getElementById("global_search_total_count").innerHTML = " <?php echo $app_strings['LBL_TOTAL_RECORDS_FOUND'] ?><b><?php echo $total_record_count; ?></b>";
	</script>
<?php
	}
} else {
	echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>".$mod_strings['ERR_ONE_CHAR']."</em>";
}

/**
 * Function to get the the List of Searchable Modules as a combo list which will be displayed in right corner under the Header
 * @param string $search_module -- search module, this module result will be shown defaultly
 */
function getSearchModulesComboList($search_module) {
	global $object_array, $app_strings;
?>
		<script>
		function displayModuleList(selectmodule_view) {
			<?php
			foreach ($object_array as $module => $object_name) {
				if (isPermitted($module, 'index') == 'yes') {
			?>
				mod = "global_list_"+"<?php echo $module; ?>";
				if (selectmodule_view.options[selectmodule_view.options.selectedIndex].value == "All")
					show(mod);
				else
					hide(mod);
			<?php
				}
			}
			?>

			if (selectmodule_view.options[selectmodule_view.options.selectedIndex].value != "All") {
				selectedmodule="global_list_"+selectmodule_view.options[selectmodule_view.options.selectedIndex].value;
				show(selectedmodule);
			}
		}
		</script>
		<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		<tr>
		<td colspan="3" id="global_search_total_count" style="padding-left:30px">&nbsp;</td>
		<td nowrap align="right"><?php echo $app_strings['LBL_SHOW_RESULTS'] ?>&nbsp;
			<select id="global_search_module" name="global_search_module" onChange="displayModuleList(this);" class="small">
			<option value="All"><?php echo $app_strings['COMBO_ALL'] ?></option>
			<?php
			foreach ($object_array as $module => $object_name) {
				$selected = '';
				if ($search_module != '' && $module == $search_module) {
					$selected = 'selected';
				}
				if ($search_module == '' && $module == 'All') {
					$selected = 'selected';
				}
				if (isPermitted($module, 'index') == 'yes') {
			?>
				<option value="<?php echo $module; ?>" <?php echo $selected; ?> ><?php echo getTranslatedString($module, $module); ?></option>
			<?php
				}
			}
			?>
			</select>
		</td>
		</tr>
		</table>
<?php
}

// To get the modules allowed for global search
if (!function_exists('getSearchModules')) {
	function getSearchModules($filter = array()) {
		return getSearchModulesCommon($filter);
	}
}
?>