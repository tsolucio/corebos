<?php
/*************************************************************************************************
 * Copyright 2013 JPL TSolucio, S.L.  --  This file is a part of JPL TSolucio vtiger CRM Extensions.
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
*  Module       : evvtMenu
*  Version      : 1.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

function delMenuBranch($topofbranch) {
	global $adb;
	$mnurs = $adb->pquery('select evvtmenuid,mtype,mseq from vtiger_evvtmenu where mparent=?', array($topofbranch));
	if ($mnurs && $adb->num_rows($mnurs)>0) {
		while ($mnu = $adb->fetch_array($mnurs)) {
			if ($mnu['mtype']=='menu') {
				delMenuBranch($mnu['evvtmenuid']);
			}
			$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid=?', array($mnu['evvtmenuid']));
			$adb->pquery('update vtiger_evvtmenu set mseq=mseq-1 where mseq>? and mparent=?', array($mnu['mseq'], $topofbranch));
		}
	}
	$mnurs = $adb->pquery('select mparent,mseq from vtiger_evvtmenu where evvtmenuid=?', array($topofbranch));
	if ($mnurs && $adb->num_rows($mnurs)>0) {
		$mnu = $adb->fetch_array($mnurs);
		$adb->pquery('update vtiger_evvtmenu set mseq=mseq-1 where mseq>? and mparent=?', array($mnu['mseq'], $mnu['mparent']));
		$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid=?', array($topofbranch));
	}
}

function fixMenuOrder($topofbranch) {
	global $adb;
	$menuorder=1;
	$mnurs = $adb->pquery('select evvtmenuid,mtype from vtiger_evvtmenu where mparent=? order by mseq', array($topofbranch));
	if ($mnurs && $adb->num_rows($mnurs)>0) {
		while ($mnu = $adb->fetch_array($mnurs)) {
			if ($mnu['mtype']=='menu') {
				fixMenuOrder($mnu['evvtmenuid']);
			}
			$adb->pquery('update vtiger_evvtmenu set mseq=? where evvtmenuid=?', array($menuorder, $mnu['evvtmenuid']));
			$menuorder++;
		}
	}
}
function delSavedMenu($savedone) {
	global $adb;
	$selsm = $adb->pquery('select * from vtiger_savemenu where savemenuid=?', array($savedone));
	if ($selsm && $adb->num_rows($selsm)>0) {
		$adb->pquery('delete from vtiger_savemenu where savemenuid=?', array($savedone));
	}
}
$dosaved = isset($_REQUEST['savedmenudo']) ? vtlib_purify($_REQUEST['savedmenudo']) : '';

switch ($dosaved) {
	case 'doSaveCurrent':
		$result = $adb->query('select * from vtiger_evvtmenu');
		$data = array();
		if ($result && $adb->num_rows($result)>0) {
			while ($row = $adb->fetch_array($result)) {
				$data[] = array($row['evvtmenuid'], $row['mtype'], $row['mvalue'], $row['mlabel'], $row['mparent'], $row['mseq'], $row['mvisible'], $row['mpermission']);
			}
			$structuremenu = json_encode($data, JSON_FORCE_OBJECT);
			$menuname = empty($_REQUEST['menuname']) ? 'menu_'.date('YmdHis') : vtlib_purify($_REQUEST['menuname']);
			$adb->pquery(
				'insert into vtiger_savemenu (menuname,structure) values (?,?)',
				array($menuname, $structuremenu)
			);
		}
		break;
	case 'doApplySaved':
		$savemenuid = vtlib_purify($_REQUEST['savemenuid']);
		$menuname = vtlib_purify($_REQUEST['menuname']);
		$menu = $adb->pquery('select structure from vtiger_savemenu where savemenuid=? limit 1', array($savemenuid));
		if ($menu && $adb->num_rows($menu)>0) {
			$structure = $adb->query_result($menu, 0, 'structure');
			$stru = html_entity_decode($structure);
			$menuitems = json_decode($stru, true);
			$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid!=?', array(0));
			foreach ($menuitems as $item) {
				$sq = 'insert into vtiger_evvtmenu (evvtmenuid,mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values ('.generateQuestionMarks($item).')';
				$adb->pquery($sq, $item);
			}
		}
		break;
	case 'doRenameSaved':
		if (!empty($_REQUEST['savemenuid']) && !empty($_REQUEST['menuname'])) {
			$savemenuid = vtlib_purify($_REQUEST['savemenuid']);
			$menuname = vtlib_purify($_REQUEST['menuname']);
			$apply = $adb->pquery(
				'update vtiger_savemenu set menuname=? where savemenuid=?',
				array($menuname, $savemenuid)
			);
		}
		break;
	case 'doImportMenu':
		if (isset($_FILES) && isset($_FILES['jsonfile']) && is_uploaded_file($_FILES['jsonfile']['tmp_name']) && $_FILES['jsonfile']['type']=='application/json') {
			$contents = file_get_contents($_FILES['jsonfile']['tmp_name']);
			$menuname = empty($_REQUEST['menuname']) ? basename($_FILES['jsonfile']['name'], '.json') : vtlib_purify($_REQUEST['menuname']);
			$import = $adb->pquery(
				'insert into vtiger_savemenu (menuname,structure) values (?,?)',
				array($menuname, $contents)
			);
		}
		break;
	case 'doDownloadMenu':
		$savemenuid = vtlib_purify($_REQUEST['savemenuid']);
		if ($savemenuid != '') {
			$resu = $adb->pquery('SELECT menuname,structure FROM vtiger_savemenu WHERE savemenuid=? limit 1', array($savemenuid));
			if ($resu) {
				$menu = $adb->fetch_array($resu);
				$structure = $menu['structure'];
				header('Content-disposition: attachment; filename="'.$menu['menuname'].'.json"');
				header('Content-Type: application/json: charset=utf-8');
				echo html_entity_decode($structure);
				die();
			}
		}
		break;
	case 'doDelSaved':
		$savemenuid = vtlib_purify($_REQUEST['savemenuid']);
		if ($savemenuid != '') {
			delSavedMenu($savemenuid);
		}
		break;
}
$do = vtlib_purify($_REQUEST['evvtmenudo']);

switch ($do) {
	case 'doAdd':
		$mtype = vtlib_purify($_REQUEST['mtype']);
		$mlabel = vtlib_purify($_REQUEST['mlabel']);
		$mvalue = vtlib_purify($_REQUEST['mvalue']);
		$evvtmenuid = vtlib_purify($_REQUEST['evvtmenuid']);
		$mparent = vtlib_purify($_REQUEST['mparent']);
		$mvisible = (isset($_REQUEST['mvisible']) ? 1 : 0);
		$mpermission = isset($_REQUEST['mpermission']) ? vtlib_purify($_REQUEST['mpermission']) : '';
		if (empty($mpermission)) {
			$mpermission = array();
		}
		if ($mparent == 0) {
			$mparent = $evvtmenuid;
		}
		if ($mtype=='module') {
			$mvalue = vtlib_purify($_REQUEST['modname']);
		}
		$pmenuidrs = $adb->pquery('select max(mseq) from vtiger_evvtmenu where mparent=?', array($mparent));
		$mseq = (int)$adb->query_result($pmenuidrs, 0, 0) + 1;
		$adb->pquery(
			'insert into vtiger_evvtmenu (mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values (?,?,?,?,?,?,?)',
			array($mtype, $mvalue, $mlabel, $mparent, $mseq, $mvisible, implode(',', $mpermission))
		);
		break;
	case 'doUpd':
		$evvtmenuid = vtlib_purify($_REQUEST['evvtmenuid']);
		if (is_numeric($evvtmenuid)) {
			$mtype = vtlib_purify($_REQUEST['mtype']);
			$mparent = (isset($_REQUEST['mparent']) ? vtlib_purify($_REQUEST['mparent']) : 0);
			$mlabel = vtlib_purify($_REQUEST['mlabel']);
			$mvalue = vtlib_purify($_REQUEST['mvalue']);
			$mvisible = (isset($_REQUEST['mvisible']) ? 1 : 0);
			if ($mtype=='module') {
				$mvalue = vtlib_purify($_REQUEST['modname']);
			}
			$mpermission = isset($_REQUEST['mpermission']) ? implode(',', vtlib_purify($_REQUEST['mpermission'])) : '';
			$updrs = $adb->pquery(
				'update vtiger_evvtmenu set mtype=?,mvalue=?,mlabel=?, mparent=?,mpermission=?,mvisible=? where evvtmenuid=?',
				array($mtype, $mvalue, $mlabel, $mparent, $mpermission, $mvisible, $evvtmenuid)
			);
		}
		break;
	case 'doDel':
		$evvtmenuid = vtlib_purify($_REQUEST['evvtmenuid']);
		if (is_numeric($evvtmenuid)) {
			delMenuBranch($evvtmenuid);
		}
		break;
	case 'fixOrphaned':
		$rsmenu = $adb->query("select evvtmenuid from vtiger_evvtmenu where mtype='menu'");
		$menus = array();
		while ($m = $adb->fetch_array($rsmenu)) {
			$menus[] = $m['evvtmenuid'];
		}
		$adb->query('update vtiger_evvtmenu set mparent=0 where mparent not in ('.implode(',', $menus).')');
		break;
	case 'fixOrder':
		fixMenuOrder(0);
		break;
	case 'updateTree':
		$treeIds = vtlib_purify($_REQUEST['treeIds']);
		$treeParents = vtlib_purify($_REQUEST['treeParents']);
		$treePositions = vtlib_purify($_REQUEST['treePositions']);
		$ids = explode(',', $treeIds);
		$parents = explode(',', $treeParents);
		$positions = explode(',', $treePositions);
		for ($i=0; $i<count($positions); $i++) {
			$id = $ids[$i];
			$parent = $parents[$i];
			$position = $positions[$i];
			$rs = $adb->pquery('select mseq from vtiger_evvtmenu WHERE evvtmenuid=?', array($id));
			$currentseq = $adb->query_result($rs, 0, 0);
			if ($currentseq<$position) {
				$adb->pquery('update vtiger_evvtmenu set mseq = mseq - 1 WHERE mparent=? AND mseq <= ? AND evvtmenuid <> ?', array($parent, $position, $id));
			}
			$adb->pquery('update vtiger_evvtmenu set mparent=?, mseq=? WHERE evvtmenuid=?', array($parent, $position, $id));
			if ($currentseq>$position) {
				$adb->pquery('update vtiger_evvtmenu set mseq = mseq + 1 WHERE mparent=? AND mseq >= ? AND evvtmenuid <> ?', array($parent, $position, $id));
			}
		}
		break;
}
header('Location:index.php?action=index&module=evvtMenu');
?>
