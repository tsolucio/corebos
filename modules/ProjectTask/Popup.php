<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if ($_REQUEST['return_module']=='Project' && $_REQUEST['popuptype']=='detailview' 
    && $_REQUEST['form']=='EditView') {
    $where="vtiger_projecttask.projectid in ('',NULL)";
}
require_once('Popup.php');
?>
