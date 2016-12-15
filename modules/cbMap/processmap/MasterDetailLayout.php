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
include 'include/Webservices/DescribeObject.php';

class MasterDetailLayout extends processcbMap {

	public $mapping = array();
	private $fieldsinfo = array();
	private $relatedfieldsinfo = array();
	private $detailModule = '';

	function processMap($arguments) {
		global $adb, $current_user;
		$this->mapping=$this->convertMap2Array();
		return $this->mapping;
	}

	function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=array();
		$mapping['originmodule'] = (String)$xml->originmodule;
		$mapping['targetmodule'] = (String)$xml->targetmodule;
		$this->detailModule = $mapping['targetmodule'];
		$mapping['linkfields'] = array(
			'originfield' => (String)$xml->linkfields->originfield,
			'targetfield' => (String)$xml->linkfields->targetfield,
		);
		$mapping['sortfield'] = (String)$xml->sortfield;
		$mapping['toolbar'] = array(
			'title' => (String)$xml->toolbar->title,
			'expandall' => (String)$xml->toolbar->expandall,
			'create' => (String)$xml->toolbar->create,
		);
		$mapping['listview'] = array();
		$mapping['listview']['toolbar'] = array(
			'moveup' => (String)$xml->listview->toolbar->moveup,
			'movedown' => (String)$xml->listview->toolbar->movedown,
			'delete' => (String)$xml->listview->toolbar->delete,
		);
		$mapping['listview']['fields'] = array();
		if (is_object($xml->listview->fields->field))
		foreach($xml->listview->fields->field as $k=>$v) {
			$fieldtype = isset($v->fieldtype) ? (String)$v->fieldtype : '';
			$fieldname = isset($v->fieldname) ? (String)$v->fieldname : '';
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
						$fieldinfo['label'] = isset($v->fieldlabel) ? (String)$v->fieldlabel : '';
						$fieldinfo['uitype'] = 'computed';
						break;
				}
			}
			$mapping['listview']['fields'][] = array(
				'fieldtype' => $fieldtype,
				'fieldinfo' => $fieldinfo,
				'editable' => isset($v->editable) ? (String)$v->editable : '',
				'mandatory' => isset($v->mandatory) ? (String)$v->mandatory : '',
				'hidden' => isset($v->hidden) ? (String)$v->hidden : '0',
				'layout' => isset($v->layout) ? (String)$v->layout : '',
			);
			$mapping['listview']['fieldnames'][] = $fieldinfo['name'];
		}
		$mapping['detailview'] = array();
		$mapping['detailview']['layout'] = isset($xml->detailview->layout) ? (String)$xml->detailview->layout : '';
		$mapping['detailview']['fields'] = array();
		if (is_object($xml->detailview->fields->field))
		foreach($xml->detailview->fields->field as $k=>$v) {
			$fieldtype = isset($v->fieldtype) ? (String)$v->fieldtype : '';
			$fieldname = isset($v->fieldname) ? (String)$v->fieldname : '';
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
						$fieldinfo['label'] = isset($v->fieldlabel) ? (String)$v->fieldlabel : '';
						$fieldinfo['uitype'] = 'computed';
						break;
				}
			}
			$mapping['detailview']['fields'][] = array(
				'fieldtype' => $fieldtype,
				'fieldinfo' => $fieldinfo,
				'editable' => isset($v->editable) ? (String)$v->editable : '',
				'mandatory' => isset($v->mandatory) ? (String)$v->mandatory : '',
				'hidden' => isset($v->hidden) ? (String)$v->hidden : '0',
				'value' => isset($v->value) ? (String)$v->value : '',
				'layout' => isset($v->layout) ? (String)$v->layout : '',
			);
			$mapping['detailview']['fieldnames'][] = $fieldinfo['name'];
		}
		$mapping['aggregations'] = array();
		if (is_object($xml->aggregations->operation))
		foreach($xml->aggregations->operation as $k=>$v) {
			$mapping['aggregations'][] = array(
				'type' => isset($v->type) ? (String)$v->type : '',
				'items' => isset($v->items) ? (String)$v->items : '',
				'operation' => isset($v->operation) ? (String)$v->operation : '',
				'column' => isset($v->column) ? (String)$v->column : '',
				'variable' => isset($v->variable) ? (String)$v->variable : '0',
				'label' => isset($v->label) ? (String)$v->label : '',
				'currency' => isset($v->currency) ? (strtolower((String)$v->currency)=='true' ? 1 : 0) : 0,
				'position' => isset($v->position) ? (String)$v->position : '',
			);
		}
		return $mapping;
	}

	function getFieldInfo($fieldname) {
		global $current_user;
		if (count($this->fieldsinfo)==0) {
			$wsfieldsinfo = vtws_describe($this->detailModule, $current_user);
			$this->fieldsinfo = $wsfieldsinfo['fields'];
		}
		// PHP 5.5 search and get fieldinfo
		//$ret = array_search($fieldname, array_column($this->fieldsinfo, 'name'));
		// PHP 5.4 search and get fieldinfo
		foreach ($this->fieldsinfo as $ret => $finfo) {
			if ($finfo['name']==$fieldname) break;
		}
		if ($this->fieldsinfo[$ret]['uitype']==10) {
			$refmod = $this->fieldsinfo[$ret]['type']['refersTo'][0];
			$rmod = CRMEntity::getInstance($refmod);
			$WSCodeID = vtws_getEntityId($refmod);
			$this->fieldsinfo[$ret]['searchin'] = $refmod;
			$this->fieldsinfo[$ret]['searchby'] = $refmod.$rmod->list_link_field;
			$this->fieldsinfo[$ret]['searchwsid'] = $WSCodeID;
		}
		return $this->fieldsinfo[$ret];
	}

	function getRelatedFieldInfo($fieldname) {
		global $current_user;
		list($module,$fieldname) = explode('.', $fieldname);
		if (count($this->relatedfieldsinfo)==0 or !isset($this->relatedfieldsinfo[$module])) {
			$wsfieldsinfo = vtws_describe($module, $current_user);
			$this->relatedfieldsinfo[$module] = $wsfieldsinfo['fields'];
		}
		// PHP 5.5 search and get fieldinfo
		//$ret = array_search($fieldname, array_column($this->fieldsinfo, 'name'));
		// PHP 5.4 search and get fieldinfo
		foreach ($this->relatedfieldsinfo[$module] as $ret => $finfo) {
			if ($finfo['name']==$fieldname) break;
		}
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