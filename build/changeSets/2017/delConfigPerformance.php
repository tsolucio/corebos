<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class delConfigPerformance extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->sendMsg('This changeset eliminates the config.performance.php file');
			$this->sendMsg("Some variables have been moved to config.inc.php and the rest to global variables.");

			/* Migrate these variables to Global Variables
			 **********************************************************/
			// LISTVIEW_RECORD_CHANGE_INDICATOR, LISTVIEW_DEFAULT_SORTING, LISTVIEW_COMPUTE_PAGE_COUNT
			// HOME_PAGE_WIDGET_GROUP_SIZE, NOTIFY_OWNER_EMAILS, DETAILVIEW_RECORD_NAVIGATION
			@include 'config.performance.php';
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
			if (isset($PERFORMANCE_CONFIG) and isset($PERFORMANCE_CONFIG['LISTVIEW_RECORD_CHANGE_INDICATOR']) and !$PERFORMANCE_CONFIG['LISTVIEW_RECORD_CHANGE_INDICATOR']) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_ListView_Record_Change_Indicator';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (isset($PERFORMANCE_CONFIG) and isset($PERFORMANCE_CONFIG['LISTVIEW_DEFAULT_SORTING']) and $PERFORMANCE_CONFIG['LISTVIEW_DEFAULT_SORTING']) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_ListView_Default_Sorting';
				$rec['value'] = 1;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (isset($PERFORMANCE_CONFIG) and isset($PERFORMANCE_CONFIG['LISTVIEW_COMPUTE_PAGE_COUNT']) and $PERFORMANCE_CONFIG['LISTVIEW_COMPUTE_PAGE_COUNT']) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_ListView_Compute_Page_Count';
				$rec['value'] = 1;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (isset($PERFORMANCE_CONFIG) and isset($PERFORMANCE_CONFIG['DETAILVIEW_RECORD_NAVIGATION']) and !$PERFORMANCE_CONFIG['DETAILVIEW_RECORD_NAVIGATION']) {
				$rec = $default_values;
				$rec['gvname'] = 'Application_DetailView_Record_Navigation';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (isset($PERFORMANCE_CONFIG) and isset($PERFORMANCE_CONFIG['NOTIFY_OWNER_EMAILS']) and !$PERFORMANCE_CONFIG['NOTIFY_OWNER_EMAILS']) {
				$rec = $default_values;
				$rec['gvname'] = 'HelpDesk_Notify_Owner_EMail';
				$rec['value'] = 0;
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			if (isset($PERFORMANCE_CONFIG) and isset($PERFORMANCE_CONFIG['HOME_PAGE_WIDGET_GROUP_SIZE']) and $PERFORMANCE_CONFIG['HOME_PAGE_WIDGET_GROUP_SIZE'] != 12) {
				$rec = $default_values;
				$rec['gvname'] = 'HomePage_Widget_Group_Size';
				$rec['value'] = $PERFORMANCE_CONFIG['HOME_PAGE_WIDGET_GROUP_SIZE'];
				vtws_create('GlobalVariable', $rec, $current_user);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
