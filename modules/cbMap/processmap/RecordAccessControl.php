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
 *  Module       : Business Mappings:: Record Access Control
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <originmodule>
    <originid>22</originid>  {optional}
    <originname>SalesOrder</originname>
  </originmodule>
  <listview>
  <c>1</c>  Add button
  <r>1</r>  View record
  <u>1</u>  Edit action
  <d>1</d>  Delete action
  </listview>
  <detailview>
  <c>1</c>  Duplicate button
  <r>1</r>  View record
  <u>1</u>  Edit action
  <d>1</d>  Delete action
  </detailview>
  <relatedlists>
    <relatedlist>
      <modulename>Invoice</modulename>
      <c>1</c>  Add button
      <r>1</r>  View list
      <u>1</u>  Edit action
      <d>1</d>  Delete action
      <s>1</s>  Select button
    </relatedlist>
    .....
  </relatedlists>
  </map>
 *************************************************************************************************/

class RecordAccessControl extends processcbMap {
	private $mapping = array();
	private $modulename = '';
	private $moduleid = 0;

	function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		$this->modulename = (String)$xml->originmodule->originname;
		$this->moduleid = (isset($xml->originmodule->originid) ? (String)$xml->originmodule->originid : 0);
		$this->mapping['listview']['c'] = (isset($xml->listview->c) ? (Integer)$xml->listview->c : 1);
		$this->mapping['listview']['r'] = (isset($xml->listview->r) ? (Integer)$xml->listview->r : 1);
		$this->mapping['listview']['u'] = (isset($xml->listview->u) ? (Integer)$xml->listview->u : 1);
		$this->mapping['listview']['d'] = (isset($xml->listview->d) ? (Integer)$xml->listview->d : 1);
		$this->mapping['detailview']['c'] = (isset($xml->detailview->c) ? (Integer)$xml->detailview->c : 1);
		$this->mapping['detailview']['r'] = (isset($xml->detailview->r) ? (Integer)$xml->detailview->r : 1);
		$this->mapping['detailview']['u'] = (isset($xml->detailview->u) ? (Integer)$xml->detailview->u : 1);
		$this->mapping['detailview']['d'] = (isset($xml->detailview->d) ? (Integer)$xml->detailview->d : 1);
		if (isset($xml->relatedlists)) {
			foreach($xml->relatedlists->relatedlist as $k=>$v) {
				$modulename = (String)$v->modulename;
				$this->mapping['relatedlist'][$modulename]['c'] = (isset($v->c) ? (Integer)$v->c : 1);
				$this->mapping['relatedlist'][$modulename]['r'] = (isset($v->r) ? (Integer)$v->r : 1);
				$this->mapping['relatedlist'][$modulename]['u'] = (isset($v->u) ? (Integer)$v->u : 1);
				$this->mapping['relatedlist'][$modulename]['d'] = (isset($v->d) ? (Integer)$v->d : 1);
				$this->mapping['relatedlist'][$modulename]['s'] = (isset($v->s) ? (Integer)$v->s : 1);
			}
		}
	}

	/**
	* param $operation: create | retrieve | update | delete
	*/
	public function hasListViewPermissionTo($operation) {
		if (count($this->mapping)==0) $this->convertMap2Array();
		if (!isset($this->mapping['listview'])) return true;
		switch (strtolower($operation)) {
			case 'create':
				return (isset($this->mapping['listview']['c']) ? $this->mapping['listview']['c'] : true);
				break;
			case 'retrieve':
				return (isset($this->mapping['listview']['r']) ? $this->mapping['listview']['r'] : true);
				break;
			case 'update':
			case 'edit':
			case 'editview':
				return (isset($this->mapping['listview']['u']) ? $this->mapping['listview']['u'] : true);
				break;
			case 'delete':
				return (isset($this->mapping['listview']['d']) ? $this->mapping['listview']['d'] : true);
				break;
			default:
				return true;
				break;
		}
	}

	/**
	* param $operation: create | retrieve | update | delete
	*/
	public function hasDetailViewPermissionTo($operation) {
		if (count($this->mapping)==0) $this->convertMap2Array();
		if (!isset($this->mapping['detailview'])) return true;
		switch (strtolower($operation)) {
			case 'create':
				return (isset($this->mapping['detailview']['c']) ? $this->mapping['detailview']['c'] : true);
				break;
			case 'retrieve':
				return (isset($this->mapping['detailview']['r']) ? $this->mapping['detailview']['r'] : true);
				break;
			case 'update':
			case 'edit':
			case 'editview':
				return (isset($this->mapping['detailview']['u']) ? $this->mapping['detailview']['u'] : true);
				break;
			case 'delete':
				return (isset($this->mapping['detailview']['d']) ? $this->mapping['detailview']['d'] : true);
				break;
			default:
				return true;
				break;
		}
	}

	/**
	* param $operation: create | retrieve | update | delete | select
	*/
	public function hasRelatedListPermissionTo($operation,$onmodule) {
		if (empty($onmodule)) return true;
		if (count($this->mapping)==0) $this->convertMap2Array();
		if (!isset($this->mapping['relatedlist']) or !isset($this->mapping['relatedlist'][$onmodule])) return true;
		switch (strtolower($operation)) {
			case 'create':
				return (isset($this->mapping['relatedlist'][$onmodule]['c']) ? $this->mapping['relatedlist'][$onmodule]['c'] : true);
				break;
			case 'retrieve':
				return (isset($this->mapping['relatedlist'][$onmodule]['r']) ? $this->mapping['relatedlist'][$onmodule]['r'] : true);
				break;
			case 'update':
				return (isset($this->mapping['relatedlist'][$onmodule]['u']) ? $this->mapping['relatedlist'][$onmodule]['u'] : true);
				break;
			case 'delete':
				return (isset($this->mapping['relatedlist'][$onmodule]['d']) ? $this->mapping['relatedlist'][$onmodule]['d'] : true);
				break;
			case 'select':
				return (isset($this->mapping['relatedlist'][$onmodule]['s']) ? $this->mapping['relatedlist'][$onmodule]['s'] : true);
				break;
			default:
				return true;
				break;
		}
	}

}
?>
