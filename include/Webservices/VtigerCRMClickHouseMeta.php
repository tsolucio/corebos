<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/VtigerCRMObjectMeta.php';
require_once 'include/database/ClickHouseDatabase.php';

class VtigerCRMClickHouseMeta extends EntityMeta {
	protected $pearDB;
	protected static $fieldTypeMapping = array();
	private $hasAccess;
	private $hasReadAccess;
	private $hasCreateAccess;
	private $hasWriteAccess;
	private $hasDeleteAccess;
	private $PermissionModule = '';
	protected $objectName;

	public function __construct($tableName, $webserviceObject, $adb, $user) {
		global $cdb;
		if (empty($cdb)) {
			$cdb = new ClickHouseDatabase();
			$cdb->connect();
		}
		parent::__construct($webserviceObject, $user);
		$this->baseTable = $tableName;
		$this->idColumn = null;
		$this->pearDB = $cdb;

		$fieldList = $this->getTableFieldList($tableName);
		$this->moduleFields = array();
		foreach ($fieldList as $field) {
			$this->moduleFields[$field->getFieldName()] = $field;
		}

		$this->tableList = array($this->baseTable);
		$this->tableIndexList = array($this->baseTable=>$this->idColumn);
		$this->defaultTableList = array();
		$this->objectName = $webserviceObject->getEntityName();
		$this->computeAccess($webserviceObject, $user);
	}

	public function addAnotherTable($tableName, $tableIDColumn) {
		$fieldList = $this->getTableFieldList($tableName);
		foreach ($fieldList as $field) {
			$this->moduleFields[$field->getFieldName()] = $field;
		}
		$this->tableList[] = $tableName;
		$this->tableIndexList[$tableName] = $tableIDColumn;
	}

	public function getmoduleFields() {
		return $this->moduleFields;
	}

	public function setmoduleField($field, $property, $value) {
		switch ($property) {
			// case 'nullable':
			// 	$this->moduleFields[$field]->setNullable($value); // private
			// 	break;
			case 'default':
				$this->moduleFields[$field]->setDefault($value);
				break;
			case 'fieldDataType':
				$this->moduleFields[$field]->setFieldDataType($value);
				break;
			case 'referenceList':
				$this->moduleFields[$field]->setReferenceList($value);
				break;
			case 'uitype':
				$this->moduleFields[$field]->setUIType($value);
				break;
			default:
				break;
		}
	}

	public function getTabName() {
		return $this->objectName;
	}

	public function getPermissionModule() {
		return $this->PermissionModule;
	}

	private function computeAccess($webserviceObject, $user) {
		$this->hasAccess = true;
		$this->hasCreateAccess = true;
		$this->hasReadAccess = true;
		$this->hasWriteAccess = true;
		$this->hasDeleteAccess = false;
	}

	protected function getTableFieldList($tableName) {
		$tableFieldList = array();

		$factory = WebserviceField::fromArray($this->pearDB, array('tablename'=>$tableName));
		$dbTableFields = $factory->getTableFields();
		foreach ($dbTableFields as $dbField) {
			if ($dbField->primary_key) {
				if ($this->idColumn === null) {
					$this->idColumn = $dbField->name;
				} else {
					if ($tableName!='vtiger_modtracker_detail') { // valid table with compound primary key
						throw new WebServiceException(WebServiceErrorCode::$UNKNOWNENTITY, 'Entity table with multi column primary key is not supported');
					}
				}
			}
			$field = $this->getFieldArrayFromDBField($dbField, $tableName);
			$webserviceField = WebserviceField::fromArray($this->pearDB, $field);
			$fieldDataType = $this->getFieldType($dbField, $tableName);
			if ($fieldDataType === null) {
				$fieldDataType = $this->getFieldDataTypeFromDBType($dbField->type);
			}
			$webserviceField->setFieldDataType($fieldDataType);
			if (strcasecmp($fieldDataType, 'reference') === 0) {
				$webserviceField->setReferenceList($this->getReferenceList($dbField, $tableName));
			}
			$tableFieldList[] = $webserviceField;
		}
		return $tableFieldList;
	}

	protected function getFieldArrayFromDBField($dbField, $tableName) {
		$field = array();
		$field['fieldname'] = $dbField->name;
		$field['columnname'] = $dbField->name;
		$field['tablename'] = $tableName;
		$field['fieldlabel'] = str_replace('_', ' ', $dbField->name);
		$field['displaytype'] = 1;
		$field['uitype'] = 1;
		$fieldDataType = $this->getFieldType($dbField, $tableName);
		if ($fieldDataType !== null) {
			$fieldType = $this->getTypeOfDataForType($fieldDataType);
		} else {
			$fieldType = $this->getTypeOfDataForType($dbField->type);
		}
		$typeOfData = null;
		if (($dbField->not_null && !$dbField->primary_key) || (isset($dbField->unique_key) && $dbField->unique_key == 1)) {
			$typeOfData = $fieldType.'~M';
		} else {
			$typeOfData = $fieldType.'~O';
		}
		$field['typeofdata'] = $typeOfData;
		$field['tabid'] = null;
		$field['fieldid'] = null;
		$field['masseditable'] = 0;
		$field['presence'] = '0';
		return $field;
	}

