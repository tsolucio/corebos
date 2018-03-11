<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

if (isset($_REQUEST['activitytype']) && $_REQUEST['activitytype']=='Emails') {
	$_REQUEST['activitytype'] = 'Task'; // cbCalendar Emails cannot be created through this GUI
}
$__cbSaveSendHeader = false;
require_once 'modules/Vtiger/Save.php';

if (isset($_REQUEST['Module_Popup_Edit']) && $_REQUEST['Module_Popup_Edit']==1) {
	echo "<script>if (typeof window.opener.graphicalCalendarRefresh == 'function') window.opener.graphicalCalendarRefresh();window.close();</script>";
} else {
	header('Location: index.php?' . $req->getReturnURL() . $search);
}
?>
