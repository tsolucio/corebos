<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class VTDummyTask extends VTTask {
	public $executeImmediately = true;
	public $queable = false;

	public function getFieldNames() {
		return array('statement');
	}

	public function doTask(&$entity) {
		global $current_user;
		$statement=$this->statement;
		echo "This is a dummy workflow task: $statement";
		// sending workflow log data to the messageQueue
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->sendMessage('workflowdata', 'logall', 'logalldata', 'Message', '1:M', 1, 30, 0, $current_user->id, json_encode($entity));
	}
}
?>