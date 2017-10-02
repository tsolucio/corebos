{****************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************
*}
<div id="vtigerHelpWelcomePopupLay" style="display:none;width:750px;box-shadow: 5px 8px 10px #777777;" class="vtigerHelpWelcomePopupLay">
	<table class="layerHeadingULine hdrTabBg" width="100%" cellpadding="5" cellspacing="0" width="100%" border="0" >
	<tr valign="top">
		<td class="genHeaderSmall">
			{'LBL_GETTING_STARTED'|@getTranslatedString:'Home'}
		</td>
		<td align="right">
			<a href="javascript:;" onclick='document.getElementById("vtigerHelpWelcomePopupLay").style.display="none";VtigerJS_DialogBox.unblock();'><img src="{'help_close_black.png'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" width="13px" heigth="13px" style="padding-right:2px;"></a>
		</td>
	</tr>
	</table>
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td colspan="2">
			<div id="vtigerHelpWelcomePopupContent" class="vtigerHelpPopupLay">
				<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}">
			</div>
		</td>
	</table>
	<div id="vtigerHelpWelcomeGTranslateEl"></div>
</div>

{literal}
<script type="text/javascript">
	jQuery( window ).on('load',function() {
	if (typeof firsttime_login_welcome == 'function') {
		firsttime_login_welcome(document.getElementById('vtigerHelpWelcomePopupLay'), document.getElementById('vtigerHelpWelcomePopupContent'));
	}
});
</script>
{/literal}
