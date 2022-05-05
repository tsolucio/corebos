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
require_once 'modules/Campaigns/Campaigns.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Emails/Emails.php';
require_once 'modules/HelpDesk/HelpDesk.php';
require 'modules/Vtiger/default_module_view.php';

class Contacts extends CRMEntity {
	public $table_name = 'vtiger_contactdetails';
	public $table_index= 'contactid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = true;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-contact', 'class' => 'slds-icon', 'icon'=>'contact');

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
	*  @param string assigned to user name
	*  @return integer the count of contacts assigned to user
	*/
	public function getCount($user_name) {
		global $log, $adb;
		$log->debug('> getCount '.$user_name);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = 'select count(*)
			from vtiger_contactdetails
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
			inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where user_name=? and vtiger_crmentity.deleted=0';
		$result = $adb->pquery($query, array($user_name), true, 'Error retrieving contacts count');
		$log->debug('< getCount');
		return $adb->query_result($result, 0, 0);
	}

	/** Function to process list query for Plugin with Security Parameters for a given query
	 *  @param string query
	 *  @return array results of query
	 */
	public function plugin_process_list_query($query) {
		global $log, $adb, $current_user,$currentModule;
		$log->debug('> plugin_process_list_query '.$query);
		$permitted_field_lists = array();
		$userprivs = $current_user->getPrivileges();
		if ($userprivs->hasGlobalReadPermission()) {
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
		$result1 = $adb->pquery($sql1, $params1);
		for ($i = 0; $i < $adb->num_rows($result1); $i++) {
			$permitted_field_lists[] = $adb->query_result($result1, $i, 'columnname');
		}

		$result = &$adb->query($query, true, "Error retrieving $currentModule list: ");
		$list = array();
		$rows_found = $adb->getRowCount($result);
		if ($rows_found != 0) {
			for ($index = 0, $row = $adb->fetchByAssoc($result, $index); $row && $index < $rows_found; $index++, $row = $adb->fetchByAssoc($result, $index)) {
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
				$contact['contactid'] = $row['contactid'];
				$list[] = $contact;
			}
		}

		$response = array();
		$response['list'] = $list;
		$response['row_count'] = $rows_found;
		$log->debug('< plugin_process_list_query');
		return $response;
	}

	/** Returns a list of the associated opportunities */
	public function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule, $adb;
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' ".
					"onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable".
					"&form=EditView&form_submit=false&recordid=$id', 'test', cbPopupWindowSettings);\" value='".
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
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Potentials');
		$query ='select case when (vtiger_users.user_name not like "") then vtiger_users.ename else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*,vtiger_contactdetails.accountid,vtiger_contactdetails.contactid,vtiger_potential.*,vtiger_potentialscf.*,vtiger_account.accountname
			from vtiger_contactdetails
			left join vtiger_contpotentialrel on vtiger_contpotentialrel.contactid=vtiger_contactdetails.contactid
			left join vtiger_potential on (vtiger_potential.potentialid = vtiger_contpotentialrel.potentialid or
				vtiger_potential.related_to = vtiger_contactdetails.contactid or
				vtiger_potential.related_to = vtiger_contactdetails.accountid)
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_potential.potentialid
			left JOIN vtiger_potentialscf ON vtiger_potential.potentialid = vtiger_potentialscf.potentialid
			left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where vtiger_contactdetails.contactid ='.$id.' and vtiger_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_opportunities');
		return $return_value;
	}

	 /**
	 * Function to get Contact related Products
	 * @param integer contact id
	 * @return array related Products record
	 */
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
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

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					."cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Products');
		$query = 'SELECT vtiger_products.*,vtiger_productcf.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,vtiger_contactdetails.lastname
			FROM vtiger_products
			INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.productid=vtiger_products.productid and vtiger_seproductsrel.setype="Contacts"
			INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
			INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_products.productid
			INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seproductsrel.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_contactdetails.contactid = '.$id.' and vtiger_crmentity.deleted = 0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_products');
		return $return_value;
	}

	/**
	 * Function to get Contact related PurchaseOrder
	 * @param integer contact id
	 * @return array related PurchaseOrder record
	 */
	public function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					."cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('PurchaseOrder');
		$query = "select case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name,vtiger_crmentity.*,
				vtiger_purchaseorder.*,vtiger_purchaseordercf.*,vtiger_vendor.vendorname,vtiger_contactdetails.lastname
			from vtiger_purchaseorder
			inner join $crmEntityTable on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid
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

		$log->debug('< get_purchase_orders');
		return $return_value;
	}

	/** Returns a list of the associated Campaigns
	 * @param integer campaign id
	 * @return array list of campaigns
	 */
	public function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_campaigns '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

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
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					."cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW').' '. $singular_modname
					."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,".'"sendmail_cont");sendmail("'.$this_module.'",'.$id
					.");' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW').' '. $singular_modname."'></td>";
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Campaigns');
		$query = "SELECT case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name,
				vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus,
				vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime
			from vtiger_campaign
			inner join vtiger_campaigncontrel on vtiger_campaigncontrel.campaignid=vtiger_campaign.campaignid
			inner join $crmEntityTable on vtiger_crmentity.crmid = vtiger_campaign.campaignid
			inner join vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			where vtiger_campaigncontrel.contactid=".$id.' and vtiger_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_campaigns');
		return $return_value;
	}

	/**
	* Function to get Contact related Invoices
	* @param integer contact id
	* @return array related Invoices record
	*/
	public function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					."cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Invoice');
		$query = "SELECT case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name,
			vtiger_crmentity.*, vtiger_invoice.*, vtiger_contactdetails.lastname,vtiger_contactdetails.firstname, vtiger_salesorder.subject AS salessubject
			FROM vtiger_invoice
			INNER JOIN ".$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			LEFT OUTER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_invoice.contactid
			LEFT OUTER JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_contactdetails.contactid='.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_invoices');
		return $return_value;
	}

	/**
	* Function to get Contact related vendors.
	* @param integer contact id
	* @return array related vendor records
	*/
	public function get_vendors($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug('> get_vendors '.$id);
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
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',"
					."cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' '
					. getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>&nbsp;";
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Vendors');
		$query = "SELECT case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name,
			vtiger_crmentity.crmid, vtiger_vendor.*, vtiger_vendorcf.*
			from vtiger_vendor inner join ".$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_vendor.vendorid
			INNER JOIN vtiger_vendorcontactrel on vtiger_vendorcontactrel.vendorid=vtiger_vendor.vendorid
			LEFT JOIN vtiger_vendorcf on vtiger_vendorcf.vendorid=vtiger_vendor.vendorid
			LEFT JOIN vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0 and vtiger_vendorcontactrel.contactid='.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_vendors');
		return $return_value;
	}

	/** Function to export the contact records in CSV Format
	* @param string reference variable - where condition is passed when the query is executed
	* @return string Export Contacts Query
	*/
	public function create_export_query($where) {
		global $log, $current_user, $adb;
		$log->debug('> create_export_query '.$where);

		include_once 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('Contacts', 'detail_view');
		$fields_list = getFieldsListFromQuery($sql);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = "SELECT vtiger_contactdetails.salutation as 'Salutation',$fields_list,
				case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			FROM vtiger_contactdetails
			inner join ".$crmEntityTable." on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.status='Active'
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_account on vtiger_contactdetails.accountid=vtiger_account.accountid
			left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid=vtiger_contactdetails.contactid
			left join vtiger_contactscf on vtiger_contactscf.contactid=vtiger_contactdetails.contactid
			left join vtiger_customerdetails on vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_contactdetails vtiger_contactdetails2 ON vtiger_contactdetails2.contactid = vtiger_contactdetails.reportsto";
		include_once 'include/fields/metainformation.php';
		$tabid = getTabid('Contacts');
		$result = $adb->pquery('select tablename, fieldname, columnname from vtiger_field where tabid=? and uitype=?', array($tabid, Field_Metadata::UITYPE_ACTIVE_USERS));
		while ($row = $adb->fetchByAssoc($result)) {
			$query .= ' LEFT JOIN vtiger_users as vtiger_users'.$row['fieldname'].' ON vtiger_users'.$row['fieldname'].'.id='.$row['tablename'].'.'.$row['columnname'];
		}
		$query .= getNonAdminAccessControlQuery('Contacts', $current_user);
		$where_auto = ' vtiger_crmentity.deleted = 0 ';
		if ($where != '') {
			$query .= " WHERE ($where) AND ".$where_auto;
		} else {
			$query .= ' WHERE '.$where_auto;
		}
		$log->debug('< create_export_query');
		return $query;
	}

	/** Function to get the Columnnames of the Contacts
	* Used By vtigerCRM Word Plugin
	* Returns the Merge Fields for Word Plugin
	*/
	public function getColumnNames() {
		global $log, $current_user, $adb;
		$log->debug('> getColumnNames');
		$userprivs = $current_user->getPrivileges();
		if ($userprivs->hasGlobalReadPermission()) {
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
		$result = $adb->pquery($sql1, $params1);
		$numRows = $adb->num_rows($result);
		$custom_fields = array();
		for ($i=0; $i < $numRows; $i++) {
			$custom_fields[$i] = $adb->query_result($result, $i, 'fieldlabel');
			$custom_fields[$i] = preg_replace('/\s+/', '', $custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug('< getColumnNames');
		return $mergeflds;
	}

	/** Function to get the Contacts assigned to a user with a valid email address.
	* @param string User Name
	* @param string Email Address for each contact
	* Used By vtigerCRM Outlook Plugin
	* @return string query
	*/
	public function get_searchbyemailid($username, $emailaddress) {
		global $log, $current_user, $adb;
		require_once 'modules/Users/Users.php';
		$seed_user=new Users();
		$user_id=$seed_user->retrieve_user_id($username);
		$current_user=$seed_user;
		$current_user->retrieve_entity_info($user_id, 'Users');
		$log->debug('> Contact:get_searchbyemailid '.$username.','.$emailaddress);
		//get users group ID's
		$gresult = $adb->pquery('SELECT groupid FROM vtiger_users2group WHERE userid=?', array($user_id));
		$groupidlist = '';
		for ($j=0; $j < $adb->num_rows($gresult); $j++) {
			$groupidlist .= ','.$adb->query_result($gresult, $j, 'groupid');
		}
		//crm-now changed query to search in groups too and make only owned contacts available
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = 'select vtiger_contactdetails.lastname,vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_contactdetails.salutation,
				vtiger_contactdetails.email,vtiger_contactdetails.title, vtiger_contactdetails.mobile,vtiger_account.accountname,
				vtiger_account.accountid as accountid
			from vtiger_contactdetails
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
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

		$log->debug('< get_searchbyemailid');
		return $this->plugin_process_list_query($query);
	}

	/** Function to get the Contacts associated with the particular User Name.
	*  @param string User Name
	*  @return string query
	*/
	public function get_contactsforol($user_name) {
		global $log,$adb, $current_user;
		require_once 'modules/Users/Users.php';
		$seed_user=new Users();
		$user_id=$seed_user->retrieve_user_id($user_name);
		$current_user=$seed_user;
		$current_user->retrieve_entity_info($user_id, 'Users');
		$userprivs = $current_user->getPrivileges();

		if ($userprivs->hasGlobalReadPermission()) {
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

		$log->debug('> get_contactsforol '.$user_name);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = 'select vtiger_contactdetails.contactid as id, '.implode(',', $column_table_lists)
			." from vtiger_contactdetails
			inner join ".$crmEntityTable." on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
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
		$log->debug('< get_contactsforol');
		return $query;
	}

	public function save($module, $fileid = '') {
		global $adb;
		if ($this->mode=='edit') {
			$rs = $adb->pquery('select convertedfromlead from vtiger_contactdetails where contactid = ?', array($this->id));
			$this->column_fields['convertedfromlead'] = $adb->query_result($rs, 0, 'convertedfromlead');
		}
		parent::save($module, $fileid);
	}

	/** Function to handle module specific operations when saving a entity */
	public function save_module($module) {
		$this->insertIntoAttachment($this->id, $module);
	}

	/** Validate values trying to be saved
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @return array
	 *   saveerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 *   error_action: action to redirect to inside the same module in case of error. if redirected to EditView (default action)
	 *                 all values introduced by the user will be preloaded
	 */
	public function preSaveCheck($request) {
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
	 * @param string This module name
	 * @param array List of Entity Id's from which related records need to be transfered
	 * @param integer Id of the the Record to which the related records are to be moved
	 */
	public function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb, $log;
		$log->debug('> transferRelatedRecords', ['module' => $module, 'transferEntityIds' => $transferEntityIds, 'entityId' => $entityId]);
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$rel_table_arr = array(
			'Potentials'=>'vtiger_contpotentialrel',
			'Activities'=>'vtiger_cntactivityrel',
			'Emails'=>'vtiger_seactivityrel',
			'Quotes'=>'vtiger_quotes',
			'PurchaseOrder'=>'vtiger_purchaseorder',
			'SalesOrder'=>'vtiger_salesorder',
			'Products'=>'vtiger_seproductsrel',
			'Attachments'=>'vtiger_seattachmentsrel',
			'Campaigns'=>'vtiger_campaigncontrel',
		);
		$tbl_field_arr = array(
			'vtiger_contpotentialrel'=>'potentialid',
			'vtiger_cntactivityrel'=>'activityid',
			'vtiger_seactivityrel'=>'activityid',
			'vtiger_quotes'=>'quoteid',
			'vtiger_purchaseorder'=>'purchaseorderid',
			'vtiger_salesorder'=>'salesorderid',
			'vtiger_seproductsrel'=>'productid',
			'vtiger_seattachmentsrel'=>'attachmentsid',
			'vtiger_campaigncontrel'=>'campaignid',
		);
		$entity_tbl_field_arr = array(
			'vtiger_contpotentialrel'=>'contactid',
			'vtiger_cntactivityrel'=>'contactid',
			'vtiger_seactivityrel'=>'crmid',
			'vtiger_quotes'=>'contactid',
			'vtiger_purchaseorder'=>'contactid',
			'vtiger_salesorder'=>'contactid',
			'vtiger_seproductsrel'=>'crmid',
			'vtiger_seattachmentsrel'=>'crmid',
			'vtiger_campaigncontrel'=>'contactid',
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
		$log->debug('< transferRelatedRecords');
	}

	/**
	 * Function to get the secondary query part of a report
	 * @param string primary module name
	 * @param string secondary module name
	 * @return string the query string formed on fetching the related data for report for secondary module
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

	/**
	 * Function to get the relation tables for related modules
	 * @param string secondary module name
	 * @return array with table names and fieldnames storing relations between module and this module
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
		global $adb;
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Potentials');
		$crmEntityTable1 = CRMEntity::getcrmEntityTableAlias('Potentials', true);
		$pot_q = 'SELECT vtiger_crmentity.crmid
			FROM '.$crmEntityTable.' 
			INNER JOIN vtiger_potential ON vtiger_crmentity.crmid=vtiger_potential.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_potential.related_to
			WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.related_to=?';
		$pot_res = $adb->pquery($pot_q, array($id));
		$pot_ids_list = array();
		for ($k=0; $k < $adb->num_rows($pot_res); $k++) {
			$pot_id = $adb->query_result($pot_res, $k, 'crmid');
			$pot_ids_list[] = $pot_id;
			$sql = 'UPDATE '.$crmEntityTable1.' SET deleted = 1 WHERE crmid = ?';
			$adb->pquery($sql, array($pot_id));
		}
		//Backup deleted Contact related Potentials.
		$params = array($id, RB_RECORD_UPDATED, $crmEntityTable1, 'deleted', 'crmid', implode(',', $pot_ids_list));
		$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Backup Contact-Trouble Tickets Relation
		$tkt_q = 'SELECT ticketid FROM vtiger_troubletickets WHERE parent_id=?';
		$tkt_res = $adb->pquery($tkt_q, array($id));
		if ($adb->num_rows($tkt_res) > 0) {
			$tkt_ids_list = array();
			for ($k=0; $k < $adb->num_rows($tkt_res); $k++) {
				$tkt_ids_list[] = $adb->query_result($tkt_res, $k, 'ticketid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_troubletickets', 'parent_id', 'ticketid', implode(',', $tkt_ids_list));
			$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Trouble Tickets
		$adb->pquery('UPDATE vtiger_troubletickets SET parent_id=0 WHERE parent_id=?', array($id));

		//Backup Contact-PurchaseOrder Relation
		$po_q = 'SELECT purchaseorderid FROM vtiger_purchaseorder WHERE contactid=?';
		$po_res = $adb->pquery($po_q, array($id));
		if ($adb->num_rows($po_res) > 0) {
			$po_ids_list = array();
			for ($k=0; $k < $adb->num_rows($po_res); $k++) {
				$po_ids_list[] = $adb->query_result($po_res, $k, 'purchaseorderid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_purchaseorder', 'contactid', 'purchaseorderid', implode(',', $po_ids_list));
			$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with PurchaseOrder
		$adb->pquery('UPDATE vtiger_purchaseorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-SalesOrder Relation
		$so_q = 'SELECT salesorderid FROM vtiger_salesorder WHERE contactid=?';
		$so_res = $adb->pquery($so_q, array($id));
		if ($adb->num_rows($so_res) > 0) {
			$so_ids_list = array();
			for ($k=0; $k < $adb->num_rows($so_res); $k++) {
				$so_ids_list[] = $adb->query_result($so_res, $k, 'salesorderid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_salesorder', 'contactid', 'salesorderid', implode(',', $so_ids_list));
			$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with SalesOrder
		$adb->pquery('UPDATE vtiger_salesorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-Quotes Relation
		$quo_res = $adb->pquery('SELECT quoteid FROM vtiger_quotes WHERE contactid=?', array($id));
		if ($adb->num_rows($quo_res) > 0) {
			$quo_ids_list = array();
			for ($k=0; $k < $adb->num_rows($quo_res); $k++) {
				$quo_ids_list[] = $adb->query_result($quo_res, $k, 'quoteid');
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_quotes', 'contactid', 'quoteid', implode(',', $quo_ids_list));
			$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Quotes
		$adb->pquery('UPDATE vtiger_quotes SET contactid=0 WHERE contactid=?', array($id));
		//remove the portal info the contact
		$adb->pquery('DELETE FROM vtiger_portalinfo WHERE id = ?', array($id));
		$adb->pquery('UPDATE vtiger_customerdetails SET portal=0,support_start_date=NULL,support_end_date=NULl WHERE customerid=?', array($id));
		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		$customRelModules = ['Accounts', 'Potentials', 'Campaigns', 'Products', 'Vendors', 'Documents'];
		if (in_array($return_module, $customRelModules)) {
			$data = array();
			$data['sourceModule'] = getSalesEntityType($id);
			$data['sourceRecordId'] = $id;
			$data['destinationModule'] = $return_module;
			$data['destinationRecordId'] = $return_id;
			cbEventHandler::do_action('corebos.entity.link.delete', $data);
		}
		if ($return_module == 'Accounts') {
			$adb->pquery('UPDATE vtiger_contactdetails SET accountid = ? WHERE contactid = ?', array(null, $id));
		} elseif ($return_module == 'Potentials') {
			$adb->pquery('DELETE FROM vtiger_contpotentialrel WHERE contactid=? AND potentialid=?', array($id, $return_id));
		} elseif ($return_module == 'Campaigns') {
			$adb->pquery('DELETE FROM vtiger_campaigncontrel WHERE contactid=? AND campaignid=?', array($id, $return_id));
		} elseif ($return_module == 'Products') {
			$adb->pquery('DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?', array($id, $return_id));
		} elseif ($return_module == 'Vendors') {
			$adb->pquery('DELETE FROM vtiger_vendorcontactrel WHERE vendorid=? AND contactid=?', array($return_id, $id));
		} elseif ($return_module == 'Documents') {
			$adb->pquery('DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?', array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
		if (in_array($return_module, $customRelModules)) {
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
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

		$result = $adb->pquery('SELECT subject,template FROM vtiger_msgtemplate WHERE reference=?', array('Customer Login Details'));
		if ($result && $adb->num_rows($result)>0) {
			$body=$adb->query_result($result, 0, 'template');
			$contents = html_entity_decode($body, ENT_QUOTES, $default_charset);
			$contents = str_replace('$contact_name$', $entityData->get('firstname').' '.$entityData->get('lastname'), $contents);
			$contents = str_replace('$login_name$', $entityData->get('email'), $contents);
			$contents = str_replace('$password$', $password, $contents);
			$contents = str_replace('$URL$', $portalURL, $contents);
			$contents = str_replace('$support_team$', getTranslatedString('Support Team', $moduleName), $contents);
			$contents = str_replace('$logo$', '<img src="cid:logo" />', $contents);
			$contents = getMergedDescription($contents, $entityData->getId(), 'Contacts');

			if ($type == 'LoginDetails') {
				$value['subject']=$adb->query_result($result, 0, 'subject');
				$value['body']=$contents;
				return $value;
			}
		} else {
			$contents = '';
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
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaigncontrel WHERE campaignid = ? AND contactid = ?', array($with_crmid, $crmid));
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

	/**
	* Function to get Contact hierarchy of the given Contact
	* @param integer contact id
	* @return array Contact hierarchy
	*/
	public function getContactHierarchy($id) {
		global $log, $current_user;
		$log->debug('> getContactHierarchy '.$id);
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
								$data = '<a href="index.php?module=Contacts&action=DetailView&record='.$contact_id.'">'.$data.'</a>';
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
		$log->debug('< getContactHierarchy');
		return $contact_hierarchy;
	}

	/**
	* Function to Recursively get all the upper contacts of a given Contact
	* @param integer contact id
	* @param array all the parent contacts
	* @return array all the parent contacts of the given contactid
	*/
	public function __getParentContacts($id, &$parent_contacts, &$encountered_contacts) {
		global $log, $adb;
		$log->debug('> __getParentContacts '.$id);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = 'SELECT reportsto FROM vtiger_contactdetails '
			.' INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid'
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
			' INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid' .
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
		$log->debug('< __getParentContacts');
		return $parent_contacts;
	}

	/**
	* Function to Recursively get all the child contacts of a given Contact
	* @param integer contact id
	* @param array all the child contacts
	* @param integer septh at which the particular contact has to be placed in the hierarchy
	* @return array all the child contacts of the given contactid
	*/
	public function __getChildContacts($id, &$child_contacts, $depth) {
		global $log, $adb;
		$log->debug('> __getChildContacts '.$id);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = 'SELECT vtiger_contactdetails.*, '
			." CASE when (vtiger_users.user_name not like '') THEN vtiger_users.user_name ELSE vtiger_groups.groupname END as user_name "
			.' FROM vtiger_contactdetails'
			.' INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid'
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
		$log->debug('< __getChildContacts');
		return $child_contacts;
	}

	public function getvtlib_open_popup_window_function($fieldname, $basemodule) {
		if ($basemodule=='Issuecards') {
			return 'set_return_shipbilladdress';
		} elseif ($fieldname=='contact_id' && ($basemodule=='Contacts' || $basemodule=='Quotes' || $basemodule=='Invoice' || $basemodule=='SalesOrder' || $basemodule=='PurchaseOrder' )) {
			return 'selectContactvtlib';
		} elseif ($fieldname == 'cto_id' && $basemodule == 'cbCalendar') {
			return 'open_filtered_contactsIfAccounts';
		} else {
			return 'vtlib_open_popup_window';
		}
	}
}
?>
