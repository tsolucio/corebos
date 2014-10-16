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
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/
global $adb,$log;
 $entitylogid=$_REQUEST['entitylogid'];
 $modulename=$_REQUEST['name'];
 $tabid=gettabId($modulename);
 $querytabname=$adb->pquery("select tablename,entityidfield from vtiger_entityname where modulename='".$modulename."'and tabid=?",array($tabid));
 $tablename =$adb->query_result($querytabname,0,'tablename');
 $entityidfield=$adb->query_result($querytabname,0,'entityidfield');

$query=$adb->pquery("select * from vtiger_entitylog where entitylogid=?",array($entitylogid));
 $finalstate=$adb->query_result($query,0,'finalstate');
 $relatedto=$adb->query_result($query,0,'relatedto');

$data=explode(';',$finalstate);
                 
 foreach($data as $d)
  { 
    if(stristr($d,'fieldname='))
    $fldname=substr($d,strpos($d,'fieldname=')+10);
    if(stristr($d,'oldvalue='))
    $oldvl=substr($d,strpos($d,'oldvalue=')+9);
    if(stristr($d,'newvalue'))
    $newvl=substr($d,strpos($d,'newvalue=')+9);
        }
        
       
  $qdatatype=$adb->query("select * from vtiger_field where columnname='$fldname' and tablename='$tablename'  and tabid=$tabid");    
    
    
  $datatype= $adb->query_result($qdatatype,0,'typeofdata');
     $dtt1=explode('~',$datatype);
     $dtt=$dtt1[0];
     if($dtt=='N'|| $dtt=='NN' || $dtt=='I')
     {   $update= $adb->query("update $tablename set $fldname=".intval($oldvl)."  where $entityidfield=$relatedto ")  ;    
     } else
     {   $update=$adb->query("update $tablename set $fldname='$oldvl' where $entityidfield=$relatedto ")  ;    
     }
  if( $adb->database->Affected_Rows($update)!=0)
        echo "Restored to $oldvl";
    else
        echo"No restore.";
?>
