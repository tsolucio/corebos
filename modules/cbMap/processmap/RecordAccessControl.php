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
  <c>1</c>  Does not apply. Not used
  <r>1</r>  View record
  <u>1</u>  Edit action
  <d>1</d>  Delete action
  {conditiongroup}  Optional
  </listview>
  <detailview>
  <c>1</c>  Does not apply. Not used
  <r>1</r>  View record
  <u>1</u>  Edit action
  <d>1</d>  Delete action
  {conditiongroup}  Optional
  </detailview>
  <relatedlists>
	<relatedlist>
	  <modulename>Invoice</modulename>
	  <c>1</c>  Add button
	  <r>1</r>  View list
	  <u>1</u>  Edit action
	  <d>1</d>  Delete action
	  <s>1</s>  Select button
	  {conditiongroup}  Optional
	</relatedlist>
	.....
  </relatedlists>
  </map>

 where {conditiongroup} is

 <condition>
   <businessrule>{cbMapID}</businessrule>
   <c>1</c>  Add button
   <r>1</r>  View list
   <u>1</u>  Edit action
   <d>1</d>  Delete action
   <s>1</s>  Select button
  </condition>

 the business rule must be of type ConditionQuery or ConditionExpression and return
  - a number bigger than zero
  - a boolean true
  - the string 'true'
  - the string 'yes'
  * Any other value will be false.
 the SQL will be executed with only one parameter which is the CRMID of the record launching the RAC,
 the Expression will be executed against the record  launching the RAC,
 and the CRUDS settings contained inside the <condition> will override the default settings if the condition is accepted
 *************************************************************************************************/
require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';

class RecordAccessControl extends processcbMap {
	private $mapping = array();
	private $modulename = '';
	private $moduleid = 0;
	private $relatedid = 0;

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function setRelatedRecordID($id) {
		$this->relatedid = $id;
	}

	public function getRelatedRecordID($id) {
		return $this->relatedid;
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$this->modulename = (string)$xml->originmodule->originname;
		$this->moduleid = (isset($xml->originmodule->originid) ? (string)$xml->originmodule->originid : 0);
		if (isset($xml->listview->c)) {
			$this->mapping['listview']['c'] = (integer)$xml->listview->c;
		}
		if (isset($xml->listview->r)) {
			$this->mapping['listview']['r'] = (integer)$xml->listview->r;
		}
		if (isset($xml->listview->u)) {
			$this->mapping['listview']['u'] = (integer)$xml->listview->u;
		}
		if (isset($xml->listview->d)) {
			$this->mapping['listview']['d'] = (integer)$xml->listview->d;
		}
		if (isset($xml->listview->condition)) {
			$this->mapping['listview']['condition'] = $this->convertConditionMap2Array($xml->listview->condition);
		} else {
			$this->mapping['listview']['condition'] = array();
		}
		if (isset($xml->detailview->c)) {
			$this->mapping['detailview']['c'] = (integer)$xml->detailview->c;
		}
		if (isset($xml->detailview->r)) {
			$this->mapping['detailview']['r'] = (integer)$xml->detailview->r;
		}
		if (isset($xml->detailview->u)) {
			$this->mapping['detailview']['u'] = (integer)$xml->detailview->u;
		}
		if (isset($xml->detailview->d)) {
			$this->mapping['detailview']['d'] = (integer)$xml->detailview->d;
		}
		if (isset($xml->detailview->condition)) {
			$this->mapping['detailview']['condition'] = $this->convertConditionMap2Array($xml->detailview->condition);
		} else {
			$this->mapping['detailview']['condition'] = array();
		}
		if (isset($xml->relatedlists)) {
			foreach ($xml->relatedlists->relatedlist as $v) {
				$module_name = (string)$v->modulename;
				if (isset($v->c)) {
					$this->mapping['relatedlist'][$module_name]['c'] = (integer)$v->c;
				}
				if (isset($v->r)) {
					$this->mapping['relatedlist'][$module_name]['r'] = (integer)$v->r;
				}
				if (isset($v->u)) {
					$this->mapping['relatedlist'][$module_name]['u'] = (integer)$v->u;
				}
				if (isset($v->d)) {
					$this->mapping['relatedlist'][$module_name]['d'] = (integer)$v->d;
				}
				if (isset($v->s)) {
					$this->mapping['relatedlist'][$module_name]['s'] = (integer)$v->s;
				}
				if (isset($v->condition)) {
					$this->mapping['relatedlist'][$module_name]['condition'] = $this->convertConditionMap2Array($v->condition);
				} else {
					$this->mapping['relatedlist'][$module_name]['condition'] = array();
				}
			}
		}
	}

