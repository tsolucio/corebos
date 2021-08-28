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
 *  Module       : Business Mappings:: Kanban View Mapping
 *  Version      : 1.0
 *************************************************************************************************
 * The accepted format is:
<map>
	<module>Module name</module>
	<lanefield>Module field name</lanefield>
	<showsearch>0|1, optional</showsearch>
	<showfilter>0|1, optional</showfilter>
	<applyfilter>filter name, optional</applyfilter>
	<pagesize>number of records per lane, optional, by default Application_ListView_PageSize</pagesize>
	<lanes>
		<lane>
			<name>value of module field name</name>
			<sequence></sequence>
			<image>
			<library>LDS library name</library>
			<icon>LDS icon name</icon>
			</image>
			<color>CSS color definition</color>
		</lane>
	</lanes>
	<card>
		<title>Module field name</title>
		<showfields>
			<field>Module field name</field>
			...
		</showfields>
		<morefields>
			<field>Module field name</field>
			...
		</morefields>
	</card>
</map>
 *************************************************************************************************/

class Kanban extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml) || empty($xml->module) || empty($xml->lanefield)) {
			return array();
		}
		$this->mapping['module'] = (string)$xml->module;
		$this->mapping['lanefield'] = (string)$xml->lanefield;
		$this->mapping['showsearch'] = isset($xml->showsearch) ? (int)$xml->showsearch : 1;
		$this->mapping['showfilter'] = isset($xml->showfilter) ? (int)$xml->showfilter : 1;
		$customView = new CustomView($this->mapping['module']);
		if (!empty($xml->applyfilter)) {
			$this->mapping['applyfilter'] = $customView->getViewIdByName((string)$xml->applyfilter, $this->mapping['module']);
		}
		if (empty($this->mapping['applyfilter'])) {
			$this->mapping['applyfilter'] = $customView->getViewId($this->mapping['module']);
		}
		if (empty($xml->pagesize)) {
			$this->mapping['pagesize'] = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $this->mapping['module']);
		} else {
			$this->mapping['pagesize'] = (int)$xml->pagesize;
		}
		list($this->mapping['lanes'], $this->mapping['lanenames']) = $this->getLanes($xml);
		$this->mapping['cards'] = $this->getCards($xml);
		return $this->mapping;
	}

	private function getLanes($xml) {
		global $adb, $current_user;
		$qg = new QueryGenerator($this->mapping['module'], $current_user);
		$qg->setFields([$this->mapping['lanefield']]);
		$sql = $qg->getQuery(true);
		$sql .= ' ORDER BY '.$qg->getOrderByColumn($this->mapping['lanefield']). ' asc';
		$rs = $adb->query($sql);
		$dblanes = array();
		if ($rs && $adb->num_rows($rs)>0) {
			foreach ($adb->rowGenerator($rs) as $lanevalue) {
				if (!empty($lanevalue[$this->mapping['lanefield']])) {
					$dblanes[$lanevalue[$this->mapping['lanefield']]] = $lanevalue[$this->mapping['lanefield']];
				}
			}
		}
		$xmllanes = array();
		if (!empty($xml->lanes)) {
			foreach ($xml->lanes->lane as $v) {
				if (empty($v->name)) {
					continue;
				}
				$xmllane = array(
					'name' => (string)$v->name,
					'id' => uniqid(strtolower(str_replace(' ', '', (string)$v->name))),
					'sequence' => empty($v->sequence) ? -1 : (int)$v->sequence,
				);
				if (!empty($v->image) && !empty($v->image->library) && !empty($v->image->icon)) {
					$xmllane['image'] = array(
						'library' => (string)$v->image->library,
						'icon' => (string)$v->image->icon,
					);
				}
				if (!empty($v->color)) {
					$xmllane['color'] = (string)$v->color;
				}
				$xmllanes[] = $xmllane;
			}
			if (!empty($xmllanes)) {
				$keys = array_column($xmllanes, 'sequence');
				array_multisort($keys, SORT_ASC, $xmllanes);
			}
		}
		$lanes = $lanenames = array();
		$notsortedbutinfo = array();
		foreach ($xmllanes as $value) {
			if ($value['sequence']==-1) {
				$notsortedbutinfo[$value['name']] = $value;
				continue;
			}
			$lanes[$value['name']] = $value;
			unset($dblanes[$value['name']]);
			$lanenames[] = $value['name'];
		}
		foreach ($dblanes as $fvalue) {
			if (isset($notsortedbutinfo[$fvalue])) {
				$lanes[$fvalue] = $notsortedbutinfo[$fvalue];
			} else {
				$lanes[$fvalue] = array(
					'name' => $fvalue,
					'id' => uniqid(strtolower(str_replace(' ', '', $fvalue))),
				);
				$lanenames[] = $fvalue;
			}
		}
		return [$lanes, $lanenames];
	}

	private function getCards($xml) {
		$modinst = Vtiger_Module::getInstance($this->mapping['module']);
		$modcrm = CRMEntity::getInstance($this->mapping['module']);
		$cards = array();
		if (empty($xml->card)) {
				$cards['title'] = $modcrm->list_link_field;
				$cards['showfields'] = $this->getListColumns($this->mapping['module'], $modcrm);
		} else {
			if (!empty($xml->card->title) && Vtiger_Field::getInstance((string)$xml->card->title, $modinst)) {
				$cards['title'] = (string)$xml->card->title;
			} else {
				$cards['title'] = $modcrm->list_link_field;
			}
			if (empty($xml->card->showfields)) {
				$cards['showfields'] = $this->getListColumns($this->mapping['module'], $modcrm);
			} else {
				$cards['showfields'] = array();
				foreach ($xml->card->showfields->field as $f) {
					if (!empty($f) && Vtiger_Field::getInstance((string)$f, $modinst)) {
						$cards['showfields'][] = (string)$f;
					}
				}
			}
			if (!empty($xml->card->morefields)) {
				$cards['morefields'] = array();
				foreach ($xml->card->morefields->field as $f) {
					if (!empty($f) && Vtiger_Field::getInstance((string)$f, $modinst)) {
						$cards['morefields'][] = (string)$f;
					}
				}
			}
		}
		return $cards;
	}

	private function getListColumns($module, $modcrm) {
		$lc = $modcrm->list_fields_name;
		$bmapname = $module.'_ListColumns';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $module);
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$cbMapLC = $cbMap->ListColumns();
			$lc = $cbMapLC->getListFieldsNameFor($module);
		}
		return $lc;
	}
}
?>
