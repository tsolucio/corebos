<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Documentation.
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
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

$current_user = Users::getActiveAdminUser();
$onlyNonStandard = isset($argv[1]);
$stdModules = array(
	'Accounts',
	'Assets',
	'BusinessActions',
	'Calendar4You',
	'Campaigns',
	'cbAuditTrail',
	'cbCalendar',
	'cbCompany',
	'cbCredentials',
	'cbCVManagement',
	'cbLoginHistory',
	'cbMap',
	'cbPulse',
	'cbQuestion',
	'cbSurvey',
	'cbSurveyAnswer',
	'cbSurveyDone',
	'cbSurveyQuestion',
	'cbTermConditions',
	'cbtranslation',
	'cbupdater',
	'CobroPago',
	'ConfigEditor',
	'Contacts',
	'CronTasks',
	'CustomerPortal',
	'Dashboard',
	'Documents',
	'Emails',
	'EtiquetasOO',
	'evvtgendoc',
	'evvtMenu',
	'Faq',
	'GlobalVariable',
	'HelpDesk',
	'Home',
	'Import',
	'InventoryDetails',
	'Invoice',
	'Leads',
	'MailManager',
	'Messages',
	'Mobile',
	'ModComments',
	'ModTracker',
	'MsgTemplate',
	'PBXManager',
	'Portal',
	'Potentials',
	'PriceBooks',
	'ProductComponent',
	'Products',
	'Project',
	'ProjectMilestone',
	'ProjectTask',
	'PurchaseOrder',
	'Quotes',
	'RecycleBin',
	'Reports',
	'Rss',
	'SalesOrder',
	'ServiceContracts',
	'Services',
	'SMSNotifier',
	'Tooltip',
	'Users',
	'Vendors',
	'VtigerBackup',
	'Webforms',
	'WSAPP',
);

$mods = $adb->query('select name,tablename,isentitytype from vtiger_tab left join vtiger_entityname on vtiger_tab.tabid=vtiger_entityname.tabid order by name;');
foreach ($adb->rowGenerator($mods) as $mod) {
	if (!$onlyNonStandard || !in_array($mod['name'], $stdModules)) {
		echo $mod['name']."\t".' ('.$mod['tablename'].((int)$mod['isentitytype'] ? '' : 'extension').")\n";
	}
}
?>