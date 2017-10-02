<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

class addWebdomainWebFormscb extends cbupdaterWorker {
	
	function applyChange() {
		global $adb;
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$restb = $adb->query("SHOW TABLES LIKE 'vtiger_webforms'");
			if($adb->num_rows($restb) > 0){
				$res = $adb->query("SHOW COLUMNS FROM vtiger_webforms LIKE 'web_domain'");
				if($adb->num_rows($res) == 0){
					$this->ExecuteQuery("ALTER TABLE vtiger_webforms ADD web_domain VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
				}else{
					$this->sendMsg('Field web_domain exists!');
				}
			}else{
				$this->sendMsg('Table vtiger_webforms doesn\'t exist!');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(); // this should not be done if changeset is Continuous
		}
		$this->finishExecution();
	}
}