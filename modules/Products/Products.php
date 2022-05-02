<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'include/RelatedListView.php';
require 'modules/Vtiger/default_module_view.php';

class Products extends CRMEntity {
	public $table_name = 'vtiger_products';
	public $table_index= 'productid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = true;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-product', 'class' => 'slds-icon', 'icon'=>'product');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_productcf','productid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_products', 'vtiger_productcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_products' => 'productid',
		'vtiger_productcf' => 'productid',
		'vtiger_seproductsrel' => 'productid',
		'vtiger_producttaxrel' => 'productid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		'Product Name'=>array('products'=>'productname'),
		'Part Number'=>array('products'=>'productcode'),
		'Commission Rate'=>array('products'=>'commissionrate'),
		'Product Category'=>array('products'=>'productcategory'),
		'Vendor Name'=>array('products'=>'vendor_id'),
		'Qty/Unit'=>array('products'=>'qty_per_unit'),
		'Unit Price'=>array('products'=>'unit_price')
	);
	public $list_fields_name = array(
		'Product Name'=>'productname',
		'Part Number'=>'productcode',
		'Commission Rate'=>'commissionrate',
		'Product Category'=>'productcategory',
		'Vendor Name'=>'vendor_id',
		'Qty/Unit'=>'qty_per_unit',
		'Unit Price'=>'unit_price'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field= 'productname';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Product Name'=>array('products'=>'productname'),
		'Part Number'=>array('products'=>'productcode'),
		'Product Category'=>array('products'=>'productcategory'),
		'Vendor Name'=>array('products'=>'vendor_id'),
		'Unit Price'=>array('products'=>'unit_price')
	);
	public $search_fields_name = array(
		'Product Name'=>'productname',
		'Part Number'=>'productcode',
		'Product Category'=>'productcategory',
		'Vendor Name'=>'vendor_id',
		'Unit Price'=>'unit_price'
	);

	// For Popup window record selection
	public $popup_fields = array('productname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'productname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'productname';

	// Required Information for enabling Import feature
	public $required_fields = array('productname'=>1);

	public $default_order_by = 'productname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'productname');

	public $unit_price; // for importing/exporting

	public function save_module($module) {
		if (isFrontendEditViewAction($_REQUEST, $module)) {
			$this->insertPriceInformation('vtiger_productcurrencyrel', 'Products');
		}
		if (isFrontendEditViewAction($_REQUEST, $module) || $_REQUEST['action'] == 'MassEditSave') {
			$this->insertTaxInformation('vtiger_producttaxrel', 'Products');
		}

		// Update unit price value in vtiger_productcurrencyrel
		$this->updateUnitPrice();
		//Inserting into attachments
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		$copyBundle = GlobalVariable::getVariable('Product_Copy_Bundle_OnDuplicate', 'false');
		if ($copyBundle!='false' && $_REQUEST['cbcustominfo1']=='duplicatingproduct' && !empty($_REQUEST['cbcustominfo2']) && vtlib_isModuleActive('ProductComponent')) {
			include_once 'include/Webservices/Create.php';
			include_once 'include/Webservices/Retrieve.php';
			global $adb, $current_user;
			$crmtablealias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
			$pcrs = $adb->pquery(
				'select productcomponentid
					from vtiger_productcomponent
					inner join '.$crmtablealias.' on crmid=productcomponentid
					where deleted=0 and frompdo=?',
				array($_REQUEST['cbcustominfo2'])
			);
			$pcmwsid = vtws_getEntityId('ProductComponent').'x';
			$pdowsid = vtws_getEntityId('Products').'x';
			while ($pc = $adb->fetch_array($pcrs)) {
				$pcdup = vtws_retrieve($pcmwsid.$pc['productcomponentid'], $current_user);
				$pcdup['frompdo'] = $pdowsid.$this->id;
				unset($pcdup['id'], $pcdup['frompdoename'], $pcdup['topdoename'], $pcdup['assigned_user_idename']);
				vtws_create('ProductComponent', $pcdup, $current_user);
			}
		}
	}

	/**	function to save the product tax information in vtiger_producttaxrel table
	 *	@param string tablename to save the product tax relationship (producttaxrel)
	 *	@param string current module name
	 *	@return void
	*/
	public function insertTaxInformation($tablename, $module) {
		global $adb, $log;
		$log->debug("> insertTaxInformation $tablename, $module");
		$tax_details = getAllTaxes();
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'MassEditSave') {
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
				$log->debug("Going to save the Product - $tax_name tax relationship");
				$adb->pquery('insert into vtiger_producttaxrel values(?,?,?)', array($this->id, $taxid, $tax_per));
			}
		}
		$log->debug('< insertTaxInformation');
	}

	/**	function to save the product price information in vtiger_productcurrencyrel table
	 *	@param string tablename to save the product currency relationship (productcurrencyrel)
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

		$product_base_conv_rate = getBaseConversionRateForProduct($this->id, $this->mode);

		//Save the Product - Currency relationship if corresponding currency check box is enabled
		for ($i=0; $i<count($currency_details); $i++) {
			$curid = $currency_details[$i]['curid'];
			$curname = $currency_details[$i]['currencylabel'];
			$cur_checkname = 'cur_' . $curid . '_check';
			$cur_valuename = 'curname' . $curid;
			if (!empty($_REQUEST[$cur_checkname]) && ($_REQUEST[$cur_checkname] == 'on' || $_REQUEST[$cur_checkname] == 1)) {
				$requestPrice = CurrencyField::convertToDBFormat($_REQUEST['unit_price'], null, true);
				$actualPrice = CurrencyField::convertToDBFormat($_REQUEST[$cur_valuename], null, true);
				$conversion_rate = $currency_details[$i]['conversionrate'];
				$actual_conversion_rate = $product_base_conv_rate * $conversion_rate;
				$converted_price = $actual_conversion_rate * $requestPrice;

				$log->debug("Going to save the Product - $curname currency relationship");
				$adb->pquery('insert into vtiger_productcurrencyrel values(?,?,?,?)', array($this->id, $curid, $converted_price, $actualPrice));

				// Update the Product information with Base Currency choosen by the User.
				if ($_REQUEST['base_currency'] == $cur_valuename) {
					$adb->pquery('update vtiger_products set currency_id=?, unit_price=? where productid=?', array($curid, $actualPrice, $this->id));
				}
			}
		}
		$log->debug('< insertPriceInformation');
	}

	public function updateUnitPrice() {
		global $adb;
		$prod_res = $adb->pquery('select unit_price, currency_id from vtiger_products where productid=?', array($this->id));
		$prod_unit_price = $adb->query_result($prod_res, 0, 'unit_price');
		$prod_base_currency = $adb->query_result($prod_res, 0, 'currency_id');
		$query = 'update vtiger_productcurrencyrel set actual_price=? where productid=? and currencyid=?';
		$params = array($prod_unit_price, $this->id, $prod_base_currency);
		$adb->pquery($query, $params);
	}

	public function insertIntoAttachment($id, $module, $direct_import = false) {
		global $log, $adb;
		$log->debug("> insertIntoAttachment $id,$module");
		if (empty($_FILES) || !is_array($_FILES)) {
			$log->debug('< insertIntoAttachment: no FILES');
			return;
		}
		$file_saved = false;
		foreach ($_FILES as $fileindex => $files) {
			if (substr($fileindex, 0, 5)!='file_' && !($fileindex=='file' && $_REQUEST['action']=='ProductsAjax' && $_REQUEST['file']=='UploadImage')) {
				continue;
			}
			if ($files['name'] != '' && $files['size'] > 0) {
				if (!empty($_REQUEST[$fileindex.'_hidden'])) {
					$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				} else {
					$files['original_name'] = stripslashes($files['name']);
				}
				$files['original_name'] = str_replace('"', '', $files['original_name']);
				$file_saved = $this->uploadAndSaveFile($id, $module, $files, '', $direct_import, 'imagename');
				if (!$file_saved) {
					return false;
				}
			}
			unset($_FILES[$fileindex]);
		}

		// Remove the deleted attachments from db
		if (!empty($_REQUEST['del_file_list'])) {
			$del_file_list = explode('###', trim($_REQUEST['del_file_list'], '###'));
			foreach ($del_file_list as $del_file_name) {
				$attach_res = $adb->pquery(
					'select vtiger_attachments.attachmentsid
					from vtiger_attachments
					inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
					where crmid=? and name=?',
					array($id,$del_file_name)
				);
				$attachments_id = $adb->query_result($attach_res, 0, 'attachmentsid');
				$adb->pquery('delete from vtiger_attachments where attachmentsid=?', array($attachments_id));
				$adb->pquery('delete from vtiger_seattachmentsrel where attachmentsid=?', array($attachments_id));
			}
		}

		if (!empty($_FILES)) {
			parent::insertIntoAttachment($id, $module, $direct_import);
		}
		$log->debug('< insertIntoAttachment');
		return true;
	}

	/**	function used to get the list of leads which are related to the product
	 *	@param integer product id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_leads($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_leads '.$id);
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = 'SELECT vtiger_leaddetails.leadid, vtiger_crmentity.crmid, vtiger_leaddetails.firstname, vtiger_leaddetails.lastname, vtiger_leaddetails.company,
				vtiger_leadaddress.phone, vtiger_leadsubdetails.website, vtiger_leaddetails.email,
				case when (vtiger_users.user_name not like "") then vtiger_users.user_name else vtiger_groups.groupname end as user_name, vtiger_crmentity.smownerid,
				vtiger_products.productname, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_products.expiry_date
			FROM vtiger_leaddetails
			INNER JOIN '.$crmtablealias.' ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
			INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid
			INNER JOIN vtiger_leadsubdetails ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid
			INNER JOIN vtiger_leadscf ON vtiger_leaddetails.leadid = vtiger_leadscf.leadid
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid=vtiger_leaddetails.leadid
			INNER JOIN vtiger_products ON vtiger_seproductsrel.productid = vtiger_products.productid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid = '.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_leads');
		return $return_value;
	}

	/**	function used to get the list of accounts which are related to the product
	 *	@param integer product id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_accounts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_accounts '.$id);
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
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = 'SELECT vtiger_account.accountid, vtiger_crmentity.crmid, vtiger_account.accountname, vtiger_accountbillads.bill_city, vtiger_account.website,
				vtiger_account.phone, case when (vtiger_users.user_name not like "") then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.smownerid, vtiger_products.productname, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_products.expiry_date
			FROM vtiger_account
			INNER JOIN '.$crmtablealias.' ON vtiger_crmentity.crmid = vtiger_account.accountid
			INNER JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
			INNER JOIN vtiger_accountscf ON vtiger_account.accountid = vtiger_accountscf.accountid
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid=vtiger_account.accountid
			INNER JOIN vtiger_products ON vtiger_seproductsrel.productid = vtiger_products.productid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid = '.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_accounts');
		return $return_value;
	}

	/**	function used to get the list of contacts which are related to the product
	 *	@param integer product id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_contacts '.$id);
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
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = 'SELECT vtiger_contactdetails.firstname, vtiger_contactdetails.lastname, vtiger_contactdetails.title, vtiger_contactdetails.accountid,
				vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_crmentity.crmid,
				case when (vtiger_users.user_name not like "") then vtiger_users.user_name else vtiger_groups.groupname end as user_name, vtiger_crmentity.smownerid,
				vtiger_products.productname, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_products.expiry_date,vtiger_account.accountname
			FROM vtiger_contactdetails
			INNER JOIN '.$crmtablealias.' ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
			INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
			INNER JOIN vtiger_customerdetails ON vtiger_contactdetails.contactid = vtiger_customerdetails.customerid
			INNER JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid=vtiger_contactdetails.contactid
			INNER JOIN vtiger_products ON vtiger_seproductsrel.productid = vtiger_products.productid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid='.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_contacts');
		return $return_value;
	}

	/**	function used to get the list of potentials which are related to the product
	 *	@param integer product id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_opportunities '.$id);
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*, vtiger_potential.*, vtiger_potentialscf.*, vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
			vtiger_products.productname, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_products.expiry_date
			FROM vtiger_potential
			INNER JOIN ".$crmtablealias.' ON vtiger_crmentity.crmid = vtiger_potential.potentialid
			INNER JOIN vtiger_potentialscf ON vtiger_potential.potentialid = vtiger_potentialscf.potentialid
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_potential.potentialid
			INNER JOIN vtiger_products ON vtiger_seproductsrel.productid = vtiger_products.productid
			LEFT JOIN vtiger_account ON vtiger_potential.related_to = vtiger_account.accountid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0 AND vtiger_products.productid='.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_opportunities');
		return $return_value;
	}

	/**	function used to get the list of tickets which are related to the product
	 *	@param integer product id
	 *	@return array which will be returned from the function GetRelatedList
	 */
	public function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('> get_tickets '.$id);
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'product_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, vtiger_users.id,
			vtiger_products.productid, vtiger_products.productname,
			vtiger_troubletickets.ticketid,
			vtiger_troubletickets.parent_id, vtiger_troubletickets.title,
			vtiger_troubletickets.status, vtiger_troubletickets.priority,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_crmentity.modifiedtime, vtiger_troubletickets.ticket_no
			FROM vtiger_troubletickets
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
			LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_troubletickets.product_id
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_tickets');
		return $return_value;
	}

	/**	function used to get the list of quotes which are related to the product
	 *	@param integer product id
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*,
			vtiger_quotes.*,
			vtiger_potential.potentialname,
			vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_quotes
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
			INNER JOIN (SELECT DISTINCT(vtiger_inventoryproductrel.id) as id FROM vtiger_inventoryproductrel WHERE vtiger_inventoryproductrel.productid = $id) as invrel
				ON invrel.id = vtiger_quotes.quoteid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_quotes.accountid
			LEFT OUTER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_quotes.potentialid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_quotes');
		return $return_value;
	}

	/**	function used to get the list of purchase orders which are related to the product
	 *	@param integer product id
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_crmentity.*, vtiger_purchaseorder.*, vtiger_products.productname, vtiger_inventoryproductrel.productid,
			case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_purchaseorder
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_purchaseorder.purchaseorderid
			INNER JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_purchaseorder.purchaseorderid
			INNER JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0 AND vtiger_products.productid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_purchase_orders');
		return $return_value;
	}

	/**	function used to get the list of sales orders which are related to the product
	 *	@param integer product id
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
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
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
			WHERE vtiger_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_salesorder');
		return $return_value;
	}

	/**	function used to get the list of invoices which are related to the product
	 *	@param integer product id
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
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
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_invoices');
		return $return_value;
	}

	/**	function used to get the number of vendors which are related to the product
	 *	@param integer product id
	 *	@return integer number of rows - return the number of products which do not have relationship with vendor
	 */
	public function product_novendor() {
		global $log, $adb;
		$log->debug('> product_novendor');
		$query = 'SELECT vtiger_products.productname, vtiger_crmentity.deleted
			FROM vtiger_products
			INNER JOIN '.$this->crmentityTableAlias.' ON vtiger_crmentity.crmid = vtiger_products.productid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.vendor_id is NULL';
		$result=$adb->pquery($query, array());
		$log->debug('< product_novendor');
		return $adb->num_rows($result);
	}

	/**
	* Function to get Product's related Products
	* @param integer productid
	* @return array related Products records
	*/
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_products '.$id);
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

		if ($actions && $this->ismember_check() === 0) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					. "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=Products&return_module=Products"
					. "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					. "cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input type='hidden' name='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"ProductComponent\";' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
				$button .= '<input type="hidden" name="frompdo" id="frompdo" value="' . $id . '">';
				$button .= '<input type="hidden" name="frompdo_type" id="frompdo_type" value="' . $currentModule . '">';
			}
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias($related_module);
		$query = "SELECT vtiger_productcomponent.*,vtiger_productcomponentcf.*,vtiger_products.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid
			FROM vtiger_productcomponent
			INNER JOIN vtiger_productcomponentcf ON vtiger_productcomponentcf.productcomponentid = vtiger_productcomponent.productcomponentid
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_productcomponent.productcomponentid
			INNER JOIN vtiger_products on vtiger_products.productid=vtiger_productcomponent.topdo
			INNER JOIN ".$this->crmentityTable." cpdo ON cpdo.crmid = vtiger_products.productid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND cpdo.deleted = 0 AND vtiger_productcomponent.frompdo = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_products');
		return $return_value;
	}

	/**
	* Function to get Product's related Products
	* @param  integer productid
	* @return array related Products records
	*/
	public function get_parent_products($id) {
		global $log, $singlepane_view, $app_strings;
		$log->debug('> get_parent_products '.$id);

		$focus = CRMEntity::getInstance('ProductComponent');

		$button = '';

		if (isPermitted('ProductComponent', 1, '') == 'yes') {
			$button .= '<input title="'.$app_strings['LBL_NEW_PRODUCT'].'" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';'
				.'this.form.module.value=\'ProductComponent\';this.form.return_module.value=\'ProductComponent\';this.form.return_action.value=\'DetailView\'" type="submit"'
				.' name="button" value="'.$app_strings['LBL_NEW_PRODUCT'].'">&nbsp;';
		}
		if ($singlepane_view == 'true') {
			$returnset = '&return_module=Products&return_action=DetailView&is_parent=1&return_id='.$id;
		} else {
			$returnset = '&return_module=Products&return_action=CallRelatedList&is_parent=1&return_id='.$id;
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
		$query = "SELECT vtiger_productcomponent.*,vtiger_productcomponentcf.*,vtiger_products.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid
			FROM vtiger_productcomponent
			INNER JOIN $crmtablealias ON vtiger_crmentity.crmid = vtiger_productcomponent.productcomponentid
			INNER JOIN vtiger_productcomponentcf ON vtiger_productcomponentcf.productcomponentid = vtiger_productcomponent.productcomponentid
			INNER JOIN vtiger_products on vtiger_products.productid=vtiger_productcomponent.frompdo
			INNER JOIN ".$this->crmentityTable." cpdo ON cpdo.crmid = vtiger_products.productid
			WHERE vtiger_crmentity.deleted = 0 AND cpdo.deleted = 0 AND vtiger_productcomponent.topdo = $id";

		$log->debug('< get_parent_products');
		return GetRelatedList('Products', 'ProductComponent', $focus, $query, $button, $returnset);
	}

	/** Function to check if the product is parent of any other product */
	public function isparent_check() {
		global $adb;
		if (!vtlib_isModuleActive('ProductComponent')) {
			return false;
		}
		$crmtablealias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
		$isparent_query = $adb->pquery(
			'SELECT EXISTS (SELECT 1
				FROM vtiger_productcomponent
				INNER JOIN '.$crmtablealias.' ON vtiger_crmentity.crmid = vtiger_productcomponent.productcomponentid
				WHERE vtiger_crmentity.deleted=0 AND vtiger_productcomponent.frompdo=?)',
			array($this->id)
		);
		return (int)$adb->query_result($isparent_query, 0, 0);
	}

	/** Function to check if the product is member of other product */
	public function ismember_check() {
		global $adb;
		if (!vtlib_isModuleActive('ProductComponent')) {
			return false;
		}
		$SubProductBeParent = GlobalVariable::getVariable('Product_Permit_Subproduct_Be_Parent', 'no');
		$ismember = 0;
		if ($SubProductBeParent == 'no') {
			$crmtablealias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
			$ismember_query = $adb->pquery(
				'SELECT EXISTS (SELECT 1
					FROM vtiger_productcomponent
					INNER JOIN '.$crmtablealias.' ON vtiger_crmentity.crmid = vtiger_productcomponent.productcomponentid
					WHERE vtiger_crmentity.deleted=0 AND vtiger_productcomponent.topdo=?)',
				array($this->id)
			);
			$ismember = $adb->query_result($ismember_query, 0, 0);
		}
		return (int)$ismember;
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
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$rel_table_arr = array(
			'Products'=>'vtiger_productcomponent',
			'Attachments'=>'vtiger_seattachmentsrel',
			'Quotes'=>'vtiger_inventoryproductrel',
			'PurchaseOrder'=>'vtiger_inventoryproductrel',
			'SalesOrder'=>'vtiger_inventoryproductrel',
			'Invoice'=>'vtiger_inventoryproductrel',
			'Leads'=>'vtiger_seproductsrel',
			'Accounts'=>'vtiger_seproductsrel',
			'Potentials'=>'vtiger_seproductsrel',
			'Contacts'=>'vtiger_seproductsrel',
		);
		$tbl_field_arr = array(
			'vtiger_productcomponent'=>'productcomponentid',
			'vtiger_seattachmentsrel'=>'attachmentsid',
			'vtiger_inventoryproductrel'=>'id',
			'vtiger_seproductsrel'=>'crmid',
		);
		$entity_tbl_field_arr = array(
			'vtiger_productcomponent'=>'topdo',
			'vtiger_seattachmentsrel'=>'crmid',
			'vtiger_inventoryproductrel'=>'productid',
			'vtiger_seproductsrel'=>'productid',
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
		$log->debug('< transferRelatedRecords');
	}

	/**
	 * Function to get the secondary query part of a report
	 * @param string primary module name
	 * @param string secondary module name
	 * @return string the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryplanner, $type = '', $where_condition = '') {
		global $current_user;
		$matrix = $queryplanner->newDependencyMatrix();

		$matrix->setDependency('vtiger_crmentityProducts', array('vtiger_groupsProducts', 'vtiger_usersProducts', 'vtiger_lastModifiedByProducts'));

		if (!$queryplanner->requireTable('vtiger_products', $matrix) && !$queryplanner->requireTable('vtiger_productcf', $matrix)) {
			return '';
		}
		$matrix->setDependency('vtiger_products', array('innerProduct','vtiger_crmentityProducts','vtiger_productcf','vtiger_vendorRelProducts'));

		$query = $this->getRelationQuery($module, $secmodule, 'vtiger_products', 'productid', $queryplanner);
		if ($queryplanner->requireTable('innerProduct')) {
			$query .= ' LEFT JOIN (
				SELECT vtiger_products.productid,
					(CASE WHEN (vtiger_products.currency_id = 1 ) THEN vtiger_products.unit_price
						ELSE (vtiger_products.unit_price / vtiger_currency_info.conversion_rate) END
					) AS actual_unit_price
				FROM vtiger_products
				LEFT JOIN vtiger_currency_info ON vtiger_products.currency_id = vtiger_currency_info.id
				LEFT JOIN vtiger_productcurrencyrel ON vtiger_products.productid = vtiger_productcurrencyrel.productid
				AND vtiger_productcurrencyrel.currencyid = '. $current_user->currency_id . '
			) AS innerProduct ON innerProduct.productid = vtiger_products.productid';
		}
		if ($queryplanner->requireTable('vtiger_crmentityProducts')) {
			$query .= ' left join '.$this->crmentityTable.' as vtiger_crmentityProducts on vtiger_crmentityProducts.crmid=vtiger_products.productid and vtiger_crmentityProducts.deleted=0';
		}
		if ($queryplanner->requireTable('vtiger_productcf')) {
			$query .= ' left join vtiger_productcf on vtiger_products.productid = vtiger_productcf.productid';
		}
		if ($queryplanner->requireTable('vtiger_groupsProducts')) {
			$query .= ' left join vtiger_groups as vtiger_groupsProducts on vtiger_groupsProducts.groupid = vtiger_crmentityProducts.smownerid';
		}
		if ($queryplanner->requireTable('vtiger_usersProducts')) {
			$query .= ' left join vtiger_users as vtiger_usersProducts on vtiger_usersProducts.id = vtiger_crmentityProducts.smownerid';
		}
		if ($queryplanner->requireTable('vtiger_vendorRelProducts')) {
			$query .= ' left join vtiger_vendor as vtiger_vendorRelProducts on vtiger_vendorRelProducts.vendorid = vtiger_products.vendor_id';
		}
		if ($queryplanner->requireTable('vtiger_lastModifiedByProducts')) {
			$query .= ' left join vtiger_users as vtiger_lastModifiedByProducts on vtiger_lastModifiedByProducts.id = vtiger_crmentityProducts.modifiedby ';
		}
		if ($queryplanner->requireTable('vtiger_CreatedByProducts')) {
			$query .= ' left join vtiger_users as vtiger_CreatedByProducts on vtiger_CreatedByProducts.id = vtiger_crmentityProducts.smcreatorid ';
		}
		return $query;
	}

	/**
	 * Function to get the relation tables for related modules
	 * @param string secondary module name
	 * @return array with table and field names storing relations between secondary module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			'HelpDesk' => array('vtiger_troubletickets'=>array('product_id','ticketid'),'vtiger_products'=>'productid'),
			'Quotes' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_products'=>'productid'),
			'PurchaseOrder' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_products'=>'productid'),
			'SalesOrder' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_products'=>'productid'),
			'Invoice' => array('vtiger_inventoryproductrel'=>array('productid','id'),'vtiger_products'=>'productid'),
			'Leads' => array('vtiger_seproductsrel'=>array('productid','crmid'),'vtiger_products'=>'productid'),
			'Accounts' => array('vtiger_seproductsrel'=>array('productid','crmid'),'vtiger_products'=>'productid'),
			'Contacts' => array('vtiger_seproductsrel'=>array('productid','crmid'),'vtiger_products'=>'productid'),
			'Potentials' => array('vtiger_seproductsrel'=>array('productid','crmid'),'vtiger_products'=>'productid'),
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_products'=>'productid'),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
		//Backup Campaigns-Product Relation
		global $adb;
		$cmp_res = $adb->pquery('SELECT campaignid FROM vtiger_campaign WHERE product_id = ?', array($id));
		if ($adb->num_rows($cmp_res) > 0) {
			$cmp_ids_list = array();
			for ($k=0; $k < $adb->num_rows($cmp_res); $k++) {
				$cmp_ids_list[] = $adb->query_result($cmp_res, $k, 'campaignid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_campaign', 'product_id', 'campaignid', implode(',', $cmp_ids_list));
			$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//we have to update the product_id as null for the campaigns which are related to this product
		$adb->pquery('UPDATE vtiger_campaign SET product_id=0 WHERE product_id = ?', array($id));
		$adb->pquery('DELETE from vtiger_seproductsrel WHERE productid=? or crmid=?', array($id,$id));
		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		$customRelModules = ['cbCalendar', 'Accounts', 'Potentials', 'Contacts', 'Leads', 'Vendors', 'Documents'];
		if (in_array($return_module, $customRelModules)) {
			$data = array();
			$data['sourceModule'] = getSalesEntityType($id);
			$data['sourceRecordId'] = $id;
			$data['destinationModule'] = $return_module;
			$data['destinationRecordId'] = $return_id;
			cbEventHandler::do_action('corebos.entity.link.delete', $data);
		}
		if ($return_module == 'cbCalendar') {
			$sql = 'DELETE FROM vtiger_seactivityrel WHERE crmid = ? AND activityid = ?';
			$adb->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Leads' || $return_module == 'Accounts' || $return_module == 'Contacts' || $return_module == 'Potentials') {
			$sql = 'DELETE FROM vtiger_seproductsrel WHERE productid = ? AND crmid = ?';
			$adb->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Vendors') {
			$sql = 'UPDATE vtiger_products SET vendor_id = ? WHERE productid = ?';
			$adb->pquery($sql, array(null, $id));
		} elseif ($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$adb->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
		if (in_array($return_module, $customRelModules)) {
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		}
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();

		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Leads' || $with_module == 'Accounts' || $with_module == 'Contacts' || $with_module == 'Potentials') {
				$query = $adb->pquery("SELECT * from vtiger_seproductsrel WHERE crmid=? and productid=?", array($crmid, $with_crmid));
				if ($adb->num_rows($query)==0) {
					$adb->pquery("insert into vtiger_seproductsrel values (?,?,?)", array($with_crmid, $crmid, $with_module));
				}
			} elseif ($with_module=='Products') {
				include_once 'include/Webservices/Create.php';
				global $current_user;
				$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
				$default_values = array(
					'assigned_user_id' => $usrwsid,
					'frompdo' =>vtws_getEntityId('Products').'x'.$crmid,
					'topdo' =>vtws_getEntityId('Products').'x'.$with_crmid,
					'relmode' => 'Required',
					'relfrom' => date('Y-m-d'),
					'relto' => '2030-01-01',
					'quantity' => '1',
					'instructions' => '',
				);
				vtws_create('ProductComponent', $default_values, $current_user);
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	public function getvtlib_open_popup_window_function($fieldname, $basemodule) {
		if ($basemodule=='Movement') {
			return 'WareHouseProductsOpenCapture';
		} else {
			return 'vtlib_open_popup_window';
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	public function getQueryByModuleField($module, $fieldname, $srcrecord, $query = '') {
		global $adb;
		$wherepos = stripos($query, 'where'); // there is always a where
		$query_body = substr($query, 0, $wherepos-1);
		$query_cond = substr($query, $wherepos+5);
		if ($module == 'Movement') {
			$srcwhID = empty($_REQUEST['srcwhid']) ? 0 : vtlib_purify($_REQUEST['srcwhid']);
			$whrs = $adb->pquery('SELECT warehno FROM vtiger_warehouse WHERE warehouseid=?', array($srcwhID));
			if ($whrs && $adb->num_rows($whrs) && $whrs->fields['warehno'] != 'Purchase') {
				$crmtable = CRMEntity::getcrmEntityTableAlias('Stock', true);
				return 'SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.description, vtiger_products.*,
					vtiger_productcf.*,vtiger_stock.stocknum as qtyinstock
				FROM vtiger_products
				INNER JOIN '.$this->crmentityTableAlias.' ON vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
				INNER JOIN vtiger_stock ON vtiger_stock.pdoid=vtiger_products.productid
				INNER JOIN '.$crmtable.' crmstock ON vtiger_stock.stockid=crmstock.crmid and crmstock.deleted = 0
				LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_products.vendor_id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				WHERE vtiger_products.productid > 0 AND vtiger_crmentity.deleted = 0 and vtiger_products.discontinued <> 0
				and vtiger_stock.whid='.$srcwhID;
			}
		} elseif (vtlib_isModuleActive('Warehouse') && ($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'MassiveMovements')) {
			$crmtable = CRMEntity::getcrmEntityTableAlias('Stock', true);
			$query_relation = ' INNER JOIN vtiger_stock ON vtiger_stock.pdoid=vtiger_products.productid
				INNER JOIN '.$crmtable.' crmstock ON vtiger_stock.stockid=crmstock.crmid and crmstock.deleted=0';
			$whID = empty($_REQUEST['whid']) ? 0 : vtlib_purify($_REQUEST['whid']);
			$whrs = $adb->pquery('SELECT warehno FROM vtiger_warehouse WHERE warehouseid=?', array($whID));
			if ($whrs && $adb->num_rows($whrs) && $whrs->fields['warehno'] != 'Purchase' && $whID != 0) {
				return $query_body .$query_relation.' WHERE vtiger_stock.whid='.$whID.' and '.$query_cond;
			}
		}
		return $query;
	}
}
?>
