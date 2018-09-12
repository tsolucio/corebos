<?php


//saveImportBussinesMapping

include_once ("modules/cbMap/cbMap.php");
require_once ('data/CRMEntity.php');
require_once ('include/utils/utils.php');
require_once('All_functions.php');
require_once('Staticc.php');


global $root_directory, $log; 
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$UpdateId = $_POST['UpdateId']; // get update
$MapType = "Import"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "Import";
}

if (empty($UpdateId))
{
    $MapType = "FIRST";
}

if (!empty($Data))
{
	$jsondecodedata=json_decode($Data);
	//print_r($jsondecodedata);
	// echo add_content($jsondecodedata,$UpdateId);
	
	if(strlen($MapID[1]==0)){

	   $focust = new cbMap();
     $focust->column_fields['assigned_user_id'] = 1;
     // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
     $focust->column_fields['mapname']=$mapname;
     $focust->column_fields['content']=add_content($jsondecodedata,$UpdateId);
     $focust->column_fields['maptype'] =$MapType;
     $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
     $focust->column_fields['description']= add_content($jsondecodedata,$UpdateId);
     $focust->column_fields['mvqueryid']=$idquery2;
     $log->debug(" we inicialize value for insert in database ");
     if (!$focust->saveentity("cbMap"))//
      {
      		
          if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                 echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata,$UpdateId)).",".$focust->id;
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
     $focust->column_fields['content']=add_content($jsondecodedata,$UpdateId);
     $focust->column_fields['maptype'] =$MapType;
     $focust->column_fields['mvqueryid']=$idquery2;
     $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
     $focust->column_fields['description']= add_content($jsondecodedata,$UpdateId);
     $focust->mode = "edit";
     $focust->save("cbMap");

          if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                 echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata,$UpdateId)).",".$MapID[1];
             } 
             else{
                echo "0,0";
                 $log->debug("Error!! MIssing the history Table");
             }
    }
}

/**
 * @param DataDecode {Array} {This para is a array }
 */
function add_content($DataDecode,$UpdateId)
{
     //$DataDecode = json_decode($dat, true);
     $countarray=(count($DataDecode)-1);
     $xml = new DOMDocument("1.0");
     $root = $xml->createElement("map");
     $xml->appendChild($root);
    
     

     // put the target module 
     $targetmodule = $xml->createElement("targetmodule");
     $targetname = $xml->createElement("targetname");
     $targetnameText = $xml->createTextNode($DataDecode[0]->temparray->FirstModule);
     $targetname->appendChild($targetnameText);
     $targetmodule->appendChild($targetname);
     $root->appendChild($targetmodule);

      $fields = $xml->createElement("fields");
      $matches = $xml->createElement("matches");

      foreach ($DataDecode as $value) {
      	$field = $xml->createElement("field");
      	 $fieldname = $xml->createElement("fieldname");
         $fieldnameText = $xml->createTextNode(explode(":", $value->temparray->Firstfield)[1]);
         $fieldname->appendChild($fieldnameText);
         $field->appendChild($fieldname);

         $fieldID = $xml->createElement("fieldID");
         $fieldIDText = $xml->createTextNode("");
         $fieldID->appendChild($fieldIDText);
         $field->appendChild($fieldID);

         $values = $xml->createElement("value");
         $valuesText = $xml->createTextNode(explode(":", $value->temparray->Firstfield)[1]);
         $values->appendChild($valuesText);
         $field->appendChild($values);

         $predefined = $xml->createElement("predefined");
         $predefinedText = $xml->createTextNode("");
         $predefined->appendChild($predefinedText);
         $field->appendChild($predefined);

         $Orgfields = $xml->createElement("Orgfields");
         $Relfield = $xml->createElement("Relfield");

	      	 $RelfieldName = $xml->createElement("RelfieldName");
	         $RelfieldNameText = $xml->createTextNode(GetThevaluesFromModul(GetModulrelation(CheckIfIsRelation(explode(':',$value->temparray->Firstfield)[1]))));
	         $RelfieldName->appendChild($RelfieldNameText);
	         $Relfield->appendChild($RelfieldName);

	         $RelModule = $xml->createElement("RelModule");
	         $RelModuleText = $xml->createTextNode(GetModulrelation(CheckIfIsRelation(explode(':',$value->temparray->Firstfield)[1])));
	         $RelModule->appendChild($RelModuleText);
	         $Relfield->appendChild($RelModule);

	         $linkfield = $xml->createElement("linkfield");
	         $linkfieldText = $xml->createTextNode(!empty(CheckIfIsRelation(explode(':',$value->temparray->Firstfield)[1]))?explode(":", $value->temparray->Firstfield)[1]:"");
	         $linkfield->appendChild($linkfieldText);
	         $Relfield->appendChild($linkfield);
	     $Orgfields->appendChild($Relfield);
	     $field->appendChild($Orgfields);
	     $fields->appendChild($field);

	     // // //Matches
	     $match = $xml->createElement("match");
      	 $mfieldname = $xml->createElement("fieldname");
         $mfieldnameText = $xml->createTextNode(explode(":", $value->temparray->SecondField)[1]);
         $mfieldname->appendChild($mfieldnameText);
         $match->appendChild($mfieldname);

         $mfieldID = $xml->createElement("fieldID");
         $mfieldIDText = $xml->createTextNode("");
         $mfieldID->appendChild($mfieldIDText);
         $match->appendChild($mfieldID);

         $mvalue = $xml->createElement("value");
         $mvalueText = $xml->createTextNode(explode(":", $value->temparray->SecondField)[1]);
         $mvalue->appendChild($mvalueText);
         $match->appendChild($mvalue);

         $mpredefined = $xml->createElement("predefined");
         $mpredefinedText = $xml->createTextNode("");
         $mpredefined->appendChild($mpredefinedText);
         $match->appendChild($mpredefined);

         $matches->appendChild($match);
        $updateId=$value->temparray->UpdateId;
      }
     
     $options = $xml->createElement("options");
     $update = $xml->createElement("update");
     $updateText = $xml->createTextNode($updateId);
     $update->appendChild($updateText);
     $options->appendChild($update);
     

     $root->appendChild($fields);      
     $root->appendChild($matches);
     $root->appendChild($options);      
     $xml->formatOutput = true;
     return $xml->saveXML();
}


