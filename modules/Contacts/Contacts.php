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
require_once 'modules/Potentials/Potentials.php';
require_once 'modules/Calendar/Activity.php';
require_once 'modules/Campaigns/Campaigns.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Emails/Emails.php';
require_once 'modules/HelpDesk/HelpDesk.php';
require 'user_privileges/default_module_view.php';

class Contacts extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_contactdetails';
	public $table_index= 'contactid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_contactscf', 'contactid');
	public $related_tables = array('vtiger_account' => array('accountid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity','vtiger_contactdetails','vtiger_contactaddress','vtiger_contactsubdetails','vtiger_contactscf','vtiger_customerdetails');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_contactdetails'=>'contactid',
		'vtiger_contactaddress'=>'contactaddressid',
		'vtiger_contactsubdetails'=>'contactsubscriptionid',
		'vtiger_contactscf'=>'contactid',
		'vtiger_customerdetails'=>'customerid'
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Last Name' => array('contactdetails'=>'lastname'),
		'First Name' => array('contactdetails'=>'firstname'),
		'Title' => array('contactdetails'=>'title'),
		'Account Name' => array('account'=>'accountid'),
		'Email' => array('contactdetails'=>'email'),
		'Office Phone' => array('contactdetails'=>'phone'),
		'Assigned To' => array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Last Name' => 'lastname',
		'First Name' => 'firstname',
		'Title' => 'title',
		'Account Name' => 'account_id',
		'Email' => 'email',
		'Office Phone' => 'phone',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field= 'lastname';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Name' => array('contactdetails'=>'lastname'),
		'Title' => array('contactdetails'=>'title'),
		'Account Name'=>array('contactdetails'=>'account_id'),
		'Assigned To'=>array('crmentity'=>'smownerid'),
	);
	public $search_fields_name = array(
		'Name' => 'lastname',
		'Title' => 'title',
		'Account Name'=>'account_id',
		'Assigned To'=>'assigned_user_id'
	);

	// For Popup window record selection
	public $popup_fields = array('firstname','lastname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array('lastname','firstname','title','email','phone','smownerid','accountname');

	// For Alphabetical search
	public $def_basicsearch_col = 'lastname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'lastname';

	// Required Information for enabling Import feature
	public $required_fields = array('lastname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'lastname';
	public $default_sort_order = 'ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('lastname', 'createdtime', 'modifiedtime');

	/** Function to get the number of Contacts assigned to a particular User.
	*  @param varchar $user name - Assigned to User
	*  Returns the count of contacts assigned to user.
	*/
	public function getCount($user_name) {
		global $log;
		$log->debug('Entering getCount('.$user_name.') method ...');
		$query = 'select count(*)
			from vtiger_contactdetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
			inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where user_name=? and vtiger_crmentity.deleted=0';
		$result = $this->db->pquery($query, array($user_name), true, 'Error retrieving contacts count');
		$log->debug('Exiting getCount');
		return $this->db->query_result($result, 0, 0);
	}

	/** Function to process list query for Plugin with Security Parameters for a given query
	 *  @param $query
	 *  Returns the results of query in array format
	 */
	public function plugin_process_list_query($query) {
		global $log, $adb, $current_user,$currentModule;
		$log->debug('Entering plugin_process_list_query(' . $query . ') method ...');
		$permitted_field_lists = array();
		require 'user_privileges/user_privileges_' . $current_user->id . '.php';
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = 'select columnname from vtiger_field where tabid=4 and block <> 75 and vtiger_field.presence in (0,2)';
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'select columnname
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=4 and vtiger_field.block <> 6 and vtiger_field.block <> 75 and vtiger_field.displaytype in (1,2,4,3)
					and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= ' and vtiger_profile2field.profileid in (' . generateQuestionMarks($profileList) . ')';
				$params1[] = $profileList;
			}
		}
		$result1 = $this->db->pquery($sql1, $params1);
		for ($i = 0; $i < $adb->num_rows($result1); $i++) {
			$permitted_field_lists[] = $adb->query_result($result1, $i, 'columnname');
		}

		$result = &$this->db->query($query, true, "Error retrieving $currentModule list: ");
		$list = array();
		$rows_found = $this->db->getRowCount($result);
		if ($rows_found != 0) {
			for ($index = 0, $row = $this->db->fetchByAssoc($result, $index); $row && $index < $rows_found; $index++, $row = $this->db->fetchByAssoc($result, $index)) {
				$contact = array();

				$contact['lastname'] = in_array('lastname', $permitted_field_lists) ? $row['lastname'] : '';
				$contact['firstname'] = in_array('firstname', $permitted_field_lists) ? $row['firstname'] : '';
				$contact['email'] = in_array('email', $permitted_field_lists) ? $row['email'] : '';

				if (in_array('accountid', $permitted_field_lists)) {
					$contact['accountname'] = $row['accountname'];
					$contact['account_id'] = $row['accountid'];
				} else {
					$contact['accountname'] = '';
					$contact['account_id'] = '';
				}
				$contact['contactid'] = $row[contactid];
				$list[] = $contact;
			}
		}

		$response = array();
		$response['list'] = $list;
		$response['row_count'] = $rows_found;
		//$response['next_offset'] = $next_offset;
		//$response['previous_offset'] = $previous_offset;
		$log->debug('Exiting plugin_process_list_query method ...');
		return $response;
	}

	/** Returns a list of the associated opportunities */
	public function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule, $adb;
		$log->debug('Entering get_opportunities('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

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
					"' class='crmbutton small edit' type='button' ".
					"onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable".
					"&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='".
					getTranslatedString('LBL_SELECT'). ' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
					$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
					$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query ='select case when (vtiger_users.user_name not like "") then '.$userNameSql.' else vtiger_groups.groupname end as user_name,
				vtiger_contactdetails.accountid, vtiger_contactdetails.contactid , vtiger_potential.potentialid, vtiger_potential.potentialname,
				vtiger_potential.potentialtype, vtiger_potential.sales_stage, vtiger_potential.amount, vtiger_potential.closingdate,
				vtiger_potential.related_to, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_account.accountname
			from vtiger_contactdetails
			left join vtiger_contpotentialrel on vtiger_contpotentialrel.contactid=vtiger_contactdetails.contactid
			left join vtiger_potential on (vtiger_potential.potentialid = vtiger_contpotentialrel.potentialid or
				vtiger_potential.related_to = vtiger_contactdetails.contactid or
				vtiger_potential.related_to = vtiger_contactdetails.accountid)
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.potentialid
			left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where vtiger_contactdetails.contactid ='.$id.' and vtiger_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_opportunities method...');
		return $return_value;
	}

	/**
	* Function to get Contact related Tickets.
	* @param  integer   $id      - contactid
	* returns related Ticket records in array format
	*/
	public function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		return parent::get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions);
	}

	/**
	 * Function to get Contact related Quotes
	 * @param  integer   $id  - contactid
	 * returns related Quotes record in array format
	 */
	public function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_quotes('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}
		$button = '';

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.*,
				vtiger_quotes.*,vtiger_potential.potentialname,vtiger_contactdetails.lastname,vtiger_account.accountname
			from vtiger_quotes
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid
			left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_quotes.contactid
			left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid
			left join vtiger_account on vtiger_account.accountid = vtiger_quotes.accountid
			LEFT JOIN vtiger_quotescf ON vtiger_quotescf.quoteid = vtiger_quotes.quoteid
			LEFT JOIN vtiger_quotesbillads ON vtiger_quotesbillads.quotebilladdressid = vtiger_quotes.quoteid
			LEFT JOIN vtiger_quotesshipads ON vtiger_quotesshipads.quoteshipaddressid = vtiger_quotes.quoteid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_quotes');
		return $return_value;
	}

	/**
	 * Function to get Contact related SalesOrder
	 * @param  integer   $id  - contactid
	 * returns related SalesOrder record in array format
	 */
	public function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_salesorder('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}
		$button = '';

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.*,
				vtiger_salesorder.*, vtiger_quotes.subject as quotename, vtiger_account.accountname, vtiger_contactdetails.lastname
			from vtiger_salesorder
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid
			left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid
			LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.start_period = vtiger_salesorder.salesorderid
			left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_salesorder.contactid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_salesorder.contactid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_salesorder method ...');
		return $return_value;
	}

	 /**
	 * Function to get Contact related Products
	 * @param  integer   $id  - contactid
	 * returns related Products record in array format
	 */
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug('Entering get_products('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

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
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$query = 'SELECT vtiger_products.*,vtiger_productcf.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,vtiger_contactdetails.lastname
			FROM vtiger_products
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.productid=vtiger_products.productid and vtiger_seproductsrel.setype="Contacts"
			INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
			INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seproductsrel.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_contactdetails.contactid = '.$id.' and vtiger_crmentity.deleted = 0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_products method ...');
		return $return_value;
	}

	/**
	 * Function to get Contact related PurchaseOrder
	 * @param  integer   $id  - contactid
	 * returns related PurchaseOrder record in array format
	 */
	public function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_purchase_orders('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}
		$button = '';

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.*,
				vtiger_purchaseorder.*,vtiger_purchaseordercf.*,vtiger_vendor.vendorname,vtiger_contactdetails.lastname
			from vtiger_purchaseorder
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid
			left outer join vtiger_vendor on vtiger_purchaseorder.vendorid=vtiger_vendor.vendorid
			left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_purchaseorder.contactid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_pobillads ON vtiger_pobillads.pobilladdressid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_poshipads ON vtiger_poshipads.poshipaddressid = vtiger_purchaseorder.purchaseorderid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_purchaseorder.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_purchase_orders method ...');
		return $return_value;
	}

	/** Returns a list of the associated Campaigns
	 * @param $id -- campaign id :: Type Integer
	 * @returns list of campaigns in array format
	 */
	public function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_campaigns('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}
		$button = '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW').' '. $singular_modname
					."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,".'"sendmail_cont");sendmail("'.$this_module.'",'.$id
					.");' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW').' '. $singular_modname."'></td>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus,
				vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime
			from vtiger_campaign
			inner join vtiger_campaigncontrel on vtiger_campaigncontrel.campaignid=vtiger_campaign.campaignid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_campaign.campaignid
			inner join vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			where vtiger_campaigncontrel.contactid=".$id.' and vtiger_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_campaigns method ...');
		return $return_value;
	}

	/**
	* Function to get Contact related Invoices
	* @param  integer   $id      - contactid
	* returns related Invoices record in array format
	*/
	public function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug('Entering get_invoices('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}
		$button = '';

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_crmentity.*, vtiger_invoice.*, vtiger_contactdetails.lastname,vtiger_contactdetails.firstname, vtiger_salesorder.subject AS salessubject
			FROM vtiger_invoice
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			LEFT OUTER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_invoice.contactid
			LEFT OUTER JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0
			AND vtiger_contactdetails.contactid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_invoices method ...');
		return $return_value;
	}

	/**
	* Function to get Contact related vendors.
	* @param  integer   $id      - contactid
	* returns related vendor records in array format
	*/
	public function get_vendors($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug('Entering get_vendors('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

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
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_crmentity.crmid, vtiger_vendor.*, vtiger_vendorcf.*
			from vtiger_vendor inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_vendor.vendorid
			INNER JOIN vtiger_vendorcontactrel on vtiger_vendorcontactrel.vendorid=vtiger_vendor.vendorid
			LEFT JOIN vtiger_vendorcf on vtiger_vendorcf.vendorid=vtiger_vendor.vendorid
			LEFT JOIN vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0 and vtiger_vendorcontactrel.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_vendors method ...');
		return $return_value;
	}

	/** Function to export the contact records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Contacts Query.
	*/
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug('Entering create_export_query('.$where.') method ...');

		include 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('Contacts', 'detail_view');
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT vtiger_contactdetails.salutation as 'Salutation',$fields_list,
				case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_contactdetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.status='Active'
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_account on vtiger_contactdetails.accountid=vtiger_account.accountid
			left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid=vtiger_contactdetails.contactid
			left join vtiger_contactscf on vtiger_contactscf.contactid=vtiger_contactdetails.contactid
			left join vtiger_customerdetails on vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_contactdetails vtiger_contactdetails2 ON vtiger_contactdetails2.contactid = vtiger_contactdetails.reportsto";
		$query .= getNonAdminAccessControlQuery('Contacts', $current_user);
		$where_auto = ' vtiger_crmentity.deleted = 0 ';
		if ($where != '') {
			$query .= " WHERE ($where) AND ".$where_auto;
		} else {
			$query .= ' WHERE '.$where_auto;
		}
		$log->debug('Exiting create_export_query method ...');
		return $query;
	}

	/** Function to get the Columnnames of the Contacts
	* Used By vtigerCRM Word Plugin
	* Returns the Merge Fields for Word Plugin
	*/
	public function getColumnNames() {
		global $log, $current_user;
		$log->debug('Entering getColumnNames() method ...');
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = 'select fieldlabel from vtiger_field where tabid=4 and block <> 75 and vtiger_field.presence in (0,2)';
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'select vtiger_field.fieldid,fieldlabel
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=4 and vtiger_field.block <> 75 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0
					and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .') group by fieldid';
				$params1[] = $profileList;
			}
		}
		$result = $this->db->pquery($sql1, $params1);
		$numRows = $this->db->num_rows($result);
		$custom_fields = array();
		for ($i=0; $i < $numRows; $i++) {
			$custom_fields[$i] = $this->db->query_result($result, $i, 'fieldlabel');
			$custom_fields[$i] = preg_replace('/\s+/', '', $custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug('Exiting getColumnNames method ...');
		return $mergeflds;
	}

	/** Function to get the Contacts assigned to a user with a valid email address.
	* @param string $username - User Name
	* @param string $emailaddress - Email Addr for each contact.
	* Used By vtigerCRM Outlook Plugin
	* Returns the Query
	*/
	public function get_searchbyemailid($username, $emailaddress) {
		global $log, $current_user, $adb;
		require_once 'modules/Users/Users.php';
		$seed_user=new Users();
		$user_id=$seed_user->retrieve_user_id($username);
		$current_user=$seed_user;
		$current_user->retrieve_entity_info($user_id, 'Users');
		$log->debug('Entering get_searchbyemailid('.$username.','.$emailaddress.') method ...');
		//get users group ID's
		$gresult = $adb->pquery('SELECT groupid FROM vtiger_users2group WHERE userid=?', array($user_id));
		$groupidlist = '';
		for ($j=0; $j < $adb->num_rows($gresult); $j++) {
			$groupidlist .= ','.$adb->query_result($gresult, $j, 'groupid');
		}
		//crm-now changed query to search in groups too and make only owned contacts available
		$query = 'select vtiger_contactdetails.lastname,vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_contactdetails.salutation,
				vtiger_contactdetails.email,vtiger_contactdetails.title, vtiger_contactdetails.mobile,vtiger_account.accountname,
				vtiger_account.accountid as accountid
			from vtiger_contactdetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
			inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
			left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid';
		$query .= getNonAdminAccessControlQuery('Contacts', $current_user);
		$query .= 'where vtiger_crmentity.deleted=0';
		if (trim($emailaddress) != '') {
			$query .= " and ((vtiger_contactdetails.email like '". formatForSqlLike($emailaddress)."') or vtiger_contactdetails.lastname REGEXP REPLACE('".$emailaddress
				."',' ','|') or vtiger_contactdetails.firstname REGEXP REPLACE('".$emailaddress."',' ','|')) and vtiger_contactdetails.email != ''";
		} else {
			$query .= " and (vtiger_contactdetails.email like '". formatForSqlLike($emailaddress) . "' and vtiger_contactdetails.email != '')";
			if (!empty($groupidlist)) {
				$query .= " and (vtiger_users.user_name='".$username."' OR vtiger_crmentity.smownerid IN (".substr($groupidlist, 1).'))';
			} else {
				$query .= " and vtiger_users.user_name='".$username."'";
			}
		}

		$log->debug('Exiting get_searchbyemailid method ...');
		return $this->plugin_process_list_query($query);
	}

	/** Function to get the Contacts associated with the particular User Name.
	*  @param varchar $user_name - User Name
	*  Returns query
	*/
	public function get_contactsforol($user_name) {
		global $log,$adb, $current_user;
		require_once 'modules/Users/Users.php';
		$seed_user=new Users();
		$user_id=$seed_user->retrieve_user_id($user_name);
		$current_user=$seed_user;
		$current_user->retrieve_entity_info($user_id, 'Users');
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';

		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = 'select tablename,columnname from vtiger_field where tabid=4 and vtiger_field.presence in (0,2)';
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'select tablename,columnname
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=4 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0
					and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
				$params1[] = $profileList;
			}
		}
		$result1 = $adb->pquery($sql1, $params1);
		for ($i=0; $i < $adb->num_rows($result1); $i++) {
			$permitted_lists[] = $adb->query_result($result1, $i, 'tablename');
			$permitted_lists[] = $adb->query_result($result1, $i, 'columnname');
			if ($adb->query_result($result1, $i, 'columnname') == 'accountid') {
				$permitted_lists[] = 'vtiger_account';
				$permitted_lists[] = 'accountname';
			}
		}
		$permitted_lists = array_chunk($permitted_lists, 2);
		$column_table_lists = array();
		for ($i=0; $i < count($permitted_lists); $i++) {
			$column_table_lists[] = implode('.', $permitted_lists[$i]);
		}

		$log->debug('Entering get_contactsforol('.$user_name.') method ...');
		$query = 'select vtiger_contactdetails.contactid as id, '.implode(',', $column_table_lists)
			." from vtiger_contactdetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
			inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			left join vtiger_customerdetails on vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
			left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
			left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
			left join vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid
			left join vtiger_campaigncontrel on vtiger_contactdetails.contactid = vtiger_campaigncontrel.contactid
			left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaigncontrel.campaignrelstatusid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_users.user_name='".$user_name."'";
		$log->debug('Exiting get_contactsforol method ...');
		return $query;
	}

	/** Function to handle module specific operations when saving a entity */
	public function save_module($module) {
		$this->insertIntoAttachment($this->id, $module);
	}

	/* Validate values trying to be saved.
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @return array
	 *   saveerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 *   error_action: action to redirect to inside the same module in case of error. if redirected to EditView (default action)
	 *                 all values introduced by the user will be preloaded
	 */
	public function preSaveCheck($request) {
		global $adb;
		$saveerror = false;
		$errmsg = '';
		if ($_REQUEST['action'] != 'ContactsAjax' && !empty($_FILES)) {
			$upload_file_path = decideFilePath();
			$dirpermission = is_writable($upload_file_path);
			$upload = is_uploaded_file($_FILES['imagename']['tmp_name']);
			$ferror = (isset($_FILES['error']) ? $_FILES['error'] : $_FILES['imagename']['error']);
			if ((!$dirpermission && ($this->mode=='' || ($this->mode!='' && $upload))) || ($ferror!=0 && $ferror!=4) || (!$upload && $ferror!=4)) {
				$saveerror = true;
				if ($ferror == 2) {
					$errmsg = getTranslatedString('LBL_MAXIMUM_LIMIT_ERROR', 'Contacts');
				} elseif ($ferror == 3) {
					$errmsg = getTranslatedString('LBL_UPLOAD_ERROR', 'Contacts');
				} else {
					$errmsg = getTranslatedString('LBL_FILEUPLOAD_FAILED', 'Documents');
				}
			}
		}
		if ($saveerror) {
			return array($saveerror, $errmsg, 'EditView', '');
		} else {
			return parent::preSaveCheck($request);
		}
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	public function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$rel_table_arr = array(
			'Potentials'=>'vtiger_contpotentialrel','Activities'=>'vtiger_cntactivityrel','Emails'=>'vtiger_seactivityrel',
			'HelpDesk'=>'vtiger_troubletickets','Quotes'=>'vtiger_quotes','PurchaseOrder'=>'vtiger_purchaseorder',
			'SalesOrder'=>'vtiger_salesorder','Products'=>'vtiger_seproductsrel',
			'Attachments'=>'vtiger_seattachmentsrel','Campaigns'=>'vtiger_campaigncontrel','cbCalendar'=>'vtiger_activity'
		);

		$tbl_field_arr = array(
			'vtiger_contpotentialrel'=>'potentialid','vtiger_cntactivityrel'=>'activityid','vtiger_seactivityrel'=>'activityid',
			'vtiger_troubletickets'=>'ticketid','vtiger_quotes'=>'quoteid','vtiger_purchaseorder'=>'purchaseorderid',
			'vtiger_salesorder'=>'salesorderid','vtiger_seproductsrel'=>'productid',
			'vtiger_seattachmentsrel'=>'attachmentsid','vtiger_campaigncontrel'=>'campaignid','vtiger_activity'=>'activityid'
		);

		$entity_tbl_field_arr = array(
			'vtiger_contpotentialrel'=>'contactid','vtiger_cntactivityrel'=>'contactid','vtiger_seactivityrel'=>'crmid',
			'vtiger_troubletickets'=>'parent_id','vtiger_quotes'=>'contactid','vtiger_purchaseorder'=>'contactid',
			'vtiger_salesorder'=>'contactid','vtiger_seproductsrel'=>'crmid',
			'vtiger_seattachmentsrel'=>'crmid','vtiger_campaigncontrel'=>'contactid','vtiger_activity'=>'cto_id'
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
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?", array($entityId, $transferId, $id_field_value));
					}
				}
			}
			// direct relation with potentials
			$adb->pquery('UPDATE vtiger_potential SET related_to = ? WHERE related_to = ?', array($entityId, $transferId));
		}
		$log->debug('Exiting transferRelatedRecords...');
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryplanner, $type = '', $where_condition = '') {
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryplanner, $type, $where_condition);
		if ($queryplanner->requireTable('vtiger_contactdetailsContacts')) {
			$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto';
		}
		if ($queryplanner->requireTable('vtiger_contactaddress')) {
			$query .= ' left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid';
		}
		if ($queryplanner->requireTable('vtiger_customerdetails')) {
			$query .= ' left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid';
		}
		if ($queryplanner->requireTable('vtiger_contactsubdetails')) {
			$query .= ' left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid';
		}
		if ($queryplanner->requireTable('vtiger_accountContacts')) {
			$query .= ' left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid';
		}
		if ($queryplanner->requireTable('vtiger_email_trackContacts')) {
			$query .= ' LEFT JOIN vtiger_email_track AS vtiger_email_trackContacts ON vtiger_email_trackContacts.crmid = vtiger_contactdetails.contactid';
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
			'Calendar' => array('vtiger_cntactivityrel'=>array('contactid','activityid'),'vtiger_contactdetails'=>'contactid'),
			'HelpDesk' => array('vtiger_troubletickets'=>array('parent_id','ticketid'),'vtiger_contactdetails'=>'contactid'),
			'Quotes' => array('vtiger_quotes'=>array('contactid','quoteid'),'vtiger_contactdetails'=>'contactid'),
			'PurchaseOrder' => array('vtiger_purchaseorder'=>array('contactid','purchaseorderid'),'vtiger_contactdetails'=>'contactid'),
			'SalesOrder' => array('vtiger_salesorder'=>array('contactid','salesorderid'),'vtiger_contactdetails'=>'contactid'),
			'Products' => array('vtiger_seproductsrel'=>array('crmid','productid'),'vtiger_contactdetails'=>'contactid'),
			'Campaigns' => array('vtiger_campaigncontrel'=>array('contactid','campaignid'),'vtiger_contactdetails'=>'contactid'),
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_contactdetails'=>'contactid'),
			'Accounts' => array('vtiger_contactdetails'=>array('contactid','accountid')),
			'Invoice' => array('vtiger_invoice'=>array('contactid','invoiceid'),'vtiger_contactdetails'=>'contactid'),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
		//Deleting Contact related Potentials.
		$pot_q = 'SELECT vtiger_crmentity.crmid
			FROM vtiger_crmentity
			INNER JOIN vtiger_potential ON vtiger_crmentity.crmid=vtiger_potential.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_potential.related_to
			WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.related_to=?';
		$pot_res = $this->db->pquery($pot_q, array($id));
		$pot_ids_list = array();
		for ($k=0; $k < $this->db->num_rows($pot_res); $k++) {
			$pot_id = $this->db->query_result($pot_res, $k, 'crmid');
			$pot_ids_list[] = $pot_id;
			$sql = 'UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($pot_id));
		}
		//Backup deleted Contact related Potentials.
		$params = array($id, RB_RECORD_UPDATED, 'vtiger_crmentity', 'deleted', 'crmid', implode(',', $pot_ids_list));
		$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Backup Contact-Trouble Tickets Relation
		$tkt_q = 'SELECT ticketid FROM vtiger_troubletickets WHERE parent_id=?';
		$tkt_res = $this->db->pquery($tkt_q, array($id));
		if ($this->db->num_rows($tkt_res) > 0) {
			$tkt_ids_list = array();
			for ($k=0; $k < $this->db->num_rows($tkt_res); $k++) {
				$tkt_ids_list[] = $this->db->query_result($tkt_res, $k, 'ticketid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_troubletickets', 'parent_id', 'ticketid', implode(',', $tkt_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Trouble Tickets
		$this->db->pquery('UPDATE vtiger_troubletickets SET parent_id=0 WHERE parent_id=?', array($id));

		//Backup Contact-PurchaseOrder Relation
		$po_q = 'SELECT purchaseorderid FROM vtiger_purchaseorder WHERE contactid=?';
		$po_res = $this->db->pquery($po_q, array($id));
		if ($this->db->num_rows($po_res) > 0) {
			$po_ids_list = array();
			for ($k=0; $k < $this->db->num_rows($po_res); $k++) {
				$po_ids_list[] = $this->db->query_result($po_res, $k, 'purchaseorderid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_purchaseorder', 'contactid', 'purchaseorderid', implode(',', $po_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with PurchaseOrder
		$this->db->pquery('UPDATE vtiger_purchaseorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-SalesOrder Relation
		$so_q = 'SELECT salesorderid FROM vtiger_salesorder WHERE contactid=?';
		$so_res = $this->db->pquery($so_q, array($id));
		if ($this->db->num_rows($so_res) > 0) {
			$so_ids_list = array();
			for ($k=0; $k < $this->db->num_rows($so_res); $k++) {
				$so_ids_list[] = $this->db->query_result($so_res, $k, 'salesorderid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_salesorder', 'contactid', 'salesorderid', implode(',', $so_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with SalesOrder
		$this->db->pquery('UPDATE vtiger_salesorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-Quotes Relation
		$quo_res = $this->db->pquery('SELECT quoteid FROM vtiger_quotes WHERE contactid=?', array($id));
		if ($this->db->num_rows($quo_res) > 0) {
			$quo_ids_list = array();
			for ($k=0; $k < $this->db->num_rows($quo_res); $k++) {
				$quo_ids_list[] = $this->db->query_result($quo_res, $k, 'quoteid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_quotes', 'contactid', 'quoteid', implode(',', $quo_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Quotes
		$this->db->pquery('UPDATE vtiger_quotes SET contactid=0 WHERE contactid=?', array($id));
		//remove the portal info the contact
		$this->db->pquery('DELETE FROM vtiger_portalinfo WHERE id = ?', array($id));
		$this->db->pquery('UPDATE vtiger_customerdetails SET portal=0,support_start_date=NULL,support_end_date=NULl WHERE customerid=?', array($id));
		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}

		if ($return_module == 'Accounts') {
			$this->db->pquery('UPDATE vtiger_contactdetails SET accountid = ? WHERE contactid = ?', array(null, $id));
		} elseif ($return_module == 'Potentials') {
			$this->db->pquery('DELETE FROM vtiger_contpotentialrel WHERE contactid=? AND potentialid=?', array($id, $return_id));
		} elseif ($return_module == 'Campaigns') {
			$this->db->pquery('DELETE FROM vtiger_campaigncontrel WHERE contactid=? AND campaignid=?', array($id, $return_id));
		} elseif ($return_module == 'Products') {
			$this->db->pquery('DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?', array($id, $return_id));
		} elseif ($return_module == 'Vendors') {
			$this->db->pquery('DELETE FROM vtiger_vendorcontactrel WHERE vendorid=? AND contactid=?', array($return_id, $id));
		} elseif ($return_module == 'Documents') {
			$this->db->pquery('DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?', array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	//added to get mail info for portal user
	//type argument included when when addin customizable tempalte for sending portal login details
	public static function getPortalEmailContents($entityData, $password, $type = '') {
		require_once 'config.inc.php';
		global $default_charset;

		$adb = PearDatabase::getInstance();
		$moduleName = $entityData->getModuleName();
		$PORTAL_URL = GlobalVariable::getVariable('Application_Customer_Portal_URL', 'http://your_support_domain.tld/customerportal');
		$portalURL = '<a href="'.$PORTAL_URL.'" style="font-family:Arial, Helvetica, sans-serif;font-size:12px; font-weight:bolder;text-decoration:none;color: #4242FD;">'
			.getTranslatedString('Please Login Here', $moduleName).'</a>';

		//here id is hardcoded with 5. it is for support start notification in vtiger_notificationscheduler
		$query='SELECT subject,body FROM vtiger_emailtemplates WHERE templateid=10';

		$result = $adb->pquery($query, array());
		$body=$adb->query_result($result, 0, 'body');
		$contents = html_entity_decode($body, ENT_QUOTES, $default_charset);
		$contents = str_replace('$contact_name$', $entityData->get('firstname').' '.$entityData->get('lastname'), $contents);
		$contents = str_replace('$login_name$', $entityData->get('email'), $contents);
		$contents = str_replace('$password$', $password, $contents);
		$contents = str_replace('$URL$', $portalURL, $contents);
		$contents = str_replace('$support_team$', getTranslatedString('Support Team', $moduleName), $contents);
		$contents = str_replace('$logo$', '<img src="cid:logo" />', $contents);
		$contents = getMergedDescription($contents, $entityData->getId(), 'Contacts');

		if ($type == 'LoginDetails') {
			$temp=$contents;
			$value['subject']=$adb->query_result($result, 0, 'subject');
			$value['body']=$temp;
			return $value;
		}
		return $contents;
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();
		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Products') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_seproductsrel WHERE productid = ? AND crmid = ?', array($with_crmid, $crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$adb->pquery('insert into vtiger_seproductsrel values (?,?,?)', array($crmid, $with_crmid, 'Contacts'));
			} elseif ($with_module == 'Campaigns') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaigncontrel WHERE campaignid = ? AND contacrid = ?', array($with_crmid, $crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$adb->pquery('insert into vtiger_campaigncontrel values(?,?,1)', array($with_crmid, $crmid));
			} elseif ($with_module == 'Potentials') {
				$adb->pquery('insert ignore into vtiger_contpotentialrel values(?,?)', array($crmid, $with_crmid));
			} elseif ($with_module == 'Vendors') {
				$adb->pquery('insert ignore into vtiger_vendorcontactrel values (?,?)', array($with_crmid, $crmid));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	public function getListButtons($app_strings) {
		$list_buttons = array();

		if (isPermitted('Contacts', 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}
		if (isPermitted('Contacts', 'EditView', '') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings['LBL_MASS_EDIT'];
		}
		return $list_buttons;
	}

//////////////////////////////////////////////////////////////////////////
// pag 2012-Jan-18 contacts hierarchy deducted from accounts hierarchy  //
//////////////////////////////////////////////////////////////////////////
	/**
	* Function to get Contact hierarchy of the given Contact
	* @param  integer   $id      - contactid
	* returns Contact hierarchy in array format
	*/
	public function getContactHierarchy($id) {
		global $log, $current_user;
		$log->debug('Entering getContactHierarchy($id) method ...');
		$tabname = getParentTab();
		$listview_header = array();
		$listview_entries = array();

		foreach ($this->list_fields_name as $fieldname => $colname) {
			if (getFieldVisibilityPermission('Contacts', $current_user->id, $colname) == '0') {
				$listview_header[] = getTranslatedString($fieldname);
			}
		}

		$contacts_list = array();

		// Get the contacts hierarchy from the top most contact in the hierarchy of the current contact, including the current contact
		$encountered_contacts = array($id);

		$contacts_list = $this->__getParentContacts($id, $contacts_list, $encountered_contacts);

		// Get the contacts hierarchy (list of child contacts) based on the current contact
		$contacts_list = $this->__getChildContacts($id, $contacts_list, $contacts_list[$id]['depth']);

		// Create array of all the contacts in the hierarchy
		foreach ($contacts_list as $contact_id => $contact_info) {
			$contact_info_data = array();
			$hasRecordViewAccess = (is_admin($current_user)) || (isPermitted('Contacts', 'DetailView', $contact_id) == 'yes');
			foreach ($this->list_fields_name as $fieldname => $colname) {
				// Permission to view contact is restricted, avoid showing field values (except contact name)
				if (!$hasRecordViewAccess && $colname != 'lastname') {
					$contact_info_data[] = '';
				} elseif (getFieldVisibilityPermission('Contacts', $current_user->id, $colname) == '0') {
					$data = $contact_info[$colname];
					if ($colname == 'lastname') {
						if ($contact_id != $id) {
							if ($hasRecordViewAccess) {
								$data = '<a href="index.php?module=Contacts&action=DetailView&record='.$contact_id.'&parenttab='.$tabname.'">'.$data.'</a>';
							} else {
								$data = '<i>'.$data.'</i>';
							}
						} else {
							$data = '<b>'.$data.'</b>';
						}
						// - to show the hierarchy of the Contacts
						$contact_depth = str_repeat(' .. ', $contact_info['depth'] * 1); // * 2
						$data = $contact_depth . $data;
					} elseif ($colname == 'website') {
						$data = '<a href="http://'. $data .'" target="_blank">'.$data.'</a>';
					}
					$contact_info_data[] = $data;
				}
			}
			$listview_entries[$contact_id] = $contact_info_data;
		}
		$contact_hierarchy = array('header'=>$listview_header,'entries'=>$listview_entries);
		$log->debug('Exiting getContactHierarchy method ...');
		return $contact_hierarchy;
	}

	/**
	* Function to Recursively get all the upper contacts of a given Contact
	* @param  integer   $id            - contactid
	* @param  array   $parent_contacts - Array of all the parent contacts
	* returns All the parent contacts of the given contactid in array format
	*/
	public function __getParentContacts($id, &$parent_contacts, &$encountered_contacts) {
		global $log, $adb;
		$log->debug("Entering __getParentContacts($id, ".print_r($parent_contacts, true).') method ...');
		$query = 'SELECT reportsto FROM vtiger_contactdetails '
			.' INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid'
			.' WHERE vtiger_crmentity.deleted = 0 and vtiger_contactdetails.contactid = ?';
		$params = array($id);
		$res = $adb->pquery($query, $params);

		if ($adb->num_rows($res) > 0 && $adb->query_result($res, 0, 'reportsto') != '' && $adb->query_result($res, 0, 'reportsto') != 0
			&& !in_array($adb->query_result($res, 0, 'reportsto'), $encountered_contacts)
		) {
			$parentid = $adb->query_result($res, 0, 'reportsto');
			$encountered_contacts[] = $parentid;
			$this->__getParentContacts($parentid, $parent_contacts, $encountered_contacts);
		}
		$query = 'SELECT vtiger_contactdetails.*, ' .
			" CASE when (vtiger_users.user_name not like '') THEN vtiger_users.user_name ELSE vtiger_groups.groupname END as user_name " .
			' FROM vtiger_contactdetails' .
			' INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid' .
			' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid' .
			' LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid' .
			' WHERE vtiger_crmentity.deleted = 0 and vtiger_contactdetails.contactid = ?';
		$params = array($id);
		$res = $adb->pquery($query, $params);
		$parent_contact_info = array();
		$depth = 0;
		$immediate_parentid = $adb->query_result($res, 0, 'reportsto');
		if (isset($parent_contacts[$immediate_parentid])) {
			$depth = $parent_contacts[$immediate_parentid]['depth'] + 1;
		}
		$parent_contact_info['depth'] = $depth;
		foreach ($this->list_fields_name as $columnname) {
			if ($columnname == 'account_id') {
				$accountid = $adb->query_result($res, 0, 'accountid');
				$accountname = getAccountName($accountid);
				$parent_contact_info[$columnname] = '<a href="index.php?module=Accounts&action=DetailView&record='.$accountid.'">'.$accountname.'</a>';
			} else {
				if ($columnname == 'assigned_user_id') {
					$parent_contact_info[$columnname] = $adb->query_result($res, 0, 'user_name');
				} else {
					$parent_contact_info[$columnname] = $adb->query_result($res, 0, $columnname);
				}
			}
		}
		$parent_contacts[$id] = $parent_contact_info;
		$log->debug('Exiting __getParentContacts method ...');
		return $parent_contacts;
	}

	/**
	* Function to Recursively get all the child contacts of a given Contact
	* @param  integer   $id           - contactid
	* @param  array   $child_contacts - Array of all the child contacts
	* @param  integer   $depth        - Depth at which the particular contact has to be placed in the hierarchy
	* returns All the child contacts of the given contactid in array format
	*/
	public function __getChildContacts($id, &$child_contacts, $depth) {
		global $log, $adb;
		$log->debug("Entering __getChildContacts($id,".print_r($child_contacts, true).",$depth) method ...");
		$query = 'SELECT vtiger_contactdetails.*, '
			." CASE when (vtiger_users.user_name not like '') THEN vtiger_users.user_name ELSE vtiger_groups.groupname END as user_name "
			.' FROM vtiger_contactdetails'
			.' INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid'
			.' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid'
			.' LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid'
			.' WHERE vtiger_crmentity.deleted = 0 and reportsto = ?';
		$params = array($id);
		$res = $adb->pquery($query, $params);
		$num_rows = $adb->num_rows($res);
		if ($num_rows > 0) {
			$depth = $depth + 1;
			for ($i=0; $i<$num_rows; $i++) {
				$child_acc_id = $adb->query_result($res, $i, 'contactid');
				if (array_key_exists($child_acc_id, $child_contacts)) {
					continue;
				}
				$child_contact_info = array();
				$child_contact_info['depth'] = $depth;
				foreach ($this->list_fields_name as $columnname) {
					if ($columnname == 'account_id') {
						$accountid = $adb->query_result($res, $i, 'accountid');
						$accountname = getAccountName($accountid);
						$child_contact_info[$columnname] = '<a href="index.php?module=Accounts&action=DetailView&record='.$accountid.'">'.$accountname.'</a>';
					} else {
						if ($columnname == 'assigned_user_id') {
							$child_contact_info[$columnname] = $adb->query_result($res, $i, 'user_name');
						} else {
							$child_contact_info[$columnname] = $adb->query_result($res, $i, $columnname);
						}
					}
				}
				$child_contacts[$child_acc_id] = $child_contact_info;
				$this->__getChildContacts($child_acc_id, $child_contacts, $depth);
			}
		}
		$log->debug('Exiting __getChildContacts method ...');
		return $child_contacts;
	}
//////////////////////////////////////////////////////////////////////////////
// END pag 2012-Jan-18 contacts hierarchy deducted from accounts hierarchy  //
//////////////////////////////////////////////////////////////////////////////

	public function getvtlib_open_popup_window_function($fieldname, $basemodule) {
		if ($basemodule=='Issuecards') {
			return 'set_return_shipbilladdress';
		} elseif ($fieldname == 'cto_id' && $basemodule == 'cbCalendar') {
			return 'open_filtered_contactsIfAccounts';
		} else {
			return 'vtlib_open_popup_window';
		}
	}
}
?>
