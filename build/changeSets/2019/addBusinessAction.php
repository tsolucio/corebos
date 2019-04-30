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
include_once 'include/utils/CommonUtils.php';

class addBusinessAction extends cbupdaterWorker {
	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$tabid = getTabid('Faq');
            $type = 'DETAILVIEWBASIC';
			$label = 'LBL_SENDMAIL_BUTTON_LABEL';
			$url = "sendmail('$MODULE$', $RECORD$,'', 'send_mail_for_FAQ');";
			$iconpath = 'themes/images/sendmail.png';
			$handlerInfo = null;
			$onlyonmymodule = true;
			$rsCbmap = $adb->query("SELECT cbmapid FROM vtiger_cbmap INNER JOIN vtiger_crmentity 
			ON vtiger_crmentity.crmid=vtiger_cbmap.cbmapid
			WHERE mapname='SendEmail_ConditionExpression' AND vtiger_crmentity.deleted = 0 ");
			$cbmap = $adb->fetch_array($rsCbmap);
			$brmap = $cbmap['cbmapid'];
			BusinessActions::addLink($tabid, $type, $label, $url, $iconpath, 0, $handlerInfo, $onlyonmymodule, $brmap);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}