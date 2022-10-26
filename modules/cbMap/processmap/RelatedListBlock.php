<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Related List Block
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
	<title></title>
	<linkfields>
		<targetfield>asset_no</targetfield>
	</linkfields>
	<modules>
		<module>
			<name>Messages</name>
			<wizard>mapid</wizard>
			<nextstep>0 | 1</nextstep>
			<relatedfield></relatedfield>
			<tooltip>
				<fields></fields>
				...
			</tooltip>
		</module>
		<module>
			<name>Assets</name>
			<listview>
				<fields>
					<field>
						<fieldtype>corebos</fieldtype>
						<fieldname>asset_no</fieldname>
						<editable>1</editable>
						<mandatory>0</mandatory>
						<hidden>0</hidden>
						<sortable>true</sortable>
						<layout></layout>
					</field>
					...
				</fields>
			</listview>
			<editview>
				<fields>
					<field>
						<fieldtype>corebos</fieldtype>
						<fieldname>asset_no</fieldname>
						<editable>1</editable>
						<mandatory>0</mandatory>
						<hidden>0</hidden>
						<sortable>true</sortable>
						<layout></layout>
					</field>
					...
				</fields>
			</editview>
		</module>
	</modules>
</map>
 *************************************************************************************************/
include_once 'include/Webservices/DescribeObject.php';
include_once 'include/ListView/GridUtils.php';

class RelatedListBlock extends processcbMap {

	public $mapping = array();
	public $mapping_arr = array();
	private $fieldsinfo = array();
	private $relatedfieldsinfo = array();
	private $detailModule = '';

	public function processMap($arguments) {
		$this->mapping=$this->convertMap2Array();
		return $this->mapping;
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$this->mapping_arr['title'] = (string)$xml->title;
		$this->mapping_arr['linkfields']['targetfield'] = (string)$xml->linkfields->targetfield;
		foreach ((array)$xml->modules as $module) {
			$idx = 0;
			foreach ($module as $key) {
				$this->mapping_arr['modules'][$idx] = (array)$key;
				if (isset($key->tooltip)) {
					$this->mapping_arr['modules'][$idx]['tooltip'] = (array)$key->tooltip;
				}
				if (isset($key->listview)) {
					$this->detailModule = (string)$key->name;
					$this->mapping_arr['modules'][$idx]['listview'] = $this->FormatFields((array)$key->listview->fields, 'listview');
				}
				if (isset($key->editview)) {
					$this->detailModule = (string)$key->name;
					$this->mapping_arr['modules'][$idx]['editview'] = $this->FormatFields((array)$key->editview->fields, 'editview');
				}
				$idx++;
			}
		}
		return $this->mapping_arr;
	}

	public function FormatFields($fields, $mode) {
		$fldsInfo = array();
		foreach ($fields as $key) {
			foreach ($key as $v) {
				$fieldtype = isset($v->fieldtype) ? (string)$v->fieldtype : '';
				$fieldname = isset($v->fieldname) ? (string)$v->fieldname : '';
				$fieldinfo = array();
				if (!empty($fieldname)) {
					switch (strtolower($fieldtype)) {
						case 'corebos':
							$fieldinfo = $this->getFieldInfo($fieldname);
							break;
						case 'corebos.related':
							$fieldinfo = $this->getRelatedFieldInfo($fieldname);
							break;
						case 'computed':
							$fieldinfo['name'] = $fieldname;
							$fieldinfo['label'] = isset($v->fieldlabel) ? (string)$v->fieldlabel : '';
							$fieldinfo['uitype'] = 'computed';
							break;
					}
				}
				if ($mode == 'editview') {
					$fldsInfo[] = $fieldinfo['fieldid'];
				} else {
					$fldsInfo[] = array(
						'fieldtype' => $fieldtype,
						'fieldinfo' => $fieldinfo,
						'editable' => isset($v->editable) ? (string)$v->editable : '',
						'mandatory' => isset($v->mandatory) ? (string)$v->mandatory : '',
						'hidden' => isset($v->hidden) ? (string)$v->hidden : '0',
						'layout' => isset($v->layout) ? (string)$v->layout : '',
						'sortable' => !empty($v->sortable),
						'sortingType' => isset($v->sortingType) ? (string)$v->sortingType : '',
					);
				}
			}
		}
		return $fldsInfo;
	}

	public function getFieldInfo($fieldname) {
		global $current_user;
		if (!isset($this->fieldsinfo[$this->detailModule])) {
			$wsfieldsinfo = vtws_describe($this->detailModule, $current_user);
			$this->fieldsinfo[$this->detailModule] = $wsfieldsinfo['fields'];
			$tabid = getTabid($this->detailModule);
			foreach ($this->fieldsinfo[$this->detailModule] as $key => $finfo) {
				$this->fieldsinfo[$this->detailModule][$key]['fieldid'] = getFieldid($tabid, $finfo['name']);
				$this->fieldsinfo[$this->detailModule][$key]['columnname'] = getColumnnameByFieldname($tabid, $finfo['name']);
			}
		}
		$ret = array_search($fieldname, array_column($this->fieldsinfo[$this->detailModule], 'name'));
		if (isset($this->fieldsinfo[$this->detailModule][$ret]['uitype']) && $this->fieldsinfo[$this->detailModule][$ret]['uitype']==10) {
			$refmod = $this->fieldsinfo[$this->detailModule][$ret]['type']['refersTo'][0];
			$rmod = CRMEntity::getInstance($refmod);
			$WSCodeID = vtws_getEntityId($refmod);
			$this->fieldsinfo[$this->detailModule][$ret]['searchin'] = $refmod;
			$this->fieldsinfo[$this->detailModule][$ret]['searchby'] = $refmod.$rmod->list_link_field;
			$this->fieldsinfo[$this->detailModule][$ret]['searchwsid'] = $WSCodeID;
		}
		return $this->fieldsinfo[$this->detailModule][$ret];
	}

	public function getRelatedFieldInfo($fieldname) {
		global $current_user;
		list($module,$fieldname) = explode('.', $fieldname);
		if (count($this->relatedfieldsinfo)==0 || !isset($this->relatedfieldsinfo[$module])) {
			$wsfieldsinfo = vtws_describe($module, $current_user);
			$this->relatedfieldsinfo[$module] = $wsfieldsinfo['fields'];
		}
		$ret = array_search($fieldname, array_column($this->fieldsinfo, 'name'));
		if ($this->relatedfieldsinfo[$module][$ret]['uitype']==10) {
			$refmod = $this->relatedfieldsinfo[$module][$ret]['type']['refersTo'][0];
			$rmod = CRMEntity::getInstance($refmod);
			$WSCodeID = vtws_getEntityId($refmod);
			$this->relatedfieldsinfo[$module][$ret]['searchin'] = $refmod;
			$this->relatedfieldsinfo[$module][$ret]['searchby'] = $refmod.$rmod->list_link_field;
			$this->relatedfieldsinfo[$module][$ret]['searchwsid'] = $WSCodeID;
		}
		return $this->relatedfieldsinfo[$module][$ret];
	}
}
?>
