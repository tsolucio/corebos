<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class activatePortalFieldsContacts extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$result = $adb->pquery('SELECT operationid FROM vtiger_ws_operation WHERE name=?', array('loginPortal'));
			if ($result) {
				$operationid = $adb->query_result($result, 0, 'operationid');
				if (isset($operationid)) {
					$chkrs = $adb->pquery('SELECT 1 FROM vtiger_ws_operation_parameters WHERE operationid=? and name=?', array($operationid, 'entity'));
					if ($chkrs && $adb->num_rows($chkrs)==0) {
						$this->ExecuteQuery("INSERT INTO vtiger_ws_operation_parameters (operationid, name, type, sequence) VALUES ($operationid, 'entity', 'String', 3);");
					}
				}
			}
			// enlarge password field
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_portalinfo` CHANGE `user_password` `user_password` VARCHAR(12550) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;',
				array()
			);
			// move language field
			$mvfield = array(
				'Contacts' => array(
					'template_language'
				)
			);
			$this->massMoveFieldsToBlock($mvfield, 'LBL_CUSTOMER_PORTAL_INFORMATION');
			// add new fields
			$fieldLayout=array(
				'Contacts' => array(
					'LBL_CUSTOMER_PORTAL_INFORMATION'=> array(
						'portalpasswordtype' => array(
							'label' => 'portalpasswordtype',
							'columntype'=>'varchar(26)',
							'typeofdata'=>'V~O',
							'uitype'=>'16',
							'displaytype'=>'1',
							'vals' => array(
								'sha512',
								'sha256',
								'md5',
								'plaintext',
							)
						),
						'portalloginuser' => array(
							'label' => 'portalloginuser',
							'columntype'=>'int(11)',
							'typeofdata'=>'I~O',
							'uitype'=>'77',
							'displaytype'=>'1',
						),
					),
				),
			);
			$this->massCreateFields($fieldLayout);
			// activate widget
			$module = Vtiger_Module::getInstance('Contacts');
			if ($module) {
				$module->addLink('DETAILVIEWWIDGET', 'PortalUserPasswordManagement', 'module=Contacts&action=ContactsAjax&file=PortalUserPasswordManagement&recordid=$RECORD$');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		} else {
			if ($this->isApplied()) {
				// move language field
				$mvfield = array(
					'Contacts' => array(
						'template_language'
					)
				);
				$this->massMoveFieldsToBlock($mvfield, 'LBL_CONTACT_INFORMATION');
				$fieldLayout=array(
					'Contacts' => array(
						'portalpasswordtype',
						'portalloginuser',
					),
				);
				$this->massHideFields($fieldLayout);
				$module = Vtiger_Module::getInstance('Contacts');
				if ($module) {
					$module->deleteLink('DETAILVIEWWIDGET', 'PortalUserPasswordManagement', 'module=Contacts&action=ContactsAjax&file=PortalUserPasswordManagement&recordid=$RECORD$');
				}
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone();
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}
}