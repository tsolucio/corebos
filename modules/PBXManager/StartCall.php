<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
 
/**
 * this function starts the call, it writes the caller and called information to database where it is picked up from
 */
 echo startCall();
 
 
 
function startCall(){	
	global $current_user, $adb,$log;
	require_once 'include/utils/utils.php';
	require_once 'modules/PBXManager/utils/AsteriskClass.php';
	require_once('modules/PBXManager/AsteriskUtils.php');
	
	$id = $current_user->id;
	$number = $_REQUEST['number'];
	$record = $_REQUEST['recordid'];
	$result = $adb->query("select * from vtiger_asteriskextensions where userid=".$current_user->id);
	$extension = $adb->query_result($result, 0, "asterisk_extension");
	$data = getAsteriskInfo($adb);
	if(!empty($data)){
		$server = $data['server'];
		$port = $data['port'];
		$username = $data['username'];
		$password = $data['password'];
		$version = $data['version'];
		$errno = $errstr = NULL;
		$sock = fsockopen($server, $port, $errno, $errstr, 1);
		stream_set_blocking($sock, false);
		if( $sock === false ) {
			echo "Socket cannot be created due to error: $errno:  $errstr\n";
			$log->debug("Socket cannot be created due to error:   $errno:  $errstr\n");
			exit(0);
		}
		$asterisk = new Asterisk($sock, $server, $port);

		loginUser($username, $password, $asterisk);

		$asterisk->transfer($extension,$number);

		$callerModule = getSalesEntityType($record);
		$entityNames = getEntityName($callerModule, array($record));
		$callerName = $entityNames[$record];
		$callerInfo = array('id'=>$record, 'module'=>$callerModule, 'name'=>$callerName);

		//adds to pbx manager
		addToCallHistory($extension, $extension, $number, "outgoing", $adb, $callerInfo);

		// add to the records activity history
		addOutgoingcallHistory($current_user ,$extension,$record ,$adb);
	}		
}
?>
