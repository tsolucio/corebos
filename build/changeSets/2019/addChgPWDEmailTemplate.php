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
include_once 'include/Webservices/Create.php';

class addChgPWDEmailTemplate extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			global $adb, $current_user;
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$rec = array(
				'assigned_user_id' => $usrwsid,
				'reference' => 'Password Change Template',
				'msgt_type' => 'Email',
				'msgt_status' => 'Active',
				'msgt_language' => 'en',
				'msgt_module' => 'Users',
				'msgt_fields' => '',
				'msgt_metavars' => '',
				'subject' => 'User password change details',
				'template' => '<p>Dear $user_name$,</p>

<p> </p>

<p>Your password has been successfully changed, your new password is $user_password$</p>',
				'templateonlytext' => 'Dear $user_name$,

Your password has been successfully changed, your new password is $user_password$',
				'tags' => '',
				'msgt_category' => '--None--',
				'description' => 'Send password change email to user',
			);
			vtws_create('MsgTemplate', $rec, $current_user);
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}