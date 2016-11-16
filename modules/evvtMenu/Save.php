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
	$mnurs = $adb->query('select evvtmenuid,mtype from vtiger_evvtmenu where mparent='.$topbranch);
	if ($mnurs and $adb->num_rows($mnurs)>0)
	while ($mnu = $adb->fetch_array($mnurs)) {
		if ($mnu['mtype']=='menu') {
			delMenuBranch($mnu['evvtmenuid']);
		}
		$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid=?',array($mnu['evvtmenuid']));
	}
	$adb->pquery('delete from vtiger_evvtmenu where evvtmenuid=?',array($topofbranch));
}

function saveMenuBranch($topofbranch,$mparent) {
	global $adb;
	$gmvisible = 0;
	if (!empty($topofbranch) and is_array($topofbranch)) {
		foreach ($topofbranch as $mitem) {
			$mvisible = $mitem['mvisible'];
			$mvis = saveMenuBranch($mitem['items'],$mitem['mid']);
			$mvisible = ($mvisible+$mvis>0 ? 1 : 0);
			$gmvisible = ($gmvisible+$mvisible>0 ? 1 : 0);
			$adb->pquery('update vtiger_evvtmenu
				set mseq=?,mvisible=?,mparent=? where evvtmenuid=?',
				array($mitem['mseq'],$mvisible,$mparent,$mitem['mid']));
		}
	}
	return $gmvisible;
}

$do = vtlib_purify($_REQUEST['evvtmenudo']);

switch ($do) {
	case 'doSave':
		$emt = str_replace('\\','',$_REQUEST['evvtmenutree']);
		$emt = str_replace('items":["','items":[',$emt);
		$emt = str_replace('}","{','},{',$emt);
		$emt = str_replace('}"]','}]',$emt);
		$menustruct = json_decode("[$emt]",true);
		saveMenuBranch($menustruct,0);
		break;
	case 'doAdd':
		$mtype = vtlib_purify($_REQUEST['mtype']);
		$mlabel = vtlib_purify($_REQUEST['mlabel']);
		$mvalue = vtlib_purify($_REQUEST['mvalue']);
		$evvtmenuid = vtlib_purify($_REQUEST['evvtmenuid']);
		$mparent = vtlib_purify($_REQUEST['mparent']);
		$mpermission = vtlib_purify($_REQUEST['mpermission']);
		if (empty($mpermission)) $mpermission = array();
		if($mtype == 'menu') {
            $pmenuidrs = $adb->query('select max(mseq) from vtiger_evvtmenu where mparent= 0');
            $mseq = $adb->query_result($pmenuidrs, 0, 0) + 1;
            $adb->pquery('insert into vtiger_evvtmenu
				(mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values (?,?,?,?,?,?,?)',
                array($mtype, $mvalue, $mlabel, 0, $mseq, 1, implode(',', $mpermission)));
        } else {
            if($mparent == 0) $mparent = $evvtmenuid;
            if($mtype=='module') $mvalue = vtlib_purify($_REQUEST['modname']);
            $pmenuidrs = $adb->query('select max(mseq) from vtiger_evvtmenu where mparent='.$mparent);
            $mseq = $adb->query_result($pmenuidrs,0,0) + 1;
            $adb->pquery('insert into vtiger_evvtmenu
				(mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values (?,?,?,?,?,?,?)',
                array($mtype,$mvalue,$mlabel,$mparent,$mseq,1,implode(',',$mpermission)));
        }

		break;
	case 'doUpd':
		$evvtmenuid = vtlib_purify($_REQUEST['evvtmenuid']);
		if (is_numeric($evvtmenuid)) {
			$mtype = vtlib_purify($_REQUEST['mtype']);
			$mlabel = vtlib_purify($_REQUEST['mlabel']);
			$mvalue = vtlib_purify($_REQUEST['mvalue']);
			if ($mtype=='module') {
				$mvalue = vtlib_purify($_REQUEST['modname']);
			}
			$mpermission = vtlib_purify($_REQUEST['mpermission']);
			$adb->pquery('update vtiger_evvtmenu
				set mtype=?,mvalue=?,mlabel=?,mpermission=? where evvtmenuid=?',
				array($mtype,$mvalue,$mlabel,implode(',',$mpermission),$evvtmenuid));
		}
		break;
	case 'doDel':
		$evvtmenuid = vtlib_purify($_REQUEST['evvtmenuid']);
		if (is_numeric($evvtmenuid)) {
			delMenuBranch($evvtmenuid);
		}
		break;
}
$parenttab = getParentTab();
header("Location: index.php?action=index&module=evvtMenu&parenttab=$parenttab");
?>
