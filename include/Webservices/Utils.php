<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
require_once 'modules/Users/Users.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once 'include/Webservices/VtigerCRMObject.php';
require_once 'include/Webservices/VtigerCRMObjectMeta.php';
require_once 'include/Webservices/DataTransform.php';
require_once 'include/Webservices/WebServiceError.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once 'include/Webservices/PreserveGlobal.php';

/** return all the users in the groups that the given user is part of.
 * @param integer id of the user
 * @return array user names of all the users in the groups that this user is part of indexed by their ID
 */
function vtws_getUsersInTheSameGroup($id) {
	require_once 'include/utils/GetGroupUsers.php';
	require_once 'include/utils/GetUserGroups.php';

	$groupUsers = new GetGroupUsers();
	$userGroups = new GetUserGroups();
	$allUsers = array();
	$userGroups->getAllUserGroups($id);
	$groups = $userGroups->user_groups;

	foreach ($groups as $group) {
		$groupUsers->getAllUsersInGroup($group);
		$usersInGroup = $groupUsers->group_users;
		foreach ($usersInGroup as $user) {
			if ($user != $id) {
				$allUsers[$user] = getUserFullName($user);
			}
		}
	}
	return $allUsers;
}

function vtws_generateRandomAccessKey($length = 10) {
	$source = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$accesskey = '';
	$maxIndex = strlen($source);
	for ($i=0; $i<$length; ++$i) {
		$accesskey = $accesskey.substr($source, rand(0, $maxIndex), 1);
	}
	return $accesskey;
}

/**
 * get current vtiger version from the database.
 */
function vtws_getVtigerVersion() {
	global $adb;
	$result = $adb->pquery('select current_version from vtiger_version', array());
	$version = '';
	while ($row = $adb->fetch_array($result)) {
		$version = $row['current_version'];
	}
	return $version;
}

function vtws_getUserAccessibleGroups($moduleId, $user) {
	global $adb;
	$userprivs = $user->getPrivileges();
	$tabName = getTabname($moduleId);
	if (!$userprivs->hasGlobalWritePermission() && !$userprivs->hasModuleWriteSharing($moduleId)) {
		$result=get_current_user_access_groups($tabName);
	} else {
		$result = get_group_options();
	}

	$groups = array();
	if ($result != null && $result != '' && is_object($result)) {
		$rowCount = $adb->num_rows($result);
		for ($i = 0; $i < $rowCount; $i++) {
			$nameArray = $adb->query_result_rowdata($result, $i);
			$groupId=$nameArray['groupid'];
			$groupName=$nameArray['groupname'];
			$groups[] = array('id'=>$groupId,'name'=>$groupName);
		}
	}
	return $groups;
}

function vtws_getWebserviceGroupFromGroups($groups) {
	global $adb;
	$webserviceObject = VtigerWebserviceObject::fromName($adb, 'Groups');
	foreach ($groups as $index => $group) {
		$groups[$index]['id'] = vtws_getId($webserviceObject->getEntityId(), $group['id']);
	}
	return $groups;
}

function vtws_getUserWebservicesGroups($tabId, $user) {
	$groups = vtws_getUserAccessibleGroups($tabId, $user);
	return vtws_getWebserviceGroupFromGroups($groups);
}

function vtws_getIdComponents($elementid) {
	return explode('x', $elementid);
}

function vtws_getId($objId, $elemId) {
	return $objId.'x'.$elemId;
}

function vtws_getEntityId($entityName) {
	global $adb;
	$wsrs=$adb->pquery('select id from vtiger_ws_entity where name=?', array($entityName));
	if ($wsrs && $adb->num_rows($wsrs)==1) {
		$wsid = $adb->query_result($wsrs, 0, 0);
	} else {
		$wsid = 0;
	}
	return $wsid;
}

function vtws_getEntityName($entityId) {
	global $adb;
	$result = $adb->pquery('select name from vtiger_ws_entity where id=?', array($entityId));
	if ($result && $adb->num_rows($result)>0) {
		return $result->fields['name'];
	}
	return '';
}

function vtws_getWSID($id) {
	if (strlen($id)==40) {
		$return = CRMEntity::getWSIDfromUUID($id);
		return ($return=='' ? '0x0' : $return);
	} elseif (preg_match('/^[0-9]+x[0-9]+$/', $id)) {
		return $id;
	} elseif (is_numeric($id)) {
		return vtws_getEntityId(getSalesEntityType($id)).'x'.$id;
	} else {
		return '0x0';
	}
}

function vtws_getCRMID($id) {
	if (strlen($id)==40) {
		return CRMEntity::getCRMIDfromUUID($id);
	} elseif (preg_match('/^[0-9]+x[0-9]+$/', $id)) {
		$parts = vtws_getIdComponents($id);
		return $parts[1];
	} elseif (is_numeric($id)) {
		return $id;
	} else {
		return 0;
	}
}

function getEmailFieldId($meta, $entityId) {
	global $adb;
	//no email field accessible in the module. since its only association pick up the field any way.
	$query = 'SELECT fieldid,fieldlabel,columnname FROM vtiger_field WHERE tabid=? and uitype=13 and presence in (0,2)';
	$result = $adb->pquery($query, array($meta->getTabId()));
	//pick up the first field.
	return $adb->query_result($result, 0, 'fieldid');
}

