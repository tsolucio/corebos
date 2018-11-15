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
require_once 'modules/Calendar/Activity.php';
require_once 'modules/Campaigns/Campaigns.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Emails/Emails.php';
require 'user_privileges/default_module_view.php';

class Leads extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_leaddetails';
	public $table_index= 'leadid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;

	public $tab_name = array('vtiger_crmentity','vtiger_leaddetails','vtiger_leadsubdetails','vtiger_leadaddress','vtiger_leadscf');
	public $tab_name_index = array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_leaddetails'=>'leadid',
		'vtiger_leadsubdetails'=>'leadsubscriptionid',
		'vtiger_leadaddress'=>'leadaddressid',
		'vtiger_leadscf'=>'leadid'
	);

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_leadscf', 'leadid');
	public $related_tables = array(
		'vtiger_leadsubdetails' => array('leadsubscriptionid', 'vtiger_leaddetails', 'leadid'),
		'vtiger_leadaddress'    => array('leadaddressid', 'vtiger_leaddetails', 'leadid'),
	);

	public $sortby_fields = array('lastname','firstname','email','phone','company','smownerid','website');

	// This is the list of vtiger_fields that are in the lists.
	public $list_fields = array(
		'Last Name'=>array('leaddetails'=>'lastname'),
		'First Name'=>array('leaddetails'=>'firstname'),
		'Company'=>array('leaddetails'=>'company'),
		'Phone'=>array('leadaddress'=>'phone'),
		'Website'=>array('leadsubdetails'=>'website'),
		'Email'=>array('leaddetails'=>'email'),
		'Assigned To'=>array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		'Last Name'=>'lastname',
		'First Name'=>'firstname',
		'Company'=>'company',
		'Phone'=>'phone',
		'Website'=>'website',
		'Email'=>'email',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'lastname';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Name'=>array('leaddetails'=>'lastname'),
		'Company'=>array('leaddetails'=>'company')
	);
	public $search_fields_name = array(
		'Name'=>'lastname',
		'Company'=>'company'
	);

	// For Popup window record selection
	public $popup_fields = array('lastname');

	public $required_fields = array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('lastname', 'createdtime' ,'modifiedtime');

	//Added these variables which are used as default order by and sortorder in ListView
	public $default_order_by = 'lastname';
	public $default_sort_order='ASC';

	// For Alphabetical search
	public $def_basicsearch_col = 'lastname';

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Leads Query.
	*/
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Leads", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT $fields_list,case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			FROM vtiger_crmentity
			INNER JOIN vtiger_leaddetails ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid
			LEFT JOIN vtiger_leadsubdetails ON vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid
			LEFT JOIN vtiger_leadaddress ON vtiger_leaddetails.leadid=vtiger_leadaddress.leadaddressid
			LEFT JOIN vtiger_leadscf ON vtiger_leadscf.leadid=vtiger_leaddetails.leadid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";
		$query .= $this->getNonAdminAccessControlQuery('Leads', $current_user);
		$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
		$where_auto = " vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted =$val_conv";

		if ($where != '') {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= " where ".$where_auto;
		}
		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	/** Returns a list of the associated Campaigns
	 * @param $id -- campaign id :: Type Integer
	 * @returns list of campaigns in array format
	 */
	public function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
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

		$button = '';

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',".
					"'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT').' '.
					getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name ,
			vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus,
			vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_crmentity.modifiedtime from vtiger_campaign
			inner join vtiger_campaignleadrel on vtiger_campaignleadrel.campaignid=vtiger_campaign.campaignid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_campaign.campaignid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			where vtiger_campaignleadrel.leadid=".$id." and vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_campaigns method ...");
		return $return_value;
	}

	/**
	* Function to get lead related Products
	* @param  integer   $id      - leadid
	* returns related Products record in array format
	*/
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',".
					"'width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT').' '.
					getTranslatedString($related_module, $related_module) ."'>&nbsp;";
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
				INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid and vtiger_seproductsrel.setype = 'Leads'
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
				INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_seproductsrel.crmid
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.leadid = $id";
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	}

	/** Function to get the Combo List Values of Leads Field
	 * @param string $list_option
	 * Returns Combo List Options
	*/
	public function get_lead_field_options($list_option) {
		global $log;
		$log->debug('Entering get_lead_field_options('.$list_option.') method ...');
		$comboFieldArray = getComboArray($this->combofieldNames);
		$log->debug('Exiting get_lead_field_options method ...');
		return $comboFieldArray[$list_option];
	}

	/** Function to get the Columnnames of the Leads Record
	* Used By vtigerCRM Word Plugin
	* Returns the Merge Fields for Word Plugin
	*/
	public function getColumnNames_Lead() {
		global $log,$current_user;
		$log->debug("Entering getColumnNames_Lead() method ...");
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = "select fieldlabel from vtiger_field where tabid=7 and vtiger_field.presence in (0,2)";
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'select vtiger_field.fieldid,fieldlabel
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=7 and vtiger_field.displaytype in (1,2,3,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0
					and vtiger_field.presence in (0,2)';
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .") group by fieldid";
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
		$log->debug("Exiting getColumnNames_Lead method ...");
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
		$rel_table_arr = array("Attachments"=>"vtiger_seattachmentsrel",
					"Products"=>"vtiger_seproductsrel","Campaigns"=>"vtiger_campaignleadrel");

		$tbl_field_arr = array("vtiger_seattachmentsrel"=>"attachmentsid",
					"vtiger_seproductsrel"=>"productid","vtiger_campaignleadrel"=>"campaignid");

		$entity_tbl_field_arr = array("vtiger_seattachmentsrel"=>"crmid",
					"vtiger_seproductsrel"=>"crmid","vtiger_campaignleadrel"=>"leadid");

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
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryPlanner, $type, $where_condition);
		if ($queryPlanner->requireTable("vtiger_leadaddress")) {
			$query .= " left join vtiger_leadaddress on vtiger_leaddetails.leadid = vtiger_leadaddress.leadaddressid";
		}
		if ($queryPlanner->requireTable("vtiger_leadsubdetails")) {
			$query .= " left join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid";
		}
		if ($queryPlanner->requireTable("vtiger_email_trackLeads")) {
			$query .= " LEFT JOIN vtiger_email_track AS vtiger_email_trackLeads ON vtiger_email_trackLeads.crmid = vtiger_leaddetails.leadid";
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
			"Calendar" => array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_leaddetails"=>"leadid"),
			"Products" => array("vtiger_seproductsrel"=>array("crmid","productid"),"vtiger_leaddetails"=>"leadid"),
			"Campaigns" => array("vtiger_campaignleadrel"=>array("leadid","campaignid"),"vtiger_leaddetails"=>"leadid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_leaddetails"=>"leadid"),
			"Services" => array("vtiger_crmentityrel"=>array("crmid","relcrmid"),"vtiger_leaddetails"=>"leadid"),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}

		if ($return_module == 'Campaigns') {
			$sql = 'DELETE FROM vtiger_campaignleadrel WHERE leadid=? AND campaignid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Products') {
			$sql = 'DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public function getListButtons($app_strings) {
		$list_buttons = array();

		if (isPermitted('Leads', 'Delete', '') == 'yes') {
			$list_buttons['del'] =	$app_strings['LBL_MASS_DELETE'];
		}
		if (isPermitted('Leads', 'EditView', '') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings['LBL_MASS_EDIT'];
		}
		return $list_buttons;
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();
		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Products') {
				$adb->pquery('insert into vtiger_seproductsrel values (?,?,?)', array($crmid, $with_crmid, $module));
			} elseif ($with_module == 'Campaigns') {
				$adb->pquery('insert into vtiger_campaignleadrel values(?,?,1)', array($with_crmid, $crmid));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
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
		$log->debug("Entering get_searchbyemailid(".$username.",".$emailaddress.") Leads method ...");
		//get users group ID's
		$gquery = 'SELECT groupid FROM vtiger_users2group WHERE userid=?';
		$gresult = $adb->pquery($gquery, array($user_id));
		for ($j=0; $j < $adb->num_rows($gresult); $j++) {
			$groupidlist.=",".$adb->query_result($gresult, $j, 'groupid');
		}
		//crm-now changed query to search in groups too and make only owned contacts available
		$query = 'SELECT vtiger_leaddetails.lastname, vtiger_leaddetails.firstname, vtiger_leaddetails.leadid, vtiger_leaddetails.email, vtiger_leaddetails.company
			FROM vtiger_leaddetails
			INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid
			LEFT JOIN vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted=0';
		if (trim($emailaddress) != '') {
			$query .= " AND ((vtiger_leaddetails.email like '". formatForSqlLike($emailaddress) ."') or vtiger_leaddetails.lastname REGEXP REPLACE('".$emailaddress.
				"',' ','|') or vtiger_leaddetails.firstname REGEXP REPLACE('".$emailaddress."',' ','|'))  and vtiger_leaddetails.email != ''";
		} else {
			$query .= " AND (vtiger_leaddetails.email like '". formatForSqlLike($emailaddress) ."' and vtiger_leaddetails.email != '')";
		}
		if (isset($groupidlist)) {
			$query .= " AND (vtiger_users.user_name='".$username."' OR vtiger_crmentity.smownerid IN (".substr($groupidlist, 1)."))";
		} else {
			$query .= " AND vtiger_users.user_name='".$username."'";
		}

		$tab_id = getTabid('Leads');
		if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3) {
			$sec_parameter=getListViewSecurityParameter('Leads');
			$query .= $sec_parameter;
		}

		$log->debug('Exiting get_searchbyemailid method ...');
		return $this->plugin_process_list_query($query);
	}

	public function plugin_process_list_query($query) {
		global $log,$adb,$current_user, $currentModule;
		$log->debug('Entering process_list_query('.$query.') method ...');
		$permitted_field_lists = array();
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			$sql1 = 'select columnname from vtiger_field where tabid=7 and block <> 75 and vtiger_field.presence in (0,2)';
			$params1 = array();
		} else {
			$profileList = getCurrentUserProfileList();
			$sql1 = 'select columnname
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=7 and vtiger_field.block <> 6 and vtiger_field.block <> 75 and vtiger_field.displaytype in (1,2,4,3)
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
				$lead = array();

				$lead[lastname] = in_array("lastname", $permitted_field_lists) ? $row[lastname] : "";
				$lead[firstname] = in_array("firstname", $permitted_field_lists)? $row[firstname] : "";
				$lead[email] = in_array("email", $permitted_field_lists) ? $row[email] : "";
				$lead[leadid] =  $row[leadid];
				$lead[company] = in_array("company", $permitted_field_lists) ? $row[company] : "";
				$list[] = $lead;
			}
		}

		$response = array();
		$response['list'] = $list;
		$response['row_count'] = $rows_found;
		//$response['next_offset'] = $next_offset;
		//$response['previous_offset'] = $previous_offset;
		$log->debug('Exiting process_list_query method ...');
		return $response;
	}
}
?>
