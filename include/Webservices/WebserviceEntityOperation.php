<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

abstract class WebserviceEntityOperation {
	protected $user;
	protected $log;
	protected $webserviceObject;
	protected $meta;
	protected $pearDB;

	protected static $metaCache = array();

	protected function __construct($webserviceObject, $user, $adb, $log) {
		$this->user = $user;
		$this->log = $log;
		$this->webserviceObject = $webserviceObject;
		$this->pearDB = $adb;
	}

	public function create($elementType, $element) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation Create is not supported for this entity'
		);
	}

	public function retrieve($id) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation Retrieve is not supported for this entity'
		);
	}

	public function update($element) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation Update is not supported for this entity'
		);
	}

	public function revise($element) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation Update is not supported for this entity'
		);
	}

	public function delete($id) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation delete is not supported for this entity'
		);
	}

	public function query($q) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation query is not supported for this entity'
		);
	}

	public function describe($elementType) {
		throw new WebServiceException(
			WebServiceErrorCode::$OPERATIONNOTSUPPORTED,
			'Operation describe is not supported for this entity'
		);
	}

	public function getFieldTypeDetails($webserviceField) {
		global $current_user, $adb;
		$typeDetails = array();
		switch ($webserviceField->getFieldDataType()) {
			case 'reference':
				$typeDetails['refersTo'] = $webserviceField->getReferenceList();
				if (in_array('DocumentFolders', $typeDetails['refersTo'])) {
					$fldrs = array();
					$fldwsid = vtws_getEntityId('DocumentFolders').'x';
					$res=$adb->pquery('select foldername,folderid from vtiger_attachmentsfolder order by foldername', array());
					for ($i=0; $i<$adb->num_rows($res); $i++) {
						$fid=$adb->query_result($res, $i, 'folderid');
						$fldrs[] = array(
							'value' => $fldwsid.$fid,
							'label' => $adb->query_result($res, $i, 'foldername'),
						);
					}
					$typeDetails['picklistValues'] = $fldrs;
				}
				if (in_array('Currency', $typeDetails['refersTo'])) {
					$crs = array();
					$cwsid = vtws_getEntityId('Currency').'x';
					$res=$adb->pquery("select * from vtiger_currency_info where currency_status = 'Active' and deleted=0", array());
					for ($i=0; $i<$adb->num_rows($res); $i++) {
						$cid=$adb->query_result($res, $i, 'id');
						$crs[] = array(
							'value' => $cwsid.$cid,
							'label' => $adb->query_result($res, $i, 'currency_name'),
						);
					}
					$typeDetails['picklistValues'] = $crs;
				}
				if ($webserviceField->getUIType()==77) {
					$mname = getTabModuleName($webserviceField->getTabId());
					$crs = array();
					$res=json_decode(vtws_getAssignedUserList($mname, $current_user), true);
					for ($i=0; $i<count($res); $i++) {
						$crs[] = array(
							'value' => $res[$i]['userid'],
							'label' => $res[$i]['username'],
						);
					}
					$typeDetails['picklistValues'] = $crs;
				}
				break;
			case 'multipicklist':
			case 'picklist':
				$typeDetails['picklistValues'] = $webserviceField->getPicklistDetails($webserviceField);
				$typeDetails['defaultValue'] = $typeDetails['picklistValues'][0]['value'];
				break;
			case 'file':
				$maxUploadSize = 0;
				$maxUploadSize = ini_get('upload_max_filesize');
				$maxUploadSize = strtolower($maxUploadSize);
				$maxUploadSize = explode('m', $maxUploadSize);
				$maxUploadSize = $maxUploadSize[0];
				if (!is_numeric($maxUploadSize)) {
					$maxUploadSize = 0;
				}
				$maxUploadSize = $maxUploadSize * 1000000;
				$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000);
				if ($upload_maxsize > $maxUploadSize) {
					$maxUploadSize = $upload_maxsize;
				}
				$typeDetails['maxUploadFileSize'] = $maxUploadSize;
				break;
			case 'date':
				$typeDetails['format'] = $this->user->date_format;
				break;
			case 'owner':
				$mname = getTabModuleName($webserviceField->getTabId());
				$typeDetails['assignto']['users'] = array(
					'label_raw' => 'Users',
					'label' => getTranslatedString('Users', $mname),
					'options' => json_decode(vtws_getAssignedUserList($mname, $current_user), true)
				);
				$typeDetails['assignto']['groups'] = array(
					'label_raw' => 'Groups',
					'label' => getTranslatedString('Groups', $mname),
					'options' => json_decode(vtws_getAssignedGroupList($mname, $current_user), true),
				);
		}
		return $typeDetails;
	}

	public function isEditable($webserviceField) {
		if (((int)$webserviceField->getDisplayType()) === 2 || strcasecmp($webserviceField->getFieldDataType(), "autogenerated") === 0
			|| strcasecmp($webserviceField->getFieldDataType(), "id")===0 || $webserviceField->isReadOnly() == true
		) {
			return false;
		}
		//uitype 70 is vtiger generated fields, such as (of vtiger_crmentity table) createdtime
		//and modified time fields.
		if ($webserviceField->getUIType() ==  70 || $webserviceField->getUIType() ==  4) {
			return false;
		}
		return true;
	}

	public function getIdField($label) {
		return array(
			'name'=>'id', 'label'=>$label, 'mandatory'=>false, 'type'=>'id', 'editable'=>false,
			'type'=>array('name'=>'autogenerated'), 'nullable'=>false, 'default'=>''
		);
	}

	/**
	 * @return Intance of EntityMeta class.
	 *
	 */
	abstract public function getMeta();
	abstract protected function getMetaInstance();
}
?>