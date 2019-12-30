<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'Smarty_setup.php';
require_once 'modules/PickList/PickListUtils.php';

class genDuplicateRelations extends generatecbMap {

	public function generateMap() {
		$Map = $this->getMap();
		include 'modules/cbMap/generatemap/GenMapHeader.php';
		$xml = $this->getXMLContent();
		$relmods = array();
		$DuplicateDirectRelations = (isset($xml->DuplicateDirectRelations) && strtolower($xml->DuplicateDirectRelations)=='true');
		if (isset($xml->relatedmodules)) {
			foreach ($xml->relatedmodules->relatedmodule as $relm) {
				$relmods[] = array(
					(string)$relm->module,
					(string)$relm->relation,
				);
			}
		}
		$module = $Map->column_fields['targetname'];
		$smarty->assign('MODULES', getPicklistValuesSpecialUitypes('1613', '', $module));
		$smarty->assign('targetmodule', $module);
		$smarty->assign('DuplicateDirectRelations', $DuplicateDirectRelations);
		$smarty->assign('RelatedModules', $relmods);
		$smarty->assign('MapID', $Map->id);
		$smarty->assign('MapFields', $Map->column_fields);
		$smarty->assign('NameOFMap', $Map->column_fields['mapname']);
		$smarty->display('modules/cbMap/DuplicateRelations.tpl');
		$smarty->display('modules/cbMap/GenMapFooter.tpl');
	}

	public function convertToMap() {
		global $adb;
		$Map = $this->getMap();
		$module = $Map->column_fields['targetname'];
		if ($module!=$_REQUEST['tmodule']) {
			$module = vtlib_purify($_REQUEST['tmodule']);
			$adb->pquery('update vtiger_cbmap set targetname=? where cbmapid=?', array($module, $Map->id));
		}
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><map/>');
		$m = $xml->addChild('originmodule');
		$m->addChild('originname', $module);
		if (!empty($_REQUEST['relmods'])) {
			$r = $xml->addChild('relatedmodules');
			$rels = explode(',', urldecode($_REQUEST['relmods']));
			foreach ($rels as $rel) {
				$rl = explode('|', $rel);
				$rlm = $r->addChild('relatedmodule');
				$rlm->addChild('module', $rl[0]);
				$rlm->addChild('relation', $rl[1]);
			}
		}
		$xml->addChild('DuplicateDirectRelations', empty($_REQUEST['DuplicateDirectRelations']) ? 'false' : 'true');
		return str_replace('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL, '', $xml->asXML());
	}
}
?>