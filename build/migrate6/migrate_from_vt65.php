<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Migrate from vtiger CRM 6.5 to vtiger CRM 6.4
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

ExecuteQuery("ALTER TABLE vtiger_portalinfo DROP cryptmode");

// regenerate portal users password
$portalinfo_hasmore = true;
$page = 0;
do {
	$result = $adb->pquery("SELECT id FROM vtiger_portalinfo order by id limit $page,1000", array());
	$page = $page + 1000;
	$portalinfo_hasmore = false; // assume we are done.
	while ($row = $adb->fetch_array($result)) {
		$portalinfo_hasmore = true; // we found at least one so there could be more.
		$enc_password = makeRandomPassword();
		$adb->pquery('UPDATE vtiger_portalinfo SET user_password=? WHERE id=?', array($enc_password, $row['id']));
	}
} while ($portalinfo_hasmore);

ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Administration&action=index&parenttab=Settings' where name='LBL_USERS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=listroles&parenttab=Settings' where name='LBL_ROLES'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=ListProfiles&parenttab=Settings' where name='LBL_PROFILES'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=listgroups&parenttab=Settings' where name='USERGROUPLIST'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=OrgSharingDetailView&parenttab=Settings' where name='LBL_SHARING_ACCESS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=DefaultFieldPermissions&parenttab=Settings' where name='LBL_FIELDS_ACCESS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=ListLoginHistory&parenttab=Settings' where name='LBL_LOGIN_HISTORY_DETAILS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=ModuleManager&parenttab=Settings' where name='VTLIB_LBL_MODULE_MANAGER'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=PickList&action=PickList&parenttab=Settings' where name='LBL_PICKLIST_EDITOR'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings' where name='LBL_PICKLIST_DEPENDENCY_SETUP'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=listnotificationschedulers&parenttab=Settings' where name='NOTIFICATIONSCHEDULERS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=listinventorynotifications&parenttab=Settings' where name='INVENTORYNOTIFICATION'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=OrganizationConfig&parenttab=Settings' where name='LBL_COMPANY_DETAILS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=EmailConfig&parenttab=Settings' where name='LBL_MAIL_SERVER_SETTINGS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=CurrencyListView&parenttab=Settings' where name='LBL_CURRENCY_SETTINGS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=TaxConfig&parenttab=Settings' where name='LBL_TAX_SETTINGS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=Announcements&parenttab=Settings' where name='LBL_ANNOUNCEMENT'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=DefModuleView&parenttab=Settings' where name='LBL_DEFAULT_MODULE_VIEW'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=OrganizationTermsandConditions&parenttab=Settings' where name='INVENTORYTERMSANDCONDITIONS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings' where name='LBL_CUSTOMIZE_MODENT_NUMBER'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=MailScanner&parenttab=Settings' where name='LBL_MAIL_SCANNER'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings' where name='LBL_LIST_WORKFLOWS'");
ExecuteQuery("update vtiger_settings_field set linkto='index.php?module=Settings&action=MenuEditor&parenttab=Settings' where name='LBL_MENU_EDITOR'");
ExecuteQuery("DELETE FROM `vtiger_settings_field` WHERE `vtiger_settings_field`.`name` = 'Automated Backup'");

$delmods = array(
	'AutomatedBackup','EEMassMap'
);

foreach ($delmods as $module) {
	$mod = Vtiger_Module::getInstance($module);
	if ($mod) {
		$mod->deleteRelatedLists();
		$mod->deleteLinks();
		$mod->deinitWebservice();
		$mod->delete();
		echo "<b>Module $module EXTERMINATED!</b><br>";
	}
}

ExecuteQuery("update vtiger_version set old_version='6.3.0', current_version='6.4.0' where id=1");