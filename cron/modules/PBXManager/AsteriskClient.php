#!/usr/bin/php
<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
 
@ini_set('error_reporting', E_WARNING & ~E_NOTICE);

chdir('../../../');

# In case chdir is not permitted
# ini_set('include_path','../../../');
require_once ('config.php');
require_once ('include/utils/utils.php');
require_once ('include/language/en_us.lang.php');
require_once ('modules/PBXManager/utils/AsteriskClass.php');
require_once ('modules/PBXManager/AsteriskUtils.php');

main__asteriskClient();

function main__asteriskClient() {
	global $app_strings, $current_user;
	global $adb, $log;

	$data = getAsteriskInfo($adb);

	$errno = $errstr = null;
	$sock  = @fsockopen($data['server'], $data['port'], $errno, $errstr, 1);
	stream_set_blocking($sock, false);

	if($sock === false) {
		echo "Socket cannot be created due to errno [$errno] - $errstr";
		$log->debug("Socket cannot be created due to errno [$errno] - $errstr");
		exit(0);
	}

	echo "Connecting to asterisk server @ " . date("Y-m-d H:i:s") . "\n";
	$log->debug("Connecting to asterisk server @ " . date("Y-m-d H:i:s"));
	echo "Connected successfully\n\n";

	$asterisk = new Asterisk($sock, $data['server'], $data['port']);	
	
	# authorize user first
	authorizeUser($data['username'], $data['password'], $asterisk);

	// Keep looping to poll the asterisk events
	while(true) {
		// Give some break to avoid server hanging
		sleep(1);
		try {
			$incoming = asterisk_handleEvents($asterisk, $adb, $data['version']);
			asterisk_IncomingEventCleanup($adb);
		} catch(Exception $ex) {
			echo "EXCEPTION: " . $ex->getMessage() . "\n";
		}
	}
	fclose($sock);
	unset($sock);
}

/*
 * Delete the stale incoming events information recorded to avoid
 * overgrowth of the database. 
 */
function asterisk_IncomingEventCleanup($adb) {
	$HOURRANGE = 60 * 60;
	$TIMELIMIT = $HOURRANGE * 12; // Delete events older than 'n' hours
	
	$adb->pquery("DELETE FROM vtiger_asteriskincomingevents WHERE timer < ? ", array(time() - $TIMELIMIT) );
}

/**
 * Grab the events from server, parse it and process it.
 */
function asterisk_handleEvents($asterisk, $adb, $version="1.4") {	
	$fnEntryTime = time();
	//values of flag for asteriskincomingevents(-1 for stray calls, 0 for incoming calls, 1 for outgoing call)
	do {
		$mainresponse = $asterisk->getAsteriskResponse();

		if(!empty($mainresponse)) {
			$state = ($version == "1.6")? "ChannelStateDesc" : "State";

			if(asterisk_handleResponse1($mainresponse, $state, $adb)) {
				if(asterisk_handleResponse2($mainresponse, $adb, $asterisk, $state)) {
					if(asterisk_handleResponse3($mainresponse, $adb, $asterisk)){
						// Proceed if previous event could not be handled.
					}
				}
			}
		} else {
			// No more response to consume
			break;
		}
	} while(true);

	return false;
}

function asterisk_handleResponse1($mainresponse, $state, $adb) {
	if(
		(($mainresponse['Event'] == 'Newstate' || $mainresponse['Event'] == 'Newchannel') && ($mainresponse[$state] == 'Ring') 
		|| ($mainresponse['Event'] == 'Newstate' && $mainresponse[$state] == 'Ringing'))
	) {

		$uniqueid = $mainresponse['Uniqueid'];

		if(!empty($mainresponse['CallerID'])) {
			$callerNumber = $mainresponse['CallerID'];
		}elseif(!empty($mainresponse['CallerIDNum'])) {
			$callerNumber = $mainresponse['CallerIDNum'];
		}
		if(!empty($mainresponse['CallerIDName'])) {
			$callerName = $mainresponse['CallerIDName'];
		}
		$channel = $mainresponse['Channel'];

		$sql = "INSERT INTO vtiger_asteriskincomingevents
			(uid, channel, from_number, from_name, timer, flag) VALUES(?,?,?,?,?,?)";
		$adb->pquery($sql, array($uniqueid, $channel, $callerNumber, $callerName, time(), -1));
		
		return false;
	}
	return true;
}

