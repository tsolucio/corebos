<?php
global $adb,$log;
 $entitylogid=$_REQUEST['entitylogid'];
 $modulename=$_REQUEST['name'];
//$log->debug("test12". $name);
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
