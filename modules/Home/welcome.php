<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
global $current_user;
include_once 'modules/com_vtiger_workflow/VTSimpleTemplateOnData.inc';

$FirstTimeLogin_Template = GlobalVariable::getVariable('Application_FirstTimeLogin_Template', '');
if (!empty($FirstTimeLogin_Template)) {
	$util = new VTWorkflowUtils();
	$entityCache = new VTEntityCache($current_user);
	$wsid = vtws_getWebserviceEntityId('Users', $current_user->id);
	$entityData = $entityCache->forId($wsid);
	$data = $entityData->data;
	$data['assigned_user_id'] = $wsid;
	$data['Application_UI_Name'] = GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name);
	$data['Application_UI_Version'] = GlobalVariable::getVariable('Application_UI_Version', $coreBOS_app_version);
	$data['Application_UI_URL'] = GlobalVariable::getVariable('Application_UI_URL', $coreBOS_app_url);
	$ct = new VTSimpleTemplateOnData($FirstTimeLogin_Template);
	$FirstTimeLogin_Template = $ct->render($entityCache, 'Users', $data);
}
echo $FirstTimeLogin_Template;