	private function convertConditionMap2Array($condition) {
		$cmap = array();
		if (!empty($condition->businessrule)) {
			if (isset($condition->c)) {
				$cmap['c'] = (integer)$condition->c;
			}
			if (isset($condition->r)) {
				$cmap['r'] = (integer)$condition->r;
			}
			if (isset($condition->u)) {
				$cmap['u'] = (integer)$condition->u;
			}
			if (isset($condition->d)) {
				$cmap['d'] = (integer)$condition->d;
			}
			if (isset($condition->s)) {
				$cmap['s'] = (integer)$condition->s;
			}
			$cmap['cmapid'] = $condition->businessrule;
		}
		return $cmap;
	}

	private function getMap2Use($map2use) {
		if (!empty($map2use['condition']['cmapid'])) {
			$focus = new cbMap();
			$focus->id = $map2use['condition']['cmapid'];
			$focus->mode = '';
			$focus->retrieve_entity_info($focus->id, 'cbMap');
			$contentok = processcbMap::isXML(htmlspecialchars_decode($focus->column_fields['content']));
			if ($contentok) {
				if ($focus->column_fields['maptype']=='Condition Query') {
					$condition = $focus->ConditionQuery($this->relatedid);
				} elseif ($focus->column_fields['maptype']=='Condition Expression') {
					global $adb;
					$setype = getSalesEntityType($this->relatedid);
					$wsrs=$adb->pquery('select id from vtiger_ws_entity where name=?', array($setype));
					if ($wsrs && $adb->num_rows($wsrs)==1) {
						$eid = $adb->query_result($wsrs, 0, 0).'x'.$this->relatedid;
					} else {
						return $map2use;
					}
					$condition = $focus->ConditionExpression($eid);
				} else {
					$condition = false;
				}
				if ($condition===true || strtolower($condition)=='true' || strtolower($condition)=='yes' || (is_numeric($condition) && $condition>0)) {
					return $map2use['condition'];
				} else {
					return $map2use;
				}
			} else {
				return $map2use;
			}
		} else {
			return $map2use;
		}
	}

	/**
	* param $operation: create | retrieve | update | delete
	*/
	public function hasListViewPermissionTo($operation, $default = true) {
		if (count($this->mapping)==0) {
			$this->convertMap2Array();
		}
		if (!isset($this->mapping['listview'])) {
			return $default;
		}
		$map2use = $this->mapping['listview'];
		if (count($map2use['condition'])>0) {
			$map2use = $this->getMap2Use($map2use);
		}
		switch (strtolower($operation)) {
			case 'create':
				return (isset($map2use['c']) ? $map2use['c'] : $default);
				break;
			case 'retrieve':
			case 'detailview':
				return (isset($map2use['r']) ? $map2use['r'] : $default);
				break;
			case 'update':
			case 'edit':
			case 'editview':
				return (isset($map2use['u']) ? $map2use['u'] : $default);
				break;
			case 'delete':
				return (isset($map2use['d']) ? $map2use['d'] : $default);
				break;
			default:
				return $default;
				break;
		}
	}

	/**
	* param $operation: create | retrieve | update | delete
	*/
	public function hasDetailViewPermissionTo($operation, $default = true) {
		if (count($this->mapping)==0) {
			$this->convertMap2Array();
		}
		if (!isset($this->mapping['detailview'])) {
			return $default;
		}
		$map2use = $this->mapping['detailview'];
		if (count($map2use['condition'])>0) {
			$map2use = $this->getMap2Use($map2use);
		}
		switch (strtolower($operation)) {
			case 'create':
			case 'createview':
				return (isset($map2use['c']) ? $map2use['c'] : $default);
				break;
			case 'retrieve':
			case 'detailview':
				return (isset($map2use['r']) ? $map2use['r'] : $default);
				break;
			case 'update':
			case 'edit':
			case 'editview':
				return (isset($map2use['u']) ? $map2use['u'] : $default);
				break;
			case 'delete':
				return (isset($map2use['d']) ? $map2use['d'] : $default);
				break;
			default:
				return $default;
				break;
		}
	}

	/**
	* param $operation: create | retrieve | update | delete | select
	*/
	public function hasRelatedListPermissionTo($operation, $onmodule, $default = true) {
		if (empty($onmodule)) {
			return $default;
		}
		if (count($this->mapping)==0) {
			$this->convertMap2Array();
		}
		if (!isset($this->mapping['relatedlist']) || !isset($this->mapping['relatedlist'][$onmodule])) {
			return true;
		}
		$map2use = $this->mapping['relatedlist'][$onmodule];
		if (count($map2use['condition'])>0) {
			$map2use = $this->getMap2Use($map2use);
		}
		switch (strtolower($operation)) {
			case 'create':
				return (isset($map2use['c']) ? $map2use['c'] : $default);
				break;
			case 'retrieve':
				return (isset($map2use['r']) ? $map2use['r'] : $default);
				break;
			case 'update':
				return (isset($map2use['u']) ? $map2use['u'] : $default);
				break;
			case 'delete':
				return (isset($map2use['d']) ? $map2use['d'] : $default);
				break;
			case 'select':
				return (isset($map2use['s']) ? $map2use['s'] : $default);
				break;
			default:
				return $default;
				break;
		}
	}
}
?>
