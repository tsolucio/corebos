<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/VtigerCRMActorMeta.php';
require_once 'modules/ModTracker/ModTrackerUtils.php';

class ModTrackerOperation extends WebserviceEntityOperation {
	protected $entityTableName = 'vtiger_modtracker_basic';
	protected $moduleFields;
	protected $isEntity = false;
	protected $element;
	protected $id;
	private $queryTotalRows = 0;
	private $actorModule = 'ModTracker';

	public function __construct($webserviceObject, $user, $adb, $log) {
		parent::__construct($webserviceObject, $user, $adb, $log);
		$this->meta = $this->getMetaInstance();
		$this->moduleFields = null;
		$this->element = null;
		$this->id = null;
	}

	protected function getMetaInstance() {
		if (empty(WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id])) {
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]
				= new VtigerCRMActorMeta($this->entityTableName, $this->webserviceObject, $this->pearDB, $this->user);
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->addAnotherTable('vtiger_modtracker_detail', 'id');
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->setmoduleField('whodid', 'uitype', '101');
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->setmoduleField('whodid', 'fieldDataType', 'reference');
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->setmoduleField('whodid', 'referenceList', array('Users'));
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->setmoduleField('crmid', 'uitype', '10');
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->setmoduleField('crmid', 'fieldDataType', 'reference');
			$referenceList = array();
			$infomodules = ModTrackerUtils::modTrac_getModuleinfo();
			foreach ($infomodules as $value) {
				$referenceList[] = $value['name'];
			}
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]->setmoduleField('crmid', 'referenceList', $referenceList);
		}
		return WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id];
	}

	public function getMeta() {
		return $this->meta;
	}

	public function create($elementType, $elem) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$OPERATIONNOTSUPPORTED)
		);
	}

	public function __retrieve($id) {
		$query = 'SELECT vtiger_modtracker_basic.*,vtiger_modtracker_detail.fieldname,vtiger_modtracker_detail.prevalue,vtiger_modtracker_detail.postvalue,vtiger_users.first_name,vtiger_users.last_name
			FROM vtiger_modtracker_basic
			INNER JOIN vtiger_modtracker_detail ON vtiger_modtracker_basic.id=vtiger_modtracker_detail.id
			INNER JOIN vtiger_users ON vtiger_users.id=whodid
			WHERE vtiger_modtracker_basic.id=?';
		$transactionSuccessful = vtws_runQueryAsTransaction($query, array($id), $result);
		if (!$transactionSuccessful) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		$db = $this->pearDB;
		if ($result) {
			$rowCount = $db->num_rows($result);
			if ($rowCount >0) {
				$this->element = $db->query_result_rowdata($result, 0);
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
		return DataTransform::filterAndSanitize($this->getElement(), $this->meta);
	}

	public function massRetrieve($wsIds) {
		global $adb;
		$rdo = array();
		$query = 'SELECT vtiger_modtracker_basic.*,vtiger_modtracker_detail.fieldname,vtiger_modtracker_detail.prevalue,vtiger_modtracker_detail.postvalue,vtiger_users.first_name,vtiger_users.last_name
			FROM vtiger_modtracker_basic
			INNER JOIN vtiger_modtracker_detail ON vtiger_modtracker_basic.id=vtiger_modtracker_detail.id
			INNER JOIN vtiger_users ON vtiger_users.id=whodid
			WHERE vtiger_modtracker_basic.id in ('.generateQuestionMarks($wsIds).')';
		array_walk(
			$wsIds,
			function (&$val, $idx) {
				if (strpos($val, 'x')>0) {
					list($wsid, $val) = explode('x', $val);
				}
			}
		);
		$rs = $adb->pquery($query, $wsIds);
		while (!$rs->EOF) {
			$rdo[] = DataTransform::filterAndSanitize($rs->FetchRow(), $this->meta);
		}
		return $rdo;
	}

	public function update($elem) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$OPERATIONNOTSUPPORTED)
		);
	}

	public function revise($elem) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$OPERATIONNOTSUPPORTED)
		);
	}

	public function delete($id) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$OPERATIONNOTSUPPORTED)
		);
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

	public function getFilterFields($module) {
		return array(
			'fields'=> array('id','module','crmid','fieldname','prevalue','postvalue','first_name','last_name'),
			'linkfields'=>array('id'),
			'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $this->actorModule)),
		);
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
		$mysql_query = $this->wsVTQL2SQL($q, $meta, $queryRelatedModules);
		return $this->querySQLResults($mysql_query, $q, $meta, $queryRelatedModules);
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