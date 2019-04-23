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
 *************************************************************************************************
 *  Module       : Mass Upload Image On Product
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
include 'vtlib/Vtiger/Module.php';
global $current_user, $adb;
$current_user = Users::getActiveAdminUser();

$productID = 2616;
$images = array(
	'themes/images/yes.gif',
	'themes/images/no.gif',
);
$product = CRMEntity::getInstance('Products');
$product->retrieve_entity_info($productID, 'Products');
$_REQUEST['module'] = 'Products';
$_REQUEST['action'] = 'ProductsAjax';
$_REQUEST['file'] = 'UploadImage';
$_REQUEST['record'] = $productID;
$finfo = finfo_open(FILEINFO_MIME_TYPE);
foreach ($images as $image) {
	$_FILES = array();
	$imagename = basename($image);
	$_FILES['file'] = array(
		'name' => $imagename,
		'type' => finfo_file($finfo, $image),
		'tmp_name' => $image,
		'error' => 0,
		'size' => filesize($image),
	);
	$product->insertIntoAttachment($productID, 'Products', true);
	echo "Image $image added to product $productID \n";
}
?>
