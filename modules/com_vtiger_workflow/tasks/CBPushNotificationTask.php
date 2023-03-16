<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

class CBPushNotificationTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;
	public $url_query;

	public function getFieldNames() {
		return array('url_query');
	}

	public function doTask(&$entity) {
		global $logbg;
		$logbg->debug('> PushNotificationTask');
		if (!empty($this->url_query)) {
			list($ent, $ent_id) = explode('x', $entity->getId());
			$url = getMergedDescriptionForURL(vtlib_purify($this->url_query), $ent_id, 0);
			$inBucketServeUrl = GlobalVariable::getVariable('Debug_Email_Send_To_Inbucket', '');
			if (!empty($inBucketServeUrl)) {
				require_once 'modules/Emails/mail.php';
				require_once 'modules/Emails/Emails.php';
				$logmsg = "(PushNotificationTask) sending email to inbucket ($url)";
				return send_mail('Email', 'push@notification.tld', 'corebos inbucket', 'corebos@inbucket.tld', 'Push Notification', $url);
			} else {
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($curl);
				$logmsg = "(PushNotificationTask) curl ($url) result: $result";
			}
		} else {
			$logmsg = '(PushNotificationTask) not called: the url_query is empty';
		}
		$logbg->debug($logmsg);
		$this->logmessages[] = $logmsg;
		$logbg->debug('< PushNotificationTask');
	}
}
?>