<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Master Detail Layout
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
  <originmodule>DesignQuotes</originmodule>
  <targetmodule>DesignQuotesLines</targetmodule>
  <linkfields>
  <originfield>designquotesid</originfield>
  <targetfield>designquotesid</targetfield>
  <condition>querycondition</condition>
  </linkfields>
  <sortfield>designquotesid</sortfield>
  <toolbar>
	<title></title>
	<expandall>1</expandall>
	<create>1</create>
  </toolbar>
  <listview>
	<toolbar>
	  <moveup>1</moveup>
	  <movedown>1</movedown>
	  <delete>1</delete>
	</toolbar>
	<fields>
	  <field>
		<fieldtype>corebos</fieldtype>
		<fieldname>fname</fieldname>
		<editable>1</editable>
		<mandatory>1</mandatory>
		<hidden>1</hidden>
		<layout></layout>
	  </field>
	  <field>
		<fieldtype>computed</fieldtype>
		<fieldname>put your operation here</fieldname>
		<fieldlabel></fieldlabel>
		<editable>1</editable>
		<mandatory>1</mandatory>
		<hidden>1</hidden>
	  </field>
	</fields>
  </listview>
  <detailview>
   <layout></layout>
	<fields>
	  <field>
		<fieldtype>corebos</fieldtype>
		<fieldname>fname</fieldname>
		<value>xxx</value>
		<editable>1</editable>
		<mandatory>1</mandatory>
		<defaultvalue>11</defaultvalue>
		<duplicatevalue>22</duplicatevalue>
		<hidden>1</hidden>
		<layout></layout>
	  </field>
	</fields>
  </detailview>
  <aggregations>
	  <operation>
		<type>aggregation | calculation</type>
		<items>callresponse</items>
		<operation>sum</operation>
		<column>linetotal</column>
		<variable>dqlinestotal</variable>
		<label>Subtotal</label>
		<currency>true | false</currency>
		<position>top</position>
	  </operation>
  </aggregations>
</map>
 *************************************************************************************************/
include_once 'include/Webservices/DescribeObject.php';
include_once 'include/ListView/GridUtils.php';

class MasterDetailLayout extends processcbMap {

