<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************/
class StandAlone extends cbupdaterWorker {

	public function applyChange() {

		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$moduleToDeactivate = array('ModTracker', 'VtigerBackup', 'Dashboard', 'Actions', 'cbAuditTrail', 'Timecontrol', 'Timeline', 'TimecontrolInv', 'cbgmp', 'Task', 'SubsList', 'OpenStreetMap', 'MarketingDashboard', 'vtmessages', 'Messages', 'Forecasts', 'vtsendgrid', 'TCTotals', 'PriceBooks', 'PlannedActions', 'HelpDesk', 'Conversation', 'Leads', 'Portal', 'Emails', 'Faq', 'Rss', 'Campaigns', 'ConfigEditor', 'MailManager', 'Mobile',  'PBXManager', 'ServiceContracts', 'Services', 'CronTasks', '$adb->', 'ProjectMilestone', 'ProjectTask', 'Project', 'SMSNotifier', 'Webforms', 'cbLoginHistory', 'cbTermConditions', 'cbSurvey', 'cbSurveyQuestion', 'cbSurveyDone', 'cbSurveyAnswer', 'PALaunch', 'Assets', 'cbtranslation', 'BusinessActions');
			$fieldToDeactivate = array(
				'Accounts' => array(
					'tickersymbol', 'account_id', 'employees', 'accounttype', 'isconvertedfromlead', 'industry', 'ownership', 'annual_revenue', 'notify_owner', 'cf_718', 'cf_720', 'cf_724', 'cf_723', 'cf_725', 'cf_721', 'cf_719', 'commentadded', 'description',
				) ,
				'Invoice' => array(
					'exciseduty', 'modifiedtime', 'salescommission', 'createdtime',
				),
				'PurchaseOrder' => array(
					'salescommission', 'exciseduty'
				),
				'SalesOrder' => array(
					'exciseduty', 'salescommission'
				),
				'Quotes' => array(
					'carrier', 'assigned_user_id1', 'shipping'));
			$removeFromMenu = array(
				'cbtranslation', 'BusinessActions', 'cbTermConditions', 'CronTasks'
				);
			foreach ($moduleToDeactivate as $module) {
				if ($this->isModuleInstalled($module)) {
					if (in_array($module, $removeFromMenu)) {
						$this->removeAllMenuEntries($module);
					} else {
						vtlib_toggleModuleAccess($module, false);
						$this->sendMsg("$module deactivated!");
					}
				}
			}
			$this->massHideFields($fieldToDeactivate);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}