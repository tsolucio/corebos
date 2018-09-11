
<?php 
/*
 * @Author: Edmond Kacaj 
 * @Date: 2018-09-11 11:03:04 
 * @Last Modified by: edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 14:55:28
 */

function GetModulRel($m)
{
    global $log, $mod_strings,$adb;
    $j = 0;
    $result = $adb->pquery("SELECT relmodule,columnname,fieldlabel 
            from vtiger_fieldmodulerel join vtiger_field 
            on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid 
            where module= ? and relmodule<>'Faq' and relmodule<>'Emails' and relmodule<>'Events'
            and relmodule<>'Webmails' and relmodule<>'SMSNotifier'
            and relmodule<>'PBXManager' and relmodule<>'Modcomments' and relmodule<>'Calendar' 
            and relmodule in (select name from vtiger_tab where presence=0)", array($m));

    $num_rows = $adb->num_rows($result);
    if ($num_rows != 0) {
        for ($i = 1; $i <= $num_rows; $i++) {

            $modul1 = $adb->query_result($result, $i - 1, 'relmodule');
            $log->debug("Fillim$i" . $modul1);
            $column = $adb->query_result($result, $i - 1, 'columnname');
            $fl = $adb->query_result($result, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                $a .= '<option selected value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                $a .= '<option value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
           // echo $modul1;
        }
    }
    $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
             and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
             and relmodule in (select name from  vtiger_tab where presence=0) 
             and module in (select name from  vtiger_tab where presence=0)";


    $result1 = $adb->query($query1);
    $num_rows1 = $adb->num_rows($result1);
    if ($num_rows1 != 0) {
        for ($i = 1; $i <= $num_rows1; $i++) {
            $modul1 = $adb->query_result($result1, $i - 1, 'module');
            $column = $adb->query_result($result1, $i - 1, 'columnname');
            $fl = $adb->query_result($result1, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                $a .= '<option selected value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                $a .= '<option value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
        }
    }
    $query2 = "SELECT uitype, columnname, fieldlabel from  vtiger_field 
             join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
             where (uitype=76 or uitype=50 or uitype=51 or uitype=57 or uitype=58 or uitype=59 or uitype=73 or uitype=75 or  uitype=78
             or  uitype=80 or uitype=81 or uitype=68) and name='$m' and  vtiger_tab.presence=0";

    $result2 = $adb->query($query2);
    $num_rows2 = $adb->num_rows($result2);
    if ($num_rows2 != 0) {
        for ($i = 1; $i <= $num_rows2; $i++) {
            $ui = $adb->query_result($result2, $i - 1, 'uitype');
            $column = $adb->query_result($result2, $i - 1, 'columnname');
            $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');

            if ($ui == 51 || $ui == 50 || $ui == 73 || $ui == 68) {
                $modul1 = "Accounts";
                if ($ui == 68) $modul2 = 'Contacts';
            } else if ($ui == 57) {
                $modul1 = "Contacts";
                $modul2 = '';
            } else if ($ui == 59) {
                $modul1 = "Products";
                $modul2 = '';

            } else if ($ui == 58) {
                $modul1 = "Campaigns";
                $modul2 = '';

            } else if ($ui == 76) {
                $modul1 = "Potentials";
                $modul2 = '';

            } else if ($ui == 75 || $ui = 81) {
                $modul1 = "Vendors";
                $modul2 = '';
            } else if ($ui == 78) {
                $modul1 = "Quotes";
                $modul2 = '';
            } else if ($ui == 80) {
                $modul1 = "SalesOrder";
                $modul2 = '';
            }
            $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
            if ($modul2 != '') {
                $mo2 = $adb->query("select * from  vtiger_tab where name='$modul2' and presence=0");
            }
            if ($adb->num_rows($mo) != 0) {
                if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                    $a .= '<option selected value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                } else {
                    $a .= '<option value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                }
            }
            if ($modul2 != '' && $adb->num_rows($mo2) != 0)
                if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul2) {
                    $a .= '<option selected value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
                else {
                    $a .= '<option value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
            }

    }

    if ($m == "Accounts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field
                join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where (uitype=73 or uitype=50
                or uitype=51 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events'
                and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' 
                and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Contacts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field 
                  join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
                  where (uitype=57 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' 
                  and name<>'Webmails' and name<>'SMSNotifier'and name<>'PBXManager' and name<>'Modcomments' 
                  and name<>'Calendar' and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Produts") {
        $query2 = "SELECT columnname,name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=59 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select *  from vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "Campaigns") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=58 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0
    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Potentials") {
        $query2 = "SELECT columnname, name ,fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=76 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and   vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Quotes") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=78
        and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "SalesOrder") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=81 and uitype=75 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';


            }
        }
    }
    if ($m == "Vendors") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=80 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0) {
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected="selected" value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else{
                        $a .= '<option  value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
            }
        }
    }
    return $a;
}

// 
 /**
  * [GetAllRelationMOdul this function is for find the relatio0n without tag option 
  * @param [type] $m  Modul name
  */


