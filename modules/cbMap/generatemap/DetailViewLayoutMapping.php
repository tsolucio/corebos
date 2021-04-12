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

class genDetailViewLayoutMapping extends generatecbMap {

	public function generateMap() {
		global $log;
		$Map = $this->getMap();
		$mapcontent = '';
		include 'modules/cbMap/generatemap/GenMapHeader.php';
		$module = $Map->column_fields['targetname'];
		$mapcontent = $Map->column_fields['content'];
		$mapcontent = simplexml_load_string($mapcontent);

		$jsonmapcontent = json_encode($mapcontent);
		$mapcontentArr = json_decode($jsonmapcontent, true);
		$type = isset($mapcontentArr['blocks']['block']['type']) ? $mapcontentArr['blocks']['block']['type']: '';
		$smarty->assign('type', $type);
		if ($type == 'Widget' || $type == 'CodeWithHeader' || $type == 'CodeWithoutHeader') {
			$details = $mapcontentArr['blocks']['block'];
			$log->fatal('The case '.$details);
			!empty($details['loadfrom']) ? $smarty->assign('loadfrom', $details['loadfrom']) : $smarty->assign('loadfrom', '');
			!empty($details['loadcode']) ? $smarty->assign('loadcode', $details['loadcode']) : $smarty->assign('loadcode', '');
			!empty($details['handler_paths']) ? $smarty->assign('handler_paths', $details['handler_paths']) : $smarty->assign('handler_paths', '');
			!empty($details['handler_path']) ? $smarty->assign('handler_path', $details['handler_path']) : $smarty->assign('handler_path', '');
			!empty($details['handler_class']) ? $smarty->assign('handler_class', $details['handler_class']) : $smarty->assign('handler_class', '');
			!empty($details['handler']) ? $smarty->assign('handler', $details['handler']) : $smarty->assign('handler', '');
		}
		if ($type == 'RelatedList') {
			$details = $mapcontentArr['blocks']['block'];
			$smarty->assign('relloadfrom', $details['loadfrom']);
		}
		$smarty->assign('MODULES', getPicklistValuesSpecialUitypes('1613', '', $module));
		$smarty->assign('targetmodule', $module);
		$smarty->assign('MapID', $Map->id);
		$smarty->assign('MapFields', $Map->column_fields);
		$smarty->assign('NameOFMap', $Map->column_fields['mapname']);
		$smarty->display('modules/cbMap/DetailViewLayout.tpl');
		$smarty->display('modules/cbMap/GenMapFooter.tpl');
	}

	public function convertToMap() {
		global $adb, $log;
		$Map = $this->getMap();
		$module = $Map->column_fields['targetname'];
		if ($module!=$_REQUEST['tmodule']) {
			$adb->pquery('update vtiger_cbmap set targetname=? where cbmapid=?', array(vtlib_purify($_REQUEST['tmodule']), $Map->id));
		}
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><map/>');
		$type = urldecode($_REQUEST['type']);
		$content = $_REQUEST['content'];
		$log->fatal($content);
		$contentArr = array_filter(explode('$$', $content));
		$log->fatal($contentArr);
		if ($type == 'ApplicationFields') {
			$blocks = $xml->addChild('blocks');
			for ($x=0; $x < count($contentArr); $x++) {
				if ($contentArr[$x] == 'block') {
					$block = $blocks->addChild('block');
					$block->addChild('type', $type);
					$layout = $block->addChild('layout');
				}
				if (strpos($contentArr[$x], '##') != false) {
					list($key, $val) = explode('##', $contentArr[$x]);
					if (!empty($key) && !empty($val) && $block) {
						$block->addChild($key, $val);
					}
				}
			}
		}
		if ($type == 'FieldList') {
			$blocks = $xml->addChild('blocks');
			$block = null;
			$layout = null;
			$row = null;
			$column = null;
			for ($i=0; $i < count($contentArr); $i++) {
				if ($contentArr[$i] == 'block') {
					$block = $blocks->addChild('block');
					$block->addChild('type', $type);
					$layout = $block->addChild('layout');
				}
				if (strpos($contentArr[$i], '##') != false) {
					list($key, $val) = explode('##', $contentArr[$i]);
					if (!empty($key) && !empty($val) && $block) {
						$block->addChild($key, $val);
					}
				}
				if ($contentArr[$i] == 'row') {
					$row = $layout->addChild('row');
				}
				if (!empty($contentArr[$i-1]) && $contentArr[$i-1] == 'column') {
					$row->addChild('column', $contentArr[$i]);
				}
			}
		}
		if ($type == 'RelatedList') {
			$blocks = $xml->addChild('blocks');
			$block = $blocks->addChild('block');
			$block->addChild('type', $type);
			for ($i=0; $i < count($contentArr); $i++) {
				$log->fatal($contentArr[$i]);
				if (strpos($contentArr[$i], '##') != false) {
					list($key, $val) = explode('##', $contentArr[$i]);
					$log->fatal('Key: '.$key. ' val: '.$val);
					if (!empty($key) && !empty($val) && $block) {
						$block->addChild($key, $val);
					}
				}
			}
		}
		if ($type == 'Widget' || $type == 'CodeWithHeader' || $type == 'CodeWithoutHeader') {
			$blocks = $xml->addChild('blocks');
			$block = $blocks->addChild('block');
			$block->addChild('type', $type);
			for ($x=0; $x < count($contentArr); $x++) {
				list($key, $val) = explode('##', $contentArr[$x]);
				if (!empty($key) && !empty($val)) {
					$block->addChild($key, $val);
				}
			}
		}
		return str_replace('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL, '', $xml->asXML());
	}
}
?>