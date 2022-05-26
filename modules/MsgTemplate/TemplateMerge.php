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
require_once 'include/utils/Request.php';
require_once 'data/CRMEntity.php';
global $default_charset, $log;

if (isset($_REQUEST['action_id']) && $_REQUEST['action_id'] !='') {
	$ids = explode(':', $_REQUEST['listofids']);
	$crmid = null;
	if (count($ids) == 1) {
		$crmid = $ids[0];
	}
	$tpl = getTemplateDetails($_REQUEST['action_id'], $crmid);

	// Merge template
	$mids = array();
	if (isset($_REQUEST['merge_template_with']) && $_REQUEST['merge_template_with'] != '') {
		$mids = explode(',', $_REQUEST['merge_template_with']);
	}
	if (count($mids) > 0) {
		foreach ($mids as $mid) {
			$module = getSalesEntityType($mid);
			$tpl[2] = getMergedDescription($tpl[2], $mid, $module);
			$tpl[1] = getMergedDescription($tpl[1], $mid, $module);
		}
	}

	// Get Related Documents
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents');
	$query='select vtiger_notes.notesid,vtiger_notes.filename
		from vtiger_notes
		inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
		inner join '.$crmEntityTable.' on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
		where vtiger_senotesrel.crmid=?';
	$result = $adb->pquery($query, array($_REQUEST['action_id']));
}
$req = new Vtiger_Request();
$req->set('action_id', isset($_REQUEST['action_id']) ? $_REQUEST['action_id'] : '');
$req->set('callvalue', isset($_REQUEST['callvalue']) ? $_REQUEST['callvalue'] : '');
$req->set('targetfield', isset($_REQUEST['targetfield']) ? $_REQUEST['targetfield'] : '');
$req->set('callfrom', isset($_REQUEST['callfrom']) ? $_REQUEST['callfrom'] : '');
?>
<script type="text/javascript" src="include/js/vtlib.js"></script>
<form name="frmrepstr" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="subject" value="<?php echo $tpl[2];?>">
<textarea name="repstr" style="visibility:hidden">
<?php echo htmlentities($tpl[1], ENT_NOQUOTES, $default_charset); ?>
</textarea>
</form>
<script type="text/javascript">
if (typeof window.opener.document.getElementById('subject') != 'undefined' && window.opener.document.getElementById('subject') != null) {
	window.opener.document.getElementById('subject').value = window.document.frmrepstr.subject.value;
	window.opener.document.getElementById('description').value = window.document.frmrepstr.repstr.value;
	window.opener.oCKeditor.setData(window.document.frmrepstr.repstr.value);
<?php while ($row = $adb->getNextRow($result, false)) { ?>
	var attachment = '<?php echo $row['filename']; ?>';
	window.opener.addOption(<?php echo $row['notesid']; ?>, attachment);
<?php } ?>
} else {
	vtlib_setvalue_from_popup('<?php echo $req->get('action_id'); ?>', '<?php echo $req->get('callvalue') ; ?>', '<?php echo $req->get('targetfield'); ?>', '<?php echo $req->get('callform'); ?>');
}
window.close();
</script>
