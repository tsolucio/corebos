<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class updateSettingsIcons extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'people' where name = 'LBL_USERS' and description = 'LBL_USER_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'user_role' where name = 'LBL_ROLES' and description = 'LBL_ROLE_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'user' where name = 'LBL_PROFILES' and description = 'LBL_PROFILE_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'groups' where name = 'USERGROUPLIST' and description = 'LBL_GROUP_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'privately_shared' where name = 'LBL_SHARING_ACCESS' and description = 'LBL_SHARING_ACCESS_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'resource_absence' where name = 'LBL_FIELDS_ACCESS' and description = 'LBL_SHARING_FIELDS_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'component_customization' where name = 'VTLIB_LBL_MODULE_MANAGER' and description = 'VTLIB_LBL_MODULE_MANAGER_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'record_update' where name = 'LBL_PICKLIST_EDITOR' and description = 'LBL_PICKLIST_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'setup' where name = 'LBL_PICKLIST_DEPENDENCY_SETUP' and description = 'LBL_PICKLIST_DEPENDENCY_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'company.gif' where name = 'LBL_COMPANY_DETAILS' and description = 'LBL_COMPANY_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'hierarchy' where name = 'LBL_MAIL_SERVER_SETTINGS' and description = 'LBL_MAIL_SERVER_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'money' where name = 'LBL_CURRENCY_SETTINGS' and description = 'LBL_CURRENCY_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'adjust_value' where name = 'LBL_TAX_SETTINGS' and description = 'LBL_TAX_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'money' where name = 'LBL_PROXY_SETTINGS' and description = 'LBL_PROXY_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'set-IcoTwoTabConfig.gif' where name = 'LBL_DEFAULT_MODULE_VIEW' and description = 'LBL_DEFAULT_MODULE_VIEW_DESC'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'richtextnumberedlist' where name = 'LBL_CUSTOMIZE_MODENT_NUMBER' and description = 'LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'email' where name = 'LBL_MAIL_SCANNER' and description = 'LBL_MAIL_SCANNER_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'flow_alt' where name = 'LBL_LIST_WORKFLOWS' and description = 'LBL_LIST_WORKFLOWS_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'menueditor.png' where name = 'LBL_MENU_EDITOR' and description = 'LBL_MENU_DESC'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'list' where name = 'LBL_WORKFLOW_LIST' and description = 'LBL_AVAILABLE_WORKLIST_LIST'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'migrate.gif' where name = 'Configuration Editor' and description = 'Update configuration file of the application'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'set-IcoLoginHistory.gif' where name = 'ModTracker' and description = 'LBL_MODTRACKER_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'date_input' where name = 'Scheduler' and description = 'Allows you to Configure Cron Task'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'modules/FieldFormulas/resources/FieldFormulas.png' where name = 'LBL_FIELDFORMULAS' and description = 'LBL_FIELDFORMULAS_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'quickview.png' where name = 'LBL_TOOLTIP_MANAGEMENT' and description = 'LBL_TOOLTIP_MANAGEMENT_DESCRIPTION'");
			$this->ExecuteQuery("update vtiger_settings_field set iconpath = 'record_create' where name = 'LBL_MODULE_BUILDER' and description = 'LBL_MODULE_BUILDER_DESCRIPTION'");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
