<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/WSAPP/WSAPP.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/Webservices/GetUpdates.php';
require_once 'include/utils/CommonUtils.php';
require_once 'modules/WSAPP/Utils.php';
require_once 'include/Webservices/Update.php';
require_once 'include/Webservices/Revise.php';
require_once 'modules/WSAPP/Handlers/SyncHandler.php';

class vtigerCRMHandler extends SyncHandler {

    private $assignToChangedRecords;
	protected $clientSyncType='user';

    public function  __construct($appkey){
        $this->key = $appkey;
        $this->assignToChangedRecords = array();
    }

    public function get($module,$token,$user){
        $syncModule = $module;
        $this->user = $user;
        $syncModule = $module;
		$syncType = 'user';
		if(!$this->isClientUserSyncType()){
			$syncType = 'application';
		}
		$result = vtws_sync($token, $syncModule, $syncType, $this->user);
        
        $result['updated']  = $this->translateTheReferenceFieldIdsToName($result['updated'],$syncModule,$user);
        return $this->nativeToSyncFormat($result);
    }

    public function put($recordDetails,$user){
        $this->user = $user;
        $recordDetails = $this->syncToNativeFormat($recordDetails);
        $createdRecords = $recordDetails['created'];
        $updatedRecords = $recordDetails['updated'];
        $deletedRecords = $recordDetails['deleted'];

        if(count($createdRecords)>0){
            $createdRecords = $this->translateReferenceFieldNamesToIds($createdRecords,$user);
			$createdRecords = $this->fillNonExistingMandatoryPicklistValues($createdRecords);
		}
        foreach($createdRecords as $index=>$record){
            $createdRecords[$index] = vtws_create($record['module'], $record, $this->user);
        }
        
        if(count($updatedRecords) >0){
            $updatedRecords = $this->translateReferenceFieldNamesToIds($updatedRecords,$user);
        }
        
        $crmIds = array();
        foreach($updatedRecords as $index=>$record){
            $webserviceRecordId = $record["id"];
            $recordIdComp = vtws_getIdComponents($webserviceRecordId);
            $crmIds[] = $recordIdComp[1];

        }
        $assignedRecordIds = wsapp_checkIfRecordsAssignToUser($crmIds, $this->user->id);
        foreach($updatedRecords as $index=>$record){
            $webserviceRecordId = $record["id"];
            $recordIdComp = vtws_getIdComponents($webserviceRecordId);
            if(in_array($recordIdComp[1], $assignedRecordIds))
            {
                $updatedRecords[$index] = vtws_revise($record, $this->user);
            }
            else{
                $this->assignToChangedRecords[$index] = $record;
            }
        }
        $hasDeleteAccess = null;
        $deletedCrmIds = array();
        foreach($deletedRecords as $index=>$record){
            $webserviceRecordId = $record;
            $recordIdComp = vtws_getIdComponents($webserviceRecordId);
            $deletedCrmIds[] = $recordIdComp[1];
        }
        $assignedDeletedRecordIds = wsapp_checkIfRecordsAssignToUser($deletedCrmIds, $this->user->id);
        foreach($deletedRecords as $index=>$record){
            $idComp = vtws_getIdComponents($record);
            if(empty ($hasDeleteAccess)){
             $handler = vtws_getModuleHandlerFromId($idComp[0], $this->user);
             $meta = $handler->getMeta();
             $hasDeleteAccess = $meta->hasDeleteAccess();
            }
            if($hasDeleteAccess){
                if(in_array($idComp[1], $assignedDeletedRecordIds)){
                    vtws_delete($record, $this->user);
                }
            }
        }
        $recordDetails['created'] = $createdRecords;
        $recordDetails['updated'] = $updatedRecords;
        $recordDetails['deleted'] = $deletedRecords;
		return $this->nativeToSyncFormat($recordDetails);
    }

    public function nativeToSyncFormat($element){
        return $element;
    }

    public function syncToNativeFormat($element){
        $syncCreatedRecords = $element['created'];
        $nativeCreatedRecords = array();
        foreach($syncCreatedRecords as $index=>$createRecord){
            if(empty($createRecord['assigned_user_id'])){
                $createRecord['assigned_user_id'] = vtws_getWebserviceEntityId("Users",$this->user->id);
            }
            $nativeCreatedRecords[$index] = $createRecord;
        }
        $element['created'] = $nativeCreatedRecords;
        return $element;
    }

    public function map($element,$user){
        return $element;
    }

