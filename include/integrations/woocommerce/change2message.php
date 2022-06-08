<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : coreBOS Change to Message Handler
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/integrations/woocommerce/woocommerce.php';

class woocommercechange2message extends VTEventHandler {

	public $cbmq;
	public $ttl = 43200; // seconds: 12hours

	public function __construct() {
		$this->cbmq = coreBOS_MQTM::getInstance();
	}

	public function handleEvent($eventName, $entityData) {
		global $current_user, $default_charset;
		$moduleName = $entityData->getModuleName();
		$recordId = $entityData->getId();
		if (in_array($moduleName, corebos_woocommerce::$supportedModules) && $this->isSyncActiveForRecord($moduleName, $recordId)) {
			switch ($eventName) {
				case 'vtiger.entity.aftersave.final':
					$vtEntityDelta = new VTEntityDelta();
					$delta = $vtEntityDelta->getEntityDelta($moduleName, $recordId, true);
					if (is_array($delta)) {
						$columnFields = array();
						foreach ($delta as $fieldName => $values) {
							if ($fieldName != 'modifiedtime') {
								$columnFields[$fieldName] = array(
									'prevalue' => $values['oldValue'],
									'postvalue' => html_entity_decode($values['currentValue'], ENT_QUOTES, $default_charset),
								);
							}
						}
						$isNew = $entityData->isNew();
						$logfields = array(
							'record_id' => $recordId,
							'module' => $moduleName,
							'userid' => $current_user->id,
							'changedon' => date('Y-m-d H:i:s'),
							'operation' => ($isNew ? 'CREATED' : 'UPDATED'),
							'changes' => $columnFields,
						);
						$this->cbmq->sendMessage('WooCChangeChannel', 'WCChangeHandler', 'WCChangeSync', 'Data', '1:M', 1, $this->ttl, 0, 0, json_encode($logfields));
					}
					break;
				case 'vtiger.entity.afterdelete':
					$msg = array(
						'record_id' => $recordId,
						'module' => $moduleName,
						'userid' => $current_user->id,
						'changedon' => date('Y-m-d H:i:s'),
						'operation' => 'DELETED',
					);
					$this->cbmq->sendMessage('WooCChangeChannel', 'WCChangeHandler', 'WCDeleteSync', 'Data', '1:M', 1, $this->ttl, 0, 0, json_encode($msg));
					break;
			}
		}
	}

	private function isSyncActiveForRecord($module, $crmid) {
		global $adb, $current_user;
		$syncing = coreBOS_Settings::getSetting('woocommerce_syncing', null);
		if ($syncing==$crmid || $syncing=='creating') {
			return false;
		}
		$queryGenerator = new QueryGenerator($module, $current_user);
		$queryGenerator->setFields(array('wcsyncstatus','wcdeleted'));
		$queryGenerator->addCondition('id', $crmid, 'e');
		$query = $queryGenerator->getQuery();
		$crmtbl = CRMEntity::getcrmEntityTableAlias($module, true);
		$query = str_ireplace($crmtbl.'.deleted=0 AND', '', $query); // for afterdelete event
		$rs = $adb->pquery($query, array());
		if ($rs && $adb->num_rows($rs)==1) {
			$sw = $adb->query_result($rs, 0, 'wcsyncstatus');
			$dl = $adb->query_result($rs, 0, 'wcdeleted');
			return ($sw != 'Inactive' && $dl!='1');
		}
		return false;
	}
}
?>
