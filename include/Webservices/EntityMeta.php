<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

abstract class EntityMeta {

	public static $RETRIEVE = 'DetailView';
	public static $CREATE = 'Save';
	public static $UPDATE = 'EditView';
	public static $DELETE = 'Delete';

	protected $webserviceObject;
	protected $objectName;
	protected $objectId;
	protected $user;
	protected $baseTable;
	protected $tableList;
	protected $tableIndexList;
	protected $defaultTableList;
	protected $idColumn;

	protected $userAccessibleColumns;
	protected $columnTableMapping;
	protected $fieldColumnMapping;
	protected $mandatoryFields;
	protected $referenceFieldDetails;
	protected $emailFields;
	protected $imageFields;
	protected $ownerFields;
	protected $moduleFields;

	protected function __construct($webserviceObject, $user) {
		$this->webserviceObject = $webserviceObject;
		$this->objectName = $this->webserviceObject->getEntityName();
		$this->objectId = $this->webserviceObject->getEntityId();
		$this->user = $user;
	}

	public function getEmailFields() {
		if ($this->emailFields === null) {
			$this->emailFields = array();
			foreach ($this->moduleFields as $fieldName => $webserviceField) {
				if (strcasecmp($webserviceField->getFieldType(), 'e') === 0) {
					$this->emailFields[] = $fieldName;
				}
			}
		}
		return $this->emailFields;
	}

	public function getImageFields() {
		if ($this->imageFields === null) {
			$this->imageFields = array();
			foreach ($this->moduleFields as $fieldName => $webserviceField) {
				if ($webserviceField->getUIType() == 69) {
					$this->imageFields[] = $fieldName;
				}
			}
		}
		return $this->imageFields;
	}

	public function getFieldColumnMapping() {
		if ($this->fieldColumnMapping === null) {
			$this->fieldColumnMapping = array();
			foreach ($this->moduleFields as $fieldName => $webserviceField) {
				$this->fieldColumnMapping[$fieldName] = $webserviceField->getColumnName();
			}
			$this->fieldColumnMapping['id'] = $this->idColumn;
		}
		return $this->fieldColumnMapping;
	}

	public function getMandatoryFields() {
		if ($this->mandatoryFields === null) {
			$this->mandatoryFields = array();
			foreach ($this->moduleFields as $fieldName => $webserviceField) {
				if ($webserviceField->isMandatory() === true) {
					$this->mandatoryFields[] = $fieldName;
				}
			}
		}
		return $this->mandatoryFields;
	}

	public function getReferenceFieldDetails() {
		if ($this->referenceFieldDetails === null) {
			$this->referenceFieldDetails = array();
			foreach ($this->moduleFields as $fieldName => $webserviceField) {
				if (strcasecmp($webserviceField->getFieldDataType(), 'reference') === 0) {
					$this->referenceFieldDetails[$fieldName] = $webserviceField->getReferenceList();
				}
			}
		}
		return $this->referenceFieldDetails;
	}

	public function getOwnerFields() {
		if ($this->ownerFields === null) {
			$this->ownerFields = array();
			foreach ($this->moduleFields as $fieldName => $webserviceField) {
				if (strcasecmp($webserviceField->getFieldDataType(), 'owner') === 0) {
					$this->ownerFields[] = $fieldName;
				}
			}
		}
		return $this->ownerFields;
	}

	public function getObectIndexColumn() {
		return $this->idColumn;
	}

	public function getUserAccessibleColumns() {
		if ($this->userAccessibleColumns === null) {
			$this->userAccessibleColumns = array();
			foreach ($this->moduleFields as $webserviceField) {
				$this->userAccessibleColumns[] = $webserviceField->getColumnName();
			}
			$this->userAccessibleColumns[] = $this->idColumn;
		}
		return $this->userAccessibleColumns;
	}

	public function getFieldByColumnName($column) {
		$fields = $this->getModuleFields();
		foreach ($fields as $webserviceField) {
			if ($column == $webserviceField->getColumnName()) {
				return $webserviceField;
			}
		}
		return null;
	}