function vtws_stripSlashesRecursively($p) {
	if (is_array($p)) {
		return array_map('vtws_stripSlashesRecursively', $p);
	} else {
		return stripslashes($p);
	}
}

function vtws_addSlashesRecursively($p) {
	if (is_array($p)) {
		$p = array_map('vtws_addSlashesRecursively', $p);
	} else {
		$p = addslashes($p);
	}
	return $p;
}

function vtws_getParameter($parameterArray, $paramName, $default = null) {
	if (isset($parameterArray[$paramName])) {
		if (is_array($parameterArray[$paramName])) {
			$param = vtws_addSlashesRecursively($parameterArray[$paramName]);
		} else {
			$param = addslashes($parameterArray[$paramName]);
		}
	} else {
		$param = '';
	}
	if (!$param) {
		$param = $default;
	}
	return $param;
}

function vtws_getQueableCommands() {
	global $adb;
	$wsops = $adb->query('SELECT name FROM vtiger_ws_operation where queable=1');
	$queable = [];
	foreach ($adb->rowGenerator($wsops) as $wsop) {
		$queable[] = $wsop['name'];
	}
	return $queable;
}

function vtws_logcalls($input) {
	global $current_user, $application_unique_key;
	if (GlobalVariable::getVariable('Webservice_LogCallsToQueue', '')!='') {
		$appname = GlobalVariable::getVariable('Application_Unique_Identifier', $application_unique_key);
		$input['application'] = $appname;
		$input['donefrom'] = $_SERVER['REMOTE_ADDR'];
		unset($input['sessionName']);
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->sendMessage('WebServiceLogCalls', 'logwscall', 'logwscall', 'WSCall', '1:M', 1, 172800, 0, $current_user->id, json_encode($input));
	}
}

function vtws_getEntityNameFields($moduleName) {
	global $adb;
	$query = 'select fieldname,tablename,entityidfield from vtiger_entityname where modulename = ?';
	$result = $adb->pquery($query, array($moduleName));
	$rowCount = $adb->num_rows($result);
	$nameFields = array();
	if ($rowCount > 0) {
		$fieldsname = $adb->query_result($result, 0, 'fieldname');
		if (strpos($fieldsname, ',')) {
			 $nameFields = explode(',', $fieldsname);
		} else {
			$nameFields[] = $fieldsname;
		}
	}
	return $nameFields;
}

/** function to get the module List to which are crm entities.
 *  @return array modules list
 */
function vtws_getModuleNameList() {
	global $adb;
	$sql = "select name from vtiger_tab where isentitytype=1 and name not in ('Rss','Recyclebin') order by tabsequence";
	$res = $adb->pquery($sql, array());
	$mod_array = array();
	while ($row = $adb->fetchByAssoc($res)) {
		$mod_array[] = $row['name'];
	}
	return $mod_array;
}

function vtws_getWebserviceEntities() {
	global $adb;
	$res = $adb->pquery('select name,id,ismodule from vtiger_ws_entity', array());
	$moduleArray = array();
	$entityArray = array();
	while ($row = $adb->fetchByAssoc($res)) {
		if ($row['ismodule'] == '1') {
			$moduleArray[] = $row['name'];
		} else {
			$entityArray[] = $row['name'];
		}
	}
	return array('module'=>$moduleArray,'entity'=>$entityArray);
}

/**
 *
 * @param VtigerWebserviceObject $webserviceObject
 * @return CRMEntity
 */
function vtws_getModuleInstance($webserviceObject) {
	$moduleName = $webserviceObject->getEntityName();
	return CRMEntity::getInstance($moduleName);
}

function vtws_isRecordOwnerUser($ownerId) {
	global $adb;
	static $cache = array();
	if (is_array($ownerId) && isset($ownerId['Users'])) {
		$ownerId = $ownerId['Users'];
	}
	if (!array_key_exists($ownerId, $cache)) {
		$result = $adb->pquery('select first_name from vtiger_users where id = ?', array($ownerId));
		$rowCount = $adb->num_rows($result);
		$ownedByUser = ($rowCount > 0);
		$cache[$ownerId] = $ownedByUser;
	} else {
		$ownedByUser = $cache[$ownerId];
	}
	return $ownedByUser;
}

function vtws_isRecordOwnerGroup($ownerId) {
	global $adb;

	static $cache = array();
	if (!array_key_exists($ownerId, $cache)) {
		$result = $adb->pquery('select groupname from vtiger_groups where groupid = ?', array($ownerId));
		$rowCount = $adb->num_rows($result);
		$ownedByGroup = ($rowCount > 0);
		$cache[$ownerId] = $ownedByGroup;
	} else {
		$ownedByGroup = $cache[$ownerId];
	}
	return $ownedByGroup;
}

function vtws_getOwnerType($ownerId) {
	if (vtws_isRecordOwnerGroup($ownerId)) {
		return 'Groups';
	}
	if (vtws_isRecordOwnerUser($ownerId)) {
		return 'Users';
	}
	throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Invalid owner of the record');
}

