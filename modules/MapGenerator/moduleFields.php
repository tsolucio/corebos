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
global $log,$adb;
//require_once ('include/utils/utils.php');
//require_once('include/database/PearDatabase.php');
//require_once('database/DatabaseConnection.php');
//require_once ('include/CustomFieldUtil.php');
//require_once ('data/Tracker.php');

include 'XmlContent.php';

$FieldsArray=array();
if (isset($_REQUEST['MapId'])) {
    $mapid = $_REQUEST['MapId'];

    $FieldsArray = takeAllFileds($mapid);
    //echo "brenda kushtit mapID ".$mapid;
    function getModFields($module, $FieldsArrays=[])
    {
        global $log,$OPTION_SET;
        $log->debug("Entering getAdvSearchfields(" . $module . ") method ...");
        global $adb;
        global $current_user;
        global $mod_strings, $app_strings;
        $OPTION_SET.='<optgroup label="$module">';
        $tabid = getTabid($module, $dbname);
        if ($tabid == 9)
            $tabid = "9,16";


        $sql = "select * from  vtiger_field ";
        $sql .= " where vtiger_field.tabid in(?) and";
        $sql .= " vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2)";
        if ($tabid == 13 || $tabid == 15) {
            $sql .= " and vtiger_field.fieldlabel != 'Add Comment'";
        }
        if ($tabid == 14) {
            $sql .= " and vtiger_field.fieldlabel != 'Product Image'";
        }
        if ($tabid == 9 || $tabid == 16) {
            $sql .= " and vtiger_field.fieldname not in('notime','duration_minutes','duration_hours')";
        }
        if ($tabid == 4) {
            $sql .= " and vtiger_field.fieldlabel != 'Contact Image'";
        }
        if ($tabid == 13 || $tabid == 10) {
            $sql .= " and vtiger_field.fieldlabel != 'Attachment'";
        }
        $sql .= " group by vtiger_field.fieldlabel order by block,sequence";

        $params = array($tabid);


        $result = $adb->pquery($sql, $params);
        $noofrows = $adb->num_rows($result);
        $block = '';
        $select_flag = '';
        //echo "edmondi 2";
        for ($i = 0; $i < $noofrows; $i++) {
            $fieldtablename = $adb->query_result($result, $i, "tablename");
            $fieldcolname = $adb->query_result($result, $i, "columnname");
            $fieldname = $adb->query_result($result, $i, "fieldname");
            $block = $adb->query_result($result, $i, "block");
            $fieldtype = $adb->query_result($result, $i, "typeofdata");
            $fieldtype = explode("~", $fieldtype);
            $fieldtypeofdata = $fieldtype[0];
            if ($fieldcolname == 'account_id' || $fieldcolname == 'accountid' || $fieldcolname == 'product_id' || $fieldcolname == 'vendor_id' || $fieldcolname == 'contact_id' || $fieldcolname == 'contactid' || $fieldcolname == 'vendorid' || $fieldcolname == 'potentialid' || $fieldcolname == 'salesorderid' || $fieldcolname == 'quoteid' || $fieldcolname == 'parentid' || $fieldcolname == "recurringtype" || $fieldcolname == "campaignid" || $fieldcolname == "inventorymanager" || $fieldcolname == "currency_id")
                $fieldtypeofdata = "V";
            if ($fieldcolname == "discontinued" || $fieldcolname == "active")
                $fieldtypeofdata = "C";
            $fieldlabel = $mod_strings[$adb->query_result($result, $i, "fieldlabel")];

            // Added to display customfield label in search options
            if ($fieldlabel == "")
                $fieldlabel = $adb->query_result($result, $i, "fieldlabel");

            if ($fieldlabel == "Related To") {
                $fieldlabel = "Related to";
            }
            if ($fieldlabel == "Start Date & Time") {
                $fieldlabel = "Start Date";
                if ($module == 'Activities' && $block == 19)
                    $module_columnlist['vtiger_activity:time_start::Activities_Start Time:I'] = 'Start Time';

            }
            //$fieldlabel1 = str_replace(" ","_",$fieldlabel); // Is not used anywhere
            //Check added to search the lists by Inventory manager
            if ($fieldtablename == 'vtiger_quotes' && $fieldcolname == 'inventorymanager') {
                $fieldtablename = 'vtiger_usersQuotes';
                $fieldcolname = 'user_name';
            }
            if ($fieldtablename == 'vtiger_contactdetails' && $fieldcolname == 'reportsto') {
                $fieldtablename = 'vtiger_contactdetails2';
                $fieldcolname = 'lastname';
            }
            if ($fieldtablename == 'vtiger_notes' && $fieldcolname == 'folderid') {
                $fieldtablename = 'vtiger_attachmentsfolder';
                $fieldcolname = 'foldername';
            }
            if ($fieldlabel != 'Related to') {
                if ($i == 0)
                    $select_flag = "";

                $mod_fieldlabel = $mod_strings[$fieldlabel];
                if ($mod_fieldlabel == "") $mod_fieldlabel = $fieldlabel;

                if ($fieldlabel == "Product Code") {
                    $checkexist=false;
                    foreach ($FieldsArrays as $item) {//$item
                        if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                            $checkexist=true;
                            //$OPTION_SET .= "<option selected value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . "</option>";
                        } else {
                           // $checkexist=false;
                            //$OPTION_SET .= "<option value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . "</option>";
                        }
                    }
                    if ($checkexist) {
                        $OPTION_SET .= "<option selected value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . "</option>";
                    } else {
                        $OPTION_SET .= "<option value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . "</option>";
                    }

                }
                if ($fieldlabel == "Reports To") {
                    $checkexist=false;
                    foreach ($FieldsArrays as $item) {//$item

                        if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                            $checkexist=true;
                            //$OPTION_SET .= "<option selected value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . " - " . $mod_strings['LBL_LIST_LAST_NAME'] . "</option>";
                        } else {
                        // $checkexist=false;
                         //   $OPTION_SET .= "<option value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . " - " . $mod_strings['LBL_LIST_LAST_NAME'] . "</option>";
                        }
                    }
                    if ($checkexist) {
                        $OPTION_SET .= "<option selected value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . " - " . $mod_strings['LBL_LIST_LAST_NAME'] . "</option>";
                    } else {
                        $OPTION_SET .= "<option value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "'" . $select_flag . ">" . $mod_fieldlabel . " - " . $mod_strings['LBL_LIST_LAST_NAME'] . "</option>";
                    }
                } elseif ($fieldcolname == "contactid" || $fieldcolname == "contact_id") {
                    $checkexist=false;
                    foreach ($FieldsArrays as $item) {//$item
                        $checkexist=false;
                        if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                            $checkexist=true;
                            //$OPTION_SET .= "<option selected value='vtiger_contactdetails:lastname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $app_strings['LBL_CONTACT_LAST_NAME'] . "</option>";
                            //$OPTION_SET .= "<option selected value='vtiger_contactdetails:firstname:" . $fieldname . "::" . $fieldtypeofdata . "'>" . $app_strings['LBL_CONTACT_FIRST_NAME'] . "</option>";
                        } else {
                           // $OPTION_SET .= "<option value='vtiger_contactdetails:lastname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $app_strings['LBL_CONTACT_LAST_NAME'] . "</option>";
                           // $OPTION_SET .= "<option selected value='vtiger_contactdetails:firstname:" . $fieldname . "::" . $fieldtypeofdata . "'>" . $app_strings['LBL_CONTACT_FIRST_NAME'] . "</option>";
                        }
                    }
                    if ($checkexist) {
                        $OPTION_SET .= "<option selected value='vtiger_contactdetails:lastname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $app_strings['LBL_CONTACT_LAST_NAME'] . "</option>";
                        $OPTION_SET .= "<option selected value='vtiger_contactdetails:firstname:" . $fieldname . "::" . $fieldtypeofdata . "'>" . $app_strings['LBL_CONTACT_FIRST_NAME'] . "</option>";
                    } else {
                        $OPTION_SET .= "<option value='vtiger_contactdetails:lastname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $app_strings['LBL_CONTACT_LAST_NAME'] . "</option>";
                        $OPTION_SET .= "<option selected value='vtiger_contactdetails:firstname:" . $fieldname . "::" . $fieldtypeofdata . "'>" . $app_strings['LBL_CONTACT_FIRST_NAME'] . "</option>";
                    }


                } elseif ($fieldcolname == "campaignid") {
                    $checkexist=false;
                    foreach ($FieldsArrays as $item) {//$item

                        if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                            $checkexist=true;
                            //$OPTION_SET .= "<option selected value='vtiger_campaign:campaignname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $mod_fieldlabel . "</option>";
                        } else {
                           // $checkexist=false;
                            //$OPTION_SET .= "<option   value='vtiger_campaign:campaignname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $mod_fieldlabel . "</option>";
                        }
                    }
                    if ($checkexist) {
                       // $checkexist=true;
                        $OPTION_SET .= "<option selected value='vtiger_campaign:campaignname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $mod_fieldlabel . "</option>";
                    } else {
                        //$checkexist=false;
                        $OPTION_SET .= "<option   value='vtiger_campaign:campaignname:" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . $mod_fieldlabel . "</option>";
                    }
                } else {
                    $checkexist=false;
                    foreach ($FieldsArrays as $item) {//$item
                        if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                            $checkexist=true;
                            //$OPTION_SET .= "<option selected value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . str_replace("'", "`", $fieldlabel) . "</option>";
                        } else {
                           // $checkexist=false;
                            //$OPTION_SET .= "<option   value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . str_replace("'", "`", $fieldlabel) . "</option>";
                        }
                    }
                    if ($checkexist) {
                        $OPTION_SET .= "<option selected value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . str_replace("'", "`", $fieldlabel) . "</option>";
                    } else {
                        $OPTION_SET .= "<option   value='" . $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "' " . $select_flag . ">" . str_replace("'", "`", $fieldlabel) . "</option>";
                    }
                }

            }//end related to if
        }
        //Added to include Ticket ID in HelpDesk advance search
        if ($module == 'HelpDesk') {
            $mod_fieldlabel = $mod_strings['Ticket ID'];
            if ($mod_fieldlabel == "") $mod_fieldlabel = 'Ticket ID';
            $checkexist=false;
            foreach ($FieldsArrays as $item) {//$item
                if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                   $checkexist=true;
                    // $OPTION_SET .= "<option selected value=\'vtiger_crmentity:crmid:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
                } else {
                    //$OPTION_SET .= "<option value=\'vtiger_crmentity:crmid:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
                }
            }
            if ($checkexist) {
                $OPTION_SET .= "<option selected value=\'vtiger_crmentity:crmid:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
            } else {
                $OPTION_SET .= "<option value=\'vtiger_crmentity:crmid:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
            }
        }
        //Added to include activity type in activity advance search
        if ($module == 'Activities') {
            $mod_fieldlabel = $mod_strings['Activity Type'];
            if ($mod_fieldlabel == "") $mod_fieldlabel = 'Activity Type';
            $checkexist=false;
            foreach ($FieldsArrays as $item) {//$item
                if (strlen($item) != 0 && strpos($item, $fieldcolname) !== false) {
                    $checkexist=true;
                    //$OPTION_SET .= "<option selected value=\'vtiger_activity.activitytype:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
                } else {
                    //$OPTION_SET .= "<option value=\'vtiger_activity.activitytype:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
                }
            }
            if ($checkexist) {
                $OPTION_SET .= "<option selected value=\'vtiger_activity.activitytype:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
            } else {
                $OPTION_SET .= "<option value=\'vtiger_activity.activitytype:" . $fieldname . "::" . $fieldtypeofdata . "\'>" . $mod_fieldlabel . "</option>";
            }
        }
        $log->debug("Exiting getAdvSearchfields method ...");
        $OPTION_SET .= "</optgroup>";
         foreach ($FieldsArrays as $item) {
            echo "String 1".$item;
         }
        // return '';
    }
}
else{
   include('modfields.php');
}

