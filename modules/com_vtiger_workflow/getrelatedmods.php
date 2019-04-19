<?php
require_once 'include/Webservices/getRelatedModules.php';
$module = '';
global $current_user;
if (isset($_REQUEST['currentmodule'])) {
	$module = $_REQUEST['currentmodule'];
}
$relatedMods = getRelatedModulesInfomation($module, $current_user);
$listres = '';
foreach ($relatedMods as $keymod => $modval) {
	$listres =$listres .'<option value='.$modval['related_module'].'>'.$keymod.'</option>';
}
echo $listres;
?>