<?php
/*************************************************************************************************
* Copyright 2021 Spike
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
*************************************************************************************************
*  Module       : Sendgrid Notifications
*************************************************************************************************/
require_once 'vendor/autoload.php';
$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';
use SendGrid\EventWebhook\EventWebhook;
use SendGrid\EventWebhook\EventWebhookHeader;

function evvtWrite2Log($msg) {
	$writeLog = true;
	if ($writeLog) {
		$logFile='logs/sendgridlogs.log';
		error_log("$msg\n", 3, $logFile);
	}
}

/* $input contains this array after json_decode:
array(
	0 => stdClass::__set_state(array(
		'email' => 'joe@tsolucio.com',
		'smtp-id' => '<1381941003.525ebf0b54427@erpevolutivo.com>',
		'timestamp' => 1381941015,
		'response' => '250 2.0.0 Ok: queued as 8786E2F0039 ',
		'category' => '-7',
		'event' => 'delivered',
		'crmid' => 11,
	)),
)
*/
function sendgridsync($input) {
	global $adb, $current_user,$log;
	$sendgridevents = json_decode($input);

	$date=date('l jS \of F Y h:i:s A');
	$LogContent = "****\nSendGrid Notification $date \n";
	if (!is_array($sendgridevents) && !isset($sendgridevents[0])) {
		evvtWrite2Log("$LogContent Error Input Information");
		return 1;
	}
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Messages', true);
	foreach ($sendgridevents as $request) {
		foreach ($request as $key => $value) {
			if (!is_object($value)) {
				$LogContent.= "Key: $key; Value: ".print_r($value, true)." \n";
			}
		}
		evvtWrite2Log($LogContent);

		$recipient=trim($request->email);
		$event=$request->event;
		$crmid = isset($request->crmid) ? $request->crmid : 0;
		$crmtype=getSalesEntityType($crmid);
		if ($crmtype!='Messages' && $crmtype!='Emails') {
			evvtWrite2Log("Error CRM Type: $crmtype - $crmid");
			continue;
		}
		$combid=explode('-', $request->category);
		$category=$combid[0];
		evvtWrite2Log("CRM Type: $crmtype - $crmid");
		$current_user = Users::getActiveAdminUser();
		$em = new VTEventsManager($adb);
		// Initialize Event trigger cache
		$em->initTriggerCache();
		$entityData = VTEntityData::fromEntityId($adb, $crmid);
		//Event triggering code
		$em->triggerEvent('vtiger.entity.beforesave', $entityData);
		//Event triggering code ends
		if ($crmtype=='Messages') {
			$updtable = 'vtiger_messages';
			$updindex = 'messagesid';
		} else {
			$updtable = 'vtiger_emaildetails';
			$updindex = 'emailid';
		}
		$msg = '';
		$query = '';
		switch ($event) {
			case 'open':
				$query="Update $updtable set $event=$event+1 where $updindex=?";
				break;
			case 'bounce':
				$query="Update $updtable set $event=$event+1 where $updindex=?";
				$msg = $request->type.'('.$request->status.') : '.$request->reason;
				break;
			case 'spamreport':
			case 'dropped':  // when sendgrid drops it, it is because we have been warned somehow, better to stop sending
				$msg = $request->reason;
				// fall through intentional
			case 'unsubscribe':
				$query="Update $updtable set $event=1 where $updindex=?";
				// mark emailoptout fields in modules
				if ($crmtype=='Messages') {
					$msgrs = $adb->pquery('select contact_message,account_message,lead_message from vtiger_messages where messagesid=?', array($crmid));
					$messages = $adb->fetch_array($msgrs);
					if (!empty($messages['contact_message'])) {
						$adb->pquery('update vtiger_contactdetails set emailoptout=1 where contactid=?', array($messages['contact_message']));
					}
					if (!empty($messages['account_message'])) {
						$adb->pquery('update vtiger_account set emailoptout=1 where accountid=?', array($messages['account_message']));
					}
					if (!empty($messages['lead_message'])) {
						$adb->pquery('update vtiger_leaddetails set emailoptout=1 where leadid=?', array($messages['lead_message']));
					}
				} else {
					$msgrs = $adb->pquery('select idlists from vtiger_emaildetails where emailid=?', array($crmid));
					$messages = $adb->fetch_array($msgrs);
					$idlists = explode('|', $messages['idlists']);
					foreach ($idlists as $eid) {
						list($id, $void) = explode('@', $eid);
						$cet = getSalesEntityType($id);
						switch ($cet) {
							case 'Contacts':
								$crs = $adb->pquery('select email from vtiger_contactdetails where contactid=?', array($id));
								$ce = $adb->fetch_row($crs);
								if ($ce['email']==$recipient) {
									$adb->pquery('update vtiger_contactdetails set emailoptout=1 where contactid=?', array($id));
									break 2;
								}
								break;
							case 'Accounts':
								$crs = $adb->pquery('select email1 from vtiger_account where accountid=?', array($id));
								$ce = $adb->fetch_row($crs);
								if ($ce['email']==$recipient) {
									$adb->pquery('update vtiger_account set emailoptout=1 where accountid=?', array($id));
									break 2;
								}
								break;
							case 'Leads':
								$crs = $adb->pquery('select email from vtiger_leaddetails where leadid=?', array($id));
								$ce = $adb->fetch_row($crs);
								if ($ce['email']==$recipient) {
									$adb->pquery('update vtiger_leaddetails set emailoptout=1 where leadid=?', array($id));
									break 2;
								}
								break;
						}
					}
				}
				break;
			case 'delivered':
				$query="Update $updtable set $event=1 where $updindex=?";
				$msg = $request->reason;
				break;
			case 'click':
				$query="Update $updtable set clicked=clicked+1 where $updindex=?";
				if ($crmtype=='Messages') {
					$rsdesc = $adb->pquery('select description from '.$crmEntityTable.' where crmid=?', array($crmid));
					$desc = $adb->query_result($rsdesc, 0, 'description');
					$msg = $desc.$request->url.';';
					$adb->pquery('update vtiger_messages set lasturlclicked=? where messagesid=?', array($request->url, $crmid));
				}
		}
		evvtWrite2Log($query);
		if ((!empty($query) || !empty($msg)) && $crmtype=='Messages') {
			if (!empty($msg)) {
				$adb->pquery('update '.$crmEntityTable.' set description=? where crmid=?', array($msg, $crmid));
			}
			$adb->pquery('update vtiger_messages set lasteventtime=now() where messagesid=?', array($crmid));
		}
		if (!empty($query)) {
			$adb->pquery($query, array($crmid));
			$mtime = date('Y-m-d H:i:s');
			$adb->pquery('update '.$crmEntityTable.' set modifiedtime=? where crmid=?', array($crmid, $mtime));
			$adb->pquery('update vtiger_crmobject set modifiedtime=? where crmid=?', array($crmid, $mtime));
			//Event triggering code
			$em->triggerEvent('vtiger.entity.aftersave', $entityData);
			//Event triggering code ends
		}
		$notificationInfo = array(
			'recipient' => $recipient,
			'event' => $event,
			'category' => $category,
			'crmid' => $crmid,
			'crmtype' => $crmtype,
			'eventobject' => $request,
			'query' => $query,
			'message' => $msg,
		);
		cbEventHandler::do_action('sendgrid.NotificationHook', $notificationInfo);
	} // foreach all events
}

function validateSignedNotification($publicValue, $publicKey, $payload) {
	$headers =getallheaders();
	$eventWebhook = new EventWebhook();
	$ecPublicKey = $eventWebhook->convertPublicKeyToECDSA($publicKey);
	return $eventWebhook->verifySignature(
		$ecPublicKey,
		$payload,
		$headers[EventWebhookHeader::SIGNATURE],
		$headers[EventWebhookHeader::TIMESTAMP]
	);
}
?>
