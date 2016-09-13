<?php

//ini_set('display_errors', 'on');
require_once("config.inc.php");
include_once('data/CRMEntity.php');
include_once('modules/cbMap/cbMap.php');
require_once('include/utils/utils.php');
require_once('include/database/PearDatabase.php');

global $adb, $log, $root_directory, $current_user;
$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(1);
if (isset($argv) && !empty($argv)) {
    $csvfile = $argv[1];
    $mapid = $argv[2];
}
$csvfile=explode("=",$csvfile);
$mp=explode("=",$mapid);
$mapid=$mp[1];
$filename = "import/$csvfile[1]";
$table = pathinfo($filename);

$tb=explode("=",$table['filename']);
$table = "massivelauncher_" . $tb[0];
$drop = "drop table if exists $table;";
$adb->query($drop);
$delimiter = ',';

$fp = fopen($filename, 'r');
$frow = fgetcsv($fp, 1000, $delimiter);

$allHeaders = implode(",", $frow);
$columns = "`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `selected` varchar(3) ";
foreach ($frow as $column) {
if($column=='') $column='lastvalue';
    $columns .= ", `$column` varchar(250)";
}
$create = "create table if not exists $table ($columns);";
$adb->query($create);

$file = $root_directory . $filename;
$irow=0;
while (($data = fgetcsv($fp, 1000, $delimiter)) !== FALSE) {
        $row_vals=implode("','",$data);
        $str="INSERT INTO $table  VALUES ('','','$row_vals')";
        echo $str;
        $adb->query($str);//break;
    $irow++;
    }
$mapfocus = CRMEntity::getInstance("cbMap");
$mapfocus->retrieve_entity_info($mapid, "cbMap");
$mapinfo = $mapfocus->readImportType();
$updateFld = $mapinfo['target'];
$matchFld = $mapinfo['match'];
$options = $mapinfo['options'];

$module = $mapfocus->getMapTargetModule();
include_once("modules/$module/$module.php");

$focus = CRMEntity::getInstance($module);
$customfld = $focus->customFieldTable;

$header = NULL;
$data = array();
$dataQuery = $adb->query("SELECT * FROM $table");
while ($dataQuery && $data = $adb->fetch_array($dataQuery)) {
    $id = $data['id'];
$nn++;
    $index_q = "SELECT $focus->table_name.$focus->table_index
            FROM $focus->table_name
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$focus->table_name.$focus->table_index
            INNER JOIN $customfld[0] ON $customfld[0].$customfld[1]=$focus->table_name.$focus->table_index
            WHERE vtiger_crmentity.deleted=0 ";
    foreach ($matchFld as $k => $v) {
        $params[] = $data[$v];
        $index_q.=" AND $k LIKE '" . $data[$v] . "' ";
    }
    $params = array();
    $index_query = $adb->pquery($index_q, $params);
    $nr_rows = $adb->num_rows($index_query);
    if ($nr_rows>0) {
        $allids = array();
        if ($options['update'] == 'FIRST') {
            $allids[] = $adb->query_result($index_query, 0, $focus->table_index);
        } elseif ($options['update'] == 'LAST') {
            $allids[] = $adb->query_result($index_query, $nr_rows - 1, $focus->table_index);
        }
        if ($options['update'] == 'ALL') {
            for ($i = 0; $i < $nr_rows; $i++) {
                $allids[] = $adb->query_result($index_query, $i, $focus->table_index);
            }
        } 
        for ($el = 0; $el < count($allids); $el++) {
            $index_result = $adb->query_result($index_query, $el, $focus->table_index);
              if($nr_rows>0)  $focus->retrieve_entity_info($index_result, $module);
                 foreach ($updateFld as $upkey => $upVal) {
                    $predefined = $upVal['predefined'];
                    $value = $upVal['value'];
                    if ($predefined == 'AUTONUM')
                        $focus->column_fields[$upkey] = $el;
                    else if(isset($upVal['relatedFields']) && !empty($upVal['relatedFields'])) {
                            $relInformation = $upVal['relatedFields'][0];
                            $relModule = $relInformation['relmodule'];
                            $linkField = $relInformation['linkfield'];
                            $fieldName = $relInformation['fieldname'];
                            $otherid = $data[$linkField];

                            if (!empty($otherid)) {
                                include_once "modules/$relModule/$relModule.php";
                                $otherModule = CRMEntity::getInstance($relModule);
                                $customfld1 = $otherModule->customFieldTable;
                                $index_rel = $adb->query("SELECT $otherModule->table_name.$otherModule->table_index
                                FROM $otherModule->table_name
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$otherModule->table_name.$otherModule->table_index
                                INNER JOIN $customfld1[0] ON $customfld1[0].$customfld1[1]=$otherModule->table_name.$otherModule->table_index
                                WHERE vtiger_crmentity.deleted=0 and $fieldName='$otherid'");
                                $focus->column_fields[$upkey] =$adb->query_result($index_rel,0);
                                             
                        }
                    }
                    elseif (!empty($data[$value]))
                        $focus->column_fields[$upkey] = $data[$value];
                    else
                        $focus->column_fields[$upkey] = $predefined;
                }
                $focus->mode = 'edit';
                $focus->id = $index_result;

                $focus->saveentity($module);  $k1++;
                if (!empty($focus->id)) {
                    $adb->pquery("UPDATE $table SET selected=1 WHERE id=?", array($id));
                } 
        }
    }
    else {$focus1=new $module();
         foreach ($updateFld as $upkey => $upVal) {
                    $predefined = $upVal['predefined'];
                    $value = $upVal['value'];
                         if(isset($upVal['relatedFields']) && !empty($upVal['relatedFields'])) {
                            $relInformation = $upVal['relatedFields'][0];
                            $relModule = $relInformation['relmodule'];
                            $linkField = $relInformation['linkfield'];
                            $fieldName = $relInformation['fieldname'];
                            $otherid = $data[$linkField];

                            if (!empty($otherid)) {
                                include_once "modules/$relModule/$relModule.php";
                                $otherModule = CRMEntity::getInstance($relModule);
                                $customfld1 = $otherModule->customFieldTable;
                                $index_rel = $adb->query("SELECT $otherModule->table_name.$otherModule->table_index
                                FROM $otherModule->table_name
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$otherModule->table_name.$otherModule->table_index
                                INNER JOIN $customfld1[0] ON $customfld1[0].$customfld1[1]=$otherModule->table_name.$otherModule->table_index
                                WHERE vtiger_crmentity.deleted=0 and $fieldName='$otherid'");
                                $focus1->column_fields[$upkey] =$adb->query_result($index_rel,0);
                                             
                        }
                        }
                        elseif (!empty($data[$value]))
                        $focus1->column_fields[$upkey] = $data[$value];
                        else
                        $focus1->column_fields[$upkey] = $predefined;
                }
                $focus1->column_fields["assigned_user_id"]=1;
                $focus1->saveentity($module); $r++;
                if (!empty($focus1->id)) {
                $adb->pquery("UPDATE $table SET selected=1 WHERE id=?", array($id));   
    }
    
       }
}

echo "Import was lauched successfully here";
?>
