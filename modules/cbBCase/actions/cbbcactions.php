<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/cbBCase/cbBCase.php';

class cbbcactions_Action extends CoreBOS_ActionController {

	private function checkQIDParam() {
		$record = isset($_REQUEST['bcid']) ? vtlib_purify($_REQUEST['bcid']) : 0;
		if (empty($record)) {
			$rdo = array();
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('LBL_RECORD_NOT_FOUND');
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
			$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
			echo json_encode($rdo);
			die();
		}
		return $record;
	}

	public function recalculateTotals() {
		$record = $this->checkQIDParam();
		$bc = CRMEntity::getInstance('cbBCase');
		$bc->retrieve_entity_info($record, 'cbBCase');
		$bc->reCalculateActuals($record);
		$rdo = array();
		$rdo['status'] = 'OK';
		$rdo['msg'] = getTranslatedString('Recalculated');
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}
}
?>
