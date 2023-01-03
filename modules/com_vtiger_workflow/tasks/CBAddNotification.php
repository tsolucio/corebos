<?php
/*************************************************************************************************
 * Copyright 2022 Spike. -- This file is a part of Spike coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. Spike. reserves all rights not expressly
 * granted by the License. coreBOS distributed by Spike. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 * Author : Spike
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/cbCalendar/cbCalendar.php';

class CBAddNotification extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('cbmodule', 'cbrecord', 'cbdate', 'cbtime', 'moreaction', 'moreinfo', 'ownerid', 'relwith');
	}

	public function getContextVariables() {
		return array(
			'AddNotification_Module' => array(
				'type' => '1613',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_Module',
			),
			'AddNotification_Record' => array(
				'type' => '10',
				'values' => 0,
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_Record',
			),
			'AddNotification_Date' => array(
				'type' => '5',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_Date',
			),
			'AddNotification_Time' => array(
				'type' => '14',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_Time',
			),
			'AddNotification_MoreAction' => array(
				'type' => '1',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_MoreAction',
			),
			'AddNotification_MoreInfo' => array(
				'type' => '1',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_MoreInfo',
			),
			'AddNotification_Owner' => array(
				'type' => '77',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_Owner',
			),
			'AddNotification_Related' => array(
				'type' => '10',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_CBAddNotification_Related',
			),
		);
	}

	public function doTask(&$entity) {
		if (empty($entity->WorkflowContext['AddNotification_Module'])) {
			if (empty($this->cbmodule) || $this->cbmodule=='wfmodule') {
				$cbmodule = $entity->getModuleName();
			} else {
				$cbmodule = $this->cbmodule;
			}
		} else {
			$cbmodule = $entity->WorkflowContext['AddNotification_Module'];
		}
		if (empty($entity->WorkflowContext['AddNotification_Record'])) {
			if (empty($this->cbrecord)) {
				$cbrecord = vtws_getCRMID($entity->getId());
			} else {
				$cbrecord = vtws_getCRMID($this->cbrecord);
			}
		} else {
			$cbrecord = vtws_getCRMID($entity->WorkflowContext['AddNotification_Record']);
		}
		if (empty($entity->WorkflowContext['AddNotification_Time'])) {
			if (empty($this->cbtime)) {
				$cbtime = date('H:i:s');
			} else {
				$cbtime = $this->cbtime;
			}
		} else {
			$cbtime = $entity->WorkflowContext['AddNotification_Time'];
		}
		if (empty($entity->WorkflowContext['AddNotification_Date'])) {
			if (empty($this->cbdate)) {
				$datetime = date('Y-m-d').' '.$cbtime;
			} else {
				$datetime = $this->cbdate.' '.$cbtime;
			}
		} else {
			$datetime = $entity->WorkflowContext['AddNotification_Date'].' '.$cbtime;
		}
		if (empty($entity->WorkflowContext['AddNotification_MoreAction'])) {
			if (empty($this->moreaction)) {
				$moreaction = '{}';
			} else {
				$moreaction = $this->moreaction;
			}
		} else {
			$moreaction = $entity->WorkflowContext['AddNotification_MoreAction'];
		}
		if (empty($entity->WorkflowContext['AddNotification_MoreInfo'])) {
			if (empty($this->moreinfo)) {
				$moreinfo = '{}';
			} else {
				$moreinfo = $this->moreinfo;
			}
		} else {
			$moreinfo = $entity->WorkflowContext['AddNotification_MoreInfo'];
		}
		if (empty($entity->WorkflowContext['AddNotification_Owner'])) {
			if (empty($this->ownerid) || $this->cbmodule=='wfuser') {
				global $current_user;
				$ownerid = $current_user->id;
			} else {
				$ownerid = vtws_getCRMID($this->ownerid);
			}
		} else {
			$ownerid = vtws_getCRMID($entity->WorkflowContext['AddNotification_Owner']);
		}
		if (empty($entity->WorkflowContext['AddNotification_Related'])) {
			if (empty($this->relwith)) {
				$relwith = 0;
			} else {
				$relwith = vtws_getCRMID($this->relwith);
			}
		} else {
			$relwith = vtws_getCRMID($entity->WorkflowContext['AddNotification_Related']);
		}
		cbCalendar::addNotificationReminder($cbmodule, $cbrecord, $datetime, $ownerid, $relwith, $moreaction, $moreinfo);
	}
}
?>