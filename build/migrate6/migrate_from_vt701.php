<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Migrate from vtiger CRM 7.0.1 to vtiger CRM 7.0.0
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
global $current_user, $adb;

$wfs = $adb->query('select * from com_vtiger_workflow_tasktypes');
while ($wf = $adb->fetch_array($wfs)) {
	if (!file_exists($wf['templatepath']) && file_exists('Smarty/templates/com_vtiger_workflow/taskforms/'.$wf['classname'])) {
		ExecuteQuery('UPDATE com_vtiger_workflow_tasktypes SET templatepath=?', array('Smarty/templates/com_vtiger_workflow/taskforms/'.$wf['classname']));
	}
}
ExecuteQuery('ALTER TABLE vtiger_field DROP COLUMN isunique', array());
ExecuteQuery('ALTER TABLE vtiger_tab DROP COLUMN issyncable', array());
ExecuteQuery('ALTER TABLE vtiger_tab DROP COLUMN allowduplicates', array());
ExecuteQuery('ALTER TABLE vtiger_tab DROP COLUMN sync_action_for_duplicates', array());

$em = new VTEventsManager($adb);
$em->unregisterHandler('CheckDuplicateHandler');

ExecuteQuery('DROP TABLE vtiger_webform_file_fields');

$operationResult = $adb->pquery('SELECT operationid FROM vtiger_ws_operation WHERE name=?', array('add_related'));
if ($operationResult && $adb->num_rows($operationResult)) {
	$op = $adb->fetch_array($operationResult);
	ExecuteQuery('DELETE FROM vtiger_ws_operation WHERE operationid=?', array($op['operationid']));
	ExecuteQuery('DELETE FROM vtiger_ws_operation_parameters WHERE operationid=?', array($op['operationid']));
}

$em->unregisterHandler('FollowRecordHandler');

ExecuteQuery('ALTER TABLE vtiger_tab DROP COLUMN source', array());

ExecuteQuery('DROP TABLE vtiger_google_event_calendar_mapping');
ExecuteQuery('DROP TABLE vtiger_crmentity_user_field');
ExecuteQuery('DROP TABLE vtiger_app2tab');
ExecuteQuery('DROP TABLE vtiger_module_dashboard_widgets');

ExecuteQuery("update vtiger_version set old_version='6.5.0', current_version='7.0.0' where id=1");