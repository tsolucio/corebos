<?php


// SELECT * from vtiger_tab join vtiger_field  ON vtiger_tab.tabid=vtiger_field.tabid where name='Adocdetail' and uitype='10';

// SELECT * from  vtiger_fieldmodulerel;

// Select * from vtiger_entityname where modulename='Adocdetail'
 
include_once ("modules/cbMap/cbMap.php");
require_once ('data/CRMEntity.php');
require_once ('include/utils/utils.php');
require_once('All_functions.php');
include_once 'Staticc.php';
global $root_directory, $log; 
$Data = array();

//  var_dump($_REQUEST, true);
// exit();

$MapName = $_POST['MapName'];
$SaveasMapText=$_POST['SaveasMapText']; // stringa con tutti i campi scelti in selField1
$MapType = "Mapping"; // stringa con tutti i campi scelti in selField1
$Data = $_POST['ListData'];
$MapID=explode(',', $_REQUEST['savehistory']); 

$mapname=(!empty($SaveasMapText) ? $SaveasMapText : $MapName);
$idquery=!empty($MapID[0])?$MapID[0]:md5(date("Y-m-d H:i:s").uniqid(rand(), true));

if (empty($SaveasMapText)) {
     if (empty($MapName)) {
            echo "Missing the name of map Can't save";
            return;
       }
}
if (empty($MapType))
 {
    $MapType = "Mapping";
}
 
if (!empty($Data))
{
     $decodedata = json_decode($Data, true);
    //   print_r($decodedata);
    //   echo add_content($decodedata);
    //  exit();

    if (strlen($MapID[1])==0) {
         include_once('modules/cbMap/cbMap.php');
         $focust = new cbMap();
         $focust->column_fields['assigned_user_id'] = 1;
         $focust->column_fields['mapname'] = $mapname;
         $focust->column_fields['content']=add_content($decodedata);
         $focust->column_fields['maptype'] =$MapType;
         $focust->column_fields['description']= add_description($decodedata);
         $focust->column_fields['mvqueryid']=$idquery;
         $focust->column_fields['targetname'] =preg_replace('/\s+/', '',$decodedata[0]['FirstModuleval']);
         $log->debug(" we inicialize value for insert in database ");

         if (!$focust->saveentity("cbMap"))//
          {

              if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0) {
                     echo save_history(add_aray_for_history($decodedata),$idquery,add_content($decodedata)).",".$focust->id;
                 } 
                 else{
                    echo "0,0";
                     $log->debug("Error!! MIssing the history Table");
                 }  
                        
          } else 
          {
             //echo focus->id;
             echo "Error!! something went wrong";
             $log->debug("Error!! something went wrong");
          }       
    }else
    {
         include_once('modules/cbMap/cbMap.php');
         $focust = new cbMap();
         $focust->id = $MapID[1];
         $focust->retrieve_entity_info($MapID[1],"cbMap");
         $focust->column_fields['assigned_user_id'] = 1;
         // $focust->column_fields['mapname'] = $MapName;
         $focust->column_fields['content']=add_content($decodedata);
         $focust->column_fields['targetname'] =preg_replace('/\s+/', '',$decodedata[0]['FirstModuleval']);
         // $focust->column_fields['maptype'] ="MasterDetailLayout";
         $focust->column_fields['mvqueryid']=$idquery;
         $focust->column_fields['description']= add_description($decodedata);
         $focust->mode = "edit";
         $focust->save("cbMap");

        if (Check_table_if_exist(TypeOFErrors::Tabele_name)>0){
                 echo save_history(add_aray_for_history($decodedata),$idquery,add_content($decodedata)).",".$MapID[1];

        } 
        else{
                echo "0,0";
                 $log->debug("Error!! MIssing the history Table");
        }  
                        
    }       
       
}//end of if (! empty($Data)) 

