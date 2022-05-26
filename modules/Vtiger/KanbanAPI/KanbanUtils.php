<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Link.php';

function kbGetItemQuery($module, $limit_start_rec, $boardinfo) {
	global $current_user;
	require_once 'modules/'.$module.'/'.$module.'.php';
	$focus = new $module();
	$focus->initSortbyField($module);
	if (ListViewSession::hasViewChanged($module)) {
		coreBOS_Session::set($module.'_Order_By', '');
	}
	$queryGenerator = new QueryGenerator($module, $current_user);
	$customView = new CustomView($module);
	$viewid = $customView->getViewId($module);
	try {
		if ($viewid != '0') {
			$queryGenerator->initForCustomViewById($viewid);
		} else {
			$queryGenerator->initForDefaultCustomView();
		}
	} catch (Exception $e) {
		return '';
	}
	$queryGenerator->setFields($boardinfo['allfields']);
	if (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') {
		$queryGenerator->addUserSearchConditions($_REQUEST);
	}
	$order_by = $focus->getOrderBy();
	if (empty($order_by)) {
		$order_by = $focus->default_order_by;
	}
	$queryGenerator->addWhereField($order_by);
	$queryGenerator->addCondition($boardinfo['lanefield'], $boardinfo['lanename'], 'e', QueryGenerator::$AND);
	$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.before', $queryGenerator);
	$list_query = $queryGenerator->getQuery();
	$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.after', $queryGenerator);
	$list_query = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.query', $list_query);
	$where = $queryGenerator->getConditionalWhere();
	if (isset($where) && $where != '') {
		coreBOS_Session::set('export_where', $where);
	} else {
		coreBOS_Session::delete('export_where');
	}
	return $list_query. " ORDER BY $order_by LIMIT ".($limit_start_rec*$boardinfo['pagesize']).', '.$boardinfo['pagesize'];
}
?>