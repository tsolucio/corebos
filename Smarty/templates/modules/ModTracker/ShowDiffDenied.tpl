{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
 
<div id="orgLay" class="layerPopup">

<table class="layerHeadingULine" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
	<td class="layerPopupHeading" align="left" nowrap="nowrap" width="70%">
		{'LBL_ACCESS_RESTRICTED'|@getTranslatedString:$MODULE}
	</td>
	<td align="right" width="2%">
		<a href='javascript:void(0);'><img src="{'close.gif'|@vtiger_imageurl:$THEME}" onclick="ModTrackerCommon.hide();" align="right" border="0"></a>
	</td>
</tr>
</table>

<table border=0 cellspacing=1 cellpadding=0 width=100% class="lvtBg">
<tr>
	<td>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tr class='lvtColData'>
			<td rowspan='2' width='11%'><img src="{'denied.gif'|@vtiger_imageurl:$THEME}" border=0></td>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
				<span class='genHeaderSmall'>{'LBL_NOT_PERMITTED_TO_ACCESS_INFORMATION'|@getTranslatedString:$MODULE}</span>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>		
</div>
