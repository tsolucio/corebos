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
require('user_privileges/default_module_view.php');

class HelpDesk extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_troubletickets';
	var $table_index= 'ticketid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_ticketcf', 'ticketid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity','vtiger_troubletickets','vtiger_ticketcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_troubletickets'=>'ticketid',
		'vtiger_ticketcf'=>'ticketid',
		'vtiger_ticketcomments'=>'ticketid');
	var $entity_table = 'vtiger_crmentity';

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Ticket No'=>Array('troubletickets'=>'ticket_no'),
		'Subject'=>Array('troubletickets'=>'title'),
		'Related to'=>Array('troubletickets'=>'parent_id'),
		'Status'=>Array('troubletickets'=>'status'),
		'Priority'=>Array('troubletickets'=>'priority'),
		'Assigned To'=>Array('crmentity' => 'smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Ticket No'=>'ticket_no',
		'Subject'=>'ticket_title',
		'Related to'=>'parent_id',
		'Status'=>'ticketstatus',
		'Priority'=>'ticketpriorities',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field= 'ticket_title';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Ticket No' =>Array('vtiger_troubletickets'=>'ticket_no'),
		'Title' => Array('vtiger_troubletickets'=>'title')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Ticket No' => 'ticket_no',
		'Title'=>'ticket_title',
	);

	// For Popup window record selection
	var $popup_fields = Array('ticket_title');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array('title','status','priority','crmid','firstname','smownerid');

	// For Alphabetical search
	var $def_basicsearch_col = 'ticket_title';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'ticket_title';

	// Required Information for enabling Import feature
	var $required_fields = array();

	var $default_order_by = 'title';
	var $default_sort_order = 'DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id', 'createdtime', 'modifiedtime', 'ticket_title', 'update_log');

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

	function save($module, $fileid = '') {
		global $adb;
		if (!empty($this->id)) {
			$adb->pquery("update vtiger_troubletickets set commentadded='0' where ticketid=?",array($this->id));
		}
		$this->column_fields['commentadded'] = '0';
		parent::save($module, $fileid);
	}

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		if(isset($_REQUEST['mode']) && $_REQUEST['mode'] =='Import')
			$this->column_fields['comments'] = '';
		//Inserting into Ticket Comment Table
		$this->insertIntoTicketCommentTable();

		//Inserting into vtiger_attachments
		$this->insertIntoAttachment($this->id,$module);

		//service contract update
		$return_action = $_REQUEST['return_action'];
		$for_module = $_REQUEST['return_module'];
		$for_crmid = $_REQUEST['return_id'];
		if ($return_action && $for_module && $for_crmid) {
			if ($for_module == 'ServiceContracts') {
				$on_focus = CRMEntity::getInstance($for_module);
				$on_focus->save_related_module($for_module, $for_crmid, $module, $this->id);
			}
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmid) {
		parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		if ($with_module == 'ServiceContracts') {
			$serviceContract = CRMEntity::getInstance("ServiceContracts");
			$serviceContract->updateHelpDeskRelatedTo($with_crmid,$crmid);
			$serviceContract->updateServiceContractState($with_crmid);
		}
	}

	// Function to insert values in ticketcomments
	function insertIntoTicketCommentTable() {
		global $log, $adb, $current_user;
		$log->info('in insertIntoTicketCommentTable');

		$current_time = $adb->formatDate(date('Y-m-d H:i:s'), true);
		if ($this->column_fields['from_portal'] != 1) {
			$ownertype = 'user';
			$ownerId = $current_user->id;
		} else {
			$ownertype = 'customer';
			$ownerId = $this->column_fields['parent_id'];
		}

		$comment = $this->column_fields['comments'];
		if ($comment != '') {
			$sql = "insert into vtiger_ticketcomments (ticketid,comments,ownerid,ownertype,createdtime) values(?,?,?,?,?)";
			$params = array($this->id, from_html($comment), $ownerId, $ownertype, $current_time);
			$adb->pquery($sql, $params);
			$adb->pquery("update vtiger_troubletickets set commentadded='1' where ticketid=?",array($this->id));
			$this->column_fields['commentadded'] = '1';
		}
	}

	/**
	 * This function is used to add the vtiger_attachments. This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 * @param int $id - entity id to which the files to be uploaded
	 * @param string $module - the current module name
	*/
	function insertIntoAttachment($id,$module, $direct_import=false)
	{
		global $log, $adb;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;

		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/** Function to form the query to get the list of activities
	 * @param int $id - ticket id
	 * @return array - return an array which will be returned from the function GetRelatedList
	 **/
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
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_EVENT', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EventEditView\";this.form.module.value=\"Calendar4You\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name," .
					" vtiger_activity.*, vtiger_cntactivityrel.contactid, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname," .
					" vtiger_crmentity.crmid, vtiger_recurringevents.recurringtype, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime," .
					" vtiger_seactivityrel.crmid as parent_id " .
					" from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid" .
					" inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid" .
					" left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid = vtiger_activity.activityid " .
					" left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid" .
					" left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid" .
					" left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid" .
					" left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid" .
					" where vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted=0 and (activitytype NOT IN ('Emails'))" .
							" AND ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Completed' )" .
							" and ( vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus != 'Held') ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/** Function to get the Ticket History information as in array format
	 *	@param int $ticketid - ticket id
	 *	@return array - return an array with title and the ticket history informations in the following format
				array(
					header=>array('0'=>'title'),
					entries=>array('0'=>'info1','1'=>'info2',etc.,)
				)
	 */
	function get_ticket_history($ticketid)
	{
		global $log, $adb;
		$log->debug("Entering into get_ticket_history($ticketid) method ...");

		$query="select title,update_log from vtiger_troubletickets where ticketid=?";
		$result=$adb->pquery($query, array($ticketid));
		$update_log = $adb->query_result($result,0,"update_log");

		$splitval = explode('--//--',trim($update_log,'--//--'));

		$header[] = $adb->query_result($result,0,"title");

		$return_value = Array('header'=>$header,'entries'=>$splitval);

		$log->debug("Exiting from get_ticket_history($ticketid) method ...");

		return $return_value;
	}

	/**	Function to get the ticket comments as a array
	 *	@param  int   $ticketid - ticketid
	 *	@return array $output - array(
						[$i][comments]    => comments
						[$i][owner]       => name of the user or customer who made the comment
						[$i][createdtime] => the comment created time
					)
				where $i = 0,1,..n which are all made for the ticket
	**/
	function get_ticket_comments_list($ticketid)
	{
		global $log;
		$log->debug("Entering get_ticket_comments_list(".$ticketid.") method ...");
		 $sql = "select * from vtiger_ticketcomments where ticketid=? order by createdtime DESC";
		 $result = $this->db->pquery($sql, array($ticketid));
		 $noofrows = $this->db->num_rows($result);
		 for($i=0;$i<$noofrows;$i++)
		 {
			 $ownerid = $this->db->query_result($result,$i,"ownerid");
			 $ownertype = $this->db->query_result($result,$i,"ownertype");
			 if($ownertype == 'user')
				 $name = getUserFullName($ownerid);
			 elseif($ownertype == 'customer')
			 {
				 $sql1 = 'select * from vtiger_portalinfo where id=?';
				 $name = $this->db->query_result($this->db->pquery($sql1, array($ownerid)),0,'user_name');
			 }

			 $output[$i]['comments'] = nl2br($this->db->query_result($result,$i,"comments"));
			 $output[$i]['owner'] = $name;
			 $output[$i]['createdtime'] = $this->db->query_result($result,$i,"createdtime");
		 }
		$log->debug("Exiting get_ticket_comments_list method ...");
		 return $output;
	 }

	/**	Function to get the HelpDesk field labels in caps letters without space
	 *	@return array $mergeflds - array(	key => val	)    where   key=0,1,2..n & val = ASSIGNEDTO,RELATEDTO, .,etc
	**/
	function getColumnNames_Hd()
	{
		global $log,$current_user;
		$log->debug("Entering getColumnNames_Hd() method ...");
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
		{
			$sql1 = "select fieldlabel from vtiger_field where tabid=13 and block <> 30 and vtiger_field.uitype <> '61' and vtiger_field.presence in (0,2)";
			$params1 = array();
		}else
		{
			$profileList = getCurrentUserProfileList();
			$sql1 = "select vtiger_field.fieldid,fieldlabel from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=13 and vtiger_field.block <> 30 and vtiger_field.uitype <> '61' and vtiger_field.displaytype in (1,2,3,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
			$params1 = array();
			if (count($profileList) > 0) {
				$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .") group by fieldid";
				array_push($params1, $profileList);
			}
		}
		$result = $this->db->pquery($sql1, $params1);
		$numRows = $this->db->num_rows($result);
		$custom_fields = array();
		for($i=0; $i < $numRows;$i++)
		{
			$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
			$custom_fields[$i] = preg_replace("/\s+/","",$custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug("Exiting getColumnNames_Hd method ...");
		return $mergeflds;
	}

	/** Function to get the list of comments for the given ticket id
	 * @param  int  $ticketid - Ticket id
	 * @return list $list - return the list of comments and comment informations as a html output where as these comments and comments informations will be formed in div tag.
	**/
	function getCommentInformation($ticketid)
	{
		global $log, $adb, $mod_strings, $default_charset;
		$log->debug("Entering getCommentInformation(".$ticketid.") method ...");

		$sql = "select * from vtiger_ticketcomments where ticketid=?";
		$result = $adb->pquery($sql, array($ticketid));
		$noofrows = $adb->num_rows($result);

		//In ajax save we should not add this div
		$list = '';
		if($_REQUEST['action'] != 'HelpDeskAjax')
		{
			$list .= '<div id="comments_div" style="overflow: auto;height:200px;width:100%;">';
			$enddiv = '</div>';
		}
		for($i=0;$i<$noofrows;$i++)
		{
			if($adb->query_result($result,$i,'comments') != '')
			{
				//this div is to display the comment
				$comment = $adb->query_result($result,$i,'comments');
				// Asha: Fix for ticket #4478 . Need to escape html tags during ajax save.
				if($_REQUEST['action'] == 'HelpDeskAjax') {
					$comment = htmlentities($comment, ENT_QUOTES, $default_charset);
				}
				$list .= '<div valign="top" style="width:99%;padding-top:10px;" class="dataField">';
				$list .= make_clickable(nl2br($comment));

				$list .= '</div>';

				//this div is to display the author and time
				$list .= '<div valign="top" style="width:99%;border-bottom:1px dotted #CCCCCC;padding-bottom:5px;" class="dataLabel"><font color=darkred>';
				$list .= $mod_strings['LBL_AUTHOR'].' : ';

				if($adb->query_result($result,$i,'ownertype') == 'user')
					$list .= getUserFullName($adb->query_result($result,$i,'ownerid'));
				elseif($adb->query_result($result,$i,'ownertype') == 'customer') {
					$contactid = $adb->query_result($result,$i,'ownerid');
					$displayValueArray = getEntityName('Contacts', $contactid);
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $key => $field_value) {
							$contact_name = $field_value;
						}
					} else {
						$contact_name='';
					}
					$list .= $contact_name;
				}
				$date = new DateTimeField($adb->query_result($result,$i,'createdtime'));
				$list .= ' on '.$date->getDisplayDateTimeValue().' &nbsp;';

				$list .= '</font></div>';
			}
		}

		$list .= $enddiv;

		$log->debug("Exiting getCommentInformation method ...");
		return $list;
	}

	/** Function to get the Customer Name who has made comment to the ticket from the customer portal
	 * @param  int    $id   - Ticket id
	 * @return string $customername - The contact name
	**/
	function getCustomerName($id)
	{
		global $log, $adb;
		$log->debug("Entering getCustomerName(".$id.") method ...");
		$sql = "select * from vtiger_portalinfo inner join vtiger_troubletickets on vtiger_troubletickets.parent_id = vtiger_portalinfo.id where vtiger_troubletickets.ticketid=?";
		$result = $adb->pquery($sql, array($id));
		$customername = $adb->query_result($result,0,'user_name');
		$log->debug("Exiting getCustomerName method ...");
		return $customername;
	}
	//Pavani: Function to create, export query for helpdesk module
	/** Function to export the ticket records in CSV Format
	 * @param reference variable - where condition is passed when the query is executed
	 * Returns Export Tickets Query.
	 */
	function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("HelpDesk", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		//Ticket changes--5198
		$fields_list = 	str_replace(",vtiger_ticketcomments.comments as 'Add Comment'",' ',$fields_list);

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
					'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT $fields_list,case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			FROM ".$this->entity_table. "
			INNER JOIN vtiger_troubletickets ON vtiger_troubletickets.ticketid =vtiger_crmentity.crmid
			LEFT JOIN vtiger_crmentity vtiger_crmentityRelatedTo ON vtiger_crmentityRelatedTo.crmid = vtiger_troubletickets.parent_id
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_troubletickets.parent_id
			LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id
			LEFT JOIN vtiger_ticketcf ON vtiger_ticketcf.ticketid=vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid and vtiger_users.status='Active'
			LEFT JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid =vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_attachments ON vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
			LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_troubletickets.product_id";
		$query .= getNonAdminAccessControlQuery('HelpDesk',$current_user);
		$where_auto=" vtiger_crmentity.deleted = 0 ";

		if($where != "")
			$query .= " WHERE ($where) AND ".$where_auto;
		else
			$query .= " WHERE ".$where_auto;

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}


	/**	Function used to get the Activity History
	 *	@param	int	$id - ticket id to which we want to display the activity history
	 *	@return array	- return an array which will be returned from the function getHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
						'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status, vtiger_activity.eventstatus, vtiger_activity.date_start, vtiger_activity.due_date,vtiger_activity.time_start,vtiger_activity.time_end,vtiger_activity.activitytype, vtiger_troubletickets.ticketid, vtiger_troubletickets.title, vtiger_crmentity.modifiedtime,vtiger_crmentity.createdtime, vtiger_crmentity.description,
			case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			from vtiger_activity
			inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid= vtiger_activity.activityid
			inner join vtiger_troubletickets on vtiger_troubletickets.ticketid = vtiger_seactivityrel.crmid
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where (vtiger_activity.activitytype != 'Emails')
			and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
			and vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Entering get_history method ...");
		return getHistory('HelpDesk',$query,$id);
	}

	/** Function to get the update ticket history for the specified ticketid
	 * @param $id -- $ticketid:: Type Integer
	 */
	function constructUpdateLog($focus, $mode, $assigned_group_name, $assigntype) {
		global $adb, $current_user;
		if($mode != 'edit') {
			$updatelog = self::getUpdateLogCreateMessage($focus->column_fields, $assigned_group_name, $assigntype);
		} else {
			$updatelog = self::getUpdateLogEditMessage($focus->id,$focus->column_fields);
		}
		return $updatelog;
	}

	public static function getUpdateLogCreateMessage($column_fields, $assigned_group_name, $assigntype) {
		global $log,$current_user;
		$updatelog = "Ticket created. Assigned to ";

		if(!empty($assigned_group_name) && $assigntype == 'T')
		{
			$updatelog .= " group ".(is_array($assigned_group_name)? $assigned_group_name[0] : $assigned_group_name);
		}
		elseif($column_fields['assigned_user_id'] != '')
		{
			$updatelog .= " user ".getUserFullName($column_fields['assigned_user_id']);
		}
		else
		{
			$updatelog .= " user ".getUserFullName($current_user->id);
		}

		$fldvalue = date("l dS F Y h:i:s A").' by '.$current_user->user_name;
		$updatelog .= " -- ".$fldvalue."--//--";
		return $updatelog;
	}

	public static function getUpdateLogEditMessage($ticketid,$column_fields) {
		global $adb,$log,$current_user;
		//First retrieve the existing information
		$tktresult = $adb->pquery("select * from vtiger_troubletickets where ticketid=?", array($ticketid));
		$crmresult = $adb->pquery("select * from vtiger_crmentity where crmid=?", array($ticketid));

		$updatelog = decode_html($adb->query_result($tktresult,0,"update_log"));

		$old_owner_id = $adb->query_result($crmresult,0,"smownerid");
		$old_status = $adb->query_result($tktresult,0,"status");
		$old_priority = $adb->query_result($tktresult,0,"priority");
		$old_severity = $adb->query_result($tktresult,0,"severity");
		$old_category = $adb->query_result($tktresult,0,"category");

		//Assigned to change log
		if($column_fields['assigned_user_id'] != $old_owner_id)
		{
			$owner_name = getOwnerName($column_fields['assigned_user_id']);
			if($assigntype == 'T')
				$updatelog .= ' Transferred to group '.$owner_name.'\.';
			else
				$updatelog .= ' Transferred to user '.decode_html($owner_name).'\.'; // Need to decode UTF characters which are migrated from versions < 5.0.4.
		}
		//Status change log
		if($old_status != $column_fields['ticketstatus'] && $column_fields['ticketstatus'] != '')
		{
			$updatelog .= ' Status Changed to '.$column_fields['ticketstatus'].'\.';
		}
		//Priority change log
		if($old_priority != $column_fields['ticketpriorities'] && $column_fields['ticketpriorities'] != '')
		{
			$updatelog .= ' Priority Changed to '.$column_fields['ticketpriorities'].'\.';
		}
		//Severity change log
		if($old_severity != $column_fields['ticketseverities'] && $column_fields['ticketseverities'] != '')
		{
			$updatelog .= ' Severity Changed to '.$column_fields['ticketseverities'].'\.';
		}
		//Category change log
		if($old_category != $column_fields['ticketcategories'] && $column_fields['ticketcategories'] != '')
		{
			$updatelog .= ' Category Changed to '.$column_fields['ticketcategories'].'\.';
		}

		$updatelog .= ' -- '.date("l dS F Y h:i:s A").' by '.$current_user->user_name.'--//--';
		return $updatelog;
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

		$rel_table_arr = Array("Activities"=>"vtiger_seactivityrel","Attachments"=>"vtiger_seattachmentsrel","Documents"=>"vtiger_senotesrel");

		$tbl_field_arr = Array("vtiger_seactivityrel"=>"activityid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_senotesrel"=>"notesid");

		$entity_tbl_field_arr = Array("vtiger_seactivityrel"=>"crmid","vtiger_seattachmentsrel"=>"crmid","vtiger_senotesrel"=>"crmid");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result = $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
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
		$log->debug("Exiting transferRelatedRecords...");
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_troubletickets","ticketid");
		$query .=" left join vtiger_crmentity as vtiger_crmentityHelpDesk on vtiger_crmentityHelpDesk.crmid=vtiger_troubletickets.ticketid and vtiger_crmentityHelpDesk.deleted=0
				left join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
				left join vtiger_crmentity as vtiger_crmentityRelHelpDesk on vtiger_crmentityRelHelpDesk.crmid = vtiger_troubletickets.parent_id
				left join vtiger_account as vtiger_accountRelHelpDesk on vtiger_accountRelHelpDesk.accountid=vtiger_crmentityRelHelpDesk.crmid
				left join vtiger_contactdetails as vtiger_contactdetailsRelHelpDesk on vtiger_contactdetailsRelHelpDesk.contactid= vtiger_crmentityRelHelpDesk.crmid
				left join vtiger_products as vtiger_productsRel on vtiger_productsRel.productid = vtiger_troubletickets.product_id
				left join vtiger_groups as vtiger_groupsHelpDesk on vtiger_groupsHelpDesk.groupid = vtiger_crmentityHelpDesk.smownerid
				left join vtiger_users as vtiger_usersHelpDesk on vtiger_usersHelpDesk.id = vtiger_crmentityHelpDesk.smownerid
				left join vtiger_users as vtiger_lastModifiedByHelpDesk on vtiger_lastModifiedByHelpDesk.id = vtiger_crmentityHelpDesk.modifiedby ";
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" => array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_troubletickets"=>"ticketid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_troubletickets"=>"ticketid"),
			"Products" => array("vtiger_troubletickets"=>array("ticketid","product_id")),
			"Services" => array("vtiger_crmentityrel"=>array("crmid","relcrmid"),"vtiger_troubletickets"=>"ticketid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Contacts' || $return_module == 'Accounts') {
			$sql = 'UPDATE vtiger_troubletickets SET parent_id=? WHERE ticketid=?';
			$this->db->pquery($sql, array(null, $id));
			$se_sql= 'DELETE FROM vtiger_seticketsrel WHERE ticketid=?';
			$this->db->pquery($se_sql, array($id));
		} elseif($return_module == 'Products') {
			$sql = 'UPDATE vtiger_troubletickets SET product_id=? WHERE ticketid=?';
			$this->db->pquery($sql, array(null, $id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	public static function getTicketEmailContents($entityData) {
	 $adb = PearDatabase::getInstance();
		$moduleName = $entityData->getModuleName();
		$wsId = $entityData->getId();
		$parts = explode('x', $wsId);
		$entityId = $parts[1];

		$isNew = $entityData->isNew();

		if (!$isNew) {
			$reply = getTranslatedString("replied", $moduleName);
			$temp = getTranslatedString("Re", $moduleName);
		} else {
			$reply = getTranslatedString("created", $moduleName);
			$temp = " ";
		}

		$wsParentId = $entityData->get('parent_id');
		$parentIdParts = explode('x', $wsParentId);
		$parentId = $parentIdParts[1];
		$desc = getTranslatedString('Ticket ID', $moduleName) . ' : ' . $entityId . '<br>'
				. getTranslatedString('Ticket Title', $moduleName) . ' : ' . $temp . ' '
				. $entityData->get('ticket_title');
		$desc .= "<br><br>" . getTranslatedString('Hi', $moduleName) . " " . getParentName($parentId) . ",<br><br>"
				. getTranslatedString('LBL_PORTAL_BODY_MAILINFO', $moduleName) . " " . $reply . " " . getTranslatedString('LBL_DETAIL', $moduleName) . "<br>";
		$desc .= "<br>" . getTranslatedString('Ticket No', $moduleName) . " : " . $entityData->get('ticket_no');
		$desc .= "<br>" . getTranslatedString('Status', $moduleName) . " : " . $entityData->get('ticketstatus');
		$desc .= "<br>" . getTranslatedString('Category', $moduleName) . " : " . $entityData->get('ticketcategories');
		$desc .= "<br>" . getTranslatedString('Severity', $moduleName) . " : " . $entityData->get('ticketseverities');
		$desc .= "<br>" . getTranslatedString('Priority', $moduleName) . " : " . $entityData->get('ticketpriorities');
		$desc .= "<br><br>" . getTranslatedString('Description', $moduleName) . " : <br>" . $entityData->get('description');
		$desc .= "<br><br>" . getTranslatedString('Solution', $moduleName) . " : <br>" . $entityData->get('solution');
		$desc .= getTicketComments($entityId);

		$sql = "SELECT * FROM vtiger_ticketcf WHERE ticketid = ?";
		$result = $adb->pquery($sql, array($entityId));
		$cffields = $adb->getFieldsArray($result);
		foreach ($cffields as $cfOneField) {
			if ($cfOneField != 'ticketid') {
				$cfData = $adb->query_result($result, 0, $cfOneField);
				$sql = "SELECT fieldlabel FROM vtiger_field WHERE columnname = ? and vtiger_field.presence in (0,2)";
				$cfLabel = $adb->query_result($adb->pquery($sql, array($cfOneField)), 0, 'fieldlabel');
				$desc .= '<br><br>' . $cfLabel . ' : <br>' . $cfData;
			}
		}
		// end of contribution
		$desc .= '<br><br><br>';
		$desc .= '<br>' . getTranslatedString("LBL_REGARDS", $moduleName) . ',<br>' . getTranslatedString("LBL_TEAM", $moduleName) . '.<br>';
		return $desc;
	}

	public static function getPortalTicketEmailContents($entityData) {
		$moduleName = $entityData->getModuleName();
		$wsId = $entityData->getId();
		$parts = explode('x', $wsId);
		$entityId = $parts[1];

		$wsParentId = $entityData->get('parent_id');
		$parentIdParts = explode('x', $wsParentId);
		$parentId = $parentIdParts[1];
		$PORTAL_URL = GlobalVariable::getVariable('Application_Customer_Portal_URL','http://your_support_domain.tld/customerportal');
		$portalUrl = "<a href='" . $PORTAL_URL . "/index.php?module=HelpDesk&action=index&ticketid=" . $entityId . "&fun=detail'>"
				. getTranslatedString('LBL_TICKET_DETAILS', $moduleName) . "</a>";
		$contents = getTranslatedString('Dear', $moduleName) . " " . getParentName(parentId) . ",<br><br>";
		$contents .= getTranslatedString('reply', $moduleName) . ' <b>' . $entityData->get('ticket_title')
				. '</b>' . getTranslatedString('customer_portal', $moduleName);
		$contents .= getTranslatedString("link", $moduleName) . '<br>';
		$contents .= $portalUrl;
		$contents .= '<br><br>' . getTranslatedString("Thanks", $moduleName) . '<br><br>' . getTranslatedString("Support_team", $moduleName);
		return $contents;
	}

	function clearSingletonSaveFields() {
		$this->column_fields['comments'] = '';
	}

}
?>