function GetAllRelationMOdul($m){
    global $log, $mod_strings,$adb;
    $j = 0;
    $returnarray=array();
    $result = $adb->pquery("SELECT relmodule,columnname,fieldlabel 
            from vtiger_fieldmodulerel join vtiger_field 
            on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid 
            where module= ? and relmodule<>'Faq' and relmodule<>'Emails' and relmodule<>'Events'
            and relmodule<>'Webmails' and relmodule<>'SMSNotifier'
            and relmodule<>'PBXManager' and relmodule<>'Modcomments' and relmodule<>'Calendar' 
            and relmodule in (select name from vtiger_tab where presence=0)", array($m));

    $num_rows = $adb->num_rows($result);
    if ($num_rows != 0) {
        for ($i = 1; $i <= $num_rows; $i++) {

            $modul1 = $adb->query_result($result, $i - 1, 'relmodule');
            $log->debug("Fillim$i" . $modul1);
            $column = $adb->query_result($result, $i - 1, 'columnname');
            $fl = $adb->query_result($result, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                $a = $modul1 . ';' . $column;
                array_push($returnarray,$a);
            } else {
                $a = $modul1 . ';' . $column;
                array_push($returnarray,$a);
            }
           // echo $modul1;
        }
    }
    $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
             and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
             and relmodule in (select name from  vtiger_tab where presence=0) 
             and module in (select name from  vtiger_tab where presence=0)";


    $result1 = $adb->query($query1);
    $num_rows1 = $adb->num_rows($result1);
    if ($num_rows1 != 0) {
        for ($i = 1; $i <= $num_rows1; $i++) {
            $modul1 = $adb->query_result($result1, $i - 1, 'module');
            $column = $adb->query_result($result1, $i - 1, 'columnname');
            $fl = $adb->query_result($result1, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                $a = $modul1 . ';' . $column;
                array_push($returnarray,$a);
            } else {
                $a = $modul1 . ';' . $column ;
                array_push($returnarray,$a);
            }
        }
    }
    $query2 = "SELECT uitype, columnname, fieldlabel from  vtiger_field 
             join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
             where (uitype=76 or uitype=50 or uitype=51 or uitype=57 or uitype=58 or uitype=59 or uitype=73 or uitype=75 or  uitype=78
             or  uitype=80 or uitype=81 or uitype=68) and name='$m' and  vtiger_tab.presence=0";

    $result2 = $adb->query($query2);
    $num_rows2 = $adb->num_rows($result2);
    if ($num_rows2 != 0) {
        for ($i = 1; $i <= $num_rows2; $i++) {
            $ui = $adb->query_result($result2, $i - 1, 'uitype');
            $column = $adb->query_result($result2, $i - 1, 'columnname');
            $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');

            if ($ui == 51 || $ui == 50 || $ui == 73 || $ui == 68) {
                $modul1 = "Accounts";
                if ($ui == 68) $modul2 = 'Contacts';
            } else if ($ui == 57) {
                $modul1 = "Contacts";
                $modul2 = '';
            } else if ($ui == 59) {
                $modul1 = "Products";
                $modul2 = '';

            } else if ($ui == 58) {
                $modul1 = "Campaigns";
                $modul2 = '';

            } else if ($ui == 76) {
                $modul1 = "Potentials";
                $modul2 = '';

            } else if ($ui == 75 || $ui = 81) {
                $modul1 = "Vendors";
                $modul2 = '';
            } else if ($ui == 78) {
                $modul1 = "Quotes";
                $modul2 = '';
            } else if ($ui == 80) {
                $modul1 = "SalesOrder";
                $modul2 = '';
            }
            $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
            if ($modul2 != '') {
                $mo2 = $adb->query("select * from  vtiger_tab where name='$modul2' and presence=0");
            }
            if ($adb->num_rows($mo) != 0) {
                if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                    $a =  $modul1 . '; ' . $column;
                    array_push($returnarray,$a);
                } else {
                    $a = $modul1 . '; ' . $column;
                    array_push($returnarray,$a);
                }
            }
            if ($modul2 != '' && $adb->num_rows($mo2) != 0)
                if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul2) {
                    $a=  $modul2 . '; ' . $column;
                    array_push($returnarray,$a);
                }
                else {
                    $a = $modul2 . '; ' . $column;
                    array_push($returnarray,$a);
                }
            }

    }

    if ($m == "Accounts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field
                join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where (uitype=73 or uitype=50
                or uitype=51 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events'
                and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' 
                and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a = $modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else {
                        $a = $modul1 . ';' . $column ;
                        array_push($returnarray,$a);
                    }
                }
        }
    }
    if ($m == "Contacts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field 
                  join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
                  where (uitype=57 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' 
                  and name<>'Webmails' and name<>'SMSNotifier'and name<>'PBXManager' and name<>'Modcomments' 
                  and name<>'Calendar' and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else {
                        $a = $modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                }
        }
    }
    if ($m == "Produts") {
        $query2 = "SELECT columnname,name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=59 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select *  from vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }

            }
        }
    }
    if ($m == "Campaigns") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=58 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0
    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
            }
        }
    }
    if ($m == "Potentials") {
        $query2 = "SELECT columnname, name ,fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=76 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and   vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a = $modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
            }
        }
    }
    if ($m == "Quotes") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=78
        and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a =  $modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }

            }
        }
    }
    if ($m == "SalesOrder") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=81 and uitype=75 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    $a = $modul1 . ';' . $column;
                array_push($returnarray,$a);


            }
        }
    }
    if ($m == "Vendors") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=80 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0) {
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                    else{
                        $a =$modul1 . ';' . $column;
                        array_push($returnarray,$a);
                    }
                }
            }
        }
    }
    return $returnarray;
}







