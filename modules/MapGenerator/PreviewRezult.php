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

global $adb, $log, $current_user, $default_charset;

$selectedfieldsfromhistory = $_POST['queryhistory'];
$listoffileds = explode("FROM", $selectedfieldsfromhistory);
$myArray = explode(",", substr($listoffileds[0], 6));
$arraylistofields = array();
for ($j = 0; $j < count($myArray); $j++) {
    $expdies = explode(".", $myArray[$j]);
    array_push($arraylistofields, $expdies[1]);
}

$tableshow = '<table style="border-style:solid;border-width:1px"><thead> <tr class="slds-text-title_caps">  ';

foreach ($arraylistofields as $value) {
    $tableshow .= '<th scope="col">';
    $tableshow .= '<div class="slds-truncate" title="' . $value . '">' . $value . '</div>';
    $tableshow .= '</th>';

}

$tableshow .= ' </tr> </thead> <tbody >';
$query = $adb->query($selectedfieldsfromhistory);
$nr = $adb->num_rows($query);
$reference = array();
$ind = 0;
for ($ref = 0; $ref < $adb->num_rows($query); $ref++) {
    $tableshow .= '<tr>';
    foreach ($arraylistofields as $value) {
        $valuess = $adb->query_result($query, $ref, $ind);
        if ($valuess != '' && $valuess != null) {
            $tableshow .= '<td scope="row" data-label="' . $value . '"> <div class="slds-truncate" title="' . $valuess . '">' . $valuess . '</div></td>';
        } else {
            $tableshow .= '<td scope="row" data-label="' . $value . '"> <div class="slds-truncate" title="Haven\'t value">-</div></td>';
        }
        $ind++;

    }
    $tableshow .= '</tr>';

}

$tableshow .= '</tbody></table>';
echo $tableshow;
