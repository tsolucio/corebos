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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

class CBTagTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;
	public $listoftags;
	public $addrel;
	public $forallusers;

	public function getFieldNames() {
		return array('listoftags', 'addrel', 'forallusers');
	}

	public function doTask(&$entity) {
		global $current_user, $logbg;
		$logbg->debug('> CBTagTask');
		list($cto, $cto_id) = explode('x', $entity->getId());
		if (!empty($cto_id) && !empty($this->listoftags)) {
			$setype = getSalesEntityType($cto_id);
			require_once 'include/freetag/freetag.class.php';
			$freetag = new freetag();
			if ($this->addrel) {
				if ($this->forallusers) {
					$usrs = get_user_array();
					$users = '';
					foreach ($usrs as $uid => $uname) {
						if (!empty($uid)) {
							$users.= $uid.',';
						}
					}
					$users = trim($users, ',');
				} else {
					$users = $current_user->id;
				}
				$logbg->debug('(CBTagTask) tag object');
				$result = $freetag->tag_object($users, $cto_id, $this->listoftags, $setype);
				$logbg->debug('(CBTagTask) tag object result: ' . $result);
			} else {
				$logbg->debug('(CBTagTask) not called: addrel is empty');
				if ($this->forallusers) {
					$logbg->debug('(CBTagTask) delete object tags');
					$freetag->delete_object_tags($cto_id, $this->listoftags);
				} else {
					$logbg->debug('(CBTagTask) delete object tag');
					$freetag->delete_object_tag($current_user->id, $cto_id, $this->listoftags);
				}
			}
		} else {
			$logbg->debug('(CBTagTask) not called: cto_id or listoftags are empty');
		}
		$logbg->debug('< CBTagTask');
	}
}
?>