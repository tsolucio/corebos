<?php
    // Turn on debugging level
    $Vtiger_Utils_Log = true;

    include_once 'vtlib/Vtiger/Module.php';
    error_reporting(E_ALL);
    ini_set("display_errors", "on");
    $current_user = Users::getActiveAdminUser();

    require_once 'include/utils/pdfUtil.php';

    $input = '/home/timothy/Documents/pdfUtil/NOM_LisDPRosOMA4-desbloqueado.pdf';
    $password = 'corebos';
    $output = '/var/www/html/setassign/files/NOM_LisDPRosOMA4-desbloqueado_encrypted.pdf';
    $pdfUtil = pdfUtil::PDFProtect($input, $password, $output);
    //var_dump($pdfUtil);

    //echo "<br /><br />";

    // $input = '/var/www/html/setassign/files/NOM_LisDPRosOMA4-desbloqueado_encrypted.pdf';
    // $password = 'corebos';
    // $output = '/var/www/html/setassign/files/NOM_LisDPRosOMA4-desbloqueado_UN-ecrypted.pdf';
    // $pdfUtil = pdfUtil::PDFUnProtect($input, $password, $output);
    // var_dump($pdfUtil);

    $input = '/home/timothy/Documents/pdfUtil/NOM_LisDPRosOMA4-desbloqueado.pdf';
    $module = 'Accounts';
    $fieldname = 'account_no';
    $pdfUtil = pdfUtil::PDFIdentifyByNIF($input, $module, $fieldname);
    var_dump($pdfUtil);