function vtws_runQueryAsTransaction($query, $params, &$result) {
	global $adb;

	$adb->startTransaction();
	$result = $adb->pquery($query, $params);
	$error = $adb->hasFailedTransaction();
	$adb->completeTransaction();
	return !$error;
}

/**
 * @deprecated
 */
function vtws_getCalendarEntityType($id) {
	return 'cbCalendar';
}

/**
 * Get the webservice reference Id given the entity's id and it's type name
 */
function vtws_getWebserviceEntityId($entityName, $id) {
	global $adb;
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $entityName);
	return $webserviceObject->getEntityId().'x'.$id;
}

function vtws_addDefaultModuleTypeEntity($moduleName) {
	vtws_addModuleTypeWebserviceEntity($moduleName, 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation');
}

function vtws_addModuleTypeWebserviceEntity($moduleName, $filePath, $className) {
	global $adb;
	$checkres = $adb->pquery(
		'SELECT id FROM vtiger_ws_entity WHERE name=? AND handler_path=? AND handler_class=?',
		array($moduleName, $filePath, $className)
	);
	if ($checkres && $adb->num_rows($checkres) == 0) {
		$adb->pquery(
			'insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
			array($adb->getUniqueID('vtiger_ws_entity'), $moduleName, $filePath, $className, 1)
		);
	}
}

function vtws_deleteWebserviceEntity($moduleName) {
	global $adb;
	$adb->pquery('DELETE FROM vtiger_ws_entity WHERE name=?', array($moduleName));
}

function vtws_addDefaultActorTypeEntity($actorName, $actorNameDetails, $withName = true) {
	$actorHandler = array('file'=>'include/Webservices/VtigerActorOperation.php', 'class'=>'VtigerActorOperation');
	if ($withName) {
		vtws_addActorTypeWebserviceEntityWithName($actorName, $actorHandler['file'], $actorHandler['class'], $actorNameDetails);
	} else {
		vtws_addActorTypeWebserviceEntityWithoutName($actorName, $actorHandler['file'], $actorHandler['class'], $actorNameDetails);
	}
}

function vtws_addActorTypeWebserviceEntityWithName($moduleName, $filePath, $className, $actorNameDetails) {
	global $adb;
	$isModule=0;
	$entityId = $adb->getUniqueID('vtiger_ws_entity');
	$adb->pquery(
		'insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
		array($entityId,$moduleName,$filePath,$className,$isModule)
	);
	vtws_addActorTypeName($entityId, $actorNameDetails['fieldNames'], $actorNameDetails['indexField'], $actorNameDetails['tableName']);
}

function vtws_addActorTypeWebserviceEntityWithoutName($moduleName, $filePath, $className, $actorNameDetails) {
	global $adb;
	$isModule=0;
	$entityId = $adb->getUniqueID('vtiger_ws_entity');
	$adb->pquery(
		'insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
		array($entityId, $moduleName, $filePath, $className, $isModule)
	);
}

function vtws_addActorTypeName($entityId, $fieldNames, $indexColumn, $tableName) {
	global $adb;
	$adb->pquery(
		'insert into vtiger_ws_entity_name(entity_id,name_fields,index_field,table_name) values (?,?,?,?)',
		array($entityId,$fieldNames,$indexColumn,$tableName)
	);
}

function vtws_getName($id, $user) {
	global $log,$adb;

	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	return $meta->getName($id);
}

function vtws_preserveGlobal($name, $value) {
	return VTWS_PreserveGlobal::preserveGlobal($name, $value);
}

/**
 * Given the details of a webservices definition, it creates it if it doesn't exist already
 * @param array with the new web service method definition. Like this:
  $operationInfo = array(
	 'name'    => 'getRelatedRecords',
	 'include' => 'include/Webservices/GetRelatedRecords.php',
	 'handler' => 'getRelatedRecords',
	 'prelogin'=> 0,
	 'type'    => 'POST',
	 'parameters' => array(
		 array('name' => 'id','type' => 'String'),
		 array('name' => 'module','type' => 'String'),
		 array('name' => 'relatedModule','type' => 'String'),
		 array('name' => 'queryParameters','type' => 'encoded')
	 )
  );
 * @return boolean false if already registered, true if registered correctly
 * @throws InvalidArgumentException if failed to create webservice or failed to setup parameters
 */
function registerWSAPI($operationInfo) {
	global $adb;

	if (!isset($operationInfo['prelogin'])) {
		$operationInfo['prelogin'] = 0;
	}

	$check = $adb->pquery('SELECT 1 FROM vtiger_ws_operation WHERE name=?', array($operationInfo['name']));
	if ($check && $adb->num_rows($check)) {
		return false;  // it exists > we leave
	}

	$operationId = vtws_addWebserviceOperation(
		$operationInfo['name'],
		$operationInfo['include'],
		$operationInfo['handler'],
		$operationInfo['type'],
		$operationInfo['prelogin']
	);

	if (empty($operationId)) {
		throw new InvalidArgumentException('FAILED TO SETUP '.$operationInfo['name'].' WEBSERVICE');
	}

	$sequence = 1;
	foreach ($operationInfo['parameters'] as $parameters) {
		$status = vtws_addWebserviceOperationParam($operationId, $parameters['name'], $parameters['type'], $sequence++);
		if ($status === false) {
			throw new InvalidArgumentException('FAILED TO SETUP '.$parameters['name'].' WEBSERVICE HALFWAY THOURGH');
		}
	}
	return true;
}

/**
 * Takes the details of a webservices and exposes it over http.
 * @param string name of the webservice to be added with namespace.
 * @param string file to be include which provides the handler method for the given webservice.
 * @param string name of the function to the called when this webservice is invoked.
 * @param string type of request that this operation should be, if in doubt give it as GET,
 * 	general rule of thumb is that, if the operation is adding/updating data on server then it must be POST otherwise it should be GET.
 * @param boolean 0 if the operation need the user to authorised to access the webservice and
 * 	1 if the operation is called before login operation hence the there will be no user authorisation happening for the operation.
 * @return integer operationId of successful or null upon failure.
 */
function vtws_addWebserviceOperation($name, $handlerFilePath, $handlerMethodName, $requestType, $preLogin = 0) {
	global $adb;
	$createOperationQuery = 'insert into vtiger_ws_operation(operationid,name,handler_path,handler_method,type,prelogin) values (?,?,?,?,?,?);';
	if (strtolower($requestType) != 'get' && strtolower($requestType) != 'post') {
		return null;
	}
	$requestType = strtoupper($requestType);
	if (empty($preLogin)) {
		$preLogin = 0;
	} else {
		$preLogin = 1;
	}
	$operationId = $adb->getUniqueID('vtiger_ws_operation');
	$result = $adb->pquery($createOperationQuery, array($operationId,$name,$handlerFilePath,$handlerMethodName, $requestType,$preLogin));
	if ($result !== false) {
		return $operationId;
	}
	return null;
}

/**
 * Add a parameter to a webservice.
 * @param integer Id of the operation for which a webservice needs to be added.
 * @param string name of the parameter used to pickup value from request(POST/GET) object.
 * @param string type of the parameter, it can either 'string','datetime' or 'encoded'
 * 	encoded type is used for input which will be encoded in JSON or XML(NOT SUPPORTED).
 * @param integer sequence of the parameter in the definition in the handler method.
 * @return boolean true if the parameter was added successfully, false otherwise
 */
function vtws_addWebserviceOperationParam($operationId, $paramName, $paramType, $sequence) {
	global $adb;
	$supportedTypes = array('string','encoded','datetime','double','boolean');
	if (!is_numeric($sequence)) {
		$sequence = 1;
	}
	if ($sequence <=1) {
		$sequence = 1;
	}
	if (!in_array(strtolower($paramType), $supportedTypes)) {
		return false;
	}
	$createOperationParamsQuery = 'insert into vtiger_ws_operation_parameters(operationid,name,type,sequence) values (?,?,?,?);';
	$result = $adb->pquery($createOperationParamsQuery, array($operationId,$paramName,$paramType,$sequence));
	return ($result !== false);
}

/**
 * @global PearDatabase $adb
 * @global object $log
 * @param string module name
 * @param Users user
 * @param boolean if we should access all displaytypes
 * @return WebserviceEntityOperation
 */
function vtws_getModuleHandlerFromName($name, $user, $allDisplayTypes = false) {
	global $adb, $log;
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $name);
	$webserviceObject->allDisplayTypes = $allDisplayTypes;
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	return new $handlerClass($webserviceObject, $user, $adb, $log);
}

