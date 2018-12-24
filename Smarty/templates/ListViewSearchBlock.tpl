{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{if isset($LVCSearchActive) && $LVCSearchActive!=0}
<tr bgcolor='white' name='customAdvanceSearch' id='customAdvanceSearch'>
	<td>&nbsp;
		<a onclick="clearAllField()">
			<img src="{'no.gif'|@vtiger_imageurl:$THEME}" />
		</a>
		<select name='fcolcolumnIndex' id='fcolcolumnIndex' style="display:none" >{$COLUMNS_BLOCK}</select>
		<input type="hidden" value="{$TKS_LIST_SEARCH|@count}" name="noofsearchfields" id="noofsearchfields" />
	</td>
	{assign var=val value=0}
	{foreach item=arr from=$TKS_LIST_SEARCH}
	{if $arr.fieldtype eq 'date' || $arr.fieldtype eq 'datetime' || $arr.fieldtype eq 'owner' || $arr.fieldtype eq 'select' || $arr.fieldtype eq 'checkbox'}
	<td width="11%">
	{else}
	<td>
	{/if}
		<input type="hidden" name="fname_{$val}" value="{$arr.fieldname}" />
		<input type="hidden" name="fvalue_{$val}" value="{$arr.value}" />
		<input type="hidden" name="customval_{$val}" value="" />
		<input type="hidden" name="type_{$val}" value="{$arr.fieldtype}" />
		{if $arr.fieldtype eq 'checkbox'}
		<table>
			<tr>
				<td width="80%">
				<select name="tks_{$arr.fieldname}" id="tks_{$arr.fieldname}" class="small" multiple="multiple">
					<option value="1">{'LBL_YES'|@getTranslatedString}</option>
					<option value="0">{'LBL_NO'|@getTranslatedString}</option>
				</select>
				</td>
				<td align="right">
					<a onclick="clearSelect('tks_{$arr.fieldname}');">
						<img src="{'no.gif'|@vtiger_imageurl:$THEME}" height="13" width="12" />
					</a>
				</td>
			</tr>
		</table>
		{elseif $arr.fieldtype eq 'owner'}
		<table>
			<tr>
				<td width="80%">
					<select name="tks_{$arr.fieldname}" id="tks_{$arr.fieldname}" multiple="multiple">
						<optgroup id='Users' label='Users'>
						{foreach from=$arr.pickdata.users key=k item=v}
							<option value ='{$v}'>{$v}</option>
						{/foreach}
						</optgroup>
						<optgroup id='Groups' label='Groups'>
						{foreach from=$arr.pickdata.group key=k item=v}
							<option value ='{$v}'>{$v}</option>
						{/foreach}
						</optgroup>
					</select>
				</td>
				<td align="right">
					<a onclick="clearSelect('tks_{$arr.fieldname}');">
						<img src="{'no.gif'|@vtiger_imageurl:$THEME}" height="13" width="12" />
					</a>
				</td>
			</tr>
		</table>
		{elseif $arr.fieldtype eq 'select'}
		<table>
			<tr>
				<td width="80%">
					 <select name="tks_{$arr.fieldname}" id="tks_{$arr.fieldname}" class="small" multiple="multiple">
						{foreach from=$arr.pickdata key=k item=v}
							<option value ='{$k}'>{$v}</option>
						{/foreach}
					</select>
				</td>
				<td align="right">
					<a onclick="clearSelect('tks_{$arr.fieldname}');">
						<img src="{'no.gif'|@vtiger_imageurl:$THEME}" height="13" width="12" />
					</a>
				</td>
			</tr>
		</table>
		{elseif $arr.fieldtype eq 'date' || $arr.fieldtype eq 'datetime'}
		<table>
			<tr>
				<td>
					<input name="tks_{$arr.fieldname}_date1" id="jscal_field_{$arr.fieldname}_date1"
						type="text" style="border:1px solid #bababa;" size="8" maxlength="10" value=""
						onchange="copyDate( 'jscal_field_{$arr.fieldname}_date1', 'jscal_field_{$arr.fieldname}_date2')" onkeypress="return disableEnterKey(event,'{$MODULE}')" />
					<img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$arr.fieldname}_date1">
					<script type="text/javascript" id='massedit_calendar_{$arr.fieldname}_date1'>
						{if $arr.fieldtype eq 'datetime'}
						var dt = true;
						var timeformat = ' %H:%M:%S';
						{else}
						var dt = false;
						timeformat = '';
						{/if}
						Calendar.setup ({ldelim}
							inputField : "jscal_field_{$arr.fieldname}_date1", ifFormat : "{''|parse_calendardate}"+timeformat,
							showsTime : dt, button : "jscal_trigger_{$arr.fieldname}_date1", singleClick : true, step : 1
						{rdelim})
					</script>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<input name="tks_{$arr.fieldname}_date2" id="jscal_field_{$arr.fieldname}_date2"
						type="text" style="border:1px solid #bababa;" size="8" maxlength="10" value="" onkeypress="return disableEnterKey(event,'{$MODULE}')" />
					<img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$arr.fieldname}_date2">
					<script type="text/javascript" id='massedit_calendar_{$arr.fieldname}_date2'>
						{if $arr.fieldtype eq 'datetime'}
						var dt = true;
						var timeformat = ' %H:%M:%S';
						{else}
						var dt = false;
						timeformat = '';
						{/if}
						Calendar.setup ({ldelim}
							inputField : "jscal_field_{$arr.fieldname}_date2", ifFormat : "{''|parse_calendardate}"+timeformat,
							showsTime : dt, button : "jscal_trigger_{$arr.fieldname}_date2", singleClick : true, step : 1
						{rdelim})
					</script>
				</td>
			</tr>
		</table>
		{else}
		<table>
			<tr>
				<td width="70%">
					<input type="text" id="tks_{$arr.fieldname}" name="tks_{$arr.fieldname}" value="" width:"100%" onkeypress="return disableEnterKey(event,'{$MODULE}')" />
				</td>
				<td align="right">
					<a onblur="disableDiv('div_{$arr.fieldname}')" onclick="enableDiv('div_{$arr.fieldname}')">
						<img src="{'arrow.png'|@vtiger_imageurl:$THEME}" height="13" width="12" />
					</a>
					<a onclick="clearField('tks_{$arr.fieldname}');disableDiv('div_{$arr.fieldname}')">
						<img src="{'no.gif'|@vtiger_imageurl:$THEME}" height="13" width="12" />
					</a>
				</td>
			</tr>
		</table>
		<div id="div_{$arr.fieldname}" style="z-index:12;width:200px;position:absolute;display:none;" class="layerPopup" name ='layerPopup'>
			<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerHeadingULine">
				<tbody>
					<tr>
						<td width="90%" align="left" class="genHeaderSmall">{'Select Condition'|@getTranslatedString} &nbsp;</td>
						<td width="10%" align="right">
							<a onclick="disableDiv('div_{$arr.fieldname}')">
								<img title="{'LBL_CLOSE'|@getTranslatedString}" alt="{'LBL_CLOSE'|@getTranslatedString}" src="themes/images/close.gif" border="0" align="absmiddle">
							</a>
						</td>
					</tr>
				</tbody>
			</table>
			<table border="0" cellspacing="0" cellpadding="5" width="95%" align="center">
				<tbody>
					<tr>
						<td class="small">
							<select name="op_cond_{$val}" id="op_cond_{$val}" class="repBox" style="width:100px;" onblur="document.getElementById('div_{$arr.fieldname}').style.display='none'">
								{if $arr.fieldtype eq 'text'}
								<option value="e">{'equals'|@getTranslatedString:'CustomView'}</option>
								<option value="n">{'not equal to'|@getTranslatedString:'CustomView'}</option>
								<option value="s">{'starts with'|@getTranslatedString:'CustomView'}</option>
								<option value="ew">{'ends with'|@getTranslatedString:'CustomView'}</option>
								<option value="c" selected="selected">{'contains'|@getTranslatedString:'CustomView'}</option>
								<option value="k">{'does not contain'|@getTranslatedString:'CustomView'}</option>
								{else}
								<option value="e" selected="selected">{'equals'|@getTranslatedString:'CustomView'}</option>
								<option value="n">{'not equal to'|@getTranslatedString:'CustomView'}</option>
								<option value="l">{'less than'|@getTranslatedString:'CustomView'}</option>
								<option value="g">{'greater than'|@getTranslatedString:'CustomView'}</option>
								<option value="m">{'less or equal'|@getTranslatedString:'CustomView'}</option>
								<option value="h">{'greater or equal'|@getTranslatedString:'CustomView'}</option>
								{/if}
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		{/if}
	</td>
	{assign var=val value=$val+1}
	{/foreach}
	<td>
		<input type="button" id="tks_searchbutton" name="tks_searchbutton" value="{'LBL_Search'|@getTranslatedString:'MailManager'}" onclick="activateCustomSearch('{$MODULE}')" class="crmbutton small create" />
	</td>
</tr>
{/if}