function asterisk_handleResponse2($mainresponse, $adb, $asterisk, $state) {
	$appdata = $mainresponse['AppData'];
	  	
	$uniqueid = $channel = $callerType = $extension = null;
	$parseSuccess = false;
	
	if(
		$mainresponse['Event'] == 'Newexten' && (strstr($appdata, "__DIALED_NUMBER") || strstr($appdata, "EXTTOCALL"))
	) {

		$uniqueid = $mainresponse['Uniqueid'];

		$channel = $mainresponse['Channel'];
		$splits = explode('/', $channel);
		$callerType = $splits[0];

		$splits = explode('=', $appdata);
		$extension = $splits[1];
		
		$parseSuccess = true;
	} else if($mainresponse['Event'] == 'OriginateResponse'){
		//if the event is OriginateResponse then its an outgoing call and set the flag to 1, so that AsteriskClient does not pick up as incoming call
		$uniqueid = $mainresponse['Uniqueid'];
		$adb->pquery("UPDATE vtiger_asteriskincomingevents set flag = 1 WHERE uid = ?", array($uniqueid));
	}
	
	if($parseSuccess) {	
		
		if(checkExtension($extension, $adb)) {
			
			$sql = "UPDATE vtiger_asteriskincomingevents SET to_number=?, callertype=?, timer=?, flag=? WHERE uid=?";
			$adb->pquery($sql, array($extension, $callerType, time(), 0, $uniqueid));
			
			$callerinfo = $adb->pquery("SELECT from_number,from_name FROM vtiger_asteriskincomingevents WHERE uid = ?",array($uniqueid));
			
			if($adb->num_rows($callerinfo) > 0){
				$callerNumber = $adb->query_result($callerinfo, 0, "from_number");
				$callerName = $adb->query_result($callerinfo, 0, "from_name");
				
				if(empty($callerNumber) || $callerNumber == '0') {
					// We don't have the information who is calling, could happen in Asterisk 1.4 (when call is made to Queue)
					// Let us defer the popup show for next Event: Link
					$sql = "UPDATE vtiger_asteriskincomingevents SET flag=? WHERE uid=?";
					$adb->pquery($sql, array(-1, $uniqueid));
				} else {
					$query = "INSERT INTO vtiger_asteriskincomingcalls (refuid, from_number, from_name, to_number, callertype, flag, timer) VALUES(?,?,?,?,?,?,?)";
					$adb->pquery($query,array($uniqueid, $callerNumber, $callerName, $extension, $callerType, 0, time()));
				
				}			
			}
		}
		
		return false;
	}
	
	return true;
}

