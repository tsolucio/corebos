<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'include/utils/utils.php';
class ConfigurationUtils{
	static function getEntityModule() {
		global $adb;
		$unusedmodules = array('Events','Emails');
		$additionalModules = array('Home');
		$query = "SELECT name FROM vtiger_tab WHERE isentitytype = 1";
		$res = $adb->pquery($query, array());
		$rows = $adb->num_rows($res);
		for($i=0; $i <$rows; $i++) {
			$module[] = $adb->query_result($res, $i, 'name');
		}
		$modules = array_merge($module,$additionalModules);
		$modules = array_diff($modules,$unusedmodules);
		return $modules;
	}
}
?>
