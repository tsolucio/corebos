<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : Business Map
 *  Version      : 1.0
 *  Author       : AT Consulting
 *************************************************************************************************/
include_once("modules/cbMap/cbMap.php");
require_once('data/CRMEntity.php');
require_once('include/utils/utils.php');
require_once('All_functions.php');
require_once('Staticc.php');
global $root_directory, $log;

$FirstModule=$_POST['FirstModul'];
$secmodule=$_POST['secmodule'];
$FirstModuleID=$_POST['selField1'];
$SecondModuleID=$_POST['selField2'];
$allvalues=$_POST['allvalues'];
$MapName=$_POST['nameView'];
$SaveasMapTextImput=$_POST['SaveasMapTextImput'];
$QueryGenerate=$_POST['QueryGenerate'];
$MapID=explode(',',$_POST['MapId']);

$mapname=(!empty($SaveasMapTextImput)?$SaveasMapTextImput:$MapName);
$idquery=!empty($MapID[0])?$MapID[0]:md5(date("Y-m-d H:i:s").uniqid(rand(), true));
if (!empty($allvalues)) {
    // echo $FirstModule;
    // echo $secmodule;
    // echo $FirstModuleID;
    // echo $SecondModuleID;
     $alldata=json_decode($allvalues);
   
    try
    {
        if (strlen($MapID[1])==0) {
            include_once('modules/cbMap/cbMap.php');
            $focust = new cbMap();
            $focust->column_fields['assigned_user_id'] = 1;
            $focust->column_fields['mapname'] = $mapname;
            $focust->column_fields['content']=addsqlTag($QueryGenerate,explode(':',$alldata->returnvaluesval)[1]);
            $focust->column_fields['mvqueryid']=$queryid;
            $focust->column_fields['targetname'] =$alldata->FirstModuleJSONvalue;
            $focust->column_fields['description'] = add_description($alldata);
            $focust->column_fields['selected_fields'] =$alldata->Labels;
            $focust->column_fields['maptype'] = "Condition Query";
            $focust->column_fields['mvqueryid'] = $idquery;
            $log->debug(" we inicialize value for insert in database ");
            if (!$focust->saveentity("cbMap")) {
                     if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0)
                     {
                        echo save_history($alldata,$idquery,addsqlTag($QueryGenerate,explode(':',$alldata->returnvaluesval)[1])).",".$focust->id;
                     }else
                     {
                         throw new Exception(" Missing the History Table Check The name of table History", 1);
                     }
            } else {
               throw new Exception(" Something was wrong on save Map check the saveentity", 1);
            }

        }else
        {
            include_once('modules/cbMap/cbMap.php');
            $focust = new cbMap();
             $focust->id = $MapID[1];
            $focust->retrieve_entity_info($MapID[1],"cbMap");
            // $focust->column_fields['assigned_user_id'] = 1;
            // $focust->column_fields['mapname'] = ;
            $focust->column_fields['content']=addsqlTag($QueryGenerate,explode(':',$alldata->returnvaluesval)[1]);
            $focust->column_fields['mvqueryid']=$queryid;
            $focust->column_fields['targetname'] =$alldata->FirstModuleJSONvalue;
            $focust->column_fields['description'] = add_description($alldata);
            $focust->column_fields['selected_fields'] =$alldata->Labels;
            // $focust->column_fields['maptype'] = "Condition Query";
            $focust->column_fields['mvqueryid'] = $idquery;
            $log->debug(" we inicialize value for insert in database ");
            $focust->mode = "edit";
            $focust->save("cbMap");
               if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                    echo save_history($alldata,$idquery,addsqlTag($QueryGenerate,explode(':',$alldata->returnvaluesval)[1])).",".$focust->id;
                 } 
                 else
                 {
                     throw new Exception(" Missing the History Table Check The name of table History", 1);
                 }
        }

        
    }catch (Exception $ex) {
        $log->debug(TypeOFErrors::ErrorLG."Something was wrong check the Exception ".$ex);
        echo TypeOFErrors::ErrorLG."Something was wrong check the Exception ".$ex;
    }

}else
{

}

/**
 * function to save the history 
 * @param  [type] $datas   [description]
 * @param  [type] $queryid [description]
 * @param  [type] $xmldata [description]
 * @return [type]          [description]
 */
