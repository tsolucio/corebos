<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************* */
require_once 'modules/Webforms/model/WebformsFieldModel.php';

class Webforms_Model {

	public $data;
	protected $fields = array();

	public function __construct($values = array()) {
		$this->setData($values);
	}

	protected function addField(Webforms_Field_Model $field) {
		$this->fields[] = $field;
	}

	public function setData($data) {
		$this->data = $data;
		if (isset($data['fields'])) {
			$this->setFields(vtlib_purify($data['fields']), vtlib_purify($data['required']), vtlib_purify($data['value']));
		}
		if (isset($data['id'])) {
			if (isset($data['enabled']) && ($data['enabled'] == 'on') || ($data['enabled'] == 1)) {
				$this->setEnabled(1);
			} else {
				$this->setEnabled(0);
			}
		} else {
			$this->setEnabled(1);
		}
	}

	public function hasId() {
		return!empty($this->data['id']);
	}

	public function setId($id) {
		$this->data['id'] = $id;
	}

	public function setName($name) {
		$this->data['name'] = $name;
	}

	public function setTargetModule($module) {
		$this->data['targetmodule'] = $module;
	}

	protected function setPublicId($publicid) {
		$this->data['publicid'] = $publicid;
	}

	public function setEnabled($enabled) {
		$this->data['enabled'] = $enabled;
	}

	public function setDescription($description) {
		$this->data['description'] = $description;
	}

	public function setReturnUrl($returnurl) {
		$this->data['returnurl'] = $returnurl;
	}

	public function setWebDomain($web_domain) {
		$this->data['web_domain'] = $web_domain;
	}

	public function setOwnerId($ownerid) {
		$this->data['ownerid'];
	}

	public function setFields(array $fieldNames, $required, $value) {
		require_once 'include/fields/DateTimeField.php';
		foreach ($fieldNames as $fieldname) {
			$fieldInfo = Webforms::getFieldInfo($this->getTargetModule(), $fieldname);
			$fieldModel = new Webforms_Field_Model();
			$fieldModel->setFieldName($fieldname);
			$fieldModel->setNeutralizedField($fieldname, $fieldInfo['label']);
			$field = Webforms::getFieldInfo($this->getTargetModule(), $fieldname);
			if (($field['type']['name'] == 'date')) {
				$defaultvalue = DateTimeField::convertToDBFormat($value[$fieldname]);
			} elseif (($field['type']['name'] == 'boolean')) {
				if (in_array($fieldname, $required)) {
					if (empty($value[$fieldname])) {
						$defaultvalue='off';
					} else {
						$defaultvalue='on';
					}
				} else {
					$defaultvalue=$value[$fieldname];
				}
			} else {
				$defaultvalue = vtlib_purify($value[$fieldname]);
			}
			$fieldModel->setDefaultValue($defaultvalue);
			if ((!empty($required) && in_array($fieldname, $required))) {
				$fieldModel->setRequired(1);
			} else {
				$fieldModel->setRequired(0);
			}
			$this->addField($fieldModel);
		}
	}

	public function getId() {
		return (isset($this->data['id']) ? vtlib_purify($this->data['id']) : '');
	}

	public function getName() {
		return (isset($this->data['name']) ? html_entity_decode(vtlib_purify($this->data['name'])) : '');
	}

	public function getTargetModule() {
		return (isset($this->data['targetmodule']) ? vtlib_purify($this->data['targetmodule']) : '');
	}

	public function getPublicId() {
		return (isset($this->data['publicid']) ? vtlib_purify($this->data['publicid']) : '');
	}

	public function getEnabled() {
		return (isset($this->data['enabled']) ? vtlib_purify($this->data['enabled']) : '');
	}

	public function getDescription() {
		return (isset($this->data['description']) ? vtlib_purify($this->data['description']) : '');
	}

	public function getReturnUrl() {
		return (isset($this->data['returnurl']) ? vtlib_purify($this->data['returnurl']) : '');
	}

	public function getWebDomain() {
		return (isset($this->data['web_domain']) ? vtlib_purify($this->data['web_domain']) : '');
	}

	public function getOwnerId() {
		require_once 'modules/Users/Users.php';
		$return = (isset($this->data['ownerid']) ? vtlib_purify($this->data['ownerid']) : '');
		return (empty($return) ? Users::getActiveAdminId() : $return);
	}

	public function getFields() {
		return $this->fields;
	}

	public function generatePublicId($name) {
		return md5(microtime(true) + $name);
	}

	public function retrieveFields() {
		global $adb;
		$fieldsResult = $adb->pquery('SELECT * FROM vtiger_webforms_field WHERE webformid=?', array($this->getId()));
		while ($fieldRow = $adb->fetch_array($fieldsResult)) {
			$this->addField(new Webforms_Field_Model($fieldRow));
		}
		return $this;
	}

