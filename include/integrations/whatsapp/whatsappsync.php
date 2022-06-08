<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Whatsapp Integration
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
function whatsappsync($input) {
	global $adb;
	$logFile='logs/whatsapp.log';
	$date=date('l jS \of F Y h:i:s A');
	$LogContent = "WHATSAPP Notification $date \n";
	error_log($LogContent.' \n', 3, $logFile);
	error_log($input.' \n', 3, $logFile);
	$findparams = explode('&', $input);
	$eventype = '';
	$msid = '';
	foreach ($findparams as $rec) {
		$head = explode('=', $rec);
		if ($head[0] == 'EventType') {
			$eventype = $head[1];
		} elseif ($head[0] == 'MessageSid') {
			$msid = $head[1];
		}
	}
	if ($eventype == 'READ') {
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Messages');
		$unique = $adb->pquery(
			'select messagesid,open from vtiger_messages join '.$crmEntityTable.' on crmid=messagesid where deleted=0 and messagesuniqueid=?',
			array($msid)
		);
		$mesid = $adb->query_result($unique, 0, 0);
		$open = $adb->query_result($unique, 0, 1);
		$adb->pquery('update vtiger_messages set open=? where messagesid=?', array($open+1, $mesid));
	}
}