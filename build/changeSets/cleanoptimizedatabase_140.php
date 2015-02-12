<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class cleandatabase_140 extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$droptable = array(
				'vtiger_accountdepstatus', 'vtiger_accountownership', 'vtiger_accountregion', 'vtiger_activsubtype',
				'vtiger_businesstype', 'vtiger_competitor', 'vtiger_dealintimation', 'vtiger_downloadpurpose',
				'vtiger_evaluationstatus', 'vtiger_leadacctrel', 'vtiger_leadcontrel', 'vtiger_leadpotrel',
				'vtiger_potcompetitorrel', 'vtiger_productcollaterals', 'vtiger_ticketstracktime',
				'vtiger_licencekeystatus', 'vtiger_revenuetype', 'vtiger_usertype', 'vtiger_lar',
				'vtiger_contacttype', 'vtiger_crmentitynotesrel', 'vtiger_files', 'vtiger_headers',
			);
			
			foreach ($droptable as $table) {
				$this->ExecuteQuery("DROP TABLE $table");
			}
			
			$dropflds = array(
				'vtiger_leaddetails.purpose','vtiger_leaddetails.evaluationstatus','vtiger_potential.evaluationstatus',
				'vtiger_leaddetails.licencekeystatus','vtiger_leaddetails.revenuetype',
				'vtiger_contactdetails.usertype', 'vtiger_contactdetails.contacttype'
			);
			
			foreach ($dropflds as $fqfn) {
				list($table,$field) = explode('.', $fqfn);
				$this->ExecuteQuery("ALTER TABLE $table DROP $field");
			}
			
			/////////////////////////////////
			//  VTIGER CRM 6.0  interesting changes
			/////////////////////////////////
			$this->ExecuteQuery("ALTER TABLE vtiger_account MODIFY COLUMN annualrevenue decimal(25,5)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_leaddetails MODIFY COLUMN annualrevenue decimal(25,5)", array());
			$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldlabel='Annual Revenue' and typeofdata='I~O'",array());
			/* // Currency changes
			$this->ExecuteQuery("ALTER TABLE vtiger_currency_info MODIFY COLUMN conversion_rate decimal(12,5)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_productcurrencyrel MODIFY COLUMN actual_price decimal(28,8)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_productcurrencyrel MODIFY COLUMN converted_price decimal(28,8)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_pricebookproductrel MODIFY COLUMN listprice decimal(27,8)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel MODIFY COLUMN listprice decimal(27,8)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel MODIFY COLUMN discount_amount decimal(27,8)", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_invoice MODIFY COLUMN discount_amount decimal(27,8)", array());
			// We should do all other currency too
			$this->ExecuteQuery('UPDATE vtiger_field SET uitype=?, typeofdata=? WHERE fieldname=?',array(71, 'N~O', 'listprice'));
			$this->ExecuteQuery('UPDATE vtiger_field SET typeofdata=? WHERE fieldname=?',array('N~O', 'quantity'));
			$this->ExecuteQuery('UPDATE vtiger_field SET typeofdata=?, uitype =?, fieldlabel=? WHERE fieldname =? and tablename=?', array('N~O', 71, 'Discount', 'discount_amount', 'vtiger_inventoryproductrel'));
			*/
			
			$this->ExecuteQuery('ALTER TABLE vtiger_assets CHANGE account account INT(19) NULL', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_assets CHANGE datesold datesold date NULL', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_assets CHANGE dateinservice dateinservice date NULL', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_assets CHANGE serialnumber serialnumber varchar(200) NULL', array());
			$this->ExecuteQuery('UPDATE vtiger_field SET defaultvalue=1 WHERE fieldname = ?',array("filestatus"));
			$this->ExecuteQuery('ALTER TABLE vtiger_inventoryproductrel MODIFY comment text', array());
			$this->ExecuteQuery("ALTER TABLE vtiger_cvadvfilter MODIFY value VARCHAR(512)", array());
			$this->ExecuteQuery('ALTER TABLE vtiger_cvadvfilter MODIFY comparator VARCHAR(20)', array());
			// fix error
			$this->ExecuteQuery('UPDATE vtiger_cvadvfilter SET comparator = ? WHERE comparator = ?', array('next120days', 'next120day'));
			$this->ExecuteQuery('UPDATE vtiger_cvadvfilter SET comparator = ? WHERE comparator = ?', array('last120days', 'last120day'));
			$this->ExecuteQuery('ALTER TABLE vtiger_users MODIFY signature TEXT', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_mailscanner_ids modify column messageid varchar(512)' , array());
			$this->ExecuteQuery('ALTER TABLE vtiger_links MODIFY column linktype VARCHAR(50)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_links MODIFY column linklabel VARCHAR(50)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_links MODIFY column linkurl VARCHAR(512)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_links MODIFY column handler_class VARCHAR(50)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_links MODIFY column handler VARCHAR(50)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_cron_task MODIFY COLUMN laststart INT(11) UNSIGNED',Array());
			$this->ExecuteQuery('ALTER TABLE vtiger_cron_task MODIFY COLUMN lastend INT(11) UNSIGNED',Array());
			$this->ExecuteQuery("ALTER TABLE vtiger_relcriteria MODIFY value VARCHAR(512)", array());
			
			// Indexes
			$this->ExecuteQuery('ALTER TABLE vtiger_invoice_recurring_info ADD PRIMARY KEY (salesorderid)',array());
			$this->ExecuteQuery('ALTER TABLE vtiger_modtracker_basic ADD INDEX crmidx (crmid)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_modtracker_detail ADD INDEX idx (id)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_leaddetails add index email_idx (email)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_contactdetails add index email_idx (email)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_account add index email_idx (email1, email2)', array());
			$this->ExecuteQuery('ALTER TABLE com_vtiger_workflowtask_queue DROP INDEX com_vtiger_workflowtask_queue_idx',array());
			$this->ExecuteQuery('ALTER TABLE vtiger_mailscanner_ids add index scanner_message_ids_idx (scannerid, messageid)', array());
			
			$adb->getUniqueID("vtiger_inventoryproductrel");
			$this->ExecuteQuery("UPDATE vtiger_inventoryproductrel_seq SET id=(select max(lineitem_id) from vtiger_inventoryproductrel);",array());
			
			$adb->getUniqueID("vtiger_modtracker_basic");
			$this->ExecuteQuery("UPDATE vtiger_modtracker_basic_seq SET id=(select max(id) from vtiger_modtracker_basic);",array());

			/////////////////////////////////
			//  VTIGER CRM 6.0  interesting changes
			/////////////////////////////////
			
			/////////////////////////////////
			//  SQL Optimizations
			/////////////////////////////////
			/*
			 * Created by Boris CLEMENT - ABOnline solutions (boris@abo-s.com): http://www.abo-s.com/
			 * Release on 2012-05-31   Available for vtigercrm 5.4
			 * Modified by Joe Bordes on 2014-06-24 for coreBOS 5.5
			 * Make a backup of your database before applying this patch
			 */
			$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflows` DROP INDEX `com_vtiger_workflows_idx`');
			$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks` DROP INDEX `com_vtiger_workflowtasks_idx`, ADD INDEX `com_vtiger_workflowtasks_workflowidx` ( `workflow_id` )');
			$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks_entitymethod` DROP INDEX com_vtiger_workflowtasks_entitymethod_idx');
			$this->ExecuteQuery('ALTER TABLE `vtiger_activity` ADD INDEX `activity_activitytype_idx` ( `activitytype` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_activity_reminder_popup` ADD INDEX `activity_reminder_popup_recordid_idx` ( `recordid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_announcement` DROP INDEX `announcement_creatorid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_asteriskextensions` ADD INDEX `asteriskextensions_userid_idx` ( `userid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_attachments` DROP INDEX attachments_attachmentsid_idx');
			$this->ExecuteQuery('ALTER TABLE `vtiger_audit_trial` ADD INDEX `audit_trial_userid_idx` ( `userid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_blocks` ADD INDEX `sequence` ( `sequence` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_campaign` DROP INDEX `campaign_campaignid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_crmentityrel` ADD INDEX `crmentityrel_crmid_relcrmid_idx` ( `crmid` , `relcrmid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_customview` ADD INDEX `userid_idx` ( `userid` ), ADD INDEX `setmetrics` ( `setmetrics` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_cvstdfilter` DROP INDEX `cvstdfilter_cvid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_email_access` ADD INDEX ( `crmid` , `mailid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_entityname` DROP INDEX `entityname_tabid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_freetags` ADD INDEX `tag_idx` ( `tag` ),  ADD INDEX `multi_idx` ( `id` , `tag` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_freetagged_objects` DROP INDEX `freetagged_objects_tag_id_tagger_id_object_id_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_homedashbd` DROP INDEX `stuff_stuffid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_homedefault` DROP INDEX `stuff_stuffid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_homemodule` DROP INDEX `stuff_stuffid_idx`, ADD INDEX `homemodule_customviewid_idx` ( `customviewid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_homerss` DROP INDEX `stuff_stuffid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_homestuff` DROP INDEX `stuff_stuffid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_inventorysubproductrel` ADD INDEX `inventorysubproductrel_productid_idx` ( `productid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_invoice` DROP INDEX `invoice_purchaseorderid_idx`, ADD INDEX `invoice_contactid_idx` ( `contactid` ), ADD INDEX `invoice_accountid_idx` ( `accountid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_mail_accounts` ADD INDEX `userid_idx` ( `user_id` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_modcomments` ADD INDEX `modcomments_related_to_idx` ( `related_to` ), ADD INDEX `modcomments_modcommentsid_idx` ( `modcommentsid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_modtracker_basic` ADD INDEX `modtracker_basic_crmid_idx` ( `crmid` ) , ADD INDEX `modtracker_basic_whodid_idx` ( `whodid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_notebook_contents` ADD INDEX `notebook_contents_userid_idx` ( `userid` ), ADD INDEX `notebook_contents_userid_notebookid_idx` ( `userid` , `notebookid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_notes` DROP INDEX `notes_notesid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_picklist_dependency` ADD INDEX `picklist_dependency_tabid_idx` ( `tabid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_products` ADD INDEX `productname` ( `productname` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_productcurrencyrel` ADD INDEX ( `productid` , `currencyid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_profile2globalpermissions` DROP INDEX `idx_profile2globalpermissions`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_profile2standardpermissions` DROP INDEX `profile2standardpermissions_profileid_tabid_operation_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_profile2utility` DROP INDEX `profile2utility_profileid_tabid_activityid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_quotes` ADD INDEX `quotes_accountid_idx` ( `accountid` ), ADD INDEX `quotes_currencyid_idx` ( `currency_id` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_relatedlists`  DROP INDEX `relatedlists_relation_id_idx`, ADD INDEX `relatedlists_tabid_idx` ( `tabid` ), ADD INDEX `relatedlists_related_tabid_idx` ( `related_tabid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_relatedlists_rb` ADD INDEX `relatedlists_rb_entityid_idx` ( `entityid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_report` ADD INDEX `report_owner_idx` ( `owner` ), ADD INDEX `report_sharingtype_idx` ( `sharingtype` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_reportsharing` ADD INDEX `reportsharing_reportid_idx` ( `reportid` ), ADD INDEX `reportsharing_shareid_idx` ( `shareid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_role` ADD INDEX `parent` ( `parentrole` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_role2picklist` DROP INDEX `role2picklist_roleid_picklistid_idx` , ADD INDEX `role2picklist_roleid_picklistid_idx` ( `roleid` , `picklistid` , `picklistvalueid` , `sortid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_role2profile` DROP INDEX `role2profile_roleid_profileid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_salesorder` ADD INDEX `salesorder_accountid_idx` ( `accountid` ), ADD INDEX `salesorder_quoteid_idx` ( `quoteid` ), ADD INDEX `salesorder_potentialid_idx` ( `potentialid` ),  ADD INDEX `salesorder_subject_idx` ( `subject` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_selectquery` DROP INDEX `selectquery_queryid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_servicecontracts` ADD PRIMARY KEY ( `servicecontractsid` ), ADD INDEX `sc_related_to_idx` ( `sc_related_to` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_tab` DROP INDEX `tab_tabid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_ticketcomments` ADD INDEX `ticketcomments_ownerid_idx` ( `ownerid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_tracker` ADD INDEX `tracker_multi_idx` ( `user_id` , `item_id` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_troubletickets` DROP INDEX `troubletickets_ticketid_idx`, ADD INDEX `parentid_idx` ( `parent_id` ) ,  ADD INDEX `productid_idx` ( `product_id` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_users2group` DROP INDEX `users2group_groupname_uerid_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_webforms` DROP INDEX `publicid` , DROP INDEX `webforms_webforms_id_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_webforms_field` DROP INDEX `webforms_webforms_field_idx`');
			$this->ExecuteQuery('ALTER TABLE `vtiger_wsapp_recordmapping` ADD INDEX `wsapp_recordmapping_appid_idx` ( `appid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_wsapp_sync_state` ADD INDEX `wsapp_sync_state_userid_idx` ( `userid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_asteriskextensions` ADD UNIQUE `asteriskextensions_userid_uniq` ( `userid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_asteriskincomingevents` ADD INDEX `asteriskincomingevents_relcrmid_idx` ( `relcrmid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_project` ADD PRIMARY KEY ( `projectid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_projectmilestone` ADD INDEX `projectmilestone_projectid_idx` ( `projectid` )');
			$this->ExecuteQuery('ALTER TABLE `vtiger_projecttask` ADD INDEX `projecttask_projectid_idx` ( `projectid` )');
			
			/* int(11) vs int(anything-else)
			 *   http://stackoverflow.com/questions/7552223/int11-vs-intanything-else
			 *   http://stackoverflow.com/questions/5562322/difference-between-int-and-int3-data-types-in-my-sql
			 */
			/////////////////////////////////
			//  SQL Optimizations
			/////////////////////////////////
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
	
}
