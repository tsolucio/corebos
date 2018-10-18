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
$data=json_decode($_POST['fields']);
$queryid=$_POST['queryid'];
$mapid=$_POST['MapID'];
global $adb;
$q=$adb->query("select sequence from mvqueryhistory where id='$queryid' order by sequence DESC");
//$nr=$adb->num_rows($q);
$seq=$adb->query_result($q,0,0)+1;
$adb->query("update mvqueryhistory set active=0 where id='$queryid'");

if($mapid!=""){
  $seqmap=count($data);
  $adb->pquery("insert into mvqueryhistory values (?,?,?,?,?,?,?,?,?,?,?)",array($queryid,$data[$seqmap-1]->FirstModuleJSONvalue,$data[$seqmap-1]->FirstModuleJSONtext,$data[$seqmap-1]->SecondModuleJSONvalue,$data[$seqmap-1]->SecondModuleJSONtext,$data[$seqmap-1]->ValuesParagraf,$seq,'1',$data[$seqmap-1]->FirstModuleJSONfield,$data[$seqmap-1]->SecondModuleJSONfield,$data[$seqmap-1]->Labels));
}else {
  $adb->pquery("insert into mvqueryhistory values (?,?,?,?,?,?,?,?,?,?,?)",array($queryid,$data[$seq-1]->FirstModuleJSONvalue,$data[$seq-1]->FirstModuleJSONtext,$data[$seq-1]->SecondModuleJSONvalue,$data[$seq-1]->SecondModuleJSONtext,$data[$seq-1]->ValuesParagraf,$seq,'1',$data[$seq-1]->FirstModuleJSONfield,$data[$seq-1]->SecondModuleJSONfield,$data[$seq-1]->Labels));
}
echo 'ok';
