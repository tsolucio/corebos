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

class evvtMenu {

 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
 			$menus = $adb->query('SELECT vtiger_parenttab.parenttabid,vtiger_parenttab.parenttab_label,visible,vtiger_parenttabrel.tabid,vtiger_tab.name 
				FROM vtiger_parenttabrel
				inner join vtiger_parenttab on vtiger_parenttab.parenttabid=vtiger_parenttabrel.parenttabid
				inner join vtiger_tab on vtiger_tab.tabid=vtiger_parenttabrel.tabid
				order by vtiger_parenttab.parenttabid,vtiger_parenttab.sequence,vtiger_parenttabrel.sequence');
 			$mainmenucnt=0;
 			$submenucnt=1;
 			while ($menu = $adb->fetch_array($menus)) {
 				if ($mainmenucnt==0 or $mainmenucnt!=$menu['parenttabid']) {
 					$mainmenucnt++;
 					$adb->query("insert into vtiger_evvtmenu (mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values
 							('menu','','".$menu['parenttab_label']."',0,$mainmenucnt,1,'')");
 					$pmenuidrs = $adb->query('select max(evvtmenuid) from vtiger_evvtmenu');
 					$pmenuid = $adb->query_result($pmenuidrs,0,0);
 					$submenucnt=1;
 				}
 				$adb->query("insert into vtiger_evvtmenu (mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values
 							('module','".$menu['name']."','".$menu['name']."',$pmenuid,$submenucnt,1,'')");
 				$submenucnt++;
 			}
		} else if($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
		} else if($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}
}
?>
