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
global $adb;
$tab = $_REQUEST['nometab'];
$reportid = $_REQUEST['reportId'];
$accins = $_REQUEST['accins'];
$accQuery = $adb->pquery("Select * from vtiger_accountinstallation
                         where accountinstallationid = ?",array($accins));

$dbname = $adb->query_result($accQuery,0,"dbname");
$acno = $adb->query_result($accQuery,0,"acin_no");
$port=$adb->query_result($accQuery,0,"port");
$ip=$adb->query_result($accQuery,0,"hostname");
$pass=$adb->query_result($accQuery,0,"password");
$us=$adb->query_result($accQuery,0,"username");
$path=$adb->query_result($accQuery,0,"vtigerpath");
$db = $dbname.$acno;
$id = str_replace(" ","",$reportid);
//generate php script
$ourFileName = $root_directory."script_report_".$id.$tab.".php";
$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file"); 

$data = "<?php \r\n";
$data.="global \$adb;\r\n";
$data.="include 'function.php';\r\n";
$data.="\$current_user->id=1;\r\n";
$data.="include_once('modules/Reports/Reports.php');\r\n";
$data.="include('modules/Reports/ReportRun.php');\r\n";
$data.="include_once('include/utils/CommonUtils.php');\r\n";
$data.="\$tab='".$tab."';\r\n";
$data.="\$reportid='".$reportid."';\r\n";

$data.="\$col1=Array();\r\n";
$data.="\$focus1=new ReportRun(\$reportid);\r\n";
$data.="\$currencyfieldres = \$adb->pquery(\"SELECT tabid, fieldlabel, uitype from vtiger_field WHERE uitype in (71,72,10)\", array());\r\n";
$data.="if(\$currencyfieldres) {\r\n";
$data.="foreach(\$currencyfieldres as \$currencyfieldrow) {\r\n";
$data.="\$modprefixedlabel = getTabModuleName(\$currencyfieldrow['tabid']).\" \".\$currencyfieldrow['fieldlabel'];\r\n";
$data.="\$modprefixedlabel = str_replace(' ','_',\$modprefixedlabel);\r\n";
$data.="if(\$currencyfieldrow['uitype']!=10){\r\n";
$data.="if(!in_array(\$modprefixedlabel, \$focus1->convert_currency) && !in_array(\$modprefixedlabel, \$focus1->append_currency_symbol_to_value)) {\r\n";
$data.="\$focus1->convert_currency[] = \$modprefixedlabel;\r\n";
$data.="}\r\n";
$data.="} else {\r\n";
$data.="if(!in_array(\$modprefixedlabel, \$focus1->ui10_fields)) {\r\n";
$data.="}\r\n";
$data.="}\r\n";
$data.="}\r\n";
$data.="}\r\n";

$data.="\$stdfiltersql = \"select vtiger_reportdatefilter.* from vtiger_report\"; \r\n";
$data.="\$stdfiltersql .= \" inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid\"; \r\n";
$data.="\$stdfiltersql .= \" where vtiger_report.reportid = ?\"; \r\n";
            
$data.="\$result = \$adb->pquery(\$stdfiltersql, array(\$reportid)); \r\n";
//$data.="\$startdate = \$adb->query_result(\$result,0,'startdate'); \r\n";
//$data.="\$enddate = \$adb->query_result(\$result,0,'enddate'); \r\n";
//Modification 18-05-2015
$data.="\$curYear = date('Y'); \r\n";
$data.="\$startdate = \$curYear.'-01-01'; \r\n";
$data.="\$enddate = date('Y-m-d'); \r\n";
$data.="\$datecolumnname = \$adb->query_result(\$result,0,'datecolumnname');\r\n";
$data.="\$fieldName= explode(\":\",\$datecolumnname);\r\n";
$data.="if(\$fieldName[1] != \"\") \$condition = \" and \$fieldName[0].\$fieldName[1] between '\$startdate' and '\$enddate'\";\r\n";

$data.="\$holidays = array();\r\n";
//working days minus saturday and sunday
$data.="\$nr_working_days = getWorkingDays(\$startdate,\$enddate,\$holidays);\r\n";
$data.="\$usersQuery = \$adb->query(\"Select distinct smownerid as user from vtiger_potential join vtiger_crmentity \r\n"; $data.=" on crmid = potentialid and deleted = 0 \$condition\"); \r\n";
//table columns
$data.="\$colonne=array(); \r\n";
$data.="\$col1 = array('FunzionarioCommerciale','Ordinato','OrdinatoPrevFA','ObbiettivoFA',
                'VisiteClienti','VisiteCL_GG','AppTeleMkt','OrdinidaTeleMkt','NroOfferte',
                'NroOfferte_GG','Forecast');\r\n";
