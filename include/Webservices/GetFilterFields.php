<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************/

function vtws_getfilterfields($module, $user) {
	global $adb, $log;
	if ($module=='Users') {
		return array(
			'fields'=>array('first_name', 'last_name', 'email1'),
			'linkfields'=>array('first_name', 'last_name'),
			'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module)),
		);
	}
	$types = vtws_listtypes(null, $user);
	if (!in_array($module, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($module) is denied");
	}
	if (in_array($module, vtws_getActorModules())) {
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $module);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		return $handler->getFilterFields($module);
	}
	if (!vtlib_isEntityModule($module)) {
		return array(
			'fields'=>'',
			'linkfields'=>'',
			'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module)),
		);
	}
	$focus = CRMEntity::getInstance($module);

	$linkfields=array($focus->list_link_field);
	if ($module=='Contacts' || $module=='Leads') {
		$linkfields=array('firstname', 'lastname');
	}
	$customView = new CustomView($module);
	$saveAction = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$_REQUEST['action'] = 'ListView';
	$viewid = $customView->getViewId($module);
	$_REQUEST['action'] = $saveAction;
	$viewinfo = $customView->getColumnsListByCvid($viewid);
	$fields = array();
	$relatedfields = array();
	$relatedfieldsInfo = array();
	foreach ($viewinfo as $fld) {
		$finfo=explode(':', $fld);
		$mod = explode('_', $finfo[3]);
		if ($mod[0] == 'Notes') {
			$mod[0] = 'Documents';
		}
		if ($mod[0] != $module) {
			$fields[]=$mod[0].'.'.$finfo[2];
			if (empty($relatedfieldsInfo[$mod[0]])) {
				$relatedfieldsInfo[$mod[0]] = getModuleFieldsInfo($mod[0]);
			}
			if (empty($relatedfieldsInfo[$mod[0]])) {
				continue;
			}
			$row = array();
			foreach ($relatedfieldsInfo[$mod[0]] as $rows) {
				if ($rows['fieldname'] == $finfo[2]) {
					$row = $rows;
					break;
				}
			}
			$relatedfields[$finfo[2]] = array(
				'name' => $finfo[2],
				'label' => getTranslatedString($row['fieldlabel'], $mod[0]),
				'label_raw' => $row['fieldlabel'],
				'uitype' => $row['uitype'],
				'typeofdata' => $row['typeofdata'],
				'default' => $row['defaultvalue'],
			);
		} else {
			$fields[]=($finfo[1]=='smownerid' ? 'assigned_user_id' : $finfo[2]);
		}
	}

	return array(
		'fields'=>$fields,
		'relatedfields'=>$relatedfields,
		'linkfields'=>$linkfields,
		'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module)),
	);
}
?>