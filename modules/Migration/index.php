<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
include_once 'modules/Migration/migrationutils.php';
global $current_user;
if($current_user->is_admin != 'on')
{
	die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

// Remove the Migration.tpl file from Smarty cache
$migration_tpl_file = get_smarty_compiled_file('Migration.tpl');
if ($migration_tpl_file != null) unlink($migration_tpl_file);

if(getMigrationCharsetFlag() != MIG_CHARSET_PHP_UTF8_DB_UTF8 && !isset($_REQUEST['migration_charstcheck'])) 
{
	include('modules/Migration/MigrationStep0.php');
	exit;
}

include("modules/Migration/versions.php");
require_once('Smarty_setup.php');
global $app_strings,$mod_strings,$theme,$currentModule;
include("vtigerversion.php");
//Check the current version before starting migration. If the current versin is latest, then we wont allow to do 5.x migration. But here we must allow for 4.x migration. Because 4.x migration can be done with out changing the current database. -Shahul
$status=true;
$exists=$adb->query("show create table vtiger_version");
if($exists)
{
	$result = $adb->query("select * from vtiger_version");
	$dbversion = $adb->query_result($result, 0, 'current_version');
	if($dbversion == $vtiger_current_version)
	{
		$status=false;
	}
}
if(!$adb->isPostgres()) {
	if(isset($_REQUEST['dbconversionutf8'])) {
		if($_REQUEST['dbconversionutf8'] == 'yes') {
			$query = " ALTER DATABASE ".$dbconfig['db_name']." DEFAULT CHARACTER SET utf8";
			$adb->query($query);
		}
	}
}

$smarty = new vtigerCRM_Smarty();

$smarty->assign("MIG_CHECK", $status);
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE","Migration");

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$source_versions = "<select id='source_version' name='source_version'>";
foreach($versions as $ver => $label)
{
	$ver_selected = '';
	if($label == $dbversion) $ver_selected = "selected";
	$source_versions .= "<option value='$ver' $ver_selected>$label</option>";
}
$source_versions .= "</select>";

$smarty->assign("SOURCE_VERSION", $source_versions);
global $vtiger_current_version;
$smarty->assign("CURRENT_VERSION", $vtiger_current_version);

$smarty->display("Migration.tpl");

?>