$data.="\$colonne = array('ID INT(9) UNSIGNED NOT NULL AUTO_INCREMENT,PRIMARY KEY (ID)','FunzionarioCommerciale VARCHAR(250)','Ordinato VARCHAR(250)',
            'OrdinatoPrevFA VARCHAR(250)','ObbiettivoFA VARCHAR(250)','VisiteClienti VARCHAR(250)','VisiteCL_GG VARCHAR(250)','AppTeleMkt VARCHAR(250)','OrdinidaTeleMkt VARCHAR(250)',
            'NroOfferte VARCHAR(250)','NroOfferte_GG VARCHAR(250)','Forecast VARCHAR(250)');\r\n";
        
$data.="\$col=implode(\",\",\$colonne); \r\n";
$data.="\$colInput = implode(\",\", \$col1);\r\n";
$data.="\$adb->query(\"drop table IF EXISTS mv_\$id\$tab\"); \r\n";
$data.="\$adb->query(\"create table mv_\$id\$tab (\$col) ENGINE=InnoDB\");  \r\n";
$data.="\$nr_Users = \$adb->num_rows(\$usersQuery); \r\n";
global $log;
// For each user get values of columns
//[0] => User Info
$data.="for(\$i = 0; \$i < \$nr_Users; \$i++){ \r\n";
    $data.="\$rowData = array_fill(0,10, '0'); \r\n";
    $data.="\$userId = \$adb->query_result(\$usersQuery,\$i,0); \r\n";
    $data.="\$rowData[0] = \"'\".getUserFullName(\$userId).\"'\"; \r\n";
    
   //Ordinato till current_date
    $data.="\$ordQuery = \$adb->pquery(\"Select sum(IF(sales_stage = 'Closed Won', amount, 0)) As cw \r\n";
                             $data.="from vtiger_potential join vtiger_crmentity on   \r\n";
                             $data.="potentialid = crmid where closedate  BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') AND CURDATE() \r\n";
                             $data.="and smownerid = ? and deleted = 0 \$condition\",array(\$userId)); \r\n";
    
   $data.="\$cw = \$adb->query_result(\$ordQuery,0,'cw'); \r\n";
   //[1] => Ordinato
   $data.="if(\$cw != '')  \$rowData[1] = \$cw; \r\n";
   //[2] => Ordinato Prevedibile finno anno
//    $data.="\$ordFinnoAnnoQuery = \$adb->pquery(\"Select sum(IF(sales_stage Not like '%closed%', amount, 0)) As pro  \r\n";
//                                        $data.="  from vtiger_potential join vtiger_crmentity on  \r\n";
//                                        $data.="  potentialid = crmid where closingdate BETWEEN CURRENT_DATE() AND  CONCAT(YEAR(CURDATE()),'-12-31') and deleted = 0 and smownerid = ? \$condition \",array(\$userId));  \r\n";
//    
 
