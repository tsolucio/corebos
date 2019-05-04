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
include_once 'modules/BusinessActions/BusinessActions.php';
include_once 'modules/Users/Users.php';
include_once 'include/Webservices/Create.php';
include_once 'include/utils/CommonUtils.php';

class addMassTagFunctionality extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			if ($this->isModuleInstalled('BusinessActions')) {
				vtlib_toggleModuleAccess('BusinessActions', true);
				global $current_user;
				$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
				$rec = array(
					'mapname' => 'MassTagButtonShow',
					'maptype' => 'Condition Expression',
					'targetname' => 'Accounts',
					'content' => '<map>
<function>
	<name>getTagCloudView</name>
	<parameters>
		<parameter>currentuserID</parameter>
	</parameters>
</function>
</map>',
					'description' => 'Condition to show/hide the mass tag button',
					'assigned_user_id' => $usrwsid,
				);
				$brule = vtws_create('cbMap', $rec, $current_user);
				$idComponents = vtws_getIdComponents($brule['id']);
				$bruleId = isset($idComponents[1]) ? $idComponents[1] : 0;
				foreach (array('Accounts', 'Contacts', 'Leads') as $modulename) {
					BusinessActions::addLink(getTabid($modulename), 'LISTVIEWBASIC', 'Mass Tag', 'javascript:showMassTag();', '', 0, null, false, $bruleId);
				}
				$this->sendMsg('Changeset '.get_class($this).' applied!');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}
?>