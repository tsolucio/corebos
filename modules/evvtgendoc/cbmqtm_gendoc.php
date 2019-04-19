<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************/
function cbmqtm_Consumelog() {
	global $adb;
	$cbmq = coreBOS_MQTM::getInstance();
	$msg = $cbmq->getMessage('WSGendocChannel', 'gendoclogger', 'gendoc');
	$logarray = unserialize($msg['information']);
	$uname = getUserName($logarray['user']);
	$adb->pquery(
		'INSERT INTO cb_evvtgendoc_log (`date`,ip,user_id,docsize,params,gdtime,totaltime,user_name) VALUES (?,?,?,?,?,?,?)',
		array($msg['senton'], $logarray['ip'], $logarray['user'], $logarray['docsize'], $logarray['params'], $logarray['gdtime'], $logarray['totaltime'], $uname)
	);
}