function asterisk_handleResponse3($mainresponse, $adb, $asterisk){
	
	$uid = false;
	$receiver_callerinfo = false;
	
	// Asterisk 1.4 (Event: Link), Asterisk 1.6 (Event: Bride, Bridgestate: Link)
	if($mainresponse['Event'] == 'Link' || ($mainresponse['Event'] == 'Bridge' && $mainresponse['Bridgestate'] == 'Link')){
		
		$uid = $mainresponse['Uniqueid1'];
		$uid2 = $mainresponse['Uniqueid2'];
		$callerNumber = $mainresponse['CallerID1'];
		$extensionCalled = $mainresponse['CallerID2'];
		
		// Ignore the case wheren CallerIDs are same!
		if($callerNumber == $extensionCalled) {
			// case handled but we ignored.
			return false;
		}
		
		$callerType = '';
		$status = "received";
		
		$sourceChannel = $mainresponse['Channel1'];
		
		// Check if Popup has already been shown to user? 
		// Due to (asterisk 1.4 bug: https://issues.asterisk.org/view.php?id=11757)
		// Popup display for Call made to queue is defered and will be handled below
		// So we need to pick up events with (flag = 0, asterisk 1.6) or (flag = -1, asterisk 1.4)
		// asterisk 1.4 - from_number is NULL, 
		// TODO check the state of from_number in asterisk 1.6
		$checkres = $adb->pquery("SELECT * FROM vtiger_asteriskincomingevents WHERE uid=? and (flag = 0 or flag = -1) and (from_number is NULL or from_number = 0)", array($uid));
		if($adb->num_rows($checkres) > 0) {
			if(empty($checkresrow['from_name'])) $checkresrow['from_name'] = "Unknown";
			
			$checkresrow = $adb->fetch_array($checkres);
			$sql = "UPDATE vtiger_asteriskincomingevents SET from_number=?, to_number=?, timer=?, flag=? WHERE uid=?";
			$adb->pquery($sql, array($callerNumber, $extensionCalled, time(), 0, $uid));
			
			// Check if the user has checked Incoming Calls in My Preferences
			if(checkExtension($extensionCalled, $adb)) {
				$query = "INSERT INTO vtiger_asteriskincomingcalls (refuid, from_number, from_name, to_number, callertype, flag, timer) VALUES(?,?,?,?,?,?,?)";
				$adb->pquery($query,array($uid, $callerNumber, $checkresrow['from_name'], $extensionCalled, '', 0, time()));
			}
		}
		// END
	} else if($mainresponse['Event']== 'Newexten' && $mainresponse['AppData'] == "DIALSTATUS=CONGESTION" || $mainresponse['Event'] == 'Hangup'){
		$status = "missed";
		$uid = $mainresponse['Uniqueid'];
		$extensionCalled = false;

	}		
	// TODO Need to detect the caller number using the Event Information
		$callerNumberInfo = $adb->pquery("SELECT from_number, callertype FROM vtiger_asteriskincomingevents WHERE uid=? AND from_number is not NULL LIMIT 1", array($uid));
		if($callerNumberInfo && $adb->num_rows($callerNumberInfo)) {
			$callerNumber = $adb->query_result($callerNumberInfo, 0, 'from_number');
			$receiver_callerinfo = getCallerInfo($callerNumber);
		}
	
	
	if($uid !== false) {
		// Create Record if not yet done and link to the event for further use
		$eventResult = $adb->pquery("SELECT * FROM vtiger_asteriskincomingevents WHERE uid = ? and pbxrecordid is NULL AND flag =0", array($uid));
		
		if($adb->num_rows($eventResult)){
			
			$eventResultRow = $adb->fetch_array($eventResult);
			
			$callerNumber = $eventResultRow['from_number'];
			
			if($extensionCalled === false) {
				$extensionCalled = $eventResultRow['to_number'];
			}
			
			// If we are not knowing the caller informatio (Asterisk 1.4, Event: Link not yet called)
			if($callerNumber != 'Unknown' && $callerNumber != '0') {				
				$pbxrecordid = addToCallHistory($extensionCalled, $callerNumber, 
					$extensionCalled , "incoming-$status", $adb, $receiver_callerinfo);
				$adb->pquery("UPDATE vtiger_asteriskincomingevents SET pbxrecordid = ? WHERE uid = ?", array($pbxrecordid, $uid));
				if(!empty($receiver_callerinfo['id'])) {
					$adb->pquery("UPDATE vtiger_asteriskincomingevents SET relcrmid = ? WHERE uid = ?", array($receiver_callerinfo['id'], $uid));
				}
			}
			return false;
		}
	}
	return true;
}

/**
 * Check if extension is configured to user in vtiger
 */
function checkExtension($ext, $adb){
	$sql = "select 1 from vtiger_asteriskextensions where asterisk_extension=?";
	$result = $adb->pquery($sql, array($ext));
	
	if($adb->num_rows($result)>0){
		return true;
	}else{
		return false;
	}
}
