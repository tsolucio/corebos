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
include 'All_functions.php';
include 'modfields.php';
global $adb;

$FirstmoduleXML = ""; //"edmondi" . $_POST['MapID'];
if (isset($_REQUEST['MapID'])) {
    $mapid = $_REQUEST['MapID'];
    $qid = $_REQUEST['queryid'];
    $sql = "SELECT * from mvqueryhistory where id=? AND active=?";
    $result = $adb->pquery($sql, array($qid, 1));
    $FirstmoduleXML = $adb->query_result($result, 0, 'firstmodule');
    //$FirstmoduleXML = takeFirstMOduleFromXMLMap($mapid);
    // echo "brenda kushtit mapID ".$mapid;
}

if (isset($_REQUEST['secModule']) && isset($_REQUEST['firstModule'])) {
    $secModule = implode(',', array_keys(array_flip(explode(',', $_REQUEST['secModule']))));
    $modulesAllowed = '"' . $_REQUEST['firstModule'] . '","' . str_replace(',', '","', $secModule) . '"';
    $query = "SELECT * from vtiger_tab where isentitytype=1 and name<>'Faq'
        and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and presence=0
        and name in ($modulesAllowed)";
    // echo "brenda ifit seltab etj ";
} else {
    $query = "SELECT * from vtiger_tab where isentitytype=1 and name<>'Faq'
        and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and presence=0";
    //echo "brenda elsit nese nuk plotesohet if ";
}
$result = $adb->query($query);
$num_rows = $adb->num_rows($result);
//echo "para ciklit fore  ";
if ($num_rows != 0) {
    //echo "if num rows eshte e madhe se 0 ";
    for ($i = 1; $i <= $num_rows; $i++) {
        //echo "brenda ciklit for ".$i;
        $modul1 = $adb->query_result($result, $i - 1, 'name');

        $a .= $modul1 . '#';
        ///echo "nese nuk  plotesohet kushti firstmodulexml";

    }
}

if (!empty($a)) {

    $dataarray = explode("#", $a);
    // echo getModFields($modules, $acno.$dbname);

    if (!empty($dataarray)) {
        foreach ($dataarray as $key) {
            // echo "value=".$key."<br>";
            $datareturn .= getModFields($key, $acno . $dbname);
        }
        // $datareturn.=getModFields($modules);
    } else {
        //echo getModFields($modules);
    }

    $selectedfiel = "<option value='' >( Select a field )</option>" . $datareturn;
    echo $selectedfiel;
}
