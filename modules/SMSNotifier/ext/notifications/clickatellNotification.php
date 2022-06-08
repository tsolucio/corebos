<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : SMS Integration
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
function clickatellsync($input) {
	global $adb;
	$logFile='logs/clickatell.log';
	$date=date('l jS \of F Y h:i:s A');
	$LogContent = "ClickATell Notification $date \n";
	$request = json_decode($input);
	foreach ($request as $key => $value) {
		if (!is_object($value)) {
			$LogContent.= "$key : $value \n";
		}
	}

	error_log($LogContent.' \n', 3, $logFile);
	$messageid = $request->messageId;
	if ($request->status == 'RECEIVED_BY_RECIPIENT') {
		$unique = $adb->pquery(
			"SELECT smsnotifierid FROM `vtiger_smsnotifier_status` WHERE smsmessageid =? and status = 'Dispatched'",
			array($messageid)
		);
		$smsnotifierid = $adb->query_result($unique, 0, 0);
		$responseStatus = 'Delivered';
		$needlookup = 0;
		$adb->pquery(
			'UPDATE vtiger_smsnotifier_status SET status=?, needlookup=? WHERE smsmessageid = ?',
			array($responseStatus, $needlookup, $messageid)
		);
		$adb->pquery('UPDATE vtiger_smsnotifier SET status=? WHERE smsnotifierid = ?', array($responseStatus, $smsnotifierid));
	}
}