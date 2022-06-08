<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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
require_once 'modules/cbCVManagement/cbCVManagement.php';

function getViewsByModule($module, $user) {
	global $adb, $log;
	// pickup meta data of module
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $module);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$mainModule = $meta->getTabName();  // normalize module name
	// check modules
	if (!$meta->isModuleEntity()) {
		throw new WebServiceException('INVALID_MODULE', "Given module ($module) cannot be found");
	}
	if ($module=='Users') {
		return array(
			'filters'=>array(array(
				'name' => 'All',
				'status' => '1',
				'advcriteria' => '[]',
				'advcriteriaWQL' => '',
				'advcriteriaEVQL' => '',
				'stdcriteria' => '[]',
				'stdcriteriaWQL' => '',
				'stdcriteriaEVQL' => '',
				'fields' => array('first_name', 'last_name', 'email1'),
				'default' => true,
			)),
			'linkfields'=>array('first_name', 'last_name'),
			'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module)),
		);
	}

	// check permission on module
	$entityName = $meta->getEntityName();
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($mainModule) is denied");
	}

	if (!$meta->hasReadAccess()) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read module is denied');
	}
	$focus = CRMEntity::getInstance($module);
	$linkfields=array($focus->list_link_field);
	if ($module=='Contacts' || $module=='Leads') {
		$linkfields=array('firstname', 'lastname');
	}
	$rdo = cbCVManagement::getAllViews($module, $user->id);
	$viewinfo = cbws_getViewsInformation($rdo, $module);
	return array(
		'filters' => $viewinfo,
		'linkfields' => $linkfields,
		'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module)),
	);
}

/** get information about the list of given views
 * @param $viewids :: array Integer
 * @return array view information
 */
function cbws_getViewsInformation($viewids, $module) {
	global $adb, $app_strings, $currentModule;
	$currentModule = $module;
	$dft = cbCVManagement::getDefaultView($module);
	$customView = new CustomView($module);
	$ssql = 'select vtiger_customview.* from vtiger_customview where vtiger_customview.cvid=?';
	$filters = array();
	foreach ($viewids as $viewid) {
		$result = $adb->pquery($ssql, array($viewid));
		$cvrow = $adb->fetch_array($result);
		if ($cvrow['viewname'] == 'All') {
			$cvrow['viewname'] = $app_strings['COMBO_ALL'];
		}
		$filter = array(
			'name' => $cvrow['viewname'],
			'status' => $cvrow['status'],
		);
		$advft_criteria = $customView->getAdvFilterByCvid($cvrow['cvid']);
		$advft = array();
		$groupnum = 1;
		foreach ($advft_criteria as $groupinfo) {
			if ($groupnum==1) {
				$groupcolumns = $groupinfo['columns'];
				foreach ($groupcolumns as $columnindex => $columninfo) {
					$columnname = $columninfo['columnname'];
					$comparator = $columninfo['comparator'];
					$value = $columninfo['value'];
					$columncondition = $columninfo['column_condition'];

					$columns = explode(':', $columnname);
					$name = $columns[1];

					$advft[$columnindex]['columnname'] = $name;
					$advft[$columnindex]['comparator'] = $comparator;
					if ($value == 'yes') {
						$advft[$columnindex]['value'] = 1;
					} elseif ($value == 'no') {
						$advft[$columnindex]['value'] = 0;
					} else {
						$advft[$columnindex]['value'] = $value;
					}
					$advft[$columnindex]['column_condition'] = $columncondition;
				}
				$groupnum++;
			}
		}
		$filter['advcriteria'] = json_encode($advft);
		$filter['advcriteriaWQL'] = $customView->getCVAdvFilterSQL($cvrow['cvid'], true);
		$filter['advcriteriaEVQL'] = $customView->getCVAdvFilterEVQL($cvrow['cvid']);
		$stdfilter = $customView->getStdFilterByCvid($cvrow['cvid']);
		if (empty($stdfilter['columnname'])) {
			$filter['stdcriteria'] = '[]';
		} else {
			$stdfltcol = explode(':', $stdfilter['columnname']);
			$filter['stdcriteria'] = json_encode(array(
				array(
					'columnname' => $stdfltcol[2],
					'comparator' => 'bw',
					'value' => $stdfilter['startdate'].','.$stdfilter['enddate'],
					'column_condition' => '',
				),
			));
		}
		$filter['stdcriteriaWQL'] = $customView->getCVStdFilterSQL($cvrow['cvid'], true);
		$filter['stdcriteriaEVQL'] = $customView->getCVStdFilterEVQL($cvrow['cvid']);
		$viewinfo = $customView->getColumnsListByCvid($cvrow['cvid']);
		$fields = array();
		foreach ($viewinfo as $fld) {
			$finfo=explode(':', $fld);
			$fields[]=($finfo[1]=='smownerid' ? 'assigned_user_id' : $finfo[2]);
		}
		$filter['fields'] = $fields;
		$filter['default'] = ($dft==$cvrow['cvid']);
		$filters[$cvrow['cvid']] = $filter;
	}
	return $filters;
}
?>
