<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc';

class genDecisionTable extends generatecbMap {

	public function generateMap() {
		global $adb;
		$Map = $this->getMap();
		include 'modules/cbMap/generatemap/GenMapHeader.php';
		$xml = $this->getXMLContent();
		$hitpolicy = isset($xml->hitpolicy) ? $xml->hitpolicy : '';
		$aggregate = isset($xml->aggregate) ? $xml->aggregate : '';
		$emgr = new VTExpressionsManager($adb);
		$smarty->assign('FNDEFS', json_encode($emgr->expressionFunctionDetails()));
		$smarty->assign('FNCATS', $emgr->expressionFunctionCategories());
		$module = $Map->column_fields['targetname'];
		$smarty->assign('targetmodule', $module);
		$smarty->assign('MODULES', getPicklistValuesSpecialUitypes('1613', '', $module));
		$smarty->assign('hitpolicy', $hitpolicy);
		$smarty->assign('aggregate', $aggregate);
		$smarty->assign('maptype', 'DecisionTable');
		$smarty->assign('mapcontent', json_encode($xml));
		$smarty->assign('MapID', $Map->id);
		$smarty->assign('MapFields', $Map->column_fields);
		$smarty->assign('NameOFMap', $Map->column_fields['mapname']);
		$smarty->display('modules/cbMap/DecisionMap.tpl');
		$smarty->display('modules/cbMap/GenMapFooter.tpl');
	}

	public function convertToMap() {
		global $adb;
		$Map = $this->getMap();
		$module = $Map->column_fields['targetname'];
		if ($module!=$_REQUEST['tmodule']) {
			$adb->pquery('update vtiger_cbmap set targetname=? where cbmapid=?', array(vtlib_purify($_REQUEST['tmodule']), $Map->id));
		}
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><deletethis/>');
		$map = json_decode(urldecode($_REQUEST['content']), true);
		$decision = $xml->addChild('decision');
		$decision->addChild('hitPolicy', $map['hitPolicy']);
		if ($map['hitPolicy']=='G') {
			$decision->addChild('aggregate', $map['aggregate']);
		} else {
			$decision->addChild('aggregate');
		}
		$rules = $decision->addChild('rules');
		foreach ($map['rules'] as $rule) {
			$r = $rules->addChild('rule');
			$r->addChild('sequence', $rule['sequence']);
			if (!empty($rule['expression'])) {
				$exp = $r->addChild('expression');
				$node = dom_import_simplexml($exp);
				$node->appendChild($node->ownerDocument->createCDATASection($rule['expression']));
			} elseif (!empty($rule['mapid'])) {
				$r->addChild('mapid', $rule['mapid']);
			} elseif (!empty($rule['decisionTable'])) {
				$dt = $r->addChild('decisionTable');
				$dtr = $rule['decisionTable'];
				$dt->addChild('module', $dtr['module']);
				if (!empty($dtr['conditions'])) {
					$dtcs = $dt->addChild('conditions');
					foreach ($dtr['conditions']['condition'] as $cond => $condValue) {
						$dtc = $dtcs->addChild('condition');
						$dtc->addChild('input', $condValue['input']);
						$dtc->addChild('operation', $condValue['operation']);
						$dtc->addChild('field', $condValue['field']);
					}
				}
				if (empty($dtr['orderby'])) {
					$dt->addChild('orderby');
				} else {
					$dt->addChild('orderby', $dtr['orderby']);
				}
				if (!empty($dtr['searches'])) {
					$dtss = $dt->addChild('searches');
					foreach ($dtr['searches']['search'] as $searchValue) {
						$dts = $dtss->addChild('search');
						foreach ($searchValue as $cond) {
							$dtc = $dts->addChild('condition');
							$dtc->addChild('input', $cond['input']);
							if (!empty($cond['preprocess'])) {
								$dtc->addChild('preprocess', $cond['preprocess']);
							}
							$dtc->addChild('operation', $cond['operation']);
							$dtc->addChild('field', $cond['field']);
						}
					}
				}
				$dt->addChild('output', $dtr['output']);
			}
			$r->addChild('output', $rule['output']);
		}
		$dom = dom_import_simplexml($xml)->ownerDocument;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		return str_replace(
			array(
				'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<deletethis>',
				'</deletethis>',
			),
			'',
			$dom->saveXML()
		);
	}
}
?>