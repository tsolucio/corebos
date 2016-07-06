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
global $adb,$current_user,$singlepane_view;
$formodule = vtlib_purify($_REQUEST['formodule']);
if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
	include('tabdata.php');
}
require('user_privileges/user_privileges_' . $current_user->id . '.php');
$fortabid = getTabid($formodule);
$forrecord = vtlib_purify($_REQUEST['forrecord']);
$rls = array();
$query = 'select relation_id,related_tabid,label,vtiger_tab.name,actions
	from vtiger_relatedlists
	inner join vtiger_tab on vtiger_tab.tabid=vtiger_relatedlists.related_tabid
	where vtiger_relatedlists.tabid=? order by sequence';
$result = $adb->pquery($query, array($fortabid));
while ($rel = $adb->fetch_array($result)) {
	$relatedId = $rel['relation_id'];
	$relationLabel = $rel['label'];
	$relatedTabId = $rel['related_tabid'];
	//check for disabled module.
	$permitted = $tab_seq_array[$relatedTabId];
	if ($permitted === 0 || empty($relatedTabId)) {
		if ($is_admin || $profileTabsPermission[$relatedTabId] === 0 || empty($relatedTabId)) {
			$rls[$relatedId] = array('label'=>$relationLabel,'tabid'=>$relatedTabId,'module'=>$rel['name'],'actions'=>$rel['actions']);
		}
	}
}
$goto = getTranslatedString('LBL_JUMP_BTN');
$add = getTranslatedString('LBL_CREATE');
echo '<table width="100%" border=0>';
foreach ($rls as $relid => $relinfo) {
	$module = $relinfo['module'];
	$label = $relinfo['label'];
	$actions = $relinfo['actions'];
	$labelnospace = str_replace(' ', '', $label);
	echo '<tr>';
	if ($singlepane_view=='true') {
		$onclick = "onclick=\"javascript:loadRelatedListBlock(".
				"'module=$formodule&action={$formodule}Ajax&file=DetailViewAjax&record={$forrecord}&ajxaction=LOADRELATEDLIST&header={$label}&relation_id={$relid}&actions={$actions}',".
				"'tbl_{$formodule}_{$labelnospace}','{$formodule}_{$labelnospace}');document.location='#tbl_".$formodule.'_'.$labelnospace.'\';"';
		echo '<td><a title="'.$goto.'" href="javascript:;" '.$onclick.'>'.getTranslatedString($label,$module).'</a></td>';
	} else {
		echo '<td><a title="'.$goto.'" href="index.php?action=CallRelatedList&module='.$formodule.'&record='.$forrecord.'&selected_header='.$label.'&relation_id='.$relid.'">'.getTranslatedString($label,$module).'</a></td>';
	}
	if ($module=='Emails') {
		echo '<td><img align="absmiddle" width="20px" title="'.$add.'" src="themes/softed/images/btnL3Add.gif" onclick="fnvshobj(this,\'sendmail_cont\');sendmail(\''.$formodule."',$forrecord);".'"></td>';
	} else {
		echo '<td><img align="absmiddle" width="20px" title="'.$add.'" src="themes/softed/images/btnL3Add.gif" onclick="document.location=\'index.php?module='.$module.'&action=EditView&createmode=link&return_id='.$forrecord.'&return_module='.$formodule.'&cbfromid='.$forrecord.'\'"></td>';
	}
	echo '</tr>';
}
echo '</table>';
?>