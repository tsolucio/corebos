{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
	<a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td>
			{include file='Buttons_List.tpl'}
			<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
				<tr>
					<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
					<td class="showPanelBg" valign=top width=100%>
						<div class="small" style="padding:10px" >
						<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
							<tr>
								<td valign=top align=left >
								{include file='modules/Calendar4You/EventEditViewContent.tpl'}
								</td>
							</tr>
						</table>
						</div>
					</td>
					<td align=right valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
				</tr>
			</table>
</td></tr></table>
