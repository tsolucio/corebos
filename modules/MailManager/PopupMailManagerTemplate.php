<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'data/CRMEntity.php';
$crmEntityTable = CRMEntity::getcrmEntityTableAlias('MsgTemplate');
global $theme,$current_user,$mod_strings;
$theme = vtlib_purify($theme);
$theme_path='themes/'.$theme.'/';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
 <title><?php echo $mod_strings['LBL_EMAIL_TEMPLATES_LIST']; ?></title>
 <link type="text/css" rel="stylesheet" href="<?php echo $theme_path ?>/style.css"/>
</head>
<body>
	<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
		<div class="lvtHeaderText"><?php echo getTranslatedString('LBL_EMAIL_TEMPLATES', 'Emails'); ?></div>
		<hr noshade="noshade" size="1">
		<input type="hidden" name="module" value="Users">
		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
		<tr>
		<th width="35%" class="lvtCol"><b><?php echo getTranslatedString('LBL_TEMPLATE_NAME', 'Emails'); ?></b></th>
		<th width="65%" class="lvtCol"><b><?php echo getTranslatedString('LBL_DESCRIPTION', 'Emails'); ?></b></th>
		</tr>
<?php
$result = $adb->pquery(
	'select * from vtiger_msgtemplate
		inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=msgtemplateid
		where vtiger_crmentity.deleted=0 order by reference',
	array()
);
$temprow = $adb->fetch_array($result);
$subject_html_id = (isset($_REQUEST['subject_id']) ? vtlib_purify($_REQUEST['subject_id']) : '');
$body_html_id    = (isset($_REQUEST['body_id']) ? vtlib_purify($_REQUEST['body_id']) : '');
$cnt=1;

require_once 'include/utils/UserInfoUtil.php';
$is_admin = is_admin($current_user);
do {
	$templatename = $temprow['reference'];
	printf("<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'><td height='25'>");
	echo "<a href=javascript:submitMailMergetemplate(".$temprow['msgtemplateid'].','."'$subject_html_id'".','."'$body_html_id');>";
	echo vtlib_purify($temprow['reference']).'</a></td>';
	printf("<td height='25'>%s</td>", vtlib_purify($temprow['description']));
	$cnt++;
} while ($temprow = $adb->fetch_array($result));
?>
</table>
</body>
</html>
