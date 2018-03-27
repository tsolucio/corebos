<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the 'Attribution-NonCommercial-ShareAlike'
 * Vizsage Public License (the 'License'). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  'AS IS' BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : coreBOS ping. Live check
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *  Proud member of the coreBOS family!  http://corebos.org
 *************************************************************************************************/

if (version_compare(phpversion(), '5.4.0') < 0 || version_compare(phpversion(), '7.2.0') >= 0) {
	echo 'NOK: incorrect PHP version';
	die();
}

if (!is_file('config.inc.php')) {
	echo 'NOK: no configuration file';
	exit();
}

require_once 'config.inc.php';
if (!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
	echo 'NOK: no database configuration';
	exit();
}

if (!is_file('vtigerversion.php')) {
	echo 'NOK: no version file';
	exit();
}

require_once 'vtigerversion.php';
if (!isset($vtiger_current_version)) {
	echo 'NOK: no version configuration';
	exit();
}

if (!is_file('vtlib/Vtiger/Module.php')) {
	echo 'NOK: missing program files: vtlib';
	exit();
}

$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';

$result = $adb->query('SELECT * FROM vtiger_version');
if ($result && $adb->num_rows($result)==1) {
	$dbversion = $adb->query_result($result, 0, 'current_version');
	if (version_compare($dbversion, $vtiger_current_version, 'ne')) {
		echo 'NOK: version mismatch';
		exit();
	}
} else {
	echo 'NOK: could not access database';
	exit();
}

if (!is_file('modules/Users/Users.php')) {
	echo 'NOK: missing program file: users';
	exit();
}

require_once 'modules/Users/Users.php';
$user = Users::getActiveAdminUser();
if (!is_file('user_privileges/user_privileges_'.$user->id.'.php')) {
	echo 'NOK: missing admin user file';
	exit();
}

echo 'OK: basic testing has passed';
?>
