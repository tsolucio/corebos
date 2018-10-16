
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
 *************************************************************************************************/
//GetLabelName.php

$getValue = $_POST["DefaultValue"];
$DefaultValueFirstModuleField = $_POST["DefaultValueFirstModuleField"];
if (!empty($getValue)) {
    // print_r(explode(":",$getValue));
    echo GetLabelName(explode(":", $getValue)[0], explode(":", $getValue)[1]);
} else if (!empty($DefaultValueFirstModuleField)) {
    // print_r(explode(":",$getValue));
    echo GetLabelName(explode(":", $DefaultValueFirstModuleField)[0], explode(":", $DefaultValueFirstModuleField)[1]);
}

function GetLabelName($tablenam, $columnnam, $fieldname)
{
    global $log;
    $log->debug("Entering getAdvSearchfields(" . $module . ") method ...");
    global $adb;
    global $current_user;
    global $mod_strings, $app_strings;

    $q = $adb->query("SELECT * from vtiger_field WHERE  tablename='$tablenam' and ( columnname='$columnnam'  OR fieldname='$fieldname')");
    return $seq = $adb->query_result($q, 0, "fieldlabel");
}