// function get all relation module only relation one to multi
function GetModulRelOneTomulti($m,$valuefromLoad="")
   {
    global $log, $mod_strings,$adb;
    $j = 0;
    $result = $adb->pquery("SELECT relmodule,columnname,fieldlabel 
            from vtiger_fieldmodulerel join vtiger_field 
            on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid 
            where module= ? and relmodule<>'Faq' and relmodule<>'Emails' and relmodule<>'Events'
            and relmodule<>'Webmails' and relmodule<>'SMSNotifier'
            and relmodule<>'PBXManager' and relmodule<>'Modcomments' and relmodule<>'Calendar' 
            and relmodule in (select name from vtiger_tab where presence=0)", array($m));

    $num_rows = $adb->num_rows($result);
    if ($num_rows != 0) {
        for ($i = 1; $i <= $num_rows; $i++) {

            $modul1 = $adb->query_result($result, $i - 1, 'relmodule');
            $log->debug("Fillim$i" . $modul1);
            $column = $adb->query_result($result, $i - 1, 'columnname');
            $fl = $adb->query_result($result, $i - 1, 'fieldlabel');
            if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                $a .= '<option selected value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                $a .= '<option value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
           // echo $modul1;
        }
    }
    $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
             and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
             and relmodule in (select name from  vtiger_tab where presence=0) 
             and module in (select name from  vtiger_tab where presence=0)";


    $result1 = $adb->query($query1);
    $num_rows1 = $adb->num_rows($result1);
    if ($num_rows1 != 0) {
        for ($i = 1; $i <= $num_rows1; $i++) {
            $modul1 = $adb->query_result($result1, $i - 1, 'module');
            $column = $adb->query_result($result1, $i - 1, 'columnname');
            $fl = $adb->query_result($result1, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
               // $a .= '<option selected value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                //$a .= '<option value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
        }
    }
    $query2 = "SELECT uitype, columnname, fieldlabel from  vtiger_field 
             join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
             where (uitype=15) and name='$m' and  vtiger_tab.presence=0";

    $result2 = $adb->query($query2);
    $num_rows2 = $adb->num_rows($result2);
    if ($num_rows2 != 0) {
        for ($i = 1; $i <= $num_rows2; $i++) {
            $ui = $adb->query_result($result2, $i - 1, 'uitype');
            $column = $adb->query_result($result2, $i - 1, 'columnname');
            $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');

            if ($ui == 51 || $ui == 50 || $ui == 73 || $ui == 68) {
                $modul1 = "Accounts";
                if ($ui == 68) $modul2 = 'Contacts';
            } else if ($ui == 57) {
                $modul1 = "Contacts";
                $modul2 = '';
            } else if ($ui == 59) {
                $modul1 = "Products";
                $modul2 = '';

            } else if ($ui == 58) {
                $modul1 = "Campaigns";
                $modul2 = '';

            } else if ($ui == 76) {
                $modul1 = "Potentials";
                $modul2 = '';

            } else if ($ui == 75 || $ui = 81) {
                $modul1 = "Vendors";
                $modul2 = '';
            } else if ($ui == 78) {
                $modul1 = "Quotes";
                $modul2 = '';
            } else if ($ui == 80) {
                $modul1 = "SalesOrder";
                $modul2 = '';
            }
            $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
            if ($modul2 != '') {
                $mo2 = $adb->query("select * from  vtiger_tab where name='$modul2' and presence=0");
            }
            if ($adb->num_rows($mo) != 0) {
                if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                    $a .= '<option selected value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                } else {
                    $a .= '<option value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                }
            }
            if ($modul2 != '' && $adb->num_rows($mo2) != 0)
                if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul2) {
                    $a .= '<option selected value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
                else {
                    $a .= '<option value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
            }

    }

    if ($m == "Accounts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field
                join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where (uitype=73 or uitype=50
                or uitype=51 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events'
                and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' 
                and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       // $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                       // $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Contacts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field 
                  join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
                  where (uitype=57 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' 
                  and name<>'Webmails' and name<>'SMSNotifier'and name<>'PBXManager' and name<>'Modcomments' 
                  and name<>'Calendar' and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                        //$a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                       // $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Produts") {
        $query2 = "SELECT columnname,name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=59 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select *  from vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       // $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        //$a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "Campaigns") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=58 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0
    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       // $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        //$a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Potentials") {
        $query2 = "SELECT columnname, name ,fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=76 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and   vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       // $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        //$a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Quotes") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=78
        and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       // $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        //$a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "SalesOrder") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=81 and uitype=75 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';


            }
        }
    }
    if ($m == "Vendors") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=80 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0) {
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       // $a .= '<option selected="selected" value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else{
                       // $a .= '<option  value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
            }
        }
    }
    return $a;
}






 
// function get all relation module 
function GetModulToAll($m,$valuefromLoad="")
   {
    global $log, $mod_strings,$adb;
    $j = 0;
    $result = $adb->pquery("SELECT relmodule,columnname,fieldlabel 
            from vtiger_fieldmodulerel join vtiger_field 
            on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid 
            where module= ? and relmodule<>'Faq' and relmodule<>'Emails' and relmodule<>'Events'
            and relmodule<>'Webmails' and relmodule<>'SMSNotifier'
            and relmodule<>'PBXManager' and relmodule<>'Modcomments' and relmodule<>'Calendar' 
            and relmodule in (select name from vtiger_tab where presence=0)", array($m));

    $num_rows = $adb->num_rows($result);
    if ($num_rows != 0) {
        for ($i = 1; $i <= $num_rows; $i++) {

            $modul1 = $adb->query_result($result, $i - 1, 'relmodule');
            $log->debug("Fillim$i" . $modul1);
            $column = $adb->query_result($result, $i - 1, 'columnname');
            $fl = $adb->query_result($result, $i - 1, 'fieldlabel');
            if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                $a .= '<option selected value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                $a .= '<option value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
           // echo $modul1;
        }
    }
    $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
             and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
             and relmodule in (select name from  vtiger_tab where presence=0) 
             and module in (select name from  vtiger_tab where presence=0)";


    $result1 = $adb->query($query1);
    $num_rows1 = $adb->num_rows($result1);
    if ($num_rows1 != 0) {
        for ($i = 1; $i <= $num_rows1; $i++) {
            $modul1 = $adb->query_result($result1, $i - 1, 'module');
            $column = $adb->query_result($result1, $i - 1, 'columnname');
            $fl = $adb->query_result($result1, $i - 1, 'fieldlabel');
            if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
               $a .= '<option selected value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                $a .= '<option value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
        }
    }
    $query2 = "SELECT uitype, columnname, fieldlabel from  vtiger_field 
             join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
             where (uitype=76 or uitype=50 or uitype=51 or uitype=57 or uitype=58 or uitype=59 or uitype=73 or uitype=75 or  uitype=78
             or  uitype=80 or uitype=81 or uitype=68) and name='$m' and  vtiger_tab.presence=0";

    $result2 = $adb->query($query2);
    $num_rows2 = $adb->num_rows($result2);
    if ($num_rows2 != 0) {
        for ($i = 1; $i <= $num_rows2; $i++) {
            $ui = $adb->query_result($result2, $i - 1, 'uitype');
            $column = $adb->query_result($result2, $i - 1, 'columnname');
            $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');

            if ($ui == 51 || $ui == 50 || $ui == 73 || $ui == 68) {
                $modul1 = "Accounts";
                if ($ui == 68) $modul2 = 'Contacts';
            } else if ($ui == 57) {
                $modul1 = "Contacts";
                $modul2 = '';
            } else if ($ui == 59) {
                $modul1 = "Products";
                $modul2 = '';

            } else if ($ui == 58) {
                $modul1 = "Campaigns";
                $modul2 = '';

            } else if ($ui == 76) {
                $modul1 = "Potentials";
                $modul2 = '';

            } else if ($ui == 75 || $ui = 81) {
                $modul1 = "Vendors";
                $modul2 = '';
            } else if ($ui == 78) {
                $modul1 = "Quotes";
                $modul2 = '';
            } else if ($ui == 80) {
                $modul1 = "SalesOrder";
                $modul2 = '';
            }
            $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
            if ($modul2 != '') {
                $mo2 = $adb->query("select * from  vtiger_tab where name='$modul2' and presence=0");
            }
            if ($adb->num_rows($mo) != 0) {
                if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                    $a .= '<option selected value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                } else {
                    $a .= '<option value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                }
            }
            if ($modul2 != '' && $adb->num_rows($mo2) != 0)
                if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul2) {
                    $a .= '<option selected value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
                else {
                    $a .= '<option value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
            }

    }

    if ($m == "Accounts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field
                join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where (uitype=73 or uitype=50
                or uitype=51 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events'
                and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' 
                and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                       $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Contacts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field 
                  join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
                  where (uitype=57 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' 
                  and name<>'Webmails' and name<>'SMSNotifier'and name<>'PBXManager' and name<>'Modcomments' 
                  and name<>'Calendar' and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                       $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Produts") {
        $query2 = "SELECT columnname,name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=59 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select *  from vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "Campaigns") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=58 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0
    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Potentials") {
        $query2 = "SELECT columnname, name ,fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=76 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and   vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Quotes") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=78
        and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "SalesOrder") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=81 and uitype=75 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';


            }
        }
    }
    if ($m == "Vendors") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=80 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0) {
                    if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                       $a .= '<option selected="selected" value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else{
                       $a .= '<option  value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
            }
        }
    }
    return $a;
}




