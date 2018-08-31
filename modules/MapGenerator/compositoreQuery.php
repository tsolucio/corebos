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
$selField1 = $_POST['selField1'];//stringa con tutte i campi scelti in selField1
$selField2 = $_POST['selField2'];//stringa con tutte i campi scelti in selField1
$nameView = $_POST['nameView'];//nome della vista
$campiSelezionati = $_POST['campiSelezionati'];
$usergroup=$_POST['userorgroup'];
$cftables=$_POST['cftables'];
$OptVAl = $_REQUEST['JoinOV'];

$sendarray = array();
$valuecombo = $_REQUEST['Valueli'];
for ($j = 0; $j < count($valuecombo); $j++) {
    $expdies = explode("!", $valuecombo[$j]);
    $sendarray[] = array(
        'Values' => $expdies[0],
        'Texti' => $expdies[1],
    );
}
//print_r($sendarray);
$optionValue = array();
$optgroup = array();
for ($j = 0; $j < count($campiSelezionati); $j++) {
    $expdies = explode(":", $campiSelezionati[$j]);
    array_push($optionValue, $expdies[0] . "." . $expdies[1]);
}

$firstmodule = $_POST['fmodule'];
$secmodule = $_POST['smodule'];
$Moduls = array();
array_push($Moduls, $firstmodule);
array_push($Moduls, $secmodule);


//echo inerJoionwithCrmentity($Moduls);
//$selField1 = explode(',',$stringaselField1);
//$selField2 = explode(',',$stringaselField2);
//$stringaFields = implode(",", selectValueswithoutjoincrmentity($OptVAl, $Moduls)); substr($stringaFields2, 0, -2)
//$stringaFields2 = implode(",", selectValueswithjoincrmentity($OptVAl, $Moduls));
$selTab1 = $_POST['selTab1'];
$selTab2 = $_POST['selTab2'];


$selTab3=array_unique(array_merge($selTab1,$selTab2));
$primfield=$selTab1[0];
$query = $adb->query("select entityidfield,tablename from vtiger_entityname where modulename='$primfield'");
$entityidfield = $adb->query_result($query, 0, "entityidfield");
$tablename = $adb->query_result($query, 0, "tablename");
$entityidfields = $tablename . "." . $entityidfield;


$queryid=$_POST['queryid'];
$mapid=$_POST['MapID'];


if($mapid!='')
{
  $sql1 = $adb->query("select sequence from mvqueryhistory where id='$queryid' AND active='1'");
  $seq=$adb->query_result($sql1, 0, "sequence");
  $sql = $adb->query("select * from mvqueryhistory where id='$queryid' order by sequence ASC");
  //$num=$adb->num_rows($sql);
  $nr=count($_POST['selTab1']);
  $selField1=$selField1[$nr-1];
  $selField2=$selField2[$nr-1];
  //$oldjoins=($num+1)-$nr;
  for ($k = 0; $k < $seq; $k++) {
        $FirstModule[] = $adb->query_result($sql, $k, "firstmodule");
  	    $SecondModule[] = $adb->query_result($sql, $k, "secondmodule");
  	    $FirstModuleField[] = $adb->query_result($sql, $k, "firstmodulefield");
  	    $SecondModuleField[] = $adb->query_result($sql, $k, "secondmodulefield");
        $selFieldload1[]=$adb->query_result($sql, $k, "firstmodulelabel");
        $selFieldload2[]=$adb->query_result($sql, $k, "secondmodulelabel");;

  }
  $labels = $adb->query_result($sql, 0, "labels");
  $Labels=explode(',',$labels);
  $selTab1=$selTab1[$nr-1];
  $selTab2=$selTab2[$nr-1];

  array_push($FirstModule,$selTab1);
  array_push($SecondModule,$selTab2);
  array_push($FirstModuleField,$selField1);
  array_push($SecondModuleField,$selField2);
  $selTab3=array_unique(array_merge($FirstModule,$SecondModule));
  $primfield=$FirstModule[0];
  $query = $adb->query("select entityidfield,tablename from vtiger_entityname where modulename='$primfield'");
  $entityidfield = $adb->query_result($query, 0, "entityidfield");
  $tablename = $adb->query_result($query, 0, "tablename");
  $entityidfields = $tablename . "." . $entityidfield;
  $generate=showJoinArray($FirstModuleField, $SecondModuleField, $nameView,$Labels, $FirstModule, $SecondModule, $entityidfields, $selTab3,$usergroup,$cftables,$selFieldload1,$selFieldload2);
  $generatetQuery = showJoinArray($FirstModuleField, $SecondModuleField, $nameView,$OptVAl, $FirstModule, $SecondModule, $entityidfields, $selTab3,$usergroup,$cftables,$selFieldload1,$selFieldload2);
} else {
  $generatetQuery = showJoinArray($selField1, $selField2, $nameView,$OptVAl, $selTab1, $selTab2, $entityidfields, $selTab3,$usergroup,$cftables);
}



