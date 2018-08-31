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
global $adb,$log;
$tab = $_REQUEST['nometab'];
$reportid = $_REQUEST['reportId'];
$accins = $_REQUEST['accins'];
$moduleName = $_REQUEST['accinsmodule'];

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
$data.="\$moduleName='".$moduleName."';\r\n";
$data.="include \"modules/\$moduleName/\$moduleName.php\";\r\n";

$data.="if(\$moduleName == \"Payment\") {\r\n";
    $data.="\$field = \"cf_559\";\r\n";
    $data.="\$focus  = new Payment();\r\n";
    $data.="\$linkfield = \"linktofinancialsettings\";\r\n";
    $data.="\$linktoadm = \"linktoadm\";\r\n";
$data.="}\r\n";
$data.="else if(\$moduleName == \"Adocdetail\") {\r\n";
    $data.="\$field = \"adoc_price\";\r\n";
    $data.="\$focus = new Adocdetail();\r\n";
    $data.="\$linkfield = \"linktofinsett\";\r\n";
    $data.="\$linktoadm = \"adoctomaster\";\r\n";
$data.="}\r\n";
$data.="\$table = \$focus->table_name;\r\n";
$data.="\$tablecf = \$focus->customFieldTable[0];\r\n";
$data.="\$modindex = \$focus->table_index;\r\n";
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
$data.="\$reportField = \$adb->pquery(\"Select columnname from vtiger_selectcolumn where queryid=?\",array(\$reportid));\r\n";
$data.="\$nr_Rep_Fields = \$adb->num_rows(\$reportField); \r\n";
$data.="\$repadoclabel = \" \";\r\n";
$data.="\$repbulabel = \" \"; \r\n";
$data.="for(\$j=0;\$j<\$nr_Rep_Fields;\$j++){ \r\n";
    $data.="if(\$repadoclabel != \" \" && \$repbulabel != \" \") break; \r\n";
    $data.="\$fieldlabel = \$adb->query_result(\$reportField,\$j,0); \r\n";
    $data.="\$pos1 = strpos(\$fieldlabel, \"AdocmasterNo\"); \r\n";
    $data.="\$pos2 = strpos(\$fieldlabel, \"Business_Unit\");\r\n";
    $data.="if(\$pos1 !== false)  \r\n";
    $data.="{\r\n";
        $data.="\$repadoccolumn = explode(\":\", \$fieldlabel);\r\n";
        $data.="\$repadoclabel = \$repadoccolumn[2];\r\n";
    $data.="} \r\n";
    $data.="if(\$pos2 != false){ \r\n";
        $data.="\$repadoccolumn = explode(\":\", \$fieldlabel); \r\n";
        $data.="\$repbulabel = \$repadoccolumn[2];  \r\n";
        $data.="\$buField = \$repadoccolumn[0].\".\".\$repadoccolumn[1];\r\n";
    $data.="} \r\n";
$data.="} \r\n";
$data.="\$BUcolumns = \$adb->query(\"select \".\$repbulabel.\" as business_unit,\".\$repadoclabel.\" as RelatedAdocMaster  from (\".\$focus1->sGetSQLforReport(\$reportid,\$nu).\"  and  \$buField  <> '' and \$buField  NOT LIKE '%,%') x\"); \r\n";
$data.="\$col1=Array();\r\n";
$data.="\$colonne=Array();\r\n";
//$data.="\$col1[0]='ID';\r\n";
$data.="\$colonne[0]='ID INT(9) UNSIGNED NOT NULL AUTO_INCREMENT,PRIMARY KEY (ID)';\r\n";
$data.="\$col1[0]='Levels';\r\n";
$data.="\$colonne[1]='Levels VARCHAR(250)';\r\n";
$data.="\$col1[1]='TypeOfPayment';\r\n";
$data.="\$colonne[2]='TypeOfPayment VARCHAR(250)';\r\n";
$data.="\$nr_BU = \$adb->num_rows(\$BUcolumns);\r\n";
$data.="\$BUnit = array();\r\n";
$data.="\$AdocMaster = array();\r\n";
$data.="\$AdocMasterIDArr = array();\r\n";
$data.="for(\$j=0; \$j < \$nr_BU;\$j++){\r\n";
$data.="\$BUnit[\$j] = \$adb->query_result(\$BUcolumns,\$j,\"business_unit\");\r\n";
$data.="\$AdocMaster[\$j] = \$adb->query_result(\$BUcolumns,\$j,1);\r\n";
$data.="}\r\n";

