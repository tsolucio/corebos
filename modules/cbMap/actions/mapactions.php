<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
include_once 'modules/cbMap/cbMap.php';
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';

class mapactions_Action extends CoreBOS_ActionController {

	private function libxmlDisplayErrors() {
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			print $this->libxmlDisplayError($error);
		}
		libxml_clear_errors();
	}

	private function libxmlDisplayError($error) {
		$return = "<br/>\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= 'Warning: ';
				break;
			case LIBXML_ERR_ERROR:
				$return .= 'Error: ';
				break;
			case LIBXML_ERR_FATAL:
				$return .= 'Fatal Error: ';
				break;
		}
		$return .= trim($error->message);
		$return .= " on line <b>$error->line</b>\n";
		return $return;
	}

	public function validateMap() {
		$mapid = vtlib_purify($_REQUEST['mapid']);
		$focus1 = CRMEntity::getInstance('cbMap');
		$focus1->retrieve_entity_info($mapid, 'cbMap');
		$maptype = $focus1->column_fields['maptype'];
		$content = $focus1->column_fields['content'];
		// Enable user error handling
		libxml_use_internal_errors(true);
		$content1 = htmlspecialchars_decode($content);
		$xml = new DOMDocument();
		$xml->loadXML($content1);
		if (!file_exists('modules/cbMap/XSD_schemas/' . $maptype . '.xsd')) {
			echo 'VALIDATION_NOT_IMPLEMENTED_YET';
		} else {
			if (!$xml->schemaValidate('modules/cbMap/XSD_schemas/' . $maptype . '.xsd')) {
				$this->libxmlDisplayErrors();
			}
		}
	}

	public function getGenMap() {
		include_once 'modules/cbMap/generatemap/generateMap.php';
		$mapid = vtlib_purify($_REQUEST['mapid']);
		$mapInstance = CRMEntity::getInstance('cbMap');
		$mapInstance->retrieve_entity_info($mapid, 'cbMap');
		$maptype = $mapInstance->column_fields['maptype'];
		$maptype = str_replace(' ', '', $maptype);
		if (file_exists('modules/cbMap/generatemap/'.$maptype.'.php')) {
			include_once 'modules/cbMap/generatemap/'.$maptype.'.php';
			$maptype = 'gen'.$maptype;
			$genmap = new $maptype($mapInstance);
		} else {
			$genmap = new generatecbMap($mapInstance);
		}
		return $genmap;
	}

	public function generateMap() {
		$genmap = $this->getGenMap();
		$genmap->generateMap();
	}

	private function convertToMap() {
		$genmap = $this->getGenMap();
		return $genmap->convertToMap();
	}

	public function saveMap() {
		global $current_user;
		include_once 'include/Webservices/Revise.php';
		$map = array(
			'id' => vtws_getEntityId('cbMap').'x'.vtlib_purify($_REQUEST['mapid']),
			'content' => $this->convertToMap(),
		);
		vtws_revise($map, $current_user);
	}

	public static function getFieldTablesForModule($return = false) {
		global $log, $adb;
		if (empty($_REQUEST['fieldsmodule'])) {
			if ($return) {
				return array();
			} else {
				echo '[]';
			}
		}
		$module = vtlib_purify($_REQUEST['fieldsmodule']);
		$log->debug('> getFieldTablesForModule '.$module);
		$res = $adb->pquery('select fieldname,tablename from vtiger_field where tabid=?', array(getTabid($module)));
		$fields = array();
		while ($row = $res->FetchRow()) {
			$fields[$row['fieldname']] = $row['tablename'];
		}
		$log->debug('< getTableNameForField');
		if ($return) {
			return $fields;
		} else {
			echo json_encode($fields);
		}
	}

	public static function getFieldTranslationForModule($return = false) {
		global $log, $adb;
		if (empty($_REQUEST['fieldsmodule'])) {
			if ($return) {
				return array();
			} else {
				echo '[]';
			}
		}
		$module = vtlib_purify($_REQUEST['fieldsmodule']);
		$log->debug('> getFieldTranslationForModule '.$module);
		$res = $adb->pquery('select fieldname,fieldlabel,tablename from vtiger_field where tabid=?', array(getTabid($module)));
		$fields = array();
		while ($row = $res->FetchRow()) {
			$fields[$row['fieldname']] = getTranslatedString($row['fieldlabel'], $module);
		}
		$log->debug('< getFieldTranslationForModule');
		if ($return) {
			return $fields;
		} else {
			echo json_encode($fields);
		}
	}

	public static function getFieldLabel() {
		global $log, $adb;
		$module = vtlib_purify($_REQUEST['fieldsmodule']);
		$field = vtlib_purify($_REQUEST['field']);
		$tabid = getTabid($module);
		$log->debug('> getFieldLabel '.$module);
		$res = $adb->pquery('select fieldlabel from vtiger_field where fieldname=? and tabid=?', array($field, $tabid));
		$fieldlabel = $adb->query_result($res, 0, 'fieldlabel');
		$label = getTranslatedString($fieldlabel, $module);
		$log->debug('< getFieldLabel');
		echo json_encode($label);
	}
}
?>