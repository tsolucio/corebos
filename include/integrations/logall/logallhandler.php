<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : coreBOS Logging Extension
 *  Version      : 1.0
 *************************************************************************************************/
require_once 'data/VTEntityDelta.php';
require_once 'include/freetag/freetag.class.php';

class cbLogAllHandler extends VTEventHandler {

	private $ladataindex = 'companydata';
	private $laauditindex = 'companydataaudit';
	private $lachangeindex = 'companydatachange';
	private $expire = 30;

	public function handleEvent($eventName, $entityData) {
		global $log, $adb, $current_user;
		include 'config.inc.php';
		$appname = GlobalVariable::getVariable('Application_Unique_Identifier', $application_unique_key);
		$cbmq = coreBOS_MQTM::getInstance();
		switch ($eventName) {
			case 'vtiger.entity.aftersave.final':
				$isNew = $entityData->isNew();
				$moduleName = $entityData->getModuleName();
				$recordId = $entityData->getId();
				$msg = array(
					'module' => $moduleName,
					'record_id' => $recordId,
					'cbuuid' => CRMEntity::getUUIDfromCRMID($recordId),
					'operation' => ($isNew ? 'Create' : 'Update'),
					'userid' => $current_user->id,
					'doneon' => date('Y-m-d H:i:s'),
					'donefrom' => $_SERVER['REMOTE_ADDR'],
					'application' => $appname,
				);
				$freetag = new freetag();
				$tagcloud = $freetag->get_tags_on_object($recordId);
				$apptags = array();
				if (is_array($tagcloud)) {
					foreach ($tagcloud as $tag) {
						$apptags[] = $tag['tag'];
					}
				}
				$webserviceObject = VtigerWebserviceObject::fromName($adb, $moduleName);
				$handlerPath = $webserviceObject->getHandlerPath();
				$handlerClass = $webserviceObject->getHandlerClass();
				require_once $handlerPath;
				$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
				$meta = $handler->getMeta();
				$data = $entityData->getData();
				unset($data['createdtime'], $data['modifiedtime'], $data['modifiedby'], $data['created_user_id']);
				$data = DataTransform::sanitizeReferences($data, $meta, true);
				$data['apptags'] = $apptags;
				$msg['data'] = $data;
				$cbmq->sendMessage($this->ladataindex, 'logall', 'logalldata', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, json_encode($msg));
				if (!$isNew) {
					$vtEntityDelta = new VTEntityDelta();
					$delta = $vtEntityDelta->getEntityDelta($moduleName, $recordId, true);
					if (is_array($delta)) {
						unset($delta['modifiedtime']);
						if (count($delta)) {
							$do = $dn = array();
							foreach ($delta as $field => $values) {
								$do[$field] = $values['oldValue'];
								$dn[$field] = $values['currentValue'];
							}
							$do = DataTransform::sanitizeReferences($do, $meta, true);
							$dn = DataTransform::sanitizeReferences($dn, $meta, true);
							foreach ($delta as $field => $values) {
								$delta[$field]['oldValue'] = $do[$field];
								$delta[$field]['currentValue'] = $dn[$field];
							}
							$msg['operation'] = 'Change';
							$msg['data'] = $delta;
							$cbmq->sendMessage($this->lachangeindex, 'logall', 'logalldata', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, json_encode($msg));
						}
					}
				}
				break;
			case 'vtiger.entity.beforedelete':
				$moduleName = $entityData->getModuleName();
				$recordId = $entityData->getId();
				$msg = json_encode(array(
					'module' => $moduleName,
					'record_id' => $recordId,
					'cbuuid' => CRMEntity::getUUIDfromCRMID($recordId),
					'operation' => 'Delete',
					'data' => '',
					'userid' => $current_user->id,
					'doneon' => date('Y-m-d H:i:s'),
					'donefrom' => $_SERVER['REMOTE_ADDR'],
					'application' => $appname,
				));
				$cbmq->sendMessage($this->ladataindex, 'logall', 'logalldata', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				break;
			case 'vtiger.entity.afterrestore':
				$moduleName = $entityData->getModuleName();
				$recordId = $entityData->getId();
				$msg = json_encode(array(
					'module' => $moduleName,
					'record_id' => $recordId,
					'cbuuid' => CRMEntity::getUUIDfromCRMID($recordId),
					'operation' => 'Restore',
					'data' => '',
					'userid' => $current_user->id,
					'doneon' => date('Y-m-d H:i:s'),
					'donefrom' => $_SERVER['REMOTE_ADDR'],
					'application' => $appname,
				));
				$cbmq->sendMessage($this->ladataindex, 'logall', 'logalldata', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				if (coreBOS_Settings::getSetting('audit_trail', false)) {
					$cbmq->sendMessage($this->laauditindex, 'logall', 'logallaudit', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				}
				break;
			case 'corebos.entity.link.after':
				$msg = json_encode(array(
					'module' => $entityData['sourceModule'],
					'record_id' => $entityData['sourceRecordId'],
					'cbuuid' => CRMEntity::getUUIDfromCRMID($entityData['sourceRecordId']),
					'operation' => 'Relate',
					'data' => array(
						'withmodule' => $entityData['destinationModule'],
						'withid' => $entityData['destinationRecordId'],
						'withcbuuid' => CRMEntity::getUUIDfromCRMID($entityData['destinationRecordId']),
					),
					'userid' => $current_user->id,
					'doneon' => date('Y-m-d H:i:s'),
					'donefrom' => $_SERVER['REMOTE_ADDR'],
					'application' => $appname,
				));
				$cbmq->sendMessage($this->ladataindex, 'logall', 'logalldata', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				break;
			case 'corebos.entity.link.delete':
				$msg = json_encode(array(
					'module' => $entityData['sourceModule'],
					'record_id' => $entityData['sourceRecordId'],
					'cbuuid' => CRMEntity::getUUIDfromCRMID($entityData['sourceRecordId']),
					'operation' => 'Unrelate',
					'data' => array(
						'withmodule' => $entityData['destinationModule'],
						'withid' => $entityData['destinationRecordId'],
						'withcbuuid' => CRMEntity::getUUIDfromCRMID($entityData['destinationRecordId']),
					),
					'userid' => $current_user->id,
					'doneon' => date('Y-m-d H:i:s'),
					'donefrom' => $_SERVER['REMOTE_ADDR'],
					'application' => $appname,
				));
				$cbmq->sendMessage($this->ladataindex, 'logall', 'logalldata', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				break;
			case 'corebos.audit.action':
			case 'corebos.audit.authenticate':
				if (coreBOS_Settings::getSetting('audit_trail', false)) {
					$msg = array(
						'module' => $entityData[1],
						'record_id' => $entityData[3],
						'cbuuid' => CRMEntity::getUUIDfromCRMID($entityData[3]),
						'operation' => $entityData[2],
						'data' => '',
						'userid' => $entityData[0],
						'doneon' => $entityData[4],
						'donefrom' => $_SERVER['REMOTE_ADDR'],
						'application' => $appname,
					);
					switch ($entityData[2]) {
						case 'loginportal':
							$msg['data'] = array('username' => vtlib_purify($_REQUEST['username']));
							break;
						case 'GlobalVariableAjax':
							if (isset($_REQUEST['gvname'])) {
								$msg['data'] = array(
									'variable' => vtlib_purify($_REQUEST['gvname']),
									'user' => vtlib_purify($_REQUEST['gvuserid']),
									'module' => vtlib_purify($_REQUEST['gvmodule'])
								);
							}
							break;
						default:
							break;
					}
					$msg = json_encode($msg);
					$cbmq->sendMessage($this->laauditindex, 'logall', 'logallaudit', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				}
				break;
			case 'corebos.audit.login':
			case 'corebos.audit.logout':
				if (coreBOS_Settings::getSetting('audit_trail', false)) {
					$uid = getUserId_Ol($entityData[0]);
					$msg = json_encode(array(
						'module' => $entityData[1],
						'record_id' => $uid,
						'cbuuid' => CRMEntity::getUUIDfromCRMID($uid),
						'operation' => $entityData[2],
						'data' => '',
						'userid' => $uid,
						'doneon' => $entityData[4],
						'donefrom' => $_SERVER['REMOTE_ADDR'],
						'application' => $appname,
					));
					$cbmq->sendMessage($this->laauditindex, 'logall', 'logallaudit', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				}
				break;
			case 'corebos.audit.login.attempt':
				if (coreBOS_Settings::getSetting('audit_trail', false)) {
					$msg = json_encode(array(
						'module' => 'Users',
						'record_id' => $entityData[3],
						'cbuuid' => CRMEntity::getUUIDfromCRMID($entityData[3]),
						'operation' => $entityData[2],
						'data' => array('user_login' => $entityData[1]),
						'userid' => $entityData[0],
						'doneon' => $entityData[4],
						'donefrom' => $_SERVER['REMOTE_ADDR'],
						'application' => $appname,
					));
					$cbmq->sendMessage($this->laauditindex, 'logall', 'logallaudit', 'Message', '1:M', 1, $this->expire, 0, $current_user->id, $msg);
				}
				break;
			default:
				return true;
				break;
		}
	}
}
?>
