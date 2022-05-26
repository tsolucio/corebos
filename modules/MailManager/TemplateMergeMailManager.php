<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/CommonUtils.php';
require_once 'data/CRMEntity.php';
global $default_charset;

if (isset($_REQUEST['templateid']) && $_REQUEST['templateid'] !='') {
	$templatedetails = getTemplateDetails($_REQUEST['templateid']);
	$crmid = null;
	$tpl = getTemplateDetails($_REQUEST['templateid'], $crmid);
	// Get Related Documents
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents');
	$query='select vtiger_notes.notesid,vtiger_notes.filename
		from vtiger_notes
		inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
		inner join '.$crmEntityTable.' on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
		where vtiger_senotesrel.crmid=?';
	$result = $adb->pquery($query, array($_REQUEST['templateid']));
}
?>
<form name="frmrepstr" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="subject" value="<?php echo (isset($templatedetails[2]) ? vtlib_purify($templatedetails[2]) : '');?>">
<textarea name="repstr" style="visibility:hidden">
<?php echo (isset($templatedetails[1]) ? htmlentities($templatedetails[1], ENT_NOQUOTES, $default_charset) : ''); ?>
</textarea>
</form>
<script type="text/javascript">
if (typeof window.opener.document.getElementById('_mail_replyfrm_subject_') != 'undefined' && window.opener.document.getElementById('_mail_replyfrm_subject_') != null) {
	window.opener.document.getElementById('_mail_replyfrm_subject_').value = window.document.frmrepstr.subject.value;
	window.opener.document.getElementById('_mail_replyfrm_body_').value = window.document.frmrepstr.repstr.value;
	window.opener.MailManager.mail_reply_rteinit(window.document.frmrepstr.repstr.value);
<?php
while ($row = $adb->getNextRow($result, false)) {
	?>
	window.opener.addAttachments(<?php echo $row['notesid']; ?>, '','Documents','ajax', '');
	<?php
}
?>
	window.close();
}
</script>