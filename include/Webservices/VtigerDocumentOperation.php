<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************/

include_once 'include/Webservices/VtigerModuleOperation.php';
include_once 'include/Webservices/AttachmentHelper.php';

class VtigerDocumentOperation extends VtigerModuleOperation {
	protected $tabId;
	protected $isEntity = true;

	public function __construct($webserviceObject,$user,$adb,$log){
		parent::__construct($webserviceObject,$user,$adb,$log);
		$this->tabId = $this->meta->getTabId();
	}

	/*
	 * This create function supports a few virtual fields for the attachment and the related entities
	 * so it expects and $element array with the normal Document fields and these additional ones:
	 * 
	 * 'attachment'  this is a base64encoded string contaning the full document to be saved internally,
	 *     this will only be checked if filelocationtype=='I'
	 * 
	 * 'attachment_name'  a string with the name of the attachment
	 * 
	 * 'relations'  this is an array of related entity id's, the id's must be in webservice extended format
	 *     all the indicated entities will be related to the document being created
	 *     *** this is done by the main vtws_create() function  ***
	 * 
	 */
	public function create($elementType,$element){
		global $adb,$log;
		$crmObject = new VtigerCRMObject($elementType, false);

		if ($element['filelocationtype']=='I' and !empty($element['filename'])) {
			$file = $element['filename'];
			$file['assigned_user_id'] = $element['assigned_user_id'];
			$file['setype'] = "Documents Attachment";
			$attachid = SaveAttachmentDB($file);
			$element['filetype']=$file['type'];
			$element['filename']=$filename = str_replace(array(' ','/'), '_',$file['name']);  // no spaces nor slashes
			$element['filesize']=$file['size'];
			if ($element['filesize']==0) {
				$dbQuery = 'SELECT * FROM vtiger_attachments WHERE attachmentsid = ?' ;
				$result = $adb->pquery($dbQuery, array($attachid));
				if($result and $adb->num_rows($result) == 1) {
					$name = @$adb->query_result($result, 0, 'name');
					$filepath = @$adb->query_result($result, 0, 'path');
					$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
					$saved_filename = $attachid."_".$name;
					$disk_file_size = filesize($filepath.$saved_filename);
					$element['filesize']=$file['size']=$disk_file_size;
				}
			}
		}

		$element = DataTransform::sanitizeForInsert($element,$this->meta);

		$error = $crmObject->create($element);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,"Database error while performing required operation");
		}

		$id = $crmObject->getObjectId();

		$error = $crmObject->read($id);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,"Database error while performing required operation");
		}

		if ($element['filelocationtype']=='I' and !empty($attachid)) {
			// Link file attached to document
			$adb->pquery("INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",Array($id, $attachid));
		}
		// Establish relations *** this is done by the main vtws_create() function  ***

		return DataTransform::filterAndSanitize($crmObject->getFields(),$this->meta);
	}

	public function retrieve($id,$deleted=false){
		global $adb,$default_charset,$site_URL;
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];
		$doc = parent::retrieve($id,$deleted);
		// Add relations
		$relsrs=$adb->pquery("SELECT crmid FROM vtiger_senotesrel where notesid=?",Array($elemid));
		$rels=array();
		while ($rl = $adb->fetch_array($relsrs)) {
			$rels[]=$this->vtyiicpng_getWSEntityId(getSalesEntityType($rl['crmid'])).$rl['crmid'];
		}
		$doc['relations']=$rels;
		if ($doc['filelocationtype']=='I') { // Add direct download link
			$relatt=$adb->pquery("SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?",Array($elemid));
			if ($relatt and $adb->num_rows($relatt)==1) {
				$fileid = $adb->query_result($relatt,0,0);
				$attrs=$adb->pquery("SELECT * FROM vtiger_attachments WHERE attachmentsid = ?",Array($fileid));
				if($attrs and $adb->num_rows($attrs) == 1) {
					$name = @$adb->query_result($attrs, 0, "name");
					$filepath = @$adb->query_result($attrs, 0, "path");
					$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
					$doc['_downloadurl'] = $site_URL."/".$filepath.$fileid."_".$name;
				}
			}
		}
		return $doc;
	}

	/*
	 * This method accepts the same virtual fields that the create method does (see create)
	 * 
	 * It will first eliminate the current related attachement and then relate the new attachment
	 * 
	 * It will first eliminate all the current relations and then establish the new ones being sent in
	 * so ALL relations that are needed must sent in again each time
	 * 
	 */
	public function update($element){
		global $adb;
		$ids = vtws_getIdComponents($element["id"]);
		if ($element['filelocationtype']=='I' and !empty($element['filename'])) {
			$file = $element['filename'];
			$element['filesize']=$file['size'];
			$file['assigned_user_id'] = $element['assigned_user_id'];
			$file['setype'] = "Documents Attachment";
			$attachid = SaveAttachmentDB($file);
			$element['filetype']=$file['type'];
			$element['filename']=$filename = str_replace(' ', '_',$file['name']);
		}

		$element = DataTransform::sanitizeForInsert($element,$this->meta);

		$crmObject = new VtigerCRMObject($this->tabId, true);
		$crmObject->setObjectId($ids[1]);
		$error = $crmObject->update($element);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,"Database error while performing required operation");
		}

		$id = $crmObject->getObjectId();

		$error = $crmObject->read($id);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,"Database error while performing required operation");
		}

		if ($element['filelocationtype']=='I' and !empty($attachid)) {
			// Link file attached to document
			$adb->pquery("DELETE from vtiger_seattachmentsrel where crmid=?",Array($id));
			$adb->pquery("INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",Array($id, $attachid));
		}

		return DataTransform::filterAndSanitize($crmObject->getFields(),$this->meta);
	}

	private function vtyiicpng_getWSEntityId($entityName) {
		global $adb;
		$rs = $adb->query("select id from vtiger_ws_entity where name='$entityName'");
		$wsid = @$adb->query_result($rs, 0, 'id').'x';
		return $wsid;
	}

}
?>
