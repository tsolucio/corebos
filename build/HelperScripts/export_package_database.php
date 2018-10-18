<?php
/*********************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ********************************************************************************/
$title='coreBOS packaging tool';

// Turn on debugging level
$Vtiger_Utils_Log = false;

include_once 'vtlib/Vtiger/Module.php';
global $current_user,$adb;
$dl = isset($_REQUEST['download']) ? vtlib_purify($_REQUEST['download']) : '';
$dl = !empty($dl);
if (!$dl) {
	header('Content-Type: text/html; charset=UTF8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title><?php echo $title; ?></title>
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px;">
<?php
}
set_time_limit(0);
ini_set('memory_limit', '1024M');

if (empty($_REQUEST['modulename'])) {
	echo '<br><br><b>Necessary Parameter {modulename} not present</b><br>';
} else {
	$modulename = vtlib_purify($_REQUEST['modulename']);

	$module = Vtiger_Module::getInstance($modulename);

	if ($module) {
		$pkg = new Vtiger_Package();
		$pkg->export($module, 'build', $modulename.'.zip', $dl);
		if ($dl) {
			die();
		}
		echo "<b>Package should be exported to the build directory of your install.</b><br>";
	} else {
		echo "<b>Failed to find ".$modulename." module.</b><br>";
	}
}
?>
</body>
</html>