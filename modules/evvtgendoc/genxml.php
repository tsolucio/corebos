<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************
 *  Module       : evvtgendoc
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'config.inc.php';
require_once 'modules/Users/Users.php';
require_once 'modules/evvtgendoc/OpenDocument.php'; // open document class
require_once 'include/logging.php';
$log =& LoggerManager::getLogger('index');
global $currentModule,$current_language,$current_user,$adb,$mod_strings;

function show_error_import($errmsg) {
	global $currentModule;
	echo '<div id="errorcontainer" style="padding:20px;">
		<div id="errormsg" style="color: #f85454;font-weight: bold;padding: 10px;border: 1px solid #FF0000;background: #FFFFFF;border-radius: 5px;margin-bottom: 10px;">'
		.getTranslatedString($errmsg, $currentModule).'</div></div>';
}

$xmltpl=vtlib_purify($_REQUEST['genxmltemplate']);
if (empty($xmltpl) || !file_exists($xmltpl)) {
	show_error_import('GenXMLTplRequired');
	die();
}
$crmid=vtlib_purify($_REQUEST['recordval']);
if (empty($crmid)) {
	show_error_import('GenXMLIDRequired');
	die();
}
$crmid = preg_replace('/[^0-9]/', '', $crmid);
$module=getSalesEntityType($crmid);

$odtout = new OpenDocument;
$bytes = $odtout->GenXML($xmltpl, $crmid, $module);
$out = '<table class="lvtCol" width="100%"><tr><td>';
$out.=$mod_strings['LBL_INFORMATION'].$module.$mod_strings['LBL_WITH']." : $crmid ".$mod_strings['LBL_TEMPLATE']." $xmltpl<br/>";
$out.= '<a href="cache/genxml/xmlgen'.$crmid.'.xml">'.$app_strings['DownloadMergeFile'].'</a></td></tr></table><br/>';
echo $out;
?>
