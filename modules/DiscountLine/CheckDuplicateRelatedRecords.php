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

class CheckDuplicateRelatedRecords extends VTEventHandler {
	/**
	 * @param $handlerType
	 * @param $entityData VTEntityData
	 */
	public function handleEvent($handlerType, $entityData) {
		global $adb, $log;
		switch ($handlerType) {
			case 'corebos.entity.link.after':
				if (in_array($entityData['sourceModule'], array('Contacts', 'Products', 'Services', 'Accounts')) && $entityData['destinationModule'] == 'DiscountLine') {
					$checkResult = $adb->pquery(
						'SELECT * FROM vtiger_crmentityrel INNER JOIN ( SELECT vtiger_crmentityrel.relcrmid FROM vtiger_crmentityrel WHERE crmid = ? OR crmid = ? ) temp ON vtiger_crmentityrel.relcrmid = temp.relcrmid AND( relmodule = ? OR relmodule = ? ) WHERE ( vtiger_crmentityrel.relmodule = ? OR vtiger_crmentityrel.relmodule = ? ) AND crmid IN( SELECT crmid FROM vtiger_crmentityrel WHERE (crmid = ? OR crmid = ?) AND( module = ? OR module = ? ) AND( relcrmid = ? OR relcrmid = ? ) AND( relmodule = ? OR relmodule = ? ) )',
						array(
							$entityData['sourceRecordId'], $entityData['destinationRecordId'], $entityData['destinationModule'], $entityData['sourceModule'], $entityData['destinationModule'], $entityData['sourceModule'], $entityData['sourceRecordId'], $entityData['destinationRecordId'], 
							$entityData['sourceModule'], $entityData['destinationModule'], $entityData['destinationRecordId'], $entityData['sourceRecordId'], 
							$entityData['destinationModule'], $entityData['sourceModule']
						)
					);

					if ($adb->num_rows($checkResult) != 1) {
						$sql = "DELETE FROM vtiger_crmentityrel WHERE crmid = ? AND module = ? AND relcrmid = ? AND relmodule = ? LIMIT 1";
						$adb->pquery($sql, array($entityData['sourceRecordId'],$entityData['sourceModule'], $entityData['destinationRecordId'], $entityData['destinationModule']));
					} elseif ($adb->num_rows($checkResult) == 0) {
						$sql = 'INSERT INTO vtiger_crmentityrel VALUES(?,?,?,?)';
						$adb->pquery($sql, array($entityData['sourceRecordId'],$entityData['sourceModule'], $entityData['destinationRecordId'], $entityData['destinationModule']));
					}
				}
			break;
		}
	}

	public function handleFilter($handlerType, $parameter) {}
}