function vtws_getModuleHandlerFromId($id, $user) {
	global $adb, $log;
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	return new $handlerClass($webserviceObject, $user, $adb, $log);
}

function vtws_CreateCompanyLogoFile($fieldname) {
	global $root_directory;
	$uploaddir = $root_directory .'/test/logo/';
	$allowedFileTypes = array('jpeg', 'png', 'jpg', 'pjpeg' ,'x-png');
	$binFile = basename($_FILES[$fieldname]['name']);
	$fileType = $_FILES[$fieldname]['type'];
	$fileSize = $_FILES[$fieldname]['size'];
	$fileTypeArray = explode('/', $fileType);
	$fileTypeValue = strtolower($fileTypeArray[1]);
	if ($fileTypeValue == '') {
		$fileTypeValue = substr($binFile, strrpos($binFile, '.')+1);
	}
	if ($fileSize != 0) {
		if (in_array($fileTypeValue, $allowedFileTypes)) {
			move_uploaded_file($_FILES[$fieldname]['tmp_name'], $uploaddir.$binFile);
			return $binFile;
		}
		throw new WebServiceException(WebServiceErrorCode::$INVALIDTOKEN, "$fieldname wrong file type given for upload");
	}
	throw new WebServiceException(WebServiceErrorCode::$INVALIDTOKEN, "$fieldname file upload failed");
}

function vtws_getActorModules() {
	global $adb;
	$actorrs = $adb->query("SELECT name FROM vtiger_ws_entity WHERE handler_class='VtigerActorOperation' or handler_class='ModTrackerOperation'");
	$actors = array();
	while (!$actorrs->EOF) {
		$row = $actorrs->FetchRow();
		$actors[] = $row['name'];
	}
	return $actors;
}

