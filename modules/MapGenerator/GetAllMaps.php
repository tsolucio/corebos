
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

/**
 * This function is to get all maps from database a
 * @param string $value  this param is if you want to filter by map type
 * @return  a list of maps
 */
function GetMaps($value = "")
{
    global $adb, $root_directory, $log;
    $log->debug("Info!! GetMaps Start here ");
    if (!empty($value)) {
        $log->debug("Info!! Value is not ampty");
        if ($value == "ALL") {
            $sql = "SELECT cb.*,cr.* FROM vtiger_cbmap cb JOIN  vtiger_crmentity cr ON cb.cbmapid=cr.crmid WHERE cr.deleted=0 ORDER BY cb.maptype ";
        } else {
            $sql = "SELECT cb.*,cr.* FROM vtiger_cbmap cb JOIN  vtiger_crmentity cr ON cb.cbmapid=cr.crmid WHERE cr.deleted=0 AND  maptype='$value' ORDER BY cb.maptype ";
        }
        $log->debug("Info!! This is the query ----- " . $sql);
        $result = $adb->query($sql);
        $num_rows = $adb->num_rows($result);
        $historymap = "";
        $a = "";
        if ($num_rows != 0) {
            for ($i = 1; $i <= $num_rows; $i++) {
                $MapID = $adb->query_result($result, $i - 1, 'cbmapid');
                $queryid = $adb->query_result($result, $i - 1, 'mvqueryid');
                $MapName = $adb->query_result($result, $i - 1, 'mapname');
                $MapType = $adb->query_result($result, $i - 1, 'maptype');
                if ($MapType != $historymap) {
                    $historymap = $MapType;
                    $a .= '<optgroup label="' . $MapType . '">';
                    $a .= '<option value="' . $MapType . '#' . $MapID . '#' . $queryid . '">' . $MapName . '</option>';
                } else {
                    $a .= '<option value="' . $MapType . '#' . $MapID . '#' . $queryid . '">' . $MapName . '</option>';
                }
                $log->debug("Info!! This is output -------------" . $a);
            }
        } else { $log->debug("Info!! The database ios empty or something was wrong");}
        return $a;

    } else {
        return "";
    }

}