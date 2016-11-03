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
*************************************************************************************************/

class delSettingsSinglePaneViewConfigEditor extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->sendMsg('This changeset eliminates the Single Pane View quick access Settings section');
			$this->sendMsg("From now on you must create an Application_Single_Pane_View global variable for this functionality");
			$this->ExecuteQuery('UPDATE vtiger_settings_field SET active=? WHERE vtiger_settings_field.name = ?', array('1', 'LBL_DEFAULT_MODULE_VIEW'));
			$this->sendMsg('This changeset eliminates the Configuration Editor Settings section');
			$this->sendMsg("From now on you must create global variables for this functionality");
			$this->ExecuteQuery('UPDATE vtiger_settings_field SET active=? WHERE vtiger_settings_field.name = ?', array('1', 'Configuration Editor'));
			/* Migrate these variables to Global Variables
			 **********************************************************/
			// $CALENDAR_DISPLAY, $WORLD_CLOCK_DISPLAY, $CALCULATOR_DISPLAY, $USE_RTE, $HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_REPLY_ID;
			// $upload_maxsize, $allow_exports, $list_max_entries_per_page, $default_module, $default_action,  $listview_max_textlength, $cors_enabled_domains;
			include 'config.inc.php';
			include_once 'include/Webservices/Create.php';
			global $current_user;
			$module = 'GlobalVariable';
			if ($this->isModuleInstalled($module)) {
				vtlib_toggleModuleAccess($module,true);
				$this->sendMsg("$module activated!");
			} else {
				$this->installManifestModule($module);
			}
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$default_values =  array(
				'default_check' => '1',
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => '',
				'category' => 'Application',
				'in_module_list' => '',
				'assigned_user_id' => $usrwsid,
			);
			if (!empty($CALENDAR_DISPLAY) and $CALENDAR_DISPLAY != 'true') {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Display_Mini_Calendar';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($WORLD_CLOCK_DISPLAY) and $WORLD_CLOCK_DISPLAY != 'true') {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Display_World_Clock';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($CALCULATOR_DISPLAY) and $CALCULATOR_DISPLAY != 'true') {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Display_Calculator';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($USE_RTE) and $USE_RTE != 'true') {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Use_RTE';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($HELPDESK_SUPPORT_EMAIL_ID)) {
				$rec = $default_values;
				$rec['gvname'] = 'HelpDesk_Support_EMail';
				$rec['value'] = $HELPDESK_SUPPORT_EMAIL_ID;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($HELPDESK_SUPPORT_NAME)) {
				$rec = $default_values;
				$rec['gvname'] = 'HelpDesk_Support_Name';
				$rec['value'] = $HELPDESK_SUPPORT_NAME;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($HELPDESK_SUPPORT_EMAIL_REPLY_ID)) {
				$rec = $default_values;
				$rec['gvname'] = 'HelpDesk_Support_Reply_EMail';
				$rec['value'] = $HELPDESK_SUPPORT_EMAIL_REPLY_ID;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($upload_maxsize)) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Upload_MaxSize';
				$rec['value'] = $upload_maxsize;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($allow_exports)) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Allow_Exports';
				$rec['value'] = $allow_exports;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($list_max_entries_per_page)) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_ListView_PageSize';
				$rec['value'] = $list_max_entries_per_page;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($listview_max_textlength)) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_ListView_Max_Text_Length';
				$rec['value'] = $listview_max_textlength;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($default_module)) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Default_Module';
				$rec['value'] = $default_module;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($default_action)) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Default_Action';
				$rec['value'] = $default_action;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($cors_enabled_domains)) {
				$rec = $default_values;
				$rec['gvname'] = 'Webservice_CORS_Enabled_Domains';
				$rec['value'] = $cors_enabled_domains;
				$rec['category'] = 'Security';
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($MINIMUM_CRON_FREQUENCY) and $MINIMUM_CRON_FREQUENCY != 15) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Minimum_Cron_Frequency';
				$rec['value'] = $MINIMUM_CRON_FREQUENCY;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($PORTAL_URL) and $PORTAL_URL != 'http://your_support_domain.tld/customerportal') {
				$rec = $default_values;
				$rec['gvname'] = 'Application_Customer_Portal_URL';
				$rec['value'] = $PORTAL_URL;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (!empty($display_empty_home_blocks) and $display_empty_home_blocks != false) {
				$rec = $default_values;
				$rec['gvname'] = 'Home_Display_Empty_Blocks';
				$rec['value'] = 1;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