	public function getColumnTableMapping() {
		if ($this->columnTableMapping === null) {
			$this->columnTableMapping = array();
			foreach ($this->moduleFields as $webserviceField) {
				$this->columnTableMapping[$webserviceField->getColumnName()] = $webserviceField->getTableName();
			}
			$this->columnTableMapping[$this->idColumn] = $this->baseTable;
		}
		return $this->columnTableMapping;
	}

	public function getUser() {
		return $this->user;
	}

	public function hasMandatoryFields($row) {
		$mandatoryFields = $this->getMandatoryFields();
		$hasMandatory = true;
		foreach ($mandatoryFields as $field) {
			if (!isset($row[$field]) || $row[$field] === '' || $row[$field] === null) {
				// Getting Field label.
				$fieldLabelKey = $this->moduleFields[$field]->getFieldLabelKey();
				$tabId = $this->moduleFields[$field]->getTabId();
				$label = getTranslatedString($fieldLabelKey, getTabModuleName($tabId));
				throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, "$label ($field) does not have a value");
			}
		}
		return $hasMandatory;
	}

	public function isUpdateMandatoryFields($element) {
		if (!is_array($element)) {
			throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, 'Mandatory field does not have a value');
		}
		$mandatoryFields = $this->getMandatoryFields();
		$updateFields = array_keys($element);
		$hasMandatory = true;
		$updateMandatoryFields = array_intersect($updateFields, $mandatoryFields);
		if (!empty($updateMandatoryFields)) {
			foreach ($updateMandatoryFields as $field) {
				// dont use empty API as '0'(zero) is a valid value.
				if (!isset($element[$field]) || $element[$field] === '' || $element[$field] === null) {
					// Getting Field label.
					$fieldLabelKey = $this->moduleFields[$field]->getFieldLabelKey();
					$tabId = $this->moduleFields[$field]->getTabId();
					$label = getTranslatedString($fieldLabelKey, getTabModuleName($tabId));
					throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, "$label ($field) does not have a value");
				}
			}
		}
		return $hasMandatory;
	}

	public function getModuleFields() {
		return $this->moduleFields;
	}

	public function getFieldNameListByType($type) {
		$type = strtolower($type);
		$typeList = array();
		foreach ($this->moduleFields as $fieldName => $webserviceField) {
			if (strcmp($webserviceField->getFieldDataType(), $type) === 0) {
				$typeList[] = $fieldName;
			}
		}
		return $typeList;
	}

	public function getFieldListByType($type) {
		$type = strtolower($type);
		$typeList = array();
		foreach ($this->moduleFields as $webserviceField) {
			if (strcmp($webserviceField->getFieldDataType(), $type) === 0) {
				$typeList[] = $webserviceField;
			}
		}
		return $typeList;
	}

	public function getIdColumn() {
		return $this->idColumn;
	}

	public function getEntityBaseTable() {
		return $this->baseTable;
	}

	public function getEntityTableIndexList() {
		return $this->tableIndexList;
	}

	public function getEntityDefaultTableList() {
		return $this->defaultTableList;
	}

	public function getEntityTableList() {
		return $this->tableList;
	}

	public function getEntityAccessControlQuery() {
		return '';
	}

	public function getEntityDeletedQuery() {
		if ($this->getEntityName() == 'Leads') {
			$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
			return "vtiger_crmentity.deleted=0 and vtiger_leaddetails.converted=$val_conv";
		}
		if ($this->getEntityName() != 'Users') {
			return 'vtiger_crmentity.deleted=0';
		}
		// not sure whether inactive users should be considered deleted or not.
		return "vtiger_users.status='Active'";
	}

	abstract public function hasPermission($operation, $webserviceId);
	abstract public function hasAssignPrivilege($ownerWebserviceId);
	abstract public function hasDeleteAccess();
	abstract public function hasAccess();
	abstract public function hasReadAccess();
	abstract public function hasWriteAccess();
	abstract public function getEntityName();
	abstract public function getEntityId();
	abstract public function exists($recordId);
	abstract public function getObjectEntityName($webserviceId);
	abstract public function getNameFields();
	abstract public function getName($webserviceId);
	abstract public function isModuleEntity();
}
?>