<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/VtigerCRMClickHouseMeta.php';

class VtigerClickHouseOperation extends WebserviceEntityOperation {
	protected $entityTableName;
	protected $moduleFields;
	protected $isEntity = false;
	protected $element;
	protected $id;
	private $queryTotalRows = 0;

	public function __construct($webserviceObject, $user, $adb, $log) {
		global $cdb;
		if (empty($cdb)) {
			$cdb = new ClickHouseDatabase();
			$cdb->connect();
		}
		parent::__construct($webserviceObject, $user, $cdb, $log);
		$this->entityTableName = $this->getActorTables();
		if ($this->entityTableName === null) {
			throw new WebServiceException(WebServiceErrorCode::$UNKNOWNENTITY, 'Entity is not associated with any tables');
		}
		$this->meta = $this->getMetaInstance();
		$this->moduleFields = null;
		$this->element = null;
		$this->id = null;
	}

	protected function getMetaInstance() {
		global $adb;
		if (empty(WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id])) {
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]
				= new VtigerCRMClickHouseMeta($this->entityTableName, $this->webserviceObject, $adb, $this->user);
		}
		return WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id];
	}

	protected function getActorTables() {
		global $adb;
		static $actorTables = array();

		if (isset($actorTables[$this->webserviceObject->getEntityName()])) {
			return $actorTables[$this->webserviceObject->getEntityName()];
		}
		$sql = 'select table_name from vtiger_ws_entity_tables where webservice_entity_id=?';
		$result = $adb->pquery($sql, array($this->webserviceObject->getEntityId()));
		$tableName = null;
		if ($result) {
			$rowCount = $adb->num_rows($result);
			for ($i=0; $i<$rowCount; ++$i) {
				$row = $adb->query_result_rowdata($result, $i);
				$tableName = $row['table_name'];
			}
			// Cache the result for further re-use
			$actorTables[$this->webserviceObject->getEntityName()] = $tableName;
		}
		return $tableName;
	}

	public function getMeta() {
		return $this->meta;
	}

	protected function getNextId($elementType, $element) {
		global $adb;
		if (strcasecmp($elementType, 'Groups') === 0) {
			$tableName='vtiger_users';
		} else {
			$tableName = $this->entityTableName;
		}
		$meta = $this->getMeta();
		if (strcasecmp($elementType, 'Groups') !== 0 && strcasecmp($elementType, 'Users') !== 0) {
			$sql = 'update '.$tableName.'_seq set id=(select max('.$meta->getIdColumn().") from $tableName)";
			$adb->pquery($sql, array());
		}
		return $adb->getUniqueId($tableName);
	}

	public function __create($elementType, $element) {
		require_once 'include/utils/utils.php';

		$this->id=$this->getNextId($elementType, $element);

		$element[$this->meta->getObectIndexColumn()] = $this->id;

		//Insert into group vtiger_table
		$query = "insert into {$this->entityTableName}(".implode(',', array_keys($element)).') values('.generateQuestionMarks(array_keys($element)).')';
		$result = null;
		return vtws_runQueryAsTransaction($query, array_values($element), $result);
	}

	public function create($elementType, $element) {
		if (isset($element['assigned_user_id'])) {
			unset($element['assigned_user_id']);
		}
		$this->pearDB->run_insert_data($this->entityTableName, $element);
		return $element;
	}

	protected function restrictFields($element, $selectedOnly = false) {
		$fields = $this->getModuleFields();
		$newElement = array();
		foreach ($fields as $field) {
			if (isset($element[$field['name']])) {
				$newElement[$field['name']] = $element[$field['name']];
			} elseif ($field['name'] != 'id' && !$selectedOnly) {
				$newElement[$field['name']] = '';
			}
		}
		return $newElement;
	}

	public function __retrieve($id) {
		$query = "select * from {$this->entityTableName} where {$this->meta->getObectIndexColumn()}=?";
		$transactionSuccessful = vtws_runQueryAsTransaction($query, array($id), $result);
		if (!$transactionSuccessful) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		if ($result) {
			$rowCount = $this->pearDB->num_rows($result);
			if ($rowCount >0) {
				$this->element = $this->pearDB->query_result_rowdata($result, 0);
				return true;
			}
		}
		return false;
	}

	public function retrieve($id) {
		$ids = vtws_getIdComponents($id);
		$elemId = $ids[1];
		$success = $this->__retrieve($elemId);
		if (!$success) {
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record not found');
		}
		$elem = $this->getElement();
		if (isset($elem['folderid']) && !isset($elem['id'])) {
			$elem['id'] = $elem['folderid'];
			unset($elem['folderid']);
		}
		return DataTransform::filterAndSanitize($elem, $this->meta);
	}

	public function massRetrieve($wsIds) {
		$wid = $this->meta->getEntityId();
		$rdo = array();
		$query = "select * from {$this->entityTableName} where {$this->meta->getObectIndexColumn()} in (" . generateQuestionMarks($wsIds) . ')';
		$rs = $this->pearDB->pquery($query, $wsIds);
		while (!$rs->EOF) {
			$elem = $rs->FetchRow();
			if (isset($elem['folderid']) && !isset($elem['id'])) {
				$elem['id'] = $elem['folderid'];
				unset($elem['folderid']);
			}
			$elemid = (empty($elem['id']) ? (empty($elem['groupid']) ? '0' : $elem['groupid']) : $elem['id']);
			$rdo[$wid.'x'.$elemid] = DataTransform::filterAndSanitize($elem, $this->meta);
		}
		return $rdo;
	}

	public function __update($element, $id) {
		$columnStr = 'set '.implode('=?,', array_keys($element)).' =? ';
		$query = 'update '.$this->entityTableName.' '.$columnStr.'where '. $this->meta->getObectIndexColumn().'=?';
		$params = array_values($element);
		$params[] = $id;
		$result = null;
		return vtws_runQueryAsTransaction($query, $params, $result);
	}

	public function update($element) {
		$ids = vtws_getIdComponents($element['id']);
		$element = DataTransform::sanitizeForInsert($element, $this->meta);
		$element = $this->restrictFields($element);

		$success = $this->__update($element, $ids[1]);
		if (!$success) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		return $this->retrieve(vtws_getId($this->meta->getEntityId(), $ids[1]));
	}

	public function __revise($element, $id) {
		return $this->__update($element, $id);
	}

	public function revise($element) {
		$ids = vtws_getIdComponents($element['id']);

		$element = DataTransform::sanitizeForInsert($element, $this->meta);
		$element = $this->restrictFields($element, true);

		$success = $this->__retrieve($ids[1]);
		if (!$success) {
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record not found');
		}

		$allDetails = $this->getElement();
		foreach ($allDetails as $index => $value) {
			if (!isset($element)) {
				$element[$index] = $value;
			}
		}
		$success = $this->__revise($element, $ids[1]);
		if (!$success) {
			throw new WebServiceException(
				WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR)
			);
		}

		return $this->retrieve(vtws_getId($this->meta->getEntityId(), $ids[1]));
	}

	public function __delete($elemId) {
		$result = null;
		$query = 'delete from '.$this->entityTableName.' where '. $this->meta->getObectIndexColumn().'=?';
		return vtws_runQueryAsTransaction($query, array($elemId), $result);
	}

	public function delete($id) {
		$ids = vtws_getIdComponents($id);
		$elemId = $ids[1];

		$success = $this->__delete($elemId);
		if (!$success) {
			throw new WebServiceException(
				WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR)
			);
		}
		return array('status'=>'successful');
	}

	public function describe($elementType) {
		$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');
		vtws_preserveGlobal('current_user', $this->user);
		$label = (isset($app_strings[$elementType])) ? $app_strings[$elementType] : $elementType;
		$createable = $this->meta->hasCreateAccess();
		$updateable = $this->meta->hasWriteAccess();
		$deleteable = $this->meta->hasDeleteAccess();
		$retrieveable = $this->meta->hasReadAccess();
		$fields = $this->getModuleFields();
		return array(
			'label'=>getTranslatedString($label, $this->meta->getPermissionModule()),
			'label_raw'=>$label,
			'name'=>$elementType,
			'createable'=>$createable,
			'updateable'=>$updateable,
			'deleteable'=>$deleteable,
			'retrieveable'=>$retrieveable,
			'fields'=>$fields,
			'idPrefix'=>$this->meta->getEntityId(),
			'isEntity'=>$this->isEntity,
			'labelFields'=>$this->meta->getNameFields()
		);
	}

	public function getFilterFields($elementType) {
		return $this->meta->getFilterFields($elementType);
	}

	public function getModuleFields() {
		if ($this->moduleFields === null) {
			$fields = array();
			foreach ($this->meta->getModuleFields() as $webserviceField) {
				$fields[] = $this->getDescribeFieldArray($webserviceField);
			}
			$this->moduleFields = $fields;
		}
		return $this->moduleFields;
	}

	public function getDescribeFieldArray($webserviceField) {
		$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');
		$fieldLabel = $webserviceField->getFieldLabelKey();
		if (isset($app_strings[$fieldLabel])) {
			$fieldLabel = $app_strings[$fieldLabel];
		}
		if (strcasecmp($webserviceField->getFieldName(), $this->meta->getObectIndexColumn()) === 0) {
			return $this->getIdField($fieldLabel);
		}

		$typeDetails = $this->getFieldTypeDetails($webserviceField);

		//set type name, in the type details array.
		$typeDetails['name'] = $webserviceField->getFieldDataType();
		$editable = $this->isEditable($webserviceField);

		$describeArray = array(
			'name' => $webserviceField->getFieldName(),
			'label' => getTranslatedString($fieldLabel, $this->meta->getPermissionModule()),
			'label_raw' => $fieldLabel,
			'mandatory' => $webserviceField->isMandatory(),
			'type' => $typeDetails,
			'nullable' => $webserviceField->isNullable(),
			'editable' => $editable
		);
		if ($webserviceField->hasDefault()) {
			$describeArray['default'] = $webserviceField->getDefault();
		}
		return $describeArray;
	}

	public function getIdField($label) {
		return array(
			'name'=>'id',
			'label'=> getTranslatedString($label, $this->meta->getPermissionModule()),
			'label_raw' => $label,
			'mandatory'=>false,
			'editable'=>false,
			'type'=>array('name'=>'autogenerated'),
			'nullable'=>false,
			'default'=>''
		);
	}

	public function wsVTQL2SQL($q, &$meta, &$queryRelatedModules) {
		$queryRelatedModules = array();
		$meta = null;
		$parser = new Parser($this->user, $q);
		$error = $parser->parse();
		if ($error) {
			return $parser->getError();
		}
		$mysql_query = $parser->getSql();
		$meta = $parser->getObjectMetaData();
		return $mysql_query;
	}

	public function query($q) {
		$this->pearDB->startTransaction();
		$mysql_query = str_replace($this->webserviceObject->getEntityName(), $this->entityTableName, $q);
		$result = $this->pearDB->pquery($mysql_query, array());
		$error = $this->pearDB->hasFailedTransaction();
		$this->pearDB->completeTransaction();
		if ($error) {
			throw new WebServiceException(
				WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR)
			);
		}

		$noofrows = $this->pearDB->num_rows($result);
		$output = array();
		for ($i=0; $i<$noofrows; $i++) {
			$row = $this->pearDB->fetch_array($result);
			$output[] = $row;
			$this->queryTotalRows++;
		}
		return $output;
	}

	public function querySQLResults($mysql_query, $q, $meta, $queryRelatedModules) {
		$this->pearDB->startTransaction();
		$result = $this->pearDB->pquery($mysql_query, array());
		$error = $this->pearDB->hasFailedTransaction();
		$this->pearDB->completeTransaction();

		if ($error) {
			throw new WebServiceException(
				WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR)
			);
		}

		$noofrows = $this->pearDB->num_rows($result);
		$output = array();
		for ($i=0; $i<$noofrows; $i++) {
			$row = $this->pearDB->fetchByAssoc($result, $i);
			if (!$meta->hasPermission(EntityMeta::$RETRIEVE, (isset($row['crmid']) ? $row['crmid'] : ''))) {
				continue;
			}
			$output[] = DataTransform::sanitizeDataWithColumn($row, $meta);
			$this->queryTotalRows++;
		}

		return $output;
	}

	public function getQueryTotalRows() {
		return $this->queryTotalRows;
	}

	protected function getElement() {
		return $this->element;
	}
}
?>