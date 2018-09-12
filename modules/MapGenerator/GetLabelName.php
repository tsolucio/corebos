
<?php

//GetLabelName.php

$getValue = $_POST["DefaultValue"];
$DefaultValueFirstModuleField = $_POST["DefaultValueFirstModuleField"];
if (!empty($getValue)) {
    // print_r(explode(":",$getValue));
    echo GetLabelName(explode(":", $getValue)[0], explode(":", $getValue)[1]);
} else if (!empty($DefaultValueFirstModuleField)) {
    // print_r(explode(":",$getValue));
    echo GetLabelName(explode(":", $DefaultValueFirstModuleField)[0], explode(":", $DefaultValueFirstModuleField)[1]);
}

function GetLabelName($tablenam, $columnnam, $fieldname)
{
    global $log;
    $log->debug("Entering getAdvSearchfields(" . $module . ") method ...");
    global $adb;
    global $current_user;
    global $mod_strings, $app_strings;

    $q = $adb->query("SELECT * from vtiger_field WHERE  tablename='$tablenam' and ( columnname='$columnnam'  OR fieldname='$fieldname')");
    return $seq = $adb->query_result($q, 0, "fieldlabel");
}