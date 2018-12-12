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
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Potentials/Potentials.php';
require_once 'modules/Calendar/Activity.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Emails/Emails.php';
require_once 'include/utils/utils.php';
require 'user_privileges/default_module_view.php';

class Accounts extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_account';
	public $table_index= 'accountid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_accountscf', 'accountid');
	public $related_tables = array(
		'vtiger_accountbillads' => array('accountaddressid', 'vtiger_account', 'accountid'),
		'vtiger_accountshipads' => array('accountaddressid', 'vtiger_account', 'accountid'),
	);

	public $tab_name = array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountshipads','vtiger_accountscf');
	public $tab_name_index = array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_account'=>'accountid',
		'vtiger_accountbillads'=>'accountaddressid',
		'vtiger_accountshipads'=>'accountaddressid',
		'vtiger_accountscf'=>'accountid'
	);

	// This is the list of fields that are in the lists.
	public $list_fields = array(
		'Account Name' => array('account'=>'accountname'),
		'Billing City' => array('accountbillads'=>'bill_city'),
		'Website' => array('account'=>'website'),
		'Phone' => array('account'=> 'phone'),
		'Assigned To' => array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		'Account Name'=>'accountname',
		'Billing City'=>'bill_city',
		'Website'=>'website',
		'Phone'=>'phone',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'accountname';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Account Name' => array('account'=>'accountname'),
		'Billing City' => array('accountbillads'=>'bill_city'),
		'Assigned To'  => array('crmentity'=>'smownerid'),
	);
	public $search_fields_name = array(
		'Account Name'=>'accountname',
		'Billing City'=>'bill_city',
		'Assigned To'=>'assigned_user_id',
	);

	// For Popup window record selection
	public $popup_fields = array('accountname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array('accountname','bill_city','website','phone','smownerid');

	// For Alphabetical search
	public $def_basicsearch_col = 'accountname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'accountname';

	// Required Information for enabling Import feature
	public $required_fields = array();

	public $default_order_by = 'accountname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'accountname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/** Returns a list of the associated Campaigns
	 * @param $id -- campaign id :: Type Integer
	 * @returns list of campaigns in array format
	 */
	public function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_campaigns(".$id.") method ...");
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

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus,
					vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
					vtiger_crmentity.modifiedtime from vtiger_campaign
					inner join vtiger_campaignaccountrel on vtiger_campaignaccountrel.campaignid=vtiger_campaign.campaignid
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_campaign.campaignid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
					where vtiger_campaignaccountrel.accountid=".$id." and vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_campaigns method ...");
		return $return_value;
	}

	/** Returns a list of the associated contacts */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_contacts(".$id.") method ...");
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_account.accountname,
			case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_contactscf.*
			FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0
			AND vtiger_contactdetails.accountid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/** Returns a list of the associated opportunities */
	public function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_opportunities(".$id.") method ...");
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
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_potential.potentialid, vtiger_potential.related_to,
				vtiger_potential.potentialname, vtiger_potential.sales_stage,
				vtiger_potential.potentialtype, vtiger_potential.amount,vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
				vtiger_potential.closingdate, vtiger_potential.potentialtype, vtiger_account.accountname,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			FROM vtiger_potential
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_potential.related_to
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_potential.related_to = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}

	/** Returns a list of the associated emails */
	public function get_emails($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule, $adb;
		$log->debug("Entering get_emails(".$id.") method ...");
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

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		$accountContacts = $adb->pquery(
			'SELECT contactid,firstname,lastname
				FROM vtiger_contactdetails
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
				WHERE vtiger_contactdetails.accountid = ? AND vtiger_crmentity.deleted = 0 ORDER BY firstname,lastname',
			array($id)
		);
		$relid = $adb->run_query_field('select relation_id from vtiger_relatedlists where tabid='.$cur_tab_id.' and related_tabid='.$rel_tab_id, 'relation_id');
		$button .= '<select name="email_filter" class="small"
			onchange="loadRelatedListBlock(\'module=Accounts&action=AccountsAjax&file=DetailViewAjax&record='.$id.'&ajxaction=LOADRELATEDLIST&header=Emails&relation_id='
			.$relid.'&email_filter=\'+this.options[this.options.selectedIndex].value+\'&actions=add&parenttab=Support\',\'tbl_Accounts_Emails\',\'Accounts_Emails\');">
			<option value="all">'.getTranslatedString('LBL_ALL').'</option>';
		$accname = getEntityName('Accounts', $id);
		$button .= '<option value="'.$id.'" '.((isset($_REQUEST['email_filter']) && $_REQUEST['email_filter']==$id) ? 'selected' : '').'>'.$accname[$id].'</option>';
		while ($cnt=$adb->fetch_array($accountContacts)) {
			$button .= '<option value="'.$cnt['contactid'].'" '
				.((isset($_REQUEST['email_filter']) && $_REQUEST['email_filter']==$cnt['contactid']) ? 'selected' : '').'>'.$cnt['firstname'].' '.$cnt['lastname']
				.'</option>';
		}
		$button .= '</select>&nbsp;';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". $singular_modname."' accessyKey='F' class='crmbutton small create'"
					." onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='".getTranslatedString('LBL_ADD_NEW')
					.' '. $singular_modname."'></td>";
			}
		}

		if (empty($_REQUEST['email_filter']) || $_REQUEST['email_filter']=='all') {
			$entityIds = array($id);
			$numOfContacts = $adb->num_rows($accountContacts);
			if ($accountContacts && $numOfContacts > 0) {
				for ($i=0; $i < $numOfContacts; ++ $i) {
					$entityIds[] = $adb->query_result($accountContacts, $i, 'contactid');
				}
			}
		} else {
			$entityIds = array(vtlib_purify($_REQUEST['email_filter']));
		}
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_activity.activityid, vtiger_activity.subject, vtiger_emaildetails.*, vtiger_email_track.access_count,
			vtiger_activity.activitytype, vtiger_crmentity.modifiedtime,vtiger_activity.time_start,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_activity.date_start, vtiger_seactivityrel.crmid as parent_id
			from vtiger_activity
			inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
			inner join vtiger_emaildetails on vtiger_emaildetails.emailid = vtiger_activity.activityid
			left join vtiger_email_track on (vtiger_email_track.crmid=vtiger_seactivityrel.crmid AND vtiger_email_track.mailid=vtiger_activity.activityid)
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			WHERE vtiger_seactivityrel.crmid IN (". implode(',', $entityIds) .") AND vtiger_activity.activitytype='Emails' AND vtiger_crmentity.deleted = 0";
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_emails method ...");
		return $return_value;
	}

	/**
	* Function to get Account related Quotes
	* @param  integer   $id      - accountid
	* returns related Quotes record in array format
	*/
	public function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_quotes(".$id.") method ...");
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_quotes.*, vtiger_potential.potentialname, vtiger_account.accountname
			FROM vtiger_quotes
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_quotes.accountid
			LEFT OUTER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_quotes.potentialid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_account.accountid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	}
	/**
	* Function to get Account related Invoices
	* @param  integer   $id      - accountid
	* returns related Invoices record in array format
	*/
	public function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_invoices(".$id.") method ...");
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.*, vtiger_invoice.*, vtiger_account.accountname, vtiger_salesorder.subject AS salessubject
			FROM vtiger_invoice
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_invoice.accountid
			LEFT OUTER JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_account.accountid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_invoices method ...");
		return $return_value;
	}

	/**
	* Function to get Account related SalesOrder
	* @param  integer   $id      - accountid
	* returns related SalesOrder record in array format
	*/
	public function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_salesorder(".$id.") method ...");
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

		if ($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id', 'readwrite') == '0') {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject AS quotename, vtiger_account.accountname,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			FROM vtiger_salesorder
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
			LEFT OUTER JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_salesorder.quoteid
			LEFT OUTER JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesorder.accountid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_salesorder.accountid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_salesorder method ...");
		return $return_value;
	}
	/**
	* Function to get Account related Tickets
	* @param  integer   $id      - accountid
	* returns related Ticket record in array format
	*/
	public function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		$return_value = parent::get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions);
		return $return_value;
	}

	/**
	* Function to get Account related Products
	* @param  integer   $id      - accountid
	* returns related Products record in array format
	*/
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view,$currentModule;
		$log->debug("Entering get_products(".$id.") method ...");
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
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT')
					.' ' . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$query = "SELECT vtiger_products.*,vtiger_productcf.*,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid
			FROM vtiger_products
			INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid and vtiger_seproductsrel.setype='Accounts'
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
			INNER JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
			INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_seproductsrel.crmid
			LEFT JOIN vtiger_users
				ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_account.accountid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/** Function to export the account records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Accounts Query.
	*/
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug('Entering create_export_query('.$where.') method ...');

		include 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('Accounts', 'detail_view');
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
				FROM vtiger_crmentity
				INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_crmentity.crmid
				LEFT JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
				LEFT JOIN vtiger_accountshipads ON vtiger_accountshipads.accountaddressid = vtiger_account.accountid
				LEFT JOIN vtiger_accountscf ON vtiger_accountscf.accountid = vtiger_account.accountid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid and vtiger_users.status = 'Active'
				LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
				LEFT JOIN vtiger_account vtiger_account2 ON vtiger_account2.accountid = vtiger_account.parentid
				LEFT JOIN vtiger_account vtiger_accountaccount_id ON vtiger_account.parentid = vtiger_accountaccount_id.accountid";
				// vtiger_account2 is added to get the Member of account
		$query .= $this->getNonAdminAccessControlQuery('Accounts', $current_user);
		$where_auto = ' vtiger_crmentity.deleted = 0 ';

		if ($where != '') {
			$query .= " WHERE ($where) AND ".$where_auto;
		} else {
			$query .= " WHERE ".$where_auto;
		}

		$log->debug('Exiting create_export_query method ...');
		return $query;
	}

	/** Function to get the Columnnames of the Account Record
	* Used By vtigerCRM Word Plugin
	* Returns the Merge Fields for Word Plugin
	*/
	public function getColumnNames_Acnt() {
		global $log,$current_user;
		$log->debug('Entering getColumnNames_Acnt() method ...');
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = 'SELECT fieldlabel FROM vtiger_field WHERE tabid = 6 and vtiger_field.presence in (0,2)';
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'SELECT vtiger_field.fieldid,fieldlabel
				FROM vtiger_field
				INNER JOIN vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				WHERE vtiger_field.tabid=6 and vtiger_field.displaytype in (1,2,4) and vtiger_profile2field.visible=0
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
			$custom_fields[$i] = $this->db->query_result($result, $i, "fieldlabel");
			$custom_fields[$i] = preg_replace("/\s+/", "", $custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug('Exiting getColumnNames_Acnt method ...');
		return $mergeflds;
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
		$rel_table_arr = array("Contacts"=>"vtiger_contactdetails","Potentials"=>"vtiger_potential","Quotes"=>"vtiger_quotes",
					"SalesOrder"=>"vtiger_salesorder","Invoice"=>"vtiger_invoice",
					"HelpDesk"=>"vtiger_troubletickets","Attachments"=>"vtiger_seattachmentsrel",
					"Products"=>"vtiger_seproductsrel","Campaigns"=>"vtiger_campaignaccountrel");

		$tbl_field_arr = array("vtiger_contactdetails"=>"contactid","vtiger_potential"=>"potentialid","vtiger_quotes"=>"quoteid",
					"vtiger_salesorder"=>"salesorderid","vtiger_invoice"=>"invoiceid","vtiger_troubletickets"=>"ticketid","vtiger_seattachmentsrel"=>"attachmentsid",
					"vtiger_seproductsrel"=>"productid","vtiger_campaignaccountrel"=>"campaignid");

		$entity_tbl_field_arr = array("vtiger_contactdetails"=>"accountid","vtiger_potential"=>"related_to","vtiger_quotes"=>"accountid",
					"vtiger_salesorder"=>"accountid","vtiger_invoice"=>"accountid","vtiger_troubletickets"=>"parent_id","vtiger_seattachmentsrel"=>"crmid",
					"vtiger_seproductsrel"=>"crmid","vtiger_campaignaccountrel"=>"accountid");

		foreach ($transferEntityIds as $transferId) {
			foreach ($rel_table_arr as $rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result = $adb->pquery(
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
		$log->debug("Exiting transferRelatedRecords...");
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			'Contacts' => array('vtiger_contactdetails'=>array('accountid','contactid'),'vtiger_account'=>'accountid'),
			'Potentials' => array('vtiger_potential'=>array('related_to','potentialid'),'vtiger_account'=>'accountid'),
			'Quotes' => array('vtiger_quotes'=>array('accountid','quoteid'),'vtiger_account'=>'accountid'),
			'SalesOrder' => array('vtiger_salesorder'=>array('accountid','salesorderid'),'vtiger_account'=>'accountid'),
			'Invoice' => array('vtiger_invoice'=>array('accountid','invoiceid'),'vtiger_account'=>'accountid'),
			'Calendar' => array('vtiger_seactivityrel'=>array('crmid','activityid'),'vtiger_account'=>'accountid'),
			'HelpDesk' => array('vtiger_troubletickets'=>array('parent_id','ticketid'),'vtiger_account'=>'accountid'),
			'Products' => array('vtiger_seproductsrel'=>array('crmid','productid'),'vtiger_account'=>'accountid'),
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_account'=>'accountid'),
			'Campaigns' => array('vtiger_campaignaccountrel'=>array('accountid','campaignid'),'vtiger_account'=>'accountid'),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryPlanner, $type, $where_condition);
		if ($queryPlanner->requireTable('vtiger_accountbillads')) {
			$query .= " left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid";
		}
		if ($queryPlanner->requireTable('vtiger_accountshipads')) {
			$query .= " left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid";
		}
		if ($queryPlanner->requireTable('vtiger_accountAccounts')) {
			$query .= "	left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid";
		}
		if ($queryPlanner->requireTable('vtiger_email_track')) {
			$query .= " LEFT JOIN vtiger_email_track AS vtiger_email_trackAccounts ON vtiger_email_trackAccounts .crmid = vtiger_account.accountid";
		}
		return $query;
	}

	/**
	* Function to get Account hierarchy of the given Account
	* @param  integer   $id      - accountid
	* returns Account hierarchy in array format
	*/
	public function getAccountHierarchy($id) {
		global $log, $current_user;
		$log->debug("Entering getAccountHierarchy(".$id.") method ...");

		$tabname = getParentTab();
		$listview_header = array();
		$listview_entries = array();

		foreach ($this->list_fields_name as $fieldname => $colname) {
			if (getFieldVisibilityPermission('Accounts', $current_user->id, $colname) == '0') {
				$listview_header[] = getTranslatedString($fieldname);
			}
		}

		$accounts_list = array();

		// Get the accounts hierarchy from the top most account in the hierarch of the current account, including the current account
		$encountered_accounts = array($id);
		$accounts_list = $this->__getParentAccounts($id, $accounts_list, $encountered_accounts);

		// Get the accounts hierarchy (list of child accounts) based on the current account
		$accounts_list = $this->__getChildAccounts($id, $accounts_list, $accounts_list[$id]['depth']);

		// Create array of all the accounts in the hierarchy
		foreach ($accounts_list as $account_id => $account_info) {
			$account_info_data = array();

			$hasRecordViewAccess = (is_admin($current_user)) || (isPermitted('Accounts', 'DetailView', $account_id) == 'yes');

			foreach ($this->list_fields_name as $fieldname => $colname) {
				// Permission to view account is restricted, avoid showing field values (except account name)
				if (!$hasRecordViewAccess && $colname != 'accountname') {
					$account_info_data[] = '';
				} elseif (getFieldVisibilityPermission('Accounts', $current_user->id, $colname) == '0') {
					$data = $account_info[$colname];
					if ($colname == 'accountname') {
						if ($account_id != $id) {
							if ($hasRecordViewAccess) {
								$data = '<a href="index.php?module=Accounts&action=DetailView&record='.$account_id.'&parenttab='.$tabname.'">'.$data.'</a>';
							} else {
								$data = '<i>'.$data.'</i>';
							}
						} else {
							$data = '<b>'.$data.'</b>';
						}
						// - to show the hierarchy of the Accounts
						$account_depth = str_repeat(" .. ", $account_info['depth'] * 2);
						$data = $account_depth . $data;
					} elseif ($colname == 'website') {
						$data = '<a href="http://'. $data .'" target="_blank">'.$data.'</a>';
					}
					$account_info_data[] = $data;
				}
			}
			$listview_entries[$account_id] = $account_info_data;
		}

		$account_hierarchy = array('header'=>$listview_header,'entries'=>$listview_entries);
		$log->debug('Exiting getAccountHierarchy method ...');
		return $account_hierarchy;
	}

	/**
	* Function to Recursively get all the upper accounts of a given Account
	* @param  integer   $id      - accountid
	* @param  array   $parent_accounts   - Array of all the parent accounts
	* returns All the parent accounts of the given accountid in array format
	*/
	public function __getParentAccounts($id, &$parent_accounts, &$encountered_accounts) {
		global $log, $adb;
		$log->debug("Entering __getParentAccounts($id,".print_r($parent_accounts, true).') method ...');

		$query = "SELECT parentid FROM vtiger_account " .
				" INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid" .
				" WHERE vtiger_crmentity.deleted = 0 and vtiger_account.accountid = ?";
		$params = array($id);

		$res = $adb->pquery($query, $params);

		if ($adb->num_rows($res) > 0 &&
			$adb->query_result($res, 0, 'parentid') != '' && $adb->query_result($res, 0, 'parentid') != 0 &&
			!in_array($adb->query_result($res, 0, 'parentid'), $encountered_accounts)) {
			$parentid = $adb->query_result($res, 0, 'parentid');
			$encountered_accounts[] = $parentid;
			$this->__getParentAccounts($parentid, $parent_accounts, $encountered_accounts);
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_account.*, vtiger_accountbillads.*," .
				" CASE when (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END as user_name " .
				" FROM vtiger_account" .
				" INNER JOIN vtiger_crmentity " .
				" ON vtiger_crmentity.crmid = vtiger_account.accountid" .
				" INNER JOIN vtiger_accountbillads" .
				" ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid " .
				" LEFT JOIN vtiger_groups" .
				" ON vtiger_groups.groupid = vtiger_crmentity.smownerid" .
				" LEFT JOIN vtiger_users" .
				" ON vtiger_users.id = vtiger_crmentity.smownerid" .
				" WHERE vtiger_crmentity.deleted = 0 and vtiger_account.accountid = ?";
		$params = array($id);
		$res = $adb->pquery($query, $params);

		$parent_account_info = array();
		$depth = 0;
		$immediate_parentid = $adb->query_result($res, 0, 'parentid');
		if (isset($parent_accounts[$immediate_parentid])) {
			$depth = $parent_accounts[$immediate_parentid]['depth'] + 1;
		}
		$parent_account_info['depth'] = $depth;
		foreach ($this->list_fields_name as $columnname) {
			if ($columnname == 'assigned_user_id') {
				$parent_account_info[$columnname] = $adb->query_result($res, 0, 'user_name');
			} else {
				$parent_account_info[$columnname] = $adb->query_result($res, 0, $columnname);
			}
		}
		$parent_accounts[$id] = $parent_account_info;
		$log->debug('Exiting __getParentAccounts method ...');
		return $parent_accounts;
	}

	/**
	* Function to Recursively get all the child accounts of a given Account
	* @param  integer   $id      - accountid
	* @param  array   $child_accounts   - Array of all the child accounts
	* @param  integer   $depth          - Depth at which the particular account has to be placed in the hierarchy
	* returns All the child accounts of the given accountid in array format
	*/
	public function __getChildAccounts($id, &$child_accounts, $depth) {
		global $log, $adb;
		$log->debug("Entering __getChildAccounts($id,".print_r($child_accounts, true).",$depth) method ...");

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_account.*, vtiger_accountbillads.*," .
				" CASE when (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END as user_name " .
				" FROM vtiger_account" .
				" INNER JOIN vtiger_crmentity " .
				" ON vtiger_crmentity.crmid = vtiger_account.accountid" .
				" INNER JOIN vtiger_accountbillads" .
				" ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid " .
				" LEFT JOIN vtiger_groups" .
				" ON vtiger_groups.groupid = vtiger_crmentity.smownerid" .
				" LEFT JOIN vtiger_users" .
				" ON vtiger_users.id = vtiger_crmentity.smownerid" .
				" WHERE vtiger_crmentity.deleted = 0 and parentid = ?";
		$params = array($id);
		$res = $adb->pquery($query, $params);

		$num_rows = $adb->num_rows($res);

		if ($num_rows > 0) {
			$depth = $depth + 1;
			for ($i=0; $i<$num_rows; $i++) {
				$child_acc_id = $adb->query_result($res, $i, 'accountid');
				if (array_key_exists($child_acc_id, $child_accounts)) {
					continue;
				}
				$child_account_info = array();
				$child_account_info['depth'] = $depth;
				foreach ($this->list_fields_name as $columnname) {
					if ($columnname == 'assigned_user_id') {
						$child_account_info[$columnname] = $adb->query_result($res, $i, 'user_name');
					} else {
						$child_account_info[$columnname] = $adb->query_result($res, $i, $columnname);
					}
				}
				$child_accounts[$child_acc_id] = $child_account_info;
				$this->__getChildAccounts($child_acc_id, $child_accounts, $depth);
			}
		}
		$log->debug("Exiting __getChildAccounts method ...");
		return $child_accounts;
	}

	// Function to unlink the dependent records of the given record by id
	public function unlinkDependencies($module, $id) {
		//Deleting Account related Potentials.
		$pot_q = 'SELECT vtiger_crmentity.crmid FROM vtiger_crmentity
			INNER JOIN vtiger_potential ON vtiger_crmentity.crmid=vtiger_potential.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_potential.related_to
			WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.related_to=?';
		$pot_res = $this->db->pquery($pot_q, array($id));
		$pot_ids_list = array();
		for ($k=0; $k < $this->db->num_rows($pot_res); $k++) {
			$pot_id = $this->db->query_result($pot_res, $k, "crmid");
			$pot_ids_list[] = $pot_id;
			$sql = 'UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($pot_id));
		}
		//Backup deleted Account related Potentials.
		$params = array($id, RB_RECORD_UPDATED, 'vtiger_crmentity', 'deleted', 'crmid', implode(",", $pot_ids_list));
		$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Deleting Account related Quotes.
		$quo_q = 'SELECT vtiger_crmentity.crmid FROM vtiger_crmentity
			INNER JOIN vtiger_quotes ON vtiger_crmentity.crmid=vtiger_quotes.quoteid
			INNER JOIN vtiger_account ON vtiger_account.accountid=vtiger_quotes.accountid
			WHERE vtiger_crmentity.deleted=0 AND vtiger_quotes.accountid=?';
		$quo_res = $this->db->pquery($quo_q, array($id));
		$quo_ids_list = array();
		for ($k=0; $k < $this->db->num_rows($quo_res); $k++) {
			$quo_id = $this->db->query_result($quo_res, $k, "crmid");
			$quo_ids_list[] = $quo_id;
			$sql = 'UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($quo_id));
		}
		//Backup deleted Account related Quotes.
		$params = array($id, RB_RECORD_UPDATED, 'vtiger_crmentity', 'deleted', 'crmid', implode(",", $quo_ids_list));
		$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Backup Contact-Account Relation
		$con_q = 'SELECT contactid FROM vtiger_contactdetails WHERE accountid = ?';
		$con_res = $this->db->pquery($con_q, array($id));
		if ($this->db->num_rows($con_res) > 0) {
			$con_ids_list = array();
			for ($k=0; $k < $this->db->num_rows($con_res); $k++) {
				$con_ids_list[] = $this->db->query_result($con_res, $k, "contactid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_contactdetails', 'accountid', 'contactid', implode(",", $con_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);
		}
		//Deleting Contact-Account Relation.
		$con_q = 'UPDATE vtiger_contactdetails SET accountid = 0 WHERE accountid = ?';
		$this->db->pquery($con_q, array($id));

		//Backup Trouble Tickets-Account Relation
		$tkt_q = 'SELECT ticketid FROM vtiger_troubletickets WHERE parent_id = ?';
		$tkt_res = $this->db->pquery($tkt_q, array($id));
		if ($this->db->num_rows($tkt_res) > 0) {
			$tkt_ids_list = array();
			for ($k=0; $k < $this->db->num_rows($tkt_res); $k++) {
				$tkt_ids_list[] = $this->db->query_result($tkt_res, $k, "ticketid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_troubletickets', 'parent_id', 'ticketid', implode(",", $tkt_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);
		}
		//Deleting Trouble Tickets-Account Relation.
		$tt_q = 'UPDATE vtiger_troubletickets SET parent_id = 0 WHERE parent_id = ?';
		$this->db->pquery($tt_q, array($id));

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		if ($return_module == 'Campaigns') {
			$this->db->pquery('DELETE FROM vtiger_campaignaccountrel WHERE accountid=? AND campaignid=?', array($id, $return_id));
		} elseif ($return_module == 'Products') {
			$this->db->pquery('DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?', array($id, $return_id));
		} elseif ($return_module == 'Documents') {
			$this->db->pquery('DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?', array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = $this->db;

		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Products') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_seproductsrel WHERE productid = ? AND crmid = ?', array($with_crmid, $crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$adb->pquery("insert into vtiger_seproductsrel values(?,?,?)", array($crmid, $with_crmid, $module));
			} elseif ($with_module == 'Campaigns') {
				$checkResult = $adb->pquery('SELECT 1 FROM vtiger_campaignaccountrel WHERE campaignid = ? AND accountid = ?', array($with_crmid, $crmid));
				if ($checkResult && $adb->num_rows($checkResult) > 0) {
					continue;
				}
				$adb->pquery("insert into vtiger_campaignaccountrel values(?,?,1)", array($with_crmid, $crmid));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	public function getListButtons($app_strings) {
		$list_buttons = array();

		if (isPermitted('Accounts', 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}
		if (isPermitted('Accounts', 'EditView', '') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings['LBL_MASS_EDIT'];
		}
		return $list_buttons;
	}

	public function get_searchbyemailid($username, $emailaddress) {
		//crm-now added $adb to provide db access
		global $log, $adb, $current_user;
		require_once 'modules/Users/Users.php';
		$seed_user=new Users();
		$user_id=$seed_user->retrieve_user_id($username);
		$current_user=$seed_user;
		$current_user->retrieve_entity_info($user_id, 'Users');
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
		$log->debug('Entering get_searchbyemailid('.$username.','.$emailaddress.') Accounts method ...');
		//get users group ID's
		$gresult = $adb->pquery('SELECT groupid FROM vtiger_users2group WHERE userid=?', array($user_id));
		for ($j=0; $j < $adb->num_rows($gresult); $j++) {
			$groupidlist.=",".$adb->query_result($gresult, $j, 'groupid');
		}
		//crm-now changed query to search in groups too and make only owned contacts available
		$query = "SELECT vtiger_account.accountname, vtiger_account.account_no, vtiger_account.accountid, vtiger_account.email1
					FROM vtiger_account 
					INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid 
					LEFT JOIN vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid  
					LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
					WHERE vtiger_crmentity.deleted=0";
		if (trim($emailaddress) != '') {
			$query .= " AND ((vtiger_account.email1 like '". formatForSqlLike($emailaddress) ."') or vtiger_account.accountname REGEXP REPLACE('"
				.$emailaddress."',' ','|') or vtiger_account.account_no REGEXP REPLACE('".$emailaddress."',' ','|'))  and vtiger_account.email1 != ''";
		} else {
			$query .= " AND (vtiger_account.email1 like '". formatForSqlLike($emailaddress) ."' and vtiger_account.email1 != '')";
		}
		if (isset($groupidlist)) {
			$query .= " AND (vtiger_users.user_name='".$username."' OR vtiger_crmentity.smownerid IN (".substr($groupidlist, 1)."))";
		} else {
			$query .= " AND vtiger_users.user_name='".$username."'";
		}

		$tab_id = getTabid('Accounts');
		if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3) {
			$sec_parameter=getListViewSecurityParameter('Accounts');
			$query .= $sec_parameter;
		}

		$log->debug('Exiting get_searchbyemailid method ...');
		return $this->plugin_process_list_query($query);
	}

	public function plugin_process_list_query($query) {
		global $log,$adb,$current_user, $currentModule;
		$log->debug('Entering process_list_query1('.$query.') method ...');
		$permitted_field_lists = array();
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = 'select columnname from vtiger_field where tabid=6 and block <> 75 and vtiger_field.presence in (0,2)';
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'select columnname
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=6 and vtiger_field.block <> 6 and vtiger_field.block <> 75 and vtiger_field.displaytype in (1,2,4,3)
					and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
				$params1[] = $profileList;
			}
		}
		$result1 = $this->db->pquery($sql1, $params1);
		for ($i=0; $i < $adb->num_rows($result1); $i++) {
			$permitted_field_lists[] = $adb->query_result($result1, $i, 'columnname');
		}

		$result = $this->db->query($query, true, "Error retrieving $currentModule list: ");
		$list = array();
		$rows_found =  $this->db->getRowCount($result);
		if ($rows_found != 0) {
			for ($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found; $index++, $row = $this->db->fetchByAssoc($result, $index)) {
				$account = array();
				$account[accountname] = in_array("accountname", $permitted_field_lists) ? $row[accountname] : "";
				$account[account_no] = in_array("account_no", $permitted_field_lists)? $row[account_no] : "";
				$account[email1] = in_array("email1", $permitted_field_lists) ? $row[email1] : "";
				$account[accountid] =  $row[accountid];
				$list[] = $account;
			}
		}

		$response = array();
		$response['list'] = $list;
		$response['row_count'] = $rows_found;
		//$response['next_offset'] = $next_offset;
		//$response['previous_offset'] = $previous_offset;
		$log->debug("Exiting process_list_query1 method ...");
		return $response;
	}

	public function getvtlib_open_popup_window_function($fieldname, $basemodule) {
		if ($basemodule=='Issuecards') {
			return 'set_return_shipbilladdress';
		} else {
			return 'vtlib_open_popup_window';
		}
	}
}
?>