/*
 * Stampa a video nel <div> con id="results" la query per la creazione della vista materializzata
 */
function showJoinArray($selField1, $selField2, $nameView, $stringaFields, $selTab1, $selTab2, $primarySelectID, $Moduls,$usergroup,$cftables,$selFieldload1,$selFieldload2)
{
    $acc = 0;
    $cont= 0;
    $lead= 0;
    $index=0;
    $strQuery = '';
    global $log,$adb;
    $prim=explode(".",$primarySelectID);
    $primarySelectID=$prim[1];
    for ($i = 0; $i < count($selTab1); $i++) {
        $selTab1mod[$i]=strtolower($selTab1[$i]);
        $selTab2mod[$i]=strtolower($selTab2[$i]);
        if(substr($selTab1mod[$i],-1)=='s')
          $selTab1mod[$i]=substr($selTab1mod[$i],0,-1);
        if(substr($selTab2mod[$i],-1)=='s')
          $selTab2mod[$i]=substr($selTab2mod[$i],0,-1);
        if($selTab1[$i]=='Territory') {
          $selTab1[$i]=='vtiger_territory';
          $cftable1[$i]=='vtiger_territorycf';
      } else if ($selTab1[$i]=='HelpDesk') {
          $selTab1[$i]='vtiger_troubletickets';
          $cftable1[$i]='vtiger_ticketcf';
      } else {
          $q1 = $adb->query("select tablename from vtiger_entityname where modulename='$selTab1[$i]'");
          $selTab1[$i]=$adb->query_result($q1,0,'tablename');
          $cf1=$adb->query("show tables LIKE '$selTab1[$i]%cf'");
          $cftable1[$i]=$adb->query_result($cf1,0,0);
          if($cftable1[$i]==''){
            $cf11=$adb->query("show tables LIKE '%$selTab1mod[$i]%cf'");
            $cftable1[$i]=$adb->query_result($cf11,0,0);
          }
      }
      if($selTab2[$i]=='Territory') {
        $selTab2[$i]=='vtiger_territory';
        $cftable2[$i]=='vtiger_territorycf';
    } else if ($selTab1[$i]=='HelpDesk') {
        $selTab2[$i]='vtiger_troubletickets';
        $cftable2[$i]='vtiger_ticketcf';
    } else {
        $q2 = $adb->query("select tablename from vtiger_entityname where modulename='$selTab2[$i]'");
        $selTab2[$i]=$adb->query_result($q2,0,'tablename');
        $cf2=$adb->query("show tables LIKE '$selTab2[$i]%cf'");
        $cftable2[$i]=$adb->query_result($cf2,0,0);
        if($cftable2[$i]==''){
          $cf22=$adb->query("show tables LIKE '%$selTab2mod[$i]%cf'");
          $cftable2[$i]=$adb->query_result($cf22,0,0);
        }
    }


        if($index==0) $index=1;
        $firsttblid=getentityidfield(strtolower($selTab1[$i]));
        $secondtblid=getentityidfield(strtolower($selTab2[$i]));
        if ($i == 0) {
            /* <b> CREATE TABLE </b>'.$nameView.'<b>  */
            $arr["$selTab1[$i]"]=strtolower($selTab1[$i]).'_0';
            $arr["$selTab2[$i]"]=strtolower($selTab2[$i]).'_'.$index;
            $maintab=strtolower($selTab1[$i]);
            $selTabmain=$selTab1[$i];
            $stringaFields2 = implode(",", selectValueswithjoincrmentity($stringaFields, $Moduls,$index,$arr,$maintab));
            $strf=substr($stringaFields2, 0, -2);
            if($usergroup=='user')
            {
            $qjoin=' JOIN vtiger_users User_'.strtolower($selTab1[$i]).'_0 on User_'.strtolower($selTab1[$i]).'_0.id=CRM_'.strtolower($selTab1[$i]).'_0.smownerid ';
            $q2join=' JOIN vtiger_users User_'.strtolower($selTab2[$i]).'_'.$index.' on User_'.strtolower($selTab2[$i]).'_'.$index.'.id=CRM_'.strtolower($selTab2[$i]).'_'.$index.'.smownerid ';
            $strf=str_replace(Array('CRM_'.strtolower($selTab1[$i]).'_0.smownerid','CRM_'.strtolower($selTab2[$i]).'_'.$index.'.smownerid'),Array('User_'.strtolower($selTab1[$i]).'_0.user_name','User_'.strtolower($selTab2[$i]).'_'.$index.'.user_name'),substr($stringaFields2, 0, -2));
            }
            else if($usergroup=='group')
            {
            $qjoin=' join vtiger_groups Group_'.strtolower($selTab1[$i]).'_0 on Group_'.strtolower($selTab1[$i]).'_0.groupid=CRM_'.strtolower($selTab1[$i]).'_0.smownerid ';
            $q2join=' join vtiger_groups Group_'.strtolower($selTab2[$i]).'_'.$index.' on Group_'.strtolower($selTab2[$i]).'_'.$index.'.groupid=CRM_'.strtolower($selTab2[$i]).'_'.$index.'.smownerid ';
            $strf=str_replace(Array('CRM_'.strtolower($selTab1[$i]).'_0.smownerid','CRM_'.strtolower($selTab2[$i]).'_'.$index.'.smownerid'),Array('Group_'.strtolower($selTab1[$i]).'_0.groupname','Group_'.strtolower($selTab2[$i]).'_'.$index.'.groupname'),substr($stringaFields2, 0, -2));
            }
            if($cftables=='cf'){
              $qjoincf=' JOIN '.$cftable1[$i].' as '.$cftable1[$i].'_0 on '.$cftable1[$i].'_0.'.$firsttblid.'='.strtolower($selTab1[$i]).'_0.'.$firsttblid;
              $q2joincf=' JOIN '.$cftable2[$i].' as '.$cftable2[$i].'_'.$index.' on '.$cftable2[$i].'_'.$index.'.'.$secondtblid.'='.strtolower($selTab2[$i]).'_'.$index.'.'.$secondtblid;
              $strf=str_replace(Array($cftable1[$i].'_'.$index,$cftable2[$i].'_'.$index),Array($cftable1[$i].'_0',$cftable2[$i].'_'.$index),substr($stringaFields2, 0, -2));
            }

            if(coreBOS_Session::get('selectedfields')=='')
            {  coreBOS_Session::set('selectedfields',strtolower($selTab1[$i]).'_0.'. $primarySelectID . "," . $strf);
               $selfields=coreBOS_Session::get('selectedfields');
            }
            else
            $selfields=coreBOS_Session::get('selectedfields');
            $selectquery='<b> SELECT </b>'.$selfields;
            if($selField1[$i]=='')
            $selfld1=$selFieldload1[$i];
            else
            $selfld1=$selField1[$i];
            if($selField2[$i]=='')
            $selfld2=$selFieldload2[$i];
            else
            $selfld2=$selField2[$i];
            $strQuery .= '<b> FROM </b>' . strtolower($selTab1[$i]) .' as '.strtolower($selTab1[$i]).'_0 join vtiger_crmentity CRM_'.strtolower($selTab1[$i]).'_0 on CRM_'.strtolower($selTab1[$i]).'_0.crmid='.strtolower($selTab1[$i]).'_0.'.$firsttblid.' <b>'.$qjoin.''.$qjoincf.' </b><b>INNER JOIN </b> '.$selTab2[$i].' <b> as </b> ' . strtolower($selTab2[$i]).'_'.$index. '<b> ON </b>' . strtolower($selTab1[$i]).'_0.'. $selfld1 . '<b> = </b>' . strtolower($selTab2[$i]).'_'.$index. '.'. $selfld2.' join vtiger_crmentity CRM_'.strtolower($selTab2[$i]).'_'.$index.' on CRM_'.strtolower($selTab2[$i]).'_'.$index.'.crmid='.strtolower($selTab2[$i]).'_'.$index.'.'.$secondtblid.' <b>'.$q2join.''.$q2joincf.'</b>';
         //  if(count($selTab1)==1)
           // $strQuery .= inerJoionwithCrmentity($Moduls,$stringaFields,$index,strtolower($selTab1[$i]));
//             if (($selTab2[$i] == "vtiger_account" || $selTab1[$i] == "vtiger_account" ) && $acc == 0) {
//                $strQuery .= '<b> INNER </b> join vtiger_accountbillads as vtiger_accountbillads_'.$index.' <b> ON </b> vtiger_account_'.$index.'.accountid=vtiger_accountbillads_'.$index.'.accountaddressid';
//                $strQuery .= ' <b> INNER join </b> vtiger_accountshipads as vtiger_accountshipads_'.$index.' <b> ON </b>  vtiger_account_'.$index.'.accountid=vtiger_accountshipads_'.$index.'.accountaddressid';
//                $acc =$acc+ 1;
//            }
//            if (($selTab2[$i] == "vtiger_contactdetails" || $selTab1[$i] == "vtiger_contactdetails" ) && $cont == 0) {
//                $strQuery .= ' <b> INNER JOIN </b> vtiger_contactaddress as vtiger_contactaddress_'.$index.' <b> ON </b>  vtiger_contactdetails_'.$index.'.contactid=vtiger_contactaddress_'.$index.'.contactaddressid';
//                $strQuery .= '<b>  INNER JOIN </b> vtiger_contactsubdetails as vtiger_contactsubdetails_'.$index.' <b> ON </b>  vtiger_contactdetails_'.$index.'.contactid=vtiger_contactsubdetails_'.$index.'.contactsubscriptionid';
//                $cont =$cont+ 1;
//            }
//            if (($selTab2[$i] == "vtiger_leaddetails" || $selTab1[$i] == "vtiger_leaddetails" ) && $lead == 0) {
//                $strQuery .= ' <b> INNER JOIN </b>vtiger_leadaddress as vtiger_leadaddress_'.$index.' <b> ON </b>  vtiger_leaddetails_'.$index.'.leadid=vtiger_leadaddress_'.$index.'.leadaddressid';
//                $strQuery .= '<b>  INNER JOIN </b>vtiger_leadsubdetails as vtiger_leadsubdetails_'.$index.' <b> ON </b>  vtiger_leaddetails_'.$index.'.leadid=vtiger_leadsubdetails_'.$index.'.leadsubscriptionid';
//                $lead =$lead+ 1;
//            }
            $index2=$index;
            $index++;
        } else {
            if($selTab1[$i]==$selTabmain)
            {
            $index2--;
            }
            if($index2<0)
            $index2=0;
            $arr["$selTab1[$i]"]=strtolower($selTab1[$i]).'_'.$index2;
            $arr["$selTab2[$i]"]=strtolower($selTab2[$i]).'_'.$index;
            $stringaFields2 = implode(",", selectValueswithjoincrmentity($stringaFields, $Moduls,$index,$arr, $maintab));
            $strf2=substr($stringaFields2, 0, -2);
            if($usergroup=='user')
            {
            $qjoin1=' JOIN vtiger_users User_'.$selTab2[$i].'_'.$index.' on User_'.$selTab2[$i].'_'.$index.'.id=CRM_'.$selTab2[$i].'_'.$index.'.smownerid ';
            $strf2=str_replace(Array('CRM_'.strtolower($selTab1[$i]).'_0.smownerid','CRM_'.strtolower($selTab2[$i]).'_'.$index.'.smownerid'),Array('User_'.strtolower($selTab1[$i]).'_0.user_name','User_'.strtolower($selTab2[$i]).'_'.$index.'.user_name'),substr($stringaFields2, 0, -2));
            }
            else if($usergroup=='group')
            {
            $qjoin1=' join vtiger_groups Group_'.$selTab2[$i].'_'.$index.' on Group_'.$selTab2[$i].'_'.$index.'.groupid=CRM_'.$selTab2[$i].'_'.$index.'.smownerid ';
            $strf2=str_replace(Array('CRM_'.strtolower($selTab1[$i]).'_0.smownerid','CRM_'.strtolower($selTab2[$i]).'_'.$index.'.smownerid'),Array('Group_'.strtolower($selTab1[$i]).'_0.groupname','Group_'.strtolower($selTab2[$i]).'_'.$index.'.groupname'),substr($stringaFields2, 0, -2));
            }
            if($cftables=='cf') {
              $qjoin1cf=' JOIN '.$cftable1[$i].' as '.$cftable1[$i]. '_'. $index.' join '.$cftable2[$i]. ' as '.$cftable2[$i].'_'.$index.' on '.$cftable2[$i].'_'.$index.'.'.$secondtblid.'='.$selTab2[$i].'_'.$index.'.'.$secondtblid;
            }
            $selfields2=coreBOS_Session::get('selectedfields')."," . $strf2;
            $selectquery='<b> SELECT </b>'.$selfields2 ;
            if($selField1[$i]==''){
            $selfld1=$selFieldload1[$i];
            }
            else
            $selfld1=$selField1[$i];
            if($selField2[$i]=='')
            $selfld2=$selFieldload2[$i];
            else
            $selfld2=$selField2[$i];
            $strQuery .= '<b> INNER JOIN </b>'.$selTab2[$i].' <b> as </b> ' . $selTab2[$i].'_'.$index . '<b> ON </b>' . strtolower($selTab1[$i]).'_'.($index-1).'.' . $selfld1 . '<b> = </b>' . strtolower($selTab2[$i]).'_'.$index. '.'. $selfld2.' join vtiger_crmentity as CRM_'.$selTab2[$i].'_'.$index.' on CRM_'.$selTab2[$i].'_'.$index.'.crmid='.$selTab2[$i].'_'.$index.'.'.$secondtblid.'<b>'.$qjoin1.' '.$qjoin1cf.'</b>';
            //$strQuery1 .= '<b> INNER JOIN </b>' . $selTab2[$i] . '<b> ON </b>' ;//. strtolower($selTab1[$i]) . '.' . $selField1[$i] . '<b> = </b>' . strtolower($selTab2[$i]) . '.' . $selField2[$i];
//            if ($selTab2[$i] == "vtiger_account" && $acc == 0) {
//                $strQuery .= '<b> INNER </b> join vtiger_accountbillads as vtiger_accountbillads_'.$index.' <b> ON </b> vtiger_account_'.$index.'.accountid=vtiger_accountbillads_'.$index.'.accountaddressid';
//                $strQuery .= ' <b> INNER join </b> vtiger_accountshipads as vtiger_accountshipads_'.$index.' <b> ON </b>  vtiger_account_'.$index.'.accountid=vtiger_accountshipads_'.$index.'.accountaddressid';
//                $acc =$acc+ 1;
//            }
//            if ($selTab2[$i] == "vtiger_contactdetails" && $cont == 0) {
//                $strQuery .= ' <b> INNER JOIN </b> vtiger_contactaddress as vtiger_contactaddress_'.$index.' <b> ON </b>  vtiger_contactdetails_'.$index.'.contactid=vtiger_contactaddress_'.$index.'.contactaddressid';
//                $strQuery .= '<b>  INNER JOIN </b> vtiger_contactsubdetails as vtiger_contactsubdetails_'.$index.' <b> ON </b>  vtiger_contactdetails_'.$index.'.contactid=vtiger_contactsubdetails_'.$index.'.contactsubscriptionid';
//                $cont =$cont+ 1;
//            }
//            if ($selTab2[$i] == "vtiger_leaddetails" && $lead == 0) {
//                $strQuery .= ' <b> INNER JOIN </b>vtiger_leadaddress as vtiger_leadaddress_'.$index.' <b> ON </b>  vtiger_leaddetails_'.$index.'.leadid=vtiger_leadaddress_'.$index.'.leadaddressid';
//                $strQuery .= '<b>  INNER JOIN </b>vtiger_leadsubdetails as vtiger_leadsubdetails_'.$index.' <b> ON </b>  vtiger_leaddetails_'.$index.'.leadid=vtiger_leadsubdetails_'.$index.'.leadsubscriptionid';
//                $lead =$lead+ 1;
//            }
           //  $strQuery .= inerJoionwithCrmentity($Moduls,$stringaFields,$index,strtolower($selTab1[$i]));
            $index++;
        }

    } //echo $selfields;
    return str_replace(",_"," ",$selectquery.' '.$strQuery);
}
function selectValueswithjoincrmentity($params, $Moduls,$nr,$arr,$maintab)
{
    global $adb;
    $query = $adb->query("select tablename from vtiger_entityname where modulename='$Moduls[1]'");
    $tablename = $adb->query_result($query, 0, "tablename");
    $Querysplit = array();
    if (!empty($params)) {
        $index = 0;

        for ($i = 0; $i <= count($params); $i++) {
            foreach ($Moduls as $modul) {
                $splitvalues = explode(":", $params[$i]);
                if ($splitvalues[1] == "vtiger_crmentity") {
                     $query2 = $adb->query("select tablename from vtiger_entityname where modulename='$modul'");
                     $tablename2 = $adb->query_result($query2, 0, "tablename");
                     $tab2=$arr["$tablename2"];
                     array_push($Querysplit, "CRM_" .$tab2. "." . $splitvalues[2]);

                }
                elseif ($splitvalues[1]==$tablename || $splitvalues[1]!=$maintab){
                    array_push($Querysplit,  $splitvalues[1]."_".$nr . "." . $splitvalues[2]);

                }
                else {
                    array_push($Querysplit,  $splitvalues[1] . "_0." . $splitvalues[2]);//($nr > 0 ? $splitvalues[1]."_".$nr . "." . $splitvalues[2] : $splitvalues[1] . "." . $splitvalues[2]);
                }


            }
            $index++;
          //return $Querysplit;
        }

       return array_unique($Querysplit);

    }
}
//function selectValueswithoutjoincrmentity($params, $Moduls)
//{
//    $Querysplit = array();
//    if (!empty($params)) {
//        $nr2 = 1;
//
//        for ($i = 0; $i <= count($params); $i++) {
//            foreach ($Moduls as $modul) {
//
//                $splitvalues = explode(":", $params[$i]);
//                if ($splitvalues[1] != "vtiger_crmentity") {
//                    array_push($Querysplit, $splitvalues[1] . "." . $splitvalues[2]);
//                }
////                } else {
////                    array_push($Querysplit, $splitvalues[1] . "." . $splitvalues[2]);
////                }
//
//
//            }
//            $nr2++;
////            return $Querysplit;
//        }
//
//        return $Querysplit;
//    }
//}

