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
$selField1 = explode(",",$_POST['selField1']);//stringa con tutte i campi scelti in selField1
$selField2 = explode(",",$_POST['selField2']);//stringa con tutte i campi scelti in selField1
$nameView=$_POST['nameView'];//nome della vista
$campiSelezionati = explode(",",$_POST['campiSelezionati']);
$optionsCombo = $_POST['html'];
$optionValue = array();
$optgroup = array();
for($j=0; $j<count($campiSelezionati);$j++){
    $expdies = explode(":",$campiSelezionati[$j]);
    array_push($optionValue,$expdies[0].".".$expdies[1]);
}

$firstmodule = $_POST['firstModule'];
$secmodule = $_POST['secModule'];

$stringaFields = implode(",", $optionValue);
$selTab1 = explode(",",$_POST['selTab1']);
$selTab2 = explode(",",$_POST['selTab2']);
$whereCondition = $_POST['whereCondition'];

$qString =  showJoinArray($selField1, $selField2, $nameView,$stringaFields,$selTab1,$selTab2,$whereCondition);
echo $qString;
/*
 * Stampa a video nel <div> con id="results" la query per la creazione della vista materializzata
 */
function showJoinArray( $selField1, $selField2, $tableName, $stringaFields,$selTab1,$selTab2,$whereCondition){
    $queryString = "";
    $acc = 0;
    for($i=0;$i<count($selTab1);$i++){
    if($selTab1[$i] == "Potentials") $selTab1[$i] = "vtiger_potential";
    else if($selTab1[$i]== "Accounts") $selTab1[$i] = "vtiger_account";
    else if($selTab1[$i]== "Contacts") $selTab1[$i] = "vtiger_contactdetails";
    else $selTab1[$i] = "vtiger_".strtolower($selTab1[$i]);
     if($selTab2[$i] == "Potentials") $selTab2[$i] = "vtiger_potential";
    else if($selTab2[$i]== "Accounts") $selTab2[$i] = "vtiger_account";
    else if($selTab2[$i] == "Contacts") $selTab2[$i] = "vtiger_contactdetails";
    else $selTab2[$i] = "vtiger_".strtolower($selTab2[$i]);
        if($i==0){
                $queryString.='CREATE TABLE mvjoinscripts_'.$tableName.' AS SELECT '.$stringaFields.' FROM '.$selTab1[$i].' INNER JOIN '.$selTab2[$i].' ON '.$selTab1[$i].'.'.$selField1[$i].' = '.$selTab2[$i].'.'.$selField2[$i];
                if($selTab1[$i]== "vtiger_account" && $acc == 0){
                    $queryString.=' inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid';
                    $queryString.=' inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid';
                    $acc=1;
                }
                }
            else{
                $queryString.=' INNER JOIN '.$selTab2[$i].' ON '.$selTab1[$i].'.'.$selField1[$i].' = '.$selTab2[$i].'.'.$selField2[$i];
                 if($selTab2[$i]== "vtiger_account" && $acc == 0){
                    $queryString.= ' inner join  vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid';
                    $queryString.=' inner join  vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid';
                    $acc=1;
                }
            }
            
    }
    if($whereCondition != "")
    $queryString.= " where ".$whereCondition;
    return $queryString;
}


/*
 * Ricevendo il nome di una tabella, fornisce il un array contenente tutti
 * i nomi dei campi in essa contenuta.
 */
function getCampi($table){
        global $db;
        $fields = mysql_list_fields($db, $table);
        $numColumn= mysql_num_fields($fields);
        for ($i = 0; $i < $numColumn; $i++){
            $fieldList[$i]=mysql_field_name($fields,$i);
        }
        return $fieldList;
}

/*
 * Riceve in ingresso un array e un intero, e restituisce un sub array 
 */
function prelevaArray($array, $indice){
    for($i=0; $i<$indice;$i++){
        $subArray[$i]=$array[$i];
    }
    return $subArray;
}


/*
 * Riceve in ingresso un array, e concatena ogni elemento in un'unica stringa
 */
function concatenaAllField($allFields)
{
      for($i=0;$i<count($allFields);$i++){
         if($i==0){
             $stringa=$allFields[$i];
         }
         else{
             $stringa=$stringa.', '.$allFields[$i];
         }
       }
    return $stringa;
}
/* Prende in ingresso due liste di tabelle.
 * $tableList1 corrisponde alle tabelle inserite nel selField1
 * $tableList2 corrisponde alle tabelle inserite nel selField2
 * 
 * La funzione, prende il primo elemento della lista di $tableList1, perchè 
 * gli altri elemnti sono già presenti, e lo inserisce in $tableList2, 
 * in questo modo si ha un unico array.
 * Successivamente si scorre tutto l'array con un for, e con un'altro si scorre 
 * nuovamente il tutto.
 * In questo modo, per ogni tabella, si prende ogni campo, e si controlla se esistono 
 * tabelle con campi con lo stesso nome, e se si, si rinominano.
 * Successivamente, ogni campo modificato, e non modificato, viene inserito nell'array
 * $allFields, che poi è il valore di ritorno della funzione.
 * 
 */