	public function save() {
		global $adb;

		$isNew = !$this->hasId();

		// Create?
		if ($isNew) {
			if (self::existWebformWithName($this->getName())) {
				throw new Exception(getTranslatedString('LBL_DUPLICATE_NAME', 'Webforms'));
			}
			$this->setPublicId($this->generatePublicId($this->getName()));
			$insertSQL = 'INSERT INTO vtiger_webforms(name, targetmodule, publicid, enabled, description,ownerid,returnurl,web_domain) VALUES(?,?,?,?,?,?,?,?)';
			$result = $adb->pquery(
				$insertSQL,
				array(
					$this->getName(), $this->getTargetModule(), $this->getPublicid(), $this->getEnabled(),
					$this->getDescription(), $this->getOwnerId(), $this->getReturnUrl(), $this->getWebDomain()
				)
			);
			$this->setId($adb->getLastInsertID());
		} else {
			// Udpate
			$updateSQL = 'UPDATE vtiger_webforms SET description=? ,returnurl=?,ownerid=?,enabled=?,web_domain=? WHERE id=?';
			$result = $adb->pquery(
				$updateSQL,
				array($this->getDescription(), $this->getReturnUrl(), $this->getOwnerId(), $this->getEnabled(), $this->getWebDomain(), $this->getId())
			);
		}

		// Delete fields and re-add enabled once
		$adb->pquery('DELETE FROM vtiger_webforms_field WHERE webformid=?', array($this->getId()));
		$fieldInsertSQL = 'INSERT INTO vtiger_webforms_field(webformid, fieldname, neutralizedfield, defaultvalue,required) VALUES(?,?,?,?,?)';
		foreach ($this->fields as $field) {
			$params = array();
			$params[] = $this->getId();
			$params[] = $field->getFieldName();
			$params[] = $field->getNeutralizedField();
			$params[] = $field->getDefaultValue();
			$params[] = $field->getRequired();
			$adb->pquery($fieldInsertSQL, $params);
		}
		return true;
	}

	public function delete() {
		global $adb;
		$adb->pquery('DELETE from vtiger_webforms_field where webformid=?', array($this->getId()));
		$adb->pquery('DELETE from vtiger_webforms where id=?', array($this->getId()));
		return true;
	}

	public static function retrieveWithPublicId($publicid) {
		global $adb;

		$model = false;
		// Retrieve model and populate information
		$result = $adb->pquery("SELECT * FROM vtiger_webforms WHERE publicid=? AND enabled=?", array($publicid, 1));
		if ($adb->num_rows($result)) {
			$model = new Webforms_Model($adb->fetch_array($result));
			$model->retrieveFields();
		}
		return $model;
	}

	public static function retrieveWithId($data) {
		global $adb;

		$id = $data;
		$model = false;
		// Retrieve model and populate information
		$result = $adb->pquery("SELECT * FROM vtiger_webforms WHERE id=?", array($id));
		if ($adb->num_rows($result)) {
			$model = new Webforms_Model($adb->fetch_array($result));
			$model->retrieveFields();
		}
		return $model;
	}

	public static function listAll() {
		global $adb;
		$webforms = array();

		$result = $adb->pquery('SELECT * FROM vtiger_webforms', array());
		for ($index = 0, $len = $adb->num_rows($result); $index < $len; $index++) {
			$webform = new Webforms_Model($adb->fetch_array($result));
			$webforms[] = $webform;
		}

		return $webforms;
	}

	public static function isWebformField($webformid, $fieldname) {
		global $adb;
		$result = $adb->pquery('SELECT 1 from vtiger_webforms_field where webformid=? AND fieldname=?', array($webformid, $fieldname));
		return (($adb->num_rows($result)) ? true : false);
	}

	public static function isCustomField($fieldname) {
		return substr($fieldname, 0, 3) === 'cf_';
	}

	public static function isRequired($webformid, $fieldname) {
		global $adb;
		$result = $adb->pquery('SELECT required FROM vtiger_webforms_field where webformid=? AND fieldname=?', array($webformid, $fieldname));
		$required = false;
		if ($adb->num_rows($result)) {
			$required = $adb->query_result($result, 0, "required");
		}
		return $required;
	}

	public static function retrieveDefaultValue($webformid, $fieldname) {
		require_once 'include/fields/DateTimeField.php';
		global $adb;
		$result = $adb->pquery('SELECT defaultvalue FROM vtiger_webforms_field WHERE webformid=? and fieldname=?', array($webformid, $fieldname));
		$defaultvalue = false;
		if ($adb->num_rows($result)) {
			$res_module = $adb->pquery('SELECT targetmodule FROM vtiger_webforms WHERE id=?', array($webformid));
			$targetmodule = $adb->query_result($res_module, 0, 'targetmodule');
			$defaultvalue = $adb->query_result($result, 0, 'defaultvalue');
			$field = Webforms::getFieldInfo($targetmodule, $fieldname);
			if (($field['type']['name'] == 'date') && !empty($defaultvalue)) {
				$defaultvalue = DateTimeField::convertToUserFormat($defaultvalue);
			}
			$defaultvalue = explode(' |##| ', $defaultvalue);
		}
		return $defaultvalue;
	}

	public static function existWebformWithName($name) {
		global $adb;
		$check = $adb->pquery('SELECT 1 FROM vtiger_webforms WHERE name=?', array($name));
		return $adb->num_rows($check) > 0;
	}

	public static function isActive($field, $mod) {
		global $adb;
		$res = $adb->pquery('SELECT 1 FROM vtiger_field WHERE fieldname = ? AND tabid = ? AND presence IN (0,2)', array($field, getTabid($mod)));
		return $adb->num_rows($res) > 0;
	}
}
?>