/**
 * [GetModulRelOneTomultiTextVal description]
 * @param [type] $m             [description]
 * @param string $valuefromLoad [description]
 */
function GetModulRelOneTomultiTextVal($m,$valuefromLoad="")
   {
    global $log, $mod_strings,$adb;
    $j = 0;
    $result = $adb->pquery("SELECT relmodule,columnname,fieldlabel 
            from vtiger_fieldmodulerel join vtiger_field 
            on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid 
            where module= ? and relmodule<>'Faq' and relmodule<>'Emails' and relmodule<>'Events'
            and relmodule<>'Webmails' and relmodule<>'SMSNotifier'
            and relmodule<>'PBXManager' and relmodule<>'Modcomments' and relmodule<>'Calendar' 
            and relmodule in (select name from vtiger_tab where presence=0)", array($m));

    $num_rows = $adb->num_rows($result);
    if ($num_rows != 0) {
        for ($i = 1; $i <= $num_rows; $i++) {

            $modul1 = $adb->query_result($result, $i - 1, 'relmodule');
            $log->debug("Fillim$i" . $modul1);
            $column = $adb->query_result($result, $i - 1, 'columnname');
            $fl = $adb->query_result($result, $i - 1, 'fieldlabel');
            if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                $a= $modul1 . ';' . $column . '#' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1));
            }
           // echo $modul1;
        }
    }
    $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
             and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
             and relmodule in (select name from  vtiger_tab where presence=0) 
             and module in (select name from  vtiger_tab where presence=0)";


   
    $query2 = "SELECT uitype, columnname, fieldlabel from  vtiger_field 
             join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
             where (uitype=76 or uitype=50 or uitype=51 or uitype=57 or uitype=58 or uitype=59 or uitype=73 or uitype=75 or  uitype=78
             or  uitype=80 or uitype=81 or uitype=68) and name='$m' and  vtiger_tab.presence=0";

    $result2 = $adb->query($query2);
    $num_rows2 = $adb->num_rows($result2);
    if ($num_rows2 != 0) {
        for ($i = 1; $i <= $num_rows2; $i++) {
            $ui = $adb->query_result($result2, $i - 1, 'uitype');
            $column = $adb->query_result($result2, $i - 1, 'columnname');
            $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');

            if ($ui == 51 || $ui == 50 || $ui == 73 || $ui == 68) {
                $modul1 = "Accounts";
                if ($ui == 68) $modul2 = 'Contacts';
            } else if ($ui == 57) {
                $modul1 = "Contacts";
                $modul2 = '';
            } else if ($ui == 59) {
                $modul1 = "Products";
                $modul2 = '';

            } else if ($ui == 58) {
                $modul1 = "Campaigns";
                $modul2 = '';

            } else if ($ui == 76) {
                $modul1 = "Potentials";
                $modul2 = '';

            } else if ($ui == 75 || $ui = 81) {
                $modul1 = "Vendors";
                $modul2 = '';
            } else if ($ui == 78) {
                $modul1 = "Quotes";
                $modul2 = '';
            } else if ($ui == 80) {
                $modul1 = "SalesOrder";
                $modul2 = '';
            }
            $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
            if ($modul2 != '') {
                $mo2 = $adb->query("select * from  vtiger_tab where name='$modul2' and presence=0");
            }
            if ($adb->num_rows($mo) != 0) {
                if (strlen($valuefromLoad) != 0 && $valuefromLoad == $modul1) {
                    $a =$modul1 . '; ' . $column . '#' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1));
                }
            }
            
               
            }

    }   
    return $a;
}






