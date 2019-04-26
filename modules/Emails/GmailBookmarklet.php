<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
require_once 'Smarty_setup.php';

global $log, $app_strings, $mod_strings, $currentModule, $theme, $site_URL;

$smarty = new vtigerCRM_Smarty;
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('CUSTOM_MODULE', true);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', getParentTab($currentModule));
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$gmailBookmarklet = "javascript:(function()%7Bvar%20doc=document;var%20bodyElement=document.body;doc.vtigerURL=%22$site_URL/%22;".
	"var%20scriptElement=document.createElement(%22script%22);scriptElement.type=%22text/javascript%22;".
	"scriptElement.src=doc.vtigerURL+%22modules/Emails/GmailBookmarkletTrigger.js%22;bodyElement.appendChild(scriptElement);%7D)();";
$smarty->assign('GMAIL_BOOKMARKLET', $gmailBookmarklet);
$smarty->display('modules/Emails/index.tpl');
?>