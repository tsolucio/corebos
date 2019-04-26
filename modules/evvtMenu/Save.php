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
	$mnurs = $adb->pquery('select evvtmenuid,mtype from vtiger_evvtmenu where mparent=?', array($topofbranch));
	if ($mnurs && $adb->num_rows($mnurs)>0) {
		while ($mnu = $adb->fetch_array($mnurs)) {
			if ($mnu['mtype']=='menu') {
				delMenuBranch($mnu['evvtmenuid']);
			}
			$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid=?', array($mnu['evvtmenuid']));
		}
	}
	$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid=?', array($topofbranch));
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
		$mseq = $adb->query_result($pmenuidrs, 0, 0) + 1;
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
	case 'updateTree':
		$treeIds = vtlib_purify($_REQUEST['treeIds']);
		$treeParents = vtlib_purify($_REQUEST['treeParents']);
		$treePositions = vtlib_purify($_REQUEST['treePositions']);
		$ids = explode(",", $treeIds);
		$parents = explode(",", $treeParents);
		$positions = explode(",", $treePositions);
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
$parenttab = getParentTab();
header("Location: index.php?action=index&module=evvtMenu&parenttab=$parenttab");
?>