function inerJoionwithCrmentity($Moduls, $OptVAl,$nr,$tab)
{
    global $adb;
    $joinCrmentity = '';
    //$nr = 1;
    $prova = array();
    $index=0;
    foreach ($Moduls as $modul) {

//        $prova= selectValues($OptVAl, $modul, $nr);
        $joinCrmentity .= '  <b>JOIN</b>   ';
        $query = $adb->query("select entityidfield,tablename from vtiger_entityname where modulename='$modul'");
        $Module_entityidfield = $adb->query_result($query, 0, "entityidfield");
        $Module_tablename = $adb->query_result($query, 0, "tablename");
        $JoinCondition = $Module_tablename . "." . $Module_entityidfield;
        if (!empty($JoinCondition)) {
            $joinCrmentity .= 'vtiger_crmentity <b>as</b> ' . 'CRM_' . strtolower($modul);
            $joinCrmentity .= '  <b>ON</b>  ';
            $joinCrmentity .= ' CRM_' . strtolower($modul) . '.crmid = ' . ($tab!= $Module_tablename ? $Module_tablename.'_'.$nr.'.'.$Module_entityidfield  : $Module_tablename.'_0.'.$Module_entityidfield);
            $joinCrmentity .= ' <b>AND</b> CRM_' . strtolower($modul) . '.deleted = 0   ';

        }
        //$nr++;
        $index++;
    }

    return $joinCrmentity;


//    $querysecondmodule = $adb->query("select entityidfield,tablename from vtiger_entityname where modulename='$modul2'");
//    $SecModule_entityidfield = $adb->query_result($querysecondmodule, 0, "entityidfield");
//    $SecModule_tablename = $adb->query_result($querysecondmodule, 0, "tablename");


//    $joinCrmentity = '  <b>JOIN</b>   ';
//    if (!empty($FirstCondition) && !empty($SecondCondition)) {
//
//        $joinCrmentity .= 'vtiger_crmentity <b>ON</b>';
//        $joinCrmentity .= ' vtiger_crmentity.crmid = ' . $FirstCondition;
//        $joinCrmentity .= '  <b>JOIN</b>  ';
//        $joinCrmentity .= 'vtiger_crmentity <b>ON</b>';
//        $joinCrmentity .= ' vtiger_crmentity.crmid = ' . $SecondCondition;
//        return $joinCrmentity;
//    } else {
//        return "";
//    }
}
function getentityidfield($table)
{global $adb;
        $query = $adb->query("select entityidfield,tablename from vtiger_entityname where tablename='$table'");
        $Module_entityidfield = $adb->query_result($query, 0, "entityidfield");
        return $Module_entityidfield;
}
/*
 * Ricevendo il nome di una tabella, fornisce il un array contenente tutti
 * i nomi dei campi in essa contenuta.
 */