$data.="\$UniqueBUnit = array_values(array_unique(\$BUnit));\r\n";
$data.="\$UniqueAdocMaster = array_values(array_unique(\$AdocMaster));\r\n";
$data.="\$adocMasterNO = implode(\",\", \$UniqueAdocMaster);\r\n";
$data.="\$adoc = '\"'. implode('\",\"', explode(',', \$adocMasterNO)) .'\"';\r\n";
//Get adocMaster id 
$data.="\$adocQuery = \$adb->query(\"Select adocmasterid from vtiger_adocmaster where adocmasterno in (\$adoc)\");\r\n";
$data.="for(\$j=0; \$j<\$adb->num_rows(\$adocQuery);\$j++){ \r\n";
   $data.="\$AdocMasterIDArr[\$j] = \$adb->query_result(\$adocQuery,\$j,0);\r\n";
$data.="}\r\n";

$data.="\$AdocMasterID = implode(\",\", \$AdocMasterIDArr);\r\n";
$data.="for(\$j=0;\$j<count(\$UniqueBUnit);\$j++){\r\n";
$data.="\$bu =  str_replace(' ', '', \$UniqueBUnit[\$j]);\r\n";
$data.="\$bu1 = str_replace('-', '', \$bu);\r\n";
$data.="\$colonne[3 + \$j]=\"\$bu1 VARCHAR(250)\";\r\n";
$data.="\$col1[2 + \$j]=  \$bu1;\r\n";
$data.="}\r\n";
$data.="\$nr_BU = count(\$UniqueBUnit);\r\n";

$data.="\$col1[2+\$nr_BU]='Total'; \r\n";
$data.="\$colonne[3+\$nr_BU]='Total VARCHAR(250)'; \r\n";
$data.="\$tot = array();\r\n";
$data.="\$col=implode(\",\",\$colonne);\r\n";
$data.="\$colInput = implode(\",\", \$col1);\r\n";
$data.="\$adb->query(\"drop table IF EXISTS mv_\$reportid\$tab\");\r\n";
$data.="\$adb->query(\"create table mv_\$reportid\$tab (\$col) ENGINE=InnoDB\"); \r\n";
$data.="\$query = \$adb->query(\"Select distinct level from vtiger_financialsettings join vtiger_crmentity on crmid = financialsettingsid  where deleted = 0 order by level\");\r\n";
$data.="\$nr_levels = \$adb->num_rows(\$query);\r\n";
$data.="\$levelIndirectPercentage = array_fill(0, 2+\$nr_BU, '0');\r\n";
//For each level 
$data.="\$levelRDiffC = array_fill(0, 2+\$nr_BU, '0');\r\n";
$data.="for(\$i = 0; \$i < \$nr_levels; \$i++){\r\n";
$data.="\$level = \$adb->query_result(\$query,\$i,'level');\r\n";
//After the first level calculate the percentage of each level
//Save the percentage for each BU in an array, make the calculations only once
$data.="if(\$i == 1)\r\n";
    $data.="for(\$j=0; \$j<\$nr_BU; \$j++){\r\n";
    $data.="\$levelIndirectPercentage[\$j+3] = round(\$levelTR[\$j+3]/\$levelTR[\$nr_BU+3],2);\r\n";
$data.="}\r\n";

    //Calculations for BU
