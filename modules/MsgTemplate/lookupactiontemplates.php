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
require_once 'include/utils/utils.php';

global $theme,$current_user;
$theme = basename(vtlib_purify($theme));
$theme_path="themes/".$theme."/";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
  <title><?php echo $mod_strings['LBL_EMAIL_TEMPLATES_LIST']; ?></title>
  <link type="text/css" rel="stylesheet" href="<?php echo $theme_path; ?>/style.css"/>
</head>
<body>
	<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
		<div class="lvtHeaderText"><?php echo $mod_strings['LBL_EMAIL_TEMPLATES']; ?></div>
		<hr noshade="noshade" size="1">
		<input type="hidden" name="module" value="Users">
		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
		<tr>
		<th width="35%" class="lvtCol"><b><?php echo $mod_strings['LBL_TEMPLATE_NAME']; ?></b></th>
		<th width="65%" class="lvtCol"><b><?php echo $mod_strings['LBL_DESCRIPTION']; ?></b></td>
		</tr>
<?php
$actions = CRMEntity::getInstance('MsgTemplate');
$secWhere = $actions->getListViewSecurityParameter('MsgTemplate');
$sql = "select *
from vtiger_msgtemplate
join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_msgtemplate.msgtemplateid and vtiger_crmentity.deleted=0
LEFT JOIN vtiger_groups
ON vtiger_groups.groupid = vtiger_crmentity.smownerid
LEFT JOIN vtiger_users
ON vtiger_crmentity.smownerid = vtiger_users.id
where actions_status='Active' {$secWhere}
order by msgtemplateid desc";
$result = $adb->query($sql);
$temprow = $adb->fetch_array($result);

$cnt=1;

require_once 'include/utils/UserInfoUtil.php';
require 'user_privileges/user_privileges_'.$current_user->id.'.php';
do {
	$templatename = $temprow["reference"];
	if ($is_admin == false) {
		$folderName = $temprow['foldername'];
		if ($folderName != 'Personal') {
			printf("<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'> <td height='25'>");
			echo "<a href='javascript:submittemplate(".$temprow['msgtemplateid'].");'>".$temprow["reference"]."</a></td>";
			printf("<td height='25'>%s</td>", $temprow["description"]);
		}
	} else {
		printf("<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'> <td height='25'>");
		echo "<a href='javascript:submittemplate(".$temprow['msgtemplateid'].");'>".$temprow["reference"]."</a></td>";
		printf("<td height='25'>%s</td>", $temprow["description"]);
	}
	$cnt++;
} while ($temprow = $adb->fetch_array($result));
?>
</table>
</body>
<script>
function submittemplate(actionId) {
	window.document.location.href = 'index.php?module=MsgTemplate&action=MsgTemplateAjax&file=TemplateMerge&idlist=<?php echo $_REQUEST['idlist']; ?>&action_id='+actionId;
}
</script>
</html>
