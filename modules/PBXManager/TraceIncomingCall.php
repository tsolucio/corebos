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
 * This function traces an incoming call and adds it to the databse for vtiger to pickup,
 * it also adds an entry to the activity history of the related Contact/Lead/Account
 * only these three modules are supported for now
 */
function TraceIncomingCall(){
	
	require_once('modules/PBXManager/AsteriskUtils.php');
	global $adb, $current_user;
	global $theme,$app_strings,$log;
	
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	
	$asterisk_extension = false;
	if(isset($current_user->column_fields)) {
		$asterisk_extension = $current_user->column_fields['asterisk_extension'];
	} else {
		$sql = "select asterisk_extension from vtiger_asteriskextensions where userid = ?";
		$result = $adb->pquery($sql, array($current_user->id));
		$asterisk_extension = $adb->query_result($result, 0, "asterisk_extension");
	}
	
	$query = "select * from vtiger_asteriskincomingcalls where to_number = ?";
	$result = $adb->pquery($query, array($asterisk_extension));
	
	if($adb->num_rows($result)>0){
		$flag = $adb->query_result($result,0,"flag");
		$oldTime = $adb->query_result($result,0,"timer");
		$callerNumber = $adb->query_result($result,0,"from_number");
		$callerName = $adb->query_result($result,0,"from_name");
		$callerType = $adb->query_result($result,0,"callertype");
		$refuid = $adb->query_result($result, 0, "refuid");

		if(!empty($callerNumber)){
			$tracedCallerInfo = getTraceIncomingCallerInfo($callerNumber);
		} 

		$callerLinks = $tracedCallerInfo['callerLinks'];
		$firstCallerInfo = false;
		if(!empty($tracedCallerInfo['callerInfos'])) {
			$firstCallerInfo = $tracedCallerInfo['callerInfos'];
		}		

		$newTime = time();
		if(($newTime-$oldTime)>=3 && $flag == 1){ 
			$adb->pquery("delete from vtiger_asteriskincomingcalls where to_number = ?", array($asterisk_extension));
		}else{
			if($flag==0){
				$flag=1;
				
				// Trying to get the Related CRM ID for the Event (if already desired by popup click)
				$relcrmid = false;
				if(!empty($refuid)) {
					$refuidres = $adb->pquery('SELECT relcrmid FROM vtiger_asteriskincomingevents WHERE uid=?',array($refuid));
					if($adb->num_rows($refuidres)) $relcrmid = $adb->query_result($refuidres, 0, 'relcrmid');
				}
				$adb->pquery("update vtiger_asteriskincomingcalls set flag = ? where to_number = ?", array($flag, $asterisk_extension));
				$activityid = asterisk_addToActivityHistory($callerName, $callerNumber, $callerType, $adb, $current_user->id, $relcrmid, $firstCallerInfo);
				
			}
			//prepare the div for incoming calls
			$status = "	<table  border='0' cellpadding='5' cellspacing='0'>
						<tr>
							<td style='padding:10px;' colspan='2'><b>".$app_strings['LBL_INCOMING_CALL']."</b></td>
						</tr>
					</table>
					<table  border='0' cellpadding='0' cellspacing='0' class='hdrNameBg'>
						<tr><td style='padding:10px;' colspan='2'><b>".$app_strings['LBL_CALLER_INFORMATION']."</b>
							<br><b>".$app_strings['LBL_CALLER_NUMBER']."</b> $callerNumber
							<br><b>".$app_strings['LBL_CALLER_NAME']."</b> $callerName
						</td></tr>
						<tr><td style='padding:10px;' colspan='2'><b>".$app_strings['LBL_INFORMATION_VTIGER']."</b>
							<br> $callerLinks
						</td></tr>
					</table>";
		}
	}else{
		$status = "failure";
	}
	return $status;
}

//functions for asterisk integration start
/**
 * this function returns the caller name based on the phone number that is passed to it
 * @param $from - the number which is calling
 * returns caller information in name(type) format :: for e.g. Mary Smith (Contact)
 * if no information is present in database, it returns :: Unknown Caller (Unknown)
 */
function getTraceIncomingCallerInfo($from) {
	global $adb;
	// Grab all possible caller informations (lookup for number as well stripped number)
	$callerInfos = getCallerInfo($from);
	$callerLinks = '';

	if($callerInfos !== false){
		$callerName = decode_html($callerInfos['name']);
		$module = $callerInfos['module'];
		$callerModule = " [$module]";
		$callerID = $callerInfos['id'];
		$callerLinks = $callerLinks."<a href='index.php?module=$module&action=DetailView&record=$callerID'>$callerName</a>$callerModule<br>";		
	}else{
		$callerLinks = $callerLinks."<br>
						<a target='_blank' href='index.php?module=Leads&action=EditView&extra_action=addToCallHistory&phone=$from'>".getTranslatedString('LBL_CREATE_LEAD')."</a><br>
						<a target='_blank' href='index.php?module=Contacts&action=EditView&extra_action=addToCallHistory&phone=$from'>".getTranslatedString('LBL_CREATE_CONTACT')."</a><br>
						<a target='_blank' href='index.php?module=Accounts&action=EditView&extra_action=addToCallHistory&phone=$from'>".getTranslatedString('LBL_CREATE_ACCOUNT')."</a>";
	}
	return array(
		'callerInfos' => $callerInfos,
		'callerLinks' => $callerLinks
	);
}

?>
