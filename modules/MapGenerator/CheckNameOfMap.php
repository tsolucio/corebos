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
$dataget = $_POST['nameView'];

if (!empty($dataget)) {

    echo CheckName($dataget);

} else {
    echo "";
}

/**
 * this function check if exist in cb_Map the name as you write
 * @param string $value the name of map you want to check
 * @return string rerturn if find the same name or not
 */
function CheckName($value = '')
{
    global $log, $mod_strings, $adb;

    if (!empty($value)) {

        $sql = "SELECT * FROM vtiger_cbmap JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_cbmap.cbmapid WHERE mapname=? AND vtiger_crmentity.deleted=0 ";

        $values = $adb->pquery($sql, array($value));
        $noofrows = $adb->num_rows($values);
        echo $noofrows;
        exit();
        if ($noofrows > 0) {
            return "true";
        } else {
            return "false";
        }

    } else {

        return "";
    }

}
