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
*************************************************************************************************/
require_once 'include/utils/utils.php';
global $adb, $current_user;
$moduleid = $_COOKIE['moduleid'];
$fieldSql = $adb->pquery('SELECT fieldsid, fieldname FROM vtiger_modulebuilder_fields WHERE moduleid=?', array(
	$moduleid
));
$fields = array();
for ($i=0; $i < $adb->num_rows($fieldSql); $i++) {
	$_FIELD = array();
	$fieldsid = $adb->query_result($fieldSql, $i, 'fieldsid');
	$fieldname = $adb->query_result($fieldSql, $i, 'fieldname');
	$_FIELD['fieldsid'] = $fieldsid;
	$_FIELD['fieldname'] = $fieldname;
	array_push($fields, $_FIELD);
}
echo json_encode($fields);
?>