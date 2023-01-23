<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'data/CRMEntity.php';
require_once 'include/tcpdf/tcpdf.php';

use setasign\Fpdi\Fpdi;

require_once 'include/fpdf/fpdf.php';
if (is_dir('include/fpdi2') && is_dir('include/fpdi_pdf-parser2')) {
	require_once 'include/fpdi2/src/autoload.php';
	require_once 'include/fpdi_pdf-parser2/src/autoload.php';
} else {
	require_once 'include/fpdi/src/autoload.php';
}

class CBSignPDFDocument extends VTTask {
	public $taskSavedData = array();
	public $executeImmediately = true;
	public $queable = false;
	public $image_field;
	public $coordX;
	public $coordY;

	public function getFieldNames() {
		return array('image_field', 'coordX', 'coordY');
	}

	public function after_retrieve() {
		$this->taskSavedData = array(
			'image_field' => $this->image_field,
			'coordX' => $this->coordX,
			'coordY' => $this->coordY );
	}

	public function doTask(&$entity) {
		global $adb, $site_URL, $current_user;
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];

		if ($this->image_field != '') {
			$image_field = $this->image_field;
			$width = (int)$this->coordX;
			$height = (int)$this->coordY;

			// Fetching PDF
			$sql = 'select vtiger_attachments.type FileType, vtiger_attachments.path as path, vtiger_attachments.name as name,crm2.modifiedtime lastmodified,
					vtiger_crmentity.modifiedtime, vtiger_seattachmentsrel.attachmentsid attachmentsid, vtiger_crmentity.smownerid smownerid,
					vtiger_notes.notesid crmid, vtiger_notes.notecontent description, vtiger_notes.*
				from vtiger_notes
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
				inner join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid
				inner join vtiger_crmobject crm2 on crm2.crmid=vtiger_senotesrel.crmid
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
				left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid';
			$sql .= getNonAdminAccessControlQuery($moduleName, $current_user);
			$sql .= ' WHERE vtiger_crmentity.deleted = 0 and crm2.crmid=? order by vtiger_attachments.attachmentsid desc LIMIT 1';
			$rs = $adb->pquery($sql, array($recordId));
			if ($rs && $adb->num_rows($rs)>0) {
				$filepath = $adb->query_result($rs, 0, 'path');
				$name = $adb->query_result($rs, 0, 'name');
				$path = $site_URL.'/'.$filepath.$adb->query_result($rs, 0, 'attachmentsid').'_'.$name;
				$file_storage_path = $filepath.$adb->query_result($rs, 0, 'attachmentsid').'_'.$name;

				$util = new VTWorkflowUtils();
				$hold_user = $current_user;
				$util->loggedInUser();
				if (is_null($current_user)) {
					$current_user = $hold_user; // make sure current_user is defined
				}

				if (isset($current_user->$image_field) && $current_user->$image_field != '') {
					// Getting image
					$signature_path = '';
					$sql = "select vtiger_attachments.*
						from vtiger_attachments
						inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid
						where vtiger_crmentity.setype='Users Attachment' and vtiger_attachments.name=?";
					$image_res = $adb->pquery($sql, array(str_replace(' ', '_', decode_html($current_user->$image_field))));
					if ($adb->num_rows($image_res)>0) {
						$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
						$image_path = $adb->query_result($image_res, 0, 'path');
						$image_name = decode_html($adb->query_result($image_res, 0, 'name'));
						$signature_path = $site_URL.'/'.$image_path . $image_id . '_' . urlencode($image_name);
					}
					if ($signature_path != '') {
						// Adding Image to PDF;
						$pdf = new FPDI();
						$pages_count = $pdf->setSourceFile($file_storage_path);
						for ($i = 1; $i <= $pages_count; $i++) {
							$tplIdx = $pdf->importPage($i);
							$s = $pdf->getTemplatesize($tplIdx);
							$pdf->AddPage();
							$pdf->useTemplate($tplIdx, 0, 0, $s['width'], $s['height'], true);
							if ($pages_count == $i) {
								$pdf->Image($signature_path, $width, $height, '', '', '', '', '', false, 300);
							}
						}
						$pdf->Output($file_storage_path, 'F');
					}
				}
				$util->revertUser();
			}
		}
	}
}
?>