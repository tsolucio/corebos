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

function __cbwf_setype($arr) {
	$ret = '';
	if (!empty($arr[0]) && strpos($arr[0], 'x') > 0) {
		list($wsid,$crmid) = explode('x', $arr[0]);
		$ret = getSalesEntityType($crmid);
	}
	return $ret;
}

function __cbwf_getimageurl($arr) {
	global $adb;
	$env = $arr[1];
	if (isset($env->moduleName)) {
		$module = $env->moduleName;
	} else {
		$module = $env->getModuleName();
	}
	$data = $env->getData();
	$recordid = $data['id'];
	list($wsid,$crmid) = explode('x', $recordid);
	if ($module == 'Contacts') {
		$imageattachment = 'Image';
	} else {
		$imageattachment = 'Attachment';
	}
	$sql = "select vtiger_attachments.*,vtiger_crmentity.setype
		from vtiger_attachments
		inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
		where vtiger_crmentity.setype='$module $imageattachment' and vtiger_attachments.name = ? and vtiger_seattachmentsrel.crmid=?";
	$image_res = $adb->pquery($sql, array(str_replace(' ', '_', decode_html($arr[0])),$crmid));
	if ($adb->num_rows($image_res)==0) {
		$sql = 'select vtiger_attachments.*,vtiger_crmentity.setype
			from vtiger_attachments
			inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
			where vtiger_attachments.name = ? and vtiger_seattachmentsrel.crmid=?';
		$image_res = $adb->pquery($sql, array(str_replace(' ', '_', $arr[0]),$crmid));
	}
	if ($adb->num_rows($image_res)>0) {
		$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
		$image_path = $adb->query_result($image_res, 0, 'path');
		$image_name = decode_html($adb->query_result($image_res, 0, 'name'));
		if ($image_name != '') {
			$imageurl = $image_path . $image_id . '_' . urlencode($image_name);
		} else {
			$imageurl = '';
		}
	} else {
		$imageurl = '';
	}
	return $imageurl;
}

function __cb_globalvariable($arr) {
	$ret = null;
	if (!empty($arr[0])) {
		$ret = GlobalVariable::getVariable($arr[0], null);
	}
	return $ret;
}
?>