function vtws_getActorEntityName($name, $idList) {
	$db = PearDatabase::getInstance();
	if (!is_array($idList) && count($idList) == 0) {
		return array();
	}
	$entity = VtigerWebserviceObject::fromName($db, $name);
	return vtws_getActorEntityNameById($entity->getEntityId(), $idList);
}

function vtws_getActorEntityNameById($entityId, $idList) {
	$db = PearDatabase::getInstance();
	if (!is_array($idList) && count($idList) == 0) {
		return array();
	}
	$nameList = array();
	$query = 'select table_name, index_field, name_fields from vtiger_ws_entity_name where entity_id = ?';
	$result = $db->pquery($query, array($entityId));
	if (is_object($result)) {
		$rowCount = $db->num_rows($result);
		if ($rowCount > 0) {
			$nameFields = $db->query_result($result, 0, 'name_fields');
			$tableName = $db->query_result($result, 0, 'table_name');
			$indexField = $db->query_result($result, 0, 'index_field');
			if (strpos($nameFields, ',')) {
				$fieldList = explode(',', $nameFields);
				$nameFields = 'concat(';
				$nameFields = $nameFields.implode(",' ',", $fieldList);
				$nameFields = $nameFields.')';
			}

			$query1 = "select $nameFields as entityname, $indexField from $tableName where $indexField in (".generateQuestionMarks($idList).')';
			$params1 = array($idList);
			$result = $db->pquery($query1, $params1);
			if (is_object($result)) {
				$rowCount = $db->num_rows($result);
				for ($i = 0; $i < $rowCount; $i++) {
					$id = $db->query_result($result, $i, $indexField);
					$nameList[$id] = $db->query_result($result, $i, 'entityname');
				}
				return $nameList;
			}
		}
	}
	return array();
}

function vtws_isRoleBasedPicklist($name) {
	$db = PearDatabase::getInstance();
	$result = $db->pquery('select picklistid from vtiger_picklist where name = ?', array($name));
	return ($db->num_rows($result) > 0);
}

function vtws_getConvertLeadFieldMapping() {
	global $adb;
	$result = $adb->pquery('select leadfid, accountfid, potentialfid, contactfid from vtiger_convertleadmapping', array());
	if ($result === false) {
		return null;
	}
	$mapping = array();
	$rowCount = $adb->num_rows($result);
	for ($i=0; $i<$rowCount; ++$i) {
		$row = $adb->query_result_rowdata($result, $i);
		$mapping[$row['leadfid']] = array(
			'Accounts' => $row['accountfid'],
			'Potentials' => $row['potentialfid'],
			'Contacts' => $row['contactfid']
		);
	}
	return $mapping;
}

/**	Function used to get the lead related Notes and Attachments with other entities Account, Contact and Potential
 *	@param integer $id - leadid
 *	@param integer $relatedId -  related entity id (accountid / contactid)
 */
function vtws_getRelatedNotesAttachments($id, $relatedId) {
	global $adb;
	$result = $adb->pquery('select notesid from vtiger_senotesrel where crmid=?', array($id));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);

	$sql='insert into vtiger_senotesrel(crmid,notesid) values (?,?)';
	for ($i=0; $i<$rowCount; ++$i) {
		$noteId=$adb->query_result($result, $i, 'notesid');
		$resultNew = $adb->pquery($sql, array($relatedId, $noteId));
		if ($resultNew === false) {
			return false;
		}
	}

	$result = $adb->pquery('select attachmentsid from vtiger_seattachmentsrel where crmid=?', array($id));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);

	$sql = 'insert into vtiger_seattachmentsrel(crmid,attachmentsid) values (?,?)';
	for ($i=0; $i<$rowCount; ++$i) {
		$attachmentId=$adb->query_result($result, $i, 'attachmentsid');
		$resultNew = $adb->pquery($sql, array($relatedId, $attachmentId));
		if ($resultNew === false) {
			return false;
		}
	}
	return true;
}

/**	Function used to save the lead related products with other entities Account, Contact and Potential
 *	@param integer leadid
 *	@param integer related entity id (accountid/contactid/potentialid)
 *	@param string related module (Accounts/Contacts/Potentials)
 */
function vtws_saveLeadRelatedProducts($leadId, $relatedId, $setype) {
	global $adb;

	$result = $adb->pquery('select productid from vtiger_seproductsrel where crmid=?', array($leadId));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);
	for ($i = 0; $i < $rowCount; ++$i) {
		$productId = $adb->query_result($result, $i, 'productid');
		$resultNew = $adb->pquery('insert into vtiger_seproductsrel values(?,?,?)', array($relatedId, $productId, $setype));
		if ($resultNew === false) {
			return false;
		}
	}
	return true;
}

/**	Function used to save the lead related services with other entities Account, Contact and Potential
 *	@param integer leadid
 *	@param integer related entity id (accountid/contactid/potentialid)
 *	@param string related module (Accounts/Contacts/Potentials)
 */
