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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script type="text/javascript" src="include/js/picklist.js"></script>
<script src="modules/Home/Homestuff.js" type="text/javascript"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
		{include file='SetMenu.tpl'}
		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		<tr>
			<td width=50 rowspan=2 valign=top><img src="{'picklist.gif'|@vtiger_imageurl:$THEME}" width="48" height="48" border=0 ></td>
			<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_PICKLIST_EDITOR}</b></td>
		</tr>
		<tr>
			<td valign=top class="small">{$MOD.LBL_PICKLIST_DESCRIPTION}</td>
		</tr>
		</table>

		<table border=0 cellspacing=0 cellpadding=10 width=100% >
		<tr>
			<td valign=top>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr>
				<td class="small" width="20%" nowrap>
					<strong>{$MOD.LBL_SELECT_MODULE}</strong>&nbsp;&nbsp;
				</td>
				<td class="small" align="left" width="30%">
					<select name="pickmodule" id="pickmodule" class="detailedViewTextBox" onChange="changeModule();">
					{html_options options=$MODULE_LISTS selected=$MODULE sortoptions=asc}
					</select>
				</td>
				<td class="small" align="right">&nbsp;</td>
			</tr>
			</table>

			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr>
				<td class="big" rowspan="2">
				<div id="picklist_datas">	
					{include file='modules/PickList/PickListContents.tpl'}
				</div>
				</td>
			</tr>
			</table>

			<table border=0 cellspacing=0 cellpadding=5 width=100% >
			<tr>
				<td class="small" nowrap align=right>
					<a href="#top">
						{$MOD.LBL_SCROLL}
					</a>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
</tbody>
</table>

<div id="actiondiv" style="display:block;position:absolute;"></div>
<div id="editdiv" style="display:block;position:absolute;width:510px;"></div>
