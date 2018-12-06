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
require_once 'include/upload_file.php';

class Documents extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_notes';
	public $table_index= 'notesid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;

	public $customFieldTable = array('vtiger_notescf', 'notesid');

	public $tab_name = array('vtiger_crmentity', 'vtiger_notes', 'vtiger_notescf');

	public $tab_name_index = array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_notes'=>'notesid',
		'vtiger_notescf'=>'notesid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		'Title'=>array('notes'=>'title'),
		'File Name'=>array('notes'=>'filename'),
		'Modified Time'=>array('crmentity'=>'modifiedtime'),
		'Assigned To' => array('crmentity'=>'smownerid'),
		'Folder Name' => array('attachmentsfolder'=>'foldername')
	);
	public $list_fields_name = array(
		'Title'=>'notes_title',
		'File Name'=>'filename',
		'Modified Time'=>'modifiedtime',
		'Assigned To'=>'assigned_user_id',
		'Folder Name' => 'folderid'
	);

	public $list_link_field= 'notes_title';

	public $search_fields = array(
		'Title' => array('notes'=>'notes_title'),
		'File Name' => array('notes'=>'filename'),
		'Assigned To' => array('crmentity'=>'smownerid'),
		'Folder Name' => array('attachmentsfolder'=>'foldername')
	);
	public $search_fields_name = array(
		'Title' => 'notes_title',
		'File Name' => 'filename',
		'Assigned To' => 'assigned_user_id',
		'Folder Name' => 'folderid'
	);

	public $popup_fields = array('notes_title');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'notes_title';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'notes_title';

	// Required Information for enabling Import feature
	public $required_fields = array('notes_title'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'title';
	public $default_sort_order = 'ASC';
	public $mandatory_fields = array('notes_title', 'createdtime', 'modifiedtime', 'filename', 'filesize', 'filetype', 'filedownloadcount');
	public $old_filename = '';

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		global $adb, $upload_badext;
		$filetype_fieldname = $this->getFileTypeFieldName();
		$filename_fieldname = $this->getFile_FieldName();
		if ($this->column_fields[$filetype_fieldname] == 'I') {
			if (!empty($_FILES[$filename_fieldname]['name'])) {
				$filedownloadcount = 0;
				$errCode=$_FILES[$filename_fieldname]['error'];
				if ($errCode == 0) {
					foreach ($_FILES as $files) {
						if ($files['name'] != '' && $files['size'] > 0) {
							$filename = $_FILES[$filename_fieldname]['name'];
							$filename = vtlib_purify(preg_replace('/\s+/', '_', $filename));
							$filetype = $_FILES[$filename_fieldname]['type'];
							$filesize = $_FILES[$filename_fieldname]['size'];
							$filelocationtype = 'I';
							$binFile = sanitizeUploadFileName($filename, $upload_badext);
							$filename = ltrim(basename(" ".$binFile)); //allowed filename like UTF-8 characters
						}
					}
				}
			} elseif ($this->mode == 'edit') {
				$fileres = $adb->pquery('select filetype, filesize,filename,filedownloadcount,filelocationtype from vtiger_notes where notesid=?', array($this->id));
				if ($adb->num_rows($fileres) > 0) {
					$filename = $adb->query_result($fileres, 0, 'filename');
					$filetype = $adb->query_result($fileres, 0, 'filetype');
					$filesize = $adb->query_result($fileres, 0, 'filesize');
					$filedownloadcount = $adb->query_result($fileres, 0, 'filedownloadcount');
					$filelocationtype = $adb->query_result($fileres, 0, 'filelocationtype');
				}
			} elseif ($this->column_fields[$filename_fieldname]) {
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
		} elseif ($this->column_fields[$filetype_fieldname] == 'E') {
			$filelocationtype = 'E';
			$filename = $this->column_fields[$filename_fieldname];
			// If filename does not has the protocol prefix, default it to http://
			// Protocol prefix could be like (https://, smb://, file://, \\, smb:\\,...)
			if (!empty($filename) && !preg_match('/^\w{1,5}:\/\/|^\w{0,3}:?\\\\\\\\/', trim($filename))) {
				$filename = "http://$filename";
			}
			$filetype = '';
			$filesize = 0;
			$filedownloadcount = null;
		}
		$query = 'UPDATE vtiger_notes SET filename = ? ,filesize = ?, filetype = ? , filelocationtype = ? , filedownloadcount = ? WHERE notesid = ?';
		$adb->pquery($query, array($filename, $filesize, $filetype, $filelocationtype, $filedownloadcount, $this->id));
		//Inserting into attachments table
		if ($filelocationtype == 'I') {
			$this->insertIntoAttachment($this->id, 'Documents');
		} else {
			$adb->pquery('delete from vtiger_seattachmentsrel where crmid = ?', array($this->id));
		}
		//set the column_fields so that its available in the event handlers
		$this->column_fields['filename'] = $filename;
		$this->column_fields['filesize'] = $filesize;
		$this->column_fields['filetype'] = $filetype;
		$this->column_fields['filedownloadcount'] = $filedownloadcount;
		if (!empty($this->parentid)) {
			$this->save_related_module('Documents', $this->id, getSalesEntityType($this->parentid), $this->parentid);
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	public function getQueryByModuleField($module, $fieldname, $srcrecord, $query = '') {
		if ($module == 'MailManager') {
			$tempQuery = explode('WHERE', $query);
			if (!empty($tempQuery[1])) {
				$where = " vtiger_notes.filelocationtype = 'I' AND vtiger_notes.filename != '' AND vtiger_notes.filestatus != 0 AND ";
				$query = $tempQuery[0].' WHERE '.$where.$tempQuery[1];
			} else {
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
	public function preSaveCheck($request) {
		global $coreBOSOnDemandActive, $cbodStorageSizeLimit, $current_user, $site_URL;
		if (isset($_REQUEST['parentid']) && $_REQUEST['parentid'] != '') {
			$this->parentid = vtlib_purify($_REQUEST['parentid']);
		}
		$saveerror = false;
		$errmsg = '';
		if (!empty($coreBOSOnDemandActive) && $_REQUEST['filelocationtype'] == 'I' && $_REQUEST['action'] != 'DocumentsAjax') {
			$sistoragesize = coreBOS_Settings::getSetting('cbod_storagesize', 0);
			$sistoragesizelimit = coreBOS_Settings::getSetting('cbod_storagesizelimit', $cbodStorageSizeLimit);
			if ($sistoragesize > $sistoragesizelimit) {
				$adminlink = '';
				if (is_admin($current_user)) {
					$adminlink = '<a href="'.$site_URL.'/index.php?module=Documents&action=StorageConfig&parenttab=Settings&formodule=Documents">';
					$adminlink.= getTranslatedString('ExtendStorageLink', 'Documents').'</a>';
				}
				$saveerror = true;
				$errmsg = getTranslatedString('StorageLimit', 'Documents').' '.$adminlink;
			}
		}
		if (!$saveerror && $_REQUEST['filelocationtype'] == 'I' && $_REQUEST['action'] != 'DocumentsAjax') {
			$upload_file_path = decideFilePath();
			$dirpermission = is_writable($upload_file_path);
			$upload = is_uploaded_file($_FILES['filename']['tmp_name']);
			$ferror = (isset($_FILES['error']) ? $_FILES['error'] : $_FILES['filename']['error']);
			if ((!$dirpermission && ($this->mode=='' || ($this->mode!='' && $upload))) || ($ferror!=0 && $ferror!=4) || (!$upload && $ferror!=4)) {
				$saveerror = true;
				$errmsg = getTranslatedString('LBL_FILEUPLOAD_FAILED', 'Documents');
			}
		}
		if ($saveerror) {
			return array($saveerror, $errmsg, 'EditView', '');
		} else {
			return parent::preSaveCheck($request);
		}
	}

	/**
	 * This function is used to add attachments.
	 * This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 * @param int $id  - entity id to which the files to be uploaded
	 * @param string $module  - the current module name
	*/
	public function insertIntoAttachment($id, $module, $direct_import = false) {
		global $log;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");
		$file_saved = false;
		if (isset($_FILES)) {
			foreach ($_FILES as $fileindex => $files) {
				if ($files['name'] != '' && $files['size'] > 0) {
					$files['original_name'] = (empty($_REQUEST[$fileindex.'_hidden']) ? vtlib_purify($files['name']) : vtlib_purify($_REQUEST[$fileindex.'_hidden']));
					$file_saved = $this->uploadAndSaveFile($id, $module, $files);
				}
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
	public function save_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		if ($module=='Documents') {
			// in this case we have to turn the parameters around to call the parent method correctly
			$with_crmid = (array)$with_crmid;
			foreach ($with_crmid as $relcrmid) {
				$checkpresence = $adb->pquery('SELECT crmid FROM vtiger_senotesrel WHERE crmid = ? AND notesid = ?', array($relcrmid,$crmid));
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$adb->pquery('INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)', array($relcrmid,$crmid));
			}
		} else { // just call parent method
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

	/** Function used to get the sort order for Documents listview
	* @return string  $sorder - first check the $_REQUEST['sorder'] if request value is empty then check in the $_SESSION['NOTES_SORT_ORDER']
	* 	 if this session value is empty then default sort order will be returned.
	*/
	public function getSortOrder() {
		global $log;
		$log->debug('Entering getSortOrder() method ...');
		if (isset($_REQUEST['sorder'])) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} else {
			$sorder = (!empty($_SESSION['NOTES_SORT_ORDER']) ? $this->db->sql_escape_string($_SESSION['NOTES_SORT_ORDER']) : $this->default_sort_order);
		}
		$log->debug('Exiting getSortOrder() method ...');
		return $sorder;
	}

	/** Function used to get the order by value for Documents listview
	* @return string  $order_by  - first check the $_REQUEST['order_by'] if request value is empty then check in the $_SESSION['NOTES_ORDER_BY']
	* 	 if this session value is empty then default order by will be returned.
	*/
	public function getOrderBy() {
		global $currentModule,$log;
		$log->debug('Entering getOrderBy() method ...');
		$use_default_order_by = '';
		if (GlobalVariable::getVariable('Application_ListView_Default_Sorting', 0, $currentModule)) {
			$use_default_order_by = $this->default_order_by;
		}
		if (isset($_REQUEST['order_by'])) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} elseif (isset($_SESSION[$currentModule.'_Order_By'])) {
			$order_by = $this->db->sql_escape_string($_SESSION[$currentModule.'_Order_By']);
		} else {
			$order_by = (!empty($_SESSION['NOTES_ORDER_BY']) ? $this->db->sql_escape_string($_SESSION['NOTES_ORDER_BY']) : $use_default_order_by);
		}
		$log->debug('Exiting getOrderBy method ...');
		return $order_by;
	}

	/**
	 * Function used to get the sort order for Documents listview
	 * @return String $sorder - sort order for a given folder.
	 */
	public function getSortOrderForFolder($folderId) {
		if (isset($_REQUEST['sorder']) && $_REQUEST['folderid'] == $folderId) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} elseif (isset($_SESSION['NOTES_FOLDER_SORT_ORDER']) && is_array($_SESSION['NOTES_FOLDER_SORT_ORDER']) && !empty($_SESSION['NOTES_FOLDER_SORT_ORDER'][$folderId])) {
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
	public function getOrderByForFolder($folderId) {
		$use_default_order_by = '';
		if (GlobalVariable::getVariable('Application_ListView_Default_Sorting', 0)) {
			$use_default_order_by = $this->default_order_by;
		}
		if (isset($_REQUEST['order_by']) && $_REQUEST['folderid'] == $folderId) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} elseif (!empty($_SESSION['NOTES_FOLDER_ORDER_BY']) && is_array($_SESSION['NOTES_FOLDER_ORDER_BY']) && !empty($_SESSION['NOTES_FOLDER_ORDER_BY'][$folderId])) {
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
	public function create_export_query($where) {
		global $log,$current_user;
		$log->debug("Entering create_export_query(". $where.") method ...");

		include "include/utils/ExportUtils.php";
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
				LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
				LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid=vtiger_groups.groupid ";
		$query .= getNonAdminAccessControlQuery('Documents', $current_user);
		$where_auto=" vtiger_crmentity.deleted=0";
		if ($where != "") {
			$query .= " WHERE ($where) AND ".$where_auto;
		} else {
			$query .= " WHERE ".$where_auto;
		}
		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	public function del_create_def_folder($query) {
		global $adb;
		$dbQuery = $query." and vtiger_attachmentsfolder.folderid = 0";
		$dbresult = $adb->pquery($dbQuery, array());
		$noofnotes = $adb->num_rows($dbresult);
		if ($noofnotes > 0) {
			$folderresult = $adb->pquery('select folderid from vtiger_attachmentsfolder', array());
			$noofdeffolders = $adb->num_rows($folderresult);
			if ($noofdeffolders == 0) {
				$adb->pquery("insert into vtiger_attachmentsfolder values (0,'Default','Contains all attachments for which a folder is not set',1,0)", array());
			}
		}
	}

	public static function createFolder($fname) {
		global $adb, $current_user;
		$dbQuery = 'select 1 from vtiger_attachmentsfolder where foldername=?';
		$rs = $adb->pquery($dbQuery, array($fname));
		if ($rs && $adb->num_rows($rs)==0) {
			$params = array();
			$sqlfid = 'select max(folderid) from vtiger_attachmentsfolder';
			$rs = $adb->pquery($sqlfid, $params);
			$fid = $adb->query_result($rs, 0, 0) + 1;
			$params = array();
			$sqlseq = 'select max(sequence) from vtiger_attachmentsfolder';
			$rs = $adb->pquery($sqlseq, $params);
			$sequence=$adb->query_result($rs, 0, 0) + 1;
			$sql = 'insert into vtiger_attachmentsfolder (folderid,foldername,description,createdby,sequence) values (?,?,?,?,?)';
			$params = array($fid, $fname, '', $current_user->id, $sequence);
			$result = $adb->pquery($sql, $params);
			return ($result ? $fid : false);
		}
		return false;
	}

	/*function save_related_module($module, $crmid, $with_module, $with_crmid){
	}*/

	/*
	 * Function to get the primary query part of a report
	 * @param - $module Primary module name
	 * returns the query string formed on fetching the related data for report for primary module
	 */
	public function generateReportsQuery($module, $queryplanner) {
		$moduletable = $this->table_name;
		$moduleindex = $this->tab_name_index[$moduletable];
		$query = "from $moduletable
			inner join vtiger_crmentity on vtiger_crmentity.crmid=$moduletable.$moduleindex";
		if ($queryplanner->requireTable("vtiger_attachmentsfolder")) {
			$query .= " inner join vtiger_attachmentsfolder on vtiger_attachmentsfolder.folderid=$moduletable.folderid";
		}
		if ($queryplanner->requireTable('vtiger_CreatedBy'.$module)) {
			$query .= " LEFT JOIN vtiger_users AS vtiger_CreatedBy$module ON vtiger_CreatedBy$module.id = vtiger_crmentity.smcreatorid";
		}
		if ($queryplanner->requireTable("vtiger_users".$module) || $queryplanner->requireTable("vtiger_groups".$module)) {
			$query .= " left join vtiger_users as vtiger_users".$module." on vtiger_users".$module.".id = vtiger_crmentity.smownerid";
			$query .= " left join vtiger_groups as vtiger_groups".$module." on vtiger_groups".$module.".groupid = vtiger_crmentity.smownerid";
		}
		$query .= " left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " left join vtiger_notescf on vtiger_notes.notesid = vtiger_notescf.notesid";
		$query .= " left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid";
		if ($queryplanner->requireTable("vtiger_lastModifiedBy".$module)) {
			$query .= " left join vtiger_users as vtiger_lastModifiedBy".$module." on vtiger_lastModifiedBy".$module.".id = vtiger_crmentity.modifiedby ";
		}
		return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryplanner, $type = '', $where_condition = '') {
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryplanner, $type, $where_condition);
		if ($queryplanner->requireTable("vtiger_attachmentsfolder")) {
			$query .= ' left join vtiger_attachmentsfolder on vtiger_attachmentsfolder.folderid=vtiger_notes.folderid';
		}
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		return '';
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
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
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		$this->db->pquery('DELETE FROM vtiger_senotesrel WHERE notesid = ? AND crmid = ?', array($id, $return_id));
		$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
		$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
		$this->db->pquery($sql, $params);
	}

	// Function to get fieldname for uitype 27 assuming that documents have only one file type field
	public function getFileTypeFieldName() {
		global $adb;
		$tabid = getTabid('Documents');
		$filetype_uitype = 27;
		$res = $adb->pquery('SELECT fieldname from vtiger_field where tabid = ? and uitype = ?', array($tabid, $filetype_uitype));
		$fieldname = null;
		if (isset($res)) {
			$rowCount = $adb->num_rows($res);
			if ($rowCount > 0) {
				$fieldname = $adb->query_result($res, 0, 'fieldname');
			}
		}
		return $fieldname;
	}

	//	Function to get fieldname for uitype 28 assuming that doc has only one file upload type
	public function getFile_FieldName() {
		global $adb;
		$tabid = getTabid('Documents');
		$filename_uitype = 28;
		$res = $adb->pquery('SELECT fieldname from vtiger_field where tabid = ? and uitype = ?', array($tabid, $filename_uitype));
		$fieldname = null;
		if (isset($res)) {
			$rowCount = $adb->num_rows($res);
			if ($rowCount > 0) {
				$fieldname = $adb->query_result($res, 0, 'fieldname');
			}
		}
		return $fieldname;
	}

	/**
	 * Check the existence of folder by folderid
	 */
	public function isFolderPresent($folderid) {
		global $adb;
		$result = $adb->pquery("SELECT folderid FROM vtiger_attachmentsfolder WHERE folderid = ?", array($folderid));
		if (!empty($result) && $adb->num_rows($result) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Customizing the restore procedure.
	 */
	public function restore($modulename, $id) {
		parent::restore($modulename, $id);

		global $adb;
		$fresult = $adb->pquery("SELECT folderid FROM vtiger_notes WHERE notesid = ?", array($id));
		if (!empty($fresult) && $adb->num_rows($fresult)) {
			$folderid = $adb->query_result($fresult, 0, 'folderid');
			if (!$this->isFolderPresent($folderid)) {
				// Re-link to default folder
				$adb->pquery("UPDATE vtiger_notes set folderid = 1 WHERE notesid = ?", array($id));
			}
		}
	}

	public function getEntities($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $adb, $app_strings;
		$log->debug("Entering getEntities($id, $cur_tab_id, $rel_tab_id, $actions) method ...");

		//Form the header columns
		$header[] = $app_strings['LBL_ENTITY_NAME'];
		$header[] = $app_strings['LBL_TYPE'];
		$header[] = $app_strings['LBL_ASSIGNED_TO'];
		$button = '';

		$related_module='Documents';
		$currentModule='Documents';
		if (isPermitted($related_module, 4, '') == 'yes') {
			$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' " .
				" type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview".
				"&select=enable&form=EditView&form_submit=false&recordid=$id','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
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

		$drs = $adb->pquery($query, array($id,$id));
		$entries_list = array();
		while ($row = $adb->fetch_array($drs)) {
			$entries = array();
			$edata = getEntityName($row['setype'], array($row['crmid']));
			$ename = $edata[$row['crmid']];
			$elink = '<a href="index.php?module='.$row['setype'].'&action=DetailView&return_module=Documents&return_action=DetailView&record='.$row['crmid'].
				'&return_id='.$id.'&parenttab='.vtlib_purify($_REQUEST['parenttab']).'">'.$ename.'</a>';
			$entries[] = $elink;
			$entries[] = getTranslatedString($row['setype']) ;
			$entries[] = $row['user_name'];
			$entries_list[] = $entries;
		}
		$return_data = array('header'=>$header,'entries'=>$entries_list,'CUSTOM_BUTTON' => $button,'navigation'=>array('',''));
		$log->debug("Exiting getEntities method ...");
		return $return_data;
	}
}
?>
