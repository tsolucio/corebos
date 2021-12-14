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
  <deduplication>
	<linkfield></linkfield>
	<columns>
	 <field>
	  <label><label>
	  <name><name>
	 </field>
	 ...
	</columns>
  </deduplication>
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

	public function getDeduplcationFields() {
		return $this->mapping['cbmapDEDUPLICATION'];
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return false;
		}
		$this->modulename = (string)$xml->originmodule->originname;
		$this->moduleid = (isset($xml->originmodule->originid) ? (string)$xml->originmodule->originid : 0);
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
				$cachedModuleFields = VTCacheUtils::lookupFieldInfo($tabid, (string)$xml->popup->linkfield);
				if (!$cachedModuleFields) {
					$cachedModuleFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, (string)$xml->popup->linkfield);
					if ($cachedModuleFields) {
						$xml->popup->linkfield = $cachedModuleFields['fieldname'];
					}
				}
				$this->mapping['cbmapPOPUP']['LINKFIELD'] = (string)$xml->popup->linkfield;
			}
			foreach ($xml->popup->columns->field as $v) {
				$label = empty($v->label) ? '' : (string)$v->label;
				$table = empty($v->table) ? '' : (string)$v->table;
				$columnname = empty($v->columnname) ? '' : (string)$v->columnname;
				if ($table=='' || $columnname=='' || $label=='') {
					$cachedModuleFields = VTCacheUtils::lookupFieldInfo($tabid, (string)$v->name);
					if ($cachedModuleFields) {
						$table = str_replace('vtiger_', '', $cachedModuleFields['tablename']);
						$columnname = $cachedModuleFields['columnname'];
						$label = ($label=='' ? $cachedModuleFields['fieldlabel'] : $label);
					} else { // we try searching with column name in case they gave us that instead of the field name
						$cachedModuleFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, (string)$v->name);
						if ($cachedModuleFields) {
							$table = str_replace('vtiger_', '', $cachedModuleFields['tablename']);
							$columnname = (string)$v->name;
							$v->name = $cachedModuleFields['fieldname'];
							$label = ($label=='' ? $cachedModuleFields['fieldlabel'] : $label);
						}
					}
				}
				$this->mapping['cbmapPOPUP']['SearchFields'][$label] = array($table => $columnname);
				$this->mapping['cbmapPOPUP']['SearchFieldsName'][$label] = (string)$v->name;
			}
		}
		if (isset($xml->relatedlists)) {
			foreach ($xml->relatedlists->relatedlist as $v) {
				$mname = (string)$v->module;
				$this->mapping[$mname]['ListFields'] = array();
				$this->mapping[$mname]['ListFieldsName'] = array();
				$cachedModuleFields = VTCacheUtils::lookupFieldInfo($tabid, (string)$v->linkfield);
				if (!$cachedModuleFields) {
					$cachedModuleFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, (string)$v->linkfield);
					if ($cachedModuleFields) {
						$v->linkfield = $cachedModuleFields['fieldname'];
					}
				}
				$this->mapping[$mname]['LINKFIELD'] = (!empty($v->linkfield) ? (string)$v->linkfield : $f->list_link_field);
				foreach ($v->columns->field as $vl) {
					$label = empty($vl->label) ? '' : (string)$vl->label;
					$table = empty($vl->table) ? '' : (string)$vl->table;
					$columnname = empty($vl->columnname) ? '' : (string)$vl->columnname;
					if ($table=='' || $columnname=='' || $label=='') {
						$cachedModuleFields = VTCacheUtils::lookupFieldInfo($tabid, (string)$vl->name);
						if ($cachedModuleFields) {
							$table = str_replace('vtiger_', '', $cachedModuleFields['tablename']);
							$columnname = $cachedModuleFields['columnname'];
							$label = ($label=='' ? $cachedModuleFields['fieldlabel'] : $label);
						} else { // we try searching with column name in case they gave us that instead of the field name
							$cachedModuleFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, (string)$vl->name);
							if ($cachedModuleFields) {
								$table = str_replace('vtiger_', '', $cachedModuleFields['tablename']);
								$columnname = (string)$vl->name;
								$vl->name = $cachedModuleFields['fieldname'];
								$label = ($label=='' ? $cachedModuleFields['fieldlabel'] : $label);
							}
						}
					}
					$this->mapping[$mname]['ListFields'][$label] = array($table => $columnname);
					$this->mapping[$mname]['ListFieldsName'][$label] = (string)$vl->name;
				}
			}
		}
		if (isset($xml->summary)) {
			$this->mapping['cbmapSUMMARY'] = array();
			$this->mapping['cbmapSUMMARY']['TITLE'] = (string) $xml->summary->title;
			$this->mapping['cbmapSUMMARY']['HEADER']['ListFields'] = array();
			$this->mapping['cbmapSUMMARY']['BODY']['ListFields'] = array();

			foreach ($xml->summary->header->fields as $v) {
				foreach ($v->field as $vf) {
					$this->mapping['cbmapSUMMARY']['HEADER']['ListFields'][(string)$vf->label] = (string)$vf->name;
				}
			}

			foreach ($xml->summary->body->fields as $v) {
				foreach ($v->field as $vf) {
					$this->mapping['cbmapSUMMARY']['BODY']['ListFields'][(string)$vf->label] = (string)$vf->name;
				}
			}
		}
		if (isset($xml->deduplication)) {
			$linkfield = (string)$xml->deduplication->linkfield;
			$cachedModuleFields = VTCacheUtils::lookupFieldInfo($tabid, $linkfield);
			if (!$cachedModuleFields) {
				$cachedModuleFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, $linkfield);
				if ($cachedModuleFields) {
					$linkfield = $cachedModuleFields['fieldname'];
				}
			}
			$this->mapping['cbmapDEDUPLICATION']['LINKFIELD'] = (!empty($linkfield) ? $linkfield : $f->list_link_field);
			foreach ($xml->deduplication->columns->field as $v) {
				$label = empty($v->label) ? '' : (string)$v->label;
				$name = empty($v->name) ? '' : (string)$v->name;
				$this->mapping['cbmapDEDUPLICATION']['ListFields'][$label] = $name;
			}
		}
	}
}
?>
