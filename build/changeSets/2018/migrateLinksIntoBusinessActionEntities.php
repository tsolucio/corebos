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

include_once 'modules/BusinessActions/BusinessActions.php';
include_once 'modules/Users/Users.php';

class migrateLinksIntoBusinessActionEntities extends cbupdaterWorker {

	public function applyChange() {

		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			if ($this->isModuleInstalled('BusinessActions')) {
                vtlib_toggleModuleAccess('BusinessActions', true);
				global $adb;

				$collectLinksSql ="SELECT linktype, 
                                          linklabel, 
                                          linkurl,
                                          handler_path,
                                          onlyonmymodule,
                                          handler_class,
                                          linkicon,
                                          handler,
                                          (SELECT vtiger_tab.name FROM vtiger_tab WHERE vtiger_tab.tabid = vtiger_links.tabid) AS module_list
                                     FROM vtiger_links";

				$collectedLinks = $adb->pquery($collectLinksSql, array());
				$adminId = Users::getActiveAdminID();

				while ($link = $adb->fetch_array($collectedLinks)) {
					$focusnew = new BusinessActions();
					$focusnew->column_fields['assigned_user_id'] = $adminId;
					$focusnew->column_fields['linktype'] = $link['linktype'];
					$focusnew->column_fields['linklabel'] = $link['linklabel'];
					$focusnew->column_fields['linkurl'] = html_entity_decode($link['linkurl'], ENT_QUOTES, 'UTF-8');
					$focusnew->column_fields['sequence'] = 0;
					$focusnew->column_fields['module_list'] = $link['module_list'];
					$focusnew->column_fields['handler_path'] = $link['handler_path'];
					$focusnew->column_fields['onlyonmymodule'] = $link['onlyonmymodule'];
					$focusnew->column_fields['handler_class'] = $link['handler_class'];
					$focusnew->column_fields['linkicon'] = $link['linkicon'];
					$focusnew->column_fields['handler'] = $link['handler'];
					$focusnew->column_fields['active'] = 1;
					$focusnew->save('BusinessActions');
				}

				$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
				$this->sendMsg('The vtiger links were migrated successfully into Business Action entities');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}