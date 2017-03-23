<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/utils.php');
require_once('include/logging.php');
require_once("modules/Dashboard/DashboardCharts.php");
global $current_language, $currentModule, $action;
$current_module_strings = return_module_language($current_language, 'Dashboard');
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
require('user_privileges/user_privileges_'.$current_user->id.'.php');

// Get _dom arrays from Database
$comboFieldNames = Array('leadsource'=>'lead_source_dom');
$comboFieldArray = getComboArray($comboFieldNames);

$log = LoggerManager::getLogger('lead_source_by_outcome');

if (isset($_REQUEST['lsbo_refresh'])) { $refresh = $_REQUEST['lsbo_refresh']; }
else { $refresh = false; }

// added for auto refresh
$refresh = true;

$tempx = array();
$datax = array();
//get list of sales stage keys to display
if (isset($_SESSION['lsbo_lead_sources']) && count($_SESSION['lsbo_lead_sources']) > 0 && !isset($_REQUEST['lsbo_lead_sources'])) {
	$tempx = $_SESSION['lsbo_lead_sources'];
	$log->debug("_SESSION['lsbo_lead_sources'] is:");
	$log->debug($_SESSION['lsbo_lead_sources']);
}
elseif (isset($_REQUEST['lsbo_lead_sources']) && count($_REQUEST['lsbo_lead_sources']) > 0) {
	$tempx = $_REQUEST['lsbo_lead_sources'];
	$current_user->setPreference('lsbo_lead_sources', $_REQUEST['lsbo_lead_sources']);
	$log->debug("_REQUEST['lsbo_lead_sources'] is:");
	$log->debug($_REQUEST['lsbo_lead_sources']);
	$log->debug("_SESSION['lsbo_lead_sources'] is:");
	$log->debug($_SESSION['lsbo_lead_sources']);
}

//set $datax using selected sales stage keys 
if (count($tempx) > 0) {
	foreach ($tempx as $key) {
		$datax[$key] = $comboFieldArray['lead_source_dom'][$key];
	}
}
else {
	$datax = $comboFieldArray['lead_source_dom'];
}

$ids = array();
//get list of user ids for which to display data
if (isset($_REQUEST['showmypipeline'])) {
	$ids = array($current_user->id);
} elseif (isset($_REQUEST['showpipelineof']) and is_numeric($_REQUEST['showpipelineof'])) {
	$ids = array($_REQUEST['showpipelineof']);
} elseif (isset($_SESSION['lsbo_ids']) && count($_SESSION['lsbo_ids']) != 0 && !isset($_REQUEST['lsbo_ids'])) {
	$ids = $_SESSION['lsbo_ids'];
	$log->debug("_SESSION['lsbo_ids'] is:");
	$log->debug($_SESSION['lsbo_ids']);
}
elseif (isset($_REQUEST['lsbo_ids']) && count($_REQUEST['lsbo_ids']) > 0) {
	$ids = $_REQUEST['lsbo_ids'];
	$current_user->setPreference('lsbo_ids', $_REQUEST['lsbo_ids']);
	$log->debug("_REQUEST['lsbo_ids'] is:");
	$log->debug($_REQUEST['lsbo_ids']);
	$log->debug("_SESSION['lsbo_ids'] is:");
	$log->debug($_SESSION['lsbo_ids']);
}
else {
	$ids = get_user_array(false);
	$ids = array_keys($ids);
}

if(isPermitted('Potentials','index')=="yes")
{
$width = 1100;
$height = 600;
if(isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX')
{
	$width = 350;
	$height = 250;
}

echo DashboardCharts::lead_source_by_outcome($datax, $ids, $width, $height);
echo "<P><font size='1'><em>".$current_module_strings['LBL_LEAD_SOURCE_BY_OUTCOME_DESC']."</em></font></P>";
if (isset($_REQUEST['lsbo_edit']) && $_REQUEST['lsbo_edit'] == 'true') {
?>
<form action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="display_view" value="<?php echo vtlib_purify($_REQUEST['display_view'])?>">
<input type="hidden" name="lsbo_refresh" value="true">
<table cellpadding="2" border="0"><tbody>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_LEAD_SOURCES'];?></strong></td>
<td valign='top'><select name="lsbo_lead_sources[]" multiple size='3'><?php echo get_select_options_with_id($comboFieldArray['lead_source_dom'],$_SESSION['lsbo_lead_sources']); ?></select></td>
</tr><tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_USERS'];?></td>
<?php if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid('Potentials')] == 3 or $defaultOrgSharingPermission[getTabid('Potentials')] == 0)) { ?>
	<td valign='top'><select name="lsbo_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(FALSE, "Active", $current_user->id,'private'),$_SESSION['lsbo_ids']); ?></select></td>
<?php } else { ?>
	<td valign='top' ><select name="lsbo_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(FALSE, "Active",$current_user->id),$_SESSION['lsbo_ids']); ?></select></td>
<?php } ?>	
</tr><tr>
<td align="right"><br /> <input class="button" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" /></td>
</tr></table>
</form>
<?php }
else {
?>
<div align=right><FONT size='1'>
[<a href="javascript:;" onClick="changeView('<?php echo isset($_REQUEST['display_view']) ? vtlib_purify($_REQUEST['display_view']) : '';?>');"><?php echo $current_module_strings['LBL_REFRESH'];?></a>]
[<a href="index.php?module=<?php echo $currentModule;?>&action=index&lsbo_edit=true&display_view=<?php echo isset($_REQUEST['display_view']) ? vtlib_purify($_REQUEST['display_view']) : '';?>"><?php echo $current_module_strings['LBL_EDIT'];?></a>]
</FONT></div>
<?php }
}
else
{
	echo $mod_strings['LBL_NO_PERMISSION'];
}
?>