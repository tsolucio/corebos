<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : evvtgendoc
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'config.inc.php';
require_once 'modules/Users/Users.php';
require_once 'modules/evvtgendoc/OpenDocument.php';
require_once 'include/logging.php';
include_once 'include/utils/pdfConcat.php';
$log = LoggerManager::getLogger('index');
global $currentModule,$current_language,$current_user,$adb,$mod_strings,$theme;
$fileid=vtlib_purify($_REQUEST['gendoctemplate']);
$records=array();
$records=explode(';', $_REQUEST['recordval']);
$module = vtlib_purify($_REQUEST['recordval_type']);
$module=preg_replace('/[[:^alnum:]]/', '', $module);
$modulei18n = getTranslatedString('SINGLE_'.$module, $module);
$format=vtlib_purify($_REQUEST['gdformat']);
$dodebug = isset($_REQUEST['debug']);
$clang = isset($_REQUEST['compilelang']) ? $_REQUEST['compilelang'] : 'en';
$holdUser = $current_user;
$current_user = Users::getActiveAdminUser();

$orgfile=$adb->pquery(
	"SELECT CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name, n.mergetemplate, n.template_for
		FROM vtiger_notes n
		JOIN vtiger_seattachmentsrel sa on sa.crmid=n.notesid
		JOIN vtiger_attachments a on a.attachmentsid=sa.attachmentsid
		WHERE n.notesid=?",
	array($fileid)
);
$mergeTemplatePath=$adb->query_result($orgfile, 0, 'filepath');
$mergeTemplateName=$adb->query_result($orgfile, 0, 'name');
$mergetemplate = $adb->query_result($orgfile, 0, 'mergetemplate');
if ($mergetemplate=='1') {
	$mergetemplatefor = $adb->query_result($orgfile, 0, 'template_for');
	if (in_array($mergetemplatefor, array('Accounts', 'Contacts', 'Leads', 'HelpDesk'))) {
		// the script below uses the $mergeTemplatePath and $mergeTemplateName variables which are defined here
		include "modules/$mergetemplatefor/Merge.php";
	} else {
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	}
} else {
	$zipname = OpenDocument::GENDOCCACHE . '/' . $module . '/gendoc' . $holdUser->id . '.zip';
	if (file_exists($zipname)) {
		unlink($zipname);
	}
	$zip = new Vtiger_Zip($zipname);
	$file2merge = array();
	$out = '';
	$debuginfo = '';
	if ($dodebug) {
		ob_start();
	}
	foreach ($records as $record) {
		if (!empty($record)) {
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
				$out.= '<a href="'.($format=='pdf' ? $pdfname : $filename).'">'.$app_strings['DownloadMergeFile'].'</a></td></tr></table><br/>';
			}
		}
	}
	if ($format == 'onepdf') {
		$pdf = new concat_pdf();
		$pdf->setFiles($file2merge);
		$pdf->concat();
		$pdf->Output($root_directory.OpenDocument::GENDOCCACHE . '/' . $module. '/' . $module . '.pdf', 'F');
		$out.= '<table class="lvtCol" width="100%"><tr><td>';
		$einfo = getEntityName($module, $record);
		$out.=$mod_strings['LBL_INFORMATION'].$modulei18n.' '.$einfo[$record].' '.$mod_strings['LBL_TEMPLATE']." $mergeTemplateName<br/>";
		$out.= '<a href="' . OpenDocument::GENDOCCACHE . '/' . $module. '/' . $module . '.pdf">' . $app_strings['DownloadMergeFile'] . '</a></td></tr></table><br/>';
	}
	$zip->save();
	$smarty = new vtigerCRM_Smarty;
	if ($dodebug) {
		$debuginfo = ob_get_contents();
		ob_end_clean();
	}
	$smarty->assign('DEBUGINFO', $debuginfo);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('THEME', $theme);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$tool_buttons = array(
		'EditView' => 'no',
		'CreateView' => 'no',
		'index' => 'yes',
		'Import' => 'no',
		'Export' => 'no',
		'Merge' => 'no',
		'DuplicatesHandling' => 'no',
		'Calendar' => 'no',
		'moduleSettings' => 'no',
	);
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->assign('OUTPUT', $out);
	$smarty->assign('ZIPNAME', $filename);
	$smarty->display('modules/evvtgendoc/odt.tpl');
}
$current_user = $holdUser;
?>
