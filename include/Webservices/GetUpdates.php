<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/CommonUtils.php';

function vtws_sync($mtime, $elementType, $syncType = '', $user = '') {
	global $adb;
	$ignoreModules = array('Users');
	$typed = true;
	$dformat = 'Y-m-d H:i:s';
	$datetime = date($dformat, $mtime);

	$output = array();
	$output['updated'] = array();
	$output['deleted'] = array();

	$applicationSync = false;
	if (is_object($syncType) && ($syncType instanceof Users)) {
		$user = $syncType;
	} elseif ($syncType == 'application') {
		$applicationSync = true;
	}

	if ($applicationSync && !is_admin($user)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Only admin users can perform application sync');
	}
	global $cbodCSAppSyncUser;
	if (in_array($user->id, $cbodCSAppSyncUser)) {
		$applicationSync = true;
	}

	$ownerIds = array($user->id);

	if (!isset($elementType) || $elementType=='' || $elementType==null) {
		$typed=false;
	}

	$adb->startTransaction();

	$accessableModules = array();
	$entityModules = array();
	$modulesDetails = vtws_listtypes(null, $user);
	$modulesInformation = $modulesDetails['information'];

	foreach ($modulesInformation as $moduleName => $entityInformation) {
		if ($entityInformation['isEntity']) {
			$entityModules[] = $moduleName;
		}
	}
	if (!$typed) {
		$accessableModules = $entityModules;
	} else {
		$elementType = explode(',', $elementType);
		if (empty(array_intersect($elementType, $entityModules))) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
		}
		$accessableModules = $elementType;
	}

	$accessableModules = array_diff($accessableModules, $ignoreModules);

	if (empty($accessableModules)) {
		$output['lastModifiedTime'] = $mtime;
		$output['more'] = false;
		return $output;
	}

	if ($typed && count($elementType)==1) {
		$baseCRMTable = CRMEntity::getcrmEntityTableAlias($elementType[0], true);
	} else {
		$baseCRMTable = ' vtiger_crmobject ';
	}

	//modifiedtime - next token
	$q = "SELECT modifiedtime FROM $baseCRMTable WHERE  modifiedtime>? and setype IN(".generateQuestionMarks($accessableModules).') ';
	$params = array($datetime);
	foreach ($accessableModules as $entityModule) {
		$params[] = $entityModule;
	}
	if (!$applicationSync) {
		$q .= ' and smownerid IN('.generateQuestionMarks($ownerIds).')';
		$params = array_merge($params, $ownerIds);
	}

	$streamraw = (isset($_REQUEST['format']) && strtolower($_REQUEST['format'])=='streamraw');
	$streaming = (isset($_REQUEST['format']) && (strtolower($_REQUEST['format'])=='stream' || $streamraw));
	$numRecordsLimit = GlobalVariable::getVariable('Webservice_Sync_RecordLimit'.($streaming ? 'Streaming' : ''), 100, $accessableModules[0]);
	$q .=" order by modifiedtime limit $numRecordsLimit";
	$result = $adb->pquery($q, $params);

	$modTime = array();
	for ($i=0; $i<$adb->num_rows($result); $i++) {
		$modTime[] = $adb->query_result($result, $i, 'modifiedtime');
	}
	if (!empty($modTime)) {
		$maxModifiedTime = max($modTime);
	} else {
		$maxModifiedTime = $datetime;
	}
	$stream = '';
	foreach ($accessableModules as $elementType) {
		$handler = vtws_getModuleHandlerFromName($elementType, $user);
		$moduleMeta = $handler->getMeta();
		$deletedQueryCondition = $moduleMeta->getEntityDeletedQuery();
		preg_match_all("/(?:\s+\w+[ \t\n\r]+)?([^=]+)\s*=(\S+|'[^']+')/", $deletedQueryCondition, $deletedFieldDetails);
		$fieldNameDetails = $deletedFieldDetails[1];
		$deleteFieldValues = $deletedFieldDetails[2];
		$deleteColumnNames = array();
		foreach ($fieldNameDetails as $tableName_fieldName) {
			$fieldComp = explode('.', $tableName_fieldName);
			$deleteColumnNames[$tableName_fieldName] = $fieldComp[1];
		}
		$params = array($moduleMeta->getTabName(),$datetime,$maxModifiedTime);

		$queryGenerator = new QueryGenerator($elementType, $user);
		$moduleFields = $moduleMeta->getModuleFields();
		$moduleFieldNames = array_keys($moduleFields);
		$moduleFieldNames[]='id';
		$queryGenerator->setFields($moduleFieldNames);
		$selectClause = 'SELECT '.$queryGenerator->getSelectClauseColumnSQL();
		// adding the fieldnames that are present in the delete condition to the select clause
		// since not all fields present in delete condition will be present in the fieldnames of the module
		foreach ($deleteColumnNames as $table_fieldName => $columnName) {
			if (!in_array($columnName, $moduleFieldNames)) {
				$selectClause .= ', '.$table_fieldName;
			}
		}
		if ($elementType=='Emails') {
			$fromClause = vtws_getEmailFromClause();
		} else {
			$fromClause = $queryGenerator->getFromClause();
		}
		$fromClause .= " INNER JOIN (select modifiedtime, crmid,deleted,setype FROM $baseCRMTable WHERE setype=? and modifiedtime >? and modifiedtime<=?";
		if (!$applicationSync) {
			$fromClause.= 'and smownerid IN('.generateQuestionMarks($ownerIds).')';
			$params = array_merge($params, $ownerIds);
		}
		$fromClause.= ' ) vtiger_ws_sync ON ('.$moduleMeta->baseTable.'.'.$moduleMeta->idColumn.'=vtiger_ws_sync.crmid)';
		$q = $selectClause.' '.$fromClause;
		$result = $adb->pquery($q, $params);
		while ($arre = $adb->fetchByAssoc($result)) {
			$key = $arre[$moduleMeta->getIdColumn()];
			if (vtws_isRecordDeleted($arre, $deleteColumnNames, $deleteFieldValues)) {
				if (!$moduleMeta->hasAccess()) {
					continue;
				}
				if ($streaming) {
					$stream .= json_encode(array('action' => 'deleted', 'record' => vtws_getId($moduleMeta->getEntityId(), $key)))."\n";
					if (($i % 500)==0) {
						echo $stream;
						flush();
						$stream = '';
					}
				} else {
					$output['deleted'][] = vtws_getId($moduleMeta->getEntityId(), $key);
				}
			} else {
				if (!$moduleMeta->hasAccess() ||!$moduleMeta->hasPermission(EntityMeta::$RETRIEVE, $key)) {
					continue;
				}
				try {
					if ($streaming) {
						$stream .= json_encode(array(
							'action' => 'updated',
							'record' => ($streamraw ? $arre : DataTransform::sanitizeDataWithColumn($arre, $moduleMeta)),
						))."\n";
						if (($i % 500)==0) {
							echo $stream;
							flush();
							$stream = '';
						}
					} else {
						$output['updated'][] = DataTransform::sanitizeDataWithColumn($arre, $moduleMeta);
					}
				} catch (WebServiceException $e) {
					//ignore records the user doesn't have access to.
					continue;
				} catch (Exception $e) {
					throw new WebServiceException(WebServiceErrorCode::$INTERNALERROR, 'Unknown Error while processing request');
				}
			}
			if ($stream!='') {
				echo $stream;
				flush();
				$stream = '';
			}
		}
	}

	$q = "SELECT count(*) as cnt FROM $baseCRMTable WHERE modifiedtime>? and setype IN(".generateQuestionMarks($accessableModules).')';
	$params = array($maxModifiedTime);

	foreach ($accessableModules as $entityModule) {
		$params[] = $entityModule;
	}
	if (!$applicationSync) {
		$q.='and smownerid IN('.generateQuestionMarks($ownerIds).')';
		$params = array_merge($params, $ownerIds);
	}
	$result = $adb->pquery($q, $params);
	$output['more'] = ($adb->query_result($result, 0, 'cnt')>0);
	if (!$maxModifiedTime) {
		$modifiedtime = $mtime;
	} else {
		$modifiedtime = vtws_getSeconds($maxModifiedTime);
	}
	if (is_string($modifiedtime)) {
		$modifiedtime = (int)$modifiedtime;
	}
	$output['lastModifiedTime'] = $modifiedtime;

	$error = $adb->hasFailedTransaction();
	$adb->completeTransaction();

	if ($error) {
		throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
	}

	VTWS_PreserveGlobal::flush();
	return $output;
}

