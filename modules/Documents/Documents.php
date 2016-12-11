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
require_once('include/upload_file.php');

class Documents extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity
	var $table_name = 'vtiger_notes';
	var $table_index= 'notesid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;

	var $tab_name = Array('vtiger_crmentity','vtiger_notes','vtiger_notescf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_notes'=>'notesid','vtiger_notescf'=>'notesid','vtiger_senotesrel'=>'notesid');
	var $customFieldTable = Array('vtiger_notescf', 'notesid');

	var $popup_fields = Array('notes_title');
	var $sortby_fields = Array('title','modifiedtime','filename','createdtime','lastname','filedownloadcount','smownerid');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('', '', '', '');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		'Title'=>Array('notes'=>'title'),
		'File Name'=>Array('notes'=>'filename'),
		'Modified Time'=>Array('crmentity'=>'modifiedtime'),
		'Assigned To' => Array('crmentity'=>'smownerid'),
		'Folder Name' => Array('attachmentsfolder'=>'foldername')
	);
	var $list_fields_name = Array(
		'Title'=>'notes_title',
		'File Name'=>'filename',
		'Modified Time'=>'modifiedtime',
		'Assigned To'=>'assigned_user_id',
		'Folder Name' => 'folderid'
	);

	var $search_fields = Array(
		'Title' => Array('notes'=>'notes_title'),
		'File Name' => Array('notes'=>'filename'),
		'Assigned To' => Array('crmentity'=>'smownerid'),
		'Folder Name' => Array('attachmentsfolder'=>'foldername')
	);
	var $search_fields_name = Array(
		'Title' => 'notes_title',
		'File Name' => 'filename',
		'Assigned To' => 'assigned_user_id',
		'Folder Name' => 'folderid'
	);
	var $list_link_field= 'notes_title';
	var $old_filename = '';

	var $mandatory_fields = Array('notes_title','createdtime' ,'modifiedtime','filename','filesize','filetype','filedownloadcount','assigned_user_id');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'title';
	var $default_sort_order = 'ASC';

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
		global $log,$adb,$upload_badext;
		$insertion_mode = $this->mode;
		$filetype_fieldname = $this->getFileTypeFieldName();
		$filename_fieldname = $this->getFile_FieldName();
		if($this->column_fields[$filetype_fieldname] == 'I' ){
			if($_FILES[$filename_fieldname]['name'] != ''){
				$filedownloadcount = 0;
				$errCode=$_FILES[$filename_fieldname]['error'];
					if($errCode == 0){
						foreach($_FILES as $fileindex => $files)
						{
							if($files['name'] != '' && $files['size'] > 0){
								$filename = $_FILES[$filename_fieldname]['name'];
								$filename = from_html(preg_replace('/\s+/', '_', $filename));
								$filetype = $_FILES[$filename_fieldname]['type'];
								$filesize = $_FILES[$filename_fieldname]['size'];
								$filelocationtype = 'I';
								$binFile = sanitizeUploadFileName($filename, $upload_badext);
								$filename = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters
							}
						}
					}
			}elseif($this->mode == 'edit') {
				$fileres = $adb->pquery("select filetype, filesize,filename,filedownloadcount,filelocationtype from vtiger_notes where notesid=?", array($this->id));
				if ($adb->num_rows($fileres) > 0) {
					$filename = $adb->query_result($fileres, 0, 'filename');
					$filetype = $adb->query_result($fileres, 0, 'filetype');
					$filesize = $adb->query_result($fileres, 0, 'filesize');
					$filedownloadcount = $adb->query_result($fileres, 0, 'filedownloadcount');
					$filelocationtype = $adb->query_result($fileres, 0, 'filelocationtype');
				}
			}elseif($this->column_fields[$filename_fieldname]) {
				$filename = $this->column_fields[$filename_fieldname];
				$filesize = $this->column_fields['filesize'];
				$filetype = $this->column_fields['filetype'];
				$filelocationtype = $this->column_fields[$filetype_fieldname];
				$filedownloadcount = 0;
			} else {
				$filelocationtype = 'I';
				$filetype = '';
				$filesize = 0;
				$filedownloadcount = null;
			}
		} else if($this->column_fields[$filetype_fieldname] == 'E' ){
			$filelocationtype = 'E';
			$filename = $this->column_fields[$filename_fieldname];
			// If filename does not has the protocol prefix, default it to http://
			// Protocol prefix could be like (https://, smb://, file://, \\, smb:\\,...)
			if(!empty($filename) && !preg_match('/^\w{1,5}:\/\/|^\w{0,3}:?\\\\\\\\/', trim($filename), $match)) {
				$filename = "http://$filename";
			}
			$filetype = '';
			$filesize = 0;
			$filedownloadcount = null;
		}
		$query = "UPDATE vtiger_notes SET filename = ? ,filesize = ?, filetype = ? , filelocationtype = ? , filedownloadcount = ? WHERE notesid = ?";
 		$re=$adb->pquery($query,array($filename,$filesize,$filetype,$filelocationtype,$filedownloadcount,$this->id));
		//Inserting into attachments table
		if($filelocationtype == 'I') {
			$this->insertIntoAttachment($this->id,'Documents');
		}else{
			$query = "delete from vtiger_seattachmentsrel where crmid = ?";
			$qparams = array($this->id);
			$adb->pquery($query, $qparams);
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord, $query='') {
		if($module == "MailManager") {
			$tempQuery = explode('WHERE', $query);
			if(!empty($tempQuery[1])) {
				$where = " vtiger_notes.filelocationtype = 'I' AND vtiger_notes.filename != '' AND vtiger_notes.filestatus != 0 AND ";
				$query = $tempQuery[0].' WHERE '.$where.$tempQuery[1];
			} else{
				$query = $tempQuery[0].' WHERE '.$tempQuery;
			}
			return $query;
		}
	}
	/* Validate values trying to be saved.
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @return array
	 *   saveerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 *   error_action: action to redirect to inside the same module in case of error. if redirected to EditView (default action)
	 *                 all values introduced by the user will be preloaded
	 */
	function preSaveCheck($request) {
		global $adb,$log;
		$saveerror = false;
		$errmsg = '';
		if ($this->mode=='' && $_REQUEST['filelocationtype'] == 'I' && $_REQUEST['action'] != 'DocumentsAjax') {
			$upload_file_path = decideFilePath();
			$dirpermission = is_writable($upload_file_path);
			$upload = is_uploaded_file($_FILES['filename']['tmp_name']);
			if (!$dirpermission || ($_FILES['error']!=0 and $_FILES['error']!=4) || (!$upload and $_FILES['error']!=4)){
				$saveerror = true;
				$errmsg = getTranslatedString('LBL_FILEUPLOAD_FAILED','Documents');
			}
		}
		return array($saveerror,$errmsg,'EditView','');
	}

	/**
	 * This function is used to add the vtiger_attachments. This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 * @param int $id  - entity id to which the files to be uploaded
	 * @param string $module  - the current module name
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

	/**
	* Save the related module record information. Triggered from CRMEntity->saveentity method or updateRelations.php
	* @param String This module name
	* @param Integer This module record number
	* @param String Related module name
	* @param mixed Integer or Array of related module record number
	*/
	function save_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		if ($module=='Documents') {
			// in this case we have to turn the parameters around to call the parent method correctly
			if (!is_array($with_crmid))
				$with_crmid = Array($with_crmid);
			foreach ($with_crmid as $relcrmid) {
				$checkpresence = $adb->pquery("SELECT crmid FROM vtiger_senotesrel WHERE crmid = ? AND notesid = ?", Array($relcrmid,$crmid));
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence))
					continue;
				$adb->pquery("INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)", array($relcrmid,$crmid));
			}
		} else { // just call parent method
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

	/** Function used to get the sort order for Documents listview
	* @return string  $sorder - first check the $_REQUEST['sorder'] if request value is empty then check in the $_SESSION['NOTES_SORT_ORDER'] if this session value is empty then default sort order will be returned.
	*/
	function getSortOrder()
	{
		global $log;
		$log->debug("Entering getSortOrder() method ...");
		if(isset($_REQUEST['sorder']))
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else
			$sorder = (!empty($_SESSION['NOTES_SORT_ORDER']) ? $_SESSION['NOTES_SORT_ORDER'] : $this->default_sort_order);
		$log->debug("Exiting getSortOrder() method ...");
		return $sorder;
	}

	/** Function used to get the order by value for Documents listview
	* @return string  $order_by  - first check the $_REQUEST['order_by'] if request value is empty then check in the $_SESSION['NOTES_ORDER_BY'] if this session value is empty then default order by will be returned.
	*/
	function getOrderBy()
	{
		global $currentModule,$log;
		$log->debug("Entering getOrderBy() method ...");

		$use_default_order_by = '';
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}
		$orderby = $use_default_order_by;
		if (isset($_REQUEST['order_by']))
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		else if(isset($_SESSION[$currentModule.'_Order_By']))
			$order_by = $_SESSION[$currentModule.'_Order_By'];
		else
			$order_by = (!empty($_SESSION['NOTES_ORDER_BY']) ? $_SESSION['NOTES_ORDER_BY'] : $use_default_order_by);
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}

	/**
	 * Function used to get the sort order for Documents listview
	 * @return String $sorder - sort order for a given folder.
	 */
	function getSortOrderForFolder($folderId) {
		if(isset($_REQUEST['sorder']) && $_REQUEST['folderid'] == $folderId) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} elseif(is_array($_SESSION['NOTES_FOLDER_SORT_ORDER']) &&
					!empty($_SESSION['NOTES_FOLDER_SORT_ORDER'][$folderId])) {
				$sorder = $_SESSION['NOTES_FOLDER_SORT_ORDER'][$folderId];
		} else {
			$sorder = $this->default_sort_order;
		}
		return $sorder;
	}

	/**
	 * Function used to get the order by value for Documents listview
	 * @return String order by column for a given folder.
	 */
	function getOrderByForFolder($folderId) {
		$use_default_order_by = '';
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}
		if (isset($_REQUEST['order_by']) && $_REQUEST['folderid'] == $folderId) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} elseif(is_array($_SESSION['NOTES_FOLDER_ORDER_BY']) &&
				!empty($_SESSION['NOTES_FOLDER_ORDER_BY'][$folderId])) {
			$order_by = $_SESSION['NOTES_FOLDER_ORDER_BY'][$folderId];
		} else {
			$order_by = ($use_default_order_by);
		}
		return $order_by;
	}

	/** Function to export the notes in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Documents Query.
	*/
	function create_export_query($where)
	{
		global $log,$current_user;
		$log->debug("Entering create_export_query(". $where.") method ...");

		include("include/utils/ExportUtils.php");
		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Documents", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, foldername, filename,
					concat(path,vtiger_attachments.attachmentsid,'_',filename) as storagename,
					concat(account_no,' ',accountname) as account, concat(contact_no,' ',firstname,' ',lastname) as contact,vtiger_senotesrel.crmid as relatedid
				FROM vtiger_notes
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
				left join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
				LEFT JOIN vtiger_attachmentsfolder on vtiger_notes.folderid=vtiger_attachmentsfolder.folderid
				LEFT JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid=vtiger_notes.notesid
				LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_senotesrel.crmid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid=vtiger_senotesrel.crmid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid=vtiger_groups.groupid ";
		$query .= getNonAdminAccessControlQuery('Documents',$current_user);
		$where_auto=" vtiger_crmentity.deleted=0";
		if($where != "")
			$query .= " WHERE ($where) AND ".$where_auto;
		else
			$query .= " WHERE ".$where_auto;
		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	function del_create_def_folder($query)
	{
		global $adb;
		$dbQuery = $query." and vtiger_attachmentsfolder.folderid = 0";
		$dbresult = $adb->pquery($dbQuery,array());
		$noofnotes = $adb->num_rows($dbresult);
		if($noofnotes > 0) {
			$folderQuery = "select folderid from vtiger_attachmentsfolder";
			$folderresult = $adb->pquery($folderQuery,array());
			$noofdeffolders = $adb->num_rows($folderresult);
			if($noofdeffolders == 0) {
				$insertQuery = "insert into vtiger_attachmentsfolder values (0,'Default','Contains all attachments for which a folder is not set',1,0)";
				$insertresult = $adb->pquery($insertQuery,array());
			}
		}
	}

	/*function save_related_module($module, $crmid, $with_module, $with_crmid){
	}*/

	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	function generateReportsQuery($module){
		$moduletable = $this->table_name;
		$moduleindex = $this->tab_name_index[$moduletable];
		$query = "from $moduletable
			inner join vtiger_crmentity on vtiger_crmentity.crmid=$moduletable.$moduleindex
			inner join vtiger_attachmentsfolder on vtiger_attachmentsfolder.folderid=$moduletable.folderid 
			inner join vtiger_notescf on vtiger_notescf.notesid = $moduletable.$moduleindex
			left join vtiger_groups as vtiger_groups".$module." on vtiger_groups".$module.".groupid = vtiger_crmentity.smownerid
			left join vtiger_users as vtiger_users".$module." on vtiger_users".$module.".id = vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			left join vtiger_users as vtiger_lastModifiedBy".$module." on vtiger_lastModifiedBy".$module.".id = vtiger_crmentity.modifiedby ";
		return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_notes","notesid");
		$query .=" left join vtiger_crmentity as vtiger_crmentityDocuments on vtiger_crmentityDocuments.crmid=vtiger_notes.notesid and vtiger_crmentityDocuments.deleted=0 
			left join vtiger_notescf on vtiger_notescf.notesid = vtiger_notes.notesid 
			left join vtiger_attachmentsfolder on vtiger_attachmentsfolder.folderid=vtiger_notes.folderid
			left join vtiger_groups as vtiger_groupsDocuments on vtiger_groupsDocuments.groupid = vtiger_crmentityDocuments.smownerid
			left join vtiger_users as vtiger_usersDocuments on vtiger_usersDocuments.id = vtiger_crmentityDocuments.smownerid
			left join vtiger_users as vtiger_lastModifiedByDocuments on vtiger_lastModifiedByDocuments.id = vtiger_crmentityDocuments.modifiedby ";
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array();
		return $rel_tables[$secmodule];
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;
		/*//Backup Documents Related Records
		$se_q = 'SELECT crmid FROM vtiger_senotesrel WHERE notesid = ?';
		$se_res = $this->db->pquery($se_q, array($id));
		if ($this->db->num_rows($se_res) > 0) {
			for($k=0;$k < $this->db->num_rows($se_res);$k++)
			{
				$se_id = $this->db->query_result($se_res,$k,"crmid");
				$params = array($id, RB_RECORD_DELETED, 'vtiger_senotesrel', 'notesid', 'crmid', $se_id);
				$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
			}
		}
		$sql = 'DELETE FROM vtiger_senotesrel WHERE notesid = ?';
		$this->db->pquery($sql, array($id));*/

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		$sql = 'DELETE FROM vtiger_senotesrel WHERE notesid = ? AND crmid = ?';
		$this->db->pquery($sql, array($id, $return_id));

		$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
		$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
		$this->db->pquery($sql, $params);
	}


// Function to get fieldname for uitype 27 assuming that documents have only one file type field

	function getFileTypeFieldName(){
		global $adb,$log;
		$query = 'SELECT fieldname from vtiger_field where tabid = ? and uitype = ?';
		$tabid = getTabid('Documents');
		$filetype_uitype = 27;
		$res = $adb->pquery($query,array($tabid,$filetype_uitype));
		$fieldname = null;
		if(isset($res)){
			$rowCount = $adb->num_rows($res);
			if($rowCount > 0){
				$fieldname = $adb->query_result($res,0,'fieldname');
			}
		}
		return $fieldname;

	}

	//	Function to get fieldname for uitype 28 assuming that doc has only one file upload type
	function getFile_FieldName(){
		global $adb,$log;
		$query = 'SELECT fieldname from vtiger_field where tabid = ? and uitype = ?';
		$tabid = getTabid('Documents');
		$filename_uitype = 28;
		$res = $adb->pquery($query,array($tabid,$filename_uitype));
		$fieldname = null;
		if(isset($res)){
			$rowCount = $adb->num_rows($res);
			if($rowCount > 0){
				$fieldname = $adb->query_result($res,0,'fieldname');
			}
		}
		return $fieldname;
	}

	/**
	 * Check the existence of folder by folderid
	 */
	function isFolderPresent($folderid) {
		global $adb;
		$result = $adb->pquery("SELECT folderid FROM vtiger_attachmentsfolder WHERE folderid = ?", array($folderid));
		if(!empty($result) && $adb->num_rows($result) > 0) return true;
		return false;
	}

	/**
	 * Customizing the restore procedure.
	 */
	function restore($modulename, $id) {
		parent::restore($modulename, $id);

		global $adb;
		$fresult = $adb->pquery("SELECT folderid FROM vtiger_notes WHERE notesid = ?", array($id));
		if(!empty($fresult) && $adb->num_rows($fresult)) {
			$folderid = $adb->query_result($fresult, 0, 'folderid');
			if(!$this->isFolderPresent($folderid)) {
				// Re-link to default folder
				$adb->pquery("UPDATE vtiger_notes set folderid = 1 WHERE notesid = ?", array($id));
			}
		}
	}

	function getEntities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $theme, $adb, $mod_strings, $app_strings;
		$log->debug("Entering getEntities($id, $cur_tab_id, $rel_tab_id, $actions) method ...");
		$theme_path="themes/".$theme."/";
		$image_path=$theme_path."images/";

		//Form the header columns
		$header[] = $app_strings['LBL_ENTITY_NAME'];
		$header[] = $app_strings['LBL_TYPE'];
		$header[] = $app_strings['LBL_ASSIGNED_TO'];
		$button = '';

		$related_module='Documents';
		$currentModule='Documents';
		if(isPermitted($related_module,4, '') == 'yes') {
			$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' " .
					" type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
					" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module, $related_module) ."'>&nbsp;";
		}
		$query = "select case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
				crm2.crmid, crm2.setype
				from vtiger_notes
				inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
				inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
				inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_senotesrel.crmid and crm2.deleted=0
				left join vtiger_groups on vtiger_groups.groupid = crm2.smownerid
				left join vtiger_users on vtiger_users.id = crm2.smownerid
				where vtiger_notes.notesid=?
				UNION
				select case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
				crm2.crmid, crm2.setype
				from vtiger_notes
				inner join vtiger_senotesrel on vtiger_senotesrel.crmid= vtiger_notes.notesid
				inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
				inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_senotesrel.notesid and crm2.deleted=0
				left join vtiger_groups on vtiger_groups.groupid = crm2.smownerid
				left join vtiger_users on vtiger_users.id = crm2.smownerid
				where vtiger_notes.notesid=?";

		$drs = $adb->pquery($query,array($id,$id));
		$entries_list = Array();
		while($row = $adb->fetch_array($drs))
		{
			$entries = Array();
			$edata = getEntityName($row['setype'],array($row['crmid']));
			$ename = $edata[$row['crmid']];
			$elink = '<a href="index.php?module='.$row['setype'].'&action=DetailView&return_module=Documents&return_action=DetailView&record='.$row["crmid"] .'&return_id='.$id.'&parenttab='.vtlib_purify($_REQUEST['parenttab']).'">'.$ename.'</a>';
			$entries[] = $elink;
			$entries[] = getTranslatedString($row['setype']) ;
			$entries[] = $row['user_name'];
			$entries_list[] = $entries;
		}
		$return_data = array('header'=>$header,'entries'=>$entries_list,'CUSTOM_BUTTON' => $button);
		$log->debug("Exiting getEntities method ...");
		return $return_data;
	}
}
?>