$data.="\$levelRD = array_fill(0, 2+\$nr_BU, '0'); \$levelRD[0] = \"'\".\$level.\"'\"; \$levelRD[1] = \"'RD'\";\r\n";
$data.="\$levelRI = array_fill(0, 2+\$nr_BU, '0'); \$levelRI[0] = \"'\".\$level.\"'\"; \$levelRI[1] = \"'RI'\"; \r\n";
$data.="\$levelTR = array_fill(0, 2+\$nr_BU, '0'); \$levelTR[0] = \"'\".\$level.\"'\"; \$levelTR[1] = \"'TOT_R'\";\r\n";
$data.="\$levelCD = array_fill(0, 2+\$nr_BU, '0'); \$levelCD[0] = \"'\".\$level.\"'\"; \$levelCD[1] = \"'CD'\";\r\n";
$data.="\$levelCI = array_fill(0, 2+\$nr_BU, '0'); \$levelCI[0] = \"'\".\$level.\"'\"; \$levelCI[1] =\"'CI'\";\r\n";
$data.="\$levelTC = array_fill(0, 2+\$nr_BU, '0'); \$levelTC[0] =\"'\".\$level.\"'\";  \$levelTC[1] = \"'TOT_C'\";\r\n";
$data.="\$levelRDiffC[0] = \"'\".\$level.\"'\"; \$levelRDiffC[1] = \"'R-C'\";\r\n";
$data.="\$levelPERCENTAGE = array_fill(0, 2+\$nr_BU, '0'); \$levelPERCENTAGE[0] = \"'\".\$level.\"'\"; \$levelPERCENTAGE[1] = \"'PERCENTAGE'\";\r\n";
    //get level direct revenue
$data.="\$levelD_R = \$adb->pquery(\"Select business_unit, sum(\$field) as total from \$table \r\n";
$data.="join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
$data.="join \$tablecf on \$tablecf.\$modindex = \$table.\$modindex \r\n";
$data.="join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
$data.="join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";  
$data.="where type_allocation = 'DIRECT' and type_finance = 'REVENUE' and level = ? and \$linktoadm  in (\$AdocMasterID) and vtiger_crmentity.deleted=0 group by business_unit\",array(\$level)); \r\n";
    //Define DR for each BU
$data.="for(\$k = 0; \$k<\$adb->num_rows(\$levelD_R);\$k++){\r\n"; 
    $data.="\$BuName= \$adb->query_result(\$levelD_R,\$k,'business_unit'); \r\n";
    $data.="\$key = array_search(\$BuName, \$UniqueBUnit);\r\n";
    $data.="\$levelRD[2+\$key] = \$adb->query_result(\$levelD_R,\$k,'total');\r\n";
    $data.="}\r\n";     
//get level INdirect revenue       
$data.="if(\$moduleName == \"Adocdetail\"){ \r\n";
$data.="\$levelI_R = \$adb->pquery(\"Select business_unit, \$field as total,productid, productcategory from \$table \r\n";
                            $data.="join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
                            $data.="join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
                            $data.="join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";  
                            $data.="join vtiger_products on vtiger_products.productid = vtiger_adocdetail.adoc_product \r\n";	
                            $data.="where type_allocation = 'INDIRECT' and type_finance = 'REVENUE' and level = ? \r\n";
                            $data.="and \$linktoadm  in (\$AdocMasterID) and vtiger_crmentity.deleted=0 \",array(\$level));   \r\n";
$data.="} \r\n";
$data.=" else { \r\n";
$data.="\$levelI_R = \$adb->pquery(\"Select business_unit, sum(\$field) as total from \$table \r\n";
                    $data.="join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
                    $data.="join \$tablecf on \$tablecf.\$modindex = \$table.\$modindex \r\n";
                    $data.="join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
                    $data.="join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";  
                    $data.="where type_allocation = 'INDIRECT' and type_finance = 'REVENUE' and level = ? \r\n";
                    $data.="and \$linktoadm  in (\$AdocMasterID) and vtiger_crmentity.deleted=0  group by business_unit\",array(\$level)); \r\n";
$data.="}\r\n";