// function get all relation module only multi to one
function GetModuleMultiToOne($m,$FirstmoduleXML="")
   {
    global $log, $mod_strings,$adb;
    $j = 0;
    $a='<option value="" >(Select a module)</option>';
    $result = $adb->pquery("SELECT relmodule,columnname,fieldlabel 
            from vtiger_fieldmodulerel join vtiger_field 
            on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid 
            where module= ? and relmodule<>'Faq' and relmodule<>'Emails' and relmodule<>'Events'
            and relmodule<>'Webmails' and relmodule<>'SMSNotifier'
            and relmodule<>'PBXManager' and relmodule<>'Modcomments' and relmodule<>'Calendar' 
            and relmodule in (select name from vtiger_tab where presence=0)", array($m));

    $num_rows = $adb->num_rows($result);
    if ($num_rows != 0) {
        for ($i = 1; $i <= $num_rows; $i++) {

            $modul1 = $adb->query_result($result, $i - 1, 'relmodule');
            $log->debug("Fillim$i" . $modul1);
            $column = $adb->query_result($result, $i - 1, 'columnname');
            $fl = $adb->query_result($result, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                //$a .= '<option selected value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
               // $a .= '<option value="' . $modul1 . ';' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
           // echo $modul1;
        }
    }
    $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
             and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
             and relmodule in (select name from  vtiger_tab where presence=0) 
             and module in (select name from  vtiger_tab where presence=0)";


    $result1 = $adb->query($query1);
    $num_rows1 = $adb->num_rows($result1);
    if ($num_rows1 != 0) {
        for ($i = 1; $i <= $num_rows1; $i++) {
            $modul1 = $adb->query_result($result1, $i - 1, 'module');
            $column = $adb->query_result($result1, $i - 1, 'columnname');
            $fl = $adb->query_result($result1, $i - 1, 'fieldlabel');
            if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                $a .= '<option selected value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            } else {
                $a .= '<option value="' . $modul1 . '(many);' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
            }
        }
    }
    $query2 = "SELECT uitype, columnname, fieldlabel from  vtiger_field 
             join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
             where (uitype=76 or uitype=50 or uitype=51 or uitype=57 or uitype=58 or uitype=59 or uitype=73 or uitype=75 or  uitype=78
             or  uitype=80 or uitype=81 or uitype=68) and name='$m' and  vtiger_tab.presence=0";

    $result2 = $adb->query($query2);
    $num_rows2 = $adb->num_rows($result2);
    if ($num_rows2 != 0) {
        for ($i = 1; $i <= $num_rows2; $i++) {
            $ui = $adb->query_result($result2, $i - 1, 'uitype');
            $column = $adb->query_result($result2, $i - 1, 'columnname');
            $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');

            if ($ui == 51 || $ui == 50 || $ui == 73 || $ui == 68) {
                $modul1 = "Accounts";
                if ($ui == 68) $modul2 = 'Contacts';
            } else if ($ui == 57) {
                $modul1 = "Contacts";
                $modul2 = '';
            } else if ($ui == 59) {
                $modul1 = "Products";
                $modul2 = '';

            } else if ($ui == 58) {
                $modul1 = "Campaigns";
                $modul2 = '';

            } else if ($ui == 76) {
                $modul1 = "Potentials";
                $modul2 = '';

            } else if ($ui == 75 || $ui = 81) {
                $modul1 = "Vendors";
                $modul2 = '';
            } else if ($ui == 78) {
                $modul1 = "Quotes";
                $modul2 = '';
            } else if ($ui == 80) {
                $modul1 = "SalesOrder";
                $modul2 = '';
            }
            $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
            if ($modul2 != '') {
                $mo2 = $adb->query("select * from  vtiger_tab where name='$modul2' and presence=0");
            }
            if ($adb->num_rows($mo) != 0) {
                if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                   // $a .= '<option selected value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                } else {
                   // $a .= '<option value="' . $modul1 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                }
            }
            if ($modul2 != '' && $adb->num_rows($mo2) != 0)
                if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul2) {
                   // $a .= '<option selected value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
                else {
                    //$a .= '<option value="' . $modul2 . '; ' . $column . '">' . str_replace("'", "", getTranslatedString($modul2)) . ' ' . str_replace("'", "", getTranslatedString($fl, $modul2)) . '</option>';
                }
            }

    }

    if ($m == "Accounts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field
                join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where (uitype=73 or uitype=50
                or uitype=51 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events'
                and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' 
                and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                       $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Contacts") {
        $query2 = "SELECT name, columnname, fieldlabel from  vtiger_field 
                  join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
                  where (uitype=57 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' 
                  and name<>'Webmails' and name<>'SMSNotifier'and name<>'PBXManager' and name<>'Modcomments' 
                  and name<>'Calendar' and  vtiger_tab.presence=0";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                       $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
        }
    }
    if ($m == "Produts") {
        $query2 = "SELECT columnname,name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=59 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select *  from vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "Campaigns") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=58 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0
    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Potentials") {
        $query2 = "SELECT columnname, name ,fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=76 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and   vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
            }
        }
    }
    if ($m == "Quotes") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=78
        and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";

        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else {
                        $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }

            }
        }
    }
    if ($m == "SalesOrder") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=81 and uitype=75 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0)
                    $a .= '<option value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';


            }
        }
    }
    if ($m == "Vendors") {
        $query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=80 and name<>'$m'
        and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
        and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

    ";


        $result2 = $adb->query($query2);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $modul1 = $adb->query_result($result2, $i - 1, 'name');
                $column = $adb->query_result($result2, $i - 1, 'columnname');
                $fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
                $mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

                if ($adb->num_rows($mo) != 0) {
                    if (strlen($FirstmoduleXML) != 0 && $FirstmoduleXML == $modul1) {
                        $a .= '<option selected="selected" value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                    else{
                        $a .= '<option  value="' . $modul1 . '(many); ' . $column . '">' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1)) . '</option>';
                    }
                }
            }
        }
    }
    return $a;
}

