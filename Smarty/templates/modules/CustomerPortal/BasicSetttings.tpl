{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<style type="text/css">
	{literal}
td.showPanelBg div.small table.lvtBg tbody tr td table.small {
	border-bottom: 1px solid #ccc;
}
{/literal}
</style>

{include file='Buttons_List.tpl'}

<form  method="post" name="new" id="form">
	<input type="hidden" name="module" value="CustomerPortal">
	<input type="hidden" name="action" value="ListView">
	<input type="hidden" name="return_action" value="ListView">
	<input type="hidden" name="mode" value="save">
	<table border=0 cellspacing=0 cellpadding=0 width="100%">
    <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
			<td class="showPanelBg" valign="top" width="100%" style="padding:05px;">
				<div class="small" style="padding:30px;width:100%;position:relative;">
					<table border=0 cellspacing=1 cellpadding=0 width="100%" class="lvtBg" >
			<tr>
				<td>
								<table border=0 cellspacing=0 cellpadding=0 width="95%" class="small" >
					<!-- Tab Links -->
						<tr>
										<td class="dvtSelectedCell" nowrap>{'LBL_CUSTOMERPORTAL_SETTINGS'|@getTranslatedString:$MODULE}</td>
							<td class="dvtTabCache" width="100%">&nbsp;</td>
						</tr>
					<!-- Acutal Contents -->				
						<tr>
										<td colspan="2">
											<table border=0 cellspacing=0 cellpadding=10 width="100%" align="center" class="dvtContentSpace" style='border-bottom: 0'>
												<tr>
													<td align="left">
								<div id='portallist'>
								{include file="modules/CustomerPortal/BasicSetttingsContents.tpl"}
								</div>
							</td>
						</tr>
						</table>
										</td>
						</tr>
						</table>
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	</table>
</form>
