<script language="JavaScript" type="text/javascript" src="modules/Tooltip/TooltipSettings.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td valign="top" width="100%">
	<div align=center>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
			<input type="hidden" id="fieldid" name="fieldid" value="{$FIELDID}">
			<input type="hidden" name="" value="">
		</table>
		
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
		<tr>
			<td class="small" align="left" nowrap>
				<strong>
					{$MOD.LBL_TOOLTIP_HELP_TEXT}
				</strong>
			</td>
			<td class="small" align="right" width="100%">
				<input title="save" class="crmButton small save" type="button" name="save" onClick="doSaveTooltipInfo();" value="{$APP.LBL_SAVE_BUTTON_LABEL}">
			</td>
			<td class="small" align="right">
				<input title="back" class="crmButton small cancel" type="button" name="Back" onClick="window.history.back();" value="{$APP.LBL_BACK}">
			</td>
		</tr>
		</table>
		
		{foreach key=module item=info from=$FIELD_LISTS}
			<div id="{$module}_fields" style="display:block">	
		 	<table cellspacing=0 cellpadding=5 width=100% class="listTable small">
				<tr>
	        	<td valign=top width="25%" >
			     	<table border=0 cellspacing=0 cellpadding=5 width=100% class=small>
					{foreach item=elements name=groupfields from=$info}
                    	<tr>
						{foreach item=elementinfo name=curvalue from=$elements}
                       		<td class="prvPrfTexture" style="width:20px">
                       			&nbsp;
                       		</td>
                       		<td width="5%" id="{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}">
                       			{$elementinfo.input}
                       		</td>
                       		<td width="25%" nowrap onMouseOver="this.className='prvPrfHoverOn',$('{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOn'" onMouseOut="this.className='prvPrfHoverOff',$('{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOff'">
                       			{$elementinfo.fieldlabel}
                       		</td>
						{/foreach}
                     	</tr>
	             	{/foreach}
	             	</table>
				</td>
		        </tr>
	        </table>
			</div>
		{/foreach}
		</form>
		</div>
	</td>
	<td valign="top">
		<img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}">
	</td>
	</tr>
</tbody>
</table>
