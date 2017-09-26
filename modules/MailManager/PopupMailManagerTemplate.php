<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/utils.php');

global $theme,$current_user,$mod_strings;
$theme = vtlib_purify($theme);
$theme_path="themes/".$theme."/";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
	<head>
		<title><?php echo $mod_strings['LBL_EMAIL_TEMPLATES_LIST']; ?></title>
		<link type="text/css" rel="stylesheet" href="<?php echo $theme_path ?>style.css"/>
		<link type="text/css" rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css"/>
		<link type="text/css" rel="stylesheet" href="include/LD/assets/styles/customLD.css"/>
	</head>
	<body>
		<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
			<table class="slds-table slds-no-row-hover">
				<tr class="slds-text-title--header">
					<th scope="col">
						<div class="slds-truncate moduleName lvtHeaderText">
							<?php echo getTranslatedString('LBL_EMAIL_TEMPLATES','Emails'); ?>
						</div>
					</th>
				</tr>
			</table>
			<input type="hidden" name="module" value="Users">
			<table class="slds-table slds-table--bordered slds-table--fixed-layout ld-font">
				<thead>
					<tr>
						<th class="slds-text-title--caps" scope="col">
							<b><?php echo getTranslatedString('LBL_TEMPLATE_NAME','Emails'); ?></b>
						</th>
						<th class="slds-text-title--caps" scope="col">
							<b><?php echo getTranslatedString('LBL_DESCRIPTION','Emails'); ?></b>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$sql = "select * from vtiger_emailtemplates order by templateid desc";
				$result = $adb->pquery($sql, array());
				$temprow = $adb->fetch_array($result);
				$subject_html_id = (isset($_REQUEST['subject_id']) ? vtlib_purify($_REQUEST['subject_id']) : '');
				$body_html_id    = (isset($_REQUEST['body_id']) ? vtlib_purify($_REQUEST['body_id']) : '');
				$cnt=1;

				require_once('include/utils/UserInfoUtil.php');
				$is_admin = is_admin($current_user);
				do
				{
					$templatename = $temprow["templatename"];
					if($is_admin == false)
					{
						$folderName = $temprow['foldername'];
						if($folderName != 'Personal')
						{
							printf("<tr class='slds-hint-parent slds-line-height--reset'> <th scope='cell' class='slds-text-align--left'>");
							echo "<a href=javascript:submitMailMergetemplate(".$temprow['templateid'].','."'$subject_html_id'".','."'$body_html_id'".");>".vtlib_purify($temprow['templatename'])."</a></th>";
							printf("<th scope='cell' class='slds-text-align--left'>%s</th>",vtlib_purify($temprow['description']));
						}
					}
					else
					{
						printf("<tr class='slds-hint-parent slds-line-height--reset'> <th scope='cell' class='slds-text-align--left'>");
				//		echo "<a href='javascript:submitMailMergetemplate(".$temprow['templateid'].',"'.$subject_html_id.'","'.$body_html_id.'"'.");>'".$temprow["templatename"]."</a></td>";
						//echo "<a href='javascript:submitMailMergetemplate(".$temprow['templateid'].','.$subject_html_id.','.$body_html_id.');>'.$temprow["templatename"]."</a></td>";
						echo "<a href='javascript:submitMailMergetemplate(".$temprow['templateid'].");'>".vtlib_purify($temprow['templatename'])."</a></th>";
						printf("<th scope='cell' class='slds-text-align--left'>%s</th>",vtlib_purify($temprow['description']));
					}
					$cnt++;

				}while($temprow = $adb->fetch_array($result));
				?>
				</tbody>
			</table>
	</body>
	<script>
	function submitMailMergetemplate(templateid)
	{
		var sub = '_mail_replyfrm_subject_';
		var tbody = '_mail_replyfrm_body_';
		var url = "index.php?module=MailManager&action=MailManagerAjax&file=TemplateMergeMailManager&templateid="+templateid+'&subject='+sub+'&textbody='+tbody;
		window.document.location.href = url;
	}
	</script>
</html>
