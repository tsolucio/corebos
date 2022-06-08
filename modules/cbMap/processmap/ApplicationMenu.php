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
 *************************************************************************************************
 *  Module       : Business Mappings:: Application Menu Mapping
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
	 <menuname>
		my_useful_menu
	 </menuname>
 </map>
 *************************************************************************************************/

class ApplicationMenu extends processcbMap {

	public function processMap($arguments) {
		global $adb;
		$structure = json_encode(array());
		$xml = $this->getXMLContent();
		if (empty($xml) || empty($xml->menuname)) {
			return $structure;
		}
		$mname = trim((string)$xml->menuname);
		if (strtolower($mname)=='get_name_from_menuname_parameter' && !empty($arguments[0]['menuname'])) {
			$mname = $arguments[0]['menuname'];
		}
		$resu = $adb->pquery(
			'SELECT structure FROM vtiger_savemenu WHERE menuname=? or savemenuid=? limit 1',
			array($mname, $mname)
		);
		if ($resu && $adb->num_rows($resu)>0) {
			$menu = json_decode(decode_html($adb->query_result($resu, 0, 'structure')), true);
			foreach ($menu as $key => $menuentry) {
				if ($menuentry[1]=='module' && vtlib_isModuleActive($menuentry[2])) {
					try {
						$mod = CRMEntity::getInstance($menuentry[2]);
						if (!empty($mod->moduleIcon)) {
							$menu[$key][]=$mod->moduleIcon;
						}
					} catch (\Throwable $th) {
						continue;
					}
				}
			}
			$structure = json_encode($menu);
		}
		return $structure;
	}
}
?>