function add_content($DataDecode)
{
     //$DataDecode = json_decode($dat, true);
     $countarray=(count($DataDecode)-1);
     $xml = new DOMDocument("1.0");
     $root = $xml->createElement("map");
     $xml->appendChild($root);
     //$name = $xml->createElement("name");
     $target = $xml->createElement("originmodule");
     $targetid = $xml->createElement("originid");
     $targetidText = $xml->createTextNode("");
     $targetid->appendChild($targetidText);
     $targetname = $xml->createElement("originname");
     $targetnameText = $xml->createTextNode( trim(preg_replace('/\s*\([^)]*\)/', '',preg_replace("(many)",'', preg_replace('/\s+/', '', explode(";",  $DataDecode[0]['SecondModuleval'])[0])))));
     $targetname->appendChild($targetnameText);
     $target->appendChild($targetid);
     $target->appendChild($targetname);
     
     $origin = $xml->createElement("targetmodule");
     $originid = $xml->createElement("targetid");
     $originText = $xml->createTextNode("");
     $originid->appendChild($originText);
     $originname = $xml->createElement("targetname");
     $originnameText = $xml->createTextNode(preg_replace('/\s+/', '',$DataDecode[0]['FirstModuleval']));
     $originname->appendChild($originnameText);
     $origin->appendChild($originid);
     $origin->appendChild($originname);
     $fields = $xml->createElement("fields");
    // $hw=0;
     for($i=0;$i<=$countarray;$i++){
         //get target field name
       
                 $field = $xml->createElement("field");
                 $fieldname = $xml->createElement("fieldname");
                 if (preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['FirstFieldval'])[1])==="smownerid") {
                   $fieldnameText = $xml->createTextNode(preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['FirstFieldval'])[2]));    
                 }else{
                  $fieldnameText = $xml->createTextNode( preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['FirstFieldval'])[1]));   
                 }
                 
                 $fieldname->appendChild($fieldnameText);
                 $field->appendChild($fieldname);
                 
                 $fieldID = $xml->createElement("fieldID");
                 $fieldideText = $xml->createTextNode("");
                 $fieldID->appendChild($fieldideText);         
                 $field->appendChild($fieldID);
                $secondmoduless=trim(preg_replace('/\s+/','',$DataDecode[$i]['SecondModuleval']));//SecondModuleval

                LogFileSimple("secondmoduless var ----------".$DataDecode[$i]['SecondModuleval']);

                $relationModule=$DataDecode[$i]['SecondFieldOptionGrup'];
                LogFileSimple("relationModule var ----------".$DataDecode[$i]['SecondModuleval']);
                 if (strpos($DataDecode[$i]['SecondFieldtext'], 'Default value') !== false)
                     {
                         $value = $xml->createElement("value");
                         $valueText = $xml->createTextNode($DataDecode[$i]['SecondFieldval']);
                         $value->appendChild($valueText);
                         $field->appendChild($value);
                     }         
                     //target module fields
                     $Orgfields = $xml->createElement("Orgfields");
                     $field->appendChild($Orgfields);                                    
                 if ((!empty($secondmoduless) && !empty($relationModule))&& $secondmoduless !=$relationModule && $DataDecode[$i]['SecondFieldtext']!="Default-Value" ) {

                     // $Orgfields = $xml->createElement("Orgfields");
                     // $field->appendChild($Orgfields);
                      LogFileSimple("if is ok var ----------");
                     $Relfield= $xml->createElement("Relfield");
                         
                     $RelfieldName = $xml->createElement("RelfieldName");
                     $RelfieldNameText= $xml->createTextNode(preg_replace('/\s+/','', explode(":",$DataDecode[$i]['SecondFieldval'])[2]));
                     $RelfieldName->appendChild($RelfieldNameText);
                     $Relfield->appendChild($RelfieldName);
                     
                     $RelModule = $xml->createElement("RelModule");
                     if ($DataDecode[$i]['SecondFieldtext']=="Default-Value") {
                          $RelModuleText= $xml->createTextNode($relationModule);
                     }else{
                         $RelModuleText= $xml->createTextNode($relationModule);
                     }
                     $RelModuleText= $xml->createTextNode($relationModule);
                     $RelModule->appendChild($RelModuleText);
                     $Relfield->appendChild($RelModule);

                     $linkfield = $xml->createElement("linkfield");
                     $linkfieldText= $xml->createTextNode(findIdrelationAndName($secondmoduless,$relationModule));
                     $linkfield->appendChild($linkfieldText);
                     $Relfield->appendChild($linkfield);
                     
                     $Orgfields->appendChild($Relfield);

                     
                 } else {
                    
                     if (strpos($DataDecode[$i]['SecondFieldtext'], 'Default value') !== false)
                     {
                         $OrgRelfield= $xml->createElement("Orgfield");
                         
                         $OrgRelfieldName = $xml->createElement("OrgfieldName");
                         $OrgRelfieldNameText= $xml->createTextNode("");
                         $OrgRelfieldName->appendChild($OrgRelfieldNameText);
                         $OrgRelfield->appendChild($OrgRelfieldName); 
                        
                         $OrgfieldID = $xml->createElement("OrgfieldID");
                         $OrgfieldIDText= $xml->createTextNode("");
                         $OrgfieldID->appendChild($OrgfieldIDText);
                         $OrgRelfield->appendChild($OrgfieldID); 
                         
                         $Orgfields->appendChild($OrgRelfield);
                        
                     }else
                     {
                         $OrgRelfield= $xml->createElement("Orgfield");
                         
                         $OrgRelfieldName = $xml->createElement("OrgfieldName");
                         $OrgRelfieldNameText= $xml->createTextNode(preg_replace('/\s+/','', explode(":",$DataDecode[$i]['SecondFieldval'])[2]));
                         $OrgRelfieldName->appendChild($OrgRelfieldNameText);
                         $OrgRelfield->appendChild($OrgRelfieldName);
                         
                         $OrgfieldID = $xml->createElement("OrgfieldID");
                         $OrgfieldIDText= $xml->createTextNode("");
                         $OrgfieldID->appendChild($OrgfieldIDText);
                         $OrgRelfield->appendChild($OrgfieldID);
                         
                         $Orgfields->appendChild($OrgRelfield);
                     }
                 }
                 

                    
                 
                 
                 $del = $xml->createElement("delimiter");
                 $delText= $xml->createTextNode("--None--");
                 $del->appendChild($delText);
                 $Orgfields->appendChild($del);
                 $fields->appendChild($field);
         }
        
       
        
     
     //$root->appendChild($name);
     $root->appendChild($target);
     $root->appendChild($origin);
     $root->appendChild($fields);
     $xml->formatOutput = true;
    return $xml->saveXML();

}




