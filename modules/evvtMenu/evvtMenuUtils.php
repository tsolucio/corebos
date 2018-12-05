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

function getMenuBranch($mparent) {
	global $adb;
	$menustructure = array();
	$menurs = $adb->query("select * from vtiger_evvtmenu where mparent = $mparent order by mseq");
	if ($menurs && $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			switch ($menu['mtype']) {
				case 'menu':
				case 'separator':
					$icon = 'folder';
					break;
				default:
					$icon = 'html';
			}
			$menustructure[] = array(
				'evvtmenuid' => $menu['evvtmenuid'],
				'mparent' =>$menu['mparent'],
				'mtype' => $menu['mtype'],
				'micon' => $icon,
				'mvalue' => $menu['mvalue'],
				'mlabel' => (empty($menu['mlabel']) ? '----':$menu['mlabel']),
				'mseq' => $menu['mseq'],
				'mvisible' => $menu['mvisible'],
				'mpermission' => $menu['mpermission'],
				'submenu' => getMenuBranch($menu['evvtmenuid']),
				'jsoninfo' => json_encode(array(
					'evvtmenuid' => $menu['evvtmenuid'],
					'mparent' =>$menu['mparent'],
					'mtype' => $menu['mtype'],
					'mvalue' => $menu['mvalue'],
					'mlabel' => $menu['mlabel'],
					'mvisible' => $menu['mvisible'],
					'mpermission' => $menu['mpermission'],
				)),
			);
		}
	}
	return $menustructure;
}

/**
 * This function creates JSON of all evvtMenu elements with
 * the id, parent id and label as main elements, and also the info element that contains
 * the full info of each item
 *
 * @return string
 */
function getMenuJSON2() {
	global $adb, $default_charset;
	$menustructure = array(array('id'=>0, 'parent'=>'#', 'text' => 'Menu'));
	$menurs = $adb->query('SELECT * FROM `vtiger_evvtmenu` ORDER BY mseq ASC');
	if ($menurs && $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetchByAssoc($menurs)) {
			$info = array(
				'evvtmenuid' => $menu['evvtmenuid'],
				'mparent' => $menu['mparent'],
				'mtype' => $menu['mtype'],
				'mvalue' => html_entity_decode($menu['mvalue'], ENT_QUOTES, $default_charset),
				'mlabel' => html_entity_decode($menu['mlabel'], ENT_QUOTES, $default_charset),
				'mvisible' => $menu['mvisible'],
				'mpermission' => $menu['mpermission'],
				'mseq' => $menu['mseq'],
			);
			$info = json_encode($info);
			$label = i18nMenuLabel($menu['mlabel'], $menu['mvalue'], $menu['mtype']);
			$menustructure[] = array(
				'id' => $menu['evvtmenuid'],
				'parent' => $menu['mparent'],
				'text' => $label,
				'li_attr' => array('class'=>'jstree-drop',),
				'a_attr' => array('onclick' => 'getMenuInfo('.$info.');', 'mseq' => $menu['mseq'])
			);
		}
	}
	return json_encode($menustructure);
}

function getMenuElements() {
	global $adb;
	$menustructure = array();
	$menurs = $adb->query('SELECT `evvtmenuid`, `mlabel` FROM `vtiger_evvtmenu`');
	if ($menurs && $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			$menustructure[$menu['evvtmenuid']] = $menu['mlabel'];
		}
	}
	return $menustructure;
}

function getMenuArray($mparent) {
	global $adb,$current_user;
	$is_admin = is_admin($current_user);
	$menustructure = array();
	$menurs = $adb->query("select * from vtiger_evvtmenu where mparent = $mparent and mvisible=1 order by mseq");
	if ($menurs && $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			if (empty($menu['mpermission']) && $menu['mtype']=='module') {
				// apply vtiger CRM permissions
				if (isPermitted($menu['mvalue'], 'index')=='no') {
					continue;
				}
			} elseif (!empty($menu['mpermission'])) {
				// apply evvtMenu permissions
				$usrprf = getUserProfile($current_user->id);
				$mperm = explode(',', $menu['mpermission']);
				if (!$is_admin && count(array_intersect($usrprf, $mperm))==0) {
					continue;
				}
			}
			$label = i18nMenuLabel($menu['mlabel'], $menu['mvalue'], $menu['mtype']);
			$submenu = getMenuArray($menu['evvtmenuid']);
			if ($menu['mparent']>0 || count($submenu)>0 || $menu['mtype']=='module' || $menu['mtype']=='url') {
				$menustructure[] = array(
					'evvtmenuid' => $menu['evvtmenuid'],
					'mparent' => $menu['mparent'],
					'mtype' => $menu['mtype'],
					'mvalue' => $menu['mvalue'],
					'mlabel' => $label,
					'mseq' => $menu['mseq'],
					'mvisible' => $menu['mvisible'],
					'submenu' => $submenu,
				);
			}
		}
	}
	return $menustructure;
}

