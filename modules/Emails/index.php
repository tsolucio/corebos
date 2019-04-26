<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $theme;
$theme_path="themes/$theme/";
$image_path=$theme_path.'images/';

if (isset($_REQUEST['mailconnect'])) {
	$ERROR_MESSAGE_CLASS = 'cb-alert-error';
	$ERROR_MESSAGE = 'LBL_MAIL_CONNECT_ERROR_INFO';
}
include 'modules/Emails/ListView.php';
?>
