<script src="include/jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="modules/MapGenerator/functions.js"></script>
<form method="post" action="">
  </br><br><br><br>
 Nome cliente  <select class="small" style='width:30%'  id="mod1" name="mod1" onchange="sellist1();" >
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
$dbname='crm_evolutivoservice';
$q = "select * from vtiger_accountinstallation ";
$res=$adb->query("select * from vtiger_accountinstallation ");
echo $adb->query_result($res,1,"acinstallationname");
global $log;
 for($i=0;$i<$adb->num_rows($res);$i++){
    $name=$adb->query_result($res,$i,"acinstallationname");
    $id=$adb->query_result($res,$i,"accountinstallationid");
    $path=explode("/",$adb->query_result($res,$i,"vtigerpath"));
    $n=count($path);
    $a.='<option value="'.$id.'">'.$name.'</option>';
}
echo $a;
?>

    </select>
  Report <select class="small" style='width:30%'  id="groupby1" name="groupby1" onchange="choose_fields3();" >
       </select>
  <br><br><br>
 Nome tabella  <input class="small" type="text" value="" name="nometab">
    <br><br><br>
       <table width="85%"  class="small" cellspacing="1" border="0" cellpadding="0" id='fieldTab'>
        <tr><td width="55%" class="lvtCol"><b><input type=checkbox name=allids1 id=allids1 checked="false" onchange='checkvalues()'>Lista dei Campi</b></td><td  width="20%" class="lvtCol"  align="center"><b>Moduli</b></td>
           
                </tr>
    


   </table>
    
  <input type="submit" name="button1" value="Crea tabella" class="crmbutton edit small">
</form>

<?php
global $adb,$log,$mod_strings;
include_once("modules/Reports/Reports.php");
include("modules/Reports/ReportRun.php");
include_once("include/utils/CommonUtils.php");
$tab = $_REQUEST['nometab'];

require_once('include/logging.php');
require_once('config.inc.php');
require_once('include/database/PearDatabase.php');
$acc=$_POST['mod1'];
$mod=$_POST['groupby1'];
$nr = $_GET['nr'];
$val = $_POST['val'];

$colname = array();
$modname = array();
$fieldname = array();
$colname1 =array();
$modname1 = array();
$fieldname1 = array();
for($i=1;$i<$val;$i++)
{
    
if( isset($_POST['checkf'.$i]) && $_POST['checkf'.$i]==1){
$fieldname1[$i]=$_POST['field'.$i];
$colname1[$i]=$_POST['colname'.$i];
   $modname1[$i] = $_POST['modul'.$i];
       
       
   }
       $fieldname[$i] =$_POST['field'.$i];
$colname[$i]=$_POST['colname'.$i];
   $modname[$i] = $_POST['modul'.$i];
}
$colname2 = implode(",",$colname);
$modname2 = implode(",",$modname);
$fieldname2 = implode(",",$fieldname);
$colname21 =implode(",",$colname1);
$modname21 = implode(",",$modname1);
$fieldname21 = implode(",",$fieldname1);
if($acc !=''){
$ai=$adb->query("select * from vtiger_accountinstallation join vtiger_crmentity on crmid=accountinstallationid join vtiger_account on accountid=linktoacsd where accountinstallationid=$acc");
$port=$adb->query_result($ai,0,"port");
$ip=$adb->query_result($ai,0,"hostname");
$pass=$adb->query_result($ai,0,"password");
$us=$adb->query_result($ai,0,"username");
$path=$adb->query_result($ai,0,"vtigerpath");
$dbname=$adb->query_result($ai,0,"dbname");
$acno=$adb->query_result($ai,0,"acin_no");}

