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
global $calpath;
global $app_strings, $mod_strings;
global $app_list_strings;
global $theme;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
require_once('include/utils/utils.php');
require_once 'include/Webservices/Utils.php';

global $adv_filter_options;

$adv_filter_options = array("e" => "" . $mod_strings['equals'] . "",
	"n" => "" . $mod_strings['not equal to'] . "",
	"s" => "" . $mod_strings['starts with'] . "",
	"ew" => "" . $mod_strings['ends with'] . "",
	"c" => "" . $mod_strings['contains'] . "",
	"k" => "" . $mod_strings['does not contain'] . "",
	"l" => "" . $mod_strings['less than'] . "",
	"g" => "" . $mod_strings['greater than'] . "",
	"m" => "" . $mod_strings['less or equal'] . "",
	"h" => "" . $mod_strings['greater or equal'] . "",
	"b" => "" . $mod_strings['before'] . "",
	"a" => "" . $mod_strings['after'] . "",
	"bw" => "" . $mod_strings['between'] . "",
);