function vtws_saveLeadRelations($leadId, $relatedId, $setype) {
	global $adb;

	$result = $adb->pquery('select relcrmid, relmodule from vtiger_crmentityrel where crmid=?', array($leadId));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);
	for ($i = 0; $i < $rowCount; ++$i) {
		$recordId = $adb->query_result($result, $i, 'relcrmid');
		$recordModule = $adb->query_result($result, $i, 'relmodule');
		$resultNew = $adb->pquery('insert into vtiger_crmentityrel values(?,?,?,?)', array($relatedId, $setype, $recordId, $recordModule));
		if ($resultNew === false) {
			return false;
		}
	}
	$result = $adb->pquery('select crmid, module from vtiger_crmentityrel where relcrmid=?', array($leadId));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);
	for ($i = 0; $i < $rowCount; ++$i) {
		$recordId = $adb->query_result($result, $i, 'crmid');
		$recordModule = $adb->query_result($result, $i, 'module');
		$resultNew = $adb->pquery(
			'insert into vtiger_crmentityrel values(?,?,?,?)',
			array($relatedId, $setype, $recordId, $recordModule)
		);
		if ($resultNew === false) {
			return false;
		}
	}

	return true;
}

function vtws_getFieldfromFieldId($fieldId, $fieldObjectList) {
	foreach ($fieldObjectList as $field) {
		if ($fieldId == $field->getFieldId()) {
			return $field;
		}
	}
	return null;
}

/**	Function used to transfer the lead related activities with other entities Account and Contact
 *	@param integer $leadId - lead entity id
 *	@param integer $accountId - related account id
 *	@param integer $contactId - related contact id
 *	@param integer $relatedId - related entity id to which the records need to be transferred
 *	@return boolean true if transfered correctly, false otherwise
 */
function vtws_transferRelatedActivities($leadId, $accountId, $contactId, $relatedId) {
	if (empty($leadId) || empty($relatedId) || (empty($accountId) && empty($contactId))) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED, 'Failed to move related Activities/Emails');
	}
	global $adb;
	$result = $adb->pquery('select activityid from vtiger_seactivityrel where crmid=?', array($leadId));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);
	for ($i=0; $i<$rowCount; ++$i) {
		$activityId=$adb->query_result($result, $i, 'activityid');
		$resultNew = $adb->pquery('select setype from vtiger_crmobject where crmid=?', array($activityId));
		if ($resultNew === false) {
			return false;
		}
		$type=$adb->query_result($resultNew, 0, 'setype');

		$resultNew = $adb->pquery('delete from vtiger_seactivityrel where crmid=? and activityid=?', array($leadId, $activityId));
		if ($resultNew === false) {
			return false;
		}
		if ($type != 'Emails') {
			if (!empty($accountId)) {
				$resultNew = $adb->pquery('insert into vtiger_seactivityrel(crmid,activityid) values (?,?)', array($accountId, $activityId));
				if ($resultNew === false) {
					return false;
				}
				$adb->pquery('update vtiger_activity set rel_id=? where activityid=?', array($accountId, $activityId));
			}
			if (!empty($contactId)) {
				$resultNew = $adb->pquery('insert into vtiger_cntactivityrel(contactid,activityid) values (?,?)', array($contactId, $activityId));
				if ($resultNew === false) {
					return false;
				}
				$adb->pquery('update vtiger_activity set cto_id=? where (cto_id="" or cto_id is null) and activityid=?', array($contactId, $activityId));
			}
		} else {
			$resultNew = $adb->pquery('insert into vtiger_seactivityrel(crmid,activityid) values (?,?)', array($relatedId, $activityId));
			if ($resultNew === false) {
				return false;
			}
		}
	}
	return true;
}

/**
 * Function used to save the lead related Campaigns with Contact
 * @param $leadid - leadid
 * @param $relatedid - related entity id (contactid/accountid)
 * @param $setype - related module(Accounts/Contacts)
 * @return boolean true on success, false otherwise.
 */
function vtws_saveLeadRelatedCampaigns($leadId, $relatedId, $seType) {
	global $adb;

	$result = $adb->pquery('select campaignid from vtiger_campaignleadrel where leadid=?', array($leadId));
	if ($result === false) {
		return false;
	}
	$rowCount = $adb->num_rows($result);
	for ($i = 0; $i < $rowCount; ++$i) {
		$campaignId = $adb->query_result($result, $i, 'campaignid');
		if ($seType == 'Accounts') {
			$resultNew = $adb->pquery('insert into vtiger_campaignaccountrel (campaignid, accountid) values(?,?)', array($campaignId, $relatedId));
		} elseif ($seType == 'Contacts') {
			$resultNew = $adb->pquery('insert into vtiger_campaigncontrel (campaignid, contactid) values(?,?)', array($campaignId, $relatedId));
		}
		if ($resultNew === false) {
			return false;
		}
	}
	return true;
}

/**
 * Function used to transfer all the lead related records to given Entity(Contact/Account) record
 * @param integer leadid
 * @param integer related entity id (contactid/accountid)
 * @param string related module (Accounts/Contacts)
 */
