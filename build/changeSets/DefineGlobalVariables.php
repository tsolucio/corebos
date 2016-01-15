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
			$global_variables = array(
				'product_service_default',
				'Debug_Record_Not_Found',
				'Debug_Report_Query',
				'Product_Default_Units',
				'Service_Default_Units',
				'Maximum_Scheduled_Workflows',
				'Billing_Address_Checked',
				'Shipping_Address_Checked',
				'Tax_Type_Default',
				'calendar_call_default_duration',
				'calendar_other_default_duration',
				'calendar_sort_users_by',
				'Debug_Send_VtigerCron_Error',
				'Import_Full_CSV',
				'Lead_Convert_TransferToAccount',
				'Show_Copy_Adress_Header',
				'SalesOrderStatusOnInvoiceSave',
				'QuoteStatusOnSalesOrderSave',
				'GoogleCalendarSync_BaseUpdateMonths',
				'GoogleCalendarSync_BaseCreateMonths',
				'Report.Excel.Export.RowHeight',
				'Calendar_Modules_Panel_Visible',
				'Calendar_Default_Reminder_Minutes',
				'Application_Global_Search_Binary',
				'Calendar_Slot_Minutes',
			);
			
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			$field = Vtiger_Field::getInstance('gvname',$moduleInstance);
			if ($field) {
				$field->setPicklistValues($global_variables);
			}
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
}