$data.="for(\$k = 0; \$k < \$adb->num_rows(\$levelI_R);\$k++){ \r\n";
    $data.="if(\$moduleName == \"Adocdetail\"){ \r\n";
    $data.="\$prodId = \$adb->query_result(\$levelI_R,\$k,'productid');\r\n";
    $data.="\$productcategory = \$adb->query_result(\$levelI_R,\$k,'productcategory'); \r\n";
    $data.="\$totalAdocDetail = \$adb->query_result(\$levelI_R,\$k,'total');\r\n";
    $data.="if(\$productcategory == \"risorsa umana\"){\r\n";
     //get all prod details of this prod    
    $data.="\$prodDetailQuery = \$adb->pquery(\"Select workingpercentage, buproductdetail from vtiger_productdetail \r\n";
                                $data.="join vtiger_products in vtiger_products.productid =  vtiger_productdetail.linktoproduct \r\n";
                                $data.="where productid = ?\",array(\$prodId)); \r\n";
    $data.="\$nrProdDetail = \$adb->num_rows(\$prodDetailQuery); \r\n";
        //foreach prod detail check if BU not empty
        $data.="for(\$counter = 0; \$counter < \$nrProdDetail; \$counter++){ \r\n";
        $data.="\$key = array_search(\$adb->query_result(\$prodDetailQuery,\$counter,'buproductdetail'), \$UniqueBUnit);\r\n";
        $data.="\$workingpercentage = \$adb->query_result(\$prodDetailQuery,\$counter,'workingpercentage')/100;\r\n";
        $data.="\$indr =  \$totalAdocDetail * \$workingpercentage;\r\n";
        $data.="\$levelRI[\$key+2] = \$levelRI[\$key+2] + \$indr;\r\n";
     $data.="} \r\n";
