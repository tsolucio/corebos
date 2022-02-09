<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : WEBDAV
 *************************************************************************************************/

class DirectoryModule extends Sabre\DAV\Collection {
	private $tabid;
	private $module;
	private static $moduleCache = array();

	public function __construct($tabid) {
		$this->tabid = $tabid;
	}

	private function getModuleName() {
		if (!empty($this->module)) {
			return $this->module;
		}
		$this->module = getTabModuleName($this->tabid);
		return $this->module;
	}

	private static function getModule($module) {
		if (isset(self::$moduleCache[$module])) {
			return self::$moduleCache[$module];
		}
		require_once "modules/$module/$module.php";
		self::$moduleCache[$module] = new $module();
		return self::$moduleCache[$module];
	}

	public function getChildren() {
		switch (GlobalVariable::getVariable('WEBDAV_Module_View', 'Letter', $this->getModuleName())) {
			case 'Files':
				$dml = new DirectoryModuleList($this->tabid);
				return $dml->getChildren();
				break;
			case 'Letter':
			default:
				// select letters
				$letters = DirectoryModule::getLetterArray($this->getModuleName(), '');
				$dirs = array();
				foreach ($letters as $letter) {
					$dirs[] = new DirectoryLetter($this->tabid, $letter);
				}
				return $dirs;
		}
	}

	public function getChild($name) {
		return new DirectoryLetter($this->tabid, $name);
	}

	public function childExists($name) {
		return true;
	}

	public function getName() {
		return 'UNUSED';
	}

	/* Module-specific settings */
	public static function getFolderName($module, $data) {
		switch ($module) {
			case 'Leads':
				$return = $data['lastname'].', '.$data['firstname'].' ['.$data['leadid'].']';
				break;
			case 'Contacts':
				$return = $data['lastname'].', '.$data['firstname'].' ['.$data['contactid'].']';
				break;
			case 'Invoice':
				$return = $data['subject'].' ['.$data['invoiceid'].']';
				break;
			case 'Accounts':
				$return = $data['accountname'].' ['.$data['accountid'].']';
				break;
			default:
				$obj = self::getModule($module);
				$column = getColumnnameByFieldname(getTabid($module), $obj->list_link_field);
				$return = $data[$column].' ['.$data[$obj->table_index].']';
				break;
		}
		return $return;
	}

	public static function getQueryFields($module) {
		switch ($module) {
			case 'Leads':
			case 'Contacts':
				return array('lastname', 'firstname', 'id');
			case 'Invoice':
				return array('subject', 'id');
				break;
			case 'Accounts':
				return array('accountname', 'id');
				break;
			default:
				$obj = self::getModule($module);
				return array($obj->list_link_field, 'id');
				break;
		}
	}

	public static function getSearchArray($module, $letter = 'A') {
		if (!isset($letter) || (empty($letter) && $letter !== '0')) {
			$letter = 'A';
		}
		$obj = self::getModule($module);
		if ($letter=='EMPTY') {
			return array('operator' => 'e', 'search_field' => $obj->list_link_field, 'search_text' => '');
		} else {
			return array('operator' => 's', 'search_field' => $obj->list_link_field, 'search_text' => $letter);
		}
	}

	public static function getLetterArray($module, $letter) {
		global $adb;
		$obj = self::getModule($module);
		$field = $obj->list_link_field;
		$column = getColumnnameByFieldname(getTabid($module), $field);
		$table = $obj->table_name;
		$id = $obj->table_index;

		unset($obj);
		$sql = 'SELECT SUBSTR('.$column.', 1, '.(strlen($letter)+1).') AS letter
			FROM '.$table.' as dataTable
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=dataTable.'.$id.'
			WHERE vtiger_crmentity.deleted=0 AND '.$column." LIKE '".$letter."%' ".($module=='Leads' ? 'AND converted=0' : '').'
			GROUP BY SUBSTR('.$column.', 1, '.(strlen($letter)+1).')';
		$records = $adb->query($sql);
		$letter = array();
		while ($data = $adb->fetch_array($records)) {
			$letter[] = empty($data['letter']) ? 'empty' : $data['letter'];
		}
		return $letter;
	}
}