function getMenuHTML($marray) {
	$menubranch = '';
	foreach ($marray as $item) {
		$menubranch.= '<li>'.$item['mlabel'];
		if (!empty($item['submenu']) && count($item['submenu'])>0) {
			$menubranch.= '<ul>';
			$menubranch.= getMenuHTML($item['submenu']);
			$menubranch.= '</ul>';
		}
		$menubranch.= '</li>';
	}
	return $menubranch;
}

function getMenuPicklist($mparent, $level) {
	global $adb;
	$menustructure = array();
	$menurs = $adb->pquery('SELECT evvtmenuid, mlabel, mtype, mvalue FROM vtiger_evvtmenu where mparent=? order by mseq', array($mparent));
	if ($menurs && $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			$label = i18nMenuLabel($menu['mlabel'], $menu['mvalue'], $menu['mtype']);
			$menustructure[$menu['evvtmenuid']] = ($level ? str_repeat('>', $level) : '').' '.$label;
			$menustructure = $menustructure + getMenuPicklist($menu['evvtmenuid'], $level+1);
		}
	}
	return $menustructure;
}

function getAdminevvtMenu() {
	global $adb;
	$rdo = array();
	$menurs = $adb->query("select * from vtiger_evvtmenu where mparent = (select evvtmenuid from vtiger_evvtmenu where mlabel ='Settings') and mvisible=1 order by mseq");
	if ($menurs && $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			switch ($menu['mtype']) {
				case 'url':
					$label = getTranslatedString($menu['mlabel'], 'evvtMenu');
					$url = $menu['mvalue'];
					break;
				case 'separator':
				case 'menu':
					continue;
					break;
				case 'module':
					$label = getTranslatedString($menu['mvalue'], $menu['mvalue']);
					$url = 'index.php?action=index&module='.$menu['mvalue'];
					break;
			}
			$rdo[$label] = $url;
		}
	} else {
		$rdo[getTranslatedString('Settings', 'Settings')] = 'index.php?module=Settings&action=index';
	}
	return $rdo;
}

function checkevvtMenuInstalled() {
	global $adb, $current_user;
	if (vtlib_isModuleActive('cbupdater')) {
		// first we make sure we have Global Variable
		if (!vtlib_isModuleActive('GlobalVariable')) {
			$holduser = $current_user;
			ob_start();
			include 'modules/cbupdater/getupdatescli.php';
			$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installglobalvars'");
			$updid = $adb->query_result($rsup, 0, 0);
			$argv[0] = 'doworkcli';
			$argv[1] = 'apply';
			$argv[2] = $updid;
			include 'modules/cbupdater/doworkcli.php';
			vtlib_toggleModuleAccess('GlobalVariable', true); // in case changeset is applied but module deactivated
			ob_end_clean();
			$current_user = $holduser;
		}
		if (!vtlib_isModuleActive('evvtMenu')) {
			$holduser = $current_user;
			ob_start();
			include 'modules/cbupdater/getupdatescli.php';
			$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='ldMenu'");
			$updid = $adb->query_result($rsup, 0, 0);
			$argv[0] = 'doworkcli';
			$argv[1] = 'apply';
			$argv[2] = $updid;
			include 'modules/cbupdater/doworkcli.php';
			vtlib_toggleModuleAccess('evvtMenu', true); // in case changeset is applied but module deactivated
			ob_end_clean();
			$current_user = $holduser;
		}
	}
}

function i18nMenuLabel($mlabel, $mvalue, $type) {
	switch ($type) {
		case 'url':
		case 'menu':
		case 'headtop':
		case 'headbottom':
			$i18nlabel = getTranslatedString($mlabel, 'evvtMenu');
			break;
		case 'module':
			$i18nlabel = getTranslatedString($mvalue, $mvalue);
			break;
		default:
			$i18nlabel = $mlabel;
			break;
	}
	return $i18nlabel;
}

checkevvtMenuInstalled();
?>
