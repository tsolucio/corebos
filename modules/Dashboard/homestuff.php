<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function dashboardDisplayCall($type,$Chart_Type,$from_page)
{
	global $app_strings, $app_list_strings, $mod_strings;

	global $currentModule, $theme;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	require_once('modules/Vtiger/layout_utils.php');
	require_once('include/logging.php');

	$graph_array = Array(
		"leadsource" => $mod_strings['leadsource'],
		"leadstatus" => $mod_strings['leadstatus'],
		"leadindustry" => $mod_strings['leadindustry'],
		"salesbyleadsource" => $mod_strings['salesbyleadsource'],
		"salesbyaccount" => $mod_strings['salesbyaccount'],
		"salesbyuser" => $mod_strings['salesbyuser'],
		"salesbyteam" => $mod_strings['salesbyteam'],
		"accountindustry" => $mod_strings['accountindustry'],
		"productcategory" => $mod_strings['productcategory'],
		"productbyqtyinstock" => $mod_strings['productbyqtyinstock'],
		"productbypo" => $mod_strings['productbypo'],
		"productbyquotes" => $mod_strings['productbyquotes'],
		"productbyinvoice" => $mod_strings['productbyinvoice'],
		"sobyaccounts" => $mod_strings['sobyaccounts'],
		"sobystatus" => $mod_strings['sobystatus'],
		"pobystatus" => $mod_strings['pobystatus'],
		"quotesbyaccounts" => $mod_strings['quotesbyaccounts'],
		"quotesbystage" => $mod_strings['quotesbystage'],
		"invoicebyacnts" => $mod_strings['invoicebyacnts'],
		"invoicebystatus" => $mod_strings['invoicebystatus'],
		"ticketsbystatus" => $mod_strings['ticketsbystatus'],
		"ticketsbypriority" => $mod_strings['ticketsbypriority'],
		"ticketsbycategory" => $mod_strings['ticketsbycategory'],
		"ticketsbyuser" => $mod_strings['ticketsbyuser'],
		"ticketsbyteam" => $mod_strings['ticketsbyteam'],
		"ticketsbyproduct"=> $mod_strings['ticketsbyproduct'],
		"contactbycampaign"=> $mod_strings['contactbycampaign'],
		"ticketsbyaccount"=> $mod_strings['ticketsbyaccount'],
		"ticketsbycontact"=> $mod_strings['ticketsbycontact'],
	);

	$log = LoggerManager::getLogger('dashboard');
	if(isset($type) && $type != '') {
		$dashboard_type = $type;
	} else {
		$dashboard_type = 'DashboardHome';
	}

	if(isset($type)) {
		require_once('modules/Dashboard/display_charts.php');
		$_REQUEST['type'] = $type;
		$_REQUEST['Chart_Type'] = $Chart_Type;
		$_REQUEST['from_page'] = 'HomePage';
		$dashval=dashBoardDisplayChart();
		return $dashval;
	}
}

?>