$rec=$_REQUEST['installationID'];
/* $ai=$adb->query("select * from vtiger_accountinstallation join vtiger_crmentity on crmid=accountinstallationid
                  join vtiger_account on accountid=linktoacsd where accountinstallationid=$rec");
$dbname=$adb->query_result($ai,0,"dbname");
$acno=$adb->query_result($ai,0,"acin_no"); */
if(isset($_REQUEST['mod'])){
 $m = $_REQUEST['mod'];
 $module = $m;
// echo $module;
$result = $adb->pquery("Select * from  vtiger_entityname where modulename = ?",array($module));
$link = $adb->query_result($result,0,"entityidfield");
//echo $m;
//exit();

}
$a='<option value="" >(Select a field)</option>';
$a .=getModFields($module,$FieldsArray);

//if($m != ""){
//    if($m == "Potentials") $tbl = "vtiger_potential";
//    else if($m == "Accounts") $tbl = "vtiger_account";
//    else if($m == "Contacts") $tbl = "vtiger_contactdetails";
//    else $tbl = "vtiger_".strtolower($m);
//if($m == "Accounts")
//$query="SELECT fieldid, fieldlabel,columnname,tablename from  vtiger_field
//        join  vtiger_tab on  vtiger_field.tabid= vtiger_tab.tabid
//        where name='$m' and fieldname<>'createdtime'and fieldname<>'modifiedtime'";
//else {
//$query="SELECT fieldid, fieldlabel,columnname,tablename from  vtiger_field
//        where tablename='$tbl' and fieldname<>'createdtime'and fieldname<>'modifiedtime'";
//}
//$a.=' <optgroup label="'.$module.'">';
//$result = $adb->query($query);
//$num_rows=$adb->num_rows($result);
//    if($num_rows!=0){
//
//	for($i=1;$i<=$num_rows;$i++)
//	{
//		$f = getTranslatedString($adb->query_result($result,$i-1,'fieldlabel'));
//                $id = $adb->query_result($result,$i-1,'fieldid');
//                $columnname = $adb->query_result($result,$i-1,'columnname');
//                $tablename = $adb->query_result($result,$i-1,'tablename');
//                $f=str_replace("'","",utf8_encode(html_entity_decode($f)));
////                $a.='<li  value="'.$tablename.'.'.$columnname.'" class="ui-widget-content">'.$f.'</li>';
//                $a.='<option value="'.$module.'#'.$tablename.'.'.$columnname.'">'.$f.'</option>';
////                $a.="<tr height=\"35\"><td class='s'><input type=\"hidden\" value='$id' id='field$i' name='field$i'><input type='hidden' id=ui1$i' value=''><input type='hidden' id='td1$i' value=''>".$f."</td></tr>";
//}
//$a.='</optgroup>';
//}
//}
$a.=";".$num_rows.";".$link.";";
echo $a;

?>
