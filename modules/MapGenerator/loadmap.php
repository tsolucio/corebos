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

require_once('Smarty_setup.php');
include 'XmlContent.php';
include('modfields.php');

global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user,$log;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
$smarty = new vtigerCRM_Smarty();


$mid=$_POST['MapID'];
$qid=$_POST['queryid'];



$sql="SELECT * from mvqueryhistory where id=? AND active=?";
$result=$adb->pquery($sql, array($qid, 1));
$query=str_replace("query", "", $adb->query_result($result,0,'query'));
$q=explode("FROM",$query);
coreBOS_Session::set('selectedfields',str_replace(Array("<b> SELECT </b>","<b>"),Array("",""),$q[0]));

function whereConditions($module)

{
    $a =getModFields($module, $acno.$dbname);
    $a=htmlentities($a);
    //$a=htmlspecialchars($a);
    $exp=explode("'", $a);
    $n=count($exp);

    for($i=0;$i<$n;$i++) {
        if ($i%2!=0) {
            $values[]=$exp[$i];
        } else {
            $values[]=preg_replace("/[^A-Za-z0-9\- ]/", "", $exp[$i]);
        }
    }

    for ($i=0; $i<$n-1; $i++) {
        if ($i%2==0) {
            $values[$i]=str_replace('gt','',$values[$i]);
            $values[$i]=substr($values[$i],0,-22);
        }
    }

    $values[$n-1]=str_replace('gt','',$values[$n-1]);
    $values[$n-1]=substr($values[$n-1],0,-18);
    array_shift($values);
    $nr=$n/2;
    for ($j = 0; $j <$nr-1; $j++) {
        $sendarray[] = array(
            'Values' => $values[2*$j],
            'Texti' => $values[2*$j+1],
        );
    }

  return $sendarray;

}
$seq=$adb->query_result($result, 0, "sequence");
$seq=$seq-1;
$FirstModule = $adb->query_result($result, $seq, "firstmodule");
$SecondModule = $adb->query_result($result, $seq, "secondmodule");
$first=whereConditions($FirstModule);
$second=whereConditions($SecondModule);
$labels=array_merge($first,$second);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("FIELDLABELS", $campiSelezionatiLabels);
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("DATEFORMAT", $current_user->date_format);
$smarty->assign("QUERY", $query);
if($SecondModule=='None')
    $smarty->assign("valueli", $first);
else
    $smarty->assign("valueli", $labels);

$smarty->display("modules/MapGenerator/WhereCondition.tpl");
?>