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
 *  Migrate from vtiger CRM 7.1 to vtiger CRM 7.0.1
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
global $adb;

$columns = $adb->getColumnNames('vtiger_users');
if (in_array('user_hash', $columns)) {
	// Remove unused column from user table
	ExecuteQuery('ALTER TABLE vtiger_users DROP COLUMN user_hash', array());
}

// Resizing column to hold wider string value.
ExecuteQuery('ALTER TABLE vtiger_systems MODIFY server_password VARCHAR(255)', array());
ExecuteQuery("update vtiger_version set old_version='7.0.0', current_version='7.0.1' where id=1");