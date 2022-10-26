<?php
/*************************************************************************************************
 * Copyright 2022 Spike, JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class LaunchWorkflowButton {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new LaunchWorkflowButton_DetailViewBlock());
	}
}

class LaunchWorkflowButton_DetailViewBlock extends DeveloperBlock {
	// Implement widget functionality
	protected $widgetName = 'Launch Workflow Button';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		$this->context = $context;
		$smarty = $this->getViewer();
		$record_id = $this->getFromContext('record');
		$workflow_id = $this->getFromContext('workflow_id');
		$type = $this->getFromContext('type');

		$slds_class = '';
		$link_label = '';
		if ($type == 'approve') {
			$slds_class = 'slds-button_brand';
			$link_label = 'Approve';
		} elseif ($type == 'decline') {
			$slds_class = 'slds-button_destructive';
			$link_label = 'Decline';
		} else {
			$slds_class = $type;
			$link_label = $this->getFromContext('button_label');
		}

		$smarty->assign('record_id', $record_id);
		$smarty->assign('workflow_id', $workflow_id);
		$smarty->assign('slds_class', $slds_class);
		$smarty->assign('link_label', getTranslatedString($link_label, 'Documents'));
		return $smarty->fetch('modules/Documents/LaunchWorkflowButton.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new LaunchWorkflowButton_DetailViewBlock();
	echo $smq->process($_REQUEST);
}