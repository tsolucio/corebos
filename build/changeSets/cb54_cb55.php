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

class cb54_cb55 extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$delfile = array('cron/class.phpmailer.php',
					'cron/class.smtp.php',
					'cron/language/phpmailer.lang-en.php',
					'cron/sendreminder.sh',
					'modules/Calendar/SendReminder.bat',
					'SendReminder.php',
					'install/SelectOptionalModules.php',
					'install/buildModules.php',
					'modules/Emails/language/phpmailer.lang-de_de.php',
					'modules/Emails/language/phpmailer.lang-en_gb.php',
					'modules/Emails/language/phpmailer.lang-en_us.php',
					'modules/Emails/language/phpmailer.lang-es_es.php',
					'modules/Emails/language/phpmailer.lang-es_mx.php',
					'modules/Emails/language/phpmailer.lang-fr_fr.php',
					'modules/Emails/language/phpmailer.lang-hu_hu.php',
					'modules/Emails/language/phpmailer.lang-nl_nl.php',
					'modules/Emails/language/phpmailer.lang-pt_br.php',
					'modules/Emails/language/phpmailer.lang-zh_cn.php',
					'include/js/hu_hu.lang.js.bak',
					'include/language/nl_nl.lang.php.bak',
					'modules/Home/language/nl_nl.lang.php.bak',
					'modules/Mobile/api/ws/models/alerts/Projects.php~',
					'modules/Mobile/api/ws/models/alerts/ProjectTasksOfMine.php~',
					'modules/imports/Excel/empty',
					'upgrade2coreBOS.php',
					'installupdater.php',
					'vtlib/ModuleDir/5.0.4/CallRelatedList.php',
					'vtlib/ModuleDir/5.0.4/CustomView.php',
					'vtlib/ModuleDir/5.0.4/Delete.php',
					'vtlib/ModuleDir/5.0.4/DetailViewAjax.php',
					'vtlib/ModuleDir/5.0.4/DetailView.php',
					'vtlib/ModuleDir/5.0.4/EditView.php',
					'vtlib/ModuleDir/5.0.4/ExportRecords.php',
					'vtlib/ModuleDir/5.0.4/Import.php',
					'vtlib/ModuleDir/5.0.4/index.php',
					'vtlib/ModuleDir/5.0.4/language/en_us.lang.php',
					'vtlib/ModuleDir/5.0.4/ListView.php',
					'vtlib/ModuleDir/5.0.4/ModuleFileAjax.php',
					'vtlib/ModuleDir/5.0.4/ModuleFile.js',
					'vtlib/ModuleDir/5.0.4/ModuleFile.php',
					'vtlib/ModuleDir/5.0.4/Popup.php',
					'vtlib/ModuleDir/5.0.4/QuickCreate.php',
					'vtlib/ModuleDir/5.0.4/Save.php',
					'vtlib/ModuleDir/5.0.4/TagCloud.php',
					'vtlib/ModuleDir/5.0.4/updateRelations.php',
			);
			foreach ($delfile as $dimg) {
				@unlink($dimg);
				$this->sendMsg("image $dimg deleted");
			}
			@rmdir('modules/imports/Excel');
			@rmdir('modules/imports');
			@rmdir('vtlib/ModuleDir/5.0.4');
			$this->sendMsg("modules/imports/Excel and vtlib/ModuleDir/5.0.4 deleted");
			// I intentionally leave packages/Vtiger
			Vtiger_Version::updateVersionDatabase('5.5.0');
			Vtiger_Version::updateVersionFile('5.5.0');
			$this->sendMsg('Updated to VERSION 5.5.0 !!!  <b>WELCOME!</b>');
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