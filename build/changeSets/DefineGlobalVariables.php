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

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$global_variables = array(
				'Debug_Record_Not_Found',
				'Debug_Report_Query',
				'Debug_ListView_Query',
				'Debug_RelatedList_Query',
				'Debug_Popup_Query',
				'Debug_Email_Sending',
				'Debug_Send_VtigerCron_Error',
				'Debug_Send_AdminLoginIPAuth_Error',
				'Debug_Send_UserLoginIPAuth_Error',
				'Debug_Calculate_Response_Time',

				'Application_Global_Search_SelectedModules',
				'Application_Global_Search_Binary',
				'Application_Global_Search_TopModules',
				'Application_Global_Search_Active',
				'Application_Global_Search_Autocomplete_Limit',
				'Application_Storage_Directory',
				'Application_Storage_SaveStrategy',
				'Application_OpenRecordInNewXOnRelatedList',
				'Application_OpenRecordInNewXOnListView',
				'Application_MaxFailedLoginAttempts',
				'Application_ExpirePasswordAfterDays',
				'Application_AdminLoginIPs',
				'Application_UserLoginIPs',
				'Application_DetailView_Inline_Edit',
				'Application_DetailView_Record_Navigation',
				'Application_TrackerMaxHistory',
				'Application_Announcement',
				'Application_Display_World_Clock',
				'Application_Display_Calculator',
				'Application_Display_Mini_Calendar',
				'Application_Use_RTE',
				'Application_Default_Action',
				'Application_Default_Module',
				'Application_Allow_Exports',
				'Application_ListView_MaxColumns',
				'Application_ListView_Max_Text_Length',
				'Application_ListView_PageSize',
				'Application_ListView_Default_Sort_Order',
				'Application_ListView_Record_Change_Indicator',
				'Application_ListView_Default_Sorting',
				'Application_ListView_Compute_Page_Count',
				'Application_ListView_Sum_Currency',
				'Application_ListView_SearchColumns',
				'Application_SaveAndRepeatActive',
				'Application_Upload_MaxSize',
				'Application_Single_Pane_View',
				'Application_Minimum_Cron_Frequency',
				'Application_Customer_Portal_URL',
				'Application_Customer_Portal_BeingUsed',
				'Application_Help_URL',
				'Application_UI_Name',
				'Application_UI_NameHTML',
				'Application_UI_CompanyName',
				'Application_UI_ShowGITVersion',
				'Application_UI_ShowGITDate',
				'Application_UI_Version',
				'Application_UI_URL',
				'Application_Group_Selection_Permitted',
				'Application_B2B',
				'Application_FirstTimeLogin_Template',
				'Application_Permit_Assign_Up',
				'Application_Permit_Assign_SameRole',
				'Application_Permit_Assign_AllGroups',
				'Application_User_SortBy',
				'Application_Pagination_Limit',

				'RelatedList_Activity_DefaultStatusFilter',

				'Calendar_Modules_Panel_Visible',
				'Calendar_Default_Reminder_Minutes',
				'Calendar_Slot_Minutes',
				'Calendar_Slot_Event_Overlap',
				'Calendar_Show_Inactive_Users',
				'Calendar_Show_Group_Events',
				'Calendar_Status_Panel_Visible',
				'Calendar_Push_End_On_Start_Change',
				'Calendar_PopupReminder_DaysPast',
				'Calendar_Priority_Panel_Visible',
				'Calendar_Show_Only_My_Events',
				'Calendar_Show_WeekNumber',

				'CronTasks_cronWatcher_mailto',
				'CronTasks_cronWatcher_TimeThreshold',

				'BusinessQuestion_TableAnswer_Limit',

				'BusinessMapping_SalesOrder2Invoice',
				'BusinessMapping_PotentialOnCampaignRelation',
				'BusinessMapping_Quotes2Invoice',
				'BusinessMapping_Quotes2SalesOrder',

				'Mobile_Module_by_default',
				'Mobile_Related_Modules',
				'Mobile_UI_Enabled',

				'Webservice_showUserAdvancedBlock',
				'Webservice_CORS_Enabled_Domains',
				'Webservice_Enabled',
				'WebService_Session_Life_Span',
				'WebService_Session_Idle_Time',
				'SOAP_CustomerPortal_Enabled',
				'SOAP_Outlook_Enabled',

				'Users_ReplyTo_SecondEmail',
				'Users_Default_Send_Email_Template',
				'Users_Select_Inactive',
				'User_AuthenticationType',
				'User_2FAAuthentication',
				'User_2FAAuthentication_SendMethod',

				'Accounts_BlockDuplicateName',
				'Campaign_CreatePotentialOnAccountRelation',
				'Campaign_CreatePotentialOnContactRelation',
				'GoogleCalendarSync_BaseUpdateMonths',
				'GoogleCalendarSync_BaseCreateMonths',
				'Import_Full_CSV',
				'Import_Batch_Limit',
				'Import_Scheduled_Limit',
				'Export_Field_Separator_Symbol',
				'Export_RelatedField_GetValueFrom',
				'Export_RelatedField_NameForSearch',
				'Lead_Convert_TransferToAccount',
				'Lead_Convert_OpportunitySelected',
				'Lead_Convert_ContactSelected',
				'PBX_Get_Line_Prefix',
				'PBX_Unknown_CallerID',
				'Product_Copy_Bundle_OnDuplicate',
				'Product_Show_Subproducts_Popup',
				'Product_Permit_Relate_Bundle_Parent',
				'Product_Permit_Subproduct_Be_Parent',
				'Product_Maximum_Number_Images',
				'Product_SubProduct_PriceRollUp',
				'Product_SubProduct_CostRollUp',
				'PurchaseOrder_TransferCostPrice',
				'PurchaseOrder_IgnoreTransferDiscount',
				'Workflow_Send_Email_ToCCBCC',
				'Workflow_GeoDistance_Country_Default',
				'Workflow_GeoDistance_ServerIP',
				'Workflow_GeoDistance_Email',
				'Workflow_GeoDistance_Nominatim_Server',
				'ModComments_DefaultCriteria',
				'ModComments_DefaultBlockStatus',
				'EMail_OpenTrackingEnabled',
				'Email_Attachments_Folder',
				'EMail_Maximum_Number_Attachments',
				'EMail_CustomCurrentDate_Format',
				'MailManager_Show_SentTo_Links',
				'ToolTip_MaxFieldValueLength',
				'HelpDesk_Support_EMail',
				'HelpDesk_Support_Name',
				'HelpDesk_Support_Reply_EMail',
				'HelpDesk_Notify_Owner_EMail',
				'HelpDesk_Sort_Comments_ASC',
				'Document_Folder_View',
				'Document_CreateSelectContactFolder',
				'Document_CreateSelectAccountFolder',
				'Document_CreateSelectAccountFolderForContact',
				'HomePage_Widget_Group_Size',
				'Zero_Bounce_API_KEY',
				'GenDoc_CopyLabelToClipboard',

				'Report_Send_Scheduled_ifEmpty',
				'Report_ListView_PageSize',
				'Report_MaxRows_OnScreen',
				'Report_MaxRelated_Modules',
				'Report_HeaderOnXLS',
				'Report_HeaderOnPDF',

				'Inventory_ListPrice_ReadOnly',
				'Inventory_Show_ShippingHandlingCharges',
				'GContacts_Max_Results',

				'CustomerPortal_PDF_Modules',
				'CustomerPortal_PDF',
				'CustomerPortal_PDFTemplate_Quote',
				'CustomerPortal_PDFTemplate_SalesOrder',
				'CustomerPortal_PDFTemplate_Invoice',
				'CustomerPortal_PDFTemplate_PurchaseOrder',
				'PBXManager_SearchOnlyOnTheseFields',
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
				'helpdesk_support_name',
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
				'Home_Display_Empty_Blocks',
			);
			$rename_these = array(
				'Show_Copy_Adress_Header' => array(
					'to' => 'Application_Show_Copy_Address',
					'change' => array(
						array(
							'not' => 'yes',
							'to' => 0
						),
						array(
							'from' => 'yes',
							'to' => 1
						),
					)
				),
				'Maximum_Scheduled_Workflows' => array(
					'to' => 'Workflow_Maximum_Scheduled',
				),
				'Billing_Address_Checked' => array(
					'to' => 'Application_Billing_Address_Checked',
					'change' => array(
						array(
							'not' => 'true',
							'to' => 0
						),
						array(
							'from' => 'true',
							'to' => 1
						),
					)
				),
				'Shipping_Address_Checked' => array(
					'to' => 'Application_Shipping_Address_Checked',
					'change' => array(
						array(
							'not' => 'false',
							'to' => 1
						),
						array(
							'from' => 'false',
							'to' => 0
						),
					)
				),
				'Tax_Type_Default' => array(
					'to' => 'Inventory_Tax_Type_Default',
				),
				'product_service_default' => array(
					'to' => 'Inventory_ProductService_Default',
				),
				'Product_Default_Units' => array(
					'to' => 'Inventory_Product_Default_Units',
				),
				'Service_Default_Units' => array(
					'to' => 'Inventory_Service_Default_Units',
				),
				'SalesOrderStatusOnInvoiceSave' => array(
					'to' => 'SalesOrder_StatusOnInvoiceSave',
				),
				'QuoteStatusOnSalesOrderSave' => array(
					'to' => 'Quote_StatusOnSalesOrderSave',
				),
				'Report.Excel.Export.RowHeight' => array(
					'to' => 'Report_Excel_Export_RowHeight',
				),
				'calendar_call_default_duration' => array(
					'to' => 'Calendar_call_default_duration',
				),
				'calendar_other_default_duration' => array(
					'to' => 'Calendar_other_default_duration',
				),
				'calendar_sort_users_by' => array(
					'to' => 'Calendar_sort_users_by',
				),
				'preload_jscalendar' => array(
					'to' => 'Application_JSCalendar_Load',
					'change' => array(
						array(
							'not' => 'true',
							'to' => 0
						),
						array(
							'from' => 'true',
							'to' => 1
						),
					)
				),
				'Application_Action_Panel_Open' => array(
					'to' => 'Application_DetailView_ActionPanel_Open',
				),
				'Application_Search_Panel_Open' => array(
					'to' => 'Application_ListView_SearchPanel_Open',
				),
			);
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			$field = Vtiger_Field::getInstance('gvname', $moduleInstance);
			if ($field) {
				foreach ($rename_these as $gvar => $change) {
					$rschk = $adb->pquery('select count(*) from vtiger_gvname where BINARY gvname=?', array($gvar));
					$checkold = $adb->query_result($rschk, 0, 0);
					$rschk = $adb->pquery('select count(*) from vtiger_gvname where BINARY gvname=?', array($change['to']));
					$checknew = $adb->query_result($rschk, 0, 0);
					if ($checkold > 0) {
						if ($checknew > 0) {
							$delete_these[] = $gvar;
						} else { // rename
							$sql = 'UPDATE vtiger_gvname SET gvname=? WHERE BINARY gvname=?';
							$this->ExecuteQuery($sql, array($change['to'],$gvar));
							$table_name = 'vtiger_globalvariable';
							$columnName = 'gvname';
							$sql = "update $table_name set $columnName=? where BINARY $columnName=?";
							$this->ExecuteQuery($sql, array($change['to'],$gvar));
							$sql = "UPDATE vtiger_picklist_dependency SET sourcevalue=? WHERE BINARY sourcevalue=? AND sourcefield='gvname' AND tabid=?";
							$this->ExecuteQuery($sql, array($change['to'], $gvar, getTabid('GlobalVariable')));
							if (isset($change['change'])) {
								foreach ($change['change'] as $fromto) {
									if (isset($fromto['not'])) {
										$sql = 'update vtiger_globalvariable set value=? where gvname=? and value!=?';
										$params = array($fromto['to'],$change['to'],$fromto['not']);
									} else {
										$sql = 'update vtiger_globalvariable set value=? where gvname=? and value=?';
										$params = array($fromto['to'],$change['to'],$fromto['from']);
									}
									$this->ExecuteQuery($sql, $params);
								}
							}
						}
					} else {
						if ($checknew > 0) {
							// all ok => do nothing
						} else {
							$global_variables[] = $change['to'];
						}
					}
				}
				$field->setPicklistValues($global_variables);
				foreach ($delete_these as $gvar) {
					$sql = 'select * from vtiger_gvname where BINARY gvname=?';
					$result = $adb->pquery($sql, array($gvar));
					if ($adb->num_rows($result)>0) {
						$origPicklistID = $adb->query_result($result, 0, 'picklist_valueid');
						$sql = 'delete from vtiger_gvname where BINARY gvname=?';
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
