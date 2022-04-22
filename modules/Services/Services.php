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

class Services extends CRMEntity {
	public $table_name = 'vtiger_service';
	public $table_index= 'serviceid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-proposition', 'class' => 'slds-icon', 'icon'=>'proposition');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_servicecf', 'serviceid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_servicecf'=>array('serviceid','vtiger_service', 'serviceid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity','vtiger_service','vtiger_servicecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_service'=>'serviceid',
		'vtiger_servicecf'=>'serviceid',
		'vtiger_producttaxrel'=>'productid'
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Service No'=>array('service'=>'service_no'),
		'Service Name'=>array('service'=>'servicename'),
		'Commission Rate'=>array('service'=>'commissionrate'),
		'No of Units'=>array('service'=>'qty_per_unit'),
		'Price'=>array('service'=>'unit_price')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Service No'=>'service_no',
		'Service Name'=>'servicename',
		'Commission Rate'=>'commissionrate',
		'No of Units'=>'qty_per_unit',
		'Price'=>'unit_price'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field= 'servicename';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Service No'=>array('service'=>'service_no'),
		'Service Name'=>array('service'=>'servicename'),
		'Price'=>array('service'=>'unit_price')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Service No'=>'service_no',
		'Service Name'=>'servicename',
		'Price'=>'unit_price'
	);

	// For Popup window record selection
	public $popup_fields = array ('servicename','service_usageunit','unit_price');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'servicename';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'servicename';

	// Required Information for enabling Import feature
	public $required_fields = array('servicename'=>1);

	public $default_order_by = 'servicename';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('servicename');

	public $unit_price;

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		//Inserting into service_taxrel table
		if (isFrontendEditViewAction($_REQUEST, $module)) {
			$this->insertPriceInformation('vtiger_productcurrencyrel', 'Services');
		}
		if (isFrontendEditViewAction($_REQUEST, $module) || $_REQUEST['action'] == 'MassEditSave') {
			$this->insertTaxInformation('vtiger_producttaxrel', 'Services');
		}
		// Update unit price value in vtiger_productcurrencyrel
		$this->updateUnitPrice();
	}

	/**	function to save the service tax information in vtiger_servicetaxrel table
	 *	@param string table name to save the service tax relationship (servicetaxrel)
	 *	@param string current module name
	 *	@return void
	*/
	public function insertTaxInformation($tablename, $module) {
		global $adb, $log;
		$log->debug("> insertTaxInformation $tablename, $module");
		$tax_details = getAllTaxes();
		if ($_REQUEST['action'] == 'MassEditSave') {
			$params = json_decode($_REQUEST['params'], true);
			$_REQUEST = array_merge($params, $_REQUEST);
		}
		$numtaxes = count($tax_details);
		$tax_per = '';
		//Save the Product - tax relationship if corresponding tax check box is enabled
		//Delete the existing tax if any
		if ($this->mode == 'edit') {
			$sql = 'delete from vtiger_producttaxrel where productid=? and taxid=?';
			for ($i=0; $i<$numtaxes; $i++) {
				$tax_checkname = $tax_details[$i]['taxname'].'_check';
				if ($_REQUEST['action'] == 'MassEditSave') { // then we only modify the marked taxes
					if (isset($_REQUEST[$tax_checkname]) && ($_REQUEST[$tax_checkname] == 'on' || $_REQUEST[$tax_checkname] == 1)) {
						$taxid = getTaxId($tax_details[$i]['taxname']);
						$adb->pquery($sql, array($this->id,$taxid));
					}
				} else {
					$taxid = getTaxId($tax_details[$i]['taxname']);
					$adb->pquery($sql, array($this->id,$taxid));
				}
			}
		}
		for ($i=0; $i<$numtaxes; $i++) {
			$tax_name = $tax_details[$i]['taxname'];
			$tax_checkname = $tax_details[$i]['taxname'].'_check';
			if (!empty($_REQUEST[$tax_checkname]) && ($_REQUEST[$tax_checkname] == 'on' || $_REQUEST[$tax_checkname] == 1)) {
				$taxid = getTaxId($tax_name);
				$tax_per = $_REQUEST[$tax_name];
				if ($tax_per == '') {
					$log->debug('Tax selected but value not given so default value will be saved.');
					$tax_per = getTaxPercentage($tax_name);
				}
				$log->debug("Going to save the Service - $tax_name tax relationship");
				$adb->pquery('insert into vtiger_producttaxrel values(?,?,?)', array($this->id, $taxid, $tax_per));
			}
		}
		$log->debug('< insertTaxInformation');
	}

	/**	function to save the service price information in vtiger_servicecurrencyrel table
	 *	@param string table name to save the service currency relationship (servicecurrencyrel)
	 *	@param string current module name
	 *	@return void
	*/
	public function insertPriceInformation($tablename, $module) {
		global $adb, $log;
		$log->debug("> insertPriceInformation $tablename, $module");
		//removed the update of currency_id based on the logged in user's preference : fix 6490

		$currency_details = getAllCurrencies('all');

		//Delete the existing currency relationship if any
		if ($this->mode == 'edit' && $_REQUEST['action'] !== 'MassEditSave') {
			$sql = 'delete from vtiger_productcurrencyrel where productid=? and currencyid=?';
			for ($i=0; $i<count($currency_details); $i++) {
				$curid = $currency_details[$i]['curid'];
				$adb->pquery($sql, array($this->id, $curid));
			}
		}

		$service_base_conv_rate = getBaseConversionRateForProduct($this->id, $this->mode, $module);

		//Save the Product - Currency relationship if corresponding currency check box is enabled
		for ($i=0; $i<count($currency_details); $i++) {
			$curid = $currency_details[$i]['curid'];
			$curname = $currency_details[$i]['currencylabel'];
			$cur_checkname = 'cur_' . $curid . '_check';
			$cur_valuename = 'curname' . $curid;
			if (!empty($_REQUEST[$cur_checkname]) && ($_REQUEST[$cur_checkname] == 'on' || $_REQUEST[$cur_checkname] == 1)) {
				$srvprice = (isset($_REQUEST['unit_price']) ? $_REQUEST['unit_price'] : 0);
				$requestPrice = CurrencyField::convertToDBFormat($srvprice, null, true);
				$actualPrice = CurrencyField::convertToDBFormat((isset($_REQUEST[$cur_valuename]) ? $_REQUEST[$cur_valuename] : 0), null, true);
				$conversion_rate = $currency_details[$i]['conversionrate'];
				$actual_conversion_rate = $service_base_conv_rate * $conversion_rate;
				$converted_price = $actual_conversion_rate * $requestPrice;

				$log->debug("Going to save the Service - $curname currency relationship");
				$adb->pquery('insert into vtiger_productcurrencyrel values(?,?,?,?)', array($this->id, $curid, $converted_price, $actualPrice));

				// Update the Product information with Base Currency choosen by the User.
				if ($_REQUEST['base_currency'] == $cur_valuename) {
					$adb->pquery('update vtiger_service set currency_id=?, unit_price=? where serviceid=?', array($curid, $actualPrice, $this->id));
				}
			}
		}
		$log->debug('< insertPriceInformation');
	}

	public function updateUnitPrice() {
		global $adb;
		$prod_res = $adb->pquery('select unit_price, currency_id from vtiger_service where serviceid=?', array($this->id));
		$prod_unit_price = $adb->query_result($prod_res, 0, 'unit_price');
		$prod_base_currency = $adb->query_result($prod_res, 0, 'currency_id');
		$query = 'update vtiger_productcurrencyrel set actual_price=? where productid=? and currencyid=?';
		$params = array($prod_unit_price, $this->id, $prod_base_currency);
		$adb->pquery($query, $params);
	}

	/**	function used to get the list of quotes which are related to the service
	 *	@param int service id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_quotes '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' "
					."onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable"
					."&form=EditView&form_submit=false&recordid=$id', 'test', cbPopupWindowSettings);\""
					." value='". getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). ' '. $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*, vtiger_quotes.*, vtiger_potential.potentialname, vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_quotes
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
			INNER JOIN (SELECT DISTINCT(vtiger_inventoryproductrel.id) as id FROM vtiger_inventoryproductrel WHERE vtiger_inventoryproductrel.productid = $id) as invrel
				ON invrel.id = vtiger_quotes.quoteid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_quotes.accountid
			LEFT OUTER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_quotes.potentialid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_quotes');
		return $return_value;
	}

	/**	function used to get the list of purchase orders which are related to the service
	 *	@param int service id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_purchase_orders '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button'"
					." onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable"
					."&form=EditView&form_submit=false&recordid=$id', 'test', cbPopupWindowSettings);\""
					." value='". getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). ' '. $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*, vtiger_purchaseorder.*, vtiger_service.servicename, vtiger_inventoryproductrel.productid,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_purchaseorder
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_purchaseorder.purchaseorderid
			INNER JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_purchaseorder.purchaseorderid
			INNER JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0 AND vtiger_service.serviceid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_purchase_orders');
		return $return_value;
	}

	/**	function used to get the list of sales orders which are related to the service
	 *	@param int service id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_salesorder '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id', 'test', cbPopupWindowSettings);\""
					." value='". getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). ' '. $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*, vtiger_salesorder.*, vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_salesorder
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
			INNER JOIN (SELECT DISTINCT(vtiger_inventoryproductrel.id) as id FROM vtiger_inventoryproductrel WHERE vtiger_inventoryproductrel.productid = $id) as invrel
				ON invrel.id = vtiger_salesorder.salesorderid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesorder.accountid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_salesorder');
		return $return_value;
	}

	/**	function used to get the list of invoices which are related to the service
	 *	@param int service id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_invoices '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id', 'test', cbPopupWindowSettings);\""
					." value='". getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). ' '. $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*, vtiger_invoice.*, vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_invoice
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_invoice.accountid
			INNER JOIN (SELECT DISTINCT(vtiger_inventoryproductrel.id) as id FROM vtiger_inventoryproductrel WHERE vtiger_inventoryproductrel.productid = $id) as invrel
				ON invrel.id = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_invoices');
		return $return_value;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param string This module name
	 * @param array List of Entity Id's from which related records need to be transfered
	 * @param integer Id of the the Record to which the related records are to be moved
	 */
	public function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug('> transferRelatedRecords', ['module' => $module, 'transferEntityIds' => $transferEntityIds, 'entityId' => $entityId]);
		$rel_table_arr = array(
			'Quotes' => 'vtiger_inventoryproductrel',
			'PurchaseOrder' => 'vtiger_inventoryproductrel',
			'SalesOrder' => 'vtiger_inventoryproductrel',
			'Invoice' => 'vtiger_inventoryproductrel',
		);
		$tbl_field_arr = array(
			'vtiger_inventoryproductrel'=>'id',
		);
		$entity_tbl_field_arr = array(
			'vtiger_inventoryproductrel'=>'productid',
		);
		foreach ($transferEntityIds as $transferId) {
			foreach ($rel_table_arr as $rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result = $adb->pquery(
					"select $id_field from $rel_table where $entity_id_field=? and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
					array($transferId,$entityId)
				);
				$res_cnt = $adb->num_rows($sel_result);
				if ($res_cnt > 0) {
					for ($i=0; $i<$res_cnt; $i++) {
						$id_field_value = $adb->query_result($sel_result, $i, $id_field);
						$adb->pquery(
							"update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value)
						);
					}
				}
			}
		}
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$log->debug('< transferRelatedRecords');
	}

	/**
	 * Function to get the primary query part of a report
	 * @param string primary module name
	 * @return string query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsQuery($module, $queryPlanner) {
		global $current_user;
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency(
			'vtiger_seproductsrel',
			array('vtiger_crmentityRelServices', 'vtiger_accountRelServices', 'vtiger_leaddetailsRelServices', 'vtiger_servicecf', 'vtiger_potentialRelServices')
		);
		$query = parent::generateReportsQuery($module, $queryPlanner);

		if ($queryPlanner->requireTable('vtiger_seproductsrel')) {
			$query .= ' left join vtiger_seproductsrel on vtiger_seproductsrel.productid= vtiger_service.serviceid';
		}
		if ($queryPlanner->requireTable('vtiger_crmentityRelServices')) {
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Services');
			$query .= " left join $crmEntityTable as vtiger_crmentityRelServices on vtiger_crmentityRelServices.crmid=vtiger_seproductsrel.crmid and vtiger_crmentityRelServices.deleted = 0";
		}
		if ($queryPlanner->requireTable('vtiger_accountRelServices')) {
			$query .= ' left join vtiger_account as vtiger_accountRelServices on vtiger_accountRelServices.accountid=vtiger_seproductsrel.crmid';
		}
		if ($queryPlanner->requireTable('vtiger_leaddetailsRelServices')) {
			$query .= ' left join vtiger_leaddetails as vtiger_leaddetailsRelServices on vtiger_leaddetailsRelServices.leadid = vtiger_seproductsrel.crmid';
		}
		if ($queryPlanner->requireTable('vtiger_potentialRelServices')) {
			$query .= ' left join vtiger_potential as vtiger_potentialRelServices on vtiger_potentialRelServices.potentialid = vtiger_seproductsrel.crmid';
		}
		if ($queryPlanner->requireTable('innerService')) {
			$query .= ' LEFT JOIN (
				SELECT vtiger_service.serviceid,
						(CASE WHEN (vtiger_service.currency_id = 1 ) THEN vtiger_service.unit_price
							ELSE (vtiger_service.unit_price / vtiger_currency_info.conversion_rate) END
						) AS actual_unit_price
				FROM vtiger_service
				LEFT JOIN vtiger_currency_info ON vtiger_service.currency_id = vtiger_currency_info.id
				LEFT JOIN vtiger_productcurrencyrel ON vtiger_service.serviceid = vtiger_productcurrencyrel.productid
				AND vtiger_productcurrencyrel.currencyid = '. $current_user->currency_id . '
			) AS innerService ON innerService.serviceid = vtiger_service.serviceid';
		}
		return $query;
	}

	/**
	 * Function to get the secondary query part of a report
	 * @param string primary module name
	 * @param string secondary module name
	 * @return string the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		global $current_user;
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentityServices', array('vtiger_usersServices','vtiger_groupsServices','vtiger_lastModifiedByServices'));
		if (!$queryPlanner->requireTable('vtiger_service', $matrix) && !$queryPlanner->requireTable('vtiger_servicecf', $matrix)) {
			return '';
		}
		$matrix->setDependency(
			'vtiger_service',
			array('actual_unit_price', 'vtiger_currency_info', 'vtiger_productcurrencyrel', 'vtiger_servicecf', 'vtiger_crmentityServices')
		);

		$query = parent::generateReportsSecQuery($module, $secmodule, $queryPlanner, $type, $where_condition);
		if ($queryPlanner->requireTable('innerService')) {
			$query .= ' LEFT JOIN (
				SELECT vtiger_service.serviceid,
						(CASE WHEN (vtiger_service.currency_id = ' . $current_user->currency_id . ' ) THEN vtiger_service.unit_price
							WHEN (vtiger_productcurrencyrel.actual_price IS NOT NULL) THEN vtiger_productcurrencyrel.actual_price
							ELSE (vtiger_service.unit_price / vtiger_currency_info.conversion_rate) * '. $current_user->conv_rate . ' END
						) AS actual_unit_price
				FROM vtiger_service
				LEFT JOIN vtiger_currency_info ON vtiger_service.currency_id = vtiger_currency_info.id
				LEFT JOIN vtiger_productcurrencyrel ON vtiger_service.serviceid = vtiger_productcurrencyrel.productid
				AND vtiger_productcurrencyrel.currencyid = '. $current_user->currency_id . '
			) AS innerService ON innerService.serviceid = vtiger_service.serviceid';
		}
		return $query;
	}

	/**
	 * Function to get the relation tables for related modules
	 * @param string secondary module name
	 * @return string the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			'Quotes' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_service'=>'serviceid'),
			'PurchaseOrder' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_service'=>'serviceid'),
			'SalesOrder' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_service'=>'serviceid'),
			'Invoice' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_service'=>'serviceid'),
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_service'=>'serviceid'),
			'Contacts' => array('vtiger_crmentityrel'=>array('crmid','relcrmid'),'vtiger_service'=>'serviceid'),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
		global $adb;
		$adb->pquery('DELETE from vtiger_seproductsrel WHERE productid=? or crmid=?', array($id, $id));
		parent::unlinkDependencies($module, $id);
	}

	 /**
	* Invoked when special actions are performed on the module.
	* @param string Module name
	* @param string Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		require_once 'include/utils/utils.php';
		global $adb;

		if ($eventType == 'module.postinstall') {
			require_once 'vtlib/Vtiger/Module.php';
			$this->setModuleSeqNumber('configure', $moduleName, 'srv-', '0000001');
			$moduleInstance = Vtiger_Module::getInstance($moduleName);
			$moduleInstance->allowSharing();

			$ttModuleInstance = Vtiger_Module::getInstance('HelpDesk');
			$ttModuleInstance->setRelatedList($moduleInstance, 'Services', array('select'));

			$leadModuleInstance = Vtiger_Module::getInstance('Leads');
			$leadModuleInstance->setRelatedList($moduleInstance, 'Services', array('select'));

			$accModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accModuleInstance->setRelatedList($moduleInstance, 'Services', array('select'));

			$conModuleInstance = Vtiger_Module::getInstance('Contacts');
			$conModuleInstance->setRelatedList($moduleInstance, 'Services', array('select'));

			$potModuleInstance = Vtiger_Module::getInstance('Potentials');
			$potModuleInstance->setRelatedList($moduleInstance, 'Services', array('select'));

			$pbModuleInstance = Vtiger_Module::getInstance('PriceBooks');
			$pbModuleInstance->setRelatedList($moduleInstance, 'Services', array('select'), 'get_pricebook_services');

			// Initialize module sequence for the module
			$adb->pquery('INSERT into vtiger_modentity_num values(?,?,?,?,?,?)', array($adb->getUniqueId('vtiger_modentity_num'), $moduleName, 'SER', 1, 1, 1));

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
		} elseif ($eventType == 'module.disabled') {
		// Handle actions when this module is disabled.
		} elseif ($eventType == 'module.enabled') {
		// Handle actions when this module is enabled.
		} elseif ($eventType == 'module.preuninstall') {
		// Handle actions when this module is about to be deleted.
		} elseif ($eventType == 'module.preupdate') {
		// Handle actions before this module is updated.
		} elseif ($eventType == 'module.postupdate') {
		// Handle actions after this module is updated.
			//adds sharing accsess
			$ServicesModule = Vtiger_Module::getInstance('Services');
			Vtiger_Access::setDefaultSharing($ServicesModule);
		}
	}
}
?>
