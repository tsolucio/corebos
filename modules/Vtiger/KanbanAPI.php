<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';
include_once 'modules/Vtiger/KanbanViewMenu.php';
global $currentModule;
Vtiger_Request::validateRequest();
$module = urlencode(vtlib_purify($_REQUEST['kbmodule']));
$op = vtlib_purify($_REQUEST['method']);
switch ($op) {
	case 'getBoardItems':
	case 'getBoardItemsFormatted':
		include_once 'modules/Vtiger/KanbanAPI/getBoardItems.php';
		$boardinfo = json_decode(vtlib_purify($_REQUEST['boardinfo']), true);
		$boardinfo['pagesize'] = isset($boardinfo['pagesize']) ?
			(int) vtlib_purify($boardinfo['pagesize']) :
			GlobalVariable::getVariable('Application_ListView_PageSize', 40, $module);
		$boardinfo['allfields'] = array_merge(['id', $boardinfo['cards']['title']], $boardinfo['cards']['showfields'], $boardinfo['cards']['morefields']);
		if ($op=='getBoardItems') {
			$ret = kbGetBoardItems($module, $boardinfo['currentPage'], $boardinfo);
		} else {
			$ret = kbGetBoardItemsFormatted($module, $boardinfo['currentPage'], $boardinfo);
		}
		break;
	case 'getBoardItem':
	case 'getBoardItemFormatted':
		include_once 'modules/Vtiger/KanbanAPI/getBoardItem.php';
		$boardinfo = json_decode(vtlib_purify($_REQUEST['boardinfo']), true);
		$tileid = vtlib_purify($_REQUEST['tileid']);
		if ($op=='getBoardItem') {
			$ret = kbGetBoardItem($module, $tileid, $boardinfo);
		} else {
			$ret = kbGetBoardItemFormatted($module, $tileid, $boardinfo);
		}
		break;
	default:
		$ret = '';
		break;
}
echo json_encode($ret);
?>
