<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
global $adb,$mod_strings;
$moduletemplate = vtlib_purify($_REQUEST['moduletemplate']);
require_once 'data/CRMEntity.php';
$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents');
$templates=$adb->pquery(
	'SELECT notesid,title
		FROM vtiger_notes
		INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_notes.notesid
		WHERE vtiger_crmentity.deleted = 0 and template=1 and template_for=? order by title',
	array($moduletemplate)
);
$tpls = array();
while ($t = $adb->fetch_array($templates)) {
	$tpls[$t['notesid']] = $t['title'];
}
asort($tpls);
$number=$adb->num_rows($templates);
if ($number>0) {
	$output="<select class='small' size=8 style='width:300px;' id='gendoctemplate' name='gendoctemplate'>";
}
foreach ($tpls as $nid => $tname) {
	$output.="<option value='".$nid."'>".$tname.'</option>';
}
if (isPermitted($moduletemplate, 'Merge')!='yes') {
	$output=getTranslatedString('LBL_PERMISSION');
} elseif ($number==0) {
	$output=$mod_strings['LBL_NOTEMPLATE'];
} else {
	$output.='</select><br/><br/>';
}
echo $output;
?>