    public function translateReferenceFieldNamesToIds($entityRecords,$user){
        $entityRecordList = array();
        foreach($entityRecords as $index=>$record){
            $entityRecordList[$record['module']][$index] = $record;
        }
        foreach($entityRecordList as $module=>$records){
            $handler = vtws_getModuleHandlerFromName($module, $user);
            $meta = $handler->getMeta();
            $referenceFieldDetails = $meta->getReferenceFieldDetails();

            foreach($referenceFieldDetails as $referenceFieldName=>$referenceModuleDetails){
                $recordReferenceFieldNames = array();
                foreach($records as $index=>$recordDetails){
                    if(!empty($recordDetails[$referenceFieldName])) {
                    $recordReferenceFieldNames[$recordDetails['id']] = $recordDetails[$referenceFieldName];
                }
                }
                $entityNameIds = wsapp_getRecordEntityNameIds(array_values($recordReferenceFieldNames), $referenceModuleDetails, $user);
                foreach($records as $index=>$recordInfo){
                    if(!empty($entityNameIds[$recordInfo[$referenceFieldName]])){
                        $recordInfo[$referenceFieldName] = $entityNameIds[$recordInfo[$referenceFieldName]];
                    } else {
                        $recordInfo[$referenceFieldName] = "";
                    }
                    $records[$index] = $recordInfo;
                }
            }
            $entityRecordList[$module] = $records;
        }

        $crmRecords = array();
        foreach($entityRecordList as $module=>$entityRecords){
            foreach($entityRecords as $index=>$record){
                $crmRecords[$index] = $record;
            }
        }
        return $crmRecords;
    }

    public function translateTheReferenceFieldIdsToName($records,$module,$user){
        $db = PearDatabase::getInstance();
        global $current_user;
        $current_user = $user;
        $handler = vtws_getModuleHandlerFromName($module, $user);
        $meta = $handler->getMeta();
        $referenceFieldDetails = $meta->getReferenceFieldDetails();
        foreach($referenceFieldDetails as $referenceFieldName=>$referenceModuleDetails){
            $referenceFieldIds = array();
            $referenceModuleIds = array();
            $referenceIdsName = array();
            foreach($records as $recordDetails){
                $referenceWsId = $recordDetails[$referenceFieldName];
                if(!empty ($referenceWsId)){
                    $referenceIdComp = vtws_getIdComponents($referenceWsId);
                    $webserviceObject = VtigerWebserviceObject::fromId($db, $referenceIdComp[0]);
                    $referenceModuleIds[$webserviceObject->getEntityName()][]= $referenceIdComp[1];
                    $referenceFieldIds[] =$referenceIdComp[1];
                }
            }

            foreach($referenceModuleIds as $referenceModule=>$idLists){
                $nameList = getEntityName($referenceModule, $idLists);
                foreach($nameList as $key=>$value)
                    $referenceIdsName[$key] = $value;
            }
	        $recordCount = count($records);
            for($i=0;$i<$recordCount;$i++){
                $record = $records[$i];
                if(!empty($record[$referenceFieldName])){
                    $wsId = vtws_getIdComponents($record[$referenceFieldName]);
                    $record[$referenceFieldName] = decode_html($referenceIdsName[$wsId[1]]);
                }
                $records[$i]= $record;
            }
        }
        return $records;
    }

    public function getAssignToChangedRecords(){
        return $this->assignToChangedRecords;
    }

	
	public function fillNonExistingMandatoryPicklistValues($recordList){
		//Meta is cached to eliminate overhead of doing the query every time to get the meta details(retrieveMeta)
		$modulesMetaCache = array();
		foreach($recordList as $index=>$recordDetails){
			if(!array_key_exists($recordDetails['module'], $modulesMetaCache)){
				$handler = vtws_getModuleHandlerFromName($recordDetails['module'], $this->user);
				$meta = $handler->getMeta();
				$modulesMetaCache[$recordDetails['module']] = $meta;
			}
			$moduleMeta = $modulesMetaCache[$recordDetails['module']];
			$mandatoryFieldsList = $meta->getMandatoryFields();
			$moduleFields = $meta->getModuleFields();
			foreach($mandatoryFieldsList as $fieldName){
				$fieldInstance = $moduleFields[$fieldName];
				if(empty($recordDetails[$fieldName]) &&
						($fieldInstance->getFieldDataType()=="multipicklist" || $fieldInstance->getFieldDataType()=="picklist")){
					$pickListDetails = $fieldInstance->getPicklistDetails($webserviceField);
					$defaultValue = $pickListDetails[0]['value'];
					$recordDetails[$fieldName] = $defaultValue;
				}
			}
			$recordList[$index]= $recordDetails;
		}
		return $recordList;
	}

	public function setClientSyncType($syncType='user'){
		$this->clientSyncType = $syncType;
		return $this;
	}

	public function isClientUserSyncType(){
		return ($this->clientSyncType == 'user')?true:false;
	}
}
?>
