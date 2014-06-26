<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class vt54_cb54 extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("update vtiger_field set block=67 where tabid=23 and columnname='s_h_amount'");
			$this->ExecuteQuery("ALTER TABLE vtiger_loginhistory CHANGE user_name user_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
			$delimg = array('include/images/AppStore.png',
					'include/images/AppStoreQRCode.png',
					'include/images/ExchangeConnector.png',
					'include/images/GooglePlay.png',
					'include/images/GooglePlayQRCode.png',
					'include/images/OutlookPlugin.png',
					'include/images/vtigercrm_icon.ico',
					'themes/alphagrey/images/vtiger-crm.gif',
					'themes/bluelagoon/images/vtiger-crm.gif',
					'themes/images/aboutUS.jpg',
					'themes/images/bullets.gif',
					'themes/images/honestCRM.gif',
					'themes/images/honestCRMTop.gif',
					'themes/images/loginTopHeaderBg.gif',
					'themes/images/loginTopHeaderName.gif',
					'themes/images/loginTopVersion.gif',
					'themes/images/vtiger-paw.jpg',
					'themes/images/vtiger.jpg',
					'themes/images/vtigerName.gif',
					'themes/images/vtigercrm_icon.ico',
					'themes/images/vtigerlogo.jpg',
					'themes/softed/images/vtiger-crm.gif',
					'themes/woodspice/images/vtiger-crm.gif',
					'test/logo/vtiger-crm-logo.gif',
			);
			foreach ($delimg as $dimg) {
				@unlink($dimg);
				$this->sendMsg("image $dimg deleted");
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		$this->finishExecution();
	}
	
}