<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 ************************************************************************************/
global $log;
$log->debug('< MasterGridAPI');
Vtiger_Request::validateRequest();
$data = json_decode(vtlib_purify($_REQUEST['data']), true);
$module = $data['module'];
$rowid = isset($data['rowid']) ? $data['rowid'] : 0;
$log->debug('Method: '.$data['method']);
switch ($data['method']) {
	case 'deleteRow':
		if ($rowid > 0 && !empty($module)) {
			$focus = CRMEntity::getInstance($module);
			list($delerror,$errormessage) = $focus->preDeleteCheck();
			if (!$delerror) {
				$focus->trash($module, $rowid);
				$ret = true;
			} else {
				$log->debug('Error: '.$errormessage);
				$ret = false;
			}
		}
		break;
	default:
		$ret = false;
		break;
}
$log->debug('> MasterGridAPI');
echo json_encode($ret);
?>
