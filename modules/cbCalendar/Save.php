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
	if (!empty($_REQUEST['saverepeat'])) {
		$sesreq = coreBOS_Session::get('saverepeatRequest', array());
		$sesreq['CANCELGO'] = 'index.php?' . $req->getReturnURL() . $search;
		coreBOS_Session::set('saverepeatRequest', $sesreq);
		header('Location: index.php?action=EditView&saverepeat=1&module='.$currentModule);
	} else {
		if (coreBOS_Session::has('ME1x1Info')) {
			$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
			if (count($ME1x1Info['pending'])==1) {
				coreBOS_Session::delete('ME1x1Info');// we are done
				header('Location: index.php?' . $req->getReturnURL() . $search);
			} else {
				array_shift($ME1x1Info['pending']); // this one is done
				$ME1x1Info['processed'][] = $ME1x1Info['next'];
				$ME1x1Info['next'] = $ME1x1Info['pending'][0];
				coreBOS_Session::set('ME1x1Info', $ME1x1Info);
				$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
				header('Location: index.php?action=EditView&record='.$ME1x1Info['pending'][0].'&module='.$currentModule);
			}
		} else {
			header('Location: index.php?' . $req->getReturnURL() . $search);
		}
	}
	die();
}
?>
