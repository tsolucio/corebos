<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class DefineGlobalVariables extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$global_variables = array(
				'Debug_Record_Not_Found',
				'Debug_Report_Query',
				'Debug_ListView_Query',
				'Debug_Popup_Query',
				'Debug_Send_VtigerCron_Error',
				'Debug_Send_AdminLoginIPAuth_Error',
				'Debug_Calculate_Response_Time',

				'Application_Global_Search_SelectedModules',
				'Application_Storage_Directory',
				'Application_Storage_SaveStrategy',
				'Application_Global_Search_Binary',
				'Application_OpenRecordInNewXOnRelatedList',
				'Application_OpenRecordInNewXOnListView',
				'Application_MaxFailedLoginAttempts',
				'Application_ExpirePasswordAfterDays',
				'Application_AdminLoginIPs',
				'Application_ListView_MaxColumns',
				'Application_Action_Panel_Open',
				'Application_Search_Panel_Open',
				'Application_TrackerMaxHistory',
				'Application_Announcement',
				'Application_Display_World_Clock',
				'Application_Display_Calculator',
				'Application_Display_Mini_Calendar',
				'Application_Use_RTE',
				'Application_Default_Action',
				'Application_Default_Module',
				'Application_Allow_Exports',
				'Application_ListView_Max_Text_Length',
				'Application_ListView_PageSize',
				'Application_Upload_MaxSize',
				'Application_Single_Pane_View',
				'Application_Minimum_Cron_Frequency',
				'Application_Customer_Portal_URL',
				'Application_Help_URL',
				'Application_UI_Name',
				'Application_UI_Version',
				'Application_UI_URL',
				'Application_Group_Selection_Permitted',

				'Calendar_Modules_Panel_Visible',
				'Calendar_Default_Reminder_Minutes',
				'Calendar_Slot_Minutes',
				'Calendar_Show_Inactive_Users',
				'Calendar_Show_Group_Events',
				'calendar_call_default_duration',
				'calendar_other_default_duration',
				'calendar_sort_users_by',

				'CronTasks_cronWatcher_mailto',

				'BusinessMapping_SalesOrder2Invoice',
				'BusinessMapping_PotentialOnCampaignRelation',

				'Mobile_Module_by_default',

				'Webservice_showUserAdvancedBlock',
				'Webservice_CORS_Enabled_Domains',
				'Webservice_Enabled',
				'WebService_Session_Life_Span',
				'WebService_Session_Idle_Time',
				'SOAP_CustomerPortal_Enabled',

				'Users_ReplyTo_SecondEmail',
				'Users_Default_Send_Email_Template',

				'Accounts_BlockDuplicateName',
				'Campaign_CreatePotentialOnAccountRelation',
				'Campaign_CreatePotentialOnContactRelation',
				'GoogleCalendarSync_BaseUpdateMonths',
				'GoogleCalendarSync_BaseCreateMonths',
				'Import_Full_CSV',
				'Import_Batch_Limit',
				'Import_Scheduled_Limit',
				'Lead_Convert_TransferToAccount',
				'PBX_Get_Line_Prefix',
				'Product_Copy_Bundle_OnDuplicate',
				'Product_Show_Subproducts_Popup',
				'Product_Permit_Relate_Bundle_Parent',
				'Product_Permit_Subproduct_Be_Parent',
				'Product_Maximum_Number_Images',
				'Workflow_Send_Email_ToCCBCC',
				'Workflow_GeoDistance_Country_Default',
				'ModComments_DefaultCriteria',
				'ModComments_DefaultBlockStatus',
				'EMail_OpenTrackingEnabled',
				'Email_Attachments_Folder',
				'ToolTip_MaxFieldValueLength',
				'HelpDesk_Support_EMail',
				'HelpDesk_Support_Name',
				'HelpDesk_Support_Reply_EMail',
				'Home_Display_Empty_Blocks',
				'Document_Folder_View',

				'Report_Send_Scheduled_ifEmpty',
				'Report_ListView_PageSize',
				'Report_MaxRows_OnScreen',

				'Inventory_ListPrice_ReadOnly',

				'Maximum_Scheduled_Workflows', // rename to Workflow_Maximum_Scheduled_Workflows
				'Billing_Address_Checked', // rename to Application_Billing_Address_Checked
				'Shipping_Address_Checked', // rename to Application_Shipping_Address_Checked
				'Show_Copy_Adress_Header', // rename to Application_Show_Copy_Adress_Header
				'Tax_Type_Default', // rename to Inventory_Tax_Type_Default
				'product_service_default', // rename to Inventory_ProductService_Default
				'Product_Default_Units', // rename to Inventory_Product_Default_Units
				'Service_Default_Units', // rename to Inventory_Service_Default_Units
				'SalesOrderStatusOnInvoiceSave', // rename to SalesOrder_StatusOnInvoiceSave
				'QuoteStatusOnSalesOrderSave',  // rename to Quote_StatusOnSalesOrderSave
				'Report.Excel.Export.RowHeight', // rename to Report_Excel_Export_RowHeight
			);
			$delete_these = array(
				'preload_prototype',
				'preload_jquery',
				'first_day_of_week',
				'calendar_display',
				'world_clock_display',
				'calculator_display',
				'history_max_viewed',
				'default_module',
				'default_action',
				'allow_exports',
				'listview_max_textlength',
				'list_max_entries_per_page',
				'upload_maxsize',
				'helpdesk_support_email_id',
				'helpdesk_support_email_reply_id',
				'limitpage_navigation',
				'default_timezone',
				'import_dir',
				'upload_dir',
				'upload_badext',
				'default_theme',
				'currency_name',
				'minimum_cron_frequency',
				'maxWebServiceSessionLifeSpan',
				'maxWebServiceSessionIdleTime',
				'default_language',
				'corebos_app_name',
				'corebos_app_url',
				'SOAP_Thunderbird_Enabled',
			);
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			$field = Vtiger_Field::getInstance('gvname',$moduleInstance);
			if ($field) {
				$field->setPicklistValues($global_variables);
				foreach ($delete_these as $gvar) {
					$sql = 'select * from vtiger_gvname where gvname=?';
					$result = $adb->pquery($sql, array($gvar));
					if ($adb->num_rows($result)>0) {
						$origPicklistID = $adb->query_result($result, 0, 'picklist_valueid');
						$sql = 'delete from vtiger_gvname where gvname=?';
						$this->ExecuteQuery($sql, array($gvar));
						$sql = 'delete from vtiger_role2picklist where picklistvalueid=?';
						$this->ExecuteQuery($sql, array($origPicklistID));
						$sql = 'DELETE FROM vtiger_picklist_dependency WHERE sourcevalue=? AND sourcefield=? AND tabid=?';
						$this->ExecuteQuery($sql, array($gvar, 'gvname', $moduleInstance->id));
					}
				}
			}
			$this->ExecuteQuery("ALTER TABLE `vtiger_globalvariable` CHANGE `value` `value` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
}