function getCampi($table)
{
    global $db;
    $fields = mysql_list_fields($db, $table);
    $numColumn = mysql_num_fields($fields);
    for ($i = 0; $i < $numColumn; $i++) {
        $fieldList[$i] = mysql_field_name($fields, $i);
    }
    return $fieldList;
}

/*
 * Riceve in ingresso un array e un intero, e restituisce un sub array
 */
function prelevaArray($array, $indice)
{
    for ($i = 0; $i < $indice; $i++) {
        $subArray[$i] = $array[$i];
    }
    return $subArray;
}


/*
 * Riceve in ingresso un array, e concatena ogni elemento in un'unica stringa
 */
//function concatenaAllField($allFields)
//{
//    for ($i = 0; $i < count($allFields); $i++) {
//        if ($i == 0) {
//            $stringa = $allFields[$i];
//        } else {
//            $stringa = $stringa . ', ' . $allFields[$i];
//        }
//    }
//    return $stringa;
//}
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
//echo $generatetQuery;
//exit();
//function getAllFields($tableList1, $tableList2)
//{
//    $allFields = array();
//    $num = 0;
//    $tableList2[count($tableList2)] = $tableList1[0];
//
//    for ($i = 0; $i < count($tableList2); $i++) {
//        if (!(in_array($tableList2[$i], prelevaArray($tableList2, $i)) || ((in_array($tableList2[$i], prelevaArray($tableList1, $i))) && $tableList2[$i] != $tableList1[0]))) {
//            $fields = getCampi($tableList2[$i]);
//            for ($j = 0; $j < (count($tableList2)); $j++) {
//                if ($tableList2[$i] != $tableList2[$j]) {
//                    for ($k = 0; $k < count($fields); $k++) {
//                        $fieldsTabList2 = getCampi($tableList2[$j]);
//                        if (in_array($fields[$k], $fieldsTabList2)) {
//                            $stringa = $tableList2[$i] . '.' . $fields[$k] . ' <b>AS</b> ' . $tableList2[$i] . '_' . $fields[$k];
//                            for ($u = 0; $u < count($fieldsTabList2); $u++) {
//                                if ($fieldsTabList2[$u] == $fields[$k]) {
//                                    $fieldsList2[$u] = $tableList2[$j] . '.' . $fieldsList2[$k] . ' <b>AS</b> ' . $tableList2[$j] . '_' . $fieldsList2[$k];
//                                }
//                            }
//                            $fields[$k] = $stringa;
//                        }
//                    }
//                }
//            }
//            for ($s = 0; $s < count($fields); $s++) {
//                $allFields[$num] = $fields[$s];
//                $num++;
//            }
//        }
//    }
//    return $allFields;
//}
require_once('Smarty_setup.php');
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
$smarty = new vtigerCRM_Smarty();
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("DATEFORMAT", $current_user->date_format);
$smarty->assign("QUERY", $generatetQuery);

//$optString = "";
//foreach ($optgroup as  $key => $v1) {
//    $optString .= "<optgroup label='".$key."'>";
//    foreach ($v1 as $k2 => $v2) {
//        $optString.='<option value='.$k2.">".$v2."</option>";
//    }
//    $optString.='</optgroup>';
//}

//$smarty->assign("FIELDS", $PRovatjeter);
$smarty->assign("valueli", $sendarray);
//$smarty->assign("texticombo", $texticombo);
//$smarty->assign("FOPTION", '');
$smarty->assign("FIELDLABELS", $campiSelezionatiLabels);
$smarty->assign("JS_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$smarty->display("modules/MapGenerator/WhereCondition.tpl");
?>
