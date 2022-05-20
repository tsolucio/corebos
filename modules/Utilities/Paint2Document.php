<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
global $current_user, $app_strings;
require_once 'modules/cbtranslation/cbtranslation.php';
require_once 'include/ListView/ListViewJSON.php';
require_once 'Smarty_setup.php';
$smarty = new vtigerCRM_Smarty();
$formodule = vtlib_purify($_REQUEST['formodule']);
$forrecord = vtlib_purify($_REQUEST['forrecord']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $formodule);
$smarty->assign('RECORD', $forrecord);
$smarty->assign('USERID', vtws_getEntityId('Users').'x'.$current_user->id);
$smarty->assign('DOCID', vtws_getEntityId('DocumentFolders').'x');
$smarty->assign('WSID', vtws_getEntityId($formodule).'x'.$forrecord);
$lv = new GridListView('DocumentFolders');
$folders = $lv->findDocumentFolders();
$smarty->assign('FOLDERS', $folders);
$smarty->assign('USER_LANG', $current_user->language);
$smarty->display('Smarty/templates/Components/Paint/index.tpl');
?>