function add_description($DataDecode){
    
    //$DataDecode = json_decode($datades, true);
    $countarray=(count($DataDecode)-1);
     
    $xml=new DOMDocument("1.0");
    $root=$xml->createElement("map");
    $xml->appendChild($root);
    //strt create the first module
    $Fmodule = $xml->createElement("Fmodule");

    $Fmoduleid = $xml->createElement("FmoduleID");
    $FmoduleText = $xml->createTextNode("");
    $Fmoduleid->appendChild($FmoduleText);
    
    $Fmodulename=$xml->createElement("Fmodulename");
    $FmodulenameText=$xml->createTextNode(preg_replace('/\s+/', '',$DataDecode[0]['FirstModuleval']));
    $Fmodulename->appendChild($FmodulenameText);

    $Fmodule->appendChild($Fmoduleid);
    $Fmodule->appendChild($Fmodulename);

    //second module
    $Secmodule = $xml->createElement("Secmodule");

    $Secmoduleid = $xml->createElement("SecmoduleID");
    $SecmoduleText = $xml->createTextNode(preg_replace('/\s+/','',explode(";",$DataDecode[0]['SecondModuleval'])[1]));
    $Secmoduleid->appendChild($SecmoduleText);     
    $Secmodulename=$xml->createElement("Secmodulename");     
    $SecmodulenameText=$xml->createTextNode( trim(preg_replace('/\s*\([^)]*\)/', '',preg_replace("(many)",'', preg_replace('/\s+/', '', explode(";",  $DataDecode[0]['SecondModuleval'])[0])))));     
    $Secmodulename->appendChild($SecmodulenameText);    
    $Secmodule->appendChild($Secmoduleid);
    $Secmodule->appendChild($Secmodulename);     
    $fields = $xml->createElement("fields");
    
    for ($i=0; $i <=$countarray ; $i++) { 
        
         $field = $xml->createElement("field");
         $fieldname = $xml->createElement("fieldID");
         if (preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['FirstFieldval'])[1])==="smownerid")
          {
            $fieldnameText = $xml->createTextNode(preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['FirstFieldval'])[2]));    
          }else
          {
              $fieldnameText = $xml->createTextNode( preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['FirstFieldval'])[1]));
          }

          $fieldname->appendChild($fieldnameText);
          $field->appendChild($fieldname);
          $fieldID = $xml->createElement("fieldname");
          $fieldideText = $xml->createTextNode($DataDecode[$i]['FirstFieldtxt']);
          $fieldID->appendChild($fieldideText);         
          $field->appendChild($fieldID);
          
          $field2 = $xml->createElement("field");
          if ($DataDecode[$i]['SecondFieldtext']=="Default-Value")
          {
             $Dfieldname = $xml->createElement("Value");
             $DfieldnameText = $xml->createTextNode($DataDecode[$i]['SecondFieldval']);
             $Dfieldname->appendChild($DfieldnameText);
             $field2->appendChild($Dfieldname);
             $DfieldID = $xml->createElement("fieldname");
             $DfieldideText = $xml->createTextNode($DataDecode[$i]['SecondFieldtext']);
             $DfieldID->appendChild($DfieldideText);         
             $field2->appendChild($DfieldID);
              
          }else
          {
                $Sfieldname = $xml->createElement("fieldID");
                if (preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['SecondFieldval'])[1])==="smownerid")
                {
                $SfieldnameText = $xml->createTextNode( preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['SecondFieldval'])[2]));    
                }else
                {
                  $SfieldnameText = $xml->createTextNode( preg_replace('/\s+/', '',explode(":",$DataDecode[$i]['SecondFieldval'])[1]));
                }

              $Sfieldname->appendChild($SfieldnameText);
              $field2->appendChild($Sfieldname);
              $SfieldID = $xml->createElement("fieldname");
              $SfieldideText = $xml->createTextNode($DataDecode[$i]['SecondFieldtext']);
              $SfieldID->appendChild($SfieldideText);         
              $field2->appendChild($SfieldID);
          }
          $fields->appendChild($field);
          $fields->appendChild($field2);

    }//end for

    //$root->appendChild($name);
     $root->appendChild($Fmodule);
     $root->appendChild($Secmodule);
     $root->appendChild($fields);
     $xml->formatOutput = true;
     return $xml->saveXML();   
}

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
             $adb->pquery("insert into ".TypeOFErrors::Tabele_name." values (?,?,?,?,?,?,?,?,?,?,?)",array($idquery2,$datas["FirstModuleval"],$datas["FirstModuletxt"],$datas["SecondModuletxt"],$datas["SecondModuleval"],$xmldata,$seq,1,"","",$datas["Labels"]));
              //return $idquery;
        }else 
        {

            $adb->pquery("insert into ".TypeOFErrors::Tabele_name." values (?,?,?,?,?,?,?,?,?,?,?)",array($idquery2,$datas["FirstModuleval"],$datas["FirstModuletxt"],$datas["SecondModuletxt"],$datas["SecondModuleval"],$xmldata,1,1,"","",$datas["Labels"]));
        }
        echo $idquery2;
}


