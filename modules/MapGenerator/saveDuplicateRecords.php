<?php

/**
 * @Author: edmondi kacaj
 * @Date:   2017-12-19 14:34:17
 * @Last Modified by: programim95@gmail.com
 * @Last Modified time: 2018-03-07 15:14:28
 */
include_once ("modules/cbMap/cbMap.php");
require_once ('data/CRMEntity.php');
require_once ('include/utils/utils.php');
require_once ('All_functions.php');
require_once ('Staticc.php');


global $root_directory, $log; 
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "DuplicateRecords"; // stringa con tutti i campi scelti in selField1
$SaveasMapText = $_POST['SaveasMapText'];
$DuplicateDirect = $_POST['DuplicateDirectRelationscheck'];
$Data = $_POST['ListData'];
$MapID=explode(',', $_REQUEST['savehistory']); 
$mapname=(!empty($SaveasMapText)? $SaveasMapText:$MapName);
$idquery2=!empty($MapID[0])?$MapID[0]:md5(date("Y-m-d H:i:s").uniqid(rand(), true));


if (empty($SaveasMapText)) {
     if (empty($MapName)) {
            echo "Missing the name of map Can't save";
            return;
       }
}
if (empty($MapType))
 {
    $MapType = "DuplicateRecords";
}

if (!empty($Data)) {
	
	$jsondecodedata=json_decode($Data);	
	  // echo add_content($jsondecodedata);
	// print_r($jsondecodedata);
	if(strlen($MapID[1]==0)){

	   $focust = new cbMap();
     $focust->column_fields['assigned_user_id'] = 1;
     $focust->column_fields['mapname']=$mapname;
     $focust->column_fields['content']=add_content($jsondecodedata,$DuplicateDirect);
     $focust->column_fields['maptype'] =$MapType;
     $focust->column_fields['description']= add_content($jsondecodedata,$DuplicateDirect);
    //  $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
     $focust->column_fields['mvqueryid']=$idquery2;
     $log->debug(" we inicialize value for insert in database ");
     if (!$focust->saveentity("cbMap"))//
      {
      		
          if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                 echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata,$DuplicateDirect)).",".$focust->id;
             } 
             else{
                echo "0,0";
                 $log->debug("Error!! MIssing the history Table");
             }  
                    
      } else 
      {
      	 // echo "Edmondi save in map,hghghghghgh";
        //   exit();
         //echo focus->id;
         echo "Error!! something went wrong";
         $log->debug("Error!! something went wrong");
      }

  }else{

     include_once ("modules/cbMap/cbMap.php");
     $focust = new cbMap();
     $focust->id = $MapID[1];
     $focust->retrieve_entity_info($MapID[1],"cbMap");
     $focust->column_fields['assigned_user_id'] = 1;
     // $focust->column_fields['mapname'] = $MapName;
     $focust->column_fields['content']=add_content($jsondecodedata,$DuplicateDirect);
    //  $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
     $focust->column_fields['maptype'] =$MapType;
     $focust->column_fields['mvqueryid']=$idquery2;
     $focust->column_fields['description']= add_content($jsondecodedata,$DuplicateDirect);
     $focust->mode = "edit";
     $focust->save("cbMap");

          if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                 echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata,$DuplicateDirect)).",".$MapID[1];
             } 
             else{
                echo "0,0";
                 $log->debug("Error!! MIssing the history Table");
             }
  }

}


/**
 * function to convert to xml the array come from post 
 *
 * @param      <type>  $DataDecode  The data decode
 * @param      DataDecode  {Array}  {This para is a array }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function add_content($DataDecode,$DuplicateDirect="0")
{
	$xml = new DOMDocument("1.0");
     $root = $xml->createElement("map");
     $xml->appendChild($root);   
     //put the menus     
     $originmodule = $xml->createElement("originmodule");
     $originmodulename = $xml->createElement("originname");
     $originmodulenameText = $xml->createTextNode($DataDecode[0]->temparray->FirstModule);
     $originmodulename->appendChild($originmodulenameText);
     $originmodule->appendChild($originmodulename);
     $root->appendChild($originmodule);
     $duplicatevalue='0';
     $relatedmodules=$xml->createElement("relatedmodules");


	foreach ($DataDecode as $values) {
		 $relatedmodule=$xml->createElement("relatedmodule");

		 $module=$xml->createElement("module");
	     $moduletext=$xml->createTextNode(explode('#',$values->temparray->relatedModule)[0]);
	     $module->appendChild($moduletext);

	     $relation=$xml->createElement("relation");
	     $relationtext=$xml->createTextNode(explode('#',$values->temparray->relatedModule)[1]);
	     $relation->appendChild($relationtext);
		
	     $relatedmodule->appendChild($module);
	     $relatedmodule->appendChild($relation);

         $relatedmodules->appendChild($relatedmodule);
         $duplicatevalue=$values->temparray->DuplicateDirectRelationscheck;

	}
     $DuplicateDirectRelations = $xml->createElement("DuplicateDirectRelations");
     $DuplicateDirectRelationsText = $xml->createTextNode($duplicatevalue);
     $DuplicateDirectRelations->appendChild($DuplicateDirectRelationsText);
     
     $root->appendChild($relatedmodules); 
     $root->appendChild($DuplicateDirectRelations);
            
     $xml->formatOutput = true;
     return $xml->saveXML();
}







function add_aray_for_history($decodedata)
{
   $Labels=""; 
  return array
     (
        'Labels'=>$Labels,
        'FirstModuleval'=>$decodedata[0]->temparray->FirstModule,
        'FirstModuletxt'=>"",
        'SecondModuleval'=>"",
        'SecondModuletxt'=>"",
        'firstmodulelabel'=>"",
        'secondmodulelabel'=>""
     );
}

/**
 * save history is a function which save in db the history of map 
 * @param  [array] $datas   array 
 * @param  [type] $queryid the id of qquery
 * @param  [type] $xmldata the xml data 
 * @return [type]          boolean true or false 
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
             $adb->pquery("insert into ".TypeOFErrors::Tabele_name." values (?,?,?,?,?,?,?,?,?,?,?)",array($idquery2,$datas["FirstModuleval"],$datas["FirstModuletxt"],$datas["SecondModuletxt"],$datas["SecondModuleval"],$xmldata,$seq,1,$datas["firstmodulelabel"],$datas["secondmodulelabel"],$datas["Labels"]));
              //return $idquery;
        }else 
        {

            $adb->pquery("insert into ".TypeOFErrors::Tabele_name." values (?,?,?,?,?,?,?,?,?,?,?)",array($idquery2,$datas["FirstModuleval"],$datas["FirstModuletxt"],$datas["SecondModuletxt"],$datas["SecondModuleval"],$xmldata,1,1,$datas["firstmodulelabel"],$datas["secondmodulelabel"],$datas["Labels"]));
        }
        echo $idquery2;
}