function add_aray_for_history($decodedata)
{
   	$labels="";
   	foreach ($decodedata as $value) {
   		$labels.=",".explode(":", $value->temparray->Firstfield)[1].",".explode(":", $value->temparray->SecondField)[1];
   	}	
    return array
     (
        'Labels'=>$labels,
        'FirstModuleval'=>preg_replace('/\s+/', '',$decodedata[0]->temparray->FirstModule),
        'FirstModuletxt'=>preg_replace('/\s+/', '',$decodedata[0]->temparray->FirstModuleText),
        'SecondModuleval'=>"",
        'SecondModuletxt'=>"",
        'firstmodulelabel'=>getModuleID(preg_replace('/\s+/', '',$decodedata[0]->temparray->FirstModule)),
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

function CheckIfIsRelation($columnname,$uitype=10,$getColumn="fieldid")
{
    try{
    	global $adb;
	  $sql="SELECT * FROM  `vtiger_field` WHERE  `columnname` ='".$columnname."' AND  `uitype`=".$uitype;
	    $result = $adb->query($sql);
	    $num_rows=$adb->num_rows($result);
	    if ($num_rows>0) {            
	        return $adb->query_result($result,0,$getColumn);
	    }else{
	        // throw new Exception("Data retrive from query are empty or something was wrong ", 1);
	        return "";
	    }
    }catch(Exception $ex){
    	return $ex;
    }
    
}

function GetModulrelation($fieldid,$columnname="module")
{
    if (!empty($fieldid)) {
    	global $adb;
	    $sql="SELECT * FROM  `vtiger_fieldmodulerel` WHERE  `fieldid` ='".$fieldid."'";
	    $result = $adb->query($sql);
	    $num_rows=$adb->num_rows($result);
	    if ($num_rows>0) {       
	            return $adb->query_result($result,0,$columnname);
	    }else{
	        // throw new Exception("Data retrive from query are empty or something was wrong ", 1);       
	        echo "";
	    }
    }
    
}

function GetThevaluesFromModul($modulename,$columnname="fieldname")
{
    if (!empty($modulename)) {
    	global $adb;
	    $sql="SELECT * FROM `vtiger_entityname` WHERE `modulename` ='".$modulename."'";
	    $result = $adb->query($sql);
	    $num_rows=$adb->num_rows($result);
	    if ($num_rows>0) {       
	            return $adb->query_result($result,0,$columnname);
	    }else{
	        // throw new Exception("Data retrive from query are empty or something was wrong ", 1);       
	        echo "";
	    }
    }
    
}
