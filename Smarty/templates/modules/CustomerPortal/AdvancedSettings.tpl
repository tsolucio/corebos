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

{include file='Buttons_List.tpl'}

<form  method="post" name="new" id="form">
<input type="hidden" name="module" value="CustomerPortal">
<input type="hidden" name="action" value="AdvancedSettings">
<input type="hidden" name="return_action" value="AdvancedSettings">
<input type="hidden" name="mode" value="save">

<table border=0 cellspacing=0 cellpadding=0 width="98%" align=center>
    <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
		<td class="showPanelBg" valign="top" width="100%" style="padding:10px;">
		<div class="small" style="width:100%;position:relative;">
			<table border=0 cellspacing=1 cellpadding=0 width="100%" class="lvtBg">
			<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=2 width="100%" class="small"> 
					<tr>
						<td style="padding-right:20px" nowrap align=right></td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=0 width="95%" class="small">
					<!-- Tab Links -->
					<tr><td>
						<table border=0 cellspacing=0 cellpadding=3 width="100%" class="small">
						<tr>
							<td class="dvtTabCache" style="width:10px" nowrap></td>
							<td class="dvtUnSelectedCell" align="left" nowrap><a href="index.php?module=CustomerPortal&action=ListView&parenttab={$CATEGORY}">{$MOD.LBL_BASIC_SETTINGS}</a></td>
							<td class="dvtTabCache" style="width:10px"></td>
							<td class="dvtSelectedCell" align="left" nowrap>{$MOD.LBL_ADVANCED_SETTINGS}</a></td>
							<td class="dvtTabCache" width="100%">&nbsp;</td>
						</tr>
						</table>
					</td></tr>
					
					<!-- Acutal Contents -->				
					<tr><td>
						<table border=0 cellspacing=0 cellpadding=10 width="100%" class="dvtContentSpace" style='border-bottom: 0'>
						<tr>
							<td>
								<div>
								{include file="modules/CustomerPortal/AdvancedSettingsContents.tpl"}
								</div>
							</td>
						</tr>
						</table>
					</td></tr>
					
					<!-- Tab Links -->
					<tr><td>
						<table border=0 cellspacing=0 cellpadding=3 width="100%" class="small">
						<tr>
							<td class="dvtTabCacheBottom" style="width:10px" nowrap></td>
							<td class="dvtUnSelectedCell" align="left" nowrap><a href="index.php?module=CustomerPortal&action=ListView&parenttab={$CATEGORY}">{$MOD.LBL_BASIC_SETTINGS}</a></td>
							<td class="dvtTabCacheBottom" style="width:10px"></td>
							<td class="dvtSelectedCellBottom" align="left" nowrap>{$MOD.LBL_ADVANCED_SETTINGS}</a></td>
							<td class="dvtTabCacheBottom" width="100%">&nbsp;</td>
						</tr>
						</table>
					</td></tr>
					
					</table>
										
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
</table>
</form>