<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ('License'); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  ********************************************************************************/
require_once 'Smarty_setup.php';

global $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$smarty = new vtigerCRM_Smarty;

$smarty->assign('THEME', $theme);
$smarty->assign('IMAGEPATH', $image_path);
$smarty->display('Clock.tpl');
?>
