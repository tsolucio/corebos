<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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
global $root_directory, $current_user;
$data = json_decode($_REQUEST['data'], true);
foreach ($data as $field => $string) {
	list($type, $file) = explode(';', trim($string));
	$type = explode('/', $type);
	$file = str_replace(array('base64,', ' '), array('', '+'), $file);
	$file = base64_decode($file);
	$info = finfo_open();
	$mime_type = finfo_buffer($info, $file, FILEINFO_MIME_TYPE);
	$path = $root_directory.'cache/massedit/';
	if (!is_dir($path)) {
		mkdir($path);
	}
	$filename = $field.'_'.$current_user->id.'.'.$type[1];
	file_put_contents($path.$filename, $file);
}