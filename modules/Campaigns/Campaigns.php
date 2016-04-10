<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Leads/Leads.php');
require_once('user_privileges/default_module_view.php');

class Campaigns extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = "vtiger_campaign";
	var $table_index= 'campaignid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_campaignscf', 'campaignid');

	var $tab_name = Array('vtiger_crmentity','vtiger_campaign','vtiger_campaignscf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_campaign'=>'campaignid','vtiger_campaignscf'=>'campaignid');

	var $list_fields = Array(
		'Campaign Name'=>Array('campaign'=>'campaignname'),
		'Campaign Type'=>Array('campaign'=>'campaigntype'),
		'Campaign Status'=>Array('campaign'=>'campaignstatus'),
		'Expected Revenue'=>Array('campaign'=>'expectedrevenue'),
		'Expected Close Date'=>Array('campaign'=>'closingdate'),
		'Assigned To' => Array('crmentity'=>'smownerid')
	);

	var $list_fields_name = Array(
		'Campaign Name'=>'campaignname',
		'Campaign Type'=>'campaigntype',
		'Campaign Status'=>'campaignstatus',
		'Expected Revenue'=>'expectedrevenue',
		'Expected Close Date'=>'closingdate',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field= 'campaignname';

	var $search_fields = Array(
		'Campaign Name'=>Array('vtiger_campaign'=>'campaignname'),
		'Campaign Type'=>Array('vtiger_campaign'=>'campaigntype'),
	);

	var $search_fields_name = Array(
		'Campaign Name'=>'campaignname',
		'Campaign Type'=>'campaigntype',
	);

	// For Popup window record selection
	var $popup_fields = Array('campaignname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array('campaignname','smownerid','campaigntype','productname','expectedrevenue','closingdate','campaignstatus','expectedresponse','targetaudience','expectedcost');

	// For Alphabetical search
	var $def_basicsearch_col = 'campaignname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'campaignname';

	// Required Information for enabling Import feature
	var $required_fields = Array('campaignname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'crmid';
	var $default_sort_order = 'DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('campaignname','createdtime' ,'modifiedtime','assigned_user_id');

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
	}

	/**
	 * Function to get Campaign related Accouts
	 * @param  integer   $id      - campaignid
	 * returns related Accounts record in array format
	 */
	function get_accounts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_accounts(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if(getFieldVisibilityPermission('Accounts', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('vtiger_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed = (getFieldVisibilityPermission('Accounts', $current_user->id, 'campaignrelstatus','readwrite') == '0')? true : false;
		}

		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		// Send mail button for selected Accounts
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';
		/* To get Accounts CustomView -START */
		require_once('modules/CustomView/CustomView.php');
		$ahtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$ahtml .= $customviewcombo_html;
		$ahtml .= "</select>";
		/* To get Accounts CustomView -END */

		$button .= $ahtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;';
		$button .= "<input title='".getTranslatedString('LBL_EMPTY_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_EMPTY_LIST',$this_module)."' type='button' name='button' onclick='emptyCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';

		if($actions)
		{
			if(is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes')
			{
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes')
			{
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_account.*,
				CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_crmentity.modifiedtime, vtiger_campaignrelstatus.*, vtiger_accountbillads.*
				FROM vtiger_account
				INNER JOIN vtiger_campaignaccountrel ON vtiger_campaignaccountrel.accountid = vtiger_account.accountid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
				LEFT JOIN vtiger_campaignrelstatus ON vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignaccountrel.campaignrelstatusid
				WHERE vtiger_campaignaccountrel.campaignid = ".$id." AND vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null)
			$return_value = Array();
		else if($is_CampaignStatusAllowed) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->add_status_popup($return_value, $statusPos, 'Accounts');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_accounts method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Contacts
	 * @param  integer   $id      - campaignid
	 * returns related Contacts record in array format
	 */
	function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_contacts(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if(getFieldVisibilityPermission('Contacts', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('vtiger_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed = (getFieldVisibilityPermission('Contacts', $current_user->id, 'campaignrelstatus','readwrite') == '0')? true : false;
		}

		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		// Send mail button for selected Leads
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		/* To get Leads CustomView -START */
		require_once('modules/CustomView/CustomView.php');
		$lhtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$lhtml .= $customviewcombo_html;
		$lhtml .= "</select>";
		/* To get Leads CustomView -END */

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;';
		$button .= "<input title='".getTranslatedString('LBL_EMPTY_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_EMPTY_LIST',$this_module)."' type='button' name='button' onclick='emptyCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.accountid, vtiger_account.accountname,
				CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name ,
				vtiger_contactdetails.contactid, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.title,
				vtiger_contactdetails.department, vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_crmentity.crmid,
				vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_campaignrelstatus.*
				FROM vtiger_contactdetails
				INNER JOIN vtiger_campaigncontrel ON vtiger_campaigncontrel.contactid = vtiger_contactdetails.contactid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
				LEFT JOIN vtiger_campaignrelstatus ON vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaigncontrel.campaignrelstatusid
				WHERE vtiger_campaigncontrel.campaignid = ".$id." AND vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null)
			$return_value = Array();
		else if($is_CampaignStatusAllowed) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->add_status_popup($return_value, $statusPos, 'Contacts');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Leads
	 * @param  integer   $id      - campaignid
	 * returns related Leads record in array format
	 */
	function get_leads($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view, $currentModule;
        $log->debug("Entering get_leads(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if(getFieldVisibilityPermission('Leads', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('vtiger_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed  = (getFieldVisibilityPermission('Leads', $current_user->id, 'campaignrelstatus','readwrite') == '0')? true : false;
		}

		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		// Send mail button for selected Leads
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		/* To get Leads CustomView -START */
		require_once('modules/CustomView/CustomView.php');
		$lhtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$lhtml .= $customviewcombo_html;
		$lhtml .= "</select>";
		/* To get Leads CustomView -END */

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;';
		$button .= "<input title='".getTranslatedString('LBL_EMPTY_LIST',$this_module)."' class='crmbutton small edit' value='".getTranslatedString('LBL_EMPTY_LIST',$this_module)."' type='button' name='button' onclick='emptyCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_leaddetails.*, vtiger_crmentity.crmid,vtiger_leadaddress.phone,vtiger_leadsubdetails.website,
				CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.smownerid, vtiger_campaignrelstatus.*
				FROM vtiger_leaddetails
				INNER JOIN vtiger_campaignleadrel ON vtiger_campaignleadrel.leadid=vtiger_leaddetails.leadid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
				INNER JOIN vtiger_leadsubdetails  ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid
				INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_campaignrelstatus ON vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
				WHERE vtiger_crmentity.deleted=0 AND vtiger_campaignleadrel.campaignid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null)
			$return_value = Array();
		else if($is_CampaignStatusAllowed) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->add_status_popup($return_value, $statusPos, 'Leads');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_leads method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Potentials
	 * @param  integer   $id      - campaignid
	 * returns related potentials record in array format
	 */
	function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_opportunities(".$id.") method ...");
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

		if($actions && getFieldVisibilityPermission($related_module,$current_user->id,'campaignid', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_potential.related_to, vtiger_account.accountname, vtiger_potential.potentialid, vtiger_potential.potentialname,
					vtiger_potential.potentialtype, vtiger_potential.sales_stage, vtiger_potential.amount, vtiger_potential.closingdate,
					vtiger_crmentity.crmid, vtiger_crmentity.smownerid FROM vtiger_campaign
					INNER JOIN vtiger_potential ON vtiger_campaign.campaignid = vtiger_potential.campaignid
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
					LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
					LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
					LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_potential.related_to
					WHERE vtiger_campaign.campaignid = ".$id." AND vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}

	/**
	 * Function to get Campaign related Activities
	 * @param  integer   $id      - campaignid
	 * returns related activities record in array format
	 */
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EventEditView\";this.form.module.value=\"Calendar4You\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
				if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EventEditView\";this.form.module.value=\"Calendar4You\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.lastname,
			vtiger_contactdetails.firstname,
			vtiger_contactdetails.contactid,
			vtiger_activity.*,
			vtiger_seactivityrel.*,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_crmentity.modifiedtime,
			CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_recurringevents.recurringtype
			FROM vtiger_activity
			INNER JOIN vtiger_seactivityrel
				ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid=vtiger_activity.activityid
			LEFT JOIN vtiger_cntactivityrel
				ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT OUTER JOIN vtiger_recurringevents
				ON vtiger_recurringevents.activityid = vtiger_activity.activityid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_seactivityrel.crmid=".$id."
			AND vtiger_crmentity.deleted = 0
			AND (activitytype = 'Task'
				OR activitytype !='Emails')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;

	}
	/*
	 * Function populate the status columns' HTML
	 * @param - $related_list return value from GetRelatedList
	 * @param - $status_column index of the status column in the list.
	 * returns true on success
	 */
	function add_status_popup($related_list, $status_column = 7, $related_module)
	{
		global $adb;

		if(!$this->campaignrelstatus)
		{
			$result = $adb->query('SELECT * FROM vtiger_campaignrelstatus;');
			while($row = $adb->fetchByAssoc($result))
			{
				$this->campaignrelstatus[$row['campaignrelstatus']] = $row;
			}
		}
		foreach($related_list['entries'] as $key => &$entry)
		{
			$popupitemshtml = '';
			foreach($this->campaignrelstatus as $campaingrelstatus)
			{
				$camprelstatus = getTranslatedString($campaingrelstatus[campaignrelstatus],'Campaigns');
				$popupitemshtml .= "<a onmouseover=\"javascript: showBlock('campaignstatus_popup_$key')\" href=\"javascript:updateCampaignRelationStatus('$related_module', '".$this->id."', '$key', '$campaingrelstatus[campaignrelstatusid]', '".addslashes($camprelstatus)."');\">$camprelstatus</a><br />";
			}
			$popuphtml = '<div onmouseover="javascript:clearTimeout(statusPopupTimer);" onmouseout="javascript:closeStatusPopup(\'campaignstatus_popup_'.$key.'\');" style="margin-top: -14px; width: 200px;" id="campaignstatus_popup_'.$key.'" class="calAction"><div style="background-color: #FFFFFF; padding: 8px;">'.$popupitemshtml.'</div></div>';

			$entry[$status_column] = "<a href=\"javascript: showBlock('campaignstatus_popup_$key');\">[+]</a> <span id='campaignstatus_$key'>".$entry[$status_column]."</span>".$popuphtml;
		}

		return $related_list;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_campaign","campaignid");
		$query .=" left join vtiger_crmentity as vtiger_crmentityCampaigns on vtiger_crmentityCampaigns.crmid=vtiger_campaign.campaignid and vtiger_crmentityCampaigns.deleted=0
				left join vtiger_products as vtiger_productsCampaigns on vtiger_campaign.product_id = vtiger_productsCampaigns.productid
				left join vtiger_campaignscf on vtiger_campaignscf.campaignid = vtiger_crmentityCampaigns.crmid
				left join vtiger_groups as vtiger_groupsCampaigns on vtiger_groupsCampaigns.groupid = vtiger_crmentityCampaigns.smownerid
				left join vtiger_users as vtiger_usersCampaigns on vtiger_usersCampaigns.id = vtiger_crmentityCampaigns.smownerid
                left join vtiger_users as vtiger_lastModifiedByCampaigns on vtiger_lastModifiedByCampaigns.id = vtiger_crmentityCampaigns.modifiedby ";

		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Contacts" => array("vtiger_campaigncontrel"=>array("campaignid","contactid"),"vtiger_campaign"=>"campaignid"),
			"Leads" => array("vtiger_campaignleadrel"=>array("campaignid","leadid"),"vtiger_campaign"=>"campaignid"),
			"Accounts" => array("vtiger_campaignaccountrel"=>array("campaignid","accountid"),"vtiger_campaign"=>"campaignid"),
			"Potentials" => array("vtiger_potential"=>array("campaignid","potentialid"),"vtiger_campaign"=>"campaignid"),
			"Calendar" => array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_campaign"=>"campaignid"),
			"Products" => array("vtiger_campaign"=>array("campaignid","product_id")),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Leads') {
			$sql = 'DELETE FROM vtiger_campaignleadrel WHERE campaignid=? AND leadid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Contacts') {
			$sql = 'DELETE FROM vtiger_campaigncontrel WHERE campaignid=? AND contactid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Accounts') {
			$sql = 'DELETE FROM vtiger_campaignaccountrel WHERE campaignid=? AND accountid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if ($with_module == 'Leads') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaignleadrel WHERE campaignid = ? AND leadid = ?',
												array($crmid, $with_crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO vtiger_campaignleadrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));

			} elseif($with_module == 'Contacts') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaigncontrel WHERE campaignid = ? AND contactid = ?',
												array($crmid, $with_crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO vtiger_campaigncontrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));
				if (GlobalVariable::getVariable('Campaign_CreatePotentialOnContactRelation', '0')=='1') {
					self::createPotentialRelatedTo($with_crmid, $crmid);
				}
			} elseif($with_module == 'Accounts') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaignaccountrel WHERE campaignid = ? AND accountid = ?',
												array($crmid, $with_crmid));
				if($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO vtiger_campaignaccountrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));
				if (GlobalVariable::getVariable('Campaign_CreatePotentialOnAccountRelation', '0')=='1') {
					self::createPotentialRelatedTo($with_crmid, $crmid);
				}
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	/* Create potential */
	public static function createPotentialRelatedTo($relatedto,$campaignid) {
		global $adb, $current_user;
		$checkrs = $adb->pquery('select 1
			from vtiger_potentials
			inner join vtiger_crmentity on crmid=potentialid
			where deleted=0 and related_to=? and campaignid=?',array($relatedto,$campaignid));
		if ($adb->num_rows($checkrs)==0) {
			require_once('modules/Potentials/Potentials.php');
			$entity = new Potentials();
			$entity->mode = '';
			$_REQUEST['assigntype'] = 'U';
			$_REQUEST['assigned_user_id'] = $current_user->id;
			$entity->column_fields['assigned_user_id'] = $current_user->id;
			$entity->column_fields['related_to'] = $relatedto;
			$entity->column_fields['campaignid'] = $campaignid;
			$dt = new DateTimeField();
			$entity->column_fields['closingdate'] = $dt->getDisplayDate();
			$cname = getEntityName('Campaigns', $campaignid);
			$pname = $cname[$campaignid].' - ';
			$rname = getEntityName(getSalesEntityType($relatedto), $relatedto);
			$pname = $pname.$rname[$relatedto];
			$entity->column_fields['potentialname'] = $pname;
			$entity->column_fields['sales_stage'] = 'Prospecting';
			$entity->save('Potentials');
		}
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

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

		$rel_table_arr = Array("Contacts"=>"vtiger_campaigncontrel","Potentials"=>"vtiger_potential",
					"Leads"=>"vtiger_campaignleadrel","Activities"=>"vtiger_seactivityrel",
					"Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel",
					"Campaigns"=>"vtiger_campaignaccountrel","CobroPago"=>"vtiger_cobropago");

		$tbl_field_arr = Array("vtiger_campaigncontrel"=>"contactid","vtiger_potential"=>"potentialid",
					"vtiger_campaignleadrel"=>"leadid","vtiger_seactivityrel"=>"activityid",
					"vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid",
					"vtiger_campaignaccountrel"=>"accountid","vtiger_cobropago"=>"cobropagoid");

		$entity_tbl_field_arr = Array("vtiger_campaigncontrel"=>"campaignid","vtiger_potential"=>"campaignid",
					"vtiger_campaignleadrel"=>"campaignid","vtiger_seactivityrel"=>"crmid",
					"vtiger_senotesrel"=>"crmid","vtiger_seattachmentsrel"=>"crmid",
					"vtiger_campaignaccountrel"=>"campaignid","vtiger_cobropago"=>"related_id");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
						" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
						array($transferId,$entityId));
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0) {
					for($i=0;$i<$res_cnt;$i++) {
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value));
					}
				}
			}
		}
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$log->debug("Exiting transferRelatedRecords...");
	}

}
?>
