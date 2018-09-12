
<?php

include 'All_functions.php';
include 'modfields.php';

global $adb, $root_directory, $log;
$modules = $_POST['mod'];
$firstmodule = $_POST['firstmodule'];
$datareturn = "";
// $datareturn.=getModFields(explode(";", $key)[0], $acno.$dbname);
if (!empty($modules)) {
    if (!empty($modules)) {
        $log->debug("Info!! Value is not ampty");
        $sql = "SELECT module FROM `vtiger_fieldmodulerel` WHERE relmodule = '$modules' ";
        $result = $adb->query($sql);
        $num_rows = $adb->num_rows($result);
        $historymap = "";
        $a = '<option value="" >(Select a field)</option>';
        $keepmodules = array();
        if ($num_rows != 0) {
            for ($i = 1; $i <= $num_rows; $i++) {
                $Module = $adb->query_result($result, $i - 1, 'module');
                $keepmodules[] = $Module;
                if ($Module != $firstmodule) {
                    $a .= getModFields($Module, $acno . $dbname);
                }
            }
            if (!in_array($modules, $keepmodules)) {
                $a .= getModFields($modules, $acno . $dbname);
            }
            echo $a;
        } else { $log->debug("Info!! The database is empty or something was wrong");}
    } else {
        echo "";
    }
}
