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

class PriceBooks extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_pricebook';
	public $table_index= 'pricebookid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	public $tab_name = array('vtiger_crmentity','vtiger_pricebook','vtiger_pricebookcf');
	public $tab_name_index = array('vtiger_crmentity'=>'crmid','vtiger_pricebook'=>'pricebookid','vtiger_pricebookcf'=>'pricebookid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_pricebookcf', 'pricebookid');

	// This is the list of fields that are in the lists.
	public $list_fields = array(
		'Price Book Name'=>array('pricebook'=>'bookname'),
		'Price'=>array('pricebookproductrel'=>'listprice'),
		'Currency'=>array('pricebook'=>'currency_id'),
		'Active'=>array('pricebook'=>'active')
	);
	public $list_fields_name = array(
		'Price Book Name'=>'bookname',
		'Price'=>'listprice',
		'Currency'=>'currency_id',
		'Active'=>'active'
	);
	public $list_link_field= 'bookname';

	public $search_fields = array(
		'Price Book Name'=>array('pricebook'=>'bookname'),
		'Currency'=>array('pricebook'=>'currency_id'),
		'Active'=>array('pricebook'=>'active')
	);
	public $search_fields_name = array(
		'Price Book Name'=>'bookname',
		'Currency'=>'currency_id',
		'Active'=>'active'
	);

	// For Popup window record selection
	public $popup_fields = array('bookname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array('bookname');

	// For Alphabetical search
	public $def_basicsearch_col = 'bookname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'bookname';

	public $default_order_by = 'bookname';
	public $default_sort_order = 'ASC';

	public $mandatory_fields = array('bookname','currency_id','pricebook_no','createdtime' ,'modifiedtime');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		// Update the list prices in the price book with the unit price, if the Currency has been changed
		$this->updateListPrices();
	}

	/* Function to Update the List prices for all the products of a current price book
	   with its Unit price, if the Currency for Price book has changed. */
	private function updateListPrices() {
		global $log, $adb;
		$log->debug('Entering function updateListPrices...');
		$pricebook_currency = $this->column_fields['currency_id'];
		$prod_res = $adb->pquery('select * from vtiger_pricebookproductrel where pricebookid=? AND usedcurrency != ?', array($this->id, $pricebook_currency));
		$numRows = $adb->num_rows($prod_res);

		for ($i=0; $i<$numRows; $i++) {
			$product_id = $adb->query_result($prod_res, $i, 'productid');
			$list_price = $adb->query_result($prod_res, $i, 'listprice');
			$used_currency = $adb->query_result($prod_res, $i, 'usedcurrency');
			$product_currency_info = getCurrencySymbolandCRate($used_currency);
			$product_conv_rate = $product_currency_info['rate'];
			$pricebook_currency_info = getCurrencySymbolandCRate($pricebook_currency);
			$pb_conv_rate = $pricebook_currency_info['rate'];
			$conversion_rate = $pb_conv_rate / $product_conv_rate;
			$computed_list_price = $list_price * $conversion_rate;
			$query = 'update vtiger_pricebookproductrel set listprice=?, usedcurrency=? where pricebookid=? and productid=?';
			$params = array($computed_list_price, $pricebook_currency, $this->id, $product_id);
			$adb->pquery($query, $params);
		}
		$log->debug('Exiting function updateListPrices...');
	}

	/**	function used to get the products which are related to the pricebook
	 *	@param int $id - pricebook id
	 *	@return array - return an array which will be returned from the function getPriceBookRelatedProducts
	 **/
	public function get_pricebook_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_pricebook_products('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";

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
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='submit' name='button' onclick=\"this.form.action.value='AddProductsToPriceBook';".
					"this.form.module.value='$related_module';this.form.return_module.value='$currentModule';this.form.return_action.value='PriceBookDetailView'\" ".
					"value='". getTranslatedString('LBL_SELECT'). ' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
		}

		$query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode, vtiger_products.commissionrate,
					vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_pricebookproductrel.listprice
				FROM vtiger_products
				INNER JOIN vtiger_pricebookproductrel ON vtiger_products.productid = vtiger_pricebookproductrel.productid
				INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_pricebook on vtiger_pricebook.pricebookid = vtiger_pricebookproductrel.pricebookid
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid '
				. getNonAdminAccessControlQuery($related_module, $current_user) .'
				WHERE vtiger_pricebook.pricebookid = '.$id.' and vtiger_crmentity.deleted = 0';

		$this->retrieve_entity_info($id, $this_module);
		$return_value = getPriceBookRelatedProducts($query, $this, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_pricebook_products method ...');
		return $return_value;
	}

	/**	function used to get the services which are related to the pricebook
	 *	@param int $id - pricebook id
	 *	@return array - return an array which will be returned from the function getPriceBookRelatedServices
	 **/
	public function get_pricebook_services($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_pricebook_services('.$id.') method ...');
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='submit' name='button' onclick=\"this.form.action.value='AddServicesToPriceBook';".
					"this.form.module.value='$related_module';this.form.return_module.value='$currentModule';this.form.return_action.value='PriceBookDetailView'\" ".
					"value='". getTranslatedString('LBL_SELECT'). ' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
		}

		$query = 'SELECT vtiger_service.serviceid, vtiger_service.servicename, vtiger_service.commissionrate, vtiger_service.qty_per_unit,
				vtiger_service.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_pricebookproductrel.listprice
			FROM vtiger_service
			INNER JOIN vtiger_pricebookproductrel on vtiger_service.serviceid = vtiger_pricebookproductrel.productid
			INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_service.serviceid
			INNER JOIN vtiger_pricebook on vtiger_pricebook.pricebookid = vtiger_pricebookproductrel.pricebookid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid '
			. getNonAdminAccessControlQuery($related_module, $current_user) .'
			WHERE vtiger_pricebook.pricebookid = '.$id.' and vtiger_crmentity.deleted = 0';

		$this->retrieve_entity_info($id, $this_module);
		$return_value = $other->getPriceBookRelatedServices($query, $this, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_pricebook_services method ...');
		return $return_value;
	}

	/**	function used to get whether the pricebook has related with a product or not
	 *	@param int $id - product id
	 *	@return true or false - if there are no pricebooks available or associated pricebooks for the product is equal to total number of pricebooks
	 *		then return false, else return true
	 */
	public function get_pricebook_noproduct($id) {
		global $log;
		$log->debug('Entering get_pricebook_noproduct('.$id.') method ...');

		$query = 'select vtiger_crmentity.crmid, vtiger_pricebook.*
			from vtiger_pricebook
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_pricebook.pricebookid
			where vtiger_crmentity.deleted=0';
		$result = $this->db->pquery($query, array());
		$no_count = $this->db->num_rows($result);
		if ($no_count !=0) {
			$pb_query = 'select vtiger_crmentity.crmid, vtiger_pricebook.pricebookid,vtiger_pricebookproductrel.productid
				from vtiger_pricebook
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_pricebook.pricebookid
				inner join vtiger_pricebookproductrel on vtiger_pricebookproductrel.pricebookid=vtiger_pricebook.pricebookid
				where vtiger_crmentity.deleted=0 and vtiger_pricebookproductrel.productid=?';
			$result_pb = $this->db->pquery($pb_query, array($id));
			if ($no_count == $this->db->num_rows($result_pb)) {
				$log->debug('Exiting get_pricebook_noproduct method ...');
				return false;
			} elseif ($this->db->num_rows($result_pb) == 0) {
				$log->debug('Exiting get_pricebook_noproduct method ...');
				return true;
			} elseif ($this->db->num_rows($result_pb) < $no_count) {
				$log->debug('Exiting get_pricebook_noproduct method ...');
				return true;
			}
		} else {
			$log->debug('Exiting get_pricebook_noproduct method ...');
			return false;
		}
	}

	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	public function generateReportsQuery($module, $queryplanner) {
		$moduletable = $this->table_name;
		$moduleindex = $this->table_index;
		$modulecftable = $this->customFieldTable[0];
		$modulecfindex = $this->customFieldTable[1];

		$cfquery = '';
		if (isset($modulecftable) && $queryplanner->requireTable($modulecftable)) {
			$cfquery = "inner join $modulecftable as $modulecftable on $modulecftable.$modulecfindex=$moduletable.$moduleindex";
		}

		$query = "from $moduletable $cfquery
			inner join vtiger_crmentity on vtiger_crmentity.crmid=$moduletable.$moduleindex";
		if ($queryplanner->requireTable("vtiger_currency_info$module")) {
			$query .= "  left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = $moduletable.currency_id";
		}
		if ($queryplanner->requireTable("vtiger_groups$module")) {
			$query .= " left join vtiger_groups as vtiger_groups$module on vtiger_groups$module.groupid = vtiger_crmentity.smownerid";
		}
		if ($queryplanner->requireTable("vtiger_users$module")) {
			$query .= " left join vtiger_users as vtiger_users$module on vtiger_users$module.id = vtiger_crmentity.smownerid";
		}
		$query .= " left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid";

		if ($queryplanner->requireTable("vtiger_lastModifiedByPriceBooks")) {
			$query .= " left join vtiger_users as vtiger_lastModifiedByPriceBooks on vtiger_lastModifiedByPriceBooks.id = vtiger_crmentity.modifiedby ";
		}
		if ($queryplanner->requireTable('vtiger_CreatedBy'.$module)) {
			$query .= " left join vtiger_users as vtiger_CreatedBy".$module." on vtiger_CreatedBy".$module.".id = vtiger_crmentity.smcreatorid";
		}
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			'Products' => array('vtiger_pricebookproductrel' => array('pricebookid', 'productid'), 'vtiger_pricebook' => 'pricebookid'),
			'Services' => array('vtiger_pricebookproductrel' => array('pricebookid', 'productid'), 'vtiger_pricebook' => 'pricebookid'),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}
}
?>
