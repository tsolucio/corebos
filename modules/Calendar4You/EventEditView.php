<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$req = new Vtiger_Request();
$req->setDefault('RETURN_MODULE', $currentModule);
if (!empty($_REQUEST['return_module'])) {
	$req->set('RETURN_MODULE', $_REQUEST['return_module']);
}
$req->setDefault('RETURN_ACTION', 'DetailView');
if (!empty($_REQUEST['return_action'])) {
	$req->set('RETURN_ACTION', $_REQUEST['return_action']);
}
//code added for returning back to the current view after edit from list view
if (!empty($_REQUEST['return_id'])) {
	$req->set('RETURN_ID', $_REQUEST['return_id']);
	$req->set('return_cbfromid', $_REQUEST['return_id']);
}
if (isset($_REQUEST['activity_mode'])) {
	$req->set('return_activitytype', ($_REQUEST['activity_mode']=='Events' ? 'Call' : $_REQUEST['activity_mode']));
}
if (isset($_REQUEST['parent_id'])) {
	$req->set('return_rel_id', $_REQUEST['parent_id']);
}
$urlparams = '&'.$req->getReturnURL();
?>
<script>
gotourl('index.php?module=cbCalendar&action=EditView<?php echo $urlparams; ?>');
</script>
<?php die(); ?>