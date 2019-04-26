<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function dashboardDisplayCall($type, $Chart_Type, $from_page) {
	require_once 'include/logging.php';
	require 'modules/Dashboard/graphdefinitions.php';
	$log = LoggerManager::getLogger('dashboard');
	if (isset($type)) {
		require_once 'modules/Dashboard/display_charts.php';
		$_REQUEST['type'] = $type;
		$_REQUEST['Chart_Type'] = $Chart_Type;
		$_REQUEST['from_page'] = 'HomePage';
		return dashBoardDisplayChart();
	}
	return '';
}
?>