function save_history($datas,$queryid,$xmldata){
        global $adb;
        $idquery2=$queryid;
        $q=$adb->query("select sequence from ".TypeOFErrors::Tabele_name." where id='$idquery2' order by sequence DESC");
             //$nr=$adb->num_rows($q);
             // echo "q=".$q;
             
        $seq=$adb->query_result($q,0,0);
      
        if(!empty($seq))
        {
            $seq=$seq+1;
             $adb->query("update ".TypeOFErrors::Tabele_name." set active=0 where id='$idquery2'");                            
              //$seqmap=count($data);
             $adb->pquery("insert into ".TypeOFErrors::Tabele_name." values (?,?,?,?,?,?,?,?,?,?,?)",array($idquery2,$datas->FirstModuleJSONvalue,$datas->FirstModuleJSONtext,$datas->SecondModuleJSONvalue,$datas->SecondModuleJSONtext,$xmldata,$seq,1,$datas->FirstModuleJSONfield,$datas->SecondModuleJSONfield,$datas->Labels));
              //return $idquery;
        }else 
        {

            $adb->pquery("insert into ".TypeOFErrors::Tabele_name." values (?,?,?,?,?,?,?,?,?,?,?)",array($idquery2,$datas->FirstModuleJSONvalue,$datas->FirstModuleJSONtext,$datas->SecondModuleJSONvalue,$datas->SecondModuleJSONtext,$xmldata,1,1,$datas->FirstModuleJSONfield,$datas->SecondModuleJSONfield,$datas->Labels));
        }
        echo $idquery2;
}

/**
 * this function is to generate the description for map 
 * @param object $datas the  object for genarete the map description
 */
function add_description($datas)
{
    if (!empty($datas)) {

            $datatuconvert=explode(',',$datas->Labels);

            $xml=new DOMDocument("1.0");
            $root=$xml->createElement("map");
            $xml->appendChild($root);
            //strt create the first module
            $Fmodule = $xml->createElement("Fmodule");

            $Fmoduleid = $xml->createElement("FmoduleID");
            $FmoduleText = $xml->createTextNode($datas->FirstModuleJSONfield);
            $Fmoduleid->appendChild($FmoduleText);
            
            $Fmodulename=$xml->createElement("Fmodulename");
            $FmodulenameText=$xml->createTextNode(preg_replace('/\s+/', '',$datas->FirstModuleJSONvalue));
            $Fmodulename->appendChild($FmodulenameText);

            $Fmodule->appendChild($Fmoduleid);
            $Fmodule->appendChild($Fmodulename);

            //second module
            $Secmodule = $xml->createElement("Secmodule");

            $Secmoduleid = $xml->createElement("SecmoduleID");
            $SecmoduleText = $xml->createTextNode(preg_replace('/\s+/','',$datas->SecondModuleJSONfield));
            $Secmoduleid->appendChild($SecmoduleText);
            $Secmodulename=$xml->createElement("Secmodulename");
            $SecmodulenameText=$xml->createTextNode( trim(preg_replace('/\s*\([^)]*\)/', '',preg_replace("(many)",'', preg_replace('/\s+/', '', explode(";",$datas->SecondModuleJSONvalue)[0])))));
            $Secmodulename->appendChild($SecmodulenameText);    
            $Secmodule->appendChild($Secmoduleid);
            $Secmodule->appendChild($Secmodulename);     
            $fields = $xml->createElement("fields");
            $countarray=(count($datatuconvert)-1);
            for($i=0;$i<=$countarray;$i++)
               {          
                //     //get target field name
                         $field = $xml->createElement("field");

                        $label = $xml->createElement("fieldID");
                        $labelText=$xml->createTextNode(explode(":",$datatuconvert[$i])[1]);
                        $label->appendChild($labelText);
                        $field->appendChild($label);

                        $name = $xml->createElement("fieldname");
                        $nameText=$xml->createTextNode(explode(":",$datatuconvert[$i])[1]);
                        $name->appendChild($nameText);
                        $field->appendChild($name);
                      $fields->appendChild($field);
               }
            //$root->appendChild($name);
             $root->appendChild($Fmodule);
             $root->appendChild($Secmodule);
             $root->appendChild($fields);
             $xml->formatOutput = true;
             return $xml->saveXML();

    }else
    {
        throw new Exception("The object is empty Check the POST allvalues", 1);
        
    }
}



function addsqlTag($QueryGenerate='',$returni)
{
    if (!empty($QueryGenerate)) {

        $xml=new DOMDocument("1.0");
        $root=$xml->createElement("map");
        $xml->appendChild($root);

        $maptype = $xml->createElement("maptype");
        $maptypeText=$xml->createTextNode("SQL");
        $maptype->appendChild($maptypeText);

        $sql = $xml->createElement("sql");
        $sqlText=$xml->createTextNode($QueryGenerate);
        $sql->appendChild($sqlText);
        
        $return = $xml->createElement("return");
        $returnText=$xml->createTextNode($returni);
        $return->appendChild($returnText);

        $root->appendChild($maptype);
        $root->appendChild($sql);
        $root->appendChild($return);
        $xml->formatOutput = true;
        return $xml->saveXML();
        
    }
}

?>
