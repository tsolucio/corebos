<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class mauticContactsAndAccountsFields extends cbupdaterWorker {
	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;

			$emm = new VTEntityMethodManager($adb);
			$emm->addEntityMethod("Contacts", "mauticContactDelete", "modules/Contacts/workflow/Mautic.php", "mauticContactDelete");
			$emm->addEntityMethod("Accounts", "mauticAccountCreate", "modules/Accounts/workflow/Mautic.php", "mauticAccountCreate");
			$emm->addEntityMethod("Accounts", "mauticAccountUpdate", "modules/Accounts/workflow/Mautic.php", "mauticAccountUpdate");
			$emm->addEntityMethod("Accounts", "mauticAccountDelete", "modules/Accounts/workflow/Mautic.php", "mauticAccountDelete");

			$fieldLayout = array(
				'Contacts' => array(
					'LBL_CONTACT_INFORMATION' => array(
						'deleted_in_mautic' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>'56',
							'displaytype'=>'1',
							'label'=>'Deleted in Mautic',
						),
						'do_not_contact' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>'56',
							'displaytype'=>'1',
							'label'=>'Do Not Contact',
						),
						'contact_points' => array(
							'columntype'=>'integer(200)',
							'typeofdata'=>'N~O',
							'uitype'=>'7',
							'displaytype'=>'1',
							'label'=>'Contact Points',
						)
					)
				),
				'Accounts' => array(
					'LBL_ACCOUNT_INFORMATION' => array(
						'mautic_id' => array(
							'columntype'=>'varchar(200)',
							'typeofdata'=>'V~O',
							'uitype'=>'1',
							'displaytype'=>'1',
							'label'=>'Mautic ID',
						),
						'deleted_in_mautic' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>'56',
							'displaytype'=>'1',
							'label'=>'Deleted in Mautic',
						),
						'do_not_contact' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>'56',
							'displaytype'=>'1',
							'label'=>'Do Not Contact',
						),
						'from_externalsource' => array(
							'columntype'=>'varchar(200)',
							'typeofdata'=>'V~O',
							'uitype'=>'1',
							'displaytype'=>'1',
							'label'=>'From External Source',
						)
					)
				)
			);
			$this->massCreateFields($fieldLayout);

			$this->markApplied(true);
		}
		$this->finishExecution();
	}
}
?>