//    $data.="\$pro = \$adb->query_result(\$ordFinnoAnnoQuery,0,'pro'); \r\n";
    $data.="\$pro = (\$cw/\$nr_working_days)*215;\r\n";
    $data.="if(\$pro != '' ) \$rowData[2] = \$pro;   \r\n";
   
    $data.="\$ordTelemarkQuery = \$adb->pquery(\"Select sum(IF(operatorecall != ' ' AND sales_stage != 'Closed Won' && sales_stage != 'Closed Lost' , amount, 0)) as ordt  \r\n";
    $data.="  from vtiger_potential join vtiger_crmentity on  \r\n";
    $data.="  potentialid = crmid where closingdate BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') AND CURDATE() and deleted = 0 and smownerid = ? \$condition \",array(\$userId));  \r\n";
    
 
    $data.="\$ordt = \$adb->query_result(\$ordTelemarkQuery,0,'ordt'); \r\n";
    $data.="if(\$ordt != '' ) \$rowData[7] = \$ordt;   \r\n";
    
    $data.="\$obfnoQuery = \$adb->pquery(\"Select phone_home from vtiger_users where id = ?\",array(\$userId));\r\n";
    $data.="\$obfno = \$adb->query_result(\$obfnoQuery,0,'phone_home');\r\n";
    $data.="if(\$obfno != '' ) \$rowData[3] = \$obfno; \r\n";
     //Forecast query

    $data.="\$frcQuery = \$adb->pquery(\"Select sum(IF(sales_stage != 'Closed Won' && sales_stage != 'Closed Lost',amount,0)) as temp  \r\n";
                                $data.="from vtiger_potential join vtiger_crmentity on \r\n";
                                $data.="potentialid = crmid where smownerid = ? and deleted = 0 and probclasses <> '' \$condition\",array(\$userId)); \r\n";
    $data.="\$temp = \$adb->query_result(\$frcQuery,0,'temp'); \r\n";
    $data.="if(\$temp != '' ) \$rowData[10] = \$temp;  \r\n";
    //visite Clienti
   $data.="\$vistieclientiQuery = \$adb->pquery(\"select count(IF(event_type = 'Appuntamento', 1, 0)) as ev,count(IF(event_type = 'Appuntamento' AND vtiger_task.winoperatore != ' ', 1, 0)) as evo \r\n";
                                    $data.="  from vtiger_task join vtiger_potential on taskid = potentialtotask \r\n";
                                    $data.="  join vtiger_crmentity on potentialid = crmid where \r\n";
                                    $data.= "closedate  BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') AND CURDATE() and deleted = 0 and smownerid = ? \$condition\",array(\$userId)); \r\n";

     $data.="\$rowData[4] = \$adb->query_result(\$vistieclientiQuery,0,'ev'); \r\n"; 
     $data.="\$rowData[5] = round(\$adb->query_result(\$vistieclientiQuery,0,'ev')/\$nr_working_days,2);\r\n";
     $data.="\$rowData[6] = \$adb->query_result(\$vistieclientiQuery,0,'evo');  \r\n";
    
    //Document query 
    $data.="\$docQuery = \$adb->pquery(\"select count(IF(foldername = 'OFFERTE', 1, 0))as  docs from  \r\n";
                            $data.=" vtiger_potential join  vtiger_senotesrel as vtiger_senotesreltmpDocuments \r\n";
                            $data.=" ON (vtiger_potential.potentialid=vtiger_senotesreltmpDocuments.crmid) \r\n";
                            $data.=" join (select vtiger_notes.* from vtiger_notes inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid \r\n"; 
                            $data.=" and vtiger_crmentity.createdtime BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') AND CURDATE() and vtiger_crmentity.deleted=0) \r\n"; 
                            $data.=" as vtiger_notes on vtiger_notes.notesid=vtiger_senotesreltmpDocuments.notesid \r\n";
                            $data.=" left join vtiger_crmentity as vtiger_crmentityDocuments on vtiger_crmentityDocuments.crmid=vtiger_notes.notesid \r\n";
                            $data.=" left join vtiger_attachmentsfolder on vtiger_attachmentsfolder.folderid=vtiger_notes.folderid \r\n";
                            $data.=" join vtiger_crmentity on vtiger_potential.potentialid = vtiger_crmentity.crmid \r\n";
                            $data.="and vtiger_crmentityDocuments.deleted=0  and vtiger_crmentity.smownerid=? \$condition\",array(\$userId)); \r\n";
    
    $data.="\$rowData[8] = \$adb->query_result(\$docQuery,0,'docs');  \r\n";
    $data.="\$rowData[9] = round(\$adb->query_result(\$docQuery,0,'docs')/\$nr_working_days,2);   \r\n";

 $data.="\$row = implode(\",\",\$rowData); \r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$id\$tab(\$colInput)  values(\$row)\"); \r\n";
 $data.="}\r\n";
fwrite($ourFileHandle,$data);
if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");

if(!($con = ssh2_connect($ip, $port))){
  echo "fail: unable to establish connection\n";

} else { echo 'Connected';
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, $us, $pass)) {
        $msgc = $mod_strings["faila"];
    }
    else {
        $msgc = $mod_strings["succ"];
        $serv = $path."/modules/BiServer/Reports/script_report_".$id.$tab.".php";
        $rootPath = $path."/script_report_".$id.$tab.".php";        
        ssh2_scp_send($con,$ourFileName ,$serv , 0777);
//$stream = ssh2_exec($con, "cd $path; php script_report_$id$tab.php");
       stream_set_blocking($stream, true);
       $lang1=stream_get_contents($stream);
       exec("rm -f $ourFileName");
    }
 } 
 echo $path."/script_report_".$id.$tab.".php";
?>
