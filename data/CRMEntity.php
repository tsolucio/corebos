<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'config.inc.php';
require_once 'include/logging.php';
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
$VTIGER_BULK_SAVE_MODE = false; // mass create/import global variable

class CRMEntity {

	public $ownedby;
	public $mode;
	public $id;
	public $linkmodeid = 0;
	public $linkmodemodule = '';
	public $DirectImageFieldValues = array();
	public $HasDirectImageField = false;
	public $crmentityTable = 'vtiger_crmentity';
	public $crmentityTableAlias;
	public $denormalized = false;
	protected static $methods = array();
	protected static $dbvalues = array();
	protected static $todvalues = array();
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-recent', 'class' => 'slds-icon', 'icon'=>'entity');

	public function __construct() {
		global $adb;
		$this_module = get_class($this);
		$tabid = getTabid($this_module);
		$result = $adb->pquery('SELECT denormtable FROM vtiger_entityname WHERE tabid=?', array($tabid));
		if ($result) {
			$this->crmentityTable = $adb->query_result($result, 0, 'denormtable');
		}
		$this->denormalized = ($this->crmentityTable!='vtiger_crmentity');
		if ($this->denormalized) {
			if (($key = array_search('vtiger_crmentity', $this->tab_name)) !== false) {
				unset($this->tab_name[$key]);
			}
			unset($this->tab_name_index['vtiger_crmentity']);
		}
		$this->crmentityTableAlias = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$this->column_fields = getColumnFields($this_module);
		$result = $adb->pquery('SELECT 1 FROM vtiger_field WHERE uitype=69 and tabid=? limit 1', array($tabid));
		$this->HasDirectImageField = ($result && $adb->num_rows($result)==1);
	}

	public static function registerMethod($method) {
		self::$methods[] = $method;
	}

	public function __call($method, $args) {
		if (in_array($method, self::$methods)) {
			$args[] = $this;
			return call_user_func_array($method, array_values($args));
		}
	}

	/**
	 * Detect if we are in bulk save mode, where some features can be turned-off to improve performance
	 */
	public static function isBulkSaveMode() {
		global $VTIGER_BULK_SAVE_MODE;
		return isset($VTIGER_BULK_SAVE_MODE) && $VTIGER_BULK_SAVE_MODE;
	}

	public static function getInstance($modName) {
		// File access security check
		if (!class_exists($modName)) {
			checkFileAccessForInclusion("modules/$modName/$modName.php");
			require_once "modules/$modName/$modName.php";
		}
		return new $modName();
	}

	public function getUUID() {
		$hcols = array();
		$hcols['moduletype'] = $this->column_fields['record_module'];
		$hcols['record_id'] = empty($this->column_fields['record_id']) ? $_REQUEST['currentid'] : $this->column_fields['record_id'];
		$hcols['creator'] = isset($this->column_fields['created_user_id']) ? getUserEmail($this->column_fields['created_user_id']) : 'email@lost.tld';
		$hcols['owner'] = isset($this->column_fields['assigned_user_id']) ? getUserEmail($this->column_fields['assigned_user_id']) : 'nouser@module.tld';
		$hcols['createdtime'] = $this->column_fields['createdtime'];
		return sha1(json_encode($hcols));
	}

	public static function getUUIDfromCRMID($refval) {
		global $adb;
		$rs = $adb->pquery('select cbuuid from vtiger_crmobject where crmid=?', array($refval));
		return (($rs && $adb->num_rows($rs)>0) ? $rs->fields['cbuuid'] : '');
	}

	public static function getUUIDfromWSID($refval) {
		if (empty($refval)) {
			return '';
		}
		$nocbuuid = array('Users', 'Currency', 'Groups', '');
		list($wsid, $crmid) = explode('x', $refval);
		if (in_array(vtws_getEntityName($wsid), $nocbuuid)) {
			return '';
		}
		return CRMEntity::getUUIDfromCRMID($crmid);
	}

	public static function getCRMIDfromUUID($refval) {
		global $adb;
		if (empty($refval)) {
			return '';
		}
		$rs = $adb->pquery('select crmid from vtiger_crmobject where cbuuid=?', array($refval));
		return (($rs && $adb->num_rows($rs)>0) ? $rs->fields['crmid'] : '');
	}

	public static function getWSIDfromUUID($refval) {
		global $adb;
		$rs = $adb->pquery(
			'select concat(id,"x",crmid) as wsid from vtiger_crmobject inner join vtiger_ws_entity on name=setype where cbuuid=?',
			array($refval)
		);
		return (($rs && $adb->num_rows($rs)>0) ? $rs->fields['wsid'] : '');
	}

	public function saveentity($module) {
		global $current_user, $adb;
		if (property_exists($module, 'HasDirectImageField') && $this->HasDirectImageField && !empty($this->id)) {
			// we have to save these names to delete previous overwritten values in uitype 69 field
			$sql = 'SELECT tablename,columnname FROM vtiger_field WHERE uitype=69 and vtiger_field.tabid = ?';
			$tabid = getTabid($module);
			$result = $adb->pquery($sql, array($tabid));
			while ($finfo = $adb->fetch_array($result)) {
				$mrowrs = $adb->pquery(
					'select '.$finfo['columnname'].' from '.$finfo['tablename'].' where '.$this->tab_name_index[$finfo['tablename']].'=?',
					array($this->id)
				);
				$this->DirectImageFieldValues[$finfo['columnname']] = $adb->query_result($mrowrs, 0, 0);
			}
		}
		$anyValue = false;
		foreach ($this->column_fields as $value) {
			if (!empty($value)) {
				$anyValue = true;
				break;
			}
		}
		if (!$anyValue) {
			die('<center>' .getTranslatedString('LBL_MANDATORY_FIELD_MISSING').'</center>');
		}

		$adb->println("TRANS saveentity starts $module");
		$adb->startTransaction();

		foreach ($this->tab_name as $table_name) {
			if ($table_name == 'vtiger_crmentity') {
				$this->insertIntoCrmEntity($module);
			} else {
				$this->insertIntoEntityTable($table_name, $module);
			}
		}

		// If multicurrency module we save the currency and conversion rate
		if (!empty($this->column_fields['conversion_rate']) && !empty($this->column_fields['currency_id'])) {
			$update_query = 'update '.$this->table_name.' set currency_id=?, conversion_rate=? where '.$this->table_index.'=?';
			$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
			$adb->pquery($update_query, $update_params);
		}

		//Calling the Module specific save code
		$this->save_module($module);

		$adb->completeTransaction();
		$adb->println('TRANS saveentity ends');

		// vtlib customization: Hook provide to enable generic module relation.
		if (isset($_REQUEST['createmode']) && $_REQUEST['createmode'] == 'link') {
			if (!empty($this->linkmodeid)) {
				$for_crmid = vtlib_purify($this->linkmodeid);
			} else {
				$for_crmid = vtlib_purify($_REQUEST['return_id']);
			}
			if (!empty($this->linkmodemodule)) {
				$for_module = vtlib_purify($this->linkmodemodule);
			} else {
				$for_module = vtlib_purify($_REQUEST['return_module']);
			}
			$with_module = $module;
			$with_crmid = $this->id;

			$on_focus = CRMEntity::getInstance($for_module);

			if ($for_module && $for_crmid && $with_module && $with_crmid) {
				relateEntities($on_focus, $for_module, $for_crmid, $with_module, $with_crmid);
			}
		}
	}

	public function insertIntoAttachment($id, $module, $direct_import = false) {
		global $log, $adb;
		if (empty($_FILES)) {
			return;
		}
		$log->debug("> insertIntoAttachment $id,$module");
		$file_saved = false;
		// get the list of uitype 69 fields so we can set their value
		$sql = 'SELECT tablename,columnname
		 FROM vtiger_field
		 INNER JOIN vtiger_blocks ON vtiger_blocks.blockid = vtiger_field.block
		 WHERE uitype=69 and vtiger_field.fieldname=? and vtiger_field.tabid = ?
		 ORDER BY vtiger_blocks.sequence,vtiger_field.sequence';
		$tabid = getTabid($module);
		foreach ($_FILES as $fileindex => $files) {
			if (!empty($files['name']) && $files['size'] > 0) {
				if (!empty($_REQUEST[$fileindex.'_hidden'])) {
					$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				} else {
					$files['original_name'] = stripslashes($files['name']);
				}
				$files['original_name'] = str_replace(array('"',':'), '', $files['original_name']);
				$result = $adb->pquery($sql, array($fileindex,$tabid));
				if (!$result || $adb->num_rows($result)==0) {
					continue;
				}
				$tblname = $adb->query_result($result, 0, 'tablename');
				$colname = $adb->query_result($result, 0, 'columnname');
				$fldname = $fileindex;
				// This is to store the existing attachment id so we can delete it when given a new image
				$attachmentname = (isset($this->DirectImageFieldValues[$colname]) ? $this->DirectImageFieldValues[$colname] : '');
				$old_attachmentrs = $adb->pquery('select vtiger_crmentity.crmid from vtiger_seattachmentsrel
				 inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid
				 inner join vtiger_attachments on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
				 where vtiger_seattachmentsrel.crmid=? and vtiger_attachments.name=?', array($id,$attachmentname));
				if ($old_attachmentrs && $adb->num_rows($old_attachmentrs)>0) {
					$old_attachmentid = $adb->query_result($old_attachmentrs, 0, 'crmid');
				} else {
					$old_attachmentid = 0;
				}
				$upd = "update $tblname set $colname=? where ".$this->tab_name_index[$tblname].'=?';
				$adb->pquery($upd, array($files['original_name'],$this->id));
				$this->column_fields[$fldname] = $files['original_name'];
				if (!empty($old_attachmentid)) {
					$setypers = $adb->pquery('select setype from '.$this->crmentityTable.' where crmid=?', array($old_attachmentid));
					$setype = $adb->query_result($setypers, 0, 'setype');
					if ($setype == 'Contacts Image' || $setype == $module.Field_Metadata::ATTACHMENT_ENTITY) {
						$cntrels = $adb->pquery('select count(*) as cnt from vtiger_seattachmentsrel where attachmentsid=?', array($old_attachmentid));
						$numrels = $adb->query_result($cntrels, 0, 'cnt');
					} else {
						$numrels = 0;
					}
				}
				$file_saved = $this->uploadAndSaveFile($id, $module, $files, $attachmentname, $direct_import, $fldname);
				// Remove the deleted attachments from db
				if ($file_saved && !empty($old_attachmentid) && ($setype == 'Contacts Image' || $setype == $module.Field_Metadata::ATTACHMENT_ENTITY)) {
					if ($numrels == 1) {
						$adb->pquery('delete from vtiger_attachments where attachmentsid=?', array($old_attachmentid));
					}
					$adb->pquery('delete from vtiger_seattachmentsrel where crmid = ? and attachmentsid=?', array($id, $old_attachmentid));
				}
			} elseif (isset($_REQUEST[$fileindex.'_canvas_image_set']) && $_REQUEST[$fileindex.'_canvas_image_set']==1 && !empty($_REQUEST[$fileindex.'_canvas_image'])) {
				$saveasfile = $module . '_' . $fileindex . '_' . date('YmdHis') . '.png';
				$fh = fopen('cache/images/'.$saveasfile, 'wb');
				$filecontent = $_REQUEST[$fileindex.'_canvas_image'];
				if (substr($filecontent, 0, strlen('data:image/png;base64,'))=='data:image/png;base64,') {
					// Base64 Encoded HTML5 Canvas image
					$filecontent = str_replace('data:image/png;base64,', '', $filecontent);
					$filecontent = str_replace(' ', '+', $filecontent);
				}
				fwrite($fh, base64_decode($filecontent));
				fclose($fh);
				$fi = array(
					'name' => $saveasfile,
					'original_name' => $saveasfile,
					'type' => 'image/png',
					'tmp_name' => 'cache/images/' . $saveasfile,
					'error' => 0,
					'size' => 0
				);
				$this->uploadAndSaveFile($id, $module, $fi, '', true, $fileindex);
				$result = $adb->pquery($sql, array($fileindex,$tabid));
				$tblname = $adb->query_result($result, 0, 'tablename');
				$colname = $adb->query_result($result, 0, 'columnname');
				$adb->pquery("update $tblname set $colname=? where ".$this->tab_name_index[$tblname].'=?', array($saveasfile,$this->id));
			} elseif (empty($files['name']) && $files['size'] == 0) {
				$result = $adb->pquery($sql, array($fileindex,$tabid));
				$tblname = $adb->query_result($result, 0, 'tablename');
				$colname = $adb->query_result($result, 0, 'columnname');
				if (empty($_REQUEST[$fileindex.'_hidden'])) {
					$upd = "update $tblname set $colname='' where ".$this->tab_name_index[$tblname].'=?';
					$adb->pquery($upd, array($this->id));
				} elseif (!empty($_REQUEST['__cbisduplicatedfromrecordid'])) {
					$attachmentname = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
					$isduplicatedfromrecordid = vtlib_purify($_REQUEST['__cbisduplicatedfromrecordid']);
					$old_attachmentrs = $adb->pquery('select vtiger_crmentity.crmid from vtiger_seattachmentsrel
					 inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid
					 inner join vtiger_attachments on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
					 where vtiger_seattachmentsrel.crmid=? and vtiger_attachments.name=?', array($isduplicatedfromrecordid,$attachmentname));
					if ($old_attachmentrs && $adb->num_rows($old_attachmentrs)>0) {
						$old_attachmentid = $adb->query_result($old_attachmentrs, 0, 'crmid');
						$upd = "update $tblname set $colname=? where ".$this->tab_name_index[$tblname].'=?';
						$adb->pquery($upd, array($attachmentname,$this->id));
						$adb->pquery('insert into vtiger_seattachmentsrel values(?,?)', array($id, $old_attachmentid));
					} else {
						$upd = "update $tblname set $colname='' where ".$this->tab_name_index[$tblname].'=?';
						$adb->pquery($upd, array($this->id));
					}
				}
			}
		}
		$log->debug('< insertIntoAttachment');
	}

	/**
	 * function used to upload the attachment in the server and save that attachment information in db.
	 * @param integer entity id to which the file will be uploaded
	 * @param string the current module name
	 * @param array contains the file information (name, type, size, tmp_name and error)
	 * @return boolean true if uploaded, false if the image is not secure or some other error occured
	 */
	public function uploadAndSaveFile($id, $module, $file_details, $attachmentname = '', $direct_import = false, $forfield = '') {
		global $log, $adb, $current_user, $upload_badext;
		$log->debug('> uploadAndSaveFile', [$id, $module, $file_details]);

		$date_var = date('Y-m-d H:i:s');

		//to get the owner id
		$ownerid = $this->column_fields['assigned_user_id'];
		if (!isset($ownerid) || $ownerid == '') {
			$ownerid = $current_user->id;
		}

		if (isset($file_details['original_name']) && $file_details['original_name'] != null) {
			$file_name = $file_details['original_name'];
		} else {
			$file_name = $file_details['name'];
		}

		$binFile = sanitizeUploadFileName($file_name, $upload_badext);

		$current_id = $adb->getUniqueID('vtiger_crmentity');

		$filename = ltrim(basename(' ' . $binFile)); //allowed filename like UTF-8 characters
		$filetype = $file_details['type'];
		$filetmp_name = $file_details['tmp_name'];

		if (validateImageFile($file_details) == 'true' && !validateImageContents($filetmp_name)) {
			$log->debug('< uploadAndSaveFile: skip save attachment process');
			return false;
		}

		//get the file path inwhich folder we want to upload the file
		$upload_file_path = decideFilePath();

		//upload the file in server
		if ($direct_import || !is_uploaded_file($filetmp_name)) {
			$upload_status = @copy($filetmp_name, $upload_file_path . $current_id . '_' . $binFile);
		} else {
			$upload_status = @move_uploaded_file($filetmp_name, $upload_file_path . $current_id . '_' . $binFile);
		}

		if ($upload_status && !empty($forfield)) {
			unset($_FILES[$forfield]);
		}

		if ($upload_status) {
			$description_val = empty($this->column_fields['description']) ? '' : $this->column_fields['description'];
			if (($module == 'Contacts' || $module == 'Products') && $forfield=='imagename') {
				$sql1 = 'insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)';
				$params1 = array(
					$current_id,
					$current_user->id,
					$ownerid,
					$module . ' Image',
					$description_val,
					$adb->formatDate($date_var, true),
					$adb->formatDate($date_var, true)
				);
			} else {
				$sql1 = 'insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)';
				$params1 = array(
					$current_id,
					$current_user->id,
					$ownerid,
					$module . Field_Metadata::ATTACHMENT_ENTITY,
					$description_val,
					$adb->formatDate($date_var, true),
					$adb->formatDate($date_var, true)
				);
			}
			$adb->pquery($sql1, $params1);

			$sql2 = 'insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)';
			$params2 = array($current_id, $filename, $description_val, $filetype, $upload_file_path);
			$adb->pquery($sql2, $params2);

			if (((isset($_REQUEST['mode']) && $_REQUEST['mode']=='edit') || $this->mode=='edit') && $id!='' && isset($_REQUEST['fileid']) && $_REQUEST['fileid']!='') {
				$adb->pquery('delete from vtiger_seattachmentsrel where crmid=? and attachmentsid=?', array($id, vtlib_purify($_REQUEST['fileid'])));
			}
			if ($module == 'Documents') {
				$query = 'delete from vtiger_seattachmentsrel where crmid = ?';
				$qparams = array($id);
				$adb->pquery($query, $qparams);
			}
			if ($module == 'Contacts' || (property_exists($this, 'HasDirectImageField') && $this->HasDirectImageField)) {
				if ($module == 'Contacts') {
					$imageattachment = 'Image';
				} else {
					$imageattachment = 'Attachment';
				}
				$att_sql = "select vtiger_seattachmentsrel.attachmentsid from vtiger_seattachmentsrel
				 inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid
				 inner join vtiger_attachments on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
				 where vtiger_crmentity.setype='$module $imageattachment'
				  and vtiger_attachments.name=?
				  and vtiger_seattachmentsrel.crmid=?";
				$res = $adb->pquery($att_sql, array($attachmentname,$id));
				$attachmentsid = $adb->query_result($res, 0, 'attachmentsid');
				if ($attachmentsid != '') {
					$cntrels = $adb->pquery('select count(*) as cnt from vtiger_seattachmentsrel where attachmentsid=?', array($attachmentsid));
					$numrels = $adb->query_result($cntrels, 0, 'cnt');
					$adb->pquery('delete from vtiger_seattachmentsrel where crmid=? and attachmentsid=?', array($id, $attachmentsid));
					if ($numrels == 1) {
						$adb->pquery('delete from vtiger_crmentity where crmid=?', array($attachmentsid));
					}
					$adb->pquery('insert into vtiger_seattachmentsrel values(?,?)', array($id, $current_id));
				} else {
					$adb->pquery('insert into vtiger_seattachmentsrel values(?,?)', array($id, $current_id));
				}
			} else {
				$adb->pquery('insert into vtiger_seattachmentsrel values(?,?)', array($id, $current_id));
			}
			return true;
		} else {
			$log->debug('< uploadAndSaveFile: skip save attachment process');
			return false;
		}
	}

	/** Function to insert values in the crmentity table for the specified module
	 * @param string module
	 */
	private function insertIntoCrmEntity($module) {
		global $adb, $current_user;
		$crmvalues = $this->getCrmEntityValues($module);
		$ownerid = $crmvalues['ownerid'];
		if ($this->mode == 'edit') {
			$userprivs = $current_user->getPrivileges();
			$tabid = getTabid($module);
			$cbuuidupdate = '';
			if (!empty($this->column_fields['cbuuid'])) {
				$cbuuidupdate = $adb->convert2Sql(',cbuuid=?', array($this->column_fields['cbuuid']));
			}
			if ($userprivs->hasGlobalReadPermission()) {
				$sql = "update vtiger_crmentity set smownerid=?,modifiedby=?,description=?,modifiedtime=? $cbuuidupdate where crmid=?";
				$params = array($ownerid, $current_user->id, $crmvalues['description'], $crmvalues['date'], $this->id);
			} else {
				$profileList = getCurrentUserProfileList();
				$perm_qry = 'SELECT 1
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid = vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
					WHERE vtiger_field.columnname=? AND vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND
						vtiger_profile2field.profileid IN (' . generateQuestionMarks($profileList) . ") AND
						vtiger_def_org_field.visible = 0 and vtiger_field.tablename='vtiger_crmentity' AND
						vtiger_field.displaytype in (1,3) and vtiger_field.presence in (0,2);";
				$perm_result = $adb->pquery($perm_qry, array('description', $tabid, $profileList));
				if ($adb->num_rows($perm_result)>0) {
					$sql = "update vtiger_crmentity set smownerid=?,modifiedby=?,description=?,modifiedtime=? $cbuuidupdate where crmid=?";
					$params = array($ownerid, $current_user->id, $crmvalues['description'], $crmvalues['date'], $this->id);
				} else {
					$sql = "update vtiger_crmentity set smownerid=?,modifiedby=?,modifiedtime=? $cbuuidupdate where crmid=?";
					$params = array($ownerid, $current_user->id, $crmvalues['date'], $this->id);
				}
			}
			$rdo = $adb->pquery($sql, $params);
			if ($rdo) {
				$adb->pquery(
					"UPDATE vtiger_crmobject set smownerid=?,modifiedtime=? $cbuuidupdate WHERE crmid=?",
					array($ownerid, $crmvalues['date'], $this->id)
				);
			}
			$sql1 = 'delete from vtiger_ownernotify where crmid=?';
			$params1 = array($this->id);
			$adb->pquery($sql1, $params1);
			if ($ownerid != $current_user->id) {
				$sql1 = 'insert into vtiger_ownernotify values(?,?,?)';
				$params1 = array($this->id, $ownerid, null);
				$adb->pquery($sql1, $params1);
			}
		} else {
			//if this is the create mode and the group allocation is chosen, then do the following
			$current_id = $adb->getUniqueID('vtiger_crmentity');
			$_REQUEST['currentid'] = $current_id;
			if ($current_user->id == '') {
				$current_user->id = 0;
			}
			$this->column_fields['record_id'] = $current_id;
			$this->column_fields['record_module'] = $module;
			if (empty($this->column_fields['cbuuid'])) {
				$this->column_fields['createdtime'] = $crmvalues['created_date'];
				$cbuuid = $this->getUUID();
			} else {
				$cbuuid = $this->column_fields['cbuuid'];
			}
			$sql = 'insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,modifiedby,createdtime,modifiedtime,cbuuid) values(?,?,?,?,?,?,?,?,?)';
			$params = array($current_id, $crmvalues['createdbyuser'], $ownerid, $module, $crmvalues['description'], $current_user->id, $crmvalues['created_date'], $crmvalues['modified_date'], $cbuuid);
			$rdo = $adb->pquery($sql, $params);
			if ($rdo) {
				$adb->pquery(
					'INSERT INTO vtiger_crmobject (crmid,deleted,setype,smownerid,modifiedtime,cbuuid) values (?,0,?,?,?,?)',
					array($current_id, $module, $ownerid, $crmvalues['modified_date'], $cbuuid)
				);
			}
			$this->id = $current_id;
		}
	}

	private function getCrmEntityValues($module) {
		global $adb, $current_user;
		$crmvalues = array();
		$crmvalues['module'] = $module;
		$crmvalues['date'] = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$crmvalues['created_date'] = $crmvalues['date'];
		$crmvalues['createdbyuser'] = $current_user->id;
		if (self::isBulkSaveMode()) {
			if (!empty($this->column_fields['createdtime'])) {
				$crmvalues['created_date'] = $adb->formatDate($this->column_fields['createdtime'], true);
			}
			if (!empty($this->column_fields['creator'])) {
				$crmvalues['createdbyuser'] = $this->column_fields['creator'];
			}
			//NOTE : modifiedtime ignored to support vtws_sync API track changes.
		}
		$crmvalues['modified_date'] = $crmvalues['date'];

		$ownerid = $this->sanitizeOwnerField($this->column_fields['assigned_user_id']);

		$res = $adb->pquery('select ownedby from vtiger_tab where name=?', array($module));
		$this->ownedby = $adb->query_result($res, 0, 'ownedby');

		if ($this->ownedby == 1) {
			$ownerid = $current_user->id;
		}
		if (empty($ownerid)) {
			if ($this->mode != 'edit') {
				$ownerid = $current_user->id;
			} else {
				$ownerrs = $adb->pquery('select smownerid from '.$this->crmentityTable.' where crmid=?', array($this->id));
				$ownerid = $adb->query_result($ownerrs, 0, 0);
			}
		}
		$crmvalues['ownerid'] = $ownerid;
		$crmvalues['description'] = (empty($this->column_fields['description']) ? '' : $this->column_fields['description']);
		return $crmvalues;
	}

	private function setCrmEntityValues($values) {
		global $current_user;
		$this->column_fields['created_user_id'] = $values['createdbyuser'];
		$this->column_fields['assigned_user_id'] = $this->sanitizeOwnerField($values['ownerid']);
		$this->column_fields['modifiedby'] = $current_user->id;
		$this->column_fields['createdtime'] = $values['created_date'];
		$this->column_fields['modifiedtime'] = $values['modified_date'];
		$this->column_fields['description'] = $values['description'];
	}

	public function sanitizeOwnerField($value, $defaultCurrent = true) {
		global $current_user;
		$ownerid = (empty($value) && $defaultCurrent) ? $current_user->id : $value;
		if (strpos($ownerid, 'x')>0) { // we have a WSid
			$usrWSid = vtws_getEntityId('Users');
			$grpWSid = vtws_getEntityId('Groups');
			list($inputWSid,$ownerid) = explode('x', $ownerid);
			if ($usrWSid!=$inputWSid && $grpWSid!=$inputWSid) {
				TerminateExecution::die('Invalid user id!');
			}
		}
		return $ownerid;
	}

	// Function which returns the value based on result type (array / ADODB ResultSet)
	private function resolve_query_result_value($result, $index, $columnname) {
		global $adb;
		if (is_array($result)) {
			return $result[$index][$columnname];
		} else {
			return $adb->query_result($result, $index, $columnname);
		}
	}

	/** Function to insert values in the specifed table for the specified module
	 * @param string table name
	 * @param string module
	 */
	private function insertIntoEntityTable($table_name, $module) {
		global $log, $current_user, $app_strings, $from_wf, $adb;
		$log->debug("> insertIntoEntityTable $module $table_name");
		$insertion_mode = $this->mode;

		//Checking if entry is already present so we have to update
		if ($insertion_mode == 'edit' && $table_name!='vtiger_invoice_recurring_info') {
			$tablekey = $this->tab_name_index[$table_name];
			// Make selection on the primary key of the module table to check.
			$check_query = "select $tablekey from $table_name where $tablekey=?";
			$check_result = $adb->pquery($check_query, array($this->id));

			$num_rows = $adb->num_rows($check_result);

			if ($num_rows <= 0) {
				$insertion_mode = '';
			}
			$creatingdisplay = '';
		} else {
			$creatingdisplay = ',5';
		}
		$this->column_fields['assigned_user_id'] = $this->sanitizeOwnerField($this->column_fields['assigned_user_id']);
		$selectFields = 'fieldname, columnname, uitype, typeofdata';

		$tabid = getTabid($module);
		$uniqueFieldsRestriction = 'vtiger_field.fieldid IN (select min(vtiger_field.fieldid) from vtiger_field where vtiger_field.tabid=? GROUP BY vtiger_field.columnname)';
		if ($insertion_mode == 'edit') {
			$update = array();
			$update_params = array();
			$userprivs = $current_user->getPrivileges();
			if (isset($from_wf) && $from_wf) {
				$sql = "select $selectFields from vtiger_field where $uniqueFieldsRestriction and tablename=? and displaytype in (1,3,4$creatingdisplay) and presence in (0,2)";
				$params = array($tabid, $table_name);
			} elseif ($userprivs->hasGlobalReadPermission()) {
				$sql = "select $selectFields from vtiger_field where $uniqueFieldsRestriction and tablename=? and displaytype in (1,3$creatingdisplay) and presence in (0,2)";
				$params = array($tabid, $table_name);
			} else {
				$profileList = getCurrentUserProfileList();
				if (count($profileList) > 0) {
					$sql = "SELECT distinct $selectFields
						FROM vtiger_field
						INNER JOIN vtiger_profile2field
						ON vtiger_profile2field.fieldid = vtiger_field.fieldid
						INNER JOIN vtiger_def_org_field
						ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
						WHERE $uniqueFieldsRestriction
						AND vtiger_profile2field.visible = 0 AND vtiger_profile2field.readonly = 0
						AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ")
						AND vtiger_def_org_field.visible = 0 and vtiger_field.tablename=? and vtiger_field.displaytype in (1,3$creatingdisplay) and vtiger_field.presence in (0,2)";
					$params = array($tabid, $profileList, $table_name);
				} else {
					$sql = "SELECT distinct $selectFields
						FROM vtiger_field
						INNER JOIN vtiger_profile2field
						ON vtiger_profile2field.fieldid = vtiger_field.fieldid
						INNER JOIN vtiger_def_org_field
						ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
						WHERE $uniqueFieldsRestriction
						AND vtiger_profile2field.visible = 0 AND vtiger_profile2field.readonly = 0
						AND vtiger_def_org_field.visible = 0 and vtiger_field.tablename=? and vtiger_field.displaytype in (1,3$creatingdisplay) and vtiger_field.presence in (0,2)";
					$params = array($tabid, $table_name);
				}
			}
		} else {
			$table_index_column = $this->tab_name_index[$table_name];
			if ($table_index_column == 'id' && $table_name == 'vtiger_users') {
				$this->id = $adb->getUniqueID('vtiger_users');
			}
			if ($this->denormalized && $table_name == $this->crmentityTable) {
				$this->id = $adb->getUniqueID('vtiger_crmentity');
				$this->setCrmEntityValues($this->getCrmEntityValues($module));
			}
			$column = array($table_index_column);
			$value = array($this->id);
			$sql = "select $selectFields from vtiger_field where $uniqueFieldsRestriction and tablename=? and displaytype in (1,3,4$creatingdisplay) and vtiger_field.presence in (0,2)";
			$params = array($tabid, $table_name);
		}

		// Attempt to re-use the query-result to avoid reading for every save operation
		static $_privatecache = array();

		$cachekey = md5($insertion_mode . $sql . json_encode($params));

		if (!isset($_privatecache[$cachekey])) {
			$result = $adb->pquery($sql, $params);
			$noofrows = $adb->num_rows($result);

			if (CRMEntity::isBulkSaveMode()) {
				$cacheresult = array();
				for ($i = 0; $i < $noofrows; ++$i) {
					$cacheresult[] = $adb->fetch_array($result);
				}
				$_privatecache[$cachekey] = $cacheresult;
			}
		} else { // Useful when doing bulk save
			$result = $_privatecache[$cachekey];
			$noofrows = count($result);
		}

		for ($i = 0; $i < $noofrows; $i++) {
			$fieldname = $this->resolve_query_result_value($result, $i, 'fieldname');
			$columname = $this->resolve_query_result_value($result, $i, 'columnname');
			$uitype = $this->resolve_query_result_value($result, $i, 'uitype');
			$typeofdata = $this->resolve_query_result_value($result, $i, 'typeofdata');

			$typeofdata_array = explode('~', $typeofdata);
			$datatype = $typeofdata_array[0];

			$ajaxSave = false;
			if ((isset($_REQUEST['file']) && $_REQUEST['file'] == 'DetailViewAjax' && isset($_REQUEST['ajxaction']) && $_REQUEST['ajxaction'] == 'DETAILVIEW'
				&& isset($_REQUEST['fldName']) && $_REQUEST['fldName'] != $fieldname)
				|| (isset($_REQUEST['action']) && $_REQUEST['action'] == 'MassEditSave' && !isset($_REQUEST[$fieldname.'_mass_edit_check'])
				&& (!isset($_REQUEST['ajxaction']) || $_REQUEST['ajxaction'] != 'Workflow'))
				|| (!empty($this->column_fields['__cbws_skipcurdbconv'.$fieldname]) || !empty($this->column_fields['__cbws_skipcurdbconvall']))
			) {
				$ajaxSave = true;
			}

			if ($uitype == 4 && $insertion_mode != 'edit') {
				$fldvalue = '';
				// Bulk Save Mode: Avoid generation of module sequence number, take care later.
				if (!CRMEntity::isBulkSaveMode()) {
					$fldvalue = $this->setModuleSeqNumber('increment', $module);
				}
				$this->column_fields[$fieldname] = $fldvalue;
			}
			if (isset($this->column_fields[$fieldname])) {
				if ($uitype == 56) {
					if ($this->column_fields[$fieldname] === 'on' || $this->column_fields[$fieldname] == 1) {
						$fldvalue = '1';
					} else {
						$fldvalue = '0';
					}
				} elseif ($uitype == 15 || $uitype == 16 || $uitype == 1613 || $uitype == 1614 || $uitype == 1615) {
					if ($this->column_fields[$fieldname] == $app_strings['LBL_NOT_ACCESSIBLE']) {
						//If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
						$sql = "select $columname from $table_name where " . $this->tab_name_index[$table_name] . '=?';
						$res = $adb->pquery($sql, array($this->id));
						$pick_val = $adb->query_result($res, 0, $columname);
						$fldvalue = $pick_val;
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
				} elseif ($uitype == 33 || $uitype == 3313 || $uitype == 3314 || $uitype == 1024 || $uitype == 1025) {
					if (empty($this->column_fields[$fieldname])) {
						$fldvalue = '';
					} else {
						if (!is_array($this->column_fields[$fieldname])) {
							$this->column_fields[$fieldname] = array_map('trim', explode('|##|', $this->column_fields[$fieldname]));
						}
						$sql = 'select columnname,tablename from vtiger_field where tabid=? and fieldname=?';
						$res = $adb->pquery($sql, array($tabid,$fieldname));
						$colj=$adb->query_result($res, 0, 0);
						$tabj = $adb->query_result($res, 0, 1);
						$sql1="select $colj from $tabj where " . $this->tab_name_index[$tabj] . '=?';
						$res = $adb->pquery($sql1, array($this->id));
						$vlera=$adb->query_result($res, 0, $colj);
						if (empty($vlera)) {
							$currentvalues = array();
						} else {
							$currentvalues = array_map('trim', explode('|##|', decode_html($vlera)));
						}
						$selectedvalues = $this->column_fields[$fieldname];
						if ($uitype == 3313 || $uitype == 3314) {
							$uservalues = getAllowedPicklistModules();
						} elseif ($uitype == 1025) {
							$uservalues = $currentvalues;
						} elseif ($uitype == 1024) {
							$roleid = $current_user->roleid;
							$subrole = getRoleSubordinates($roleid);
							$uservalues = array_merge($subrole, array($roleid));
						} else {
							$roleid=$current_user->roleid;
							$uservalues = getAssignedPicklistValues($fieldname, $roleid, $adb);
						}
						$vek=array_unique(array_merge(array_diff($currentvalues, $uservalues), $selectedvalues));
						$fldvalue = implode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $vek);
						if ($uitype == 3313 || $uitype == 3314) {
							// this value cannot be over 1010 characters if it has an index, so we cut it at that length always
							$fldvaluecut = substr($fldvalue, 0, 1010);
							if ($fldvalue!=$fldvaluecut) {
								$fldvalue = substr($fldvaluecut, 0, strrpos($fldvaluecut, Field_Metadata::MULTIPICKLIST_SEPARATOR));
							}
						}
					}
				} elseif ($uitype == 5 || $uitype == 6 || $uitype == 23) {
					//Added to avoid function call getDBInsertDateValue in ajax save
					if (isset($current_user->date_format) && !$ajaxSave) {
						$fldvalue = getValidDBInsertDateValue($this->column_fields[$fieldname]);
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
				} elseif ($uitype == 14 && empty($this->column_fields[$fieldname])) {
					$fldvalue = null;
				} elseif ($uitype == 50) {
					$timefmt = '';
					if (!empty($this->column_fields[$fieldname]) && strlen($this->column_fields[$fieldname])>16) {
						$seconds = substr($this->column_fields[$fieldname], -2);
						if (!is_numeric($seconds)) {
							$timefmt = $seconds;
							$this->column_fields[$fieldname] = substr($this->column_fields[$fieldname], 0, 16);
						}
					}
					if (isset($current_user->date_format) && !$ajaxSave) {
						$fldvalue = getValidDBInsertDateTimeValue($this->column_fields[$fieldname]);
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
					if (empty($fldvalue)) {
						$fldvalue = null;
					}
					if ($fldvalue != null && !$ajaxSave) {
						if (isset($_REQUEST['timefmt_' . $fieldname])) {
							$timefmt = vtlib_purify($_REQUEST['timefmt_' . $fieldname]);
							unset($_REQUEST['timefmt_' . $fieldname]);
						}
						$fldvalue = DateTimeField::formatDatebaseTimeString($fldvalue, $timefmt);
						$this->column_fields[$fieldname] = $fldvalue;
					}
				} elseif ($uitype == 26) {
					if (empty($this->column_fields[$fieldname])) {
						$fldvalue = 1; //the documents will stored in default folder
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
				} elseif ($uitype == 28) {
					if ($this->column_fields[$fieldname] == null) {
						$fileQuery = $adb->pquery('SELECT filename from vtiger_notes WHERE notesid=?', array($this->id));
						$fldvalue = null;
						if (isset($fileQuery)) {
							$rowCount = $adb->num_rows($fileQuery);
							if ($rowCount > 0) {
								$fldvalue = $adb->query_result($fileQuery, 0, 'filename');
							}
						}
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
				} elseif ($uitype == 8) {
					$this->column_fields[$fieldname] = rtrim($this->column_fields[$fieldname], ',');
					$ids = explode(',', $this->column_fields[$fieldname]);
					$fldvalue = json_encode($ids);
				} elseif ($uitype == 12) {
					// Bulk Save Mode: Consider the FROM email address as specified, if not lookup
					$fldvalue = $this->column_fields[$fieldname];
					if (empty($fldvalue)) {
						$query = 'SELECT email1 FROM vtiger_users WHERE id=?';
						$res = $adb->pquery($query, array($current_user->id));
						$rows = $adb->num_rows($res);
						if ($rows > 0) {
							$fldvalue = $adb->query_result($res, 0, 'email1');
						}
					}
				} elseif (($uitype == 72 || $uitype == 7 || $uitype == 9) && !$ajaxSave) {
					// Some of the currency fields like Unit Price, Total, Sub-total and normal numbers do not need currency conversion during save
					$fldvalue = CurrencyField::convertToDBFormat($this->column_fields[$fieldname], null, true);
					if ($insertion_mode == 'edit') {
						$fldvalue = $this->adjustCurrencyField($fieldname, $fldvalue, $tabid);
					}
				} elseif ($uitype == 71 && !$ajaxSave) {
					$fldvalue = CurrencyField::convertToDBFormat($this->column_fields[$fieldname]);
					if ($insertion_mode == 'edit') {
						$fldvalue = $this->adjustCurrencyField($fieldname, $fldvalue, $tabid);
					}
				} elseif ($uitype == '69m' || $uitype == '69') {
					$fldvalue = urldecode($this->column_fields[$fieldname]);
				} else {
					$fldvalue = $this->column_fields[$fieldname];
				}
			} else {
				$fldvalue = '';
			}
			if ($fldvalue == '') {
				$fldvalue = $this->get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
			}

			if ($insertion_mode == 'edit') {
				if ($table_name != 'vtiger_ticketcomments' && $uitype != 4) {
					$update[] = $columname . '=?';
					$update_params[] = $fldvalue;
				}
			} else {
				$column[] = $columname;
				$value[] = $fldvalue;
			}
		}
		$mtime = $adb->formatDate(date('Y-m-d H:i:s'), true);
		if ($this->denormalized && $table_name == $this->crmentityTable) {
			if ($insertion_mode == 'edit') {
				if (!empty($this->column_fields['cbuuid'])) {
					$update[] = 'cbuuid=?';
					$update_params[] = $this->column_fields['cbuuid'];
				}
				if (!in_array('modifiedtime=?', $update)) {
					$update[] = 'modifiedtime=?';
					$update_params[] = $mtime;
				}
				if (!in_array('modifiedby=?', $update)) {
					$update[] = 'modifiedby=?';
					$update_params[] = $current_user->id;
				}
			} else {
				$_REQUEST['currentid'] = $this->id;
				$this->column_fields['record_id'] = $this->id;
				$this->column_fields['record_module'] = $module;
				$this->column_fields['cbuuid'] = (empty($this->column_fields['cbuuid']) ? $this->getUUID() : $this->column_fields['cbuuid']);
				if (!in_array('crmid', $column)) {
					$column[] = 'crmid';
					$value[] = $this->id;
				}
				if (!in_array('setype', $column)) {
					$column[] = 'setype';
					$value[] = $module;
				}
				if (!in_array('cbuuid', $column)) {
					$column[] = 'cbuuid';
					$value[] = $this->column_fields['cbuuid'];
				}
				if (!in_array('createdtime', $column)) {
					$column[] = 'createdtime';
					$value[] =$this->column_fields['createdtime'];
				}
				if (!in_array('modifiedtime', $column)) {
					$column[] = 'modifiedtime';
					$value[] =$mtime;
				}
				if (!in_array('smcreatorid', $column)) {
					$column[] = 'smcreatorid';
					$value[] =$this->column_fields['created_user_id'];
				}
				if (!in_array('modifiedby', $column)) {
					$column[] = 'modifiedby';
					$value[] =$current_user->id;
				}
			}
		}
		$rdo = true;
		if ($insertion_mode == 'edit') {
			// If update is empty the query fails
			if (!empty($update)) {
				$sql1 = "update $table_name set " . implode(',', $update) . ' where ' . $this->tab_name_index[$table_name] . '=?';
				$update_params[] = $this->id;
				$rdo = $adb->pquery($sql1, $update_params);
				if ($rdo) {
					$adb->pquery(
						'UPDATE vtiger_crmobject set smownerid=?,modifiedtime=? WHERE crmid=?',
						array($this->column_fields['assigned_user_id'], $mtime, $this->id)
					);
					if (!empty($this->column_fields['cbuuid'])) {
						$adb->pquery('UPDATE vtiger_crmobject set cbuuid=? WHERE crmid=?', array($this->column_fields['cbuuid'], $this->id));
					}
				}
			}
		} else {
			$sql1 = "insert into $table_name(" . implode(',', $column) . ') values(' . generateQuestionMarks($value) . ')';
			$rdo = $adb->pquery($sql1, $value);
			if ($rdo) {
				$this->column_fields['cbuuid'] = (empty($this->column_fields['cbuuid']) ? $this->getUUID() : $this->column_fields['cbuuid']);
				if ($table_name == $this->crmentityTable && $this->denormalized) {
					$adb->pquery(
						'INSERT IGNORE INTO vtiger_crmobject (crmid,deleted,setype,smownerid,modifiedtime,cbuuid) values (?,0,?,?,?,?)',
						array($this->id, $module, $this->column_fields['assigned_user_id'], $this->column_fields['modifiedtime'], $this->column_fields['cbuuid'])
					);
				}
			}
		}
		if ($rdo===false) {
			$log->fatal($adb->getErrorMsg());
		}
	}

	/** Function to retrieve maximum decimal values of currency field on save
	 * @param string currency field name
	 * @param float currency value they want to save
	 * @param integer tabID of the module the field is on
	 * @return float field value from database with maximum decimals if it is the same as value being saved
	 */
	public function adjustCurrencyField($fieldname, $fldvalue, $tabid) {
		global $adb, $log, $current_user;
		$log->debug("> adjustCurrencyField $fieldname,$fldvalue");
		if (isset(self::$dbvalues[$fieldname])) {
			$dbvalue = self::$dbvalues[$fieldname];
		} else {
			$dbvals = $result = array();
			foreach ($this->tab_name_index as $table_name => $index) {
				$result = $adb->pquery("select * from $table_name where $index=?", array($this->id));
				if ($result && $adb->num_rows($result)>0) {
					$flds = $adb->fetch_array($result);
					$dbvals = array_merge($dbvals, $flds);
				}
			}
			self::$dbvalues = $dbvals;
			$dbvalue = empty(self::$dbvalues[$fieldname]) ? 0 : self::$dbvalues[$fieldname];
			$fldrs = $adb->pquery(
				'select fieldname,typeofdata from vtiger_field where vtiger_field.uitype in (7,9,71,72) and vtiger_field.tabid=?',
				array($tabid)
			);
			while ($fldinf = $adb->fetch_array($fldrs)) {
				self::$todvalues[$fldinf['fieldname']] = $fldinf['typeofdata'];
			}
		}
		$typeofdata = self::$todvalues[$fieldname];
		$decimals = CurrencyField::getDecimalsFromTypeOfData($typeofdata);
		if (round((float)$dbvalue, min($decimals, $current_user->no_of_currency_decimals))==$fldvalue) {
			$fldvalue = $dbvalue;
		}
		$log->debug('< adjustCurrencyField '.$fldvalue);
		return $fldvalue;
	}

	/** Function to delete a record in the specifed table
	 * @param string table name
	 * The function will delete a record. The id is obtained from the class variable $this->id and the columnname got from $this->tab_name_index[$table_name]
	 */
	public function deleteRelation($table_name) {
		global $adb;
		$check_query = "select * from $table_name where " . $this->tab_name_index[$table_name] . '=?';
		$check_result = $adb->pquery($check_query, array($this->id));
		$num_rows = $adb->num_rows($check_result);

		if ($num_rows == 1) {
			$del_query = "DELETE from $table_name where " . $this->tab_name_index[$table_name] . '=?';
			$adb->pquery($del_query, array($this->id));
		}
	}

	/** Function to attachment filename of the given entity
	 * @param integer crm ID
	 * The function will get the attachmentsid for the given entityid from vtiger_seattachmentsrel table and get the attachmentsname from vtiger_attachments table
	 * returns the 'filename'
	 */
	public function getOldFileName($notesid) {
		global $adb;
		$result = $adb->pquery('select * from vtiger_seattachmentsrel where crmid=?', array($notesid));
		$noofrows = $adb->num_rows($result);
		if ($noofrows != 0) {
			$attachmentid = $adb->query_result($result, 0, 'attachmentsid');
		}
		if ($attachmentid != '') {
			$rs = $adb->pquery('select * from vtiger_attachments where attachmentsid=?', array($attachmentid));
			$filename = $adb->query_result($rs, 0, 'name');
		}
		return $filename;
	}

	/** Function to retrieve the information of the given recordid
	 * @param integer Id
	 * @param string module
	 * This function retrieves the information from the database and sets the value in the class column_fields array
	 */
	public function retrieve_entity_info($record, $module, $deleted = false, $from_wf = false, $throwexception = false) {
		global $adb, $app_strings, $current_user;
		$result = array();

		//Here we check if user can see this record.
		if (!$from_wf && isPermitted($module, 'DetailView', $record) != 'yes') {
			$this->column_fields['record_id'] = $record;
			$this->column_fields['record_module'] = $module;
			return;
		}

		foreach ($this->tab_name_index as $table_name => $index) {
			$result[$table_name] = $adb->pquery("select * from $table_name where $index=?", array($record));
		}
		$isRecordDeleted = $adb->query_result($result[$this->crmentityTable], 0, 'deleted');
		if ($isRecordDeleted !== 0 && $isRecordDeleted !== '0' && !$deleted) {
			if ($throwexception) {
				throw new InvalidArgumentException($app_strings['LBL_RECORD_DELETE']." $module: $record", 1);
			} else {
				require_once 'Smarty_setup.php';
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign('APP', $app_strings);
				$smarty->assign('OPERATION_MESSAGE', $app_strings['LBL_RECORD_DELETE']." $module: $record");
				$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
				die();
			}
		}

		/* Block access to empty record */
		if (isset($this->table_name)) {
			$mod_index_col = $this->tab_name_index[$this->table_name];
			if ($adb->query_result($result[$this->table_name], 0, $mod_index_col) == '') {
				if ($throwexception) {
					throw new InvalidArgumentException($app_strings['LBL_RECORD_NOT_FOUND'], 1);
				} else {
					require_once 'Smarty_setup.php';
					$smarty = new vtigerCRM_Smarty();
					$smarty->assign('APP', $app_strings);
					$smarty->assign('OPERATION_MESSAGE', $app_strings['LBL_RECORD_NOT_FOUND']);
					$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
					if (GlobalVariable::getVariable('Debug_Record_Not_Found', false)) {
						echo '<div class="slds-m-around_x-large">';
						echo 'Looking for ' . $this->table_name . '.' . $mod_index_col . ' in <br>' . print_r($result[$this->table_name]->sql, true);
						echo '<pre>';
						debug_print_backtrace();
						echo '</pre></div>';
					}
					die();
				}
			}
		}

		// Lookup in cache for information
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);

		if ($cachedModuleFields === false) {
			$tabid = getTabid($module);

			// Let us pick up all the fields first so that we can cache information
			$sql1 = 'SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata, presence, defaultvalue, generatedtype FROM vtiger_field WHERE tabid=?';

			// NOTE: Need to skip in-active fields which we will be done later.
			$result1 = $adb->pquery($sql1, array($tabid));
			$noofrows = $adb->num_rows($result1);

			if ($noofrows) {
				while ($resultrow = $adb->fetch_array($result1)) {
					// Update information to cache for re-use
					VTCacheUtils::updateFieldInfo(
						$tabid,
						$resultrow['fieldname'],
						$resultrow['fieldid'],
						$resultrow['fieldlabel'],
						$resultrow['columnname'],
						$resultrow['tablename'],
						$resultrow['uitype'],
						$resultrow['typeofdata'],
						$resultrow['presence'],
						$resultrow['defaultvalue'],
						$resultrow['generatedtype']
					);
				}
			}

			// Get only active field information
			$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
		}

		if ($cachedModuleFields) {
			foreach ($cachedModuleFields as $fieldname => $fieldinfo) {
				$fieldcolname = $fieldinfo['columnname'];
				$tablename = $fieldinfo['tablename'];
				$fieldname = $fieldinfo['fieldname'];
				//Here we check if user has permissions to access this field.
				//If it is allowed then it will get the actual value, otherwise it gets an empty string.
				if ((!isset($from_wf) || !$from_wf) && getFieldVisibilityPermission($module, $current_user->id, $fieldname) != '0') {
					$this->column_fields[$fieldname] = '';
					continue;
				}
				// To avoid ADODB execption pick the entries that are in $tablename
				if (isset($result[$tablename])) {
					$fld_value = $adb->query_result($result[$tablename], 0, $fieldcolname);
				} else {
					$adb->println("There is no entry for this entity $record ($module) in the table $tablename");
					$fld_value = '';
				}
				$this->column_fields[$fieldname] = $fld_value;
			}
		}
		if ($module == 'Users') {
			for ($i = 0; $i < $noofrows; $i++) {
				$fieldcolname = $adb->query_result($result1, $i, 'columnname');
				$tablename = $adb->query_result($result1, $i, 'tablename');
				$fieldname = $adb->query_result($result1, $i, 'fieldname');
				$fld_value = $adb->query_result($result[$tablename], 0, $fieldcolname);
				$this->$fieldname = $fld_value;
			}
		}

		$this->column_fields['record_id'] = $record;
		$this->column_fields['record_module'] = $module;
		$this->column_fields['cbuuid'] = $adb->query_result($result[$this->crmentityTable], 0, 'cbuuid');
	}

	/** Function to retrieve the information of the given recordidS
	 * @param array of CRMIds
	 * @param string module
	 * This function retrieves the information from the database and sets the value in the class fetched_records array
	 */
	public function retrieve_entities_info($records, $module, $from_wf = false) {
		global $adb, $current_user;
		$result = array();
		$this->fetched_records = array();
		foreach ($this->tab_name_index as $table_name => $index) {
			$result[$table_name] = $adb->pquery("select * from $table_name where $index IN (" . generateQuestionMarks($records) . ') ', $records);
		}

		if (isset($this->table_name)) {
			$this->tab_name_index[$this->table_name];
		}

		// Lookup in cache for information
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);

		if ($cachedModuleFields === false) {
			$tabid = getTabid($module);

			// Let us pick up all the fields first so that we can cache information
			$sql1 = 'SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata, presence, defaultvalue, generatedtype FROM vtiger_field WHERE tabid=?';

			// NOTE: Need to skip in-active fields which we will be done later.
			$result1 = $adb->pquery($sql1, array($tabid));
			$noofrows = $adb->num_rows($result1);

			if ($noofrows) {
				while ($resultrow = $adb->fetch_array($result1)) {
					// Update information to cache for re-use
					VTCacheUtils::updateFieldInfo(
						$tabid,
						$resultrow['fieldname'],
						$resultrow['fieldid'],
						$resultrow['fieldlabel'],
						$resultrow['columnname'],
						$resultrow['tablename'],
						$resultrow['uitype'],
						$resultrow['typeofdata'],
						$resultrow['presence'],
						$resultrow['defaultvalue'],
						$resultrow['generatedtype']
					);
				}
			}

			// Get only active field information
			$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
		}

		if ($cachedModuleFields) {
			$cachedIDPermissions = array();
			foreach ($cachedModuleFields as $fieldname => $fieldinfo) {
				$fieldcolname = $fieldinfo['columnname'];
				$tablename = $fieldinfo['tablename'];
				$fieldname = $fieldinfo['fieldname'];
				//Here we check if user has permissions to access this field.
				//If it is allowed then it will get the actual value, otherwise it gets an empty string.
				$setittoempty = false;
				if (!$from_wf) {
					$setittoempty = (getFieldVisibilityPermission($module, $current_user->id, $fieldname) != '0');
				}
				// To avoid ADODB execption pick the entries that are in $tablename
				if (isset($result[$tablename]) && !$setittoempty) {
					for ($cn = 0; $cn < $adb->num_rows($result[$tablename]); $cn++) {
						if ($module=='Users') {
							$isRecordDeleted = $adb->query_result($result['vtiger_users'], $cn, 'deleted');
						} else {
							$isRecordDeleted = $adb->query_result($result[$this->crmentityTable], $cn, 'deleted');
						}
						if ($isRecordDeleted==0) {
							if ($module=='Users') {
								$tempid = $adb->query_result($result['vtiger_users'], $cn, 'id');
							} else {
								$tempid = $adb->query_result($result[$this->crmentityTable], $cn, 'crmid');
							}
							if (!$from_wf && !isset($cachedIDPermissions[$tempid])) {
								$cachedIDPermissions[$tempid] = isPermitted($module, 'DetailView', $tempid);
							}
							//Here we check if user can see this record.
							if (!$from_wf && $cachedIDPermissions[$tempid] != 'yes') {
								continue;
							}
							$fld_value = $adb->query_result($result[$tablename], $cn, $fieldcolname);
							$this->fetched_records[$tempid][$fieldname] = $fld_value;
							if (!isset($this->fetched_records[$tempid]['record_id'])) {
								$this->fetched_records[$tempid]['record_id'] = $tempid;
								$this->fetched_records[$tempid]['record_module'] = $module;
								if ($module=='Users') {
									$this->fetched_records[$tempid]['cbuuid'] = '';
								} else {
									$this->fetched_records[$tempid]['cbuuid'] = $adb->query_result($result[$this->crmentityTable], $cn, 'cbuuid');
								}
							}
						}
					}
				} elseif (!empty($result[$this->crmentityTable])) {
					for ($cn = 0; $cn < $adb->num_rows($result[$this->crmentityTable]); $cn++) {
						$isRecordDeleted = $adb->query_result($result[$this->crmentityTable], $cn, 'deleted');
						if ($isRecordDeleted==0) {
							$tempid = $adb->query_result($result[$this->crmentityTable], $cn, 'crmid');
							if (!$from_wf && !isset($cachedIDPermissions[$tempid])) {
								$cachedIDPermissions[$tempid] = isPermitted($module, 'DetailView', $tempid);
							}
							//Here we check if user can see this record.
							if (!$from_wf && $cachedIDPermissions[$tempid] != 'yes') {
								continue;
							}
							$tempid = $adb->query_result($result[$this->crmentityTable], $cn, 'crmid');
							$fld_value = '';
							$this->fetched_records[$tempid][$fieldname] = $fld_value;
						}
					}
				}
			}
		}
	}

	/** Validate values trying to be saved.
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @return array
	 *   saveerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 *   error_action: action to redirect to inside the same module in case of error. if redirected to EditView (default action)
	 *                 all values introduced by the user will be preloaded
	 *   returnvalues: a urlencoded string of values to send to the receiving page. may be empty
	 */
	public function preSaveCheck($request) {
		list($request,$void,$saveerror,$errormessage,$error_action,$returnvalues) =
			cbEventHandler::do_filter('corebos.filter.preSaveCheck', array($request,$this,false,'','',''));
		if (!$saveerror && !empty($_FILES)) {
			foreach ($_FILES as $file_details) {
				if (validateImageFile($file_details) == 'true' && !validateImageContents($file_details['tmp_name'])) {
					$saveerror = true;
					$errormessage = getTranslatedString('LBL_IMAGESECURITY_ERROR');
					$error_action = 'EditView';
					$returnvalues = '';
				}
			}
		}
		return array($saveerror,$errormessage,$error_action,$returnvalues);
	}

	/** Validate record trying to be deleted.
	 * @return array
	 *   delerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 */
	public function preDeleteCheck() {
		list($void,$delerror,$errormessage) = cbEventHandler::do_filter('corebos.filter.preDeleteCheck', array($this,false,''));
		return array($delerror,$errormessage);
	}

	/** Check launched when entering Edit View, called after creating object and loading variables. Will be empty on create
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @param object smarty template object in order to load variables for output
	 * @return void
	 */
	public function preEditCheck($request, $smarty) {
		list($request,$smarty,$void) = cbEventHandler::do_filter('corebos.filter.preEditCheck', array($request,$smarty,$this));
		return '';
	}

	/** Check launched when entering full Record View, called after creating object and loading variables.
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @param object smarty template object in order to load variables for output
	 * @return void
	 */
	public function preViewCheck($request, $smarty) {
		list($request,$smarty,$void) = cbEventHandler::do_filter('corebos.filter.preViewCheck', array($request,$smarty,$this));
		return '';
	}

	/** Function to saves the values in all the tables mentioned in the class variable $tab_name for the specified module
	 * @param string module
	 */
	public function save($module_name) {
		global $current_user, $adb;
		if (!empty($_REQUEST['FILTERFIELDSMAP'])) {
			$bmapname = vtlib_purify($_REQUEST['FILTERFIELDSMAP']);
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$mtype = $cbMap->column_fields['maptype'];
				$mdmap = $cbMap->$mtype();
				$targetmodule = $mdmap['targetmodule'];
				$targetfield = $mdmap['linkfields']['targetfield'];
				if ($targetmodule == $module_name) {
					if ($targetfield != '') {
						$MDCurrentRecord = coreBOS_Session::get('MDCurrentRecord');
						$this->column_fields[$targetfield] = $MDCurrentRecord;
					}
					if ($this->mode=='' && $mtype=='MasterDetailLayout' && !empty($mdmap['sortfield'])) {
						$qg = new QueryGenerator($mdmap['targetmodule'], $current_user);
						$qg->setFields([$mdmap['sortfield']]);
						$qg->addReferenceModuleFieldCondition(
							$mdmap['originmodule'],
							$mdmap['linkfields']['targetfield'],
							'id',
							$this->column_fields[$mdmap['linkfields']['targetfield']],
							'e',
							QueryGenerator::$AND
						);
						$sql = $qg->getQuery(); // No conditions
						$maxsql = mkMaxQuery($sql, $mdmap['sortfield']);
						$rs = $adb->query($maxsql);
						$max = $rs->fields['max'];
						$this->column_fields[$mdmap['sortfield']] = $max+1;
					} else {
						if (!empty($mdmap['editfieldnames'])) {
							$stillindb = CRMEntity::getInstance($module_name);
							$stillindb->retrieve_entity_info($this->id, $module_name, false, true);
							$handler = vtws_getModuleHandlerFromName($module_name, $current_user);
							$meta = $handler->getMeta();
							$stillindb->column_fields = DataTransform::sanitizeRetrieveEntityInfo($stillindb->column_fields, $meta);
							foreach ($stillindb->column_fields as $fname => $fvalue) {
								if (!in_array($fname, $mdmap['editfieldnames'])) {
									$this->column_fields[$fname] = $fvalue;
								}
							}
						}
					}
				}
			}
		}
		//Check if assigned_user_id is empty for assign the current user.
		if (empty($this->column_fields['assigned_user_id'])) {
			global $current_user;
			$_REQUEST['assigned_user_id'] = $current_user->id;
			$this->column_fields['assigned_user_id'] = $current_user->id;
			$_REQUEST['assigntype'] = 'U';
		}
		// get is duplicate from id if present and not set
		if (empty($this->column_fields['isduplicatedfromrecordid']) && !empty($_REQUEST['__cbisduplicatedfromrecordid'])) {
			$this->column_fields['isduplicatedfromrecordid'] = vtlib_purify($_REQUEST['__cbisduplicatedfromrecordid']);
		}

		//Event triggering code
		require_once 'include/events/include.inc';
		global $adb;

		$em = new VTEventsManager($adb);
		// Initialize Event trigger cache
		$em->initTriggerCache();
		$entityData = VTEntityData::fromCRMEntity($this);

		$em->triggerEvent('vtiger.entity.beforesave.modifiable', $entityData);
		$em->triggerEvent('vtiger.entity.beforesave', $entityData);
		$em->triggerEvent('vtiger.entity.beforesave.final', $entityData);
		//Event triggering code ends
		//GS Save entity being called with the modulename as parameter
		$this->saveentity($module_name);

		//Event triggering code
		$em->triggerEvent('vtiger.entity.aftersave.first', $entityData);
		$em->triggerEvent('vtiger.entity.aftersave', $entityData);
		$em->triggerEvent('vtiger.entity.aftersave.final', $entityData);
		//Event triggering code ends
	}

	/** Mark an item as deleted */
	public function mark_deleted($id) {
		global $current_user, $adb;
		$mtime = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$adb->pquery(
			'UPDATE '.$this->crmentityTable.' set deleted=1,modifiedtime=?,modifiedby=? where crmid=?',
			array($mtime, $current_user->id, $id),
			true,
			'Error marking record deleted: '
		);
		$adb->pquery('UPDATE vtiger_crmobject set deleted=1,modifiedtime=? WHERE crmid=?', array($mtime, $id));
	}

	/** Mark an item as undeleted */
	public function mark_undeleted($id) {
		global $adb;
		$adb->pquery('UPDATE '.$this->crmentityTable.' set deleted=0 where crmid=?', array($id));
		$adb->pquery('UPDATE vtiger_crmobject set deleted=0 WHERE crmid=?', array($id));
	}

	// this method is called during an import before inserting a bean
	// define an associative array called $special_fields
	// the keys are user defined, and don't directly map to the bean's vtiger_fields
	// the value is the method name within that bean that will do extra
	// processing for that vtiger_field. example: 'full_name'=>'get_names_from_full_name'
	public function process_special_fields() {
		foreach ($this->special_functions as $func_name) {
			if (method_exists($this, $func_name)) {
				$this->$func_name();
			}
		}
	}

	/**
	 * Function to check if the custom field table exists
	 * @param string table name to check
	 * @return boolean
	 */
	public function checkIfCustomTableExists($tablename) {
		global $adb;
		$result = $adb->query('select 1 from '.$adb->sql_escape_string($tablename));
		return $result && $adb->num_fields($result)>0;
	}

	/**
	 * function to construct the query to fetch the custom vtiger_fields
	 * return the query to fetch the custom vtiger_fields
	 */
	public function constructCustomQueryAddendum($tablename, $module) {
		global $adb;
		$tabid = getTabid($module);
		$sql1 = 'select columnname,fieldlabel from vtiger_field where generatedtype=2 and tabid=? and vtiger_field.presence in (0,2)';
		$result = $adb->pquery($sql1, array($tabid));
		$numRows = $adb->num_rows($result);
		$sql3 = 'select ';
		for ($i = 0; $i < $numRows; $i++) {
			$columnName = $adb->query_result($result, $i, 'columnname');
			$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
			//construct query as below
			if ($i == 0) {
				$sql3 .= $tablename . '.' . $columnName . " '" . $fieldlabel . "'";
			} else {
				$sql3 .= ', ' . $tablename . '.' . $columnName . " '" . $fieldlabel . "'";
			}
		}
		if ($numRows > 0) {
			$sql3 = $sql3 . ',';
		}
		return $sql3;
	}

	/**
	 * Track the viewing of a detail record.
	 * @param integer user that is viewing the record
	 * @param string module
	 * @param integer record ID
	 */
	public function track_view($user_id, $current_module, $id = '') {
		$tracker = new Tracker();
		$tracker->track_view($user_id, $current_module, $id, '');
	}

	/**
	 * Function to get the column value of a field when the field value is empty ''
	 * @param string column name for the field
	 * @param string input value for the field taken from the User
	 * @param string name of the Field
	 * @param string UI type of the field
	 * @return string column value of the field
	 */
	public function get_column_value($columnname, $fldvalue, $fieldname, $uitype, $datatype = '') {
		global $log, $current_user;
		$log->debug("> get_column_value $columnname, $fldvalue, $fieldname, $uitype, $datatype");
		if ($uitype==52 && $fldvalue=='') {
			return $current_user->id;
		}
		if (is_uitype($uitype, '_date_') && $fldvalue == '') {
			return null;
		}
		if ($datatype == 'I' || $datatype == 'N' || $datatype == 'NN' || $uitype == 10 || $uitype == 101) {
			return 0;
		}
		$log->debug('< get_column_value');
		return $fldvalue;
	}

	/**
	 * Function to make change to column fields, depending on the current user's accessibility for the fields
	 */
	public function apply_field_security() {
		global $current_user, $currentModule;
		foreach ($this->column_fields as $fieldname => $fieldvalue) {
			$reset_value = false;
			if (getFieldVisibilityPermission($currentModule, $current_user->id, $fieldname) != '0') {
				$reset_value = true;
			}
			if ($fieldname == 'record_id' || $fieldname == 'record_module') {
				$reset_value = false;
			}
			if ($reset_value) {
				$this->column_fields[$fieldname] = '';
			}
		}
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	public function getDuplicatesQuery($module, $table_cols, $field_values, $ui_type_arr, $select_cols = '') {
		global $current_user, $adb;
		$customView = new CustomView($module);
		$viewid = $customView->getViewId($module);
		$queryGenerator = new QueryGenerator($module, $current_user);
		try {
			if ($viewid != '0') {
				$queryGenerator->initForCustomViewById($viewid);
			} else {
				$queryGenerator->initForDefaultCustomView();
			}
			$list_query = $queryGenerator->getQuery();
		} catch (Exception $e) {
			$list_query = '';
		}
		$fromclause = explode('FROM', $list_query);
		$list_query = "SELECT $this->table_name.$this->table_index as id FROM ".$fromclause[1];
		$tableName = strtolower("temp".$module.$current_user->id);
		$adb->pquery("create temporary table IF NOT EXISTS $tableName (id int primary key) AS " . $list_query, array());
		$adb->pquery("create temporary table IF NOT EXISTS $tableName".'2 (id int primary key) AS ' . $list_query, array());

		$select_clause = 'SELECT '. $this->table_name .'.'.$this->table_index .' AS recordid, vtiger_users_last_import.deleted,'.$table_cols;
		$from_clause = " FROM $this->table_name";
		$from_clausesub = " FROM $this->table_name";
		$from_clause .= ' INNER JOIN '.$this->crmentityTableAlias." ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";
		$from_clausesub .= ' INNER JOIN '.$this->crmentityTableAlias." ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";
		// Consider custom table join as well.
		if (isset($this->customFieldTable)) {
			$from_clause.=' INNER JOIN '.$this->customFieldTable[0].' ON '.$this->customFieldTable[0].'.'.$this->customFieldTable[1]."=$this->table_name.$this->table_index";
			$from_clausesub.=' INNER JOIN '.$this->customFieldTable[0].' ON '.$this->customFieldTable[0].'.'.$this->customFieldTable[1]."=$this->table_name.$this->table_index";
		}
		$from_clause.=' INNER JOIN '.$tableName.' temptab ON temptab.id='.$this->table_name .'.'.$this->table_index;
		$from_clausesub.=' INNER JOIN '.$tableName.'2 temptab2 ON temptab2.id='.$this->table_name .'.'.$this->table_index;

		$from_clause .= ' LEFT JOIN vtiger_users ON vtiger_users.id='.$this->crmentityTable.'.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid='.$this->crmentityTable.'.smownerid';
		$from_clausesub .= ' LEFT JOIN vtiger_users ON vtiger_users.id='.$this->crmentityTable.'.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid='.$this->crmentityTable.'.smownerid';

		$where_clause = ' WHERE '.$this->crmentityTable.'.deleted = 0';
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM $this->table_name AS t INNER JOIN ".$this->crmentityTable." AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if (isset($this->customFieldTable)) {
				$sub_query .= ' LEFT JOIN '.$this->customFieldTable[0].' tcf ON tcf.'.$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clausesub $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		return $select_clause . $from_clause .
			' LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=' . $this->table_name .'.'.$this->table_index .
			' INNER JOIN (' . $sub_query . ') AS temp ON '.get_on_clause($field_values, $ui_type_arr, $module) .
			$where_clause .
			" ORDER BY $table_cols,". $this->table_name .'.'.$this->table_index .' ASC';
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 * $srcrecord could be empty
	 */
	public function getQueryByModuleField($module, $fieldname, $srcrecord, $query = '') {
		return false;
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	public function getListQuery($module, $usewhere = '') {
		global $current_user, $adb;
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if (!empty($this->customFieldTable)) {
			$query .= ', ' . $this->customFieldTable[0] . '.* ';
		}

		$query .= " FROM $this->table_name";
		$query .= ' INNER JOIN '.$this->crmentityTableAlias." ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = $this->crmentityTable;

		// Consider custom table join as well.
		if (!empty($this->customFieldTable)) {
			$query.=" INNER JOIN ".$this->customFieldTable[0].' ON '.$this->customFieldTable[0].'.'.$this->customFieldTable[1]." = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0];
		}
		$query .= ' LEFT JOIN vtiger_users ON vtiger_users.id = '.$this->crmentityTable.'.smownerid';
		$query .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = '.$this->crmentityTable.'.smownerid';

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';

		$linkedModulesQuery = $adb->pquery(
			'SELECT distinct tablename, columnname, relmodule
			FROM vtiger_field
			INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid'
			." WHERE uitype='10' AND vtiger_fieldmodulerel.module=?",
			array($module)
		);
		$linkedFieldsCount = $adb->num_rows($linkedModulesQuery);

		for ($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $adb->query_result($linkedModulesQuery, $i, 'relmodule');
			$tablename = $adb->query_result($linkedModulesQuery, $i, 'tablename');
			$columnname = $adb->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);

			if (!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $tablename.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		$query .= $this->getNonAdminAccessControlQuery($module, $current_user);
		$query .= ' WHERE '.$this->crmentityTable.'.deleted=0 '.$usewhere;
		return $query;
	}

	/**
	 * Create query to export the records.
	 */
	public function create_export_query($where) {
		global $current_user, $adb;
		$thismodule = $_REQUEST['module'];

		include_once 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, 'detail_view');

		$fields_list = getFieldsListFromQuery($sql);
		if ($thismodule=='Faq') {
			$fields_list = str_replace(",vtiger_faqcomments.comments as 'Add Comment'", ' ', $fields_list);
		}
		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name
			FROM ".$this->crmentityTableAlias." INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if (!empty($this->customFieldTable)) {
			$query .= ' INNER JOIN '.$this->customFieldTable[0].' ON '.$this->customFieldTable[0].'.'.$this->customFieldTable[1]."= $this->table_name.$this->table_index";
		}

		$query .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = '.$this->crmentityTable.'.smownerid';
		$query .= " LEFT JOIN vtiger_users ON ".$this->crmentityTable.".smownerid = vtiger_users.id and vtiger_users.status='Active'";
		$query .= " LEFT JOIN vtiger_users as vtigerCreatedBy ON ".$this->crmentityTable.".smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'";

		$linkedModulesQuery = $adb->pquery('SELECT distinct fieldname, tablename, columnname, relmodule FROM vtiger_field' .
			' INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid' .
			" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $adb->num_rows($linkedModulesQuery);

		$rel_mods = array();
		$rel_mods[$this->table_name] = 1;
		for ($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $adb->query_result($linkedModulesQuery, $i, 'relmodule');
			$columnname = $adb->query_result($linkedModulesQuery, $i, 'columnname');
			$tablename = $adb->query_result($linkedModulesQuery, $i, 'tablename');

			$other = CRMEntity::getInstance($related_module);

			if (!empty($rel_mods[$other->table_name])) {
				$rel_mods[$other->table_name] = $rel_mods[$other->table_name] + 1;
				$alias = $other->table_name.$rel_mods[$other->table_name];
				$query_append = "as $alias";
			} else {
				$alias = $other->table_name;
				$query_append = '';
				$rel_mods[$other->table_name] = 1;
			}

			$query .= " LEFT JOIN $other->table_name $query_append ON $alias.$other->table_index = $tablename.$columnname";
		}

		include_once 'include/fields/metainformation.php';
		$tabid = getTabid($thismodule);
		$result = $adb->pquery('select tablename, fieldname, columnname from vtiger_field where tabid=? and uitype=?', array($tabid, Field_Metadata::UITYPE_ACTIVE_USERS));
		while ($row = $adb->fetchByAssoc($result)) {
			$tableName = $row['tablename'];
			$fieldName = $row['fieldname'];
			$columName = $row['columnname'];
			$query .= ' LEFT JOIN vtiger_users as vtiger_users'.$fieldName.' ON vtiger_users'.$fieldName.'.id='.$tableName.'.'.$columName;
		}
		$query .= $this->getNonAdminAccessControlQuery($thismodule, $current_user);
		$where_auto = ' '.$this->crmentityTable.'.deleted=0';

		if ($where != '') {
			$query .= " WHERE ($where) AND $where_auto";
		} else {
			$query .= " WHERE $where_auto";
		}

		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	public function initImport($module) {
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	public function create_import_query($module) {
		global $current_user;
		return 'SELECT '.$this->crmentityTable.".crmid,
				case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, $this->table_name.*
			FROM $this->table_name"
			.($this->denormalized ? '' : "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$this->table_name.$this->table_index")
			.'LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id='.$this->crmentityTable.'.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id='.$this->crmentityTable.'.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid='.$this->crmentityTable.".smownerid
			WHERE vtiger_users_last_import.assigned_user_id='$current_user->id'
			AND vtiger_users_last_import.bean_type='$module'
			AND vtiger_users_last_import.deleted=0";
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	public function set_import_assigned_user() {
		global $current_user, $adb;
		$record_user = $this->column_fields['assigned_user_id'];

		if ($record_user != $current_user->id) {
			$sqlresult = $adb->pquery(
				'select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?',
				array($record_user, $record_user)
			);
			if ($adb->num_rows($sqlresult)!= 1) {
				$this->column_fields['assigned_user_id'] = $current_user->id;
			} else {
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields['assigned_user_id'] = $row['id'];
				} else {
					$this->column_fields['assigned_user_id'] = $current_user->id;
				}
			}
		}
	}

	/**
	 * Function invoked during export of module record value.
	 */
	public function transform_export_value($key, $value) {
		if ($key == 'owner' || $key == 'reports_to_id' || $key == 'comercial') {
			return getOwnerName($value);
		}
		return $value;
	}

	/**
	 * Function to initialize the importable fields array, based on the User's accessibility to the fields
	 */
	public function initImportableFields($module) {
		global $current_user;
		$skip_uitypes = array('4'); // uitype 4 is for Mod numbers
		// Look at cache if the fields information is available.
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);

		if ($cachedModuleFields === false) {
			getColumnFields($module); // This API will initialize the cache as well
			// We will succeed now due to above function call
			$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
		}

		$colf = array();
		if ($cachedModuleFields) {
			foreach ($cachedModuleFields as $fieldinfo) {
				// Skip non-supported fields
				if (!in_array($fieldinfo['uitype'], $skip_uitypes)) {
					$colf[$fieldinfo['fieldname']] = $fieldinfo['uitype'];
				}
			}
		}

		foreach ($colf as $key => $value) {
			if (getFieldVisibilityPermission($module, $current_user->id, $key, 'readwrite') == '0') {
				$this->importable_fields[$key] = $value;
			}
		}
	}

	/** Function to initialize the required fields array for that particular module */
	public function initRequiredFields($module) {
		global $adb;

		$tabid = getTabId($module);
		$sql = "select fieldname from vtiger_field where tabid= ? and typeofdata like '%M%' and uitype not in ('53','70') and vtiger_field.presence in (0,2)";
		$result = $adb->pquery($sql, array($tabid));
		$numRows = $adb->num_rows($result);
		for ($i = 0; $i < $numRows; $i++) {
			$fieldName = $adb->query_result($result, $i, 'fieldname');
			$this->required_fields[$fieldName] = 1;
		}
	}

	/** Function to delete an entity with given Id */
	public function trash($module, $id) {
		global $current_user, $adb;

		$setype = getSalesEntityType($id);
		if ($setype != $module && !($module == 'cbCalendar' && $setype == 'Emails')) { // security
			return false;
		}
		require_once 'include/events/include.inc';
		$em = new VTEventsManager($adb);

		// Initialize Event trigger cache
		$em->initTriggerCache();

		$entityData = VTEntityData::fromEntityId($adb, $id);

		$em->triggerEvent('vtiger.entity.beforedelete', $entityData);

		$this->mark_deleted($id);
		$this->unlinkDependencies($module, $id);

		require_once 'include/freetag/freetag.class.php';
		$freetag = new freetag();
		$freetag->delete_all_object_tags_for_user($current_user->id, $id);

		$sql_recentviewed = 'DELETE FROM vtiger_tracker WHERE user_id = ? AND item_id = ?';
		$adb->pquery($sql_recentviewed, array($current_user->id, $id));

		if ($em) {
			$entityData->SetDeleted($id);
			$em->triggerEvent('vtiger.entity.afterdelete', $entityData);
		}
	}

	/** Function to unlink all the dependent entities of the given Entity by Id */
	public function unlinkDependencies($module, $id) {
		global $adb;
		if (getSalesEntityType($id)!=$module) { // security
			return false;
		}
		$fieldRes = $adb->pquery('SELECT tabid, tablename, columnname FROM vtiger_field WHERE fieldid IN (
			SELECT fieldid FROM vtiger_fieldmodulerel WHERE relmodule=?)', array($module));
		$numOfFields = $adb->num_rows($fieldRes);
		for ($i = 0; $i < $numOfFields; $i++) {
			$tabId = $adb->query_result($fieldRes, $i, 'tabid');
			$tableName = $adb->query_result($fieldRes, $i, 'tablename');
			$columnName = $adb->query_result($fieldRes, $i, 'columnname');

			$relatedModule = vtlib_getModuleNameById($tabId);
			$focusObj = CRMEntity::getInstance($relatedModule);

			//Backup Field Relations for the deleted entity
			$relQuery = "SELECT $focusObj->table_index FROM $tableName WHERE $columnName=?";
			$relResult = $adb->pquery($relQuery, array($id));
			$numOfRelRecords = $adb->num_rows($relResult);
			if ($numOfRelRecords > 0) {
				$recordIdsList = array();
				for ($k = 0; $k < $numOfRelRecords; $k++) {
					$recordIdsList[] = $adb->query_result($relResult, $k, $focusObj->table_index);
				}
				$params = array($id, RB_RECORD_UPDATED, $tableName, $columnName, $focusObj->table_index, implode(',', $recordIdsList));
				$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
			}
		}
	}

	/** Function to unlink an entity with given Id from another entity */
	public function unlinkRelationship($id, $return_module, $return_id) {
		global $currentModule, $adb;
		$data = array();
		$data['sourceModule'] = getSalesEntityType($id);
		$data['sourceRecordId'] = $id;
		$data['destinationModule'] = $return_module;
		$data['destinationRecordId'] = $return_id;
		cbEventHandler::do_action('corebos.entity.link.delete', $data);

		$query = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
		$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
		$adb->pquery($query, $params);

		$fieldRes = $adb->pquery(
			'SELECT tabid, tablename, columnname FROM vtiger_field WHERE fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE module=? AND relmodule=?)',
			array($currentModule, $return_module)
		);
		$numOfFields = $adb->num_rows($fieldRes);
		for ($i = 0; $i < $numOfFields; $i++) {
			$tabId = $adb->query_result($fieldRes, $i, 'tabid');
			$tableName = $adb->query_result($fieldRes, $i, 'tablename');
			$columnName = $adb->query_result($fieldRes, $i, 'columnname');

			$relatedModule = vtlib_getModuleNameById($tabId);
			$focusObj = CRMEntity::getInstance($relatedModule);

			$updateQuery = "UPDATE $tableName SET $columnName=? WHERE $columnName=? AND $focusObj->table_index=?";
			$updateParams = array(null, $return_id, $id);
			$adb->pquery($updateQuery, $updateParams);
		}
		cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
	}

	/** Function to restore a deleted record of specified module with given crmid
	 * @param string module name
	 * @param array list of crmids
	 */
	public function restore($module, $id) {
		global $current_user, $adb;

		$adb->println("> restore $module, $id");
		$adb->startTransaction();

		$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$query = 'UPDATE '.$this->crmentityTable.' SET deleted=0,modifiedtime=?,modifiedby=? WHERE crmid = ?';
		$adb->pquery($query, array($date_var, $current_user->id, $id), true, 'Error restoring records :');
		$adb->pquery('UPDATE vtiger_crmobject SET deleted=0,modifiedtime=? WHERE crmid=?', array($date_var, $id), true, 'Error restoring records :');
		//Restore related entities/records
		$this->restoreRelatedRecords($module, $id);

		//Event triggering code
		require_once 'include/events/include.inc';
		$em = new VTEventsManager($adb);

		// Initialize Event trigger cache
		$em->initTriggerCache();

		$this->id = $id;
		$entityData = VTEntityData::fromCRMEntity($this);
		//Event triggering code
		$em->triggerEvent('vtiger.entity.afterrestore', $entityData);
		//Event triggering code ends

		$adb->completeTransaction();
		$adb->println('< restore');
	}

	/** Function to restore all the related records of a given record by id */
	public function restoreRelatedRecords($module, $record) {
		global $adb;
		$result = $adb->pquery('SELECT * FROM vtiger_relatedlists_rb WHERE entityid = ?', array($record));
		$numRows = $adb->num_rows($result);
		for ($i = 0; $i < $numRows; $i++) {
			$action = $adb->query_result($result, $i, 'action');
			$rel_table = $adb->query_result($result, $i, 'rel_table');
			$rel_column = $adb->query_result($result, $i, 'rel_column');
			$ref_column = $adb->query_result($result, $i, 'ref_column');
			$related_crm_ids = $adb->query_result($result, $i, 'related_crm_ids');

			if (strtoupper($action) == RB_RECORD_UPDATED) {
				$related_ids = explode(',', $related_crm_ids);
				if ($rel_table == 'vtiger_crmentity' && $rel_column == 'deleted') {
					$sql = "UPDATE $rel_table set $rel_column = 0 WHERE $ref_column IN (" . generateQuestionMarks($related_ids) . ')';
					$sql = 'UPDATE vtiger_crmobject set deleted=0 WHERE crmid IN (' . generateQuestionMarks($related_ids) . ')';
					$adb->pquery($sql, array($related_ids));
				} else {
					$sql = "UPDATE $rel_table set $rel_column = ? WHERE $rel_column = 0 AND $ref_column IN (" . generateQuestionMarks($related_ids) . ')';
					$adb->pquery($sql, array($record, $related_ids));
				}
			} elseif (strtoupper($action) == RB_RECORD_DELETED) {
				if ($rel_table == 'vtiger_seproductrel') {
					$sql = "INSERT INTO $rel_table($rel_column, $ref_column, 'setype') VALUES (?,?,?)";
					$adb->pquery($sql, array($record, $related_crm_ids, $module));
				} else {
					$sql = "INSERT INTO $rel_table($rel_column, $ref_column) VALUES (?,?)";
					$adb->pquery($sql, array($record, $related_crm_ids));
				}
			}
		}

		//Clean up the the backup data also after restoring
		$adb->pquery('DELETE FROM vtiger_relatedlists_rb WHERE entityid = ?', array($record));
	}

	/**
	 * Function to initialize the sortby fields array
	 */
	public function initSortByField($module) {
		global $adb, $log;
		$log->debug('> initSortByField '.$module);
		// Define the columnname's and uitype's which needs to be excluded
		$exclude_columns = array('quoteid', 'vendorid', 'access_count');
		$exclude_uitypes = array();

		$tabid = getTabId($module);
		$sql = 'SELECT columnname FROM vtiger_field WHERE tabid=? and vtiger_field.presence in (0,2)';
		$params = array($tabid);
		if (count($exclude_columns) > 0) {
			$sql .= ' AND columnname NOT IN (' . generateQuestionMarks($exclude_columns) . ')';
			$params[] = $exclude_columns;
		}
		if (count($exclude_uitypes) > 0) {
			$sql .= ' AND uitype NOT IN (' . generateQuestionMarks($exclude_uitypes) . ')';
			$params[] = $exclude_uitypes;
		}
		$result = $adb->pquery($sql, $params);
		$num_rows = $adb->num_rows($result);
		for ($i = 0; $i < $num_rows; $i++) {
			$columnname = $adb->query_result($result, $i, 'columnname');
			if (!in_array($columnname, $this->sortby_fields)) {
				$this->sortby_fields[] = $columnname;
			}
		}
		if ($tabid == 21 || $tabid == 22) {
			$this->sortby_fields[] = 'crmid';
		}
		$log->debug('< initSortByField');
	}

	/* Function to set the Sequence string and sequence number starting value */
	public function setModuleSeqNumber($mode, $module, $req_str = '', $req_no = '') {
		global $adb;
		//when we configure the invoice number in Settings this will be used
		if ($mode == 'configure' && $req_no != '') {
			list($mode, $module, $req_str, $req_no, $result, $returnResult) = cbEventHandler::do_filter(
				'corebos.filter.ModuleSeqNumber.set',
				array($mode, $module, $req_str, $req_no, '', false)
			);
			if ($returnResult) {
				return $result;
			}
			$check = $adb->pquery('select cur_id from vtiger_modentity_num where semodule=? and prefix=?', array($module, $req_str));
			if ($adb->num_rows($check) == 0) {
				$numid = $adb->getUniqueId('vtiger_modentity_num');
				$active = $adb->pquery('select num_id from vtiger_modentity_num where semodule=? and active=1', array($module));
				$adb->pquery('UPDATE vtiger_modentity_num SET active=0 where num_id=?', array($adb->query_result($active, 0, 'num_id')));

				$adb->pquery('INSERT into vtiger_modentity_num values(?,?,?,?,?,?)', array($numid, $module, $req_str, $req_no, $req_no, 1));
				return true;
			} elseif ($adb->num_rows($check) != 0) {
				$num_check = $adb->query_result($check, 0, 'cur_id');
				if ($req_no < $num_check) {
					return false;
				} else {
					$adb->pquery('UPDATE vtiger_modentity_num SET active=0 where active=1 and semodule=?', array($module));
					$adb->pquery('UPDATE vtiger_modentity_num SET cur_id=?, active=1 where prefix=? and semodule=?', array($req_no, $req_str, $module));
					return true;
				}
			}
		} elseif ($mode == 'increment') {
			list($mode, $module, $req_str, $req_no, $result, $returnResult) = cbEventHandler::do_filter(
				'corebos.filter.ModuleSeqNumber.increment',
				array($mode, $module, $req_str, $req_no, '', false)
			);
			if ($returnResult) {
				return $result;
			}
			//when we save new record we will increment the autonumber field
			$check = $adb->pquery(
				"select prefix, cur_id, concat(repeat('0',greatest(length(cur_id)-length(cur_id+1),0)),cur_id+1) as req_no
					from vtiger_modentity_num where semodule=? and active = 1 FOR UPDATE",
				array($module)
			);
			$req_no .= $adb->query_result($check, 0, 'req_no');
			$curid = $adb->query_result($check, 0, 'cur_id');
			$adb->pquery('UPDATE vtiger_modentity_num SET cur_id=? where cur_id=? and active=1 AND semodule=?', array($req_no, $curid, $module));
			$prefix = $adb->query_result($check, 0, 'prefix');
			$prev_inv_no = $prefix . $curid;
			return decode_html($prev_inv_no);
		}
	}

	/* Function to check if module sequence numbering is configured for the given module or not */
	public function isModuleSequenceConfigured($module) {
		$adb = PearDatabase::getInstance();
		$result = $adb->pquery('SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1', array($module));
		return $result && $adb->num_rows($result) > 0;
	}

	/* Function to get the next module sequence number for a given module */
	public function getModuleSeqInfo($module) {
		global $adb;
		$check = $adb->pquery('select cur_id,prefix from vtiger_modentity_num where semodule=? and active = 1', array($module));
		$prefix = $adb->query_result($check, 0, 'prefix');
		$curid = $adb->query_result($check, 0, 'cur_id');
		return array($prefix, $curid);
	}

	/* Function to check if the mod number already exits */
	public function checkModuleSeqNumber($table, $column, $no) {
		global $adb;
		$result = $adb->pquery(
			'select ' . $adb->sql_escape_string($column).' from ' . $adb->sql_escape_string($table).' where ' . $adb->sql_escape_string($column) . '=?',
			array($no)
		);
		return ($adb->num_rows($result) > 0);
	}

	public function updateMissingSeqNumber($module) {
		global $log, $adb;
		$log->debug('> updateMissingSeqNumber');
		list($module, $result, $returnResult) = cbEventHandler::do_filter('corebos.filter.ModuleSeqNumber.fillempty', array($module, '', false));
		if ($returnResult) {
			return $result;
		}

		if (!$this->isModuleSequenceConfigured($module)) {
			return array();
		}

		$tabid = getTabid($module);
		$fieldinfo = $adb->pquery('SELECT * FROM vtiger_field WHERE tabid = ? AND uitype = 4', array($tabid));

		$returninfo = array();

		if ($fieldinfo && $adb->num_rows($fieldinfo)) {
			// We assume the following for module sequencing fields
			// 1. There will be only one field per module
			// 2. This field is linked to module base table
			$fld_table = $adb->query_result($fieldinfo, 0, 'tablename');
			$fld_column = $adb->query_result($fieldinfo, 0, 'columnname');

			if ($fld_table == $this->table_name) {
				$records = $adb->query("SELECT $this->table_index AS recordid FROM $this->table_name WHERE $fld_column = '' OR $fld_column is NULL");

				if ($records && $adb->num_rows($records)) {
					$returninfo['totalrecords'] = $adb->num_rows($records);
					$returninfo['updatedrecords'] = 0;

					$modseqinfo = $this->getModuleSeqInfo($module);
					$prefix = $modseqinfo[0];
					$cur_id = $modseqinfo[1];

					$old_cur_id = $cur_id;
					while ($recordinfo = $adb->fetch_array($records)) {
						$value = $prefix . $cur_id;
						$adb->pquery("UPDATE $fld_table SET $fld_column = ? WHERE $this->table_index = ?", array($value, $recordinfo['recordid']));
						$strip = strlen($cur_id) - strlen($cur_id + 1);
						if ($strip < 0) {
							$strip = 0;
						}
						$temp = str_repeat('0', $strip);
						$cur_id = $temp . ($cur_id + 1);
						$returninfo['updatedrecords'] = $returninfo['updatedrecords'] + 1;
					}
					if ($old_cur_id != $cur_id) {
						$adb->pquery('UPDATE vtiger_modentity_num set cur_id=? where semodule=? and active=1', array($cur_id, $module));
					}
				}
			} else {
				$log->fatal('Updating Missing Sequence Number FAILED! REASON: Field table and module table mismatching.');
			}
		}
		return $returninfo;
	}

	/* Generic function to get attachments in the related list of a given module */
	public function get_attachments($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $currentModule, $singlepane_view, $adb;
		$this_module = $currentModule;
		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		$button = '';
		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			$wfs = '';
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				if (!$racbr || $racbr->hasRelatedListPermissionTo('select', $related_module)) {
					$button .= "<input title='" . getTranslatedString('LBL_SELECT') . ' ' . getTranslatedString($related_module, $related_module).
						"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
						"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
						"cbPopupWindowSettings);\" value='" . getTranslatedString('LBL_SELECT') . ' ' .
						getTranslatedString($related_module, $related_module) . "'>&nbsp;";
				}
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				if ($wfs == '') {
					$wfs = new VTWorkflowManager($adb);
					$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				}
				if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
					$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
					$button .= "<input type='hidden' name='createmode' value='link' />" .
						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "'>&nbsp;";
				}
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$this_module&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$this_module&return_action=CallRelatedList&return_id=$id";
		}

		$query = "select case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name,'Documents' ActivityType,
				vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,vtiger_crmentity.modifiedtime,vtiger_seattachmentsrel.attachmentsid attachmentsid,
				vtiger_crmentity.smownerid smownerid, vtiger_notes.notesid crmid,vtiger_notes.notecontent description,vtiger_notes.*
			from vtiger_notes
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid
			left join vtiger_notescf ON vtiger_notescf.notesid=vtiger_notes.notesid
			inner join ".$other->crmentityTableAlias.' on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
			inner join vtiger_crmobject crm2 on crm2.crmid=vtiger_senotesrel.crmid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
			left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
			where crm2.crmid=' . $id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;
		return $return_value;
	}

	/** Returns a list of the associated emails
	 * @param integer entity ID
	 * @return array related emails record
	 */
	public function get_emails($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_emails '.$id);
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

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
					"cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT'). ' ' .
					getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW').' '. $singular_modname."' accessyKey='F' class='crmbutton small create' ".
					"onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW').
					' '. $singular_modname."'></td>";
			}
		}

		$query ="select case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name, vtiger_activity.activityid,
				vtiger_activity.subject, vtiger_activity.semodule, vtiger_activity.activitytype, vtiger_email_track.access_count, vtiger_activity.date_start,
				vtiger_activity.time_start, vtiger_activity.status, vtiger_activity.priority, ".$other->crmentityTable.'.crmid,'.$other->crmentityTable.'.smownerid,'
				.$other->crmentityTable.'.modifiedtime, vtiger_users.user_name, vtiger_seactivityrel.crmid as parent_id, vtiger_emaildetails.*
			from vtiger_activity
			inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid'
			.' inner join '.$other->crmentityTable.' on '.$other->crmentityTable.'.crmid=vtiger_activity.activityid'
			.' inner join vtiger_emaildetails on vtiger_emaildetails.emailid = vtiger_activity.activityid
			left join vtiger_email_track on (vtiger_email_track.crmid=vtiger_seactivityrel.crmid AND vtiger_email_track.mailid=vtiger_activity.activityid)
			left join vtiger_groups on vtiger_groups.groupid='.$other->crmentityTable.'.smownerid
			left join vtiger_users on vtiger_users.id='.$other->crmentityTable.".smownerid
			where vtiger_activity.activitytype='Emails' and ".$other->crmentityTable.'.deleted=0 and vtiger_seactivityrel.crmid='.$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_emails');
		return $return_value;
	}

	/**
	 * For Record View Notification
	 */
	public function isViewed($crmid = false) {
		if (!$crmid) {
			$crmid = $this->id;
		}
		if ($crmid) {
			global $adb;
			$result = $adb->pquery('SELECT viewedtime,modifiedtime,smcreatorid,smownerid,modifiedby FROM '.$this->crmentityTable.' WHERE crmid=?', array($crmid));
			$resinfo = $adb->fetch_array($result);

			$lastviewed = $resinfo['viewedtime'];
			$modifiedon = $resinfo['modifiedtime'];
			$smownerid = $resinfo['smownerid'];
			$smcreatorid = $resinfo['smcreatorid'];
			$modifiedby = $resinfo['modifiedby'];

			if ($modifiedby == '0' && ($smownerid == $smcreatorid)) {
				/** When module record is created * */
				return true;
			} elseif ($smownerid == $modifiedby) {
				/** Owner and Modifier as same. * */
				return true;
			} elseif ($lastviewed && $modifiedon) {
				/** Lastviewed and Modified time is available. */
				if ($this->__timediff($modifiedon, $lastviewed) > 0) {
					return true;
				}
			}
		}
		return false;
	}

	public function __timediff($d1, $d2) {
		list($t1_1, $t1_2) = explode(' ', $d1);
		list($t1_y, $t1_m, $t1_d) = explode('-', $t1_1);
		list($t1_h, $t1_i, $t1_s) = explode(':', $t1_2);

		$t1 = mktime($t1_h, $t1_i, $t1_s, $t1_m, $t1_d, $t1_y);

		list($t2_1, $t2_2) = explode(' ', $d2);
		list($t2_y, $t2_m, $t2_d) = explode('-', $t2_1);
		list($t2_h, $t2_i, $t2_s) = explode(':', $t2_2);

		$t2 = mktime($t2_h, $t2_i, $t2_s, $t2_m, $t2_d, $t2_y);

		if ($t1 == $t2) {
			return 0;
		}
		return $t2 - $t1;
	}

	public function markAsViewed($userid) {
		global $adb;
		$adb->pquery('UPDATE '.$this->crmentityTable.' set viewedtime=? WHERE crmid=? AND smownerid=?', array(date('Y-m-d H:i:s', time()), $this->id, $userid));
	}

	/**
	 * Save the related module record information. Triggered from CRMEntity->saveentity method or updateRelations.php
	 * @param string This module name
	 * @param integer This module record number
	 * @param string Related module name
	 * @param mixed Integer or Array of related module record number
	 */
	public function save_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		$with_crmid = (array)$with_crmid;
		foreach ($with_crmid as $relcrmid) {
			if ($with_module == 'Documents' && $module!='DocumentFolders') {
				$checkpresence = $adb->pquery('SELECT 1 FROM vtiger_senotesrel WHERE crmid=? AND notesid=?', array($crmid, $relcrmid));
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$adb->pquery('INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)', array($crmid, $relcrmid));
			} elseif ($with_module == 'Emails') {
				$checkpresence = $adb->pquery('SELECT 1 FROM vtiger_seactivityrel WHERE crmid=? AND activityid=?', array($crmid, $relcrmid));
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$adb->pquery('INSERT INTO vtiger_seactivityrel(crmid, activityid) VALUES(?,?)', array($crmid, $relcrmid));
			} else {
				$checkpresence = $adb->pquery(
					'SELECT 1 FROM vtiger_crmentityrel WHERE crmid=? AND module=? AND relcrmid=? AND relmodule=?',
					array($crmid, $module, $relcrmid, $with_module)
				);
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$adb->pquery('INSERT INTO vtiger_crmentityrel(crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)', array($crmid, $module, $relcrmid, $with_module));
			}
		}
	}

	/**
	 * Delete the related module record information. Triggered from updateRelations.php
	 * @param string This module name
	 * @param integer This module record number
	 * @param string Related module name
	 * @param mixed Integer or Array of related module record number
	 */
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
			} else {
				$adb->pquery(
					'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND module=? AND relcrmid=? AND relmodule=?) OR (relcrmid=? AND relmodule=? AND crmid=? AND module=?)',
					array($crmid, $module, $relcrmid, $with_module,$crmid, $module, $relcrmid, $with_module)
				);
			}
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		}
	}

	/**
	 * Generic function to handle the workflow related list for a module.
	 */
	public function getWorkflowRelatedList($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		require_once 'modules/com_vtiger_workflow/VTWorkflow.php';
		global $currentModule, $singlepane_view;

		$related_module = 'com_vtiger_workflow';
		$other = new Workflow();
		unset($other->list_fields['Tools'], $other->list_fields_name['Tools']);
		$button = '';
		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . ' ' . getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
					"cbPopupWindowSettings);\" value='" . getTranslatedString('LBL_SELECT') . ' '.
					getTranslatedString($related_module, $related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input type='hidden' name='createmode' value='link' />" .
					"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"workflowlist\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		$query = 'SELECT *,workflow_id as crmid ';
		$query .= ' FROM com_vtiger_workflows';
		$query .= ' INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = workflow_id OR vtiger_crmentityrel.crmid = workflow_id)';
		$query .= " WHERE (vtiger_crmentityrel.crmid = $id OR vtiger_crmentityrel.relcrmid = $id)";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array('header'=>array(),'entries'=>array(),'navigation'=>array('',''));
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**
	 * Default (generic) function to handle the related list for the module.
	 * NOTE: Vtiger_Module::setRelatedList sets reference to this function in vtiger_relatedlists table
	 * if function name is not explicitly specified.
	 */
	public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $currentModule, $singlepane_view, $adb;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		$button = '';
		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			$wfs = '';
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				if (!$racbr || $racbr->hasRelatedListPermissionTo('select', $related_module)) {
					$button .= "<input title='" . getTranslatedString('LBL_SELECT') . ' ' . getTranslatedString($related_module, $related_module).
						"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
						"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
						"cbPopupWindowSettings);\" value='" . getTranslatedString('LBL_SELECT') . ' '.
						getTranslatedString($related_module, $related_module) . "'>&nbsp;";
				}
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				if ($wfs == '') {
					$wfs = new VTWorkflowManager($adb);
					$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				}
				if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
					$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
					$button .= "<input type='hidden' name='createmode' value='link' />" .
						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "'>&nbsp;";
				}
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}
		if ($related_module == 'Users') {
			$query = 'SELECT vtiger_users.* ';
			$maintableid = 'vtiger_users.id';
			$q_elsegroupname = '';
		} else {
			$query = 'SELECT vtiger_crmentity.* ';
			$maintableid = 'vtiger_crmentity.crmid';
			$q_elsegroupname = 'ELSE vtiger_groups.groupname ';
		}
		$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN vtiger_users.ename {$q_elsegroupname}END AS user_name";

		$more_relation = '';
		// Select Custom Field Table Columns if present
		if (isset($other->customFieldTable) && empty($other->related_tables[$other->customFieldTable[0]])) {
			$query .= ', '.$other->customFieldTable[0].'.*';
			$more_relation .= ' INNER JOIN '.$other->customFieldTable[0].' ON '.$other->customFieldTable[0].'.'.$other->customFieldTable[1] .
				" = $other->table_name.$other->table_index";
		}
		if (!empty($other->related_tables)) {
			foreach ($other->related_tables as $tname => $relmap) {
				$query .= ", $tname.*";

				// Setup the default JOIN conditions if not specified
				if (empty($relmap[1])) {
					$relmap[1] = $other->table_name;
				}
				if (empty($relmap[2])) {
					$relmap[2] = $relmap[0];
				}
				$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}
		$query .= ', '.$other->table_name.'.*';
		$query .= " FROM $other->table_name";
		if ($related_module != 'Users') {
			$query .= ' INNER JOIN '.$other->crmentityTableAlias." ON vtiger_crmentity.crmid=$other->table_name.$other->table_index";
		}
		$query .= ' INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid='.$maintableid.' OR vtiger_crmentityrel.crmid='.$maintableid.')';
		$query .= $more_relation;
		if ($related_module != 'Users') {
			$query .= ' LEFT JOIN vtiger_users ON vtiger_users.id = '.$other->crmentityTable.'.smownerid';
			$query .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = '.$other->crmentityTable.'.smownerid';
			$del_table = $other->crmentityTable;
		} else {
			$del_table = 'vtiger_users';
		}
		$query .= " WHERE {$del_table}.deleted = 0 AND (vtiger_crmentityrel.crmid = $id OR vtiger_crmentityrel.relcrmid = $id)";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array('header'=>array(),'entries'=>array(),'navigation'=>array('',''));
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**
	 * Default (generic) function to handle the dependents list for the module.
	 * NOTE: UI type '10' is used to stored the references to other modules for a given record.
	 * These dependent records can be retrieved through this function.
	 * For eg: A trouble ticket can be related to an Account or a Contact.
	 * From a given Contact/Account if we need to fetch all such dependent trouble tickets, get_dependents_list function can be used.
	 */
	public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $currentModule, $singlepane_view, $current_user, $adb;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		$button = '';

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		$return_value = null;
		$dependentFieldSql = $adb->pquery("SELECT tabid, tablename, fieldname, columnname FROM vtiger_field WHERE uitype='10' AND"
			.' fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE relmodule=? AND module=?)', array($currentModule, $related_module));
		$numOfFields = $adb->num_rows($dependentFieldSql);

		$relWithSelf = false;
		if ($numOfFields > 0) {
			$relconds = array();
			while ($depflds = $adb->fetch_array($dependentFieldSql)) {
				$dependentTable = $depflds['tablename'];
				if (isset($other->related_tables)) {
					$otherRelatedTable = (array)$other->related_tables;
				} else {
					$otherRelatedTable = array();
				}
				if ($dependentTable!=$other->table_name && !in_array($dependentTable, $otherRelatedTable)) {
					$relidx = isset($other->tab_name_index[$dependentTable]) ? $other->tab_name_index[$dependentTable] : $other->table_index;
					$other->related_tables[$dependentTable] = array($relidx,$other->table_name,$other->table_index);
				}
				$dependentColumn = $depflds['columnname'];
				$dependentField = $depflds['fieldname'];
				if ($this->table_name==$other->table_name) {
					$thistablename = $this->table_name.'RelSelf';
					$relWithSelf = true;
				} else {
					$thistablename = $this->table_name;
				}
				$relconds[] = "$thistablename.$this->table_index = $dependentTable.$dependentColumn";
				$button .= '<input type="hidden" name="' . $dependentField . '" id="' . $dependentColumn . '" value="' . $id . '">';
				$button .= '<input type="hidden" name="' . $dependentField . '_type" id="' . $dependentColumn . '_type" value="' . $currentModule . '">';
			}
			$relationconditions = '('.implode(' or ', $relconds).')';
			if ($actions) {
				if (is_string($actions)) {
					$actions = explode(',', strtoupper($actions));
				}
				$wfs = '';
				if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes'
						&& getFieldVisibilityPermission($related_module, $current_user->id, $dependentField, 'readwrite') == '0') {
					$wfs = new VTWorkflowManager($adb);
					$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
					if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
						$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
						$button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "' class='crmbutton small create'" .
							" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
							" value='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "'>&nbsp;";
					}
				}
			}

			$query = "SELECT vtiger_crmentity.*, $other->table_name.*";

			$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN vtiger_users.ename ELSE vtiger_groups.groupname END AS user_name";

			$more_relation = '';
			if (isset($other->customFieldTable) && empty($other->related_tables[$other->customFieldTable[0]])) {
				$query .= ', '.$other->customFieldTable[0].'.*';
				$more_relation .= ' INNER JOIN '.$other->customFieldTable[0].' ON '.$other->customFieldTable[0].'.'.$other->customFieldTable[1] .
					" = $other->table_name.$other->table_index";
			}
			if (!empty($other->related_tables)) {
				foreach ($other->related_tables as $tname => $relmap) {
					$query .= ", $tname.*";

					// Setup the default JOIN conditions if not specified
					if (empty($relmap[1])) {
						$relmap[1] = $other->table_name;
					}
					if (empty($relmap[2])) {
						$relmap[2] = $relmap[0];
					}
					$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
				}
			}

			$query .= " FROM $other->table_name";
			$query .= ' INNER JOIN '.$other->crmentityTableAlias." ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
			$query .= $more_relation;
			if ($relWithSelf) {
				$query .= " INNER JOIN $this->table_name as ".$this->table_name."RelSelf ON $relationconditions";
			} else {
				$query .= " INNER JOIN $this->table_name ON $relationconditions";
			}
			$query .= ' LEFT JOIN vtiger_users ON vtiger_users.id = '.$other->crmentityTable.'.smownerid';
			$query .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = '.$other->crmentityTable.'.smownerid';

			if ($relWithSelf) {
				$query .= ' WHERE '.$other->crmentityTable.'.deleted=0 AND '.$this->table_name."RelSelf.$this->table_index = $id";
			} else {
				$query .= " WHERE ".$other->crmentityTable.".deleted=0 AND $this->table_name.$this->table_index = $id";
			}

			$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
		}
		if ($return_value == null) {
			$return_value = array('header'=>array(),'entries'=>array(),'navigation'=>array('',''));
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/** Returns a list of the associated cbCalendar events
	 * Defined here for backward compatibility with previous calendar system
	*/
	public function get_activities($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $currentModule, $app_strings, $singlepane_view, $current_user, $adb;
		$rel_tab_id = getTabid('cbCalendar');

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		$button = '';

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		$return_value = null;
		$dependentFieldSql = $adb->pquery(
			"SELECT tabid, tablename, fieldname, columnname FROM vtiger_field WHERE uitype='10' AND fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE relmodule=? AND module=?)",
			array($currentModule, $related_module)
		);
		$numOfFields = $adb->num_rows($dependentFieldSql);

		$relWithSelf = false;
		if ($numOfFields > 0) {
			$relconds = array();
			while ($depflds = $adb->fetch_array($dependentFieldSql)) {
				$dependentTable = $depflds['tablename'];
				if (isset($other->related_tables)) {
					$otherRelatedTable = (array)$other->related_tables;
				} else {
					$otherRelatedTable = '';
				}
				if ($dependentTable!=$other->table_name && !in_array($dependentTable, $otherRelatedTable)) {
					$relidx = isset($other->tab_name_index[$dependentTable]) ? $other->tab_name_index[$dependentTable] : $other->table_index;
					$other->related_tables[$dependentTable] = array($relidx,$other->table_name,$other->table_index);
				}
				$dependentColumn = $depflds['columnname'];
				$dependentField = $depflds['fieldname'];
				if ($this->table_name==$other->table_name) {
					$thistablename = $this->table_name.'RelSelf';
					$relWithSelf = true;
				} else {
					$thistablename = $this->table_name;
				}
				$relconds[] = "$thistablename.$this->table_index = $dependentTable.$dependentColumn";
				$button .= '<input type="hidden" name="' . $dependentColumn . '" id="' . $dependentColumn . '" value="' . $id . '">';
				$button .= '<input type="hidden" name="' . $dependentColumn . '_type" id="' . $dependentColumn . '_type" value="' . $currentModule . '">';
			}
			$relationconditions = '('.implode(' or ', $relconds).')';
			$calStatus = getAssignedPicklistValues('eventstatus', $current_user->roleid, $adb, $app_strings);
			$relid = $adb->run_query_field('select relation_id from vtiger_relatedlists where tabid='.$cur_tab_id.' and related_tabid='.$rel_tab_id, 'relation_id');
			$button .= '<select name="cbcalendar_filter" class="small" onchange="loadRelatedListBlock(\'module='.$currentModule.'&action='.$currentModule.
				'Ajax&file=DetailViewAjax&record='.$id.'&ajxaction=LOADRELATEDLIST&header=Activities&relation_id='.$relid.
				'&cbcalendar_filter=\'+this.options[this.options.selectedIndex].value+\'&actions=add\',\'tbl_'.$currentModule.'_Activities\',\''.
				$currentModule.'_Activities\');"><option value="all">'.getTranslatedString('LBL_ALL').'</option>';
			if (!isset($_REQUEST['cbcalendar_filter'])) {
				$_REQUEST['cbcalendar_filter'] = GlobalVariable::getVariable('RelatedList_Activity_DefaultStatusFilter', 'all', $currentModule);
			}
			foreach ($calStatus as $cstatkey => $cstatvalue) {
				$button .= '<option value="'.$cstatkey.'" '.
					($_REQUEST['cbcalendar_filter']==$cstatkey ? 'selected' : '').'>'.$cstatvalue.'</option>';
			}
			$button .= '</select>&nbsp;';
			if ($actions) {
				if (is_string($actions)) {
					$actions = explode(',', strtoupper($actions));
				}
				$wfs = '';
				if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes'
						&& getFieldVisibilityPermission($related_module, $current_user->id, $dependentField, 'readwrite') == '0') {
					$wfs = new VTWorkflowManager($adb);
					$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
					if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
						$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
						$button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . ' ' . $singular_modname . "' class='crmbutton small create'" .
							" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
							" value='" . getTranslatedString('LBL_ADD_NEW') . ' ' . $singular_modname . "'>&nbsp;";
					}
				}
			}

			$query = "SELECT vtiger_crmentity.*, $other->table_name.*";

			$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN vtiger_users.ename ELSE vtiger_groups.groupname END AS user_name";

			$more_relation = '';
			if (!empty($other->related_tables)) {
				foreach ($other->related_tables as $tname => $relmap) {
					$query .= ", $tname.*";

					// Setup the default JOIN conditions if not specified
					if (empty($relmap[1])) {
						$relmap[1] = $other->table_name;
					}
					if (empty($relmap[2])) {
						$relmap[2] = $relmap[0];
					}
					$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
				}
			}

			$query .= " FROM $other->table_name";
			$query .= ' INNER JOIN '.$other->crmentityTableAlias." ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
			$query .= $more_relation;
			if ($relWithSelf) {
				$query .= " INNER JOIN $this->table_name as ".$this->table_name."RelSelf ON $relationconditions";
			} else {
				$query .= " INNER JOIN $this->table_name ON $relationconditions";
			}
			$query .= ' LEFT JOIN vtiger_users ON vtiger_users.id = '.$other->crmentityTable.'.smownerid';
			$query .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = '.$other->crmentityTable.'.smownerid';

			if ($relWithSelf) {
				$query .= ' WHERE '.$other->crmentityTable.'.deleted=0 AND '.$this->table_name."RelSelf.$this->table_index = $id";
			} else {
				$query .= " WHERE ".$other->crmentityTable.".deleted=0 AND $this->table_name.$this->table_index = $id";
			}
			$query .= " AND vtiger_activity.activitytype != 'Emails'";
			if ($_REQUEST['cbcalendar_filter'] != 'all') {
				$query .= $adb->convert2Sql(' and vtiger_activity.eventstatus=? ', array(vtlib_purify($_REQUEST['cbcalendar_filter'])));
			}
			$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
		}
		if ($return_value == null) {
			$return_value = array('header'=>array(),'entries'=>array(),'navigation'=>array('',''));
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param string This module name
	 * @param array List of Entity Id's from which related records need to be transfered
	 * @param integer Id of the the Record to which the related records are to be moved
	 */
	public function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb, $log;
		$log->debug('> transferRelatedRecords', [$module, $transferEntityIds, $entityId]);
		include_once 'include/utils/duplicate.php';
		$rel_table_arr = array('Activities'=>'vtiger_seactivityrel');
		$tbl_field_arr = array('vtiger_seactivityrel'=>'activityid');
		$entity_tbl_field_arr = array('vtiger_seactivityrel'=>'crmid');
		$depmods = getUIType10DependentModules($module);
		unset($depmods['ModComments']);
		foreach ($depmods as $mod => $details) {
			$rel_table_arr[$mod] = $details['tablename'];
			$modobj = CRMEntity::getInstance($mod);
			$tbl_field_arr[$details['tablename']] = $modobj->tab_name_index[$details['tablename']];
			$entity_tbl_field_arr[$details['tablename']] = $details['columname'];
		}

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
						$adb->pquery(
							"update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value)
						);
					}
				}
			}

			// Pick the records related to the entity to be transfered, but do not pick the ones which are already related to the current entity.
			$relatedRecords = $adb->pquery(
				'SELECT relcrmid, relmodule FROM vtiger_crmentityrel WHERE crmid=? AND module=?'
					.' AND relcrmid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid=? AND module=?)',
				array($transferId, $module, $entityId, $module)
			);
			$numOfRecords = $adb->num_rows($relatedRecords);
			for ($i = 0; $i < $numOfRecords; $i++) {
				$relcrmid = $adb->query_result($relatedRecords, $i, 'relcrmid');
				$relmodule = $adb->query_result($relatedRecords, $i, 'relmodule');
				$adb->pquery(
					'UPDATE vtiger_crmentityrel SET crmid=? WHERE relcrmid=? AND relmodule=? AND crmid=? AND module=?',
					array($entityId, $relcrmid, $relmodule, $transferId, $module)
				);
			}

			// Pick the records to which the entity to be transfered is related, but do not pick the ones to which current entity is already related.
			$parentRecords = $adb->pquery(
				'SELECT crmid, module FROM vtiger_crmentityrel WHERE relcrmid=? AND relmodule=? AND crmid NOT IN
					(SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid=? AND relmodule=?)',
				array($transferId, $module, $entityId, $module)
			);
			$numOfRecords = $adb->num_rows($parentRecords);
			for ($i = 0; $i < $numOfRecords; $i++) {
				$parcrmid = $adb->query_result($parentRecords, $i, 'crmid');
				$parmodule = $adb->query_result($parentRecords, $i, 'module');
				$adb->pquery(
					'UPDATE vtiger_crmentityrel SET relcrmid=? WHERE crmid=? AND module=? AND relcrmid=? AND relmodule=?',
					array($entityId, $parcrmid, $parmodule, $transferId, $module)
				);
			}
			$adb->pquery('UPDATE vtiger_modcomments SET related_to = ? WHERE related_to = ?', array($entityId, $transferId));
			$adb->pquery('UPDATE vtiger_senotesrel SET crmid = ? WHERE crmid = ?', array($entityId, $transferId));
		}
		$log->debug('< transferRelatedRecords');
	}

	/**
	 * Function to get the primary query part of a report
	 * @param string primary module name
	 * @return string query string formed on fetching the related data for report for primary module
	 */
	public function generateReportsQuery($module, $queryPlanner) {
		global $adb;
		$primary = CRMEntity::getInstance($module);

		$moduletable = $primary->table_name;
		$moduleindex = $primary->table_index;
		if (count($primary->customFieldTable)>0) {
			$modulecftable = $primary->customFieldTable[0];
			$modulecfindex = $primary->customFieldTable[1];
		}
		if (isset($modulecftable) && $queryPlanner->requireTable($modulecftable)) {
			$cfquery = "inner join $modulecftable as $modulecftable on $modulecftable.$modulecfindex=$moduletable.$moduleindex";
		} else {
			$cfquery = '';
		}
		$relquery = '';
		$matrix = $queryPlanner->newDependencyMatrix();

		$fields_query = $adb->pquery(
			'SELECT vtiger_field.columnname,vtiger_field.tablename,vtiger_field.fieldid
				FROM vtiger_field
				INNER JOIN vtiger_tab on vtiger_tab.name=?
				WHERE vtiger_tab.tabid=vtiger_field.tabid AND vtiger_field.uitype=10 and vtiger_field.presence in (0,2)',
			array($module)
		);
		if ($adb->num_rows($fields_query) > 0) {
			for ($i = 0; $i < $adb->num_rows($fields_query); $i++) {
				$col_name = $adb->query_result($fields_query, $i, 'columnname');
				$field_id = $adb->query_result($fields_query, $i, 'fieldid');
				$tab_name = $adb->query_result($fields_query, $i, 'tablename');
				$ui10_modules_query = $adb->pquery('SELECT relmodule FROM vtiger_fieldmodulerel WHERE fieldid=?', array($field_id));

				if ($adb->num_rows($ui10_modules_query) > 0) {
					// Capture the forward table dependencies due to dynamic related-field
					$crmentityRelModuleFieldTable = "vtiger_crmentityRel$module$field_id";

					$crmentityRelModuleFieldTableDeps = array();
					for ($j = 0; $j < $adb->num_rows($ui10_modules_query); $j++) {
						$rel_mod = $adb->query_result($ui10_modules_query, $j, 'relmodule');
						$rel_obj = CRMEntity::getInstance($rel_mod);
						$rel_tab_name = $rel_obj->table_name;
						$crmentityRelModuleFieldTableDeps[] = $rel_tab_name . "Rel$module$field_id";
					}
					$matrix->setDependency($crmentityRelModuleFieldTable, $crmentityRelModuleFieldTableDeps);
					$matrix->addDependency($tab_name, $crmentityRelModuleFieldTable);

					if ($queryPlanner->requireTable($crmentityRelModuleFieldTable, $matrix)) {
						$relquery.= ' left join '.$this->crmentityTable." as $crmentityRelModuleFieldTable on ".
							"$crmentityRelModuleFieldTable.crmid = $tab_name.$col_name and $crmentityRelModuleFieldTable.deleted=0";
					}

					for ($j = 0; $j < $adb->num_rows($ui10_modules_query); $j++) {
						$rel_mod = $adb->query_result($ui10_modules_query, $j, 'relmodule');
						$rel_obj = CRMEntity::getInstance($rel_mod);

						$rel_tab_name = $rel_obj->table_name;
						$rel_tab_index = $rel_obj->table_index;

						$rel_tab_name_rel_module_table_alias = $rel_tab_name . "Rel$module$field_id";

						if ($queryPlanner->requireTable($rel_tab_name_rel_module_table_alias)) {
							$relquery.= " left join $rel_tab_name as $rel_tab_name_rel_module_table_alias on ".
								"$rel_tab_name_rel_module_table_alias.$rel_tab_index = $crmentityRelModuleFieldTable.crmid";
						}
					}
				}
			}
		}

		$query = "from $moduletable ";
		$query .= 'inner join '.$this->crmentityTableAlias." on vtiger_crmentity.crmid=$moduletable.$moduleindex";

		// Add the pre-joined custom table query
		$query .= ' ' . $cfquery;

		if ($queryPlanner->requireTable('vtiger_users'.$module) || $queryPlanner->requireTable('vtiger_groups'.$module)) {
			$query .= " left join vtiger_users as vtiger_users" . $module . " on vtiger_users" . $module . ".id = ".$this->crmentityTable.".smownerid";
			$query .= " left join vtiger_groups as vtiger_groups" . $module . " on vtiger_groups" . $module . ".groupid = ".$this->crmentityTable.".smownerid";
		}
		if ($queryPlanner->requireTable('vtiger_lastModifiedBy'.$module)) {
			$query .= " left join vtiger_users as vtiger_lastModifiedBy" . $module . " on vtiger_lastModifiedBy" . $module . ".id = ".$this->crmentityTable.".modifiedby";
		}
		if ($queryPlanner->requireTable('vtiger_CreatedBy'.$module)) {
			$query .= " LEFT JOIN vtiger_users AS vtiger_CreatedBy$module ON vtiger_CreatedBy$module.id=".$this->crmentityTable.".smcreatorid";
		}
		$query .= ' left join vtiger_groups on vtiger_groups.groupid = '.$this->crmentityTable.'.smownerid';
		$query .= ' left join vtiger_users on vtiger_users.id = '.$this->crmentityTable.'.smownerid';

		// Add the pre-joined relation table query
		$query .= ' ' . $relquery;

		$fields_query = $adb->pquery(
			'SELECT vtiger_field.columnname,vtiger_field.tablename,vtiger_field.fieldid
				FROM vtiger_field
				INNER JOIN vtiger_tab on vtiger_tab.name = ?
				WHERE vtiger_tab.tabid=vtiger_field.tabid AND vtiger_field.uitype = 101 and vtiger_field.presence in (0,2)',
			array($module)
		);
		if ($adb->num_rows($fields_query) > 0) {
			for ($i = 0; $i < $adb->num_rows($fields_query); $i++) {
				$field_id = $adb->query_result($fields_query, $i, 'fieldid');
				$usrTable = "vtiger_usersRel$module$field_id";
				if ($queryPlanner->requireTable($usrTable)) {
					$col_name = $adb->query_result($fields_query, $i, 'columnname');
					$tab_name = $adb->query_result($fields_query, $i, 'tablename');
					$query.= " left join vtiger_users as $usrTable on $usrTable.id = $tab_name.$col_name";
				}
			}
		}
		return $query;
	}

	/**
	 * Function to get the secondary query part of a report
	 * @param string primary module name
	 * @param string secondary module name
	 * @return string query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		global $adb;
		$secondary = CRMEntity::getInstance($secmodule);

		$tablename = $secondary->table_name;
		$tableindex = $secondary->table_index;
		$modulecftable = $secondary->customFieldTable[0];
		$modulecfindex = $secondary->customFieldTable[1];

		if (isset($modulecftable) && $queryPlanner->requireTable($modulecftable)) {
			$cfquery = "left join $modulecftable as $modulecftable on $modulecftable.$modulecfindex=$tablename.$tableindex";
		} else {
			$cfquery = '';
		}

		$relquery = '';
		$matrix = $queryPlanner->newDependencyMatrix();

		$fields_query = $adb->pquery(
			'SELECT vtiger_field.columnname,vtiger_field.tablename,vtiger_field.fieldid
				FROM vtiger_field
				INNER JOIN vtiger_tab on vtiger_tab.name=?
				WHERE vtiger_tab.tabid=vtiger_field.tabid AND vtiger_field.uitype=10 and vtiger_field.presence in (0,2)',
			array($secmodule)
		);

		if ($adb->num_rows($fields_query) > 0) {
			for ($i = 0; $i < $adb->num_rows($fields_query); $i++) {
				$col_name = $adb->query_result($fields_query, $i, 'columnname');
				$field_id = $adb->query_result($fields_query, $i, 'fieldid');
				$tab_name = $adb->query_result($fields_query, $i, 'tablename');
				$ui10_modules_query = $adb->pquery('SELECT relmodule FROM vtiger_fieldmodulerel WHERE fieldid=?', array($field_id));

				if ($adb->num_rows($ui10_modules_query) > 0) {
					// Capture the forward table dependencies due to dynamic related-field
					$crmentityRelSecModuleTable = "vtiger_crmentityRel$secmodule$i";

					$crmentityRelSecModuleTableDeps = array();
					for ($j = 0; $j < $adb->num_rows($ui10_modules_query); $j++) {
						$rel_mod = $adb->query_result($ui10_modules_query, $j, 'relmodule');
						$rel_obj = CRMEntity::getInstance($rel_mod);
						$rel_tab_name = $rel_obj->table_name;
						$crmentityRelSecModuleTableDeps[] = $rel_tab_name . "Rel$secmodule" . $field_id;
					}
					$matrix->setDependency($crmentityRelSecModuleTable, $crmentityRelSecModuleTableDeps);
					$matrix->addDependency($tab_name, $crmentityRelSecModuleTable);

					if ($queryPlanner->requireTable($crmentityRelSecModuleTable, $matrix)) {
						$relquery .= ' left join '.$this->crmentityTable." as $crmentityRelSecModuleTable on ".
							"$crmentityRelSecModuleTable.crmid = $tab_name.$col_name and $crmentityRelSecModuleTable.deleted=0";
					}
					for ($j = 0; $j < $adb->num_rows($ui10_modules_query); $j++) {
						$rel_mod = $adb->query_result($ui10_modules_query, $j, 'relmodule');
						$rel_obj = CRMEntity::getInstance($rel_mod);
						$rel_tab_name = $rel_obj->table_name;
						$rel_tab_index = $rel_obj->table_index;
						$rel_tab_name_rel_secmodule_table_alias = $rel_tab_name . "Rel$secmodule" . $field_id;
						if ($queryPlanner->requireTable($rel_tab_name_rel_secmodule_table_alias)) {
							$relquery .= " left join $rel_tab_name as $rel_tab_name_rel_secmodule_table_alias on ".
								"$rel_tab_name_rel_secmodule_table_alias.$rel_tab_index = $crmentityRelSecModuleTable.crmid";
						}
					}
				}
			}
		}

		// Update forward table dependencies
		$matrix->setDependency("vtiger_crmentity$secmodule", array("vtiger_groups$secmodule", "vtiger_users$secmodule", "vtiger_lastModifiedBy$secmodule"));
		$matrix->addDependency($tablename, "vtiger_crmentity$secmodule");

		if (!$queryPlanner->requireTable($tablename, $matrix) && !$queryPlanner->requireTable($modulecftable)) {
			return '';
		}

		$query = $this->getRelationQuery($module, $secmodule, "$tablename", "$tableindex", $queryPlanner);

		if ($queryPlanner->requireTable("vtiger_crmentity$secmodule", $matrix)) {
			$query .= ' left join '.$this->crmentityTable." as vtiger_crmentity$secmodule on ".
				"vtiger_crmentity$secmodule.crmid = $tablename.$tableindex AND vtiger_crmentity$secmodule.deleted=0";
		}

		// Add the pre-joined custom table query
		$query .= ' '.$cfquery;

		if ($queryPlanner->requireTable("vtiger_groups$secmodule")) {
			$query .= ' left join vtiger_groups as vtiger_groups' . $secmodule . ' on vtiger_groups' . $secmodule . ".groupid = vtiger_crmentity$secmodule.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_users$secmodule")) {
			$query .= ' left join vtiger_users as vtiger_users' . $secmodule . ' on vtiger_users' . $secmodule . ".id = vtiger_crmentity$secmodule.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")) {
			$query .=' left join vtiger_currency_info as vtiger_currency_info' . $secmodule . ' on vtiger_currency_info' . $secmodule . ".id = $tablename.currency_id";
		}
		if ($queryPlanner->requireTable("vtiger_lastModifiedBy$secmodule")) {
			$query .= ' left join vtiger_users as vtiger_lastModifiedBy' . $secmodule . ' on '.
				'vtiger_lastModifiedBy' . $secmodule . '.id = vtiger_crmentity' . $secmodule . '.modifiedby';
		}
		if ($queryPlanner->requireTable('vtiger_CreatedBy'.$secmodule)) {
			$query .= " LEFT JOIN vtiger_users AS vtiger_CreatedBy$secmodule ON vtiger_CreatedBy$secmodule.id=".$this->crmentityTable.".smcreatorid";
		}
		// Add the pre-joined relation table query
		$query .= ' ' . $relquery;

		return $query;
	}

	/**
	 * Function to get the security query part of a report
	 * @param string primary module name
	 * @return string query string formed on fetching the related data for report for security of the module
	 */
	public function getListViewSecurityParameter($module) {
		global $current_user;
		if ($current_user) {
			$userprivs = $current_user->getPrivileges();
		} else {
			return '';
		}
		$sec_query = '';
		$tabid = getTabid($module);
		if (!$userprivs->hasGlobalReadPermission() && !$userprivs->hasModuleReadSharing($tabid)) {
			$sec_query .= ' and ('.$this->crmentityTable.".smownerid=$current_user->id or "
				.$this->crmentityTable.".smownerid in (select vtiger_user2role.userid
					from vtiger_user2role
					inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
					where vtiger_role.parentrole like '" . $userprivs->getParentRoleSequence() . "::%') or "
				.$this->crmentityTable.".smownerid in (select shareduserid
					from vtiger_tmp_read_user_sharing_per where userid=" . $current_user->id . ' and tabid=' . $tabid . ') or (';
			if ($userprivs->hasGroups()) {
				$sec_query .= ' vtiger_groups.groupid in (' . implode(',', $userprivs->getGroups()) . ') or ';
			}
			$sec_query .= ' vtiger_groups.groupid in (select vtiger_tmp_read_group_sharing_per.sharedgroupid
				from vtiger_tmp_read_group_sharing_per
				where userid=' . $current_user->id . ' and tabid=' . $tabid . ')))';
		}
		return $sec_query;
	}

	/**
	 * Function to get the security query part of a report
	 * @param string $module primary module name
	 * @return string query formed on fetching the related data for report for security of the module
	 * @deprecated
	 */
	public function getSecListViewSecurityParameter($module) {
		$tabid = getTabid($module);
		global $current_user;
		if ($current_user) {
			$userprivs = $current_user->getPrivileges();
		}
		$sec_query = " and (vtiger_crmentity$module.smownerid=$current_user->id or vtiger_crmentity$module.smownerid in ".
			"(select vtiger_user2role.userid
				from vtiger_user2role
				inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
				where vtiger_role.parentrole like '" . $userprivs->getParentRoleSequence() . "::%') or vtiger_crmentity$module.smownerid in ".
					"(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=" . $current_user->id . ' and tabid=' . $tabid . ') or (';

		if ($userprivs->hasGroups()) {
			$sec_query .= " vtiger_groups$module.groupid in (" . implode(',', $userprivs->getGroups()) . ') or ';
		}
		$sec_query .= " vtiger_groups$module.groupid in ".
			'(select vtiger_tmp_read_group_sharing_per.sharedgroupid
				from vtiger_tmp_read_group_sharing_per
				where userid=' . $current_user->id . ' and tabid=' . $tabid . '))) ';
		return $sec_query;
	}

	/**
	 * Function to get the relation query part of a report
	 * @param string primary module name
	 * @param string secondary module name
	 * @return string query string formed on relating the primary module and secondary module
	 */
	public function getRelationQuery($module, $secmodule, $table_name, $column_name, $queryPlanner) {
		global $adb;
		$tab = getRelationTables($module, $secmodule);

		foreach ($tab as $key => $value) {
			$tables[] = $key;
			$fields[] = $value;
		}
		$pritablename = $tables[0];
		$sectablename = isset($tables[1])?$tables[1]:'';
		$prifieldname = $fields[0][0];
		$secfieldname = $fields[0][1];
		$tmpname = $pritablename . 'tmp' . $secmodule;
		$condition = '';
		if (!empty($tables[1]) && !empty($fields[1])) {
			$condvalue = $tables[1] . '.' . $fields[1];
			$condtable = $table_name;
			$condition = "$condtable.$prifieldname=$condvalue";
		} else {
			$condvalue = $table_name . '.' . $column_name;
			$condtable = $pritablename;
			$cntbl = $adb->getColumnNames($condtable);
			if (!in_array($secfieldname, $cntbl)) {
				$condtable = $table_name;
			}
			$condition = "$condtable.$secfieldname=$condvalue";
		}
		$queryPlanner->addTable($condtable);
		$selectColumns = "$table_name.*";

		// Look forward for temporary table usage as defined by the QueryPlanner
		$secQueryFrom = " FROM $table_name ";
		$secQueryFrom .= 'INNER JOIN '.$this->crmentityTableAlias." ON vtiger_crmentity.crmid=$table_name.$column_name AND ".$this->crmentityTable.".deleted=0 ";

		//The relation field exists in custom field . relation field added from layout editor
		if ($pritablename != $table_name && $secmodule != 'Emails') {
			$modulecftable = $this->customFieldTable[0];
			$modulecfindex = $this->customFieldTable[1];

			if (isset($modulecftable)) {
				$columns = $adb->getColumnNames($modulecftable);
				//remove the primary key since it will conflict with base table column name or else creating temporary table will fail for duplicate columns
				//eg : vtiger_potential has potentialid and vtiger_potentialscf has same potentialid
				unset($columns[array_search($modulecfindex, $columns)]);
				if (!empty($columns)) {
					$cfSelectString = implode(',', $columns);
					$selectColumns .= ','.$cfSelectString;
				}
				$cfquery = "LEFT JOIN $modulecftable ON $modulecftable.$modulecfindex=$table_name.$column_name";
				$secQueryFrom .= $cfquery;
			}
		}

		$secQuery = 'SELECT '.$selectColumns.' '.$secQueryFrom;
		$secQueryTempTableQuery = $queryPlanner->registerTempTable($secQuery, array($column_name, $secfieldname, $prifieldname), $secmodule);

		$query = '';
		if ($pritablename == 'vtiger_crmentityrel') {
			$condition = "($table_name.$column_name={$tmpname}.{$secfieldname} OR $table_name.$column_name={$tmpname}.{$prifieldname})";
			$query = " left join vtiger_crmentityrel as $tmpname ON ($condvalue={$tmpname}.{$secfieldname} OR $condvalue={$tmpname}.{$prifieldname}) ";
		} elseif (strripos($pritablename, 'rel') === (strlen($pritablename) - 3)) {
			$instance = self::getInstance($module);
			$sectableindex = $instance->tab_name_index[$sectablename];
			$condition = "$table_name.$column_name=$tmpname.$secfieldname";
			if ($pritablename === 'vtiger_senotesrel') {
				$query = " left join $pritablename as $tmpname ON ($sectablename.$sectableindex=$tmpname.$prifieldname
					AND $tmpname.notesid IN (SELECT crmid FROM vtiger_crmobject WHERE setype='Documents' AND deleted = 0))";
			} else {
				$query = " left join $pritablename as $tmpname ON ($sectablename.$sectableindex=$tmpname.$prifieldname)";
			}
			if ($secmodule == 'Leads') {
				$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
				$condition .= " AND $table_name.converted = $val_conv";
			}
		}

		$query .= " left join $secQueryTempTableQuery as $table_name on {$condition}";
		return $query;
	}

	/**
	 * To keep track of action of field filtering and avoiding doing more than once.
	 *
	 * @var boolean
	 */
	public $__inactive_fields_filtered = false;

	/**
	 * Filter in-active fields based on type
	 *
	 * @param string $module
	 */
	public function filterInactiveFields($module) {
		if ($this->__inactive_fields_filtered) {
			return;
		}

		// Look for fields that has presence value NOT IN (0,2)
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module, array('1'));
		if ($cachedModuleFields === false) {
			// Initialize the fields calling suitable API
			getColumnFields($module);
			$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module, array('1'));
		}

		$hiddenFields = array();

		if ($cachedModuleFields) {
			foreach ($cachedModuleFields as $fieldinfo) {
				$fieldLabel = $fieldinfo['fieldlabel'];
				// NOTE: We should not translate the label to enable field diff based on it down
				$fieldName = $fieldinfo['fieldname'];
				$tableName = str_replace('vtiger_', '', $fieldinfo['tablename']);
				$hiddenFields[$fieldLabel] = array($tableName => $fieldName);
			}
		}

		if (isset($this->list_fields)) {
			$this->list_fields = array_diff_key($this->list_fields, $hiddenFields);
		}

		if (isset($this->search_fields)) {
			$this->search_fields = array_diff_key($this->search_fields, $hiddenFields);
		}

		// To avoid re-initializing everytime.
		$this->__inactive_fields_filtered = true;
	}

	public function buildSearchQueryForFieldTypes($uitypes, $value = false) {
		$uitypes = (array)$uitypes;
		$module = get_class($this);

		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
		if ($cachedModuleFields === false) {
			getColumnFields($module); // This API will initialize the cache as well
			// We will succeed now due to above function call
			$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
		}

		$lookuptables = array();
		$lookupcolumns = array();
		foreach ($cachedModuleFields as $fieldinfo) {
			if (in_array($fieldinfo['uitype'], $uitypes)) {
				$lookuptables[] = $fieldinfo['tablename'];
				$lookupcolumns[] = $fieldinfo['columnname'];
			}
		}

		$entityfields = getEntityField($module);
		$querycolumnnames = implode(',', $lookupcolumns);
		$entitycolumnnames = $entityfields['fieldname'];
		$query = "select crmid as id, $querycolumnnames, $entitycolumnnames as name ";
		$query .= " FROM $this->table_name ";
		$query .=' INNER JOIN '.$this->crmentityTableAlias." ON $this->table_name.$this->table_index = vtiger_crmentity.crmid AND vtiger_crmentity.deleted = 0 ";

		//remove the base table
		$LookupTable = array_unique($lookuptables);
		$indexes = array_keys($LookupTable, $this->table_name);
		if (!empty($indexes)) {
			foreach ($indexes as $index) {
				unset($LookupTable[$index]);
			}
		}
		foreach ($LookupTable as $tablename) {
			$query .= " INNER JOIN $tablename on $this->table_name.$this->table_index = $tablename." . $this->tab_name_index[$tablename];
		}
		if (!empty($lookupcolumns) && $value !== false) {
			$query .=' WHERE ';
			$i = 0;
			$columnCount = count($lookupcolumns);
			foreach ($lookupcolumns as $columnname) {
				if (!empty($columnname)) {
					if ($i == 0 || $i == ($columnCount)) {
						$query .= sprintf("%s = '%s'", $columnname, $value);
					} else {
						$query .= sprintf(" OR %s = '%s'", $columnname, $value);
					}
					$i++;
				}
			}
		}
		return $query;
	}

	/**
	 *
	 * @param string $tableName
	 * @return string
	 */
	public function getJoinClause($tableName) {
		if (strripos($tableName, 'rel') === (strlen($tableName) - 3)) {
			return 'LEFT JOIN';
		} else {
			return 'INNER JOIN';
		}
	}

	/**
	 *
	 * @param string module
	 * @param object Users
	 * @param string parentRole
	 * @param string userGroups comma-separated list
	 */
	public function getNonAdminAccessQuery($module, $user, $parentRole, $userGroups) {
		$query = $this->getNonAdminUserAccessQuery($user, $parentRole, $userGroups);
		if (!empty($module)) {
			$moduleAccessQuery = $this->getNonAdminModuleAccessQuery($module, $user);
			if (!empty($moduleAccessQuery)) {
				$query .= " UNION $moduleAccessQuery";
			}
		}
		return $query;
	}

	/**
	 *
	 * @param object Users
	 * @param string parentRole
	 * @param string userGroups comma-separated list
	 */
	public function getNonAdminUserAccessQuery($user, $parentRole, $userGroups) {
		$query = "(SELECT $user->id as id) UNION (SELECT vtiger_user2role.userid AS userid FROM vtiger_user2role"
			." INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid WHERE vtiger_role.parentrole like '$parentRole::%')";
		if (count($userGroups) > 0) {
			$query .= ' UNION (SELECT groupid FROM vtiger_groups where groupid in (' . implode(',', $userGroups) . '))';
		}
		return $query;
	}

	/**
	 *
	 * @param string module
	 * @param object Users
	 */
	public function getNonAdminModuleAccessQuery($module, $user) {
		$userprivs = $user->getPrivileges();
		$tabId = getTabid($module);
		$sharingRuleInfo = $userprivs->getModuleSharingRules($module, 'read');
		$query = '';
		if (!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 || count($sharingRuleInfo['GROUP']) > 0)) {
			$query = ' (SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per ' .
					"WHERE userid=$user->id AND tabid=$tabId) UNION (SELECT " .
					'vtiger_tmp_read_group_sharing_per.sharedgroupid FROM ' .
					"vtiger_tmp_read_group_sharing_per WHERE userid=$user->id AND tabid=$tabId)";
		}
		return $query;
	}

	/** Creates a temporary table with permission IDs
	 * @param string temporary table name to create
	 * @param string module name for the permissions
	 * @param object Users to calculate the permissions for
	 * @param string parent Role of the user
	 * @param string user Groups comma-separated list of groups the user belongs to
	 * @return boolean if temporary table has been created or not
	 */
	protected function setupTemporaryTable($tableName, $sharedmodule, $user, $parentRole, $userGroups) {
		$module = null;
		if (!empty($sharedmodule)) {
			$module = $sharedmodule;
		}
		$query = $this->getNonAdminAccessQuery($module, $user, $parentRole, $userGroups);
		$query = "create temporary table IF NOT EXISTS $tableName(id int(11) primary key) ignore " . $query;
		$db = PearDatabase::getInstance();
		$result = $db->pquery($query, array());
		return is_object($result);
	}

	/**
	 *
	 * @param string module name for which query needs to be generated
	 * @param Users user for which query needs to be generated
	 * @return string Access control Query for the user
	 */
	public function getNonAdminAccessControlQuery($module, $user, $scope = '') {
		global $currentModule;
		$userprivs = $user->getPrivileges();
		$query = ' ';
		$tabId = getTabid($module);
		if (!$userprivs->hasGlobalReadPermission() && !$userprivs->hasModuleReadSharing($tabId)) {
			$tableName = 'vt_tmp_u' . $user->id;
			$sharingRuleInfo = $userprivs->getModuleSharingRules($module, 'read');
			$sharedModule = null;
			if (!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 || count($sharingRuleInfo['GROUP']) > 0)) {
				$tableName = $tableName . '_t' . $tabId;
				$sharedModule = $module;
			} elseif (!empty($scope)) {
				$tableName .= '_t' . $tabId;
			}
			list($tsSpecialAccessQuery, $typeOfPermissionOverride, $unused1, $unused2, $SpecialPermissionMayHaveDuplicateRows) = cbEventHandler::do_filter(
				'corebos.permissions.accessquery',
				array(' ', 'none', $module, $user, true)
			);
			if ($typeOfPermissionOverride=='fullOverride') {
				// create the default temporary table in case it is needed
				$this->setupTemporaryTable($tableName, $sharedModule, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
				VTCacheUtils::updateCachedInformation('SpecialPermissionWithDuplicateRows', $SpecialPermissionMayHaveDuplicateRows);
				return $tsSpecialAccessQuery;
			}
			if ($typeOfPermissionOverride=='none' || trim($tsSpecialAccessQuery)=='') {
				$this->setupTemporaryTable($tableName, $sharedModule, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
				$query = " INNER JOIN $tableName $tableName$scope ON $tableName$scope.id = ".$this->crmentityTable."$scope.smownerid ";
			} else {
				global $adb;
				VTCacheUtils::updateCachedInformation('SpecialPermissionWithDuplicateRows', $SpecialPermissionMayHaveDuplicateRows);
				$tsTableName = "tsolucio_tmp_u{$user->id}";
				if ($currentModule == 'Reports') {
					$tsTableName = "tsolucio_tmp_u{$user->id}".str_replace('.', '', uniqid($user->id, true));
				}
				$adb->query("drop table if exists {$tsTableName}");
				if ($typeOfPermissionOverride=='addToUserPermission') {
					$query = $this->getNonAdminAccessQuery($module, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
					$tsSpecialAccessQuery = "$query UNION ($tsSpecialAccessQuery) ";
				}
				$adb->query("create temporary table {$tsTableName} (id int primary key) as {$tsSpecialAccessQuery}");
				if ($typeOfPermissionOverride=='addToUserPermission') {
					$query = " INNER JOIN {$tsTableName} on ({$tsTableName}.id=vtiger_crmentity$scope.crmid or {$tsTableName}.id = vtiger_crmentity$scope.smownerid) ";
				} elseif ($typeOfPermissionOverride=='showTheseRecords') {
					$query = " INNER JOIN {$tsTableName} on {$tsTableName}.id=vtiger_crmentity.crmid ";
				} elseif ($typeOfPermissionOverride=='SubstractFromUserPermission') {
					$this->setupTemporaryTable($tableName, $sharedModule, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
					$query = " INNER JOIN $tableName $tableName$scope ON $tableName$scope.id = vtiger_crmentity$scope.smownerid ";
					$query .= " INNER JOIN {$tsTableName} on {$tsTableName}.id=vtiger_crmentity.crmid ";
				}
			}
		}

		return $query;
	}

	public function listQueryNonAdminChange($query, $scope = '') {
		//make the module base table as left hand side table for the joins,
		//as mysql query optimizer puts crmentity on the left side and considerably slow down
		$query = preg_replace('/\s+/', ' ', $query);
		if (strripos($query, ' WHERE ') !== false) {
			$query = str_ireplace(' where ', " WHERE $this->table_name.$this->table_index>0 AND ", $query);
		}
		return $query;
	}

	/**
	 * Function to get the relation tables for related modules between module and this module
	 * @param string secondary module name
	 * @return array table names and fieldnames storing relations
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array(
			'Documents' => array('vtiger_senotesrel' => array('crmid', 'notesid'), $this->table_name => $this->table_index),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	/**
	 * Function to clear the fields which needs to be saved only once during the Save of the record
	 * For eg: Comments of HelpDesk should be saved only once during one save of a Trouble Ticket
	 */
	public function clearSingletonSaveFields() {
		// just return here
	}

	/**
	 * Function to track when a new record is linked to a given record
	 */
	public function trackLinkedInfo($module, $crmid, $with_module, $with_crmid) {
		global $current_user;
		$adb = PearDatabase::getInstance();
		$currentTime = date('Y-m-d H:i:s');
		$adb->pquery('UPDATE '.$this->crmentityTable.' SET modifiedtime=?, modifiedby=? WHERE crmid=?', array($currentTime, $current_user->id, $crmid));
	}

	/**
	 * Function to get sort order
	 * @return string sortorder string either 'ASC' or 'DESC'
	 */
	public function getSortOrder() {
		global $log, $adb;
		$cmodule = get_class($this);
		$log->debug('> getSortOrder');
		$sorder = strtoupper(GlobalVariable::getVariable('Application_ListView_Default_OrderDirection', $this->default_sort_order, $cmodule));
		if (isset($_REQUEST['sorder'])) {
			$sorder = $adb->sql_escape_string($_REQUEST['sorder']);
		} elseif (!empty($_SESSION[$cmodule.'_Sort_Order'])) {
			$sorder = $adb->sql_escape_string($_SESSION[$cmodule.'_Sort_Order']);
		}
		$log->debug('< getSortOrder');
		return $sorder;
	}

	/**
	 * Function to get order by
	 * @return string fieldname(eg: 'accountname')
	 */
	public function getOrderBy() {
		global $log, $adb;
		$log->debug('> getOrderBy');
		$cmodule = get_class($this);
		$order_by = '';
		if (GlobalVariable::getVariable('Application_ListView_Default_Sorting', 0, $cmodule)) {
			$order_by = GlobalVariable::getVariable('Application_ListView_Default_OrderField', $this->default_order_by, $cmodule);
		}

		if (isset($_REQUEST['order_by'])) {
			$order_by = $adb->sql_escape_string($_REQUEST['order_by']);
		} elseif (!empty($_SESSION[$cmodule.'_Order_By'])) {
			$order_by = $adb->sql_escape_string($_SESSION[$cmodule.'_Order_By']);
		}
		$log->debug('< getOrderBy');
		return $order_by;
	}

	/**
	 * Function to Listview buttons
	 * @return array $list_buttons - for module
	 */
	public function getListButtons($app_strings) {
		global $currentModule;
		$list_buttons = array();

		if (isPermitted($currentModule, 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}
		if (isPermitted($currentModule, 'EditView', '') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings['LBL_MASS_EDIT'];
		}
		return $list_buttons;
	}

	/**
	 * Function to track when a record is unlinked to a given record
	 */
	public function trackUnLinkedInfo($module, $crmid, $with_module, $with_crmid) {
		global $current_user;
		$adb = PearDatabase::getInstance();
		$currentTime = date('Y-m-d H:i:s');
		$data = array();
		$data['sourceModule'] = $module;
		$data['sourceRecordId'] = $crmid;
		$data['destinationModule'] = $with_module;
		$data['destinationRecordId'] = $with_crmid;
		cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		$adb->pquery('UPDATE '.$this->crmentityTable.' SET modifiedtime=?, modifiedby=? WHERE crmid=?', array($currentTime, $current_user->id, $crmid));
	}

	public function getParentRecords($id, &$parent_records, &$encountered_records, $refField, $currentModule, $tree = false) {
		global $log, $adb, $current_user;
		$qg = new QueryGenerator($currentModule, $current_user);
		$qg->setFields(array('*'));
		$qg->addCondition('id', $id, 'e');
		$params = array($id);
		$query = $qg->getQuery();
		$res = $adb->query($query);
		if ($adb->num_rows($res) > 0 &&
			$adb->query_result($res, 0, $refField) != '' && $adb->query_result($res, 0, $refField) != 0 &&
			!in_array($adb->query_result($res, 0, $refField), $encountered_records)) {
			$recid = $adb->query_result($res, 0, $refField);
			$encountered_records[] = $recid;
			$this->getParentRecords($recid, $parent_records, $encountered_records, $refField, $currentModule);
		}
		$depth = 0;
		$parent_record_info = array();
		$immediate_recordid = $adb->query_result($res, 0, $refField);
		if (isset($parent_records[$immediate_recordid])) {
			$depth = $parent_records[$immediate_recordid]['depth'] + 1;
		}
		if (is_array($tree)) {
			$cvtreecolumn = getEntityName($tree[0], $id);
			$parent_record_info[$tree[1]] = $cvtreecolumn[$id];
			$parent_record_info['id'] = $id;
			$parent_record_info['parent'] = $id;
			$parent_record_info['recordid'] = 'parent_'.$id;
			$parent_records[] = $parent_record_info;
		} else {
			$parent_record_info['depth'] = $depth;
			$parent_records[$id] = $parent_record_info;
		}
		return $parent_records;
	}

	public function getChildRecords($id, &$child_records, $depth, $referenceField, $currentModule, $tree = false) {
		global $log, $adb, $current_user;
		$log->debug('> getChildRecords '.$id);
		$entity = getEntityField($currentModule);
		$entityid = $entity['entityid'];
		$tablename = $entity['tablename'];
		$crmentityTable = $this->getcrmEntityTableAlias($currentModule);
		$query = $adb->convert2Sql("select {$tablename}.{$entityid} from {$tablename} inner join {$crmentityTable} on {$tablename}.{$entityid} = vtiger_crmentity.crmid where vtiger_crmentity.deleted=0 and {$tablename}.{$referenceField}=? and {$tablename}.{$entityid} > 0", array($id));
		$rs = $adb->query($query);
		$num_rows = $adb->num_rows($rs);
		if ($num_rows > 0) {
			$depth = $depth + 1;
			for ($i=0; $i < $adb->num_rows($rs); $i++) {
				$recordid = $adb->query_result($rs, $i, 0);
				if (array_key_exists($recordid, $child_records)) {
					continue;
				}
				$child_record_info = array();
				if (is_array($tree)) {
					$cvtreecolumn = getEntityName($tree[0], $recordid);
					$child_record_info[$tree[1]] = $cvtreecolumn[$recordid];
					$child_record_info['id'] = $recordid;
					$child_record_info['parent'] = $id;
					$child_record_info['recordid'] = 'parent_'.$recordid;
					$child_records[] = $child_record_info;
				} else {
					$child_record_info['depth'] = $depth;
					$child_records[$recordid] = $child_record_info;
				}
				$this->getChildRecords($recordid, $child_records, $depth, $referenceField, $currentModule, $tree);
			}
		}
		return $child_records;
	}

	/**
	* Function to get Module hierarchy of the given record
	* @param integer recorid
	* @return array Module hierarchy in array format
	*/
	public function getHierarchy($id, $currentModule) {
		global $log, $current_user;
		$log->debug('> getHierarchy '.$id);
		require_once 'include/ListView/GridUtils.php';
		$listview_header = array();
		$listview_entries = array();
		$listview_colname = array();
		$bmapname = $currentModule.'_ListColumns';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		$linkfield = $this->list_link_field;
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$cbMapLC = $cbMap->ListColumns()->getListFieldsFor($currentModule);
			$linkfield = $cbMap->ListColumns()->getListLinkFor($currentModule);
			if (!empty($cbMapLC)) {
				unset($this->list_fields_name);
				foreach ($cbMapLC as $label => $fields) {
					$tmp_field = '';
					foreach ($fields as $fieldname) {
						$tmp_field = $fieldname;
					}
					$this->list_fields_name[$label] = $tmp_field;
				}
			}
		}
		foreach ($this->list_fields_name as $fieldname => $colname) {
			if (getFieldVisibilityPermission($currentModule, $current_user->id, $colname) == '0') {
				$listview_colname[] = $colname;
				if ($colname == 'assigned_user_id') {
					$colname = 'smownerid';
				}
				$listview_header[] = array(
					'name' => $colname,
					'header' => getTranslatedString($fieldname)
				);
			}
		}
		$referenceField = $this->getSelfRelationField($currentModule);
		$records_list = array();
		$encountered_records = array($id);
		if ($referenceField) {
			$records_list = $this->getParentRecords($id, $records_list, $encountered_records, $referenceField, $currentModule);
			$records_list = $this->getChildRecords($id, $records_list, $records_list[$id]['depth'], $referenceField, $currentModule);
		}
		if (isset($records_list) && !empty($records_list)) {
			$entityField = getEntityField($currentModule);
			$entityField = $entityField['fieldname'];
			foreach ($records_list as $recordID => $dep) {
				$depth = $dep['depth'];
				$fieldsOf = __cb_getfieldsof(array(
					$recordID, $currentModule, implode(',', $listview_colname)
				));
				foreach ($fieldsOf as $field => $fieldValue) {
					$UIType = getUItype($currentModule, $field);
					$tabid = getTabid($currentModule);
					$fieldid = getFieldid($tabid, $field);
					$fieldinfo = array(
						'fieldtype' => 'corebos',
						'fieldinfo' => '',
						'name' => $field,
						'uitype' => $UIType,
						'fieldid' => $fieldid
					);
					$gridVal = getDataGridValue($currentModule, $recordID, $fieldinfo, $fieldValue);
					$record_depth = str_repeat(' .. ', $depth * 2);
					if ($entityField == $field) {
						$fieldVal = $record_depth.$gridVal[0];
					} else {
						$fieldVal = $gridVal[0];
						if ($linkfield == $field) {
							$fieldVal = '<a href="index.php?module='.$currentModule.'&action=DetailView&record='.$recordID.'">'.$record_depth.$fieldValue.'</a>';
						}
					}
					if (isset($gridVal[1]) && !empty($gridVal[1])) {
						$target = '';
						if (isset($gridVal[1][0]['mdTarget'])) {
							$target = $gridVal[1][0]['mdTarget'];
						}
						$fieldVal = '<a href="'.$gridVal[1][0]['mdLink'].'" '.$target.'>'.$fieldVal.'</a>';
					}
					if ($field == 'assigned_user_id') {
						$field = 'smownerid';
					}
					$fieldsOf[$field] = $fieldVal;
				}
				$listview_entries[] = $fieldsOf;
			}
		}
		$account_hierarchy = array('header'=>$listview_header,'entries'=>$listview_entries);
		$log->debug('< getHierarchy');
		return $account_hierarchy;
	}

	public function getSelfRelationField($module) {
		global $log, $adb;
		$log->debug('> getSelfRelationField');
		$rs = $adb->pquery('select columnname from vtiger_fieldmodulerel fl left join vtiger_field f on fl.fieldid=f.fieldid where fl.module=? and fl.relmodule=?', array($module, $module));
		if ($adb->num_rows($rs) == 1) {
			return $adb->query_result($rs, 0, 0);
		}
		$log->debug('< getSelfRelationField');
		return false;
	}

	public static function getcrmEntityTableAlias($modulename, $isaliasset = false) {
		$modObj = CRMEntity::getInstance($modulename);
		if ($isaliasset) {
			return $modObj->crmentityTable;
		}
		return (($modObj->crmentityTable != 'vtiger_crmentity') ? $modObj->crmentityTable. ' as vtiger_crmentity':'vtiger_crmentity');
	}
}
?>