$data.="} \r\n";
$data.="else{ \r\n";
$data.="\$totalDR = 0; \r\n";
$data.="\$keys = array(); \r\n";
$data.="\$BuNameConcat = \$adb->query_result(\$levelI_R,\$k,'business_unit'); \r\n";
$data.="\$totalIDR = \$adb->query_result(\$levelI_R,\$k,'total'); \r\n";
$data.="\$BuName = explode(\",\",\$BuNameConcat); \r\n";
$data.="for(\$j=0;\$j<count(\$BuName);\$j++){ \r\n";
$data.="\$key = array_search(\$BuName[\$j], \$UniqueBUnit);\r\n";
$data.="\$totalDR = \$totalDR + \$levelRD[\$key+2];\r\n";
$data.="\$keys[]  = \$key +2 ; \r\n";
$data.="}\r\n";
$data.="for(\$j=0;\$j<count(\$keys);\$j++)\r\n";
$data.="{ \r\n";
$data.="\$index = \$keys[\$j];\r\n"; 
$data.="\$levelRI[\$index] = \$levelRI[\$index] + \$levelIndirectPercentage[\$index] * \$totalIDR; \r\n"; 
$data.="}\r\n"; 
$data.="}\r\n"; 
$data.="}\r\n";
$data.="else{ \r\n";
$data.="\$totalDR = 0; \r\n";
$data.="\$keys = array(); \r\n";
$data.="\$BuNameConcat = \$adb->query_result(\$levelI_R,\$k,'business_unit'); \r\n";
$data.="\$totalIDR = \$adb->query_result(\$levelI_R,\$k,'total'); \r\n";
$data.="\$BuName = explode(\",\",\$BuNameConcat); \r\n";
$data.="for(\$j=0;\$j<count(\$BuName);\$j++){ \r\n";
$data.="\$key = array_search(\$BuName[\$j], \$UniqueBUnit);\r\n";
$data.="\$totalDR = \$totalDR + \$levelRD[\$key+2];\r\n";
$data.="\$keys[]  = \$key +2 ; \r\n";
$data.="}\r\n";
$data.="for(\$j=0;\$j<count(\$keys);\$j++)\r\n";
$data.="{ \r\n";
$data.="\$index = \$keys[\$j];\r\n"; 
$data.="\$levelRI[\$index] = \$levelRI[\$index] + \$levelIndirectPercentage[\$index] * \$totalIDR; \r\n"; 
$data.="}\r\n"; 
$data.="}\r\n"; 
$data.="}\r\n";

   //get level direct cost
    $data.="if(\$moduleName == \"Adocdetail\"){ \r\n";
    $data.="\$levelD_C = \$adb->pquery(\"Select  business_unit, \$field as total,productid,productcategory from \$table \r\n";
                         $data.=" join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
                         $data.=" join \$tablecf on \$tablecf.\$modindex = \$table.\$modindex \r\n";
                         $data.=" join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
                         $data.=" join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";
                         $data.=" join vtiger_products on vtiger_products.productid = vtiger_adocdetail.adoc_product	\r\n";
                         $data.=" where type_allocation = 'DIRECT' and type_finance = 'COST' and level = ? \r\n";
                         $data.=" and \$linktoadm  in (\$AdocMasterID) and vtiger_crmentity.deleted=0 \",array(\$level)); \r\n";
    $data.="} \r\n";
    $data.="else{ \r\n";
    $data.="\$levelD_C = \$adb->pquery(\"Select business_unit, sum(\$field) as total from \$table \r\n";
                             $data.="join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
                             $data.=" join \$tablecf on \$tablecf.\$modindex = \$table.\$modindex \r\n";
                             $data.=" join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
                             $data.="join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";  
                             $data.="where type_allocation = 'DIRECT' and type_finance = 'COST' and level = ? \r\n";
                             $data.="and \$linktoadm  in (\$AdocMasterID) and vtiger_crmentity.deleted=0 group by business_unit\",array(\$level)); \r\n";
    $data.="} \r\n";
    //Define DC for each BU
    $data.="for(\$k = 0; \$k<\$adb->num_rows(\$levelD_C);\$k++){ \r\n";
        $data.="if(\$moduleName == \"Adocdetail\"){ \r\n";
        $data.="\$prodId = \$adb->query_result(\$levelD_C,\$k,'productid');\r\n";
        $data.="\$productcategory = \$adb->query_result(\$levelD_C,\$k,'productcategory'); \r\n";
        $data.="\$totalAdocDetail = \$adb->query_result(\$levelD_C,\$k,'total');\r\n";
        $data.="if(\$productcategory == \"risorsa umana\"){\r\n";
     //get all prod details of this prod    
        $data.="\$prodDetailQuery = \$adb->pquery(\"Select workingpercentage, buproductdetail from vtiger_productdetail \r\n";
        $data.="join vtiger_products on vtiger_products.productid =  vtiger_productdetail.linktoproduct \r\n";
        $data.="join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_productdetail.productdetailid \r\n";
        $data.="where  deleted = 0 and productid = ?\",array(\$prodId)); \r\n";
        $data.="\$nrProdDetail = \$adb->num_rows(\$prodDetailQuery); \r\n";
        //foreach prod detail check if BU not empty
       $data.="for(\$counter = 0; \$counter < \$nrProdDetail; \$counter++){ \r\n";
       $data.="\$key = array_search(\$adb->query_result(\$prodDetailQuery,\$counter,'buproductdetail'), \$UniqueBUnit); \r\n";
       $data.="\$workingpercentage = \$adb->query_result(\$prodDetailQuery,\$counter,'workingpercentage')/100; \r\n";
       $data.="\$drc =  \$totalAdocDetail * \$workingpercentage; \r\n";
       $data.="\$levelCD[\$key+2] = \$levelCD[\$key+2] + \$drc; \r\n";
       $data.=" } \r\n";
       $data.="} \r\n";
       $data.="else{ \r\n";
       $data.="\$BuName = \$adb->query_result(\$levelD_C,\$k,'business_unit'); \r\n";
       $data.="\$key = array_search(\$BuName, \$UniqueBUnit); \r\n";
       $data.="\$levelCD[2+\$key] = \$levelCD[2+\$key] + \$adb->query_result(\$levelD_C,\$k,'total'); \r\n";
       $data.="} \r\n";
       $data.="} \r\n"; 
       $data.="else{ \r\n";
       $data.="\$BuName = \$adb->query_result(\$levelD_C,\$k,'business_unit'); \r\n";
       $data.="\$key = array_search(\$BuName, \$UniqueBUnit); \r\n";
       $data.="\$levelCD[2+\$key] = \$levelCD[2+\$key] + \$adb->query_result(\$levelD_C,\$k,'total'); \r\n";
       $data.="} \r\n";
       $data.="} \r\n";

 $data.="if(\$moduleName == \"Adocdetail\"){ \r\n";
 $data.="\$levelI_C= \$adb->pquery(\"Select  business_unit, \$field as total,productid,productcategory  from \$table \r\n";
                     $data.="join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
                     $data.="join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
                     $data.="join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";  
                     $data.="join vtiger_products on vtiger_products.productid = vtiger_adocdetail.adoc_product	\r\n";
                     $data.="where type_allocation = 'INDIRECT' and \$linktoadm  in (\$AdocMasterID) and type_finance = 'COST' and level = ? and vtiger_crmentity.deleted=0 \",array(\$level)); \r\n";
    
 $data.="} \r\n";
 $data.="else{ \r\n";
 $data.="\$levelI_C= \$adb->pquery(\"Select business_unit, sum(\$field) as total from \$table \r\n";
                     $data.="join vtiger_crmentity on vtiger_crmentity.crmid = \$table.\$modindex \r\n";
                     $data.="join \$tablecf on \$tablecf.\$modindex = \$table.\$modindex \r\n";
                     $data.="join vtiger_financialsettings on vtiger_financialsettings.financialsettingsid = \$table.\$linkfield \r\n";
                     $data.="join vtiger_adocmaster on vtiger_adocmaster.adocmasterid = \$table.\$linktoadm  \r\n";  
                     $data.="where type_allocation = 'INDIRECT' and \$linktoadm  in (\$AdocMasterID) and type_finance = 'COST' and level = ? and vtiger_crmentity.deleted=0 group by business_unit\",array($level)); \r\n";
 $data.="} \r\n";
 $data.="for(\$k = 0; \$k < \$adb->num_rows(\$levelI_C);\$k++){ \r\n";
    $data.="if(\$moduleName == \"Adocdetail\"){ \r\n";
    $data.="\$prodId = \$adb->query_result(\$levelI_C,\$k,'productid');\r\n";
    $data.="\$productcategory = \$adb->query_result(\$levelI_C,\$k,'productcategory'); \r\n";
    $data.="\$totalAdocDetail = \$adb->query_result(\$levelI_C,\$k,'total');\r\n";
    $data.="if(\$productcategory == \"risorsa umana\"){\r\n";
     //get all prod details of this prod    
     $data.="\$prodDetailQuery = \$adb->pquery(\"Select workingpercentage, buproductdetail from vtiger_productdetail \r\n";
                                $data.="join vtiger_products on vtiger_products.productid =  vtiger_productdetail.linktoproduct \r\n";
                                $data.="join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_productdetail.productdetailid \r\n";
                                $data.="where  deleted = 0 and productid = ?\",array(\$prodId)); \r\n";
    $data.="\$nrProdDetail = \$adb->num_rows(\$prodDetailQuery); \r\n";
        //foreach prod detail check if BU not empty
       $data.="for(\$counter = 0; \$counter < \$nrProdDetail; \$counter++){ \r\n";
       $data.="\$key = array_search(\$adb->query_result(\$prodDetailQuery,\$counter,'buproductdetail'), \$UniqueBUnit); \r\n";
       $data.="\$workingpercentage = \$adb->query_result(\$prodDetailQuery,\$counter,'workingpercentage')/100; \r\n";
       $data.="\$indr =  \$totalAdocDetail * \$workingpercentage; \r\n";
       $data.="\$levelCI[\$key+2] = \$levelCI[\$key+2] + \$indr; \r\n";
    $data.=" } \r\n";
 $data.="} \r\n";
 $data.="else{ \r\n";
 $data.="\$totalDC = 0; \r\n";
 $data.="\$keys = array();\r\n";
 $data.="\$BuNameConcat = \$adb->query_result(\$levelI_C,\$k,'business_unit');\r\n";
 $data.="\$totalIDC = \$adb->query_result(\$levelI_C,\$k,'total'); \r\n";
 $data.="\$BuName = explode(\",\",\$BuNameConcat); \r\n";
 $data.="for(\$j=0;\$j<count(\$BuName);\$j++){  \r\n";
 $data.="\$key = array_search(\$BuName[\$j], \$UniqueBUnit);  \r\n";
 $data.="\$totalDC = \$totalDC + \$levelCD[\$key+2];  \r\n";
 $data.="\$keys[]  = \$key +2 ;  \r\n";
 $data.="} \r\n";
 $data.="for(\$j=0;\$j<count(\$keys);\$j++) \r\n";
 $data.="{ \r\n";
 $data.="\$index = \$keys[\$j]; \r\n";
 //$data.="\$levelCI[\$index] = \$levelCI[\$index] + round(\$levelCD[\$index]/\$totalIDC,2) * \$totalIDC;\r\n";
 $data.="\$levelCI[\$index] = \$levelCI[\$index] + \$levelIndirectPercentage[\$index] * \$totalIDC;\r\n";
