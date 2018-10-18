<?php
/* +*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************** */
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Delete.php';
require_once 'include/Webservices/DescribeObject.php';

function vtws_convertlead($entityvalues, $user) {

	global $adb, $log;
	if (empty($entityvalues['assignedTo'])) {
		$entityvalues['assignedTo'] = vtws_getWebserviceEntityId('Users', $user->id);
	}
	if (empty($entityvalues['transferRelatedRecordsTo'])) {
		if ($entityvalues['entities']['Contacts']['create']) {
			$entityvalues['transferRelatedRecordsTo'] = 'Contacts';
		} else {
			$entityvalues['transferRelatedRecordsTo'] = 'Accounts';
		}
	}

	$leadObject = VtigerWebserviceObject::fromName($adb, 'Leads');
	$handlerPath = $leadObject->getHandlerPath();
	$handlerClass = $leadObject->getHandlerClass();

	require_once $handlerPath;

	$leadHandler = new $handlerClass($leadObject, $user, $adb, $log);

	$leadInfo = vtws_retrieve($entityvalues['leadId'], $user);
	$leadIdComponents = vtws_getIdComponents($entityvalues['leadId']);
	$result = $adb->pquery('select converted from vtiger_leaddetails where converted = 1 and leadid=?', array($leadIdComponents[1]));
	if ($result === false) {
		throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_' . WebServiceErrorCode::$DATABASEQUERYERROR));
	}
	$rowCount = $adb->num_rows($result);
	if ($rowCount > 0) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED, 'Lead is already converted');
	}

	$entityIds = array();

	$availableModules = array('Accounts', 'Contacts', 'Potentials');

	if (!(isset($entityvalues['entities']['Accounts']['create']) || isset($entityvalues['entities']['Contacts']['create']))) {
		return null;
	}

	foreach ($availableModules as $entityName) {
		if (isset($entityvalues['entities'][$entityName]['create'])) {
			$entityvalue = $entityvalues['entities'][$entityName];
			$entityObject = VtigerWebserviceObject::fromName($adb, $entityvalue['name']);
			$handlerPath = $entityObject->getHandlerPath();
			$handlerClass = $entityObject->getHandlerClass();

			require_once $handlerPath;

			$entityHandler = new $handlerClass($entityObject, $user, $adb, $log);

			$entityObjectValues = array();
			$entityObjectValues['assigned_user_id'] = $entityvalues['assignedTo'];
			$entityObjectValues = vtws_populateConvertLeadEntities($entityvalue, $entityObjectValues, $entityHandler, $leadHandler, $leadInfo);

			//update potential related to property
			if ($entityvalue['name'] == 'Potentials') {
				if (!empty($entityIds['Accounts'])) {
					$entityObjectValues['related_to'] = $entityIds['Accounts'];
				} else {
					$entityObjectValues['related_to'] = $entityIds['Contacts'];
				}
			}

			//update the contacts relation
			if ($entityvalue['name'] == 'Contacts') {
				if (!empty($entityIds['Accounts'])) {
					$entityObjectValues['account_id'] = $entityIds['Accounts'];
				}
			}

			try {
				$create = true;
				if ($entityvalue['name'] == 'Accounts' && empty($entityvalue['forcecreate'])) {
					$sql = 'SELECT vtiger_account.accountid
						FROM vtiger_account, vtiger_crmentity
						WHERE vtiger_crmentity.crmid=vtiger_account.accountid AND vtiger_account.accountname=? AND vtiger_crmentity.deleted=0';
					$result = $adb->pquery($sql, array($entityvalue['accountname']));
					if ($adb->num_rows($result) > 0) {
						$entityIds[$entityName] = vtws_getWebserviceEntityId('Accounts', $adb->query_result($result, 0, 'accountid'));
						$create = false;
					}
				}
				if ($create) {
					$entityRecord = vtws_create($entityvalue['name'], $entityObjectValues, $user);
					$entityIds[$entityName] = $entityRecord['id'];
				}
			} catch (Exception $e) {
				throw new WebServiceException(WebServiceErrorCode::$UNKNOWNOPERATION, $e->getMessage().' : '.$entityvalue['name']);
			}
		}
	}

	try {
		if (isset($entityIds['Accounts'])) {
			$accountIdComponents = vtws_getIdComponents($entityIds['Accounts']);
			$accountId = $accountIdComponents[1];
		} else {
			$accountId = 0;
		}
		if (isset($entityIds['Contacts'])) {
			$contactIdComponents = vtws_getIdComponents($entityIds['Contacts']);
			$contactId = $contactIdComponents[1];
		} else {
			$contactId = 0;
		}
		if (!empty($accountId) && !empty($contactId) && !empty($entityIds['Potentials'])) {
			$potentialIdComponents = vtws_getIdComponents($entityIds['Potentials']);
			$result = $adb->pquery('insert into vtiger_contpotentialrel values(?,?)', array($contactId, $potentialIdComponents[1]));
			if ($result === false) {
				throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_CREATE_RELATION, 'Failed to related Contact with the Potential');
			}
		}

		vtws_convertLeadTransferHandler($leadIdComponents, $entityIds, $entityvalues);

		$relatedIdComponents = vtws_getIdComponents($entityIds[$entityvalues['transferRelatedRecordsTo']]);
		vtws_getRelatedActivities($leadIdComponents[1], $accountId, $contactId, $relatedIdComponents[1]);
		vtws_updateConvertLeadStatus($entityIds, $entityvalues['leadId'], $user);
	} catch (Exception $e) {
		foreach ($entityIds as $id) {
			vtws_delete($id, $user);
		}
		return null;
	}

	vtws_createEntities($entityIds, $entityvalues['leadId']);

	return $entityIds;
}

