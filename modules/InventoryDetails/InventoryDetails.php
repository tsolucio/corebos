<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class InventoryDetails extends CRMEntity {
	public $table_name = 'vtiger_inventorydetails';
	public $table_index= 'inventorydetailsid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-product-item-transaction', 'class' => 'slds-icon', 'icon'=>'product_item_transaction');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_inventorydetailscf', 'inventorydetailsid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_inventorydetailscf'=>array('inventorydetailsid','vtiger_inventorydetails', 'inventorydetailsid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_inventorydetails', 'vtiger_inventorydetailscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_inventorydetails'   => 'inventorydetailsid',
		'vtiger_inventorydetailscf' => 'inventorydetailsid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Inventory Details No'=> array('inventorydetails' => 'inventorydetails_no'),
		'Products'=> array('inventorydetails' => 'productid'),
		'Related To'=> array('inventorydetails' => 'related_to'),
		'Accounts'=> array('inventorydetails' => 'account_id'),
		'Contacts'=> array('inventorydetails' => 'contact_id'),
		'Vendors'=> array('inventorydetails' => 'vendor_id'),
		'Quantity'=> array('inventorydetails' => 'quantity'),
		'Listprice'=> array('inventorydetails' => 'listprice'),
		'Line Total'=> array('inventorydetails' => 'linetotal'),
		'Assigned To' => array('crmentity' =>'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Inventory Details No'=> 'inventorydetails_no',
		'Products'=> 'productid',
		'Related To'=> 'related_to',
		'Accounts'=> 'account_id',
		'Contacts'=> 'contact_id',
		'Vendors'=> 'vendor_id',
		'Quantity'=> 'quantity',
		'Listprice'=> 'listprice',
		'Line Total'=> 'linetotal',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'inventorydetails_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Inventory Details No'=> array('inventorydetails' => 'inventorydetails_no'),
		'Products'=> array('inventorydetails' => 'productid'),
		'Related To'=> array('inventorydetails' => 'related_to'),
		'Accounts'=> array('inventorydetails' => 'account_id'),
		'Contacts'=> array('inventorydetails' => 'contact_id'),
		'Vendors'=> array('inventorydetails' => 'vendor_id'),
		'Quantity'=> array('inventorydetails' => 'quantity'),
		'Listprice'=> array('inventorydetails' => 'listprice'),
		'Line Total'=> array('inventorydetails' => 'linetotal'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Inventory Details No'=> 'inventorydetails_no',
		'Products'=> 'productid',
		'Related To'=> 'related_to',
		'Accounts'=> 'account_id',
		'Contacts'=> 'contact_id',
		'Vendors'=> 'vendor_id',
		'Quantity'=> 'quantity',
		'Listprice'=> 'listprice',
		'Line Total'=> 'linetotal',
	);

	// For Popup window record selection
	public $popup_fields = array('inventorydetails_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'inventorydetails_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'inventorydetails_no';

	// Required Information for enabling Import feature
	public $required_fields = array('inventorydetails_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'inventorydetails_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'inventorydetails_no');

	public function save_module($module) {
		global $adb, $current_user;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		$handler = vtws_getModuleHandlerFromName('InventoryDetails', $current_user);
		$meta = $handler->getMeta();
		$dbformat = DataTransform::sanitizeCurrencyFieldsForDB($this->column_fields, $meta);
		if (isset($dbformat['cost_price'])) {
			$this->column_fields['cost_gross'] = (float)$dbformat['quantity'] * (float)$dbformat['cost_price'];
		} else {
			$this->column_fields['cost_gross'] = 0;
		}
		$adb->pquery('update vtiger_inventorydetails set cost_gross=? where inventorydetailsid=?', array($this->column_fields['cost_gross'], $this->id));
		if (!empty($this->column_fields['productid'])) {
			$this->column_fields['total_stock'] = getPrdQtyInStck($this->column_fields['productid']);
			$adb->pquery('update vtiger_inventorydetails set total_stock=? where inventorydetailsid=?', array($this->column_fields['total_stock'], $this->id));
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			//Handle post installation actions
			require_once 'modules/com_vtiger_workflow/include.inc';
			require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
			require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
			global $adb;

			$mod=Vtiger_Module::getInstance('InventoryDetails');
			$this->setModuleSeqNumber('configure', $modulename, '', '000000001');

			$modAccounts=Vtiger_Module::getInstance('Accounts');
			$modContacts=Vtiger_Module::getInstance('Contacts');
			$modVnd=Vtiger_Module::getInstance('Vendors');
			$modInvoice=Vtiger_Module::getInstance('Invoice');
			$modSO=Vtiger_Module::getInstance('SalesOrder');
			$modPO=Vtiger_Module::getInstance('PurchaseOrder');
			$modQt=Vtiger_Module::getInstance('Quotes');
			$modPrd=Vtiger_Module::getInstance('Products');
			$modSrv=Vtiger_Module::getInstance('Services');

			if ($modAccounts) {
				$modAccounts->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modContacts) {
				$modContacts->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modVnd) {
				$modVnd->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modInvoice) {
				$modInvoice->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modSO) {
				$modSO->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modPO) {
				$modPO->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modQt) {
				$modQt->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modPrd) {
				$modPrd->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modSrv) {
				$modSrv->setRelatedList($mod, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Line Completed'");
			if ($wfrs && $adb->num_rows($wfrs)==1) {
				echo 'Workfolw already exists!';
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				$InvDtWorkFlow = $workflowManager->newWorkFlow("InventoryDetails");
				$InvDtWorkFlow->test = '[{"fieldname":"units_delivered_received","operation":"equal to","value":"quantity","valuetype":"fieldname","joincondition":"and","groupid":"0"}]';
				$InvDtWorkFlow->description = "Line Completed";
				$InvDtWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
				$InvDtWorkFlow->defaultworkflow = 1;
				$workflowManager->save($InvDtWorkFlow);
				$task = $taskManager->createTask('VTUpdateFieldsTask', $InvDtWorkFlow->id);
				$task->active = true;
				$task->summary = 'Mark as Line Completed';
				$task->field_value_mapping = '[{"fieldname":"line_completed","valuetype":"rawtext","value":"true:boolean"}]';
				$taskManager->saveTask($task);
			}
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	public static function createInventoryDetails($related_focus, $module) {
		global $adb, $current_user, $currentModule;
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('InventoryDetails');
		$save_currentModule = $currentModule;
		$currentModule = 'InventoryDetails';
		$related_to = $related_focus->id;
		$ipr_cols = $adb->query("show fields from vtiger_inventoryproductrel where field like 'tax%'");
		$txsql = '';
		$txsumsql = '(';
		while ($txinfo = $adb->fetch_array($ipr_cols)) {
			$tname = $txinfo['field'];
			$txsql .= "coalesce($tname, 0) AS id_".$tname.'_perc,';
			$txsumsql .= " COALESCE($tname, 0 ) +";
		}
		$txsumsql = rtrim($txsumsql, '+').')';
		$taxtype = getInventoryTaxType($module, $related_to);
		if ($taxtype == 'group') {
			$query = "SELECT id as related_to, vtiger_inventoryproductrel.productid, sequence_no, lineitem_id, quantity, listprice, comment as description,
			quantity * listprice AS extgross, $txsql
			COALESCE( discount_percent, COALESCE( discount_amount *100 / ( quantity * listprice ) , 0 ) ) AS discount_percent,
			COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 ) ) AS discount_amount,
			(quantity * listprice) - COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 )) AS extnet,
			((quantity * listprice) - COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 ))) AS linetotal,
			case when vtiger_products.productid != '' then vtiger_products.cost_price else vtiger_service.cost_price end as cost_price,
			case when vtiger_products.productid != '' then vtiger_products.vendor_id else 0 end as vendor_id
			FROM vtiger_inventoryproductrel
			LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_service ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid
			WHERE id = ? ORDER BY sequence_no";
		} elseif ($taxtype == 'individual') {
			$query = "SELECT id as related_to, vtiger_inventoryproductrel.productid, sequence_no, lineitem_id, quantity, listprice, comment as description,
			$txsql
			$txsumsql as tax_percent,
			quantity * listprice AS extgross,
			COALESCE( discount_percent, COALESCE( discount_amount *100 / ( quantity * listprice ) , 0 ) ) AS discount_percent,
			COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 ) ) AS discount_amount,
			(quantity * listprice) - COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 )) AS extnet,
			((quantity * listprice) - COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 ))) * $txsumsql/100 AS linetax,
			((quantity * listprice) - COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 ))) * (1 + $txsumsql/100) AS linetotal,
			case when vtiger_products.productid != '' then vtiger_products.cost_price else vtiger_service.cost_price end as cost_price,
			case when vtiger_products.productid != '' then vtiger_products.vendor_id else 0 end as vendor_id
			FROM vtiger_inventoryproductrel
			LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_service ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid
			WHERE id = ? ORDER BY sequence_no";
		}
		$res_inv_lines = $adb->pquery($query, array($related_to));

		$accountid = '0';
		$contactid = '0';

		switch ($module) {
			case 'Quotes':
			case 'SalesOrder':
			case 'Invoice':
					$accountid = $related_focus->column_fields['account_id'];
					$contactid = $related_focus->column_fields['contact_id'];
				break;
			case 'Issuecards':
					$accountid = $related_focus->column_fields['accid'];
					$contactid = $related_focus->column_fields['ctoid'];
				break;
			case 'PurchaseOrder':
					$contactid = $related_focus->column_fields['contact_id'];
				break;
			default:
				break;
		}
		// Delete all InventoryDetails where related with $related_to
		$res_to_del = $adb->pquery(
			'SELECT vtiger_inventorydetails.inventorydetailsid
				FROM vtiger_inventorydetails
				INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=vtiger_inventorydetails.inventorydetailsid
				WHERE vtiger_crmentity.deleted=0 AND vtiger_inventorydetails.related_to=?
					and vtiger_inventorydetails.lineitem_id not in (select lineitem_id from vtiger_inventoryproductrel where id=?)',
			array($related_to,$related_to)
		);
		while ($invdrow = $adb->getNextRow($res_to_del, false)) {
			$invdet_focus = new InventoryDetails();
			$invdet_focus->id = $invdrow['inventorydetailsid'];
			$invdet_focus->trash('InventoryDetails', $invdet_focus->id);
		}

		$requestindex = 1;
		$inputFiles = isset($_FILES) ? $_FILES : array();
		unset($_FILES);
		while (isset($_REQUEST['deleted'.$requestindex]) && $_REQUEST['deleted'.$requestindex] == 1) {
			$requestindex++;
		}
		$handler = vtws_getModuleHandlerFromName('InventoryDetails', $current_user);
		$meta = $handler->getMeta();
		// read $res_inv_lines result to create a new InventoryDetail for each register.
		// Remember to take the Vendor if the Product is related with this.
		while ($row = $adb->getNextRow($res_inv_lines, false)) {
			$invdet_focus = array();
			$invdet_focus = new InventoryDetails();
			$rec_exists = $adb->pquery(
				'SELECT vtiger_inventorydetails.inventorydetailsid
					FROM vtiger_inventorydetails
					INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=vtiger_inventorydetails.inventorydetailsid
					WHERE vtiger_crmentity.deleted=0 AND vtiger_inventorydetails.lineitem_id=?',
				array($row['lineitem_id'])
			);
			if ($adb->num_rows($rec_exists)>0) {
				$invdet_focus->id = $adb->query_result($rec_exists, 0, 0);
				$invdet_focus->retrieve_entity_info($invdet_focus->id, 'InventoryDetails');
				$invdet_focus->mode = 'edit';
			} else {
				$invdet_focus->id = '';
				$invdet_focus->mode = '';
			}
			if (GlobalVariable::getVariable('Inventory_Check_Invoiced_Lines', 0, $save_currentModule) == 1) {
				switch ($module) {
					case 'SalesOrder':
						if ($invdet_focus->mode == 'edit') {
							$diff = $row['quantity']-$invdet_focus->column_fields['quantity'];
							$result_units = $invdet_focus->column_fields['remaining_units']+$diff;
							if (($invdet_focus->column_fields['remaining_units']>0 && $result_units<0) || ($invdet_focus->column_fields['remaining_units']<0 && $result_units>0)) {
								$result_units = 0;
							}
							$invdet_focus->column_fields['remaining_units'] = $result_units;
						} else {
							$invdet_focus->column_fields['remaining_units'] = $row['quantity'];
						}
						break;
					case 'Invoice':
						if (array_key_exists('rel_lineitem_id'.$requestindex, $_REQUEST)) {
							$rel_invdet = $_REQUEST['rel_lineitem_id'.$requestindex];
							$sel_rel_rec_exists = 'SELECT vtiger_inventorydetails.inventorydetailsid FROM vtiger_inventorydetails INNER JOIN '.$crmEntityTable
								.' ON vtiger_crmentity.crmid=vtiger_inventorydetails.inventorydetailsid WHERE deleted=0 AND vtiger_inventorydetails.lineitem_id=?';
							$rel_rec_exists = $adb->pquery($sel_rel_rec_exists, array($rel_invdet));
							if ($adb->num_rows($rel_rec_exists)>0) {
								$rel_id_focus = new InventoryDetails();
								$rel_id_focus->id = $adb->query_result($rel_rec_exists, 0, 0);
								$rel_id_focus->retrieve_entity_info($rel_id_focus->id, 'InventoryDetails');
								$rel_id_focus->mode = 'edit';
								if ($invdet_focus->mode == 'edit') {
									$diff = $row['quantity']-$invdet_focus->column_fields['quantity'];
									$result_units = $rel_id_focus->column_fields['remaining_units']-$diff;
								} else {
									$result_units = $rel_id_focus->column_fields['remaining_units'] - $row['quantity'];
								}
								if (($rel_id_focus->column_fields['remaining_units']>0 && $result_units<0) || ($rel_id_focus->column_fields['remaining_units']<0 && $result_units>0)) {
									$result_units = 0;
								}
								$rel_id_focus->column_fields['remaining_units'] = $result_units;
								$rel_id_focus->save('InventoryDetails');
							}
						}
						$invdet_focus->column_fields['remaining_units'] = $row['quantity'];
						break;
					default:
						$invdet_focus->column_fields['remaining_units'] = $row['quantity'];
						break;
				}
			} else {
				$invdet_focus->column_fields['remaining_units'] = $row['quantity'];
			}

			foreach ($invdet_focus->column_fields as $fieldname => $val) {
				if (isset($row[$fieldname])) {
					$invdet_focus->column_fields[$fieldname] = $row[$fieldname];
				}
			}
			foreach ($invdet_focus->column_fields as $fieldname => $val) {
				if (isset($_REQUEST[$fieldname.$requestindex])) {
					$invdet_focus->column_fields[$fieldname] = vtlib_purify($_REQUEST[$fieldname.$requestindex]);
				} elseif (isset($_REQUEST[$fieldname.$requestindex.'_hidden'])) {
					$invdet_focus->column_fields[$fieldname.'_hidden'] = $_REQUEST[$fieldname.'_hidden'] = vtlib_purify($_REQUEST[$fieldname.$requestindex.'_hidden']);
					if (isset($inputFiles[$fieldname.$requestindex])) {
						$_FILES[$fieldname] = $inputFiles[$fieldname.$requestindex];
						$_REQUEST[$fieldname.'_canvas_image'] = vtlib_purify($_REQUEST[$fieldname.$requestindex.'_canvas_image']);
						$_REQUEST[$fieldname.'_canvas_image_set'] = vtlib_purify($_REQUEST[$fieldname.$requestindex.'_canvas_image_set']);
					}
				}
			}
			$invdet_focus->column_fields['lineitem_id'] = $row['lineitem_id'];
			$invdet_focus->column_fields['discount_amount'] = $row['discount_amount'];
			$_REQUEST['assigntype'] = 'U';
			$invdet_focus->column_fields['assigned_user_id'] = $current_user->id;
			$invdet_focus->column_fields['account_id'] = $accountid;
			$invdet_focus->column_fields['contact_id'] = $contactid;

			if ($taxtype == 'group') {
				$invdet_focus->column_fields['tax_percent'] = 0;
				$invdet_focus->column_fields['linetax'] = 0;
			}
			$invdet_focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($invdet_focus->column_fields, $meta);
			$invdet_focus->save('InventoryDetails');
			$requestindex++;
			while (isset($_REQUEST['deleted'.$requestindex]) && $_REQUEST['deleted'.$requestindex] == 1) {
				$requestindex++;
			}
		}
		if (GlobalVariable::getVariable('Inventory_Check_Invoiced_Lines', 0, $currentModule) == 1) {
			$check_invoiced = false;
			if ($module=='SalesOrder') {
				$soid = $related_to;
				$check_invoiced = true;
			} elseif ($module=='Invoice') {
				$soid = $related_focus->column_fields['salesorder_id'];
				if (isRecordExists($soid)) {
					$check_invoiced = true;
				}
			}
			if ($check_invoiced) {
				$sel_invoiced = 'SELECT COUNT(*) as remaining FROM vtiger_inventorydetails INNER JOIN '.$crmEntityTable
					.' ON vtiger_crmentity.crmid=vtiger_inventorydetails.inventorydetailsid'
					.' WHERE vtiger_crmentity.deleted=0 AND vtiger_inventorydetails.related_to=? AND vtiger_inventorydetails.remaining_units>0';
				$rel_invoiced = $adb->pquery($sel_invoiced, array($soid));
				$remaining = $adb->query_result($rel_invoiced, 0, 'remaining');
				if ($remaining > 0) {
					$invoiced = 0;
				} else {
					$invoiced = 1;
				}
				$adb->pquery('UPDATE vtiger_salesorder SET invoiced=? WHERE salesorderid=?', array($invoiced,$soid));
			}
		}

		$currentModule = $save_currentModule;
	}
}
?>
