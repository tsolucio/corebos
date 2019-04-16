<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'include/logging.php';
require_once 'modules/Dashboard/DashboardCharts.php';
global $current_language, $currentModule, $action, $current_user;
$current_module_strings = return_module_language($current_language, 'Dashboard');
$userprivs = $current_user->getPrivileges();
$log = LoggerManager::getLogger('pipeline_by_sales_stage');

if (isset($_REQUEST['pbss_refresh'])) {
	$refresh = $_REQUEST['pbss_refresh'];
} else {
	$refresh = false;
}

// added for auto refresh
$refresh = true;

// Get _dom Arrays from Database
$comboFieldNames = array('sales_stage'=>'sales_stage_dom');
$comboFieldArray = getComboArray($comboFieldNames);
//added to fix the issue 4307
if (isset($_REQUEST['pbss_date_start']) && $_REQUEST['pbss_date_start'] == '') {
	coreBOS_Session::set('pbss_date_start', '');
} elseif (isset($_REQUEST['pbss_date_start']) && $_REQUEST['pbss_date_start'] != '') {
	coreBOS_Session::set('pbss_date_start', $_REQUEST['pbss_date_start']);
}
if (isset($_REQUEST['pbss_date_end']) && $_REQUEST['pbss_date_end'] == '') {
	coreBOS_Session::set('pbss_date_end', '');
} elseif (isset($_REQUEST['pbss_date_start']) && $_REQUEST['pbss_date_end'] != '') {
	coreBOS_Session::set('pbss_date_end', $_REQUEST['pbss_date_end']);
}
//get the dates to display
if (isset($_SESSION['pbss_date_start']) && $_SESSION['pbss_date_start'] != '' && !isset($_REQUEST['pbss_date_start'])) {
	$date_start = $_SESSION['pbss_date_start'];
} elseif (isset($_REQUEST['pbss_date_start']) && $_REQUEST['pbss_date_start'] != '') {
	$date_start = $_REQUEST['pbss_date_start'];
	$current_user->setPreference('pbss_date_start', $_REQUEST['pbss_date_start']);
} else {
	$date_start = '2001-01-01';
}

if (isset($_SESSION['pbss_date_end']) && $_SESSION['pbss_date_end'] != '' && !isset($_REQUEST['pbss_date_end'])) {
	$date_end = $_SESSION['pbss_date_end'];
} elseif (isset($_REQUEST['pbss_date_end']) && $_REQUEST['pbss_date_end'] != '') {
	$date_end = $_REQUEST['pbss_date_end'];
	$current_user->setPreference('pbss_date_end', $_REQUEST['pbss_date_end']);
} else {
	$date_end = '2100-01-01';
}

$tempx = array();
$datax = array();
//get list of sales stage keys to display
if (isset($_SESSION['pbss_sales_stages']) && count($_SESSION['pbss_sales_stages']) > 0 && !isset($_REQUEST['pbss_sales_stages'])) {
	$tempx = $_SESSION['pbss_sales_stages'];
} elseif (isset($_REQUEST['pbss_sales_stages']) && count($_REQUEST['pbss_sales_stages']) > 0) {
	$tempx = $_REQUEST['pbss_sales_stages'];
	$current_user->setPreference('pbss_sales_stages', $_REQUEST['pbss_sales_stages']);
}

//set $datax using selected sales stage keys
if (count($tempx) > 0) {
	foreach ($tempx as $key) {
		$datax[$key] = $comboFieldArray['sales_stage_dom'][$key];
	}
} else {
	$datax = $comboFieldArray['sales_stage_dom'];
}
$ids = array();
//get list of user ids for which to display data
if (isset($_REQUEST['showmypipeline'])) {
	$ids = array($current_user->id);
} elseif (isset($_REQUEST['showpipelineof']) && is_numeric($_REQUEST['showpipelineof'])) {
	$ids = array($_REQUEST['showpipelineof']);
} elseif (isset($_SESSION['pbss_ids']) && count($_SESSION['pbss_ids']) != 0 && !isset($_REQUEST['pbss_ids'])) {
	$ids = $_SESSION['pbss_ids'];
} elseif (isset($_REQUEST['pbss_ids']) && count($_REQUEST['pbss_ids']) > 0) {
	$ids = $_REQUEST['pbss_ids'];
	$current_user->setPreference('pbss_ids', $_REQUEST['pbss_ids']);
} else {
	$ids = get_user_array(false);
	$ids = array_keys($ids);
}

