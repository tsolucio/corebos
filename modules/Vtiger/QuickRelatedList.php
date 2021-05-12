<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
global $adb,$current_user;
$formodule = vtlib_purify($_REQUEST['formodule']);
$singlepane_view = GlobalVariable::getVariable('Application_Single_Pane_View', 0, $formodule);
$singlepane_view = empty($singlepane_view) ? 'false' : 'true';
$userprivs = $current_user->getPrivileges();
$is_admin = $userprivs->isAdmin();
$fortabid = getTabid($formodule);
$forrecord = vtlib_purify($_REQUEST['forrecord']);
$Modules2Show = array();
$bmapname = $formodule.'_QuickRelatedList';
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$ModSet = $cbMap->ModuleSetMapping();
	$Modules2Show = $ModSet->getFullModuleSet();
}
$rls = array();
$query = 'select relation_id,related_tabid,label,vtiger_tab.name,actions,relationfieldid,vtiger_tab.presence
	from vtiger_relatedlists
	inner join vtiger_tab on vtiger_tab.tabid=vtiger_relatedlists.related_tabid
	where vtiger_relatedlists.tabid=? order by sequence';
$result = $adb->pquery($query, array($fortabid));
while ($rel = $adb->fetch_array($result)) {
	$relatedId = $rel['relation_id'];
	$relationLabel = $rel['label'];
	$relatedTabId = $rel['related_tabid'];
	//check for disabled module.
	$permitted = $rel['presence'];
	if ((empty($relatedTabId) || $permitted == 0) && (empty($relatedTabId) || $is_admin || $userprivs->hasModuleAccess($relatedTabId))
		&& (count($Modules2Show)==0 || in_array($rel['name'], $Modules2Show))
	) {
		$rls[$relatedId] = array(
			'label'=>$relationLabel,
			'tabid'=>$relatedTabId,
			'module'=>$rel['name'],
			'actions'=>$rel['actions'],
			'relationfieldid'=>$rel['relationfieldid'],
		);
	}
}
$goto = getTranslatedString('LBL_JUMP_BTN');
$add = getTranslatedString('LBL_CREATE');
echo '<table  class="slds-table slds-table_cell-buffer slds-table_header-hidden">';
foreach ($rls as $relid => $relinfo) {
	$module = $relinfo['module'];
	$label = $relinfo['label'];
	$actions = $relinfo['actions'];
	$labelnospace = str_replace(' ', '', $label);
	echo '<tr class="slds-hint-parent">';
	$formodule = urlencode($formodule);
	$forrecord = urlencode($forrecord);
	$labelnospace = urlencode($labelnospace);
	$label = urlencode($label);
	$actions = urlencode($actions);
	if ($singlepane_view=='true') {
		$url = "module=$formodule&action={$formodule}Ajax&file=DetailViewAjax&record={$forrecord}";
		$url.= "&ajxaction=LOADRELATEDLIST&header={$label}&relation_id={$relid}&actions={$actions}";
		$onclick = 'onclick="javascript:loadRelatedListBlock('
			."'$url',"
			."'tbl_{$formodule}_{$labelnospace}','{$formodule}_{$labelnospace}');document.location='#tbl_".$formodule.'_'.$labelnospace.'\';"';
		echo '<td scope="row" class="slds-p-left_none"><a title="'.$goto.'" href="javascript:;" '.$onclick.'>'.getTranslatedString($relinfo['label'], $module).'</a></td>';
	} else {
		$url = "index.php?action=CallRelatedList&module=$formodule&record=$forrecord&selected_header=$label&relation_id=$relid#tbl_".$formodule.'_'.$labelnospace;
		echo '<td scope="row" class="slds-p-left_none"><a title="'.$goto.'" href="'.$url.'">'.getTranslatedString($relinfo['label'], $module).'</a></td>';
	}
	if ($module=='Emails') {
		$url = "fnvshobj(this,'sendmail_cont');sendmail('$formodule',$forrecord);";
		echo '<td scope="row" class="slds-align_absolute-center"><img align="absmiddle" width="20px" title="'.$add.'" src="themes/softed/images/btnL3Add.gif" onclick="'.$url.'"></td>';
	} else {
		if (empty($relinfo['relationfieldid'])) {
			$linkmode = '&createmode=link';
		} else {
			$result = $adb->pquery('select fieldname from vtiger_field where fieldid=?', array($relinfo['relationfieldid']));
			if ($result && $adb->num_rows($result)>0) {
				$linkmode = '&'.$adb->query_result($result, 0, 0).'='.$forrecord;
			} else {
				$linkmode = '';
			}
		}
		$url = "document.location='index.php?module=".urlencode($module).'&action=EditView'.$linkmode.'&return_id='.$forrecord;
		$url.= '&return_action=DetailView&return_module='.$formodule.'&cbfromid='.$forrecord."'";
		echo '<td scope="row" class="slds-align_absolute-center"><img align="absmiddle" width="20px" title="'.$add.'" src="themes/softed/images/btnL3Add.gif" onclick="'.$url.'"></td>';
	}
	echo '</tr>';
}
echo '</table>';
?>
