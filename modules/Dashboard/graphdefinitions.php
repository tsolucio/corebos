<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the 'License'); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an 'AS IS' BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

global $current_language;
$DBmod_strings = return_module_language($current_language, 'Dashboard');

$graph_array = array(
	'DashboardHome' => $DBmod_strings['DashboardHome'],
	'leadsource' => $DBmod_strings['leadsource'],
	'leadstatus' => $DBmod_strings['leadstatus'],
	'leadindustry' => $DBmod_strings['leadindustry'],
	'salesbyleadsource' => $DBmod_strings['salesbyleadsource'],
	'salesbyaccount' => $DBmod_strings['salesbyaccount'],
	'salesbyuser' => $DBmod_strings['salesbyuser'],
	'salesbyteam' => $DBmod_strings['salesbyteam'],
	'accountindustry' => $DBmod_strings['accountindustry'],
	'productcategory' => $DBmod_strings['productcategory'],
	'productbyqtyinstock' => $DBmod_strings['productbyqtyinstock'],
	'productbypo' => $DBmod_strings['productbypo'],
	'productbyquotes' => $DBmod_strings['productbyquotes'],
	'productbyinvoice' => $DBmod_strings['productbyinvoice'],
	'sobyaccounts' => $DBmod_strings['sobyaccounts'],
	'sobystatus' => $DBmod_strings['sobystatus'],
	'pobystatus' => $DBmod_strings['pobystatus'],
	'quotesbyaccounts' => $DBmod_strings['quotesbyaccounts'],
	'quotesbystage' => $DBmod_strings['quotesbystage'],
	'invoicebyacnts' => $DBmod_strings['invoicebyacnts'],
	'invoicebystatus' => $DBmod_strings['invoicebystatus'],
	'ticketsbystatus' => $DBmod_strings['ticketsbystatus'],
	'ticketsbypriority' => $DBmod_strings['ticketsbypriority'],
	'ticketsbycategory' => $DBmod_strings['ticketsbycategory'],
	'ticketsbyuser' => $DBmod_strings['ticketsbyuser'],
	'ticketsbyteam' => $DBmod_strings['ticketsbyteam'],
	'ticketsbyproduct'=> $DBmod_strings['ticketsbyproduct'],
	'contactbycampaign'=> $DBmod_strings['contactbycampaign'],
	'ticketsbyaccount'=> $DBmod_strings['ticketsbyaccount'],
	'ticketsbycontact'=> $DBmod_strings['ticketsbycontact'],
);
