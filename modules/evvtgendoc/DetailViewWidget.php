<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : GenDoc:: Advanced Open Office Merge
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'data/CRMEntity.php';
global $adb,$current_user,$mod_strings;
$moduletemplate = vtlib_purify($_REQUEST['formodule']);
$modulei18n = getTranslatedString('SINGLE_'.$moduletemplate, $moduletemplate);
$forrecord = vtlib_purify($_REQUEST['forrecord']);
$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents');
$templates=$adb->pquery(
	'SELECT notesid,title
		FROM vtiger_notes
		INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_notes.notesid
		WHERE deleted = 0 and template=1 and template_for=? order by title',
	array($moduletemplate)
);
$tpls = array();
while ($t = $adb->fetch_array($templates)) {
	$tpls[$t['notesid']] = $t['title'];
}
$number=$adb->num_rows($templates);
if ($number==0) {
	echo getTranslatedString('LBL_NOTEMPLATE', 'evvtgendoc');
} elseif (isPermitted($moduletemplate, 'Merge')!='yes') {
	echo getTranslatedString('LBL_PERMISSION');
} else {
	echo "<select class='small' size=8 style='width:280px;overflow:auto;' id='gendoctemplate' name='gendoctemplate'>";
	$firsttime = true;
	foreach ($tpls as $nid => $tname) {
		echo "<option value='".$nid."' title='".$tname."' ". ($firsttime ? 'selected' : '') .">".$tname.'</option>';
		$firsttime = false;
	}
	$gendoc_pdfactive = coreBOS_Settings::getSetting('cbgendoc_active', 0);
	$gendoc_showpdf = coreBOS_Settings::getSetting('cbgendoc_showpdflinks', 0);
	$gendoc_active = ($gendoc_pdfactive==1 || $gendoc_showpdf==1);
	?>
	</select><br/>
	<table aria-describedby="GenDoc Templates">
	<?php
	$htmltrtdo = '<tr class="actionlink"><td align="left" style="padding-left:10px;">';
	$htmltrtdc = '</td></tr>';
	$htmlimg = '<img src="modules/evvtgendoc/images/';
	$htmlstyle = 'style="border:0;padding:0 4px;"';
	$linko = '<a class="webMnu" href="javascript:gendocAction(';
	$linkc = '</a>';
	$altdoc = ' alt="DOC" />';
	if ($gendoc_active) {
		$altpdf = ' alt="PDF" />';
		echo $htmltrtdo.$linko."'export','pdf','$moduletemplate','$forrecord','$modulei18n');".'">'.$htmlimg.'genpdf.gif" '.$htmlstyle.$altpdf.$linkc;
		echo $linko."'export','pdf','$moduletemplate','$forrecord','$modulei18n');".'">'.getTranslatedString('Export PDF', 'evvtgendoc').$linkc.$htmltrtdc;
		echo $htmltrtdo.$linko."'email','pdf','$moduletemplate','$forrecord','$modulei18n');".'">'.$htmlimg.'emailpdf.gif" '.$htmlstyle.$altpdf.$linkc;
		echo $linko."'email','pdf','$moduletemplate','$forrecord','$modulei18n');".'">'.getTranslatedString('EMail PDF', 'evvtgendoc').$linkc.$htmltrtdc;
		echo $htmltrtdo.$linko."'save','pdf','$moduletemplate','$forrecord','$modulei18n');".'">'.$htmlimg.'savepdf.png" '.$htmlstyle.$altpdf.$linkc;
		echo $linko."'save','pdf','$moduletemplate','$forrecord','$modulei18n');".'">'.getTranslatedString('Save PDF', 'evvtgendoc').$linkc.$htmltrtdc;
	}
	echo $htmltrtdo.$linko."'export','doc','$moduletemplate','$forrecord','$modulei18n');".'">'.$htmlimg.'genoo.png" '.$htmlstyle.$altdoc.$linkc;
	echo $linko."'export','doc','$moduletemplate','$forrecord','$modulei18n');".'">'.getTranslatedString('Export Doc', 'evvtgendoc').$linkc.$htmltrtdc;
	echo $htmltrtdo.$linko."'email','doc','$moduletemplate','$forrecord','$modulei18n');".'">'.$htmlimg.'emailoo.png" '.$htmlstyle.$altdoc.$linkc;
	echo $linko."'email','doc','$moduletemplate','$forrecord','$modulei18n');".'">'.getTranslatedString('EMail Doc', 'evvtgendoc').$linkc.$htmltrtdc;
	echo $htmltrtdo.$linko."'save','doc','$moduletemplate','$forrecord','$modulei18n');".'">'.$htmlimg.'saveoo.png" '.$htmlstyle.$altdoc.$linkc;
	echo $linko."'save','doc','$moduletemplate','$forrecord','$modulei18n');".'">'.getTranslatedString('Save Doc', 'evvtgendoc').$linkc.$htmltrtdc;
	?>
	</table>
	<iframe id="gendociframe" style="display:none" title="download document"></iframe>
	<?php
}
?>
