<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
require_once('modules/Portal/Portal.php');
global $app_strings,$mod_strings,$adb,$theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
if(isset($_REQUEST['record']) && $_REQUEST['record'] !='')
{
	$portalid = vtlib_purify($_REQUEST['record']);
	$query = 'select * from vtiger_portal where portalid =?';
	$result=$adb->pquery($query, array($portalid));
	if ($result and $adb->num_rows($result)==1) {
		$portalname = $adb->query_result($result,0,'portalname');
		$portalurl = $adb->query_result($result,0,'portalurl');
		/* to remove http:// from portal url*/
		$portalurl = preg_replace("/http:\/\//i","",$portalurl);
	} else {
		$portalid = $portalname = $portalurl = '';
	}
} else {
	$portalid = $portalname = $portalurl = '';
}
$portal_inputs = '<div style="display:block;position:relative;" id="orgLay" class="layerPopup">
		<form onSubmit="OnUrlChange(); SaveSite(\''.$portalid.'\');return false;" >
		<table class="slds-table slds-no-row-hover layerHeadingULine" width="100%">
		<tr class="slds-text-title--header">
			<th scope="col" class="layerPopupHeading" align="left"><div class="slds-truncate moduleName">' .$mod_strings['LBL_ADD'] .' '.$mod_strings['LBL_BOOKMARK'].'</div></th>
			<th scope="col" style="padding: .5rem;text-align:right;"><a href="javascript:fninvsh(\'orgLay\');"><img src="'. vtiger_imageurl('close.gif', $theme) .'" align="absmiddle" border="0"></a></th>
		</tr>
		</table>
<table border="0" cellspacing="0" cellpadding="5" width="95%" align="center">
	<tr>
	<td class="small" >
		<table border="0" celspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
		<tr>
			<td class="dvtCellLabel" align="right" width="40%" ><b>'.$mod_strings['LBL_BOOKMARK'].' ' .$mod_strings['LBL_URL'] .' </b></td>
			<td class="dvtCellInfo" align="left" width="60%"><input name="portalurl" id="portalurl" class="txtBox slds-input" value="'.$portalurl.'" type="text" onkeyup="OnUrlChange();"></td>
		</tr>
		<tr>
			<td class="dvtCellLabel" align="right" width="40%"> <b>'.$mod_strings['LBL_BOOKMARK'].' ' .$mod_strings['LBL_NAME'] .' </b></td>
			<td class="dvtCellInfo" align="left" width="60%"><input name="portalname" id="portalname" value="'.$portalname.'" class="txtBox slds-input" type="text"></td>
		</tr>
		</table>
	</td>
	</tr>
</table>
<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport" style="background-color:#f7f9fb;">
	<tr>
	<td align="center" style="padding:5px;">
			<input name="save" value=" &nbsp;'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'&nbsp; " class="slds-button slds-button--small slds-button_success"  type="submit">&nbsp;&nbsp;
			<input name="cancel" value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh(\'orgLay\');" type="button">
	</td>
	</tr>
</table>
</form>
</div>';
echo $portal_inputs;
?>
