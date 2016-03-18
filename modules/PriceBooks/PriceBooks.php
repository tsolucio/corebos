<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once('user_privileges/default_module_view.php');

class PriceBooks extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity
	var $table_name = 'vtiger_pricebook';
	var $table_index= 'pricebookid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	var $tab_name = Array('vtiger_crmentity','vtiger_pricebook','vtiger_pricebookcf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_pricebook'=>'pricebookid','vtiger_pricebookcf'=>'pricebookid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_pricebookcf', 'pricebookid');

	var $sortby_fields = Array('bookname');

	// This is the list of fields that are in the lists.
	var $list_fields = Array(
		'Price Book Name'=>Array('pricebook'=>'bookname'),
		'Price'=>Array('pricebookproductrel'=>'listprice'),
		'Currency'=>Array('pricebook'=>'currency_id'),
		'Active'=>Array('pricebook'=>'active')
	);

	var $list_fields_name = Array(
		'Price Book Name'=>'bookname',
		'Price'=>'listprice',
		'Currency'=>'currency_id',
		'Active'=>'active'
	);
	var $list_link_field= 'bookname';

	var $search_fields = Array(
		'Price Book Name'=>Array('pricebook'=>'bookname')
	);
	var $search_fields_name = Array(
		'Price Book Name'=>'bookname',
	);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'bookname';
	var $default_sort_order = 'ASC';

	var $mandatory_fields = Array('bookname','currency_id','pricebook_no','createdtime' ,'modifiedtime');

	// For Alphabetical search
	var $def_basicsearch_col = 'bookname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'bookname';

	function __construct() {
		global $log;
		$this_module = get_class($this);
		$this->column_fields = getColumnFields($this_module);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
		$sql = 'SELECT 1 FROM vtiger_field WHERE uitype=69 and tabid = ? limit 1';
		$tabid = getTabid($this_module);
		$result = $this->db->pquery($sql, array($tabid));
		if ($result and $this->db->num_rows($result)==1) {
			$this->HasDirectImageField = true;
		}
	}

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		// Update the list prices in the price book with the unit price, if the Currency has been changed
		$this->updateListPrices();
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;
		$thismodule = $_REQUEST['module'];

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name 
				FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		$rel_mods[$this->table_name] = 1;
		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if($rel_mods[$other->table_name]) {
				$rel_mods[$other->table_name] = $rel_mods[$other->table_name] + 1;
				$alias = $other->table_name.$rel_mods[$other->table_name];
				$query_append = "as $alias";
			} else {
				$alias = $other->table_name;
				$query_append = '';
				$rel_mods[$other->table_name] = 1;
			}

			$query .= " LEFT JOIN $other->table_name $query_append ON $alias.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = PearDatabase::getInstance();
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	function create_import_query($module) {
		global $current_user;
		$query = "SELECT vtiger_crmentity.crmid, case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_crmentity.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_users_last_import.assigned_user_id='$current_user->id'
			AND vtiger_users_last_import.bean_type='$module'
			AND vtiger_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb;
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error());
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid=?";
			$result2 = $adb->pquery($query2, array($row1['bean_id'])) or die("Error undoing last import: ".mysql_error());
			$count++;
		}
		return $count;
	}

	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb;
		$record_user = $this->column_fields["assigned_user_id"];

		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?", array($record_user, $record_user));
			if($this->db->num_rows($sqlresult)!= 1) {
				$this->column_fields["assigned_user_id"] = $current_user->id;
			} else {
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields["assigned_user_id"] = $row['id'];
				} else {
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$where_clause = " WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		$query = $select_clause . $from_clause .
					" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/* Function to Update the List prices for all the products of a current price book
	   with its Unit price, if the Currency for Price book has changed. */
	function updateListPrices() {
		global $log, $adb;
		$log->debug("Entering function updateListPrices...");
		$pricebook_currency = $this->column_fields['currency_id'];
		$prod_res = $adb->pquery("select * from vtiger_pricebookproductrel where pricebookid=? AND usedcurrency != ?",
							array($this->id, $pricebook_currency));
		$numRows = $adb->num_rows($prod_res);

		for($i=0;$i<$numRows;$i++) {
			$product_id = $adb->query_result($prod_res,$i,'productid');
			$list_price = $adb->query_result($prod_res,$i,'listprice');
			$used_currency = $adb->query_result($prod_res,$i,'usedcurrency');
			$product_currency_info = getCurrencySymbolandCRate($used_currency);
			$product_conv_rate = $product_currency_info['rate'];
			$pricebook_currency_info = getCurrencySymbolandCRate($pricebook_currency);
			$pb_conv_rate = $pricebook_currency_info['rate'];
			$conversion_rate = $pb_conv_rate / $product_conv_rate;
			$computed_list_price = $list_price * $conversion_rate;

			$query = "update vtiger_pricebookproductrel set listprice=?, usedcurrency=? where pricebookid=? and productid=?";
			$params = array($computed_list_price, $pricebook_currency, $this->id, $product_id);
			$adb->pquery($query, $params);
		}
		$log->debug("Exiting function updateListPrices...");
	}

	/**	function used to get the products which are related to the pricebook
	 *	@param int $id - pricebook id
	 *	@return array - return an array which will be returned from the function getPriceBookRelatedProducts
	 **/
	function get_pricebook_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_pricebook_products(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='submit' name='button' onclick=\"this.form.action.value='AddProductsToPriceBook';this.form.module.value='$related_module';this.form.return_module.value='$currentModule';this.form.return_action.value='PriceBookDetailView'\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		$query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode, vtiger_products.commissionrate,
						vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
						vtiger_pricebookproductrel.listprice
				FROM vtiger_products
				INNER JOIN vtiger_pricebookproductrel ON vtiger_products.productid = vtiger_pricebookproductrel.productid
				INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_pricebook on vtiger_pricebook.pricebookid = vtiger_pricebookproductrel.pricebookid
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid '
				. getNonAdminAccessControlQuery($related_module, $current_user) .'
				WHERE vtiger_pricebook.pricebookid = '.$id.' and vtiger_crmentity.deleted = 0';

		$this->retrieve_entity_info($id,$this_module);
		$return_value = getPriceBookRelatedProducts($query,$this,$returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_pricebook_products method ...");
		return $return_value;
	}

	/**	function used to get the services which are related to the pricebook
	 *	@param int $id - pricebook id
	 *	@return array - return an array which will be returned from the function getPriceBookRelatedServices
	 **/
	function get_pricebook_services($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_pricebook_services(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='submit' name='button' onclick=\"this.form.action.value='AddServicesToPriceBook';this.form.module.value='$related_module';this.form.return_module.value='$currentModule';this.form.return_action.value='PriceBookDetailView'\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
		}

		$query = 'SELECT vtiger_service.serviceid, vtiger_service.servicename, vtiger_service.commissionrate,
					vtiger_service.qty_per_unit, vtiger_service.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
					vtiger_pricebookproductrel.listprice
			FROM vtiger_service
			INNER JOIN vtiger_pricebookproductrel on vtiger_service.serviceid = vtiger_pricebookproductrel.productid
			INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_service.serviceid
			INNER JOIN vtiger_pricebook on vtiger_pricebook.pricebookid = vtiger_pricebookproductrel.pricebookid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid '
			. getNonAdminAccessControlQuery($related_module, $current_user) .'
			WHERE vtiger_pricebook.pricebookid = '.$id.' and vtiger_crmentity.deleted = 0';

		$this->retrieve_entity_info($id,$this_module);
		$return_value = $other->getPriceBookRelatedServices($query,$this,$returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_pricebook_services method ...");
		return $return_value;
	}

	/**	function used to get whether the pricebook has related with a product or not
	 *	@param int $id - product id
	 *	@return true or false - if there are no pricebooks available or associated pricebooks for the product is equal to total number of pricebooks then return false, else return true
	 */
	function get_pricebook_noproduct($id)
	{
		global $log;
		$log->debug("Entering get_pricebook_noproduct(".$id.") method ...");

		$query = "select vtiger_crmentity.crmid, vtiger_pricebook.* from vtiger_pricebook inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_pricebook.pricebookid where vtiger_crmentity.deleted=0";
		$result = $this->db->pquery($query, array());
		$no_count = $this->db->num_rows($result);
		if($no_count !=0)
		{
			$pb_query = 'select vtiger_crmentity.crmid, vtiger_pricebook.pricebookid,vtiger_pricebookproductrel.productid from vtiger_pricebook inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_pricebook.pricebookid inner join vtiger_pricebookproductrel on vtiger_pricebookproductrel.pricebookid=vtiger_pricebook.pricebookid where vtiger_crmentity.deleted=0 and vtiger_pricebookproductrel.productid=?';
			$result_pb = $this->db->pquery($pb_query, array($id));
			if($no_count == $this->db->num_rows($result_pb))
			{
				$log->debug("Exiting get_pricebook_noproduct method ...");
				return false;
			}
			elseif($this->db->num_rows($result_pb) == 0)
			{
				$log->debug("Exiting get_pricebook_noproduct method ...");
				return true;
			}
			elseif($this->db->num_rows($result_pb) < $no_count)
			{
				$log->debug("Exiting get_pricebook_noproduct method ...");
				return true;
			}
		}
		else
		{
			$log->debug("Exiting get_pricebook_noproduct method ...");
			return false;
		}
	}

	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	function generateReportsQuery($module){
		$moduletable = $this->table_name;
		$moduleindex = $this->table_index;
		$modulecftable = $this->customFieldTable[0];
		$modulecfindex = $this->customFieldTable[1];

		$cfquery = '';
		if(isset($modulecftable)){
			$cfquery = "inner join $modulecftable as $modulecftable on $modulecftable.$modulecfindex=$moduletable.$moduleindex";
		}

		$query = "from $moduletable $cfquery
			inner join vtiger_crmentity on vtiger_crmentity.crmid=$moduletable.$moduleindex
			left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = $moduletable.currency_id
			left join vtiger_groups as vtiger_groups$module on vtiger_groups$module.groupid = vtiger_crmentity.smownerid
			left join vtiger_users as vtiger_users$module on vtiger_users$module.id = vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			left join vtiger_users as vtiger_lastModifiedByPriceBooks on vtiger_lastModifiedByPriceBooks.id = vtiger_crmentity.modifiedby ";
		return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_pricebook","pricebookid");
		$query .=" left join vtiger_crmentity as vtiger_crmentityPriceBooks on vtiger_crmentityPriceBooks.crmid=vtiger_pricebook.pricebookid and vtiger_crmentityPriceBooks.deleted=0
				left join vtiger_currency_info as vtiger_currency_infoPriceBooks on vtiger_currency_infoPriceBooks.id = vtiger_pricebook.currency_id
				left join vtiger_users as vtiger_usersPriceBooks on vtiger_usersPriceBooks.id = vtiger_crmentityPriceBooks.smownerid
				left join vtiger_groups as vtiger_groupsPriceBooks on vtiger_groupsPriceBooks.groupid = vtiger_crmentityPriceBooks.smownerid";
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Products" => array("vtiger_pricebookproductrel"=>array("pricebookid","productid"),"vtiger_pricebook"=>"pricebookid"),
			"Services" => array("vtiger_pricebookproductrel"=>array("pricebookid","productid"),"vtiger_pricebook"=>"pricebookid"),
		);
		return $rel_tables[$secmodule];
	}

}
?>
