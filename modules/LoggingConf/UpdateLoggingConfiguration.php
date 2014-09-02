<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/CommonUtils.php');
global $adb;
$tab_id= getTabid(vtlib_purify($_REQUEST['Screen']));
$fieldsarray=$_REQUEST['fieldstobeloggedModule'];

        //Updating the database
$update_query = "update vtiger_loggingconfiguration set fields=? where tabid=?";
$update_params = array($fieldsarray, $tab_id);
$query=$adb->pquery($update_query, $update_params);
echo $query;
?>