function vtws_transferLeadRelatedRecords($leadId, $relatedId, $seType) {
	global $adb;
	if (empty($leadId) || empty($relatedId) || empty($seType)) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED, 'Failed to move related Records');
	}
	$status = vtws_getRelatedNotesAttachments($leadId, $relatedId);
	if ($status === false) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED, 'Failed to move related Documents to the '.$seType);
	}
	//Retrieve the lead related products and relate them with this new account
	$status = vtws_saveLeadRelatedProducts($leadId, $relatedId, $seType);
	if ($status === false) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED, 'Failed to move related Products to the '.$seType);
	}
	$status = vtws_saveLeadRelations($leadId, $relatedId, $seType);
	if ($status === false) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED, 'Failed to move Records to the '.$seType);
	}
	$status = vtws_saveLeadRelatedCampaigns($leadId, $relatedId, $seType);
	if ($status === false) {
		throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED, 'Failed to move Records to the '.$seType);
	}
	vtws_transferComments($leadId, $relatedId);
	// Tags
	$adb->pquery('update vtiger_freetagged_objects set object_id=?,module=? where object_id=?', array($relatedId,$seType,$leadId));
}

function vtws_transferComments($sourceRecordId, $destinationRecordId) {
	if (vtlib_isModuleActive('ModComments')) {
		CRMEntity::getInstance('ModComments');
		ModComments::transferRecords($sourceRecordId, $destinationRecordId);
	}
}

function vtws_transferOwnership($ownerId, $newOwnerId, $delete = true) {
	$db = PearDatabase::getInstance();
	//Updating the smcreatorid,smownerid, modifiedby in vtiger_crmentity
	$denormModules = getDenormalizedModules();
	if (count($denormModules) > 0) {
		foreach ($denormModules as $table) {
			$db->pquery('update '.$table.' set smcreatorid=? where smcreatorid=?', array($newOwnerId, $ownerId));
			$db->pquery('update '.$table.' set smownerid=? where smownerid=?', array($newOwnerId, $ownerId));
			$db->pquery('update '.$table.' set modifiedby=? where modifiedby=?', array($newOwnerId, $ownerId));
		}
	}
	$db->pquery('update vtiger_crmentity set smcreatorid=? where smcreatorid=?', array($newOwnerId, $ownerId));
	$db->pquery('update vtiger_crmentity set smownerid=? where smownerid=?', array($newOwnerId, $ownerId));
	$db->pquery('update vtiger_crmentity set modifiedby=? where modifiedby=?', array($newOwnerId, $ownerId));
	//Updating the createdby in vtiger_attachmentsfolder
	$db->pquery('update vtiger_attachmentsfolder set createdby=? where createdby=?', array($newOwnerId, $ownerId));

	//deleting from vtiger_tracker
	if ($delete) {
		$db->pquery('delete from vtiger_tracker where user_id=?', array($ownerId));
	}

	//updating the filters
	$db->pquery('update vtiger_customview set userid=? where userid=?', array($newOwnerId, $ownerId));

	//updating the vtiger_import_maps
	$db->pquery('update vtiger_import_maps set assigned_user_id=? where assigned_user_id=?', array($newOwnerId, $ownerId));

	if (Vtiger_Utils::CheckTable('vtiger_customerportal_prefs')) {
		$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
		$params = array($newOwnerId, 'defaultassignee', $ownerId);
		$db->pquery($query, $params);

		$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
		$params = array($newOwnerId, 'userid', $ownerId);
		$db->pquery($query, $params);
	}

	//delete from vtiger_homestuff
	if ($delete) {
		$db->pquery('delete from vtiger_homestuff where userid=?', array($ownerId));
	}

	//delete from vtiger_users to group vtiger_table
	if ($delete) {
		$db->pquery('delete from vtiger_user2role where userid=?', array($ownerId));
	}

	//delete from vtiger_users to vtiger_role vtiger_table
	if ($delete) {
		$db->pquery('delete from vtiger_users2group where userid=?', array($ownerId));
	}

	$sql = "select tabid,fieldname,tablename,columnname
		from vtiger_field
		left join vtiger_fieldmodulerel on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid
		where uitype in (52,53,77,101) or (uitype=10 and relmodule='Users')";
	$result = $db->pquery($sql, array());
	$it = new SqlResultIterator($db, $result);
	$columnList = array();
	foreach ($it as $row) {
		$column = $row->tablename.'.'.$row->columnname;
		if (!in_array($column, $columnList)) {
			$columnList[] = $column;
			$sql = "update $row->tablename set $row->columnname=? where $row->columnname=?";
			$db->pquery($sql, array($newOwnerId, $ownerId));
		}
	}
}

function vtws_getWebserviceTranslatedStringForLanguage($label, $currentLanguage) {
	static $translations = array();
	if (empty($currentLanguage)) {
		$currentLanguage = vtws_getWebserviceCurrentLanguage();
	}
	if (empty($translations[$currentLanguage])) {
		include 'include/Webservices/language/'.$currentLanguage.'.lang.php';
		$translations[$currentLanguage] = $webservice_strings;
	}
	if (isset($translations[$currentLanguage][$label])) {
		return $translations[$currentLanguage][$label];
	}
	return null;
}

