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
global $currentModule;
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
$idlist = isset($_REQUEST['idlist']) ? vtlib_purify($_REQUEST['idlist']) : '';
$destinationModule = vtlib_purify($_REQUEST['destination_module']);
$crmid = vtlib_purify($_REQUEST['parentid']);
$ids = explode(';', trim($idlist, ';'));
$focus = CRMEntity::getInstance($currentModule);
$with_crmid = (array)$ids;
$data = array();
$data['sourceModule'] = $currentModule;
$data['sourceRecordId'] = $crmid;
$data['destinationModule'] = $destinationModule;
if ($mode == 'delete') {
	foreach ($with_crmid as $relcrmid) {
		$data['destinationRecordId'] = $relcrmid;
		cbEventHandler::do_action('corebos.entity.link.delete', $data);
		$adb->pquery(
			'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND module=? AND relcrmid=? AND relmodule=?) OR (crmid=? AND relmodule=? AND relcrmid=? AND module=?)',
			array($crmid, $destinationModule, $relcrmid, $currentModule, $relcrmid, $destinationModule, $crmid,$currentModule)
		);
		cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
	}
} else {
	$destinationRecordIds = (array)$ids;
	$data = array();
	$data['focus'] = $focus;
	$data['sourceModule'] = $currentModule;
	$data['sourceRecordId'] = $crmid;
	$data['destinationModule'] = $destinationModule;
	foreach ($destinationRecordIds as $destinationRecordId) {
		$data['destinationRecordId'] = $destinationRecordId;
		cbEventHandler::do_action('corebos.entity.link.before', $data);
		$focus->save_related_module($currentModule, $destinationRecordId, $destinationModule, $crmid);
		$focus->trackLinkedInfo($currentModule, $destinationRecordId, $destinationModule, $crmid);
		cbEventHandler::do_action('corebos.entity.link.after', $data);
	}
}
?>
