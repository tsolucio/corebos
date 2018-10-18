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
 *  Module       : Duplicate Related Record functionality
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'include/utils/duplicate.php';
require_once 'include/utils/CommonUtils.php';

if (isset($_REQUEST['module_name']) && isset($_REQUEST['record_id'])) {
	$module = vtlib_purify($_REQUEST['module_name']);
	$rec_id = vtlib_purify($_REQUEST['record_id']);
	$map = $module.'_DuplicateRelations';
	$new_record_id = duplicaterec($module, $rec_id, $map);
	if (isset($_REQUEST['redirect'])) {
		$msg = '&error_msgclass=cb-alert-info&error_msg='.getTranslatedString('RecordDuplicated');
		header('Location: index.php?module='.$module.'&action=DetailView&record='.$new_record_id.$msg);
	} else {
		echo json_encode(array('module'=>$module, 'record_id'=>$new_record_id));
	}
	exit();
}
?>