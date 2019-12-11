<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM.
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
*************************************************************************************************
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

function cbws_getmergedtemplate($template, $crmids, $format, $user) {
	global $log,$adb;

	$mergedtemplate = null;
	if (!empty($this->attachmentids)) {
		$GenDocActive = vtlib_isModuleActive('evvtgendoc');
		if ($GenDocActive) {
			include_once 'modules/evvtgendoc/OpenDocument.php';
			$GenDocPDF = (coreBOS_Settings::getSetting('cbgendoc_server', '')!='');
		} else {
			$GenDocPDF = false;
		}
		if ($GenDocPDF) {
			$module = 'evvtgendoc';
			$orgfile = $adb->pquery(
				"SELECT CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name, n.mergetemplate, n.template_for
                    FROM vtiger_notes n
                    JOIN vtiger_seattachmentsrel sa on sa.crmid=n.notesid
                    JOIN vtiger_attachments a on a.attachmentsid=sa.attachmentsid
                    WHERE n.notesid=?",
				array($template)
			);
			$mergeTemplatePath = $adb->query_result($orgfile, 0, 'filepath');
			$mergeTemplateName = $adb->query_result($orgfile, 0, 'name');
			$mergetemplate = $adb->query_result($orgfile, 0, 'mergetemplate');

			$zipname = OpenDocument::GENDOCCACHE . '/' . $module . '/gendoc' . $holdUser->id . '.zip';
			if (file_exists($zipname)) {
				unlink($zipname);
			}
			$zip = new Vtiger_Zip($zipname);
			$file2merge = array();
			$out = '';

			$records = json_decode($crmids, true);
			foreach ($records as $record) {
				if (!empty($record)) {
					$webserviceObject = VtigerWebserviceObject::fromId($adb, $record);
					$handlerPath = $webserviceObject->getHandlerPath();
					$handlerClass = $webserviceObject->getHandlerClass();

					require_once $handlerPath;

					$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
					$meta = $handler->getMeta();
					$entityName = $meta->getObjectEntityName($record);
					$types = vtws_listtypes(null, $user);

					if (!in_array($entityName, $types['types'])) {
						throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
					}
					if ($meta->hasReadAccess()!==true) {
						throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read is denied');
					}

					if ($entityName !== $webserviceObject->getEntityName()) {
						throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
					}

					if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $record)) {
						throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
					}

					$idComponents = vtws_getIdComponents($record);
					if (!$meta->exists($idComponents[1])) {
						throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
					}

					$record = preg_replace('/[^0-9]/', '', $record);
					$fullfilename = $root_directory . OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
					$fullpdfname = $root_directory . OpenDocument::GENDOCCACHE . '/' . $module.'/odtout' . $record . '.pdf';
					$filename = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
					$pdfname = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
					$odtout = new OpenDocument;
					OpenDocument::$debug = $dodebug;
					OpenDocument::$compile_language = $clang;
					if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
						include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
					} else {
						include 'modules/evvtgendoc/commands_en.php';
					}
					if (!is_dir(OpenDocument::GENDOCCACHE . '/' . $module)) {
						mkdir(OpenDocument::GENDOCCACHE . '/' . $module, 0777, true);
					}
					if (file_exists($filename)) {
						unlink($filename);
					}
					if (file_exists($fullpdfname)) {
						unlink($fullpdfname);
					}
					$odtout->GenDoc($mergeTemplatePath, $record, $module);
					$odtout->save($filename);
					ZipWrapper::copyPictures($mergeTemplatePath, $filename, $odtout->changedImages, $odtout->newImages);
					$odtout->postprocessing($fullfilename);
					$zipfname = 'odtout'.$record;
					if ($format=='pdf') {
						$odtout->convert($filename, $pdfname);
						$zip->addFile($pdfname, $zipfname.'pdf');
					} elseif ($format=='onepdf') {
						$odtout->convert($filename, $pdfname);
						$file2merge[] = $pdfname;
					} else {
						$zip->addFile($filename, $zipfname.'odt');
					}
					if ($format != 'onepdf') {
						$out.= '<table class="lvtCol" width="100%"><tr><td>';
						$einfo = getEntityName($module, $record);
						$out.=$mod_strings['LBL_INFORMATION'].$modulei18n.' '.$einfo[$record].' '.$mod_strings['LBL_TEMPLATE']." $mergeTemplateName<br/>";
						//$out.="<br/>".$odtout->output().'<br/><br/>';
						$out.= '<a href="'.($format=='pdf' ? $pdfname : $filename).'">'.$app_strings['DownloadMergeFile'].'</a></td></tr></table><br/>';
					}
				}
			}
			$mergedtemplate = $filename;
			if ($format == 'onepdf') {
				$pdf = new concat_pdf();
				$pdf->setFiles($file2merge);
				$pdf->concat();
				$pdf->Output($root_directory.OpenDocument::GENDOCCACHE . '/' . $module. '/' . $module . '.pdf', 'F');
				$out.= '<table class="lvtCol" width="100%"><tr><td>';
				$einfo = getEntityName($module, $record);
				$out.=$mod_strings['LBL_INFORMATION'].$modulei18n.' '.$einfo[$record].' '.$mod_strings['LBL_TEMPLATE']." $mergeTemplateName<br/>";
				$out.= '<a href="' . OpenDocument::GENDOCCACHE . '/' . $module. '/' . $module . '.pdf">' . $app_strings['DownloadMergeFile'] . '</a></td></tr></table><br/>';
				$mergetemplate = $out;
			}
			$zip->save();
		}
	} else {
		return false;
	}
	return $mergedtemplate;
}
?>