$data.="} \r\n";
$data.="} \r\n";
$data.="} \r\n";
 $data.="else{ \r\n";
 $data.="\$totalDC = 0; \r\n";
 $data.="\$keys = array();\r\n";
 $data.="\$BuNameConcat = \$adb->query_result(\$levelI_C,\$k,'business_unit');\r\n";
 $data.="\$totalIDC = \$adb->query_result(\$levelI_C,\$k,'total'); \r\n";
 $data.="\$BuName = explode(\",\",\$BuNameConcat); \r\n";
 $data.="for(\$j=0;\$j<count(\$BuName);\$j++){  \r\n";
 $data.="\$key = array_search(\$BuName[\$j], \$UniqueBUnit);  \r\n";
 $data.="\$totalDC = \$totalDC + \$levelCD[\$key+2];  \r\n";
 $data.="\$keys[]  = \$key +2 ;  \r\n";
 $data.="} \r\n";
 $data.="for(\$j=0;\$j<count(\$keys);\$j++) \r\n";
 $data.="{ \r\n";
 $data.="\$index = \$keys[\$j]; \r\n";
 //$data.="\$levelCI[\$index] = \$levelCI[\$index] + round(\$levelCD[\$index]/\$totalIDC,2) * \$totalIDC;\r\n";
 $data.="\$levelCI[\$index] = \$levelCI[\$index] + \$levelIndirectPercentage[\$index] * \$totalIDC;\r\n";
