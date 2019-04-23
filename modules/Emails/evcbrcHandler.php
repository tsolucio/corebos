<?php
/*+**********************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of coreBOSMail Integration.
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
 ************************************************************************************/

class evcbrcHandler extends VTEventHandler {

	public function handleEvent($eventName, $entityData) {

		if ($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action
		}

		if ($eventName == 'vtiger.entity.aftersave') {
			// Entity has been saved, take next action
			global $adb;
			$moduleName = $entityData->getModuleName();
			$crmId = $entityData->getId();
			switch ($moduleName) {
				case 'HelpDesk':
					$rs = $adb->pquery('select parent_id from vtiger_troubletickets WHERE ticketid = ?', array($crmId));
					$relid = $adb->query_result($rs, 0, 0);
					$email = $this->getEmail($relid);
					if (!empty($email)) {
						$adb->pquery('UPDATE vtiger_troubletickets SET email = ? WHERE ticketid = ?', array($email,$crmId));
					}
					break;
				case 'Potentials':
					$rs = $adb->pquery('select related_to from vtiger_potential WHERE potentialid = ?', array($crmId));
					$relid = $adb->query_result($rs, 0, 0);
					$email = $this->getEmail($relid);
					if (!empty($email)) {
						$adb->pquery('UPDATE vtiger_potential SET email = ? WHERE potentialid = ?', array($email, $crmId));
					}
					break;
				case 'Project':
					$rs = $adb->pquery('select linktoaccountscontacts from vtiger_project WHERE projectid = ?', array($crmId));
					$relid = $adb->query_result($rs, 0, 0);
					$email = $this->getEmail($relid);
					if (!empty($email)) {
						$adb->pquery("UPDATE vtiger_project SET email = ? WHERE projectid = ?", array($email, $crmId));
					}
					break;
				case 'ProjectTask':
					$rs = $adb->pquery('select projectid from vtiger_projecttask WHERE projecttaskid = ?', array($crmId));
					$prjid = $adb->query_result($rs, 0, 0);
					$rs = $adb->pquery('select linktoaccountscontacts from vtiger_project WHERE projectid = ?', array($prjid));
					$relid = $adb->query_result($rs, 0, 0);
					$email = $this->getEmail($relid);
					if (!empty($email)) {
						$adb->pquery('UPDATE vtiger_projecttask SET email = ? WHERE projecttaskid = ?', array($email, $crmId));
					}
					break;
			}
		}
	}

	public function getEmail($relid) {
		global $adb;
		$email = '';
		if (!empty($relid)) {
			$relModule = getSalesEntityType($relid);
			if ($relModule == 'Contacts') {
				$res = $adb->pquery('SELECT email FROM vtiger_contactdetails WHERE contactid = ?', array($relid));
				$email = $adb->query_result($res, 0, 'email');
			} else {
				$res = $adb->pquery('SELECT email1 FROM vtiger_account WHERE accountid = ?', array($relid));
				$email = $adb->query_result($res, 0, 'email1');
			}
		}
		return $email;
	}
}
?>