function add_aray_for_history($decodedata)
 {
    $countarray=(count($decodedata)-1);
    for($i=0;$i<=$countarray;$i++) 
     {
        $labels.=$decodedata[0]['FirstModuletxt'].":".explode(":", $decodedata[$i]['FirstFieldval'])[0].":".explode(":", $decodedata[$i]['SecondFieldval'])[2].",".trim(preg_replace('/\s*\([^)]*\)/', '',preg_replace("(many)",'', preg_replace('/\s+/', '', explode(";",  $decodedata[0]['SecondModuleval'])[0])))).":".explode(":",$decodedata[$i]['SecondFieldval'])[0].":".explode(":",$decodedata[$i]['SecondFieldval'])[2].",";
     }
    return array
     (
        'Labels'=>$labels,
        'FirstModuleval'=>preg_replace('/\s+/', '',$decodedata[0]['FirstModuleval']),
        'FirstModuletxt'=>preg_replace('/\s+/', '',$decodedata[0]['FirstModuletxt']),
        'SecondModuleval'=>preg_replace('/\s+/', '',$decodedata[0]['SecondModuleval']),
        'SecondModuletxt'=>trim(preg_replace('/\s*\([^)]*\)/', '',preg_replace("(many)",'', preg_replace('/\s+/', '', explode(";",  $decodedata[0]['SecondModuleval'])[0]))))
     );
 }

function emptyStr($str) {
    return is_string($str) && strlen($str) === 0;
}




/**
 * [this function is to get from db the field id for 2 modul ]
 * @param  [String] $NameofModul [First modul name]
 * @param  [String] $RelModul    [second modul to fin the relation]
 * @return [String]              [return the field id for those two relation ]
 */
function findIdrelationAndName($NameofModul,$RelModul)
{
     global $adb;
     if (!empty($NameofModul) && !empty($RelModul))
     {  
            $q=$adb->query("select fm.fieldid,fi.* from vtiger_fieldmodulerel as fm JOIN vtiger_field as fi ON fm.fieldid=fi.fieldid WHERE module='$NameofModul' and relmodule='$RelModul'");
             return $adb->query_result($q,0,'fieldname');//$adb->query_result($q,0,'fieldid').":".
         
     } else {
         return "";
     }
     

}

