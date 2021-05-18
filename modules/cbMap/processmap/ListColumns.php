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
 *  Module       : Business Mappings:: List Columns
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <originmodule>
	<originid>22</originid>  {optional}
	<originname>SalesOrder</originname>
  </originmodule>
  <relatedlists>
   <relatedlist>
   <module>{parentmodule}</module>
   <linkfield></linkfield>
	<columns>
	 <field>
	  <label></label>
	  <name></name>
	  <table></table>
	  <columnname></columnname>
	 </field>
	 ...
	</columns>
   </relatedlist>
   ....
  </relatedlists>
  <popup>
	<linkfield></linkfield>
	<columns>
	 <field>
	  <label><label>
	  <name><name>
	  <table><table>
	  <columnname><columnname>
	 </field>
	 ...
	</columns>
  </popup>
 </map>
 *************************************************************************************************/
require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';

class ListColumns extends processcbMap {
	private $mapping = array();
	private $modulename = '';
	private $moduleid = 0;

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function issetListFieldsMappingFor($module) {
		return (isset($this->mapping[$module]));
	}

	public function getListFieldsFor($module) {
		if (isset($this->mapping[$module])) {
			return $this->mapping[$module]['ListFields'];
		}
		return $this->mapping['cbmapDEFAULT']['ListFields'];
	}

	public function getListFieldsNameFor($module) {
		if (isset($this->mapping[$module])) {
			return $this->mapping[$module]['ListFieldsName'];
		}
		return $this->mapping['cbmapDEFAULT']['ListFieldsName'];
	}

	public function getListLinkFor($module) {
		if (isset($this->mapping[$module])) {
			return $this->mapping[$module]['LINKFIELD'];
		}
		return $this->mapping['cbmapDEFAULT']['LINKFIELD'];
	}

	public function getSearchFields() {
		return $this->mapping['cbmapPOPUP']['SearchFields'];
	}

	public function getSearchFieldsName() {
		return $this->mapping['cbmapPOPUP']['SearchFieldsName'];
	}

	public function getSearchLinkField() {
		return $this->mapping['cbmapPOPUP']['LINKFIELD'];
	}

	public function getCompleteMapping() {
		return $this->mapping;
	}

	public function getSummaryTitle() {
		return $this->mapping['cbmapSUMMARY']['TITLE'];
	}

	public function getSummaryHeader() {
		return $this->mapping['cbmapSUMMARY']['HEADER'];
	}

	public function getSummaryBody() {
		return $this->mapping['cbmapSUMMARY']['BODY'];
	}

	private function convertMap2Array() {
		global $adb;
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return false;
		}
		$this->modulename = (String)$xml->originmodule->originname;
		$this->moduleid = (isset($xml->originmodule->originid) ? (String)$xml->originmodule->originid : 0);
		$f = CRMEntity::getInstance($this->modulename);
		$this->mapping['cbmapDEFAULT']['LINKFIELD'] = $f->list_link_field;
		$this->mapping['cbmapDEFAULT']['ListFields'] = $f->list_fields;
		$this->mapping['cbmapDEFAULT']['ListFieldsName'] = $f->list_fields_name;
		$this->mapping['cbmapPOPUP']['LINKFIELD'] = $f->list_link_field;
		$this->mapping['cbmapPOPUP']['SearchFields'] = $f->search_fields;
		$this->mapping['cbmapPOPUP']['SearchFieldsName'] = $f->search_fields_name;
		$tabid = getTabid($this->modulename);
		if (isset($xml->popup)) {
			$this->mapping['cbmapPOPUP']['SearchFields'] = array();
			$this->mapping['cbmapPOPUP']['SearchFieldsName'] = array();
			if (!empty($xml->popup->linkfield)) {
				$this->mapping['cbmapPOPUP']['LINKFIELD'] = (String)$xml->popup->linkfield;
			}
			foreach ($xml->popup->columns->field as $v) {
				$label = empty($v->label) ? '' : (String)$v->label;
				$table = empty($v->table) ? '' : (String)$v->table;
				$columnname = empty($v->columnname) ? '' : (String)$v->columnname;
				if ($table=='' || $columnname=='' || $label=='') {
					$res = $adb->pquery('SELECT columnname,tablename,fieldlabel FROM vtiger_field WHERE fieldname=? AND tabid=?', array((String)$v->name, $tabid));
					if ($res && $adb->num_rows($res)>0) {
						$table = str_replace('vtiger_', '', $adb->query_result($res, 0, 'tablename'));
						$columnname = $adb->query_result($res, 0, 'columnname');
						$label = ($label=='' ? $adb->query_result($res, 0, 'fieldlabel') : $label);
					}
				}
				$this->mapping['cbmapPOPUP']['SearchFields'][$label] = array($table => $columnname);
				$this->mapping['cbmapPOPUP']['SearchFieldsName'][$label] = (String)$v->name;
			}
		}
		if (isset($xml->relatedlists)) {
			foreach ($xml->relatedlists->relatedlist as $v) {
				$modulename = (String)$v->module;
				$this->mapping[$modulename]['ListFields'] = array();
				$this->mapping[$modulename]['ListFieldsName'] = array();
				$this->mapping[$modulename]['LINKFIELD'] = (!empty($v->linkfield) ? (String)$v->linkfield : $f->list_link_field);
				foreach ($v->columns->field as $vl) {
					$label = empty($vl->label) ? '' : (String)$vl->label;
					$table = empty($vl->table) ? '' : (String)$vl->table;
					$columnname = empty($vl->columnname) ? '' : (String)$vl->columnname;
					if ($table=='' || $columnname=='' || $label=='') {
						$res = $adb->pquery('SELECT columnname,tablename,fieldlabel FROM vtiger_field WHERE fieldname=? AND tabid=?', array((String)$vl->name, $tabid));
						if ($res && $adb->num_rows($res)>0) {
							$table = str_replace('vtiger_', '', $adb->query_result($res, 0, 'tablename'));
							$columnname = $adb->query_result($res, 0, 'columnname');
							$label = ($label=='' ? $adb->query_result($res, 0, 'fieldlabel') : $label);
						}
					}
					$this->mapping[$modulename]['ListFields'][$label] = array($table => $columnname);
					$this->mapping[$modulename]['ListFieldsName'][$label] = (String)$vl->name;
				}
			}
		}
		if (isset($xml->summary)) {
			$this->mapping['cbmapSUMMARY'] = array();
			$this->mapping['cbmapSUMMARY']['TITLE'] = (String) $xml->summary->title;
			$this->mapping['cbmapSUMMARY']['HEADER']['ListFields'] = array();
			$this->mapping['cbmapSUMMARY']['BODY']['ListFields'] = array();

			foreach ($xml->summary->header->fields as $v) {
				foreach ($v->field as $vf) {
					$this->mapping['cbmapSUMMARY']['HEADER']['ListFields'][(String)$vf->label] = (String)$vf->name;
				}
			}

			foreach ($xml->summary->body->fields as $v) {
				foreach ($v->field as $vf) {
					$this->mapping['cbmapSUMMARY']['BODY']['ListFields'][(String)$vf->label] = (String)$vf->name;
				}
			}
		}
	}
}
?>
