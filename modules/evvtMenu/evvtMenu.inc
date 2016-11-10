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
	if ($menurs and $adb->num_rows($menurs)>0) {
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

//function getMenuBranchHTML2($marray) {
//    $menuTreeHtml = '';
//    for($i=0; $i<count($marray); $i++){
//        $menuItem = $marray[$i];
//        $submenu = $menuItem['submenu'];
//        $menuTreeHtml .= "<li menuinfo=\"".$menuItem['json']."\"><input type=\"checkbox\"  checked=\"checked\" id=\"evvtmcb-".$menuItem['evvtmenuid']."\" /><label for=\"item-0-$i\">". $menuItem['mlabel']."</label>";
//        $menuTreeHtml .= "<ul>";
//        for($j=0; $j<count($submenu); $j++){
//            $subMenuItem = $submenu[$j];
//            $menuTreeHtml .= "<li menuinfo=\"".$menuItem['json']."\"><input type=\"checkbox\" id=\"evvtmcb-".$subMenuItem['evvtmenuid']."\" />". $subMenuItem['mlabel']."</li>";
//        }
//        $menuTreeHtml .= "</ul>";
//        $menuTreeHtml .= "</li>";
//
//    }
//    return $menuTreeHtml;
//}

function getMenuBranchHTML($marray) {
    $menuTreeHtml = '';
    for($i=0; $i<count($marray); $i++){
        $menuItem = $marray[$i];
        $submenu = $menuItem['submenu'];
        $label = $menuItem['mlabel'];
		$type = $menuItem['mtype'];
        $json = $menuItem['jsoninfo'];
        $json = json_encode($json);
        $json = json_decode($json);
        $json = str_replace('"', "'", $json);
        $menuTreeHtml .= "<a onclick=\"getMenuInfo(".$json.");showFormParts('$type');\" id=\"tree-$i\">$label</a>&nbsp;&nbsp;<a onclick=\"expandSection($i);\"<i class=\"fa fa-plus\" aria-hidden=\"true\"></i></a>&nbsp;&nbsp;<a onclick=\"contractSection($i);\"<i class=\"fa fa-minus\" aria-hidden=\"true\"></i></a></br>";
        $menuTreeHtml .= "<div class=\"treeitems\" id=\"trees-$i\">";
        for($j=0; $j<count($submenu); $j++){
            $subMenuItem = $submenu[$j];
            $label = $subMenuItem['mlabel'];
			$type = $subMenuItem['mtype'];
            $json = $subMenuItem['jsoninfo'];
            $json = json_encode($json);
            $json = json_decode($json);
            $json = str_replace('"', "'", $json);
            $id = $subMenuItem['evvtmenuid'];
            $menuTreeHtml .= "<a onclick=\"getMenuInfo(".$json.");showFormParts('$type');\" id=\"tree-$i\">$label</a></br>";
        }
        $menuTreeHtml .= "</div>";
    }
    return $menuTreeHtml;
}


function getMenuArray($mparent) {
	global $adb,$current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	$menustructure = array();
	$menurs = $adb->query("select * from vtiger_evvtmenu where mparent = $mparent and mvisible=1 order by mseq");
	if ($menurs and $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			if (empty($menu['mpermission']) and $menu['mtype']=='module') {
				// apply vtiger CRM permissions
				if (isPermitted($menu['mvalue'],'index')=='no') continue;
			} elseif (!empty($menu['mpermission'])) {
				// apply evvtMenu permissions
				$usrprf = getUserProfile($current_user->id);
				$mperm = explode(',',$menu['mpermission']);
				if (!$is_admin and count(array_intersect($usrprf,$mperm))==0) continue;
			}
			switch ($menu['mtype']) {
				case 'menu':
				case 'url':
					$label = getTranslatedString($menu['mlabel'],'evvtMenu');
					break;
				case 'separator':
					$label = '';
					break;
				case 'module':
					$label = getTranslatedString($menu['mvalue'],$menu['mvalue']);
					break;
			}
			$menustructure[] = array(
				'evvtmenuid' => $menu['evvtmenuid'],
				'mparent' => $menu['mparent'],
				'mtype' => $menu['mtype'],
				'mvalue' => $menu['mvalue'],
				'mlabel' => $label,
				'mseq' => $menu['mseq'],
				'mvisible' => $menu['mvisible'],
				'submenu' => getMenuArray($menu['evvtmenuid']),
			);
		}
	}
	return $menustructure;
}

function getMenuHTML($marray) {
	$menubranch = '';
	foreach ($marray as $item) {
		$menubranch.= '<li>'.$item['mlabel'];
		if (!empty($item['submenu']) and count($item['submenu'])>0) {
			$menubranch.= '<ul>';
			$menubranch.= getMenuHTML($item['submenu']);
			$menubranch.= '</ul>';
		}
		$menubranch.= '</li>';
	}
	return $menubranch;
}

function getMenuJSON($marray) {
	$menubranch = '[';
	foreach ($marray as $item) {
		switch ($item['mtype']) {
			case 'menu':
				$menubranch.= '{text:"'.decode_html($item['mlabel']).'"';
				break;
			case 'module':
				$menubranch.= '{text:"'.decode_html($item['mlabel']).'",';
				$menubranch.= 'url:"index.php?action=index&module='.$item['mvalue'].'"';
				break;
			case 'url':
				$menubranch.= '{text:"'.decode_html($item['mlabel']).'",';
				$menubranch.= 'url:"'.$item['mvalue'].'"';
				break;
			case 'sep':
				$menubranch.= '{text:"<hr/>",encoded:false';
				break;
		}
		if (!empty($item['submenu']) and count($item['submenu'])>0) {
			$menubranch.= ',items:';
			$menubranch.= getMenuJSON($item['submenu']);
		}
		$menubranch.= '},';
	}
	return rtrim($menubranch,',').']';
}

function getAdminevvtMenu() {
	global $adb;
	$rdo = array();
	$menurs = $adb->query("select * from vtiger_evvtmenu where mparent = (select evvtmenuid from vtiger_evvtmenu where mlabel ='Settings') and mvisible=1 order by mseq");
	if ($menurs and $adb->num_rows($menurs)>0) {
		while ($menu = $adb->fetch_array($menurs)) {
			switch ($menu['mtype']) {
				case 'url':
					$label = getTranslatedString($menu['mlabel'],'evvtMenu');
					$url = $menu['mvalue'];
					break;
				case 'separator':
				case 'menu':
					continue;
					break;
				case 'module':
					$label = getTranslatedString($menu['mvalue'],$menu['mvalue']);
					$url = 'index.php?action=index&module='.$menu['mvalue'];
					break;
			}
			$rdo[$label] = $url;
		}
	} else {
		$rdo[getTranslatedString('Settings','Settings')] = 'index.php?module=Settings&action=index';
	}
	return $rdo;
}
?>
