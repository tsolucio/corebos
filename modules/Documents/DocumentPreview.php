<?php
/*************************************************************************************************
 * Copyright 2022 Spike, JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************/
// block://DocumentPreview:modules/Documents/DocumentPreview.php:record_id=$RECORD$
// block://DocumentPreview:modules/Documents/DocumentPreview.php:docid=45112

require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class DocumentPreview {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new DocumentPreview_DetailViewBlock());
	}
}

class DocumentPreview_DetailViewBlock extends DeveloperBlock {
	// Implement widget functionality
	protected $widgetName = 'Document Preview';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $adb, $site_URL, $current_language;
		$this->context = $context;
		$smarty = $this->getViewer();
		$record_id = $this->getFromContext('record_id');
		$docid = $this->getFromContext('docid');
		$mapid = $this->getFromContext('mapid');
		$width = $this->getFromContext('width');
		$height = $this->getFromContext('height');
		if ($record_id) {
			$rs = $adb->pquery(
				"select vtiger_attachments.type FileType, vtiger_attachments.path as path, vtiger_attachments.name as name,
					vtiger_seattachmentsrel.attachmentsid attachmentsid,vtiger_notes.notecontent description,vtiger_notes.*
				from vtiger_notes
				inner join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
				inner join vtiger_crmobject crm2 on crm2.crmid=vtiger_senotesrel.crmid
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
				left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
				where crm2.crmid=? and filetype='application/pdf' order by vtiger_attachments.attachmentsid desc LIMIT 1",
				array($record_id)
			);
		} elseif ($docid || $mapid) {
			if ($mapid) {
				$docid = coreBOS_Rule::evaluate($mapid, $this->getFromContext('crmid'));
				if (empty($docid)) {
					$docid = 0;
				}
			}
			$rs = $adb->pquery(
				'select vtiger_attachments.type FileType, vtiger_attachments.path as path, vtiger_attachments.name as name,
					vtiger_seattachmentsrel.attachmentsid attachmentsid,vtiger_notes.notecontent description,vtiger_notes.*
				from vtiger_notes
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
				left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
				where notesid=?',
				array($docid)
			);
		} else {
			$rs = false;
		}
		if ($rs && $adb->num_rows($rs)>0) {
			$filepath = $adb->query_result($rs, 0, 'path');
			$name = $adb->query_result($rs, 0, 'name');
			$path = $site_URL.'/'.$filepath.$adb->query_result($rs, 0, 'attachmentsid').'_'.$name;
			$smarty->assign('filetype', $adb->query_result($rs, 0, 'FileType'));
			$smarty->assign('attachmentsid', $adb->query_result($rs, 0, 'attachmentsid'));
			$smarty->assign('description', $adb->query_result($rs, 0, 'description'));
			$smarty->assign('title', $adb->query_result($rs, 0, 'title'));
			$smarty->assign('filename', $adb->query_result($rs, 0, 'filename'));
			$smarty->assign('filestatus', $adb->query_result($rs, 0, 'filestatus'));
			$smarty->assign('filesize', $adb->query_result($rs, 0, 'filesize'));
			$smarty->assign('_downloadurl', $path);
			$smarty->assign('width', $width);
			$smarty->assign('height', $height);
			$smarty->assign('UserLanguage', substr($current_language, 0, 2).'-'.strtoupper(substr($current_language, -2)));
			$smarty->assign('NoFile', false);
		} else {
			$smarty->assign('NoFile', true);
		}
		return $smarty->fetch('modules/Documents/DocumentPreview.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new DocumentPreview_DetailViewBlock();
	echo $smq->process($_REQUEST);
}