/**
 * function to check if the table exist or not in database if exist show true if not create a table
 * @param [String] $tableName  The name of Table
 * @param string $primaryIds If you want to put a primary key in this table
 */
function Check_table_if_exist($tableName,$primaryIds="")
{
        global $adb;
        $exist=$adb->query_result($adb->query("SHOW TABLES LIKE '$tableName'"),0,0);
        if (strlen($exist)==0)
        {
         $createTable="
                CREATE TABLE `$tableName` (
                  `id` varchar(250) NOT NULL,
                  `firstmodule` varchar(250) NOT NULL,
                  `firstmoduletext` varchar(250) NOT NULL,
                  `secondmodule` varchar(250) NOT NULL,
                  `secondmoduletext` varchar(250) NOT NULL,
                  `query` text NOT NULL,
                  `sequence` int(11) NOT NULL,
                  `active` varchar(2) NOT NULL,
                  `firstmodulelabel` varchar(250) DEFAULT NULL,
                  `secondmodulelabel` varchar(250) DEFAULT NULL,
                  `labels` text NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            ";
            if (strlen($primaryIds)>0) {
                $createTable.="
                    ALTER TABLE `$tableName`
                      ADD PRIMARY KEY ($primaryIds);
                    COMMIT;

                ";
            }
         //return $createTable;
         $adb->query("DROP TABLE IF EXISTS `$tableName`");
         $adb->query($createTable);


        }else
        {
         return strlen($exist);
        }


        if (strlen($adb->query_result($adb->query("SHOW TABLES LIKE '$tableName'"),0,0))>0) 
        {
        return 1;
        }else
        {
        return 0;
        }
}


/**
    * function to check if exist or not the table for hht response type and if not exist create new one 
    *DROP TABLE IF EXISTS `mapgeneration_httpresponsetype`;
    * CREATE TABLE `mapgeneration_httpresponsetype` (
    *      `id` int(11) NOT NULL DEFAULT '0',
    *      `name` varchar(250) NOT NULL,
    *      `text` varchar(250) NOT NULL             
    * ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    * 
    * INSERT INTO `mapgeneration_httpresponsetype` ( `name`, `text`)
    * VALUES ('text', 'text')
    * ,('xml', 'xml')
    * ,('atom', 'atom')
    * ,('rss', 'rss')
    * ,('json', 'json')
    * ,('csv', 'csv')
    * 
    * 
    * @param string $tablename
    * @return void
*/
function CheckIfExistResponseTypeTable($tablename)
{
    global $adb,$root_directory, $log;
    $returvalues;
    $exist=$adb->query_result($adb->query("SHOW TABLES LIKE '$tablename'"),0,0);
    if (strlen($exist)==0)
    {
       try{
        $createTable="
            CREATE TABLE `$tablename` (
            `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(250) NOT NULL,
            `text` varchar(250) NOT NULL             
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ";        
        //return $createTable;
        LogFileSimple($createTable);
        $adb->query("DROP TABLE IF EXISTS `$tablename`");
        $adb->query($createTable);
        $insertdefaultvalue="INSERT INTO `$tablename` (`name`, `text`) VALUES ";
        $insertdefaultvalue.="('text', 'text')";
        $insertdefaultvalue.=",('xml', 'xml')";
        $insertdefaultvalue.=",('atom', 'atom')";
        $insertdefaultvalue.=",('rss', 'rss')";
        $insertdefaultvalue.=",('json', 'json')";
        $insertdefaultvalue.=",('csv', 'csv')";
        $adb->query($insertdefaultvalue);
        LogFileSimple($update);
        if( $adb->database->Affected_Rows($update)!=0)
        $returvalues=2;
        else
        $returvalues= 0;
       }catch(Exception $ex)
       {
            $log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception (for more information check the MapGeneratorLogs.txt) ".$ex->getMessage());
            LogFileSimple($ex);
            return "";
       }

    }

    try {
        $returndata="";
        $query ="Select * from  `$tablename` ";
        $result2 = $adb->query($query);
        $num_rows2 = $adb->num_rows($result2);
        if ($num_rows2 != 0) {
            for ($i = 1; $i <= $num_rows2; $i++) {
                $name = $adb->query_result($result2, $i - 1, 'name');
                $text = $adb->query_result($result2, $i - 1, 'text');
                $a .= '<option selected="selected" value="' . $name .'">' . $text . '</option>';
                   
                }
            }
        return $a;
    } catch (Exception $ex) {
        $log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception (for more information check the MapGeneratorLogs.txt) ".$ex->getMessage());
        LogFileSimple($ex);
        return "";
    }
}




/**
 * function to call a php file and return the values
 * @param [type] $file filename (include the path )
 */
function GetTheresultByFile($file){
    ob_start();
    require($file);
    return ob_get_clean();
}

/**
 * this function generate the template to show the error if something was wrong 
 *
 * @param      string            $TitleError  The title error
 * @param      string            $BoddyError  The boddy error
 *
 * @return     vtigerCRM_Smarty  ( description_of_the_return_value )
 */
function showError($TitleError='',$BoddyError='')
{
    global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
    $theme_path = "themes/" . $theme . "/";
    $image_path = $theme_path . "images/";
    require_once ('include/utils/utils.php');
    require_once ('Smarty_setup.php');
    require_once ('include/database/PearDatabase.php');
    // require_once('database/DatabaseConnection.php');
    require_once ('include/CustomFieldUtil.php');
    require_once ('data/Tracker.php');
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("TitleError", $TitleError);
    $smarty->assign("BodyError", $BoddyError);
    $output = $smarty->fetch('modules/MapGenerator/Error.tpl');
    return $output; 
}



/**
 * Gets the module id.or anothe record from vtiger_entityname
 *
 * @param      <type>     $module      The module
 * @param      string     $moduleName  The module name
 *
 * @throws     Exception  (description)
 *
 * @return     string     The module id.
 */
function getModuleID($module,$moduleName="entityidfield")
{
    global $adb,$root_directory, $log;
    try {

        $result = $adb->pquery("Select * from  vtiger_entityname where modulename = ?",array($module));
        $num_rows = $adb->num_rows($result);
        if ($num_rows>0) {
            $Resulti = $adb->query_result($result,0,$moduleName);

            if (!empty($Resulti)) {
                return $Resulti;
            } else {
                throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
            }
        } else {
            throw new Exception(TypeOFErrors::ERRORLG."Not exist Map with this ID=".$Queryid,1);
        }
    } catch (Exception $ex) {
         $log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
         return "";
    }
}

/**
 * function to search by module id 
 *
 * @param      <type>     $Idmodule    The idmodule
 * @param      string     $moduleName  The module name what do you want to take 
 *
 * @throws     Exception  (description)
 *
 * @return     string     ( description_of_the_return_value )
 */
function SearchbyIDModule($Idmodule,$moduleName="modulename")
{
    global $adb,$root_directory, $log;
    require_once('Staticc.php');
    try {
        
        $sql="SELECT * from vtiger_entityname WHERE tabid='$Idmodule'";
        $result = $adb->query($sql);
        $num_rows=$adb->num_rows($result);
        if ($num_rows>0) {
            $Resulti = $adb->query_result($result,0,$moduleName);

            if (!empty($Resulti)) {
                return $Resulti;
            } else {
                throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
            }
        } else {
            throw new Exception(TypeOFErrors::ERRORLG."Not exist Map with this ID=".$Queryid,1);
        }
    } catch (Exception $ex) {
         $log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
         return "";
    }
}


/**
 * Gets the allrelation.
 *
 * @param      string  $module  The module
 *
 * @return     string  The allrelation.
 */
function GetAllRelationDuplicaterecords($module="")
{
    global $adb, $root_directory, $log;
    if (!empty($module))
    {
        $log->debug("Info!! Value is not ampty");
        $idmodul=getModuleID($module,"tabid");
        $sql="SELECT * from vtiger_relatedlists where tabid='$idmodul'";
        $result = $adb->query($sql);
        $num_rows=$adb->num_rows($result);
        $historymap="";
        $a='<option value="" >(Select a module)</option>';
        if($num_rows!=0)
        {
            for($i=1;$i<=$num_rows;$i++)
            {
                $modulename=(!empty(SearchbyIDModule($adb->query_result($result,$i-1,'related_tabid')))?SearchbyIDModule($adb->query_result($result,$i-1,'related_tabid')):$adb->query_result($result,$i-1,'label'));
                $Modules = $adb->query_result($result,$i-1,'label');
                $relatedtypes = $adb->query_result($result,$i-1,'relationtype');
               
                $a.='<option value="'.$modulename.'#'.$relatedtypes.'">'.str_replace("'", "", getTranslatedString($modulename)).'</option>';                
            }
           return $a;
        }else{$log->debug("Info!! The database is empty or something was wrong");}
    }else {
        return "";
    }
     
}

/**
 * Gets the exception trace as string.
 *
 * @param      <type>  $exception  The exception
 *
 * @return     string  The exception trace as string.
 */
function getExceptionTraceAsString($exception) {
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }   
            }   
            $args = join(", ", $args);
        }
        $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
    $count,
    isset($frame['file']) ? $frame['file'] : 'unknown file',
    isset($frame['line']) ? $frame['line'] : 'unknown line',
    (isset($frame['class']))  ? $frame['class'].$frame['type'].$frame['function'] : $frame['function'],
    $args );
        $count++;
    }
    return $rtn;
}

/**
     * function to show the error only 
     *
     * @param      <type>  $exepsion  The exepsion
     */
function LogFile($exepsion)
{
    global $root_directory;
    $updateMovedInToAssigned = fopen($root_directory."logs/MapGeneratorLogs.txt", "a");
    $str = "\n\n~~~~~~~~~~~~~~~~~~~~~ \n[".date("Y/m/d h:i:s " , mktime())."] \n~~~~~~~~~~~~~~~~~~~~~\n";
     $str .= "\n\n~~~~~~~~~~~Message~~~~~~~~~~ ".$exepsion->getMessage()."\n";
    fwrite($updateMovedInToAssigned, "\n".$str."\nError Handler : \n\t\t".getExceptionTraceAsString($exepsion));
    fclose($updateMovedInToAssigned);
}

/**
  * function to show the error only
  *
  * @param      string  $exepsion  The exepsion
  */
function LogFileSimple($exepsion="")
{
    global $root_directory;
    $updateMovedInToAssigned = fopen($root_directory."logs/MapGeneratorLogs.txt", "a");
    $str = "\n\n~~~~~~~~~~~~~~~~~~~~~ \n[".date("Y/m/d h:i:s " , mktime())."] \n~~~~~~~~~~~~~~~~~~~~~\n";
     $str .= "\n\n~~~~~~~~~~~Message~~~~~~~~~~ ".$exepsion."\n";
    fwrite($updateMovedInToAssigned, "\n".$str);
    fclose($updateMovedInToAssigned);
}

/**
 * for all maps type
 *
 * @return     string  ( description_of_the_return_value )
 */
function SelectallMaps()
{
    global $mod_strings;
    require_once("AllMapsType.php");
    $allmaps='<option value="">'.$mod_strings['TypeMapNone'].'</option>';
    foreach ($AllMaps as $key => $value) {
       $allmaps.=' <option value="'.$key.'">'.$mod_strings[$value].'</option>';
    }
    return $allmaps;
}



function GetFromVtigerField($idmodule,$uitype,$columnname="fieldid")
{
    global $adb;
    $sql="SELECT * FROM  `vtiger_field` WHERE  `tabid` =".$idmodule." AND  `uitype`=".$uitype."";
    $result = $adb->query($sql);
    $num_rows=$adb->num_rows($result);
    $retrive = array();
    if ($num_rows>0) {
        for ($i=1; $i<=$num_rows; $i++) { 
            array_push($retrive,$adb->query_result($result,$i-1,$columnname));
        }
        return $retrive;
    }else{
        // throw new Exception("Data retrive from query are empty or something was wrong ", 1);
        return "";
    }
    
}


/**
 * Gets the from vtiger fieldmodulerel.
 *
 * @param      string     $fieldid     The fieldid
 * @param      string     $columnname  The columnname
 *
 * @throws     Exception  (description)
 *
 * @return     array      The from vtiger fieldmodulerel.
 */
function GetFromVtigerFieldmodulerel($fieldid,$columnname="relmodule")
{
    global $adb;
    $sql="SELECT * FROM  `vtiger_fieldmodulerel` WHERE  `fieldid` ='".$fieldid."'";
    $result = $adb->query($sql);
    $num_rows=$adb->num_rows($result);
    $modules = array();
    if ($num_rows>0) {
        for ($i=1; $i<=$num_rows; $i++) { 
            array_push($modules,$adb->query_result($result,$i-1,$columnname));
        }
        return $modules;
    }else{
        throw new Exception("Data retrive from query are empty or something was wrong ", 1);       
    }
    
}


/**
 * function for mapping 
 *
 * @param      <type>  $module  The module
 */
function MappingRelationFields($module)
{   require_once("Staticc.php");
    
    global $adb, $root_directory, $log;    
    $allmodules = array();
   try{
      if (empty($module)) {
            throw new Exception("Missing the Module name ", 1);
            
       }
      array_push($allmodules,$module);
      $idmodule=getModuleID($module,"tabid"); 
      $arrafieldid=GetFromVtigerField($idmodule,"10");
      if (!empty($arrafieldid)) {
           foreach ($arrafieldid as $value) {
              foreach (GetFromVtigerFieldmodulerel($value) as $valuee) {                   
                   array_push($allmodules,$valuee);
               } 
          }
      }
      $unioqueModul = array_unique($allmodules);      
      return $unioqueModul;
        
    }catch(Exception $ex)
    {
       $log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception (for more information check the MapGeneratorLogs.txt) ".$ex->getMessage());
       LogFile($ex);
       return "";
    }
}



?>