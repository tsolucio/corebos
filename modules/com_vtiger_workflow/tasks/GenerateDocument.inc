<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : ATConsulting, Mikel Kasneci.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/evvtgendoc/OpenDocument.php';
require_once 'modules/Documents/ShareDocLinkWidgetDetailViewBlock.php';

class GenerateDocument extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('gdformat', 'gendoctemplate', 'updateOnChange', 'shareOnCreate');
	}

	public function doTask(&$entity) {
		global $adb, $root_directory, $current_language, $default_charset;
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents', true);
		$crmEntityTableAlias = CRMEntity::getcrmEntityTableAlias('Documents', false);
		$relatedDocsSQL = 'SELECT vtiger_senotesrel.notesid
			FROM vtiger_senotesrel
			INNER JOIN vtiger_notes on vtiger_notes.notesid=vtiger_senotesrel.notesid
			WHERE vtiger_notes.gendoctplid=? and vtiger_senotesrel.crmid=?';
		$relatedDocsSQLNotDeleted = 'SELECT vtiger_senotesrel.notesid
			FROM vtiger_senotesrel
			INNER JOIN vtiger_notes on vtiger_notes.notesid=vtiger_senotesrel.notesid
			INNER JOIN '.$crmEntityTableAlias.' on vtiger_notes.notesid=vtiger_crmentity.crmid
			WHERE vtiger_notes.gendoctplid=? and vtiger_senotesrel.crmid=? and vtiger_crmentity.deleted=0';
		$smq = new ShareDocLinkWidgetDetailViewBlock();
		$this->addGenDocTplColumn();
		$format = ($this->gdformat==1 ? 'odt' : 'pdf');
		$record = preg_replace('/[^0-9]/', '', substr($entity->data['id'], strpos($entity->data['id'], 'x')));
		$module = $entity->getModuleName();
		$einfo = getEntityName($module, $record);
		$fullfilename = $root_directory .  OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
		$fullpdfname = $root_directory . OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
		$filename = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
		$pdfname = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
		if (!is_dir(OpenDocument::GENDOCCACHE . '/' . $module)) {
			mkdir(OpenDocument::GENDOCCACHE . '/' . $module, 0777, true);
		}
		$odtout = new OpenDocument();
		OpenDocument::$compile_language = GlobalVariable::getVariable('GenDoc_Default_Compile_Language', substr($current_language, 0, 2), $module);
		if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
			include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
		} else {
			include 'modules/evvtgendoc/commands_en.php';
		}
		$templates = explode(';', $this->gendoctemplate);
		foreach ($templates as $fileid) {
			$orgfile = $adb->pquery(
				"Select CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name
					from vtiger_notes n
					join vtiger_seattachmentsrel sa on sa.crmid=n.notesid
					join vtiger_attachments a on a.attachmentsid=sa.attachmentsid
					where n.notesid=?",
				array($fileid)
			);
			$mergeTemplatePath=html_entity_decode($adb->query_result($orgfile, 0, 'filepath'), ENT_QUOTES, $default_charset);
			$mergeTemplateName=basename(html_entity_decode($adb->query_result($orgfile, 0, 'name'), ENT_QUOTES, $default_charset), '.odt');
			$name = str_replace(' ', '_', $mergeTemplateName).'_'.str_replace(' ', '_', $einfo[$record]);
			if (file_exists($fullfilename)) {
				unlink($fullfilename);
			}
			if (file_exists($fullpdfname)) {
				unlink($fullpdfname);
			}
			$odtout->GenDoc($mergeTemplatePath, $record, $module);
			$odtout->save($filename);
			ZipWrapper::copyPictures($mergeTemplatePath, $filename, $odtout->changedImages, $odtout->newImages);
			$odtout->postprocessing($fullfilename);
			if ($format=='pdf') {
				$odtout->convert($filename, $pdfname);
			}
			$docid = OpenDocument::saveAsDocument($record, $module, $format, $mergeTemplateName, ($format=='pdf' ? $fullpdfname : $fullfilename), $name);
			if ($this->updateOnChange) {
				$rdocs = $adb->pquery($relatedDocsSQLNotDeleted, array($fileid, $record));
				if ($rdocs && $adb->num_rows($rdocs)>0) {
					while ($rdoc = $adb->fetch_array($rdocs)) {
						if ($this->shareOnCreate && !$smq->isShared($rdoc['notesid'])) {
							$smq->createShareLink($rdoc['notesid']);
						}
						$smq->swapDocument($rdoc['notesid'], $docid);
						$skey = $smq->getShareCode($docid);
					}
					$adb->pquery(
						'update '.$crmEntityTable.' set '.$crmEntityTable.'.deleted=1 where '.$crmEntityTable.".crmid in ($relatedDocsSQL)",
						array($fileid, $record)
					);
					$adb->pquery(
						"update vtiger_crmobject set vtiger_crmobject.deleted=1 where vtiger_crmobject.crmid in ($relatedDocsSQL)",
						array($fileid, $record)
					);
				} else { // creating for the first time
					$skey = $smq->createShareLink($docid);
				}
				$entity->WorkflowContext['SharedDocumentCode'] = $skey;
				$entity->WorkflowContext['SharedDocumentLink'] = $smq->constructShareLink($skey);
			} elseif ($this->shareOnCreate && !$smq->isShared($docid)) {
				$skey = $smq->createShareLink($docid);
				$entity->WorkflowContext['SharedDocumentCode'] = $skey;
				$entity->WorkflowContext['SharedDocumentLink'] = $smq->constructShareLink($skey);
			}
			$adb->pquery('update vtiger_notes set gendoctplid=? where notesid=?', array($fileid, $docid));
		}
	}

	private function addGenDocTplColumn() {
		global $adb;
		$cninv=$adb->getColumnNames('vtiger_notes');
		if (!in_array('gendoctplid', $cninv)) {
			$adb->query('ALTER TABLE vtiger_notes ADD COLUMN gendoctplid INT(11) DEFAULT NULL');
		}
	}
}
?>