/*
 * populate the entity fields with the lead info.
 * if mandatory field is not provided populate with '????'
 * returns the entity array.
 */

function vtws_populateConvertLeadEntities($entityvalue, $entity, $entityHandler, $leadHandler, $leadinfo) {
	global $adb;
	$entityName = $entityvalue['name'];
	$result = $adb->pquery('SELECT * FROM vtiger_convertleadmapping', array());
	if ($adb->num_rows($result)) {
		switch ($entityName) {
			case 'Accounts':
				$column = 'accountfid';
				break;
			case 'Contacts':
				$column = 'contactfid';
				break;
			case 'Potentials':
				$column = 'potentialfid';
				break;
			default:
				$column = 'leadfid';
				break;
		}

		$leadFields = $leadHandler->getMeta()->getModuleFields();
		$entityFields = $entityHandler->getMeta()->getModuleFields();
		$row = $adb->fetch_array($result);
		$count = 1;
		do {
			$entityField = vtws_getFieldfromFieldId($row[$column], $entityFields);
			if ($entityField == null) {
				//user doesn't have access so continue.TODO update even if user doesn't have access
				continue;
			}
			$leadField = vtws_getFieldfromFieldId($row['leadfid'], $leadFields);
			if ($leadField == null) {
				//user doesn't have access so continue.TODO update even if user doesn't have access
				continue;
			}
			$leadFieldName = $leadField->getFieldName();
			$entityFieldName = $entityField->getFieldName();
			$entity[$entityFieldName] = $leadinfo[$leadFieldName];
			$count++;
		} while ($row = $adb->fetch_array($result));

		foreach ($entityvalue as $fieldname => $fieldvalue) {
			if (!empty($fieldvalue)) {
				$entity[$fieldname] = $fieldvalue;
			}
		}

		$entity = vtws_validateConvertLeadEntityMandatoryValues($entity, $entityHandler, $leadinfo, $entityName);
	}
	return $entity;
}

function vtws_validateConvertLeadEntityMandatoryValues($entity, $entityHandler, $leadinfo, $module) {
	$mandatoryFields = $entityHandler->getMeta()->getMandatoryFields();
	foreach ($mandatoryFields as $field) {
		if (empty($entity[$field])) {
			$fieldInfo = vtws_getConvertLeadFieldInfo($module, $field);
			if (($fieldInfo['type']['name'] == 'picklist' || $fieldInfo['type']['name'] == 'multipicklist'
					|| $fieldInfo['type']['name'] == 'date' || $fieldInfo['type']['name'] == 'datetime')
				&& ($fieldInfo['editable'] == true)
			) {
				$entity[$field] = $fieldInfo['default'];
			} else {
				$entity[$field] = '????';
			}
		}
	}
	return $entity;
}

function vtws_getConvertLeadFieldInfo($module, $fieldname) {
	global $current_user;
	$describe = vtws_describe($module, $current_user);
	foreach ($describe['fields'] as $fieldInfo) {
		if ($fieldInfo['name'] == $fieldname) {
			return $fieldInfo;
		}
	}
	return false;
}