if (isPermitted('Potentials', 'index')=='yes') {
	$width = 1100;
	$height = 600;
	if (isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX') {
		$width = 350;
		$height = 250;
	}

	echo DashboardCharts::pipeline_by_sales_stage($datax, $date_start, $date_end, $ids, $width, $height);
	echo "<P><font size='1'><em>".$current_module_strings['LBL_SALES_STAGE_FORM_DESC']."</em></font></P>";
	if (isset($_REQUEST['pbss_edit']) && $_REQUEST['pbss_edit'] == 'true') {
		$cal_lang = "en";
		$cal_dateformat = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		$cal_dateformat = '%Y-%m-%d'; // fix providedd by Jlee for date bug in Dashboard
	?>
	<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
	<script type="text/javascript" src="jscalendar/calendar.js"></script>
	<script type="text/javascript" src="jscalendar/lang/calendar-<?php echo $cal_lang ?>.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<form name='pipeline_by_sales_stage' action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="pbss_refresh" value="true">
<input type="hidden" name="display_view" value="<?php echo vtlib_purify($_REQUEST['display_view'])?>">
<table cellpadding="2" border="0"><tbody>
<tr>

<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_START']?> <br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>

<td valign='top' ><input class="text" name="pbss_date_start" size='12' maxlength='10' id='date_start' value='<?php
if (isset($_SESSION['pbss_date_start'])) {
	echo vtlib_purify($_SESSION['pbss_date_start']);
} ?>'> <img src="<?php echo vtiger_imageurl('calendar.gif', $theme) ?>" id="date_start_trigger">
</td>
</tr><tr>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_END'];?><br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>
<td valign='top' ><input class="text" name="pbss_date_end" size='12' maxlength='10' id='date_end' value='<?php
if (isset($_SESSION['pbss_date_end'])) {
	echo vtlib_purify($_SESSION['pbss_date_end']);
} ?>'> <img src="<?php echo vtiger_imageurl('calendar.gif', $theme) ?>" id="date_end_trigger">
</td>
</tr><tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_SALES_STAGES'];?></td>
<td valign='top' ><select name="pbss_sales_stages[]" multiple size='3'><?php echo get_select_options_with_id($comboFieldArray['sales_stage_dom'], (isset($_SESSION['pbss_sales_stages']) ? $_SESSION['pbss_sales_stages'] : '')); ?></select></td>
</tr><tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_USERS'];?></td>
<?php
if (!$userprivs->hasGlobalWritePermission() && !$userprivs->hasModuleWriteSharing(getTabid('Potentials'))) {
?>
	<td valign='top'><select name="pbss_ids[]" multiple size='3'><?php
	$usrarray = get_user_array(false, 'Active', $current_user->id, 'private');
	echo get_select_options_with_id($usrarray, (isset($_SESSION['pbss_ids']) ? $_SESSION['pbss_ids'] : '')); ?>
	</select></td>
<?php } else { ?>
	<td valign='top'><select name="pbss_ids[]" multiple size='3'><?php
	$usrarray = get_user_array(false, 'Active', $current_user->id);
	echo get_select_options_with_id($usrarray, (isset($_SESSION['pbss_ids']) ? $_SESSION['pbss_ids'] : '')); ?>
	</select></td>
<?php } ?>
	</tr><tr>
	<td align="right"><br />
	<input class="button" onclick="return verify_chart_data(pipeline_by_sales_stage);" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" />
	</td>
	</tr></table>
	</form>
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "date_start", ifFormat : "<?php echo $cal_dateformat ?>", showsTime : false, button : "date_start_trigger", singleClick : true, step : 1
	});
	Calendar.setup ({
		inputField : "date_end", ifFormat : "<?php echo $cal_dateformat ?>", showsTime : false, button : "date_end_trigger", singleClick : true, step : 1
	});
	</script>

	<?php } else {
	?>
<div align=right><FONT size='1'>
[<a href="javascript:;" onClick="changeView('<?php echo isset($_REQUEST['display_view']) ? vtlib_purify($_REQUEST['display_view']) : '';?>');"><?php echo $current_module_strings['LBL_REFRESH'];?></a>]
[<a href="index.php?module=<?php echo $currentModule;?>&action=index&display_view=<?php echo isset($_REQUEST['display_view']) ? vtlib_purify($_REQUEST['display_view']) : '';?>&pbss_edit=true"><?php echo $current_module_strings['LBL_EDIT'];?></a>]
</FONT></div>
	<?php }
} else {
	echo $mod_strings['LBL_NO_PERMISSION'];
}
?>