	public $mapping = array();
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
		$mapping_arr=array();
		$mapping_arr['mapnameraw'] = $this->getMap()->column_fields['mapname'];
		$mapping_arr['mapname'] = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $mapping_arr['mapnameraw'])); // Removes special chars.
		$mapping_arr['originmodule'] = (string)$xml->originmodule;
		$mapping_arr['targetmodule'] = (string)$xml->targetmodule;
		$mapping_arr['condition'] = (string)$xml->condition;
		$this->detailModule = $mapping_arr['targetmodule'];
		$dmf = CRMEntity::getInstance($this->detailModule);
		$mapping_arr['targetmoduleidfield'] = $dmf->table_index;
		$mapping_arr['linkfields'] = array(
			'originfield' => (string)$xml->linkfields->originfield,
			'targetfield' => (string)$xml->linkfields->targetfield,
		);
		$mapping_arr['sortfield'] = (string)$xml->sortfield;
		$mapping_arr['toolbar'] = array(
			'title' => (string)$xml->toolbar->title,
			'icon' => (string)$xml->toolbar->icon,
			'expandall' => (string)$xml->toolbar->expandall,
			'create' => (string)$xml->toolbar->create,
		);
		$mapping_arr['listview'] = array();
		if (isset($xml->listview->datasource)) {
			$dsrc = (string)$xml->listview->datasource;
			if (strtolower($dsrc)=='corebos') {
				$mapping_arr['listview']['datasource'] = 'index.php?module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=list&mdmap='
					.urlencode($mapping_arr['mapnameraw']);
			} else {
				$mapping_arr['listview']['datasource'] = $dsrc;
			}
		}
		if (isset($xml->listview->toolbar)) {
			$mapping_arr['listview']['toolbar'] = array(
				'moveup' => isset($xml->listview->toolbar->moveup) ? (string)$xml->listview->toolbar->moveup : '1',
				'movedown' => isset($xml->listview->toolbar->movedown) ? (string)$xml->listview->toolbar->movedown : '1',
				'edit' => isset($xml->listview->toolbar->edit) ? (string)$xml->listview->toolbar->edit : '1',
				'delete' => isset($xml->listview->toolbar->delete) ? (string)$xml->listview->toolbar->delete : '1',
			);
		}
		$mapping_arr['listview']['fields'] = array();
		if (isset($xml->listview->fields->field) && is_object($xml->listview->fields->field)) {
			foreach ($xml->listview->fields->field as $v) {
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
				$mapping_arr['listview']['fields'][] = array(
					'fieldtype' => $fieldtype,
					'fieldinfo' => $fieldinfo,
					'editable' => isset($v->editable) ? (string)$v->editable : '',
					'mandatory' => isset($v->mandatory) ? (string)$v->mandatory : '',
					'hidden' => isset($v->hidden) ? (string)$v->hidden : '0',
					'layout' => isset($v->layout) ? (string)$v->layout : '',
					'editor' => !empty($v->editable) ? json_encode(gridGetEditor($mapping_arr['targetmodule'], $fieldinfo['name'], $fieldinfo['uitype'])) : '',
					'sortable' => !empty($v->sortable),
					'sortingType' => isset($v->sortingType) ? (string)$v->sortingType : '',
				);
				$mapping_arr['listview']['fieldnames'][] = $fieldinfo['name'];
			}
		}
		$mapping_arr['viewfields'] = array();
		$mapping_arr['viewfieldnames'] = array();
		$mapping_arr['editfields'] = array();
		$mapping_arr['editfieldnames'] = array();
		$mapping_arr['detailview'] = array();
		$mapping_arr['detailview']['layout'] = isset($xml->detailview->layout) ? (string)$xml->detailview->layout : '';
		$mapping_arr['detailview']['fields'] = array();
		if (is_object($xml->detailview->fields->field)) {
			foreach ($xml->detailview->fields->field as $v) {
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
				$editable = isset($v->editable) ? (string)$v->editable : '';
				$mapping_arr['detailview']['fields'][] = array(
					'fieldtype' => $fieldtype,
					'fieldinfo' => $fieldinfo,
					'editable' => $editable,
					'mandatory' => isset($v->mandatory) ? (string)$v->mandatory : '',
					'hidden' => isset($v->hidden) ? (string)$v->hidden : '0',
					'value' => isset($v->value) ? (string)$v->value : '',
					'defaultvalue' => isset($v->defaultvalue) ? (string)$v->defaultvalue : null,
					'duplicatevalue' => isset($v->duplicatevalue) ? (string)$v->duplicatevalue : null,
					'layout' => isset($v->layout) ? (string)$v->layout : '',
				);
				$mapping_arr['detailview']['fieldnames'][] = $fieldinfo['name'];
				if (!empty($editable) || $fieldinfo['mandatory']) {
					$mapping_arr['editfields'][] = $fieldinfo['fieldid'];
					$mapping_arr['editfieldnames'][] = $fieldinfo['name'];
				}
				$mapping_arr['viewfields'][] = $fieldinfo['fieldid'];
				$mapping_arr['viewfieldnames'][] = $fieldinfo['name'];
			}
		}
		foreach ($this->fieldsinfo as $finfo) {
			if ($finfo['mandatory']) {
				$mapping_arr['editfields'][] = $finfo['fieldid'];
				$mapping_arr['editfieldnames'][] = $finfo['name'];
			}
		}
		$mapping_arr['aggregations'] = array();
		if (is_object($xml->aggregations->operation)) {
			foreach ($xml->aggregations->operation as $v) {
				$mapping_arr['aggregations'][] = array(
					'type' => isset($v->type) ? (string)$v->type : '',
					'items' => isset($v->items) ? (string)$v->items : '',
					'operation' => isset($v->operation) ? (string)$v->operation : '',
					'column' => isset($v->column) ? (string)$v->column : '',
					'variable' => isset($v->variable) ? (string)$v->variable : '0',
					'label' => isset($v->label) ? (string)$v->label : '',
					'currency' => isset($v->currency) ? (strtolower((string)$v->currency)=='true' ? 1 : 0) : 0,
					'position' => isset($v->position) ? (string)$v->position : '',
				);
			}
		}
		return $mapping_arr;
	}

	public function getFieldInfo($fieldname) {
		global $current_user;
		if (count($this->fieldsinfo)==0) {
			$wsfieldsinfo = vtws_describe($this->detailModule, $current_user);
			$this->fieldsinfo = $wsfieldsinfo['fields'];
			$tabid = getTabid($this->detailModule);
			foreach ($this->fieldsinfo as $key => $finfo) {
				$this->fieldsinfo[$key]['fieldid'] = getFieldid($tabid, $finfo['name']);
				$this->fieldsinfo[$key]['columnname'] = getColumnnameByFieldname($tabid, $finfo['name']);
			}
		}
		$ret = array_search($fieldname, array_column($this->fieldsinfo, 'name'));
		if (isset($this->fieldsinfo[$ret]['uitype']) && $this->fieldsinfo[$ret]['uitype']==10) {
			$refmod = $this->fieldsinfo[$ret]['type']['refersTo'][0];
			$rmod = CRMEntity::getInstance($refmod);
			$WSCodeID = vtws_getEntityId($refmod);
			$this->fieldsinfo[$ret]['searchin'] = $refmod;
			$this->fieldsinfo[$ret]['searchby'] = $refmod.$rmod->list_link_field;
			$this->fieldsinfo[$ret]['searchwsid'] = $WSCodeID;
		}
		return $this->fieldsinfo[$ret];
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

	// creating > empty($associated_prod) && $isduplicate != 'true'
	public static function setCreateAsociatedProductsValue($module, &$smarty) {
		$cbMap = cbMap::getMapByName($module.'InventoryDetails', 'MasterDetailLayout');
		$smarty->assign('moreinfofields', '');
		$product_Detail = array();
		if ($cbMap!=null && isPermitted('InventoryDetails', 'EditView')=='yes') {
			$cbMapFields = $cbMap->MasterDetailLayout();
			$smarty->assign('moreinfofields', "'".implode("','", $cbMapFields['detailview']['fieldnames'])."'");
			$col_fields = array();
			foreach ($cbMapFields['detailview']['fields'] as $mdfield) {
				if ($mdfield['fieldinfo']['name']=='id') {
					continue;
				}
				if (is_null($mdfield['defaultvalue'])) {
					$col_fields[$mdfield['fieldinfo']['name']] = '';
				} else {
					$col_fields[$mdfield['fieldinfo']['name']] = $mdfield['defaultvalue'];
				}
				$foutput = getOutputHtml(
					$mdfield['fieldinfo']['uitype'],
					$mdfield['fieldinfo']['name'],
					$mdfield['fieldinfo']['label'],
					100,
					$col_fields,
					0,
					'InventoryDetails',
					'edit',
					$mdfield['fieldinfo']['typeofdata']
				);
				$product_Detail['moreinfo'][] = $foutput;
			}
		}
		return $product_Detail;
	}
}
?>