function getAllFields($tableList1, $tableList2){
    $allFields = array();
    $num=0;
    $tableList2[count($tableList2)]=$tableList1[0];
      
    for($i=0;$i<count($tableList2);$i++){
        if(!(in_array($tableList2[$i],prelevaArray($tableList2,$i) ) || ((in_array($tableList2[$i],prelevaArray($tableList1,$i)))&& $tableList2[$i]!=$tableList1[0]))){
            $fields=getCampi($tableList2[$i]);
                for($j=0;$j<(count($tableList2));$j++){
                    if($tableList2[$i]!=$tableList2[$j]){
                        for($k=0;$k<count($fields);$k++){
                            $fieldsTabList2=getCampi($tableList2[$j]); 
                                 if(in_array($fields[$k], $fieldsTabList2)){
                                    $stringa=$tableList2[$i].'.'.$fields[$k].' AS '.$tableList2[$i].'_'.$fields[$k];
                                    for($u=0;$u<count($fieldsTabList2);$u++){
                                        if($fieldsTabList2[$u]==$fields[$k]){
                                            $fieldsList2[$u]=$tableList2[$j].'.'.$fieldsList2[$k].' AS '.$tableList2[$j].'_'.$fieldsList2[$k];
                                        }
                                    }
                                    $fields[$k]=$stringa;
                                 }
                        }
                     }
                }
                for($s=0;$s<count($fields);$s++){
                    $allFields[$num]=$fields[$s];
                    $num++;
                }
       }
    }
    return $allFields;
}

global $adb,$root_directory;
$accins = $_REQUEST['installationID'];
$accQuery = $adb->pquery("Select * from vtiger_accountinstallation
                          where accountinstallationid = ?",array($accins));
$dbname = $adb->query_result($accQuery,0,"dbname");
$acno = $adb->query_result($accQuery,0,"acin_no");
$port=$adb->query_result($accQuery,0,"port");
$ip=$adb->query_result($accQuery,0,"hostname");
$pass=$adb->query_result($accQuery,0,"password");
$us=$adb->query_result($accQuery,0,"username");
$path=$adb->query_result($accQuery,0,"vtigerpath");
$db = $acno.$dbname;
////generate php script

$ourFileName =$root_directory.$nameView.".php";
$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file"); 
//
$data = "<?php \r\n";
$data.="global \$adb,\$current_user;\r\n";
$data.="include_once('include/utils/CommonUtils.php');\r\n";
$data.="\$current_user->id=1;\r\n";
$data.="\$tableName='".$tableName."';\r\n";
$data.='$qString="'.$qString.'";';
$data.="\r\n";
$data.="\$adb->query(\"drop table IF EXISTS \$tableName\");\r\n";
$data.="\$adb->query(\$qString);\r\n";
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
        $resTabBi = $adb->query("Select * from $db.vtiger_tab where name = 'BiServer'");
        $nrRow1= $adb->num_rows($resTabBi);
        $resTabTb = $adb->query("Select * from $db.vtiger_tab where name = 'TbCompanion'");
        $nrRow2 = $adb->num_rows($resTabTb);
        if($nrRow1 !=0){
        $name = 'BiServer';
        $serv = $path."/modules/BiServer/MVJoinScripts/".$nameView.".php";
        }
        if($nrRow2 !=0){
        $serv = $path."/modules/TbCompanion/MVJoinScripts/".$nameView.".php";
        $name =  'TbCompanion';
        }
        $sftp = ssh2_sftp($con);
        $fileExists = file_exists("ssh2.sftp://$sftp/$path/modules/$name/MVJoinScripts");
        if(!$fileExists) ssh2_exec($con, "mkdir $path/modules/$name/MVJoinScripts");
       
        ssh2_exec($con,"chmod 777 -R $path/modules/$name/MVJoinScripts/");
        ssh2_scp_send($con,$ourFileName ,$serv , 0777);
        stream_set_blocking($stream, true);
        $lang1=stream_get_contents($stream);
    }
} 
exec("rm -f $ourFileName");
?>