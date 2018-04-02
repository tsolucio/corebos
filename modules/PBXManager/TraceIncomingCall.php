<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

echo TraceIncomingCall();

/**
 * This function traces an incoming call and adds it to the databse to be picked up,
 * it also adds an entry to the activity history of the related Contact/Lead/Account
 * only these three modules are supported for now
 */
function TraceIncomingCall() {
	require_once 'modules/PBXManager/AsteriskUtils.php';
	global $adb, $current_user, $app_strings;

	$asterisk_extension = false;
	if (isset($current_user->column_fields)) {
		$asterisk_extension = $current_user->column_fields['asterisk_extension'];
	} else {
		$result = $adb->pquery('select asterisk_extension from vtiger_asteriskextensions where userid = ?', array($current_user->id));
		$asterisk_extension = $adb->query_result($result, 0, 'asterisk_extension');
	}
	$status = 'failure';
	$result = $adb->pquery('select * from vtiger_asteriskincomingcalls where to_number = ?', array($asterisk_extension));
	if ($adb->num_rows($result)>0) {
		$flag = $adb->query_result($result, 0, 'flag');
		$oldTime = $adb->query_result($result, 0, 'timer');
		$callerNumber = $adb->query_result($result, 0, 'from_number');
		$callerName = $adb->query_result($result, 0, 'from_name');
		$callerType = $adb->query_result($result, 0, 'callertype');
		$refuid = $adb->query_result($result, 0, 'refuid');

		$newTime = time();
		if (($newTime-$oldTime)>=3 && $flag == 1) {
			$adb->pquery('delete from vtiger_asteriskincomingcalls where to_number = ?', array($asterisk_extension));
		} else {
			$activityid = 0;
			if ($flag==0) {
				$flag=1;
				// Trying to get the Related CRM ID for the Event (if already desired by popup click)
				$relcrmid = false;
				if (!empty($refuid)) {
					$refuidres = $adb->pquery('SELECT relcrmid FROM vtiger_asteriskincomingevents WHERE uid=?', array($refuid));
					if ($adb->num_rows($refuidres)) {
						$relcrmid = $adb->query_result($refuidres, 0, 'relcrmid');
					}
				}
				$adb->pquery('update vtiger_asteriskincomingcalls set flag = ? where to_number = ?', array($flag, $asterisk_extension));
				$activityid = asterisk_addToActivityHistory($callerName, $callerNumber, $callerType, $adb, $current_user->id, $relcrmid, getCallerInfo($callerNumber));
			}
			$callerLinks = '';
			if (!empty($callerNumber)) {
				$createActivityInfo = array(
					'action' => 'asterisk_addToActivityHistory',
					'callerName' => $callerName,
					'callerNumber' => $callerNumber,
					'callerType' => $callerType,
					'activityid' => $activityid,
				);
				$tracedCallerInfo = getTraceIncomingCallerInfo($callerNumber, $callerName, $createActivityInfo);
				$callerLinks = $tracedCallerInfo['callerLinks'];
			}
			//prepare the div for incoming calls
			$status = "<table border='0' cellpadding='5' cellspacing='0'>
						<tr>
							<td style='padding:10px;' colspan='2'><b>".$app_strings['LBL_INCOMING_CALL']."</b></td>
						</tr>
					</table>
					<table border='0' cellpadding='0' cellspacing='0' class='hdrNameBg'>
						<tr><td style='padding:10px;' colspan='2'><b>".$app_strings['LBL_CALLER_INFORMATION']."</b>
							<br><b>".$app_strings['LBL_CALLER_NUMBER']."</b> $callerNumber
							<br><b>".$app_strings['LBL_CALLER_NAME']."</b> $callerName
							<br> $callerLinks
						</td></tr>
					</table>";
		}
	}
	return $status;
}

/**
 * this function returns a formatted link with the caller's name if found or links to create a new record if not
 * @param $from - the number which is calling
 * @param $fromname - the name found in the call to create the record
 * @param $createActivityInfo - info to create activity after new create
 */
function getTraceIncomingCallerInfo($from, $fromname, $createActivityInfo) {
	// Grab all possible caller informations (lookup for number as well stripped number)
	$callerInfos = getCallerInfo($from);
	$createActivityInfo = urlencode(serialize($createActivityInfo));
	if ($callerInfos !== false) {
		$callerName = decode_html($callerInfos['name']);
		$module = $callerInfos['module'];
		$callerModule = " [$module]";
		$callerID = $callerInfos['id'];
		$callerLinks = "<a href='index.php?module=$module&action=DetailView&record=$callerID'>$callerName</a>$callerModule<br>";
		$callerLinks.= "<br>
			<a target='_blank' href='index.php?module=HelpDesk&action=EditView&parent_id=$callerID&ticket_title=$callerName&cbcustominfo1=$createActivityInfo'>"
			.getTranslatedString('LBL_CREATE_TICKET')."</a><br>
			<a target='_blank' href='index.php?module=Potentials&action=EditView&related_to=$callerID&potentialname=$callerName&cbcustominfo1=$createActivityInfo'>"
			.getTranslatedString('LBL_CREATE').' '.getTranslatedString('SINGLE_Potentials', 'Potentials')."</a><br>";
	} else {
		$from = urlencode($from);
		$fromname = urlencode($fromname);
		$callerLinks = "<br>
			<a target='_blank' href='index.php?module=Leads&action=EditView&lastname=".$fromname."&phone=$from&cbcustominfo1=$createActivityInfo'>"
			.getTranslatedString('LBL_CREATE_LEAD')."</a><br>
			<a target='_blank' href='index.php?module=Contacts&action=EditView&lastname=".$fromname."&phone=$from&cbcustominfo1=$createActivityInfo'>"
			.getTranslatedString('LBL_CREATE_CONTACT')."</a><br>
			<a target='_blank' href='index.php?module=Accounts&action=EditView&accountname=".$fromname."&phone=$from&cbcustominfo1=$createActivityInfo'>"
			.getTranslatedString('LBL_CREATE_ACCOUNT')."</a>";
	}
	return array(
		'callerInfos' => $callerInfos,
		'callerLinks' => $callerLinks
	);
}
?>
