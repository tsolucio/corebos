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
 *  Module       : Business Mappings:: Record Set Mapping
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <records>
  <record>
  <id>1</id> if given, module and value are ignored
  <module>ModuleName</module>
  <value>EntityCustomNumberValue</value> we only search on the uitype 4 field
  <action>include</action>  Include | Exclude | Group  The default action is Exclude
  </record>
  .....
  </records>
  </map>
 *************************************************************************************************/

class RecordSetMapping extends processcbMap {
	private $mapping = array(
		'include'=>array(),
		'exclude'=>array(),
		'group'=>array(),
		'modules'=>array()
	);
	private $actions = array('include','exclude','group');
	private $default_action = 'exclude';

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	private function convertMap2Array() {
		global $adb, $current_user;
		$xml = $this->getXMLContent();
		if (isset($xml->records)) {
			foreach ($xml->records->record as $k => $v) {
				if (isset($v->action)) {
					$action = strtolower((String)$v->action);
					if (!in_array($action, $this->actions)) {
						$action = $this->default_action;
					}
				} else {
					$action = $this->default_action;
				}
				if (isset($v->id)) {
					$rs = $adb->pquery('select setype from vtiger_crmentity where crmid=? and deleted=0', array((Integer)$v->id));
					if ($adb->num_rows($rs)==1) {
						$recinfo = $adb->fetch_array($rs);
						$this->mapping[$action]['ids'][] = (Integer)$v->id;
						$this->mapping[$action][$recinfo['setype']][] = (Integer)$v->id;
						if (!in_array($recinfo['setype'], $this->mapping['modules'])) {
							$this->mapping['modules'][] = $recinfo['setype'];
						}
					}
				} else {
					$tabid = getTabid((String)$v->module);
					$ui4rs = $adb->pquery('select fieldname from vtiger_field where uitype=4 and tabid=?', array($tabid));
					$ui4 = $adb->query_result($ui4rs, 0, 0);
					$queryGenerator = new QueryGenerator((String)$v->module, $current_user);
					$queryGenerator->setFields(array('id'));
					$queryGenerator->addCondition($ui4, (String)$v->value, 'e');
					$query = $queryGenerator->getQuery();
					$idrs = $adb->pquery($query, array());
					if ($idrs && $adb->num_rows($idrs)>0) {
						$id = $adb->query_result($idrs, 0, 0);
						$this->mapping[$action]['ids'][] = (Integer)$id;
						$this->mapping[$action][(String)$v->module][] = (Integer)$id;
						if (!in_array($recinfo['setype'], $this->mapping['modules'])) {
							$this->mapping['modules'][] = $recinfo['setype'];
						}
					}
				}
			}
		}
	}

	public function getFullRecordSet() {
		return $this->mapping;
	}

	/**
	* param $action: include | exclude | group
	*/
	public function getRecordSet($action) {
		return $this->mapping[strtolower($action)]['ids'];
	}

	/**
	* param $action: include | exclude | group
	* param $module
	*/
	public function getRecordSetModule($action, $module) {
		return $this->mapping[strtolower($action)][$module];
	}

	/**
	 * returns the set of modules that have at least on CRMid in the record set
	 **/
	public function getRecordSetModules() {
		return $this->mapping['modules'];
	}

	/**
	* param $action: include | exclude | group
	* param $id
	*/
	public function isInRecordSet($action, $id) {
		return in_array($id, $this->mapping[strtolower($action)]['ids']);
	}

	/**
	* param $action: include | exclude | group
	* param $module
	* param $id
	*/
	public function isInRecordSetModule($action, $module, $id) {
		return in_array($id, $this->mapping[strtolower($action)][$module]);
	}
}
?>
