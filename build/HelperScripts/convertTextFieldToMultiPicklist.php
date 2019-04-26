<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Module.php';

$fieldname=vtlib_purify($_REQUEST['fieldname']);
$module=vtlib_purify($_REQUEST['module']);
global $adb,$log;
if (!empty($fieldname) && !empty($module)) {
	$moduleInstance = Vtiger_Module::getInstance($module);
	if ($moduleInstance) {
		$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
		if ($field) {
			$data =$adb->pquery('SELECT uitype,tablename FROM vtiger_field WHERE fieldid = ? and tabid=?', array($field->id,$moduleInstance->id));
			$uitype =$adb->query_result($data, 0, 'uitype');
			if ($uitype==1) {
				$table =$adb->query_result($data, 0, 'tablename');
				$adb->query("update $table set $fieldname = '--None--' where $fieldname is null or trim($fieldname)=''");
				$picklistvalues=$adb->query("Select distinct $fieldname from $table");
				$list=array();
				for ($i=0; $i<$adb->num_rows($picklistvalues); $i++) {
					$list[]=$adb->query_result($picklistvalues, $i, $fieldname);
				}
				$adb->pquery('update vtiger_field set uitype=33 where fieldid=? and tabid=?', array($field->id,$moduleInstance->id));
				$field->setPicklistValues($list);
			} else {
				echo "<b>The field $fieldname should be uitype 1.</b><br>";
			}
		} else {
			echo "<b>Failed to find $fieldname field.</b><br>";
		}
	} else {
		echo "<b>Failed to find $module module.</b><br>";
	}
} else {
	echo "<b>The fieldname and module parameters couldn't be empty</b><br>";
}
