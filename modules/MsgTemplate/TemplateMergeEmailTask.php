<?php
/*************************************************************************************************
* Copyright 2012-2013 OpenCubed  --  This file is a part of vtMktDashboard.
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
*  Module       : Actions
*  Version      : 1.8
*  Author       : OpenCubed
*************************************************************************************************/
require_once 'include/utils/CommonUtils.php';
require_once 'data/CRMEntity.php';
global $default_charset;

if (isset($_REQUEST['action_id']) && $_REQUEST['action_id'] !='') {
	$ids = explode(':', $_REQUEST['listofids']);
	$crmid = null;
	if (count($ids) == 1) {
		$crmid = $ids[0];
	}
	$tpl = getTemplateDetails($_REQUEST['action_id'], $crmid);

	// Get Related Documents
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents');
	$query='select vtiger_notes.notesid,vtiger_notes.filename
		from vtiger_notes
		inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
		inner join '.$crmEntityTable.' on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
		where vtiger_senotesrel.crmid=?';
	$result = $adb->pquery($query, array($_REQUEST['action_id']));
}
?>
<form name="frmrepstr" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="subject" value="<?php echo $tpl[2];?>">
<textarea name="repstr" style="visibility:hidden">
<?php echo htmlentities($tpl[1], ENT_NOQUOTES, $default_charset); ?>
</textarea>
</form>
<script type="text/javascript">
if (window.opener.document.getElementById('save_subject') != null && window.opener.CKEDITOR.instances.save_content != 'undefined') {
	window.opener.document.getElementById('save_subject').value = window.document.frmrepstr.subject.value;
	try {
		window.opener.CKEDITOR.instances.save_content.insertHtml(window.document.frmrepstr.repstr.value);
	} catch(err) {
	}
<?php while ($row = $adb->getNextRow($result, false)) { ?>
	window.opener.addDocs(<?php echo $row['notesid']; ?>, '','Documents','ajax', '');
<?php } ?>
}
window.close();
</script>