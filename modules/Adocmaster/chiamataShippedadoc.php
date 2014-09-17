<?php 
 /*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Adecuaciones
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/
include_once('data/CRMEntity.php');
include_once('include/database/PearDatabase.php');
require_once("modules/hpsmlog/hpsmlog.php");
//ini_set("error_reporting","E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED");
global $adb;
$lines =file('import/chiamatelog.csv');
$imported_status = array();
$imp1='';
$imp2='';
$seprator = ";";
$i = 0;

foreach($lines as $data)
{
if($i != 0){
    if($i%2==0) $col='black';
else $col='red';
$d=explode($seprator,$data);
$d[0] = str_replace('"','',$d[0]);
$d[1] = str_replace('"','',$d[1]);
$d[2] = str_replace('"','',$d[2]);
if($d[3] != "") {
$dt = explode(" ",str_replace('"','',$d[3]));
if($dt != null) 
{
   $date = strtotime($dt[0]);
   $date_add = date("Y-m-d", $date);
}
else $date_add = date("Y-m-d");}
//echo $date_add;
$adoc=$_REQUEST['record'];
$ap=$adb->query("select * from vtiger_adocmaster where adocmasterid=$adoc");
$idproj=$adb->query_result($ap,0,"project");
$num=$adb->query_result($ap,0,"nrdoc");

if($d[0] == "") $d[0] = 1;
$projectQuery = $adb->pquery("Select * from vtiger_project
                             join vtiger_pcdetails on vtiger_project.projectid=vtiger_pcdetails.project 
                             join vtiger_products on vtiger_products.productid = vtiger_pcdetails.linktoproduct
                             join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_pcdetails.pcdetailsid
                             where deleted = 0 
                             and vtiger_project.projectid=? and productname=?",array($idproj,$d[2]));
if($adb->num_rows($projectQuery)==1){
$pcdetailsproject = $adb->query_result($projectQuery,0,'projectid');

$subs=$adb->query_result($projectQuery,0,"substatusproj");
for($i1=0;$i1<$adb->num_rows($projectQuery);$i1++){
$pcdetailsid = $adb->query_result($projectQuery,$i1,'pcdetailsid');
$pcstatus = $adb->query_result($projectQuery,$i1,'pcdetailsstatus');
$pcfinerip = $adb->query_result($projectQuery,$i1,'fineriparazione');
$pcname = $adb->query_result($projectQuery,$i1,'pcdescriptionname');
if($pcstatus!='ANNULLATA DA CAT' && $pcstatus!='ANNULLATO ORDINE' && $pcstatus!='INVIATA IN RIPARAZIONE TEKNEMA' && $pcfinerip!='DOA'){
$query = $adb->pquery("Update vtiger_pcdetails set pcdetailsstatus=?,
                       qtyshipped=?, lavorateexp=?, dataprevistaconsegna=?,ddtnumber=? ,statologistica=?,recref=?
where pcdetailsid=?",array('SHIPPED',$d[0],1,$date_add,$num,'JUST SHIPPED',$d[4],$pcdetailsid));
$logpart.="<span style=\"color:$col\">Parte aggiornata ".$pcname.'<br>';
$logs[$i-1].="Parte aggiornata ".$pcname;
}
else if ($pcstatus=='ANNULLATA DA CAT' || $pcstatus=='ANNULLATO ORDINE' || $pcstatus=='INVIATA IN RIPARAZIONE TEKNEMA')
{   $logpart.="<span style=\"color:green\">Parte non aggiornata ".$pcname.' '.$pcstatus.'</span><br><br>';
$logs[$i-1].="Parte non aggiornata ".$pcname.' '.$pcstatus;}
else if ($pcfinerip=='DOA'){
    $logpart.="<span style=\"color:green\">Parte non aggiornata ".$pcname.' DOA </span><br><br>';
$logs[$i-1].="Parte non aggiornata ".$pcname." DOA";}
}
//if($adb->getAffectedRowCount($query) == 0) 
//    $imp1= "Modification of $d[1]-$d[2] failed ";
//if($adb->query_result($projectQuery,0,"substatusproj")!="CAT_parts request" && $adb->query_result($projectQuery,0,"statopartech")=='SHIPPED')
//$imp2=" Substatus errato $d[2]";
//$imported_status[] =$imp1.' '.$imp2;
//else {
$projectquery = $adb->query("Select project,pcdetailsstatus from vtiger_pcdetails
                             join vtiger_project on vtiger_pcdetails.project=vtiger_project.projectid
                             join vtiger_crmentity on crmid=pcdetailsid
                             where vtiger_project.projectid = $pcdetailsproject and deleted=0");
$nrp = $adb->num_rows($projectquery);
if($nrp!=0){
$requestPart = 0; 
$pending = 0;
$shipped = 0;
for($i2=0;$i2<$nrp;$i2++){
$pcdetailsstatus = $adb->query_result($projectquery,$i2,'pcdetailsstatus');

//$i = 1;


    if($pcdetailsstatus == "PARTE RICH. DAL TECNICO" || $pcdetailsstatus == "VERIFICA DISPONIB"
             || $pcdetailsstatus == "DA ORDINARE" || $pcdetailsstatus == "REQUESTED BY LSP/CAT"){
        $requestPart++;
    }
    else if($pcdetailsstatus == "ORDINATA" || $pcdetailsstatus == "IN LOAN" || $pcdetailsstatus=="ASSEGNATA"){
        $pending++;
    }
    else if($pcdetailsstatus == "SHIPPED") $shipped++;
             else if($pcdetailsstatus == "ANNULLATO ORDINE" || $pcdetailsstatus == "ANNULLATA DA CAT" ) $annul++;

}
$diff=$nrp-$shipped;
if($requestPart == $nrp)  $adb->pquery("Update vtiger_project set statopartech = ? where projectid = ?",array('PART REQUEST',$pcdetailsproject));
else if($shipped >=1 && $shipped != $nrp && $annul!=$diff)   $adb->pquery("Update vtiger_project set statopartech = ? where projectid = ?",array('PARTIAL SHIPPING',$pcdetailsproject));
else if($pending > 0)    $adb->pquery("Update vtiger_project set statopartech = ? where projectid = ?",array('PENDING',$pcdetailsproject));
else if(($shipped == $nrp) || ($shipped>0 && $diff==$annul))  $adb->pquery("Update vtiger_project set statopartech = ? where projectid = ?",array('SHIPPED',$pcdetailsproject));
}
global $current_user;
$current_user->id =1;
//$adb->pquery("Update vtiger_project set statopartech=? where projectid=?",array('SHIPPED',$pcdetailsproject));

require_once ("include/utils/utils.php");
require_once("include/database/PearDatabase.php");
require_once("database/DatabaseConnection.php");
require_once ("include/CustomFieldUtil.php");
require_once ("data/Tracker.php");
require_once("modules/com_vtiger_workflow/VTWorkflowManager.inc");
require_once("modules/com_vtiger_workflow/VTTaskManager.inc");
require_once("modules/com_vtiger_workflow/VTWorkflowApplication.inc");
require_once ("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
require_once("include/utils/CommonUtils.php");
require_once("include/events/SqlResultIterator.inc");
require_once("modules/com_vtiger_workflow/VTWorkflowUtils.php");
require_once("modules/Project/Project.php");
$focus1=new Project();
$focus1->retrieve_entity_info($pcdetailsproject,"Project");
$focus1->id=$pcdetailsproject;
$em = new VTEventsManager($adb);
// Initialize Event trigger cache
$em->initTriggerCache();
$entityData  = VTEntityData::fromCRMEntity($focus1);
$em->triggerEvent("vtiger.entity.beforesave.modifiable", $entityData);
$em->triggerEvent("vtiger.entity.beforesave", $entityData);
$em->triggerEvent("vtiger.entity.beforesave.final", $entityData);
$em->triggerEvent("vtiger.entity.aftersave", $entityData);
//}
$projectQuery1=$adb->query("select * from vtiger_project where projectid=$pcdetailsproject");

if($adb->query_result($projectQuery1,0,"substatusproj")=="CAT_parts sent by Teknema" && $adb->query_result($projectQuery1,0,"statopartech")=='SHIPPED')
{   $logpart.="Movimento frecce avvenuto con successo </span><br><br>";
$logs[$i-1].="Movimento frecce avvenuto con successo";
}
elseif($subs!="CAT_parts request" && strstr($subs,"attesa parti")!='' && $adb->query_result($projectQuery1,0,"statopartech")=='SHIPPED')
{$logpart.="<span style='color:blue'> Substatus errato $d[2] </span></span><br><br>";
$logs[$i-1].="Substatus errato $d[2]";
}

else { $logpart.="Substatus non modificato </span><br><br>";
$logs[$i-1].="Substatus non modificato";
}
}
else {$logpart.="<span style=\"color:$col\">Parte non trovata ".$d[1].' '.$d[2].'</span> <br><br>';
$logs[$i-1].="Parte non trovata ".$d[1].' '.$d[2];
}

$focus = new hpsmlog(); 
$focus->column_fields['assigned_user_id']=1;
$focus->column_fields['chiamata']= $logs[$i-1];
$focus->column_fields['incidentid']='';
$focus->column_fields['citta']='';
$focus->column_fields['project']=$pcdetailsproject;
$focus->column_fields['tiposcript']='Script parti';
$focus->saveentity("hpsmlog");
    //$imported_status[$i-1] ='<span style="color:'.$col.'">'.$logpart.'</span>';
}
$i++;
}

echo '<div style="font-size:14px"><b>'.$logpart.'</b></div>';

?>
