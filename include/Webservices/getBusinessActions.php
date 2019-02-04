<?php
function getBusinessActions($view, $module, $id, $linktype) {
	global $adb, $log, $current_user;

	$tabid = getTabid($module);
	$type = explode(',', $linktype);
	$action = vtlib_purify($view);
	$parameters = ['MODULE' => $module, 'ACTION' => $action];
	$recordId = null;

	//check if the user has access to the specified module
	$types = vtws_listtypes(null, $current_user);
	if (!in_array($module, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	if ($view == 'ListView') {
		$parameters['CATEGORY'] = getParentTab();
	} else {
		if (!empty($id)) {
			$idComponents = vtws_getIdComponents($id);

			$parameters['RECORD'] = $idComponents[1];

			$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$meta = $handler->getMeta();

			if ($meta->hasReadAccess()!==true) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
			}
			if ($module !== $webserviceObject->getEntityName()) {
				throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
			}
			if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
			}
			if (!$meta->exists($idComponents[1])) {
				throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
			}
		}
	}

	$businessActions = Vtiger_Link::getAllByType($tabid, $type, $parameters, $current_user->id, $recordId);
	return json_encode($businessActions);
}
