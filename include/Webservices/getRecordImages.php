<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
* Allows a webservice client to retrieve the information of the image attachments associated to a record
* Which can then be used with the build/HelperScripts/getImageData.php script to obtain the image
* params:
*   id: webservice crm id
* returns json string:
*   results: number of images available
*   images: array
*     name: image name
*     path: image path in application
*     fullpath: image absolute path
*     type: image mime type
*     id: image id
 *************************************************************************************************/

function cbws_getrecordimageinfo($id, $user) {
	global $log, $adb, $site_URL, $default_charset;
	$log->debug("Entering function cbws_getrecordimageinfo($id)");
	list($wsid,$crmid) = explode('x', $id);
	if ((vtws_getEntityId('Calendar')==$wsid || vtws_getEntityId('Events')==$wsid) && getSalesEntityType($crmid)=='cbCalendar') {
		$id = vtws_getEntityId('cbCalendar') . 'x' . $crmid;
	}
	if (vtws_getEntityId('cbCalendar')==$wsid && getSalesEntityType($crmid)=='Calendar') {
		$rs = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($crmid));
		if ($rs && $adb->num_rows($rs)==1) {
			if ($adb->query_result($rs, 0, 0)=='Task') {
				$id = vtws_getEntityId('Calendar') . 'x' . $crmid;
			} else {
				$id = vtws_getEntityId('Events') . 'x' . $crmid;
			}
		}
	}

	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($id);
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}
	if ($meta->hasReadAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read entity is denied');
	}
	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}
	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}
	$idComponents = vtws_getIdComponents($id);
	if (!$meta->exists($idComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	$ids = vtws_getIdComponents($id);
	$pdoid = $ids[1];
	$rdo = array();
	$rdo['results']=0;
	$imgs = $meta->getImageFields();
	if (count($imgs)>0) {
		$qg = new QueryGenerator($entityName, $user);
		$qg->setFields($imgs);
		$qg->addCondition('id', $pdoid, 'e');
		$query = $qg->getQuery();
		$imgnamers = $adb->query($query);
		$imgnames = $adb->fetch_array($imgnamers);
		$inames = array();
		foreach ($imgnames as $fname => $imgvalue) {
			if (is_numeric($fname)) {
				continue;
			}
			$inames[$fname] = str_replace(' ', '_', html_entity_decode($imgvalue, ENT_QUOTES, $default_charset));
		}
		$query = 'select vtiger_attachments.name, vtiger_attachments.type, vtiger_attachments.attachmentsid, vtiger_attachments.path
			from vtiger_attachments
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
			inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
			where (vtiger_crmentity.setype LIKE "%Image" or vtiger_crmentity.setype LIKE "%Attachment") and deleted=0 and vtiger_seattachmentsrel.crmid=?';
		$result_image = $adb->pquery($query, array($pdoid));
		$rdo['images']=array();
		while ($img = $adb->fetch_array($result_image)) {
			$imga = array();
			$imga['name'] = $img['name'];
			$imga['path'] = $img['path'];
			$imga['fullpath'] = $site_URL.'/'.$img['path'].$img['attachmentsid'].'_'.$img['name'];
			$imga['type'] = $img['type'];
			$imga['id'] = $img['attachmentsid'];
			$imgfield = '';
			foreach ($inames as $fname => $imgvalue) {
				if ($img['name'] == $imgvalue) {
					$imgfield = $fname;
					break;
				}
			}
			$rdo['images'][$imgfield] = $imga;
		}
		$rdo['results']=count($rdo['images']);
	}
	VTWS_PreserveGlobal::flush();
	$log->debug('Leaving function cbws_getrecordimageinfo');
	return $rdo;
}
?>