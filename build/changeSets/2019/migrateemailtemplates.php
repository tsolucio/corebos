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
 *************************************************************************************************/

class migrateemailtemplates extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			global $adb, $default_charset, $current_user;
			include_once 'modules/MsgTemplate/MsgTemplate.php';
			$_REQUEST['assigntype'] = 'U';
			$rsAccs = $adb->query("show TABLES like 'vtiger_emailtemplates'");
			if ($adb->num_rows($rsAccs)>0) {
				$rsAccs = $adb->query('SELECT * FROM vtiger_emailtemplates WHERE deleted = 0');
				while ($acc = $adb->fetch_array($rsAccs)) {
					$focus = new MsgTemplate();
					$focus->id = '';
					$focus->mode = '';
					$focus->column_fields['reference'] = html_entity_decode($acc['templatename'], ENT_QUOTES, $default_charset);
					$focus->column_fields['msgt_type'] = 'Email';
					$focus->column_fields['msgt_status'] = 'Active';
					$focus->column_fields['msgt_language'] = 'en';
					$focus->column_fields['subject'] = html_entity_decode($acc['subject'], ENT_QUOTES, $default_charset);
					$focus->column_fields['template'] = html_entity_decode($acc['body'], ENT_QUOTES, $default_charset);
					$focus->column_fields['templateonlytext'] = html_entity_decode(strip_tags(html_entity_decode($acc['body'], ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
					$focus->column_fields['tags'] = '';
					$focus->column_fields['description'] = html_entity_decode($acc['description'], ENT_QUOTES, $default_charset);
					$focus->column_fields['assigned_user_id'] = $current_user->id;
					$focus->save('MsgTemplate');
				}
				$adb->query("delete from vtiger_settings_field where name='EMAILTEMPLATES'");
			}
			$rsAccs = $adb->query("show TABLES like 'vtiger_actions'");
			if ($adb->num_rows($rsAccs)>0) {
				$rsAccs = $adb->query('SELECT * FROM vtiger_actions INNER join vtiger_crmentity ON crmid=actionsid WHERE deleted=0');
				while ($acc = $adb->fetch_array($rsAccs)) {
					$focus = new MsgTemplate();
					$focus->id = '';
					$focus->mode = '';
					$focus->column_fields['reference'] = html_entity_decode($acc['reference'], ENT_QUOTES, $default_charset);
					$focus->column_fields['msgt_type'] = $acc['actions_type'];
					$focus->column_fields['msgt_status'] = $acc['actions_status'];
					$focus->column_fields['msgt_language'] = $acc['actions_language'];
					$focus->column_fields['subject'] = html_entity_decode($acc['subject'], ENT_QUOTES, $default_charset);
					$focus->column_fields['template'] = html_entity_decode($acc['template'], ENT_QUOTES, $default_charset);
					$focus->column_fields['templateonlytext'] = html_entity_decode(strip_tags(html_entity_decode($acc['templateonlytext'], ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
					$focus->column_fields['tags'] = $acc['tags'];
					$focus->column_fields['description'] = html_entity_decode($acc['description'], ENT_QUOTES, $default_charset);
					$focus->column_fields['assigned_user_id'] = $acc['smownerid'];
					$focus->save('MsgTemplate');
				}
			}
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
