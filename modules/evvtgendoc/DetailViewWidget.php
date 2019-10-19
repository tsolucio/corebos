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
global $adb,$current_user,$mod_strings;
$moduletemplate = vtlib_purify($_REQUEST['formodule']);
$modulei18n = getTranslatedString('SINGLE_'.$moduletemplate, $moduletemplate);
$forrecord = vtlib_purify($_REQUEST['forrecord']);

$templates=$adb->pquery(
	'SELECT notesid,title
		FROM vtiger_notes
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
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
	<table>
<?php
if ($gendoc_active) {
?>
<tr class="actionlink">
	<td align="left" style="padding-left:10px;">
		<a class="webMnu" href="javascript:gendocAction('export','pdf','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><img src="modules/evvtgendoc/images/genpdf.gif" hspace="5" align="absmiddle" border="0"/></a>
		<a class="webMnu" href="javascript:gendocAction('export','pdf','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><?php echo getTranslatedString('Export PDF', 'evvtgendoc'); ?></a>
	</td>
</tr>
<tr class="actionlink">
	<td align="left" style="padding-left:10px;">
		<a class="webMnu" href="javascript:gendocAction('email','pdf','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><img src="modules/evvtgendoc/images/emailpdf.gif" hspace="5" align="absmiddle" border="0"/></a>
		<a class="webMnu" href="javascript:gendocAction('email','pdf','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><?php echo getTranslatedString('EMail PDF', 'evvtgendoc'); ?></a>
	</td>
</tr>
<tr class="actionlink">
	<td align="left" style="padding-left:10px;">
		<a class="webMnu" href="javascript:gendocAction('save','pdf','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><img src="modules/evvtgendoc/images/savepdf.png" hspace="5" align="absmiddle" border="0"/></a>
		<a class="webMnu" href="javascript:gendocAction('save','pdf','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><?php echo getTranslatedString('Save PDF', 'evvtgendoc'); ?></a>
	</td>
</tr>
<?php
}
?>
	<tr class="actionlink">
		<td align="left" style="padding-left:10px;">
			<a class="webMnu" href="javascript:gendocAction('export','doc','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><img src="modules/evvtgendoc/images/genoo.png" hspace="5" align="absmiddle" border="0"/></a>
			<a class="webMnu" href="javascript:gendocAction('export','doc','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><?php echo getTranslatedString('Export Doc', 'evvtgendoc'); ?></a>
		</td>
	</tr>
	<tr class="actionlink">
		<td align="left" style="padding-left:10px;">
			<a class="webMnu" href="javascript:gendocAction('email','doc','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><img src="modules/evvtgendoc/images/emailoo.png" hspace="5" align="absmiddle" border="0"/></a>
			<a class="webMnu" href="javascript:gendocAction('email','doc','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><?php echo getTranslatedString('EMail Doc', 'evvtgendoc'); ?></a>
		</td>
	</tr>
	<tr class="actionlink">
		<td align="left" style="padding-left:10px;">
			<a class="webMnu" href="javascript:gendocAction('save','doc','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><img src="modules/evvtgendoc/images/saveoo.png" hspace="5" align="absmiddle" border="0"/></a>
			<a class="webMnu" href="javascript:gendocAction('save','doc','<?php echo $moduletemplate; ?>','<?php echo $forrecord; ?>','<?php echo $modulei18n; ?>');"><?php echo getTranslatedString('Save Doc', 'evvtgendoc'); ?></a>
		</td>
	</tr>
	</table>
	<iframe id="gendociframe" style="display:none"></iframe>
<?php
}
?>
