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

$ourFileName =$root_directory."script_report_".$id.$tab.".php";
$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file"); 

$data = "<?php \r\n";
$data.="global \$adb;\r\n";
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
$data.="\$BUcolumns = \$adb->query(\"select FinancialSettings_Business_Unit as business_unit from (\".\$focus1->sGetSQLforReport(\$reportid,\$nu).\"  and  vtiger_financialsettings.business_unit <> '' and vtiger_financialsettings.business_unit  NOT LIKE '%,%' group by vtiger_financialsettings.business_unit) x\"); \r\n";
$data.="\$col1=Array();\r\n";
$data.="\$colonne=Array();\r\n";
$data.="\$col1[0]='Levels';\r\n";
$data.="\$colonne[0]='Levels VARCHAR(250)';\r\n";
$data.="\$col1[1]='TypeOfPayment';\r\n";
$data.="\$colonne[1]='TypeOfPayment VARCHAR(250)';\r\n";
$data.="\$nr_BU = \$adb->num_rows(\$BUcolumns);\r\n";
$data.="\$BUnit = array();\r\n";
//$BUcolumns = $adb->query("Select distinct business_unit from $db.vtiger_financialsettings where business_unit<>'' and business_unit  NOT LIKE '%,%'");

$data.="for(\$j=0; \$j < \$nr_BU;\$j++){\r\n";
$data.="\$BUnit[\$j] = \$adb->query_result(\$BUcolumns,\$j,\"business_unit\");\r\n";
$data.="\$col1[2 + \$j]=  \$BUnit[\$j];\r\n";
$data.="\$bu =  str_replace(' ', '', \$BUnit[\$j]);\r\n";
$data.="\$bu1 = str_replace('-', '', \$bu);\r\n";
$data.="\$colonne[2+ \$j]=\"\$bu1 VARCHAR(250)\";\r\n";
$data.="}\r\n";

$data.="\$col1[2+\$nr_BU]='Total'; \r\n";
$data.="\$colonne[2+\$nr_BU]='Total VARCHAR(250)'; \r\n";
$data.="\$tot = array();\r\n";
$data.="\$col=implode(\",\",\$colonne);\r\n";
$data.="\$adb->query(\"drop table IF EXISTS mv_\$reportid\$tab\");\r\n";
$data.="\$adb->query(\"create table mv_\$reportid\$tab (\$col)\"); \r\n";
$data.="\$query = \$adb->query(\"Select distinct level from vtiger_financialsettings join vtiger_crmentity on crmid = financialsettingsid  where deleted = 0 order by level\");\r\n";
$data.="\$nr_levels = \$adb->num_rows(\$query);\r\n";
//For each level 
$data.="\$levelRDiffC = array_fill(0, 2+\$nr_BU, '0');\r\n";
$data.="for(\$i = 0; \$i < \$nr_levels; \$i++){\r\n";
$data.="\$level = \$adb->query_result(\$query,\$i,'level');\r\n";
    //Calculations for BU
$data.="\$levelRD = array_fill(0, 2+\$nr_BU, '0'); \$levelRD[0] = \"'\".\$level.\"'\"; \$levelRD[1] = \"'RD'\";\r\n";
$data.="\$levelRI = array_fill(0, 2+\$nr_BU, '0'); \$levelRI[0] = \"'\".\$level.\"'\"; \$levelRI[1] = \"'RI'\"; \r\n";
$data.="\$levelTR = array_fill(0, 2+\$nr_BU, '0'); \$levelTR[0] = \"'\".\$level.\"'\"; \$levelTR[1] = \"'TOT_R'\";\r\n";
$data.="\$levelCD = array_fill(0, 2+\$nr_BU, '0'); \$levelCD[0] = \"'\".\$level.\"'\"; \$levelCD[1] = \"'CD'\";\r\n";
$data.="\$levelCI = array_fill(0, 2+\$nr_BU, '0');\$levelCI[0] = \"'\".\$level.\"'\"; \$levelCI[1] =\"'CI'\";\r\n";
$data.="\$levelTC = array_fill(0, 2+\$nr_BU, '0'); \$levelTC[0] =\"'\".\$level.\"'\"; \$levelTC[1] = \"'TOT_C'\";\r\n";
$data.="\$levelRDiffC[0] = \"'\".\$level.\"'\"; \$levelRDiffC[1] = \"'R-C'\";\r\n";
$data.="\$levelPERCENTAGE = array_fill(0, 2+\$nr_BU, '0'); \$levelPERCENTAGE[0] = \"'\".\$level.\"'\"; \$levelPERCENTAGE[1] = \"'PERCENTAGE'\";\r\n";
    //get level direct revenue
