
<?php

/*
 * @Author: Edmond Kacaj 
 * @Date: 2018-03-15 11:37:58 
 * @Last Modified by: programim95@gmail.com
 * @Last Modified time: 2018-05-07 15:17:31
 */

include_once ("modules/cbMap/cbMap.php");
require_once ('data/CRMEntity.php');
require_once ('include/utils/utils.php');
include_once ('All_functions.php');
require_once ('Staticc.php');


global $root_directory, $log; 
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "WS Validation"; // stringa con tutti i campi scelti in selField1
$SaveasMapText = $_POST['SaveasMapText'];
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
    $MapType = "WS Validation";
}

if (!empty($Data)) {
	
    $jsondecodedata=json_decode($Data);	
    $myDetails=array();
	if(strlen($MapID[1]==0)){

	   $focust = new cbMap();
     $focust->column_fields['assigned_user_id'] = 1;
     // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
     $focust->column_fields['mapname']=$mapname;
     $focust->column_fields['content']=add_content($jsondecodedata);
     $focust->column_fields['maptype'] =$MapType;
     $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
     $focust->column_fields['description']= add_content($jsondecodedata);
     $focust->column_fields['mvqueryid']=$idquery2;
     $log->debug(" we inicialize value for insert in database ");
     if (!$focust->saveentity("cbMap"))//
      {
      		
          if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                 echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata)).",".$focust->id;
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
     $focust->column_fields['content']=add_content($jsondecodedata);
     $focust->column_fields['maptype'] =$MapType;
     $focust->column_fields['mvqueryid']=$idquery2;
     $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
     $focust->column_fields['description']= add_content($jsondecodedata);
     $focust->mode = "edit";
     $focust->save("cbMap");

          if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                 echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata)).",".$MapID[1];
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
function add_content($DataDecode)
{
     //$DataDecode = json_decode($dat, true);
     $countarray=(count($DataDecode)-1);
     $xml = new DOMDocument("1.0");
     $root = $xml->createElement("map");
     $xml->appendChild($root);  
     

     $originmodule = $xml->createElement("originmodule");
     $originmoduleid = $xml->createElement("originid");
     $originmoduleText = $xml->createTextNode(getModuleID($DataDecode[0]->temparray->FirstModule));
     $originmoduleid->appendChild($originmoduleText);
     $originmodulename = $xml->createElement("originname");
     $originmoduleText = $xml->createTextNode(trim($DataDecode[0]->temparray->FirstModule));
     $originmodulename->appendChild($originmoduleText);
     $originmodule->appendChild($originmoduleid);
     $originmodule->appendChild($originmodulename);
     $root->appendChild($originmodule);  

        $target = $xml->createElement("targetmodule");
        $targetid = $xml->createElement("targetid");
        $targetidText = $xml->createTextNode(getModuleID(explode('#',$DataDecode[0]->temparray->TargetModule)[0]));
        $targetid->appendChild($targetidText);
        $targetname = $xml->createElement("targetname");
        $targetnameText = $xml->createTextNode( trim(explode('#',$DataDecode[0]->temparray->TargetModule)[0]));
        $targetname->appendChild($targetnameText);
        $target->appendChild($targetid);
        $target->appendChild($targetname);
        $root->appendChild($target);  
    

     //put the fields
     $fields=$xml->createElement("fields");
     foreach ($DataDecode as $value) {

         $field = $xml->createElement("field");
          
      	 $fieldname = $xml->createElement("fieldname");
	     $fieldnametext = $xml->createTextNode($value->temparray->{'ws-val-name'});
	     $fieldname->appendChild($fieldnametext);
         $field->appendChild($fieldname);
         
         $fieldvalue = $xml->createElement("fieldvalue");
	     $fieldvaluetext = $xml->createTextNode($value->temparray->{'ws-val-value'});
	     $fieldvalue->appendChild($fieldvaluetext);
         $field->appendChild($fieldvalue);

         $validationtype = $xml->createElement("validationtype");
	     $validationtypetext = $xml->createTextNode($value->temparray->{'ws-val-validation'});
	     $validationtype->appendChild($validationtypetext);
         $field->appendChild($validationtype);
         
         $origin = $xml->createElement("origin");
         $origintext = $xml->createTextNode($value->temparray->{'ws-val-origin-select'});
         $origin->appendChild($origintext);
         $field->appendChild($origin);
                

         $fields->appendChild($field);
        }
     
     $root->appendChild($fields);        
     $xml->formatOutput = true;
     return $xml->saveXML();
}







function add_aray_for_history($decodedata)
{
   $Labels="";
    return array
     (
        'Labels'=>$Labels,
        'FirstModuleval'=>preg_replace('/\s+/', '',$decodedata[0]->temparray->FirstModule),
        'FirstModuletxt'=>preg_replace('/\s+/', '',$decodedata[0]->temparray->FirstModuleText),
        'SecondModuleval'=>explode('#',$DataDecode[0]->temparray->TargetModule)[0],
        'SecondModuletxt'=>$DataDecode[0]->temparray->TargetModuleText,
        'firstmodulelabel'=>getModuleID(preg_replace('/\s+/', '',$decodedata[0]->temparray->FirstModule)),
        'secondmodulelabel'=>getModuleID(preg_replace('/\s+/', '',explode('#',$DataDecode[0]->temparray->TargetModule)[0])),
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





?>




