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
	global $log;
	$log->debug('Entering function vtws_getfilterfields');

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
	$viewid = $customView->getViewId($module);
	$viewinfo = $customView->getColumnsListByCvid($viewid);
	$fields = array();
	foreach ($viewinfo as $fld) {
		$finfo=explode(':', $fld);
		$fields[]=($finfo[1]=='smownerid' ? 'assigned_user_id' : $finfo[2]);
	}

	return array(
		'fields'=>$fields,
		'linkfields'=>$linkfields,
		'pagesize' => intval(GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module)),
	);
}
?>