$data.="\$levelD_R = \$adb->pquery(\"Select business_unit, sum(cf_559) as total from vtiger_payment \r\n";
$data.="join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_payment.paymentid \r\n";
$data.="join vtiger_paymentcf on vtiger_paymentcf.paymentid = vtiger_payment.paymentid \r\n";
$data.="join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = vtiger_payment.linktofinancialsettings \r\n";
$data.="where type_allocation = 'DIRECT' and type_finance = 'REVENUE' and level = ? and vtiger_crmentity.deleted=0 group by business_unit\",array(\$level)); \r\n";
    //Define DR for each BU
$data.="for(\$k = 0; \$k<\$adb->num_rows(\$levelD_R);\$k++){\r\n"; 
    $data.="\$BuName= \$adb->query_result(\$levelD_R,\$k,'business_unit'); \r\n";
    $data.="\$key = array_search(\$BuName, \$BUnit);\r\n";
    $data.="\$levelRD[2+\$key] = \$adb->query_result(\$levelD_R,\$k,'total');\r\n";
    $data.="}\r\n";
   //get level INdirect revenue
      $data.="\$levelI_R = \$adb->pquery(\"Select business_unit, sum(cf_559) as total from vtiger_payment \r\n";
                                $data.="join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_payment.paymentid \r\n";
                                $data.=" join vtiger_paymentcf on vtiger_paymentcf.paymentid = vtiger_payment.paymentid \r\n";
                                $data.=" join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = vtiger_payment.linktofinancialsettings \r\n";
                                $data.=" where type_allocation = 'INDIRECT' and type_finance = 'REVENUE' and level = ? and vtiger_crmentity.deleted=0  group by business_unit\",array(\$level)); \r\n";
    
     $data.="for(\$k = 0; \$k < \$adb->num_rows(\$levelI_R);\$k++){ \r\n";
     //Total DR revenue for all BU 
         $data.="\$totalDR = 0; \r\n";
         $data.="\$keys = array(); \r\n";
         $data.="\$BuNameConcat = \$adb->query_result(\$levelI_R,\$k,'business_unit'); \r\n";
         $data.="\$totalIDR = \$adb->query_result(\$levelI_R,\$k,'total'); \r\n";
         $data.="\$BuName = explode(\",\",\$BuNameConcat); \r\n";
         //Direct revenue for a BU
         $data.="for(\$j=0;\$j<count(\$BuName);\$j++){ \r\n";
            $data.="\$key = array_search(\$BuName[\$j], \$BUnit);\r\n";
            $data.="\$totalDR = \$totalDR + \$levelRD[\$key+2];\r\n";
            $data.="\$keys[]  = \$key +2 ; \r\n";      
         $data.="}\r\n";
        $data.=" for(\$j=0;\$j<count(\$keys);\$j++)\r\n";
         $data.="{ \r\n";
            $data.=" \$index = \$keys[\$j]; \r\n";
            $data.="\$levelRI[\$index] = \$levelRI[\$index] + round(\$levelRD[\$index]/\$totalDR,2) * \$totalIDR;\r\n";
         $data.="}\r\n";
         $data.="}\r\n";
   //get level direct cost
    $data.="\$levelD_C = \$adb->pquery(\"Select business_unit, sum(cf_559) as total from vtiger_payment \r\n";
                             $data.="join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_payment.paymentid \r\n";
                             $data.=" join vtiger_paymentcf on vtiger_paymentcf.paymentid = vtiger_payment.paymentid \r\n";
                             $data.=" join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = vtiger_payment.linktofinancialsettings \r\n";
                             $data.=" where type_allocation = 'DIRECT' and type_finance = 'COST' and level = ? and vtiger_crmentity.deleted=0 group by business_unit\",array(\$level)); \r\n";
    //Define DC for each BU
    $data.="for(\$k = 0; \$k<\$adb->num_rows(\$levelD_C);\$k++){ \r\n";
        $data.="\$BuName = \$adb->query_result(\$levelD_C,\$k,'business_unit'); \r\n";
        $data.="\$key = array_search(\$BuName, \$BUnit); \r\n";
        $data.="\$levelCD[2+\$key] = \$adb->query_result(\$levelD_C,\$k,'total'); \r\n";
    $data.="}     \r\n";
  //get level Indirect cost
       $data.="\$levelI_C= \$adb->pquery(\"Select business_unit, sum(cf_559) as total from vtiger_payment \r\n";
                                $data.="join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_payment.paymentid \r\n";
                                $data.="join vtiger_paymentcf on vtiger_paymentcf.paymentid = vtiger_payment.paymentid \r\n";
                                $data.=" join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = vtiger_payment.linktofinancialsettings \r\n";
                                $data.=" where type_allocation = 'INDIRECT' and type_finance = 'COST' and level = ? and vtiger_crmentity.deleted=0 group by business_unit\",array(\$level)); \r\n";
    
       $data.="for(\$k = 0; \$k < \$adb->num_rows(\$levelI_C);\$k++){ \r\n";
        $data.=" \$totalDC = 0;\r\n";
        $data.=" \$keys = array();\r\n";
        $data.=" \$BuNameConcat = \$adb->query_result(\$levelI_C,\$k,'business_unit');\r\n";
        $data.=" \$totalIDC = \$adb->query_result(\$levelI_C,\$k,'total');\r\n";
        $data.=" \$BuName = explode(\",\",\$BuNameConcat);\r\n";
        $data.=" for(\$j=0;\$j<count(\$BuName);\$j++){ \r\n";
        $data.="\$key = array_search(\$BuName[\$j], \$BUnit); \r\n";
            $data.=" \$totalDC = \$totalDC + \$levelCD[\$key+2]; \r\n";
            $data.=" \$keys[]  = \$key +2 ;  \r\n";    
        $data.=" } \r\n";
        $data.=" for(\$j=0;\$j<count(\$keys);\$j++) \r\n";
        $data.=" { \r\n";
            $data.="\$index = \$keys[\$j];\r\n";
            $data.=" \$levelCI[\$index] = \$levelCI[\$index] + round(\$levelCD[\$index]/\$totalIDC,2) * \$totalIDC;\r\n";
        $data.="} \r\n";
        $data.=" } \r\n";

//TOTAL revenue
       $data.="for(\$k=2; \$k<2+\$nr_BU; \$k++) \r\n";
        $data.=" {\r\n";
            $data.=" \$levelTR[\$k] = \$levelRD[\$k] + \$levelRI[\$k];\r\n";
            $data.="if(\$i == 0 )  \r\n";
            $data.="{\r\n";
            $data.="\$tot[\$k-2] = \$levelTR[\$k];\r\n";
            $data.="}\r\n";
            $data.=" \$levelTC[\$k] = \$levelCD[\$k] + \$levelCI[\$k];\r\n";
//            $data.=" \$tot =  \$levelRDiffC[\$k] + \$levelTR[\$k];\r\n";
            $data.="\$levelRDiffC[\$k] = \$levelRDiffC[\$k] + \$levelTR[\$k] - \$levelTC[\$k];\r\n";
            $data.="\$levelPERCENTAGE[\$k] = \"'\".(round(\$levelRDiffC[\$k]/\$tot[\$k-2],2) * 100).\"%'\";\r\n";
            $data.=" }\r\n";
           
        $data.="\$totalR_C = 0;\r\n";
        $data.="for(\$j=2;\$j< 2+\$nr_BU;\$j++){\r\n";
        $data.="\$levelRD[2+\$nr_BU] += \$levelRD[\$j]; \r\n";
        $data.="\$levelRI[2+\$nr_BU] += \$levelRI[\$j]; \r\n";
        $data.="\$levelCD[2+\$nr_BU] += \$levelCD[\$j]; \r\n";
        $data.="\$levelCI[2+\$nr_BU] += \$levelCI[\$j]; \r\n";
        $data.="\$levelTR[2+\$nr_BU] += \$levelTR[\$j]; \r\n";
        $data.="\$levelTC[2+\$nr_BU] += \$levelTC[\$j]; \r\n";
        $data.="\$totalR_C += \$levelRDiffC[\$j]; \r\n";
        $data.=" } \r\n";
        $data.="\$levelPERCENTAGE[2+\$nr_BU] = 0; \r\n";
        $data.="\$levelRDiffC[2+\$nr_BU] =  \$totalR_C; \r\n";
 $data.="\$row1 = implode(\",\",\$levelRD);\r\n";
 $data.="\$row2 = implode(\",\",\$levelRI);\r\n";
 $data.="\$row3 = implode(\",\",\$levelCD);\r\n";
 $data.="\$row4 = implode(\",\",\$levelCI);\r\n";
 $data.="\$row5 = implode(\",\",\$levelTR);\r\n";
 $data.="\$row6 = implode(\",\",\$levelTC);\r\n";
 $data.="\$row7 = implode(\",\",\$levelRDiffC);\r\n";
 $data.="\$row8 = implode(\",\",\$levelPERCENTAGE);\r\n";
// 
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row1)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row2)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row5)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row3)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row4)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row6)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row7)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab  values(\$row8)\");\r\n";
 $data.="}\r\n";
                    
fwrite($ourFileHandle,$data);
if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");

if(!($con = ssh2_connect($ip, $port))){
  echo "fail: unable to establish connection\n";

} else { echo 'Connected';
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, $us, $pass)) {
        $msgc = $mod_strings["faila"];
    } else {
        $msgc = $mod_strings["succ"];
        $serv = $path."/modules/TbCompanion/Reports/script_report_".$id.$tab.".php";
        $rootPath = $path."/script_report_".$id.$tab.".php";        
        ssh2_scp_send($con,$ourFileName ,$serv , 0777);
     //ssh2_scp_send($con,$ourFileName ,$rootPath , 0777);
    //$stream = ssh2_exec($con, "cd $path; php script_report_$id$tab.php");
       stream_set_blocking($stream, true);
       $lang1=stream_get_contents($stream);
    }
}  
?>