if(isset($_REQUEST['button1'])){
//$adb->pquery("drop table mv_$tab");
$reportid=$_REQUEST['groupby1'];
$query="SELECT * from $acno$dbname.vtiger_report where reportid = $reportid
";
$result = $adb->query($query);
$fl= $adb->query_result($result,0,'reportname');

$query1="SELECT * from $acno$dbname.vtiger_tab where name ='BiServer'
";
$result1 = $adb->query($query1);
$nrel=$adb->num_rows($result1);

$col1=Array();

//$focus1=new ReportRun($reportid);
//	$currencyfieldres = $adb->pquery("SELECT tabid, fieldlabel, uitype from vtiger_field WHERE uitype in (71,72,10)", array());
//		if($currencyfieldres) {
//			foreach($currencyfieldres as $currencyfieldrow) {
//				$modprefixedlabel = getTabModuleName($currencyfieldrow['tabid'])." ".$currencyfieldrow['fieldlabel'];
//				$modprefixedlabel = str_replace(' ','_',$modprefixedlabel);
//
//				if($currencyfieldrow['uitype']!=10){
//					if(!in_array($modprefixedlabel, $focus1->convert_currency) && !in_array($modprefixedlabel, $focus1->append_currency_symbol_to_value)) {
//						$focus1->convert_currency[] = $modprefixedlabel;
//					}
//				} else {
//
//					if(!in_array($modprefixedlabel, $focus1->ui10_fields)) {
//						$focus1->ui10_fields[] = $modprefixedlabel;
//					}
//				}
//			}
//		}
//
//$rep=explode("from",$focus1->sGetSQLforReport($reportid,$nu));
// $catq=$adb->query($focus1->sGetSQLforReport($reportid,$nu));
//$s = $focus1->sGetSQLforReport($reportid,$nu);
//$s1 = explode("from", $s);
//echo $s1[0].'</br>';
//$s2 = explode("AS",$s1[0]);
//for($i=0;$i<count($s2);$i++){
//echo $s2[$i+1].'</br>';
//}

//
if($nr == 0){
   $fl1 = str_replace(" ","",$fl);
   $fl2 = str_replace("-","",$fl1);
   $id = str_replace(" ","",$reportid);
$my_file = $root_directory.'script_report_'.$id."".$fl2."".$tab.'.php';
//$my_file = 'script_report_juli.php';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
$data = '<?php

global $adb;
$current_user->id=1 ;
 include_once("modules/Reports/Reports.php");
include("modules/Reports/ReportRun.php");
include_once("include/utils/CommonUtils.php");
require_once(\'include/database/PearDatabase.php\');  
require_once("include/utils/utils.php"); 
require_once(\'vtlib/Vtiger/Module.php\'); 

global $adb;';
fwrite($handle, $data);

$data  = '

$focus1=new ReportRun('.$reportid.');
	$currencyfieldres = $adb->pquery("SELECT tabid, fieldlabel, uitype from vtiger_field WHERE uitype in (71,72,10)", array());
		if($currencyfieldres) {
			foreach($currencyfieldres as $currencyfieldrow) {
				$modprefixedlabel = getTabModuleName($currencyfieldrow["tabid"])." ".$currencyfieldrow["fieldlabel"];
				$modprefixedlabel = str_replace(" ","_",$modprefixedlabel);

				if($currencyfieldrow["uitype"]!=10){
					if(!in_array($modprefixedlabel, $focus1->convert_currency) && !in_array($modprefixedlabel, $focus1->append_currency_symbol_to_value)) {
						$focus1->convert_currency[] = $modprefixedlabel;
					}
				} else {

					if(!in_array($modprefixedlabel, $focus1->ui10_fields)) {
						$focus1->ui10_fields[] = $modprefixedlabel;
					}
				}
			}
		}
  


$s = $focus1->sGetSQLforReport('.$reportid.',$nu);
    $fields = explode(",",$s);
if(strpos($fields[0],\'_Id\')!== false)
{
    $s=str_replace($fields[0].",", " ", $s);
    $s = "select ".$s;
    
}
$fieldname = "'.$fieldname2.'";
   $fields=$fieldname;
$nrfiel = explode(",",$fields);
                                            for($i=0;$i<count($nrfiel);$i++)
                                                  {
                                                       $colona = explode("_",$nrfiel[$i],2);
                                                    
                                                        $nm = getTranslatedString($colona[1],  $colona[0]);
                                                        $collabel[$i] = "$nm";
                                                       
                                                       
                                                  }
                                                   $collabel1=implode(",",$collabel); 


$adb->pquery("drop table mv_'.$id."".$fl2."".$tab.'");
$adb->pquery("create table mv_'.$id."".$fl2."".$tab.' ($s) ENGINE=InnoDB");
$adb->pquery("ALTER TABLE mv_'.$id."".$fl2."".$tab.'
              ADD COLUMN id INT NOT NULL AUTO_INCREMENT FIRST,
              ADD PRIMARY KEY (id)");
    $lq = "SELECT id from vtiger_scripts WHERE name = \'script_report_'.$id."".$fl2."".$tab.'.php\' ";
              $id = $adb->pquery($lq);
                                            $id1=$adb->query_result($id,0,"id");
      $q1 = "UPDATE `vtiger_scripts` SET `fieldlabel`= \'$collabel1\' WHERE `id` =$id1 ";
      $adb->pquery($q1); 

';


fwrite($handle, $data);



fclose($handle);}
else
{
    $fl1 = str_replace(" ","",$fl);
    $fl2 = str_replace("-","",$fl1);
    $id = str_replace(" ","",$reportid);
  $my_file = $root_directory.'script_report_'.$id."".$fl2."".$tab.'.php';
//$my_file = 'script_report_juli.php';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
$data = '<?php

global $adb;
$current_user->id=1 ;
 include_once("modules/Reports/Reports.php");
include("modules/Reports/ReportRun.php");
include_once("include/utils/CommonUtils.php");
require_once(\'include/database/PearDatabase.php\');  
require_once("include/utils/utils.php"); 
require_once(\'vtlib/Vtiger/Module.php\'); 
$val = '.$val.';
  $colname1 = "'.$colname21.'";
$modname1 = "'.$modname21.'";
$fieldname1 = "'.$fieldname21.'";
$colname = "'.$colname2.'";
$modname = "'.$modname2.'";
$fieldname = "'.$fieldname2.'";
global $adb;';
fwrite($handle, $data);

$data  = '

$focus1=new ReportRun('.$reportid.');
	$currencyfieldres = $adb->pquery("SELECT tabid, fieldlabel, uitype from vtiger_field WHERE uitype in (71,72,10)", array());
		if($currencyfieldres) {
			foreach($currencyfieldres as $currencyfieldrow) {
				$modprefixedlabel = getTabModuleName($currencyfieldrow["tabid"])." ".$currencyfieldrow["fieldlabel"];
				$modprefixedlabel = str_replace(" ","_",$modprefixedlabel);

				if($currencyfieldrow["uitype"]!=10){
					if(!in_array($modprefixedlabel, $focus1->convert_currency) && !in_array($modprefixedlabel, $focus1->append_currency_symbol_to_value)) {
						$focus1->convert_currency[] = $modprefixedlabel;
					}
				} else {

					if(!in_array($modprefixedlabel, $focus1->ui10_fields)) {
						$focus1->ui10_fields[] = $modprefixedlabel;
					}
				}
			}
		}
  


$s = $focus1->sGetSQLforReport('.$reportid.',$nu);
  $result = $adb->query("$s");
   $nr = $adb->num_rows($result);

$colonne=array();
$f=array();
 $nrmod = explode(",",$modname1);
$fields=$fieldname;
$nrfiel = explode(",",$fields);
                                            for($i=0;$i<count($nrfiel);$i++)
                                                  {
                                                       $colona = explode("_",$nrfiel[$i],2);
                                                    
                                                        $nm = getTranslatedString($colona[1],  $colona[0]);
                                                        $collabel[$i] = "$nm";
                                                       
                                                       
                                                  }
                                                   $collabel1=implode(",",$collabel);
                                            for($i=0;$i<count($nrfiel);$i++)
                                                  {
                                                       $colona = preg_replace(\'/[^A-Za-z0-9]/\',\'\',$nrfiel[$i]);
                                                    $colonne[$i]="$colona VARCHAR(250)";

                                                  }
                                                 $col = implode(",",$colonne);
                                                   $adb->pquery("drop table mv_'.$id."".$fl2."".$tab.'");
                                                   $q1 = "create table mv_'.$id."".$fl2."".$tab.' ($col) ENGINE=InnoDB";
                                                   $adb->pquery("ALTER TABLE mv_'.$id."".$fl2."".$tab.'
                                                                 ADD COLUMN id INT NOT NULL AUTO_INCREMENT FIRST,
                                                                 ADD PRIMARY KEY (id)");
                                                  
                                                  
$adb->pquery($q1);

 $fieldname = explode(",",$fieldname1);

  for($i=0;$i<$nr;$i++)
  {
$k =0;
       for($j=0;$j<count($nrfiel);$j++)
       {
          //if in arrey shikon nqs jane uitype 10
          
     
          if($nrfiel[$j] == $fieldname[$k] && $k<count($nrmod) )
          {
              $l = "SELECT tablename,fieldname,entityidfield from vtiger_entityname WHERE tabid = $nrmod[$k] ";
             
 $name = $adb->pquery($l);
                                            $name1=$adb->query_result($name,0,"fieldname");
                                             $tab1=$adb->query_result($name,0,"tablename");
                                              $ent1=$adb->query_result($name,0,"entityidfield");
                                             
             $valuefield=$adb->query_result($result,$i,$j);
             if($valuefield !=\'\'){
               $q ="SELECT $name1 from $tab1 WHERE $ent1 = \'$valuefield\' ";
            
              $uiname = $adb->pquery($q);
                                            $uiname1=str_replace("\'","\\\'",$adb->query_result($uiname,0,0));
                                           $colona = preg_replace(\'/[^A-Za-z0-9]/\',\'\',$nrfiel[$j]);
             $f[$j] = "$colona = \'$uiname1\'";}else{$colona = preg_replace(\'/[^A-Za-z0-9]/\',\'\',$nrfiel[$j]);$f[$j] = "$colona = \'\'";}
                                            $k++;
                                         
                                            
          }
          else
          {$vl =$adb->query_result($result,$i,$j);
          $colona = preg_replace(\'/[^A-Za-z0-9]/\',\'\',$nrfiel[$j]);
              $f[$j] = "$colona = \'$vl\'";}

      
       }
     $f1=implode(",",$f);
     
           $q =  "insert into mv_'.$id."".$fl2."".$tab.' set $f1";
           echo $q;
      $adb->pquery($q); 
      
      $lq = "SELECT id from vtiger_scripts WHERE name = \'script_report_'.$id."".$fl2."".$tab.'.php\' ";
              $id = $adb->pquery($lq);
                                            $id1=$adb->query_result($id,0,"id");
      $q1 = "UPDATE `vtiger_scripts` SET `fieldlabel`= \'$collabel1\' WHERE `id` =$id1 ";
      $adb->pquery($q1); 
      
      
      
  }

';


fwrite($handle, $data);



fclose($handle);  
    
    
    
    
    
}

if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22

if(!($con = ssh2_connect($ip, $port))){
   
    $msgc=$mod_strings["failc"];
} else {
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, $us, $pass)) {
        $msgc=$mod_strings["faila"];
    } else {

        $msgc=$mod_strings["succ"];
        $fl1 = str_replace(" ","",$fl);
        $fl2 = str_replace("-","",$fl1);
        $id = str_replace(" ","",$reportid);
        $loc =$root_directory."script_report_".$id."".$fl2."".$tab.".php";
        if($nrel==0)
        $serv = $path."/modules/TbCompanion/Reports/script_report_".$id."".$fl2."".$tab.".php";
        else
        $serv = $path."/modules/BiServer/Reports/script_report_".$id."".$fl2."".$tab.".php";
        
ssh2_scp_send($con,$loc ,$serv , 0777);
$stream = ssh2_exec($con, "cd ".$path." ; script_report_'.$tab.'.php");
//$stream = ssh2_exec($con, "cd ".$path." ; vim result.txt ");
stream_set_blocking($stream, true);
$lang1=stream_get_contents($stream);
}
}  
}
else echo '2012;2013;2014';
?>