function vtws_getSeconds($mtimeString) {
	return strtotime($mtimeString);
}

function vtws_isRecordDeleted($recordDetails, $deleteColumnDetails, $deletedValues) {
	$deletedRecord = false;
	$i=0;
	foreach ($deleteColumnDetails as $columnName) {
		if ($recordDetails[$columnName]!=$deletedValues[$i++]) {
			$deletedRecord = true;
			break;
		}
	}
	return $deletedRecord;
}

function vtws_getEmailFromClause() {
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCalendar');
	return 'FROM vtiger_activity
		INNER JOIN '.$crmEntityTable.' ON vtiger_activity.activityid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
		LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid
		LEFT JOIN vtiger_seattachmentsrel ON vtiger_activity.activityid = vtiger_seattachmentsrel.crmid
		LEFT JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
		LEFT JOIN vtiger_email_track ON vtiger_activity.activityid = vtiger_email_track.mailid
		INNER JOIN vtiger_emaildetails ON vtiger_activity.activityid = vtiger_emaildetails.emailid
		LEFT JOIN vtiger_users vtiger_users2 ON vtiger_emaildetails.idlists = vtiger_users2.id
		LEFT JOIN vtiger_groups vtiger_groups2 ON vtiger_emaildetails.idlists = vtiger_groups2.groupid';
}
?>
