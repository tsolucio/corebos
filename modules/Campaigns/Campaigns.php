<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/logging.php';
require_once 'include/utils/utils.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Leads/Leads.php';
require 'user_privileges/default_module_view.php';

class Campaigns extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_campaign';
	public $table_index= 'campaignid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_campaignscf', 'campaignid');

	public $tab_name = array('vtiger_crmentity','vtiger_campaign','vtiger_campaignscf');
	public $tab_name_index = array('vtiger_crmentity'=>'crmid','vtiger_campaign'=>'campaignid','vtiger_campaignscf'=>'campaignid');

	public $list_fields = array(
		'Campaign Name'=>array('campaign'=>'campaignname'),
		'Campaign Type'=>array('campaign'=>'campaigntype'),
		'Campaign Status'=>array('campaign'=>'campaignstatus'),
		'Expected Revenue'=>array('campaign'=>'expectedrevenue'),
		'Expected Close Date'=>array('campaign'=>'closingdate'),
		'Assigned To' => array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		'Campaign Name'=>'campaignname',
		'Campaign Type'=>'campaigntype',
		'Campaign Status'=>'campaignstatus',
		'Expected Revenue'=>'expectedrevenue',
		'Expected Close Date'=>'closingdate',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field= 'campaignname';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Campaign Name'=>array('vtiger_campaign'=>'campaignname'),
		'Campaign Type'=>array('vtiger_campaign'=>'campaigntype'),
	);
	public $search_fields_name = array(
		'Campaign Name'=>'campaignname',
		'Campaign Type'=>'campaigntype',
	);

	// For Popup window record selection
	public $popup_fields = array('campaignname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'campaignname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'campaignname';

	// Required Information for enabling Import feature
	public $required_fields = array('campaignname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'crmid';
	public $default_sort_order = 'DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('campaignname','createdtime' ,'modifiedtime');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Function to get Campaign related Accouts
	 * @param  integer   $id      - campaignid
	 * returns related Accounts record in array format
	 */
	public function get_accounts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_accounts(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if (getFieldVisibilityPermission('Accounts', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('vtiger_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed = getFieldVisibilityPermission('Accounts', $current_user->id, 'campaignrelstatus', 'readwrite') == '0';
		}

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		// Send mail button for selected Accounts
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON');
		$button .= "' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';
		/* To get Accounts CustomView -START */
		require_once 'modules/CustomView/CustomView.php';
		$ahtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$ahtml .= $customviewcombo_html;
		$ahtml .= "</select>";
		/* To get Accounts CustomView -END */

		$button .= $ahtml."<input title='".getTranslatedString('LBL_LOAD_LIST', $this_module)."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_LOAD_LIST', $this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;';
		$button .= "<input title='".getTranslatedString('LBL_EMPTY_LIST', $this_module)."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_EMPTY_LIST', $this_module)."' type='button' name='button' onclick='emptyCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',".
					"'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '.
					getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input type='hidden' name='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_account.*,
				CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_crmentity.modifiedtime, vtiger_campaignrelstatus.*, vtiger_accountbillads.*
				FROM vtiger_account
				INNER JOIN vtiger_campaignaccountrel ON vtiger_campaignaccountrel.accountid = vtiger_account.accountid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
				LEFT JOIN vtiger_accountscf ON vtiger_account.accountid = vtiger_accountscf.accountid
				LEFT JOIN vtiger_campaignrelstatus ON vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignaccountrel.campaignrelstatusid
				WHERE vtiger_campaignaccountrel.campaignid = ".$id." AND vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		} elseif ($is_CampaignStatusAllowed && !empty($return_value['header'])) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->addStatusPopup($return_value, $statusPos, 'Accounts');
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
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_contacts(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if (getFieldVisibilityPermission('Contacts', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('vtiger_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed = getFieldVisibilityPermission('Contacts', $current_user->id, 'campaignrelstatus', 'readwrite') == '0';
		}

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		// Send mail button for selected Leads
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		/* To get Leads CustomView -START */
		require_once 'modules/CustomView/CustomView.php';
		$lhtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$lhtml .= $customviewcombo_html;
		$lhtml .= "</select>";
		/* To get Leads CustomView -END */

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST', $this_module)."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_LOAD_LIST', $this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;';
		$button .= "<input title='".getTranslatedString('LBL_EMPTY_LIST', $this_module)."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_EMPTY_LIST', $this_module)."' type='button' name='button' onclick='emptyCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',".
					"'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '.
					getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input type='hidden' name='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.accountid, vtiger_account.accountname,
				CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name ,
				vtiger_contactdetails.contactid, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.title,
				vtiger_contactdetails.department, vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_crmentity.crmid,
				vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_campaignrelstatus.*
				FROM vtiger_contactdetails
				INNER JOIN vtiger_campaigncontrel ON vtiger_campaigncontrel.contactid = vtiger_contactdetails.contactid
				INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
				INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
				INNER JOIN vtiger_customerdetails ON vtiger_contactdetails.contactid = vtiger_customerdetails.customerid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
				LEFT JOIN vtiger_campaignrelstatus ON vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaigncontrel.campaignrelstatusid
				WHERE vtiger_campaigncontrel.campaignid = ".$id." AND vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		} elseif ($is_CampaignStatusAllowed && !empty($return_value['header'])) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->addStatusPopup($return_value, $statusPos, 'Contacts');
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
	public function get_leads($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug("Entering get_leads(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$is_CampaignStatusAllowed = false;
		global $current_user;
		if (getFieldVisibilityPermission('Leads', $current_user->id, 'campaignrelstatus') == '0') {
			$other->list_fields['Status'] = array('vtiger_campaignrelstatus'=>'campaignrelstatus');
			$other->list_fields_name['Status'] = 'campaignrelstatus';
			$other->sortby_fields[] = 'campaignrelstatus';
			$is_CampaignStatusAllowed  = getFieldVisibilityPermission('Leads', $current_user->id, 'campaignrelstatus', 'readwrite') == '0';
		}

		$parenttab = getParentTab();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		// Send mail button for selected Leads
		$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON');
		$button .= "' type='button' name='button' onclick='rel_eMail(\"$this_module\",this,\"$related_module\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';

		/* To get Leads CustomView -START */
		require_once 'modules/CustomView/CustomView.php';
		$lhtml = "<select id='".$related_module."_cv_list' class='small'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
		$lhtml .= $customviewcombo_html;
		$lhtml .= "</select>";
		/* To get Leads CustomView -END */

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST', $this_module)."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_LOAD_LIST', $this_module)."' type='button' name='button' onclick='loadCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;';
		$button .= "<input title='".getTranslatedString('LBL_EMPTY_LIST', $this_module)."' class='crmbutton small edit' value='";
		$button .= getTranslatedString('LBL_EMPTY_LIST', $this_module)."' type='button' name='button' onclick='emptyCvList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',".
					"'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). ' '.
					getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input type='hidden' name='createmode' value='link' />".
					"<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_leaddetails.*, vtiger_crmentity.crmid,vtiger_leadaddress.phone,vtiger_leadsubdetails.website,
				CASE when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.smownerid, vtiger_campaignrelstatus.*
				FROM vtiger_leaddetails
				INNER JOIN vtiger_campaignleadrel ON vtiger_campaignleadrel.leadid=vtiger_leaddetails.leadid
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
				INNER JOIN vtiger_leadsubdetails  ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid
				INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid
				INNER JOIN vtiger_leadscf ON vtiger_leaddetails.leadid = vtiger_leadscf.leadid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_campaignrelstatus ON vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
				WHERE vtiger_crmentity.deleted=0 AND vtiger_campaignleadrel.campaignid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		} elseif ($is_CampaignStatusAllowed && !empty($return_value['header'])) {
			$statusPos = count($return_value['header']) - 2; // Last column is for Actions, exclude that. Also the index starts from 0, so reduce one more count.
			$return_value = $this->addStatusPopup($return_value, $statusPos, 'Leads');
		}

		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_leads method ...");
		return $return_value;
	}

	/*
	 * Function populate the status columns' HTML
	 * @param - $related_list return value from GetRelatedList
	 * @param - $status_column index of the status column in the list.
	 * returns true on success
	 */
	private function addStatusPopup($related_list, $status_column, $related_module) {
		global $adb;
		if (empty($this->campaignrelstatus)) {
			$this->campaignrelstatus = array();
		}
		if (count($this->campaignrelstatus)==0) {
			$result = $adb->query('SELECT * FROM vtiger_campaignrelstatus;');
			while ($row = $adb->fetchByAssoc($result)) {
				$r = $row;
				$r['campaignrelstatusi18n'] = getTranslatedString($row['campaignrelstatus'], 'Campaigns');
				$this->campaignrelstatus[$row['campaignrelstatus']] = $r;
			}
		}
		if (isset($related_list['entries'])) {
			foreach ($related_list['entries'] as $key => &$entry) {
				$popupitemshtml = '';
				foreach ($this->campaignrelstatus as $campaingrelstatus) {
					$camprelstatus = $campaingrelstatus['campaignrelstatusi18n'];
					$popupitemshtml .= "<a onmouseover=\"javascript: showBlock('campaignstatus_popup_$key')\" href=\"javascript:updateCampaignRelationStatus('".
						"$related_module', '".$this->id."', '$key', '".$campaingrelstatus['campaignrelstatusid']."', '".addslashes($camprelstatus).
						"');\">$camprelstatus</a><br />";
				}
				$popuphtml = '<div onmouseover="javascript:clearTimeout(statusPopupTimer);" onmouseout="javascript:closeStatusPopup(\'campaignstatus_popup_'.$key.
					'\');" style="margin-top: -14px; width: 200px;" id="campaignstatus_popup_'.$key.'" class="calAction">'.
					'<div style="background-color: #FFFFFF; padding: 8px;">'.$popupitemshtml.'</div></div>';
				$entry[$status_column] = "<a href=\"javascript: showBlock('campaignstatus_popup_$key');\">[+]</a> <span id='campaignstatus_$key'>".$entry[$status_column].
					"</span>".$popuphtml;
			}
		}
		return $related_list;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			"Contacts" => array("vtiger_campaigncontrel"=>array("campaignid","contactid"),"vtiger_campaign"=>"campaignid"),
			"Leads" => array("vtiger_campaignleadrel"=>array("campaignid","leadid"),"vtiger_campaign"=>"campaignid"),
			"Accounts" => array("vtiger_campaignaccountrel"=>array("campaignid","accountid"),"vtiger_campaign"=>"campaignid"),
			"Potentials" => array("vtiger_potential"=>array("campaignid","potentialid"),"vtiger_campaign"=>"campaignid"),
			"Calendar" => array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_campaign"=>"campaignid"),
			"Products" => array("vtiger_campaign"=>array("campaignid","product_id")),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}

		if ($return_module == 'Leads') {
			$sql = 'DELETE FROM vtiger_campaignleadrel WHERE campaignid=? AND leadid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Contacts') {
			$sql = 'DELETE FROM vtiger_campaigncontrel WHERE campaignid=? AND contactid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Accounts') {
			$sql = 'DELETE FROM vtiger_campaignaccountrel WHERE campaignid=? AND accountid=?';
			$this->db->pquery($sql, array($id, $return_id));
			$sql = 'DELETE FROM vtiger_campaigncontrel WHERE campaignid=? AND contactid IN (SELECT contactid FROM vtiger_contactdetails WHERE accountid=?)';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();

		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Leads') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaignleadrel WHERE campaignid = ? AND leadid = ?', array($crmid, $with_crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO vtiger_campaignleadrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));
			} elseif ($with_module == 'Contacts') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaigncontrel WHERE campaignid = ? AND contactid = ?', array($crmid, $with_crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$sql = 'INSERT INTO vtiger_campaigncontrel VALUES(?,?,1)';
				$adb->pquery($sql, array($crmid, $with_crmid));
				if (GlobalVariable::getVariable('Campaign_CreatePotentialOnContactRelation', '0')=='1') {
					self::createPotentialRelatedTo($with_crmid, $crmid);
				}
			} elseif ($with_module == 'Accounts') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaignaccountrel WHERE campaignid = ? AND accountid = ?', array($crmid, $with_crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
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

	public function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		$with_crmid = (array)$with_crmid;
		$data = array();
		$data['sourceModule'] = $module;
		$data['sourceRecordId'] = $crmid;
		$data['destinationModule'] = $with_module;
		foreach ($with_crmid as $relcrmid) {
			$data['destinationRecordId'] = $relcrmid;
			cbEventHandler::do_action('corebos.entity.link.delete', $data);
			if ($with_module == 'Documents') {
				$adb->pquery('DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?', array($crmid, $relcrmid));
			} elseif ($with_module == 'Leads') {
				$adb->pquery('DELETE FROM vtiger_campaignleadrel WHERE campaignid=? AND leadid=?', array($crmid, $relcrmid));
			} elseif ($with_module == 'Contacts') {
				$adb->pquery('DELETE FROM vtiger_campaigncontrel WHERE campaignid=? AND contactid=?', array($crmid, $relcrmid));
			} elseif ($with_module == 'Accounts') {
				$adb->pquery('DELETE FROM vtiger_campaignaccountrel WHERE campaignid=? AND accountid=?', array($crmid, $relcrmid));
			} else {
				$adb->pquery(
					'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND module=? AND relcrmid=? AND relmodule=?) OR (relcrmid=? AND relmodule=? AND crmid=? AND module=?)',
					array($crmid, $module, $relcrmid, $with_module,$crmid, $module, $relcrmid, $with_module)
				);
			}
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		}
	}

	/* Create potential */
	public static function createPotentialRelatedTo($relatedto, $campaignid) {
		global $adb, $current_user;
		$checkrs = $adb->pquery('select 1
			from vtiger_potential
			inner join vtiger_crmentity on crmid=potentialid
			where deleted=0 and related_to=? and campaignid=?', array($relatedto,$campaignid));
		if ($adb->num_rows($checkrs)==0) {
			require_once 'modules/Potentials/Potentials.php';
			$entity = new Potentials();
			$entity->mode = '';
			$cname = getEntityName('Campaigns', $campaignid);
			$cname = $cname[$campaignid].' - ';
			$setype = getSalesEntityType($relatedto);
			$rname = getEntityName($setype, $relatedto);
			$rname = $rname[$relatedto];
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_PotentialOnCampaignRelation', cbMap::getMapIdByName('PotentialOnCampaignRelation'));
			if ($cbMapid) {
				$cmp = CRMEntity::getInstance('Campaigns');
				$cmp->retrieve_entity_info($campaignid, 'Campaigns');
				if ($setype=='Accounts') {
					$cmp->column_fields['AccountName'] = $rname;
					$cmp->column_fields['ContactName'] = '';
				} else {
					$cmp->column_fields['AccountName'] = '';
					$cmp->column_fields['ContactName'] = $rname;
				}
				$cbMap = cbMap::getMapByID($cbMapid);
				$entity->column_fields = $cbMap->Mapping($cmp->column_fields, array());
			}
			if (empty($entity->column_fields['assigned_user_id'])) {
				$entity->column_fields['assigned_user_id'] = $current_user->id;
			}
			$entity->column_fields['related_to'] = $relatedto;
			$entity->column_fields['campaignid'] = $campaignid;
			if (empty($entity->column_fields['closingdate'])) {
				$dt = new DateTimeField();
				$entity->column_fields['closingdate'] = $dt->getDisplayDate();
			}
			if (empty($entity->column_fields['potentialname'])) {
				$entity->column_fields['potentialname'] = $cname.$rname;
			}
			if (empty($entity->column_fields['sales_stage'])) {
				$entity->column_fields['sales_stage'] = 'Prospecting';
			}
			$_REQUEST['assigntype'] = 'U';
			$_REQUEST['assigned_user_id'] = $entity->column_fields['assigned_user_id'];
			$entity->save('Potentials');
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
		$rel_table_arr = array("Contacts"=>"vtiger_campaigncontrel","Potentials"=>"vtiger_potential",
					"Leads"=>"vtiger_campaignleadrel",
					"Attachments"=>"vtiger_seattachmentsrel",
					"Campaigns"=>"vtiger_campaignaccountrel","CobroPago"=>"vtiger_cobropago");

		$tbl_field_arr = array("vtiger_campaigncontrel"=>"contactid","vtiger_potential"=>"potentialid",
					"vtiger_campaignleadrel"=>"leadid",
					"vtiger_seattachmentsrel"=>"attachmentsid",
					"vtiger_campaignaccountrel"=>"accountid","vtiger_cobropago"=>"cobropagoid");

		$entity_tbl_field_arr = array("vtiger_campaigncontrel"=>"campaignid","vtiger_potential"=>"campaignid",
					"vtiger_campaignleadrel"=>"campaignid",
					"vtiger_seattachmentsrel"=>"crmid",
					"vtiger_campaignaccountrel"=>"campaignid","vtiger_cobropago"=>"related_id");

		foreach ($transferEntityIds as $transferId) {
			foreach ($rel_table_arr as $rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery(
					"select $id_field from $rel_table where $entity_id_field=? " .
						" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
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
		$log->debug("Exiting transferRelatedRecords...");
	}
}
?>
