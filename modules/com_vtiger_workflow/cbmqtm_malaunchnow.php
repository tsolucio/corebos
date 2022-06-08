<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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

require_once 'include/Webservices/ExecuteWorkflow.php';

class cbmqtm_malaunchnow {
	public function MALaunchNow() {
		global $current_user;
		$cbmq = coreBOS_MQTM::getInstance();
		while ($msg = $cbmq->getMessage('wfLaunchNowChannel', 'malaunchnow', 'malaunchnow')) {
			$data = json_decode($msg['information'], true);
			$wfid = $data['wfid'];
			$crmids = $data['crmids'];
			$context = '[]';
			cbwsExecuteWorkflowWithContext($wfid, json_encode($crmids), $context, $current_user);
		}
	}
}