	protected function getReferenceList($dbField, $tableName) {
		global $adb;
		static $referenceList = array();
		if (isset($referenceList[$dbField->name])) {
			return $referenceList[$dbField->name];
		}
		if (!isset(VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name])) {
			$this->getFieldType($dbField, $tableName);
		}
		$fieldTypeData = VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name];
		$referenceTypes = array();
		$result = $adb->pquery('select type from vtiger_ws_entity_referencetype where fieldtypeid=?', array($fieldTypeData['fieldtypeid']));
		$numRows = $adb->num_rows($result);
		for ($i=0; $i<$numRows; ++$i) {
			$referenceTypes[] = $adb->query_result($result, $i, 'type');
		}
		$referenceList[$dbField->name] = $referenceTypes;
		return $referenceTypes;
	}

	protected function getFieldType($dbField, $tableName) {
		global $adb;
		if (isset(VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name])) {
			if (VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name] === 'null') {
				return null;
			}
			$row = VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name];
			return $row['fieldtype'];
		}
		$result = $adb->pquery('select * from vtiger_ws_entity_fieldtype where table_name=? and field_name=?;', array($tableName,$dbField->name));
		$rowCount = $adb->num_rows($result);
		if ($rowCount > 0) {
			$row = $adb->query_result_rowdata($result, 0);
			VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name] = $row;
			return $row['fieldtype'];
		} else {
			VtigerCRMClickHouseMeta::$fieldTypeMapping[$tableName][$dbField->name] = 'null';
			return null;
		}
	}

	protected function getTypeOfDataForType($type) {
		switch ($type) {
			case 'email':
				return 'E';
			case 'password':
				return 'P';
			case 'date':
				return 'D';
			case 'datetime':
				return 'DT';
			case 'timestamp':
				return 'T';
			case 'int':
			case 'integer':
				return 'I';
			case 'decimal':
			case 'numeric':
				return 'N';
			case 'varchar':
			case 'text':
			default:
				return 'V';
		}
	}

	protected function getFieldDataTypeFromDBType($type) {
		switch ($type) {
			case 'date':
				return 'date';
			case 'datetime':
				return 'datetime';
			case 'timestamp':
				return 'time';
			case 'int':
			case 'integer':
				return 'integer';
			case 'real':
			case 'decimal':
			case 'numeric':
				return 'double';
			case 'text':
				return 'text';
			case 'varchar':
				return 'string';
			default:
				return $type;
		}
	}

	public function hasPermission($operation, $webserviceId) {
		return $this->hasAccess;
	}

	public function hasAssignPrivilege($ownerWebserviceId) {
		return true;
	}

	public function hasDeleteAccess() {
		return $this->hasDeleteAccess;
	}

	public function hasAccess() {
		return $this->hasAccess;
	}

	public function hasReadAccess() {
		return $this->hasReadAccess;
	}

	public function hasCreateAccess() {
		return $this->hasCreateAccess;
	}

	public function hasWriteAccess() {
		return $this->hasWriteAccess;
	}

	public function getEntityName() {
		return $this->webserviceObject->getEntityName();
	}

	public function getEntityId() {
		return $this->webserviceObject->getEntityId();
	}

	public function getObjectEntityName($webserviceId) {
		$idComponents = vtws_getIdComponents($webserviceId);
		$id=$idComponents[1];

		if ($this->exists($id)) {
			return $this->webserviceObject->getEntityName();
		}
		return null;
	}

	public function exists($recordId) {
		$result = $this->pearDB->pquery('select 1 from '.$this->baseTable.' where '.$this->getObectIndexColumn().'=? limit 1', array($recordId));
		return ($result && $this->pearDB->num_rows($result)>0);
	}

	public function getNameFields() {
		global $adb;
		$result = $adb->pquery('select name_fields from vtiger_ws_entity_name where entity_id = ?', array($this->objectId));
		$fieldNames = '';
		if ($result) {
			$rowCount = $adb->num_rows($result);
			if ($rowCount > 0) {
				$fieldNames = $adb->query_result($result, 0, 'name_fields');
			}
		}
		return $fieldNames;
	}

	public function getName($webserviceId) {
		$idComponents = vtws_getIdComponents($webserviceId);
		$entityId = $idComponents[0];
		$id=$idComponents[1];
		$nameList = vtws_getActorEntityNameById($entityId, array($id));
		return $nameList[$id];
	}

	public function getEntityAccessControlQuery() {
		return '';
	}

	public function getEntityDeletedQuery() {
		if ($this->getEntityName() == 'Currency') {
			return 'vtiger_currency_info.deleted=0';
		}
		return '';
	}

	public function isModuleEntity() {
		return false;
	}
}
?>