<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
/*
View
Edit
Move to
Add
Delete
*/
function getKanbanTileMenu($tabid, $module, $crmid, $lanenames, $fieldName, $lanename, $kanbanID) {
	$links = array();
	$seq = 0;
	if (isPermitted($module, 'DetailView', $crmid)=='yes') {
		$link = new Vtiger_Link();
		$link->tabid = $tabid;
		$link->linkid = 0;
		$link->linktype = 'KANBANMENU';
		$link->linklabel = getTranslatedString('LBL_VIEW', 'Settings');
		$link->linkurl = "window.open('index.php?action=DetailView&module=".$module.'&record='.$crmid."&Module_Popup_Edit=1', null, cbPopupWindowSettings+',dependent=yes');";
		$link->linkicon = '';
		$link->sequence = $seq++;
		$link->status = false;
		$link->handler_path = '';
		$link->handler_class = '';
		$link->handler = '';
		$link->onlyonmymodule = '0';
		$links[] = $link;
	}
	if (isPermitted($module, 'EditView', $crmid)=='yes') {
		$link = new Vtiger_Link();
		$link->tabid = $tabid;
		$link->linkid = 0;
		$link->linktype = 'KANBANMENU';
		$link->linklabel = getTranslatedString('LBL_EDIT', 'CustomView');
		$saveParam = urlencode(json_encode(['id'=>$kanbanID, 'lanename'=>$lanename]));
		$link->linkurl = "window.open('index.php?action=EditView&module=".$module.'&record='.$crmid
			.'&Module_Popup_Save=kbPopupSaveHook&Module_Popup_Edit=1&Module_Popup_Save_Param='.$saveParam
			."', null, cbPopupWindowSettings + ',dependent=yes');";
		$link->linkicon = '';
		$link->sequence = $seq++;
		$link->status = false;
		$link->handler_path = '';
		$link->handler_class = '';
		$link->handler = '';
		$link->onlyonmymodule = '0';
		$links[] = $link;
		$links[] = '<li class="slds-has-divider_top-space" role="separator"></li>';
		foreach ($lanenames as $lname) {
			$link = new Vtiger_Link();
			$link->tabid = $tabid;
			$link->linkid = 0;
			$link->linktype = 'KANBANMENU';
			$link->linklabel = getTranslatedString($lname, $module);
			$link->linkurl = "javascript:dtlViewAjaxDirectFieldSave('".$lname."', '".$module."', '', '".$fieldName."', ".$crmid.", '');".
				"kbMoveTile('".$kanbanID."', '".$lname."', '".$module."', ".$crmid.');';
			$link->linkicon = '';
			$link->sequence = $seq++;
			$link->status = false;
			$link->handler_path = '';
			$link->handler_class = '';
			$link->handler = '';
			$link->onlyonmymodule = '0';
			$links[] = $link;
		}
		$links[] = '<li class="slds-has-divider_top-space" role="separator"></li>';
	}
	if (isPermitted($module, 'CreateView', $crmid)=='yes') {
		$link = new Vtiger_Link();
		$link->tabid = $tabid;
		$link->linkid = 0;
		$link->linktype = 'KANBANMENU';
		$link->linklabel = getTranslatedString('LBL_ADD', 'Settings');
		$link->linkurl="window.open('index.php?action=EditView&module=".$module.'&record=&Module_Popup_Save=kbPopupSaveHook&Module_Popup_Edit=1&Module_Popup_Save_Param='
			.$kanbanID.'&'.$fieldName.'='.$lanename."', null, cbPopupWindowSettings + ',dependent=yes');";
		$link->linkicon = '';
		$link->sequence = $seq++;
		$link->status = false;
		$link->handler_path = '';
		$link->handler_class = '';
		$link->handler = '';
		$link->onlyonmymodule = '0';
		$links[] = $link;
	}
	if (isPermitted($module, 'Delete', $crmid)=='yes') {
		$link = new Vtiger_Link();
		$link->tabid = $tabid;
		$link->linkid = 0;
		$link->linktype = 'KANBANMENU';
		$link->linklabel = getTranslatedString('LBL_DELETE', 'Settings');
		$link->linkurl = "javascript:kbDeleteElement('".$module."', ".$crmid.", '".$kanbanID."');";
		$link->linkicon = '';
		$link->sequence = $seq++;
		$link->status = false;
		$link->handler_path = '';
		$link->handler_class = '';
		$link->handler = '';
		$link->onlyonmymodule = '0';
		$links[] = $link;
	}
	return $links;
}
?>