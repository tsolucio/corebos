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

require_once 'modules/Utilities/language/'.$current_language.'.lang.php';
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
$fieldtype = '';
if (strpos($query_string, '::')) {
	$resttype = substr($query_string, 0, strpos($query_string, '::'));
	$fldtypes = array();
	$rsft = $adb->query('SELECT distinct `fieldtype` FROM `vtiger_ws_fieldtype`');
	while ($ft = $adb->fetch_array($rsft)) {
		$fldtypes[] = $ft['fieldtype'];
	}
	if (in_array($resttype, $fldtypes)) {
		$query_string = substr($query_string, strpos($query_string, '::')+2);
		$fieldtype = $resttype;
	}
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
	if ($curModule=='Utilities') {
		getSearchModulesComboList($search_module);
	}
	$i = 0;
	$moduleRecordCount = array();
	if (empty($_REQUEST['ajax'])) {
		echo '<div id="globasearch_results" style="display:none;">';
	}
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', $image_path);
	$smarty->assign('ISAJAXCALL', isset($_REQUEST['ajax']));
	$smarty->assign('APP', $app_strings);
	$smarty->assign('TAG_SEARCH', $search_tag);
	$smarty->assign('SEARCH_MODULE', $search_module);
	$smarty->assign('SEARCH_STRING', htmlentities($search_val, ENT_QUOTES, $default_charset));
	$smarty->assign('MODULES_LIST', $object_array);
	foreach ($object_array as $module => $object_name) {
		if ($curModule == 'Utilities' || ($curModule == $module && !empty($_REQUEST['ajax']))) {
			$focus = CRMEntity::getInstance($module);
			if (isPermitted($module, 'index') == 'yes') {
				if (!file_exists("modules/$module/language/".$current_language.'.lang.php')) {
					$current_language = 'en_us';
				}
				require_once "modules/$module/language/".$current_language.'.lang.php';
				$smarty->assign('MOD', $mod_strings);
				$smarty->assign('MODULE', $module);
				$smarty->assign('SINGLE_MOD', $module);

				$listquery = getListQuery($module);

				if ($search_module != '' || $search_tag != '') {//This is for Tag search
					$where = getTagWhere($search_val, $current_user->id);
					$search_msg = $app_strings['LBL_TAG_SEARCH'];
					$search_msg .= '<b>'.to_html($search_val).'</b>';
				} else { //This is for Global search
					$where = getUnifiedWhere($listquery, $module, $search_val, $fieldtype);
					$search_msg = $app_strings['LBL_SEARCH_RESULTS_FOR'];
					$search_msg .= '<b>'.htmlentities($search_val, ENT_QUOTES, $default_charset).'</b>';
				}

				if ($where != '') {
					$listquery .= ' and ('.$where.')';
				}
				$Apache_Tika_URL = GlobalVariable::getVariable('Apache_Tika_URL', '');
				if (!empty($Apache_Tika_URL)) {
					$listquery .= ' OR vtiger_documentsearchinfo.text LIKE "%'.$search_val.'%"';
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
					if (!is_numeric($start) || $start < 0) {
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
					.'&noofrows='.(isset($_REQUEST['noofrows']) ? $_REQUEST['noofrows'] : 0).'&message='.(isset($_REQUEST['message']) ? $_REQUEST['message'] : '')
					.'&skipped_record_count='.(isset($_REQUEST['skipped_record_count']) ? $_REQUEST['skipped_record_count'] : 0);
				$url_string = '&modulename='.(isset($_REQUEST['modulename']) ? $_REQUEST['modulename'] : '').'&nav_module='.$module.$info_message;

				$oCustomView = new CustomView($module);
				$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $module, 'UnifiedSearch', '');
				$listview_header = getListViewHeader($focus, $module, '', '', '', 'global', $oCustomView);
				$listview_entries = getListViewEntries($focus, $module, $list_result, $navigation_array, '', '', '', '', $oCustomView);

				//Do not display the Header if there are no entries in listview_entries
				unset($listview_entries['total']);
				if (!empty($listview_entries)) {
					$display_header = 1;
					if (count($listview_entries) == 1) {
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
	if (empty($_REQUEST['ajax'])) {
		echo '</div>';
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
	if ($total_record_count == 0) {
		echo "<script type='text/javascript'>document.getElementById('globasearch_results').style.display='none';</script>";
		$smarty->assign('DESERTInfo', getTranslatedString('LBL_NO_DATA'));
		$smarty->display('Components/Desert.tpl');
	}
	if ($total_record_count > 1) {
		echo "<script type='text/javascript'>document.getElementById('globasearch_results').style.display='block';</script>";
	}

	//Added to display the Total record count
	if (empty($_REQUEST['ajax'])) {
		?>
	<script>
document.getElementById('global_search_total_count').innerHTML = " <?php echo $app_strings['LBL_TOTAL_RECORDS_FOUND']; ?>&nbsp;<b><?php echo $total_record_count; ?></b>";
	</script>
		<?php
	}
} else {
	echo '<br><br>';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-error');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERR_ONE_CHAR', 'Home'));
	$smarty->display('applicationmessage.tpl');
}

/**
 * Function to get the the List of Searchable Modules as a combo list which will be displayed in right corner under the Header
 * @param string $search_module -- search module, this module result will be shown defaultly
 */
function getSearchModulesComboList($search_module) {
	global $object_array, $app_strings;
	?>
		<div class="slds-page-header" style="position: sticky;top:40px;z-index:4;">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__figure">
					<span class="slds-icon_container slds-icon-standard-search" title="<?php echo getTranslatedString('LBL_SEARCH'); ?>">
						<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#search"></use>
						</svg>
						<span class="slds-assistive-text"><?php echo getTranslatedString('LBL_SEARCH'); ?></span>
					</span>
					</div>
					<div class="slds-media__body" style="flex:unset;width:50%;">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
						<h1>
							<span class="slds-page-header__title slds-truncate" id="global_search_total_count"></span>
						</h1>
						</div>
					</div>
					</div>
					<div class="slds-form-element" style="width:30%;">
						<label class="slds-form-element__label" for="global_search_module"><?php echo $app_strings['LBL_SHOW_RESULTS'] ?></label>
						<div class="slds-form-element__control">
							<div class="slds-select_container">
								<select id="global_search_module" name="global_search_module" onChange="displayModuleList(this);" class="slds-select">
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
							</div>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
	<?php
}

// To get the modules allowed for global search
if (!function_exists('getSearchModules')) {
	function getSearchModules($filter = array()) {
		return getSearchModulesCommon($filter);
	}
}
?>
