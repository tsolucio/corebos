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
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * Returns the image data of a given attachment
 * params
 *   imageid  crmid of the image attachment. see getProductImage webservice call
 *   shownoimage  boolean to indicate if an empty image should be sent if no attachment is found. true by default
 * return
 *   image data with corresponding web mime header
 *   this can be directly called in the SRC attribute of an <img> directive
 *************************************************************************************************/
$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';

$shownoimage = (empty($_REQUEST['shownoimage']) ? true : vtlib_purify($_REQUEST['shownoimage']));
$pdoimgid = vtlib_purify($_REQUEST['imageid']);
if (!empty($pdoimgid)) {
	$query = 'select vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.type
			 from vtiger_attachments
			 inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
			 where (vtiger_crmentity.setype LIKE "%Image" or vtiger_crmentity.setype LIKE "%Attachment")
			  and deleted=0 and vtiger_attachments.attachmentsid=?';
	$result_image = $adb->pquery($query, array($pdoimgid));
	if ($result_image && $adb->num_rows($result_image)==1) {
		$image_orgname = decode_html($adb->query_result($result_image, 0, 'name'));
		$imagepath = $adb->query_result($result_image, 0, 'path');
		$imagetype = $adb->query_result($result_image, 0, 'type');
		$image = $imagepath.$pdoimgid."_".urlencode($image_orgname);
		$shownoimage = false;
	}
}

if ($shownoimage) {
	header("Content-Type: image/png");
	header("Pragma: public");
	header("Cache-Control: private");
	header("Content-Disposition: filename=noimage.png");
	header("Content-Description: php/coreBOS Generated Data");
	$im = @imagecreate(110, 110);
	if (!$im) {
		die("Cannot Initialize new GD image stream");
	}
	$background_color = imagecolorallocate($im, 255, 255, 255);
	$text_color = imagecolorallocate($im, 0, 0, 0);
	imagestring($im, 4, 22, 25, getTranslatedString('No Image'), $text_color);
	imagestring($im, 4, 19, 55, getTranslatedString('Available'), $text_color);
	imagepng($im);
	imagedestroy($im);
} else {
	header("Content-Type: $imagetype");
	header("Pragma: public");
	header("Cache-Control: private");
	header("Content-Disposition: filename=$image_orgname");
	header("Content-Description: php/coreBOS Generated Data");
	readfile($image);
}
?>