function vtws_getWebserviceTranslatedString($label) {
	$currentLanguage = vtws_getWebserviceCurrentLanguage();
	$translation = vtws_getWebserviceTranslatedStringForLanguage($label, $currentLanguage);
	if (!empty($translation)) {
		return $translation;
	}

	//current language doesn't have translation, return translation in default language
	//if default language is english then LBL_ will not shown to the user.
	$defaultLanguage = vtws_getWebserviceDefaultLanguage();
	$translation = vtws_getWebserviceTranslatedStringForLanguage($label, $defaultLanguage);
	if (!empty($translation)) {
		return $translation;
	}

	//if default language is not en_us then do the translation in en_us to eliminate the LBL_ bit of label.
	if ('en_us' != $defaultLanguage) {
		$translation = vtws_getWebserviceTranslatedStringForLanguage($label, 'en_us');
		if (!empty($translation)) {
			return $translation;
		}
	}
	return $label;
}

function vtws_getWebserviceCurrentLanguage() {
	global $default_language, $current_language;
	if (empty($current_language)) {
		return $default_language;
	}
	return $current_language;
}

function vtws_getWebserviceDefaultLanguage() {
	global $default_language;
	return $default_language;
}

function vtws_getWsIdForFilteredRecord($moduleName, $conditions, $user) {
	global $adb;
	$queryGenerator = new QueryGenerator($moduleName, $user);
	$queryGenerator->setFields(array('id'));
	$queryGenerator->addUserSearchConditions($queryGenerator->constructAdvancedSearchConditions($moduleName, $conditions));
	$query = $queryGenerator->getQuery(false, 1);
	$result = $adb->pquery($query, array());
	if ($adb->num_rows($result) == 0) {
		return null;
	}
	return vtws_getEntityId($moduleName).'x'.$adb->query_result($result, 0, 0);
}

function vtws_checkListTypesPermission($moduleName, $user, $return = 'types') {
	global $adb, $log;
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $moduleName);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	if (!$meta->isModuleEntity()) {
		throw new WebServiceException('INVALID_MODULE', "Given module ($moduleName) cannot be found");
	}
	// check permission on module
	$entityName = $meta->getEntityName();
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($moduleName) is denied");
	}
	switch ($return) {
		case 'meta':
			return $meta;
			break;
		case 'types':
		default:
			return $types;
			break;
	}
}

function setResponseHeaders() {
	global $cors_enabled_domains;
	if (isset($_SERVER['HTTP_ORIGIN']) && !empty($cors_enabled_domains)) {
		$parse = parse_url($_SERVER['HTTP_ORIGIN']);
		if ($cors_enabled_domains=='*' || strpos($cors_enabled_domains, $parse['host'])!==false) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}
	}
	if (!(isset($_REQUEST['format']) && (strtolower($_REQUEST['format'])=='stream' || strtolower($_REQUEST['format'])=='streamraw'))) {
		header('Content-type: application/json');
	}
}

function writeErrorOutput($operationManager, $error, $outputmethod = 'echo', $headers = 'setResponseHeaders') {
	$headers();
	$state = new State();
	$state->success = false;
	$state->error = $error;
	unset($state->result);
	$output = $operationManager->encode($state);
	//Send email with error.
	$mailto = GlobalVariable::getVariable('Debug_Send_WebService_Error', '');
	if ($mailto != '') {
		$wserror = GlobalVariable::getVariable('Debug_WebService_Errors', '*');
		$wsproperty = false;
		if ($wserror != '*') {
			$wsprops = explode(',', $wserror);
			foreach ($wsprops as $wsprop) {
				if (property_exists('WebServiceErrorCode', $wsprop)) {
					$wsproperty = true;
					break;
				}
			}
		}
		if ($wserror == '*' || $wsproperty) {
			global $site_URL;
			require_once 'modules/Emails/mail.php';
			require_once 'modules/Emails/Emails.php';
			$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
			$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
			$mailsubject = '[ERROR]: '.$error->code.' - web service call throwed exception.';
			$mailcontent = '[ERROR]: '.$error->code.' '.$error->message."\n<br>".$site_URL;
			unset($_REQUEST['sessionName']);
			$mailcontent.= var_export($_REQUEST, true);
			send_mail('Emails', $mailto, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $mailsubject, $mailcontent);
		}
	}
	switch ($outputmethod) {
		case 'return':
			return $output;
			break;
		case 'roadrunner':
			global $resp;
			$resp->getBody()->write($output);
			break;
		case 'echo':
		default:
			echo $output;
			break;
	}
}

function writeOutput($operationManager, $data, $outputmethod = 'echo', $headers = 'setResponseHeaders') {
	$headers();
	$state = new State();
	if (isset($data['wsmoreinfo'])) {
		$state->moreinfo = $data['wsmoreinfo'];
		unset($data['wsmoreinfo']);
		if (!isset($data['wssuccess'])) {
			$data = $data['wsresult'];
		}
	}
	if (isset($data['wsresult']) && isset($data['wssuccess'])) {
		$state->success = $data['wssuccess'];
		$state->result = $data['wsresult'];
	} else {
		$state->success = true;
		$state->result = $data;
	}
	unset($state->error);
	$output = $operationManager->encode($state);
	switch ($outputmethod) {
		case 'return':
			return $output;
			break;
		case 'roadrunner':
			global $resp;
			$resp->getBody()->write($output);
			break;
		case 'echo':
		default:
			echo $output;
			break;
	}
}
?>
