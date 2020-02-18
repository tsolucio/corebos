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

class genConditionExpression extends generatecbMap {

	public function generateMap() {
		$Map = $this->getMap();
		include 'modules/cbMap/generatemap/GenMapHeader.php';
		$xml = $this->getXMLContent();
		$mapcontent = $fname = '';
		$fparams = array();
		if (isset($xml->expression)) {
			$maptype='expression';
			$mapcontent = $xml->expression;
		} elseif (isset($xml->function)) {
			$maptype='function';
			$fname = $xml->function->name;
			foreach ($xml->function->parameters->parameter as $prm) {
				$fparams[] = $prm;
			}
		} elseif (isset($xml->template)) {
			$maptype='template';
			$mapcontent = $xml->template;
		} else {
			$maptype='expression';
			$mapcontent = '';
		}
		$module = $Map->column_fields['targetname'];
		$smarty->assign('MODULES', getPicklistValuesSpecialUitypes('1613', '', $module));
		$smarty->assign('targetmodule', $module);
		$smarty->assign('maptype', $maptype);
		$smarty->assign('mapcontent', $mapcontent);
		$smarty->assign('fname', $fname);
		$smarty->assign('fparams', $fparams);
		$smarty->assign('MapID', $Map->id);
		$smarty->assign('MapFields', $Map->column_fields);
		$smarty->assign('NameOFMap', $Map->column_fields['mapname']);
		$smarty->display('modules/cbMap/ConditionExpression.tpl');
		$smarty->display('modules/cbMap/GenMapFooter.tpl');
	}

	public function convertToMap() {
		global $adb;
		$Map = $this->getMap();
		$module = $Map->column_fields['targetname'];
		if ($module!=$_REQUEST['tmodule']) {
			$adb->pquery('update vtiger_cbmap set targetname=? where cbmapid=?', array(vtlib_purify($_REQUEST['tmodule']), $Map->id));
		}
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><map/>');
		$content=urldecode($_REQUEST['content']);
		if ($_REQUEST['mtype']=='template') {
			$m = $xml->addChild('template', $content);
		} elseif ($_REQUEST['mtype']=='function') {
			$m = $xml->addChild('function');
			$m->addChild('name', vtlib_purify($_REQUEST['fname']));
			$p = $m->addChild('parameters');
			$params=explode(',', urldecode($_REQUEST['params']));
			foreach ($params as $param) {
				$p->addChild('parameter', $param);
			}
		} else {
			$m = $xml->addChild('expression', $content);
		}
		return str_replace('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL, '', $xml->asXML());
	}
}
?>