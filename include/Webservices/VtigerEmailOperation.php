<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of coreBOSMail
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

class VtigerEmailOperation extends VtigerModuleOperation {
	protected $tabId;
	protected $isEntity = true;

	public function __construct($webserviceObject, $user, $adb, $log) {
		parent::__construct($webserviceObject, $user, $adb, $log);
		$this->tabId = $this->meta->getTabId();
	}

	/*
	 * This create function supports a few virtual fields for the attachment and the related entities
	 * so it expects and $element array with the normal Email fields and these additional ones:
	 *
	 * 'files' this is an array of file specifications: filesize, filetype, name, content (base64 encode of file)
	 *
	 * 'relations'  this is an array of related entity id's, the id's must be in webservice extended format
	 *     all the indicated entities will be related to the document being created
	 */
	public function create($elementType, $element) {
		global $adb;
		$crmObject = new VtigerCRMObject($elementType, false);

		$attachments = array();
		if (!empty($element['files'])) {
			foreach ($element['files'] as $file) {
				$element['filesize']=$file['size'];
				$file['assigned_user_id'] = $element['assigned_user_id'];
				$file['setype'] = "Emails Attachment";
				$attachments[] = SaveAttachmentDB($file);
				$element['filetype']=$file['type'];
				$element['filename']= str_replace(' ', '_', $file['name']);
			}
		}

		$_REQUEST['module'] = 'Emails';

		$element = DataTransform::sanitizeForInsert($element, $this->meta);

		if (!empty($element['related']) && is_array($element['related'])) {
			$_REQUEST['parent_id'] = '';
			foreach ($element['related'] as $rel) {
				$ids = vtws_getIdComponents($rel);
				$relid = $ids[1];
				if (!empty($relid)) {
					$tabname = $adb->query_result($adb->pquery('select name from vtiger_ws_entity where id=?', array($ids[0])), 0, 'name');
					$tabid = getTabid($tabname);
					$rs = $adb->pquery('select fieldid from vtiger_field where tabid=? and uitype=13 and vtiger_field.presence in (0,2)', array($tabid));
					$fieldid = $adb->query_result($rs, 0, 'fieldid');
					$_REQUEST['parent_id'] .= $relid.'@'.$fieldid.'|';
				}
			}
		} else {
			$_REQUEST['parent_id'] = isset($element['parent_id']) ? $element['parent_id'] : '';
		}
		$error = $crmObject->create($element);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, 'Database error while performing required operation');
		}

		$id = $crmObject->getObjectId();

		$error = $crmObject->read($id);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, 'Database error while performing required operation');
		}

		if (!empty($attachments)) {
			foreach ($attachments as $attachid) {
				// Link file attached to document
				$adb->pquery('INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)', array($id, $attachid));
			}
		}

		return DataTransform::filterAndSanitize($crmObject->getFields(), $this->meta);
	}

	public function retrieve($id, $deleted = false) {
		global $adb;
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];
		$data = parent::retrieve($id);
		if (!Emails::EmailHasBeenSent($elemid)) {
			$data['date_start'] = '';
		}
		// Add relations
		$relsrs=$adb->pquery('SELECT crmid FROM vtiger_senotesrel where notesid=?', array($elemid));
		$rels=array();
		while ($rl = $adb->fetch_array($relsrs)) {
			$rels[] = vtws_getEntityId(getSalesEntityType($rl['crmid'])) . 'x' . $rl['crmid'];
		}
		$data['relations']=$rels;
		return $data;
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
	public function update($element) {
		global $adb;
		$ids = vtws_getIdComponents($element["id"]);
		if (!empty($element['filename'])) {
			$element['filesize']=$element['filename']['size'];
			$attachid=SaveAttachmentDB($element);
			$element['filetype']=$element['filename']['type'];
			$element['filename']= str_replace(' ', '_', $element['filename']['name']);
		}
		// $relations = isset($element['relations']) ? $element['related'] : null;
		// unset($element['relations']);

		$element = DataTransform::sanitizeForInsert($element, $this->meta);

		$crmObject = new VtigerCRMObject($this->tabId, true);
		$crmObject->setObjectId($ids[1]);
		$error = $crmObject->update($element);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, 'Database error while performing required operation');
		}

		$id = $crmObject->getObjectId();

		$error = $crmObject->read($id);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, 'Database error while performing required operation');
		}

		if (!empty($attachid)) {
			// Link file attached to document
			$adb->pquery('DELETE from vtiger_seattachmentsrel where crmid=?', array($id));
			$adb->pquery('INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)', array($id, $attachid));
		}
		// Establish relations
		//$adb->pquery("DELETE from vtiger_senotesrel where crmid=?",Array($id));
		//foreach ($relations as $rel) {
		//	$ids = vtws_getIdComponents($rel);
		//	$relid = $ids[1];
		//	$adb->pquery("INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)",Array($relid, $id));
		//}

		return DataTransform::filterAndSanitize($crmObject->getFields(), $this->meta);
	}
}
?>
