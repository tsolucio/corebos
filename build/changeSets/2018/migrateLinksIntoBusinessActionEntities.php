<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

include_once 'modules/BusinessActions/BusinessActions.php';
include_once 'modules/Users/Users.php';
include_once 'include/Webservices/Create.php';

class migrateLinksIntoBusinessActionEntities extends cbupdaterWorker {

	public function applyChange() {

		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			if ($this->isModuleInstalled('BusinessActions')) {
				vtlib_toggleModuleAccess('BusinessActions', true);
				global $adb, $current_user;
				$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
				$brules = array();
				$default_values =  array(
					'mapname' => '',
					'maptype' => 'Condition Expression',
					'targetname' => '',
					'content' => '',
					'description' => '',
					'assigned_user_id' => $usrwsid,
				);
				/////////
				$rec = $default_values;
				$rec['mapname'] = 'ConverLead_ConditionExpression';
				$rec['targetname'] = 'Leads';
				$rec['content'] = '<map>
<function>
	<name>leadCanBeConverted</name>
	<parameters>
		<parameter>record_id</parameter>
	</parameters>
</function>
</map>';
				$brule = vtws_create('cbMap', $rec, $current_user);
				$brules['LBL_CONVERT_BUTTON_LABEL'] = $brule['id'];
				/////////
				$rec = $default_values;
				$rec['mapname'] = 'ConvertTicket_ConditionExpression';
				$rec['targetname'] = 'HelpDesk';
				$rec['content'] = '<map>
<function>
	<name>isPermitted</name>
	<parameters>
		<parameter>Faq</parameter>
		<parameter>CreateView</parameter>
	</parameters>
</function>
</map>';
				$brule = vtws_create('cbMap', $rec, $current_user);
				$brules['LBL_CONVERT_AS_FAQ_BUTTON_LABEL'] = $brule['id'];
				/////////
				$rec = $default_values;
				$rec['mapname'] = 'ConvertOpportunityToInvoice_ConditionExcpression';
				$rec['targetname'] = 'Potentials';
				$rec['content'] = '<map>
<function>
	<name>isPermitted</name>
	<parameters>
		<parameter>Invoice</parameter>
		<parameter>CreateView</parameter>
		<parameter>record_id</parameter>
	</parameters>
</function>
</map>';
				$brule = vtws_create('cbMap', $rec, $current_user);
				$brules['Create Invoice'] = $brule['id'];
				/////////
				$rec = $default_values;
				$rec['mapname'] = 'SendEmail_ConditionExpression';
				$rec['targetname'] = 'Emails';
				$rec['content'] = '<map>
<function>
	<name>isPermitted</name>
	<parameters>
		<parameter>Emails</parameter>
		<parameter>CreateView</parameter>
	</parameters>
</function>
</map>';
				$brule = vtws_create('cbMap', $rec, $current_user);
				$brules['LBL_SENDMAIL_BUTTON_LABEL'] = $brule['id'];
				/////////
				$rec = $default_values;
				$rec['mapname'] = 'EventPersmission_ConditionExpression';
				$rec['targetname'] = 'Contacts';
				$rec['content'] = '<map>
<function>
	<name>CheckFieldPermission</name>
	<parameters>
		<parameter>parent_id</parameter>
		<parameter>Events</parameter>
	</parameters>
</function>
</map>';
				$brule = vtws_create('cbMap', $rec, $current_user);
				$brules['Add event'] = $brule['id'];
				$brules['Add Event'] = $brule['id'];
				/////////
				$rec = $default_values;
				$rec['mapname'] = 'AccountsMailerExportListView_ConditionExpression';
				$rec['targetname'] = 'Accounts';
				$rec['content'] = '<map>
<function>
	<name>isPermitted</name>
	<parameters>
		<parameter>Accounts</parameter>
		<parameter>Export</parameter>
		<parameter></parameter>
	</parameters>
</function>
</map>';
				$brule = vtws_create('cbMap', $rec, $current_user);
				$brules['LBL_MAILER_EXPORT'] = $brule['id'];
				/////////
				$collectLinksSql ="SELECT linktype, 
                                          linklabel, 
                                          linkurl,
                                          handler_path,
                                          onlyonmymodule,
                                          handler_class,
                                          linkicon,
                                          handler,
                                          (SELECT vtiger_tab.name FROM vtiger_tab WHERE vtiger_tab.tabid = vtiger_links.tabid) AS module_list
                                     FROM vtiger_links";

				$collectedLinks = $adb->pquery($collectLinksSql, array());
				$adminId = Users::getActiveAdminID();

				while ($link = $adb->fetch_array($collectedLinks)) {
					$focusnew = new BusinessActions();
					$focusnew->column_fields['assigned_user_id'] = $adminId;
					$focusnew->column_fields['linktype'] = $link['linktype'];
					$focusnew->column_fields['linklabel'] = $link['linklabel'];
					$focusnew->column_fields['linkurl'] = html_entity_decode($link['linkurl'], ENT_QUOTES, 'UTF-8');
					$focusnew->column_fields['sequence'] = 0;
					$focusnew->column_fields['module_list'] = $link['module_list'];
					$focusnew->column_fields['handler_path'] = $link['handler_path'];
					$focusnew->column_fields['onlyonmymodule'] = $link['onlyonmymodule'];
					$focusnew->column_fields['handler_class'] = $link['handler_class'];
					$focusnew->column_fields['linkicon'] = $link['linkicon'];
					$focusnew->column_fields['handler'] = $link['handler'];
					$focusnew->column_fields['active'] = 1;
					if (isset($brules[$link['linklabel']])) {
						if (($link['linklabel']=='Create Invoice' && $link['module_list']!='Potentials')) {
							$focusnew->column_fields['brmap'] = 0;
						} else {
							list($wsid, $brid) = explode('x', $brules[$focusnew->column_fields['linklabel']]);
							$focusnew->column_fields['brmap'] = $brid;
						}
					} else {
						$focusnew->column_fields['brmap'] = 0;
					}
					$focusnew->save('BusinessActions');
				}

				$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
				$this->sendMsg('The vtiger links were migrated successfully into Business Action entities');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}