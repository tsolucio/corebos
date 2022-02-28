<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$Document_Folder_View = GlobalVariable::getVariable('Document_Folder_View', 1, 'Documents');
$moduleView = GlobalVariable::getVariable('Application_ListView_Layout', 'table');
if ($Document_Folder_View) {
	if ($moduleView == 'tuigrid') {
		//tuigrid with folders
		include_once 'modules/Documents/ToastListView.php';
	} else {
		//standart listview with folders
		include_once 'modules/Documents/FoldersListView.php';
	}
} else {
	//tuigrid or standard without folders
	include_once 'modules/Vtiger/ListView.php';
}
?>