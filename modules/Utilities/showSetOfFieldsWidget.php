<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************/

// BA block://showSetOfFieldsWidget:modules/Utilities/showSetOfFieldsWidget.php:record_id=$RECORD$&mapid=XXXXXX

require_once 'include/utils/utils.php';
require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbMap/cbMap.php';
global $currentModule;

class showSetOfFieldsWidget {
	public static function getWidget($name) {
		return (new showSetOfFields_DetailViewBlock());
	}
}

class showSetOfFields_DetailViewBlock extends DeveloperBlock {
	private $widgetName = 'showSetOfFieldsWidget';
	public function process($context = false) {
		global $adb, $log;
		$this->context = $context;
		$recordid = $this->getFromContext('RECORDID');
		$cbmapid = $this->getFromContext('mapid');
		$mapres = cbMap::getMapByID($cbmapid);
		if ($mapres) {
			$xmlcontent = $mapres->column_fields['contentjson'];
			$decodedcontent = html_entity_decode($xmlcontent);
			$decodedcontent = json_decode($decodedcontent, true);
			$layoutdataArr = array();
			$type =  isset($decodedcontent['blocks']['block']['type']) ? $decodedcontent['blocks']['block']['type'] : '';
			$layoutdataArr['type'] = $type;
			if (!empty($type)) {
				$layoutdataArr['fields'] = array();
				if ($type == 'ApplicationFields') {
					$x = 0;
					foreach ($decodedcontent as $block) {
						$blockid = isset($block['block']['blockid']) ? $block['block']['blockid']: '';
						if (!empty($blockid)) {
							$blocklabel = getBlockName($blockid);
							$layoutdataArr['fields'][$x] = array(
								'Block ID' => $blockid,
								'Label' => isset($block['block']['label']) ? $block['block']['label']: $blocklabel,
								'Sequence' => isset($block['block']['sequence']) ? $block['block']['sequence']: $sequence
							);
							$x++;
						}
					}
				} elseif ($type == 'FieldList') {
					foreach ($decodedcontent as $block) {
						$rowdata = $block['block']['layout']['row'];
						$totalRows  = count($rowdata);
						if ($totalRows == 1) {
							$layoutdataArr['fields'][0] = $rowdata['column'];
						} elseif ($totalRows > 1) {
							for ($x= 0; $x < $totalRows; $x++) {
								$layoutdataArr['fields'][$x] = $rowdata[$x]['column'];
							}
						}
					}
				} elseif ($type == 'RelatedList') {
					$layoutdataArr['fields'] = !empty($decodedcontent['blocks']['block']) ? $decodedcontent['blocks']['block']: array();
				} elseif ($type == 'Widget' || $type == 'CodeWithHeader' || $type == 'CodeWithoutHeader') {
					$layoutdataArr['fields'] = !empty($decodedcontent['blocks']['block']) ? $decodedcontent['blocks']['block']: array();
				}
			}
		}
		if (isset($layoutdataArr['fields']['type'])) {
			unset($layoutdataArr['fields']['type']);
		}
		$module = $this->getFromContext('MODULE');
		$smarty = $this->getViewer();
		$smarty->assign('MODULE', $module);
		$smarty->assign('LAYOUT_DATA', $layoutdataArr);
		return $smarty->fetch('showSetOfFields.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$setfield = new showSetOfFields_DetailViewBlock();
	echo $setfield->process($_REQUEST);
}