//function to handle the transferring of related records for lead
function vtws_convertLeadTransferHandler($leadIdComponents, $entityIds, $entityvalues) {
	try {
		$entityidComponents = vtws_getIdComponents($entityIds[$entityvalues['transferRelatedRecordsTo']]);
		vtws_transferLeadRelatedRecords($leadIdComponents[1], $entityidComponents[1], $entityvalues['transferRelatedRecordsTo']);
	} catch (Exception $e) {
		return false;
	}
	return true;
}

function vtws_updateConvertLeadStatus($entityIds, $leadId, $user) {
	global $adb;
	$leadIdComponents = vtws_getIdComponents($leadId);
	if (!empty($entityIds['Accounts']) || !empty($entityIds['Contacts'])) {
		$result = $adb->pquery('UPDATE vtiger_leaddetails SET converted = 1 where leadid=?', array($leadIdComponents[1]));
		if ($result === false) {
			throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_MARK_CONVERTED, 'Failed mark lead converted');
		}
		//updating the campaign-lead relation
		$adb->pquery('DELETE FROM vtiger_campaignleadrel WHERE leadid=?', array($leadIdComponents[1]));

		$adb->pquery('DELETE FROM vtiger_tracker WHERE item_id=?', array($leadIdComponents[1]));

		//update the modifiedtime and modified by information for the record
		$leadModifiedTime = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$adb->pquery('UPDATE vtiger_crmentity SET modifiedtime=?, modifiedby=? WHERE crmid=?', array($leadModifiedTime, $user->id, $leadIdComponents[1]));
	}
	$moduleArray = array('Accounts','Contacts','Potentials');
	foreach ($moduleArray as $module) {
		if (!empty($entityIds[$module])) {
			$idComponents = vtws_getIdComponents($entityIds[$module]);
			$id = $idComponents[1];
			$webserviceModule = vtws_getModuleHandlerFromName($module, $user);
			$meta = $webserviceModule->getMeta();
			$fields = $meta->getModuleFields();
			$field = $fields['isconvertedfromlead'];
			$tablename = $field->getTableName();
			$tableList = $meta->getEntityTableIndexList();
			$tableIndex = $tableList[$tablename];
			$adb->pquery("UPDATE $tablename SET isconvertedfromlead = ?,convertedfromlead = ? WHERE $tableIndex = ?", array(1, $leadIdComponents[1], $id));
		}
	}
}

function vtws_createEntities($entityIds, $leadId) {
	$bmapname = 'LeadConversion';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	$entityIds['Leads'] = $leadId;
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$modules = $cbMap->ModuleSetMapping()->getFullModuleSet();
		$originModules = array('Leads', 'Accounts', 'Contacts', 'Potentials');
		$excludedModules = array('Leads', 'Potentials', 'Accounts', 'Contacts', 'Users');
		foreach ($modules as $module) {
			if (in_array($module, $excludedModules)) {
				continue;
			}
			$entityRecord = vtws_createEntity($entityIds, $originModules, $module);
			if ($entityRecord['id']) {
				$originModules[] = $module;
				$entityIds[$module] = $entityRecord['id'];
			}
		}
	}
}

function vtws_createEntity($recordid, $originMod, $targetMod) {
	global $adb,$current_user,$log;
	$return = 0;
	$newEntityInfo = CRMEntity::getInstance($targetMod);
	$mapfound = false;
	foreach ($originMod as $modName) {
		if ($recordid[$modName]) {
			$oldEntityInfo = CRMEntity::getInstance($modName);
			$modNameIdComponents = vtws_getIdComponents($recordid[$modName]);
			$oldEntityInfo->retrieve_entity_info($modNameIdComponents[1], $modName);
			$map_name = $modName.'2'.$targetMod;
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$map_name, cbMap::getMapIdByName($map_name));
			if ($cbMapid) {
				$mapfound = true;
				$cbMap = cbMap::getMapByID($cbMapid);
				$newEntityInfo->column_fields = $cbMap->Mapping($oldEntityInfo->column_fields, $newEntityInfo->column_fields);
			}
		}
	}
	if ($mapfound) {
		try {
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $targetMod);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$meta = $handler->getMeta();
			$values = DataTransform::sanitizeReferences($newEntityInfo->column_fields, $meta);
			$values = DataTransform::sanitizeOwnerFields($values, $meta);
			$return = vtws_create($targetMod, $values, $current_user);
		} catch (Exception $e) {
			throw new WebServiceException(WebServiceErrorCode::$UNKNOWNOPERATION, $e->getMessage().' : '.$targetMod);
		}
	}
	return $return;
}
?>
