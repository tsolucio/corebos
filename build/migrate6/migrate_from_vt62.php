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
 *  Migrate from vtiger CRM 6.2 to vtiger CRM 6.1
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

// Delete Google Sync Handlers
ExecuteQuery("DELETE FROM vtiger_wsapp_handlerdetails WHERE type = 'Google_vtigerHandler' or type = 'Google_vtigerSyncHandler'");
ExecuteQuery("DELETE FROM vtiger_wsapp_sync_state WHERE name IN ('Vtiger_GoogleContacts', 'Vtiger_GoogleCalendar')");
ExecuteQuery("DELETE FROM vtiger_wsapp WHERE name = 'Google_vtigerSyncHandler'");

$droptable = array(
	'vtiger_google_oauth2', 'vtiger_google_sync_settings', 'vtiger_google_sync_fieldmapping'
);

foreach ($droptable as $table) {
	ExecuteQuery("DROP TABLE $table");
}

ExecuteQuery("update vtiger_version set old_version='6.0.0', current_version='6.1.0' where id=1");