$data.="} \r\n";
$data.="} \r\n";
$data.="} \r\n";
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
        $data.="if(\$i == 0 )  \r\n";
        $data.="{\r\n";
        $data.="\$totalOfRevenues = \$levelTR[2+\$nr_BU];\r\n";
        $data.="}\r\n";
     //   $data.="\$levelPERCENTAGE[2+\$nr_BU] = 0; \r\n";
        $data.="\$levelRDiffC[2+\$nr_BU] =  \$totalR_C; \r\n";
        $data.="\$levelPERCENTAGE[2+\$nr_BU] = \"'\".(round(\$levelRDiffC[2+\$nr_BU]/\$totalOfRevenues,2) * 100).\"%'\";\r\n";

 $data.="\$row1 = implode(\",\",\$levelRD);\r\n";
 $data.="\$row2 = implode(\",\",\$levelRI);\r\n";
 $data.="\$row3 = implode(\",\",\$levelCD);\r\n";
 $data.="\$row4 = implode(\",\",\$levelCI);\r\n";
 $data.="\$row5 = implode(\",\",\$levelTR);\r\n";
 $data.="\$row6 = implode(\",\",\$levelTC);\r\n";
 $data.="\$row7 = implode(\",\",\$levelRDiffC);\r\n";
 $data.="\$row8 = implode(\",\",\$levelPERCENTAGE);\r\n";
// 
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row1)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row2)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row5)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row3)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row4)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row6)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row7)\");\r\n";
 $data.="\$insertInfo = \$adb->query(\"Insert into mv_\$reportid\$tab(\$colInput)  values(\$row8)\");\r\n";
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
        $serv = $path."/modules/BiServer/Reports/script_report_".$id.$tab.".php";
        $rootPath = $path."/script_report_".$id.$tab.".php";        
        ssh2_scp_send($con,$ourFileName ,$serv , 0777);
     //ssh2_scp_send($con,$ourFileName ,$rootPath , 0777);
    //$stream = ssh2_exec($con, "cd $path; php script_report_$id$tab.php");
       stream_set_blocking($stream, true);
       $lang1=stream_get_contents($stream);
    }
}  
exec("rm -f $ourFileName");
?>
