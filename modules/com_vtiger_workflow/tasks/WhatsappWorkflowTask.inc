<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class WhatsappWorkflowTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('messageBody', 'attachmentids');
	}

	public function doTask(&$entityData) {
	}
	public function afterRetrieve() {
		global $adb;
		$this->attachmentsinfo = array();
		$this->dzattinfo = array();
		if (!empty($this->attachmentids)) {
			$aids = explode(',', trim($this->attachmentids, ','));
			$query = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid, vtiger_notes.filename, vtiger_notes.filesize
				FROM vtiger_attachments
				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
				INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
				INNER JOIN vtiger_notes ON vtiger_notes.notesid=vtiger_seattachmentsrel.crmid
				WHERE deleted=0 AND vtiger_seattachmentsrel.crmid = ?';
			foreach ($aids as $docid) {
				$docrs = $adb->pquery($query, array($docid));
				if ($docrs && $adb->num_rows($docrs)==1) {
					$attname = $adb->query_result($docrs, 0, 'path').$adb->query_result($docrs, 0, 'attachmentsid').'_'.$adb->query_result($docrs, 0, 'name');
					$this->attachmentsinfo[] = $attname;
					$this->dzattinfo[] = array(
						'docid' => $docid,
						'name' => $adb->query_result($docrs, 0, 'filename'),
						'size' => $adb->query_result($docrs, 0, 'filesize'),
					);
				} else {
					$this->attachmentids = str_replace($docid.',', '', $this->attachmentids);
				}
			}
		}
	}
}
?>