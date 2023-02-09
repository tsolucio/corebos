<!--*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="modules/CustomView/CustomView.js"></script>
<script type="text/javascript" src="include/calculator/calc.js"></script>
{literal}
<form enctype="multipart/form-data" name="CustomView" method="POST" action="index.php" onsubmit="if(mandatoryCheck()){VtigerJS_DialogBox.block();} else{ return false; }">
{/literal}
<input type="hidden" name="module" value="CustomView">
<input type="hidden" name="action" value="Save">
<input type="hidden" name="cvmodule" value="{$CVMODULE}">
<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
<input type="hidden" name="record" value="{$CUSTOMVIEWID}">
<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
<input type="hidden" id="user_dateformat" name="user_dateformat" value="{$DATEFORMAT}">
<input type="hidden" name="permit_all" value="{$PERMITALL}" />
<script type="text/javascript">
var Application_FilterValidateMandatoryFields = 1;
GlobalVariable_getVariable('Application_FilterValidateMandatoryFields', 1, (typeof gVTModule=='undefined' ? '' : gVTModule), '').then(function (response) {
	var obj = JSON.parse(response);
	Application_FilterValidateMandatoryFields = obj.Application_FilterValidateMandatoryFields;
}, function (error) {
	Application_FilterValidateMandatoryFields = 1;
});
function mandatoryCheck() {
	if (Application_FilterValidateMandatoryFields == 0) {
		return true;
	}
	var mandatorycheck = false;
	var i,j;
	var manCheck = new Array({$MANDATORYCHECK});
	var showvalues = "{$SHOWVALUES}";
	if(manCheck) {
		var isError = false;
		var errorMessage = "";
		if (trim(document.CustomView.viewName.value) == "") {
			isError = true;
			errorMessage += "\n{$MOD.LBL_VIEW_NAME}";
		}
		// Here we decide whether to submit the form.
		if (isError == true) {
			alert("{$MOD.Missing_required_fields}:" + errorMessage);
			return false;
		}
		for(i=1;i<=9;i++) {
			var columnvalue = document.getElementById("column"+i).value;
			if(columnvalue != null) {
				for(j=0;j<manCheck.length;j++) {
					if(columnvalue == manCheck[j]) {
						mandatorycheck = true;
					}
				}
				if(mandatorycheck == true) {
					if((document.getElementById("jscal_field_date_start").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) || (document.getElementById("jscal_field_date_end").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)) {
						return stdfilterdateValidate();
					} else {
						return true;
					}
				} else {
					mandatorycheck = false;
				}
			}
		}
	}
	if(mandatorycheck == false) {
		alert("{$APP.MUSTHAVE_ONE_REQUIREDFIELD}"+showvalues);
	}
	return false;
}
</script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
 <tbody><tr>
  <td class="showPanelBg" valign="top" width="100%">
   <div class="small" style="padding: 20px;">
	<span class="lvtHeaderText"><a class="hdrLink" href="index.php?action=ListView&module={$MODULE}">{$MODULELABEL}</a> &gt;
	{if $EXIST eq "true" && $EXIST neq ''}
		{$MOD.Edit_Custom_View} {$VIEWNAME}
	{else}
	 	{$MOD.New_Custom_View}
	{/if}
	</span> <br>
      <hr noshade="noshade" size="1">
      <form name="EditView" method="post" enctype="multipart/form-data" action="index.php">
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
      <tbody><tr>
      <td align="left" valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td colspan="4" class="detailedViewHeader"><strong>{$MOD.Details}</strong></td>
		</tr>
		<tr>
			<td colspan=4 width="100%" style="padding:0px">
			<table cellpadding=4 cellspacing=0 width=100% border=0>
				<tr>
					<td class="dvtCellInfo cblds-p_medium" width="10%" align="right"><span class="style1">*</span>{$MOD.LBL_VIEW_NAME}
					</td>
					<td class="dvtCellInfo" width="30%">
						<input class="detailedViewTextBox" type="text" name='viewName' value="{if isset($VIEWNAME)}{$VIEWNAME}{/if}" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" size="40" {if $PERMITALL eq 'true'}disabled{/if}/>
					</td>
					<td class="dvtCellInfo" width="15%">
					{if $CHECKED eq 'checked'}
						<input type="checkbox" name="setDefault" value="1" checked {if $PERMITALL eq 'true'}disabled{/if} />{$MOD.LBL_SETDEFAULT}
					{else}
						<input type="checkbox" name="setDefault" value="0" {if $PERMITALL eq 'true'}disabled{/if} />{$MOD.LBL_SETDEFAULT}
					{/if}
					</td>
					<td class="dvtCellInfo" width="15%">
					{if $MCHECKED eq 'checked'}
						<input type="checkbox" name="setMetrics" value="1" checked {if $PERMITALL eq 'true'}disabled{/if} />{$MOD.LBL_LIST_IN_METRICS}
					{else}
						<input type="checkbox" name="setMetrics" value="0" {if $PERMITALL eq 'true'}disabled{/if} />{$MOD.LBL_LIST_IN_METRICS}
					{/if}
					</td>
					<td class="dvtCellInfo" width="15%">
					{if $PERMITALL eq 'true'}
						<input type="checkbox" name="setStatus" value="0" checked {if $PERMITALL eq 'true'}disabled{/if} />
					{else}
						{if $STATUS eq '' || $STATUS eq 1}
							<input type="checkbox" name="setStatus" value="1" />
						{elseif $STATUS eq 2}
							<input type="checkbox" name="setStatus" value="2" checked />
						{elseif $STATUS eq 3 || $STATUS eq 0}
							<input type="checkbox" name="setStatus" value="3" checked />
						{/if}
					{/if}
						{$MOD.LBL_SET_AS_PUBLIC}
					</td>
					<td class="dvtCellInfo" width="15%">
						{if isset($setPrivate)}
							<input type="checkbox" name="setPrivate" value="1" {$setPrivate} />
						{else}
							<input type="checkbox" name="setPrivate" value="1" />
						{/if}
						{$MOD.LBL_SET_AS_PRIVATE}
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" class="detailedViewHeader">
				<b>{$MOD.LBL_STEP_2_TITLE} </b>
			</td>
		</tr>
		{section name=SelectColumn start=1 loop=$FILTERROWS step=1}
		<tr class="{cycle values="dvtCellLabel,dvtCellInfo"}">
		{section name=Column start=1 loop=5 step=1}
		{math equation="(x-1)*4+y" x=$smarty.section.SelectColumn.index y=$smarty.section.Column.index assign="cvcolumn"}
		{math equation="x-1" x=$cvcolumn assign="cvselected"}
		{if $cvcolumn <= $ListView_MaxColumns}
		<td class="cblds-p_medium">
			<select id="column{$cvcolumn}" name ="column{$cvcolumn}" onChange="checkDuplicate();" class="small">
				<option value="">{$MOD.LBL_NONE}</option>
				{foreach item=filteroption key=label from=$CHOOSECOLUMN}
					<optgroup label="{$label}" class="select" style="border:none">
					{foreach item=text from=$filteroption}
						{assign var=option_values value=$text.text}
						<option {if isset($SELECTEDCOLUMN[$cvselected]) && $SELECTEDCOLUMN[$cvselected] eq $text.value}selected{/if} value="{$text.value}">
						{$text.text}{if isset($DATATYPE.0.$option_values) && $DATATYPE.0.$option_values eq 'M'}   {$APP.LBL_REQUIRED_SYMBOL}{/if}
						</option>
					{/foreach}
				{/foreach}
			</select>
		</td>
		{else}
		<td>&nbsp;</td>
		{/if}
		{/section}
		</tr>
		{/section}
		<tr><td colspan="4">&nbsp;</td></tr>
		{if $PERMITALL neq 'true'}
		<tr><td colspan="4"><table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
		<tbody><tr>
		 <td>
		  <table class="small cvt-tabheaders" border="0" cellpadding="3" cellspacing="0" width="100%">
		   <tbody><tr>
		    <td class="dvtTabCache" style="width: 10px;" nowrap>&nbsp;</td>
		     {if $STDCOLUMNSCOUNT neq 0}
		    <td style="width: 100px;" nowrap class="dvtSelectedCell" id="pi" onclick="fnLoadCvValues('pi','mi','mnuTab','mnuTab2')">
		     <b>{$MOD.LBL_STEP_3_TITLE}</b>
		    </td>
		    <td class="dvtUnSelectedCell" style="width: 100px;" align="center" nowrap id="mi" onclick="fnLoadCvValues('mi','pi','mnuTab2','mnuTab')">
		     <b>{$MOD.LBL_STEP_4_TITLE}</b>
		    </td>
		    {else}
                    <td class="dvtSelectedCell" style="width: 100px;" align="center" nowrap id="mi">
                     <b>{$MOD.LBL_STEP_4_TITLE}</b>
                    </td>
                    {/if}
		    <td class="dvtTabCache" nowrap style="width:55%;">&nbsp;</td>
		   </tr>
		   </tbody>
	          </table>
		 </td>
	        </tr>
		<tr>
		 <td align="left" valign="top">
		{if $STDCOLUMNSCOUNT eq 0}
                        {assign var=stddiv value="style=display:none"}
                        {assign var=advdiv value="style=display:block"}
                {else}
                        {assign var=stddiv value="style=display:block"}
                        {assign var=advdiv value="style=display:none"}
                {/if}
		  <div id="mnuTab" {$stddiv}>
		     <table width="100%" cellspacing="0" cellpadding="5" class="dvtContentSpace">
                      <tr><td><br>
			<table width="75%" border="0" cellpadding="5" cellspacing="0" align="center">
			  <tr><td colspan="2" class="detailedViewHeader"><b>{$MOD.Simple_Time_Filter}</b></td></tr>
			  <tr>
			     <td width="75%" align="right" class="dvtCellLabel">{$MOD.LBL_Select_a_Column} :</td>
			     <td width="25%" class="dvtCellInfo">
				<select name="stdDateFilterField" class="select small" onchange="standardFilterDisplay();">
				{foreach item=stdfilter from=$STDFILTERCOLUMNS}
					<option {$stdfilter.selected} value={$stdfilter.value}>{$stdfilter.text}</option>
				{/foreach}
				</select>
			  </tr>
			  <tr>
			     <td align="right" class="dvtCellLabel">{$MOD.Select_Duration} :</td>
			     <td class="dvtCellInfo">
			        <select name="stdDateFilter" id="stdDateFilter" class="select small" onchange='showDateRange(this.options[this.selectedIndex].value)'>
				{foreach item=duration from=$STDFILTERCRITERIA}
					<option {$duration.selected} value={$duration.value}>{$duration.text}</option>
				{/foreach}
				</select>
			     </td>
			  </tr>
			  <tr>
			     <td align="right" class="dvtCellLabel">{$MOD.Start_Date} :</td>
			     <td width="25%" align=left class="dvtCellInfo">
			     {if $STDFILTERCRITERIA.0.selected eq "selected" || $CUSTOMVIEWID eq ""}
				{assign var=img_style value="visibility:visible"}
				{assign var=msg_style value=""}
			     {else}
				{assign var=img_style value="visibility:hidden"}
				{assign var=msg_style value="readonly"}
			     {/if}
			     <input name="startdate" id="jscal_field_date_start" type="text" size="10" class="textField small" value="{if isset($STARTDATE)}{$STARTDATE}{/if}" {$msg_style}>
			     <img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start" style="vertical-align:middle;{$img_style}">
			     <font size=1><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
			     <script type="text/javascript">
			  		Calendar.setup ({ldelim}
			 		inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
					{rdelim})
			     </script></td>
	            	  </tr>
			  <tr>
				<td align="right" class="dvtCellLabel">{$MOD.End_Date} :</td>
				<td width="25%" align=left class="dvtCellInfo">
				<input name="enddate" {$msg_style} id="jscal_field_date_end" type="text" size="10" class="textField small" value="{if isset($ENDDATE)}{$ENDDATE}{/if}">
				<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end" style="vertical-align:middle;{$img_style}">
				<font size=1><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
				<script type="text/javascript">
					Calendar.setup ({ldelim}
					inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1
					{rdelim})
				</script></td>
			  </tr>
			</table>
			</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div id="mnuTab2" class="cv-advfilt-tab" {$advdiv}>
		<table width="100%" cellspacing="0" cellpadding="5" class="dvtContentSpace">
			<tr><td><br>
			<table width="75%" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td>
					<div class="slds-grid slds-m-top_small cbds-advanced-search--active" id="cbds-advanced-search">
						<div class="slds-col">
							<div class="slds-expression slds-p-bottom_xx-large slds-p-horizontal_small">
								<input type="hidden" name="advft_criteria" id="advft_criteria" value="">
								<input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value="">
								{include file='AdvanceFilter.tpl' SOURCE='customview' MODULES_BLOCK=$FIELDNAMES_ARRAY}
							</div>
						</div>
					</div>
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
	</td>
	</tr>
	{/if}
  <tr><td colspan="4">&nbsp;</td></tr>
  <tr><td colspan="4" style="padding: 5px;">
	<ul class="slds-button-group-list slds-align_absolute-center" name="cbCVButtonGroup">
		<li>
		<button
			class="slds-button slds-button_neutral"
			title="{'LBL_SAVE_BUTTON_TITLE'|@getTranslatedString:$MODULE}"
			accessKey="{'LBL_SAVE_BUTTON_KEY'|@getTranslatedString:$MODULE}"
			onclick="return validateCV();"
			type="submit"
			name="button2">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
			</svg>
			{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}
		</button>
		</li>
		{if $PERMITALL neq 'true'}
			<li>
			<button
				class="slds-button slds-button_outline-brand"
				title="{'LBL_NEW_BUTTON_TITLE'|@getTranslatedString:$MODULE}"
				accessKey="{'LBL_NEW_BUTTON_KEY'|@getTranslatedString:$MODULE}"
				onclick="return validateCV();"
				type="submit"
				name="newsave">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
				</svg>
				{'LBL_NEW_BUTTON_LABEL'|@getTranslatedString:$MODULE}
			</button>
			</li>
		{/if}
		<li>
		<button
			class="slds-button slds-button_text-destructive"
			title="{'LBL_CANCEL_BUTTON_TITLE'|@getTranslatedString:$MODULE}"
			accessKey="{'LBL_CANCEL_BUTTON_KEY'|@getTranslatedString:$MODULE}"
			onclick="window.history.back();"
			type="button"
			name="button2">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use>
			</svg>
			{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}
		</button>
		</li>
	</ul>
  </td></tr>
  <tr><td colspan="4">&nbsp;</td></tr>
</table>
</table>
</table>
{if isset($STDFILTER_JAVASCRIPT)}{$STDFILTER_JAVASCRIPT}{/if}
{if isset($JAVASCRIPT)}{$JAVASCRIPT}{/if}
<!-- to show the mandatory fields while creating new customview -->
<script type="text/javascript">
var k;
var colOpts;
var manCheck = new Array({$MANDATORYCHECK});
var permitAll = {$PERMITALL};
{literal}
if (document.CustomView.record.value == '') {
	for (k=0; k<manCheck.length; k++) {
		selname = "column"+(k+1);
		selelement = document.getElementById(selname);
		if (selelement == null || typeof selelement == 'undefined') {
			continue;
		}
		colOpts = selelement.options;
		for (l=0; l<colOpts.length; l++) {
			if (colOpts[l].value == manCheck[k]) {
				colOpts[l].selected = true;
			}
		}
	}
}

function validateCV() {
	if (checkDuplicate()) {
		return window.AdvancedFilter.updateHiddenFields();
	}
	return false;
}

function checkDuplicate() {
	if (getObj('viewName').value.toLowerCase() == 'all' && !permitAll) {
		alert(alert_arr.ALL_FILTER_CREATION_DENIED);
		return false;
	}
	var cvselect_array = new Array();
	for (var cols=1;cols<={/literal}{$ListView_MaxColumns}{literal};cols++) {
		cvselect_array.push('column'+cols);
	}
	for (var loop=0;loop < cvselect_array.length-1;loop++) {
		selected_cv_columnvalue = document.getElementById(cvselect_array[loop]).options[document.getElementById(cvselect_array[loop]).selectedIndex].value;
		if (selected_cv_columnvalue != '') {
			for (var iloop=loop+1;iloop < cvselect_array.length;iloop++) {
				selected_cv_icolumnvalue = document.getElementById(cvselect_array[iloop]).options[document.getElementById(cvselect_array[iloop]).selectedIndex].value;
				if (selected_cv_columnvalue == selected_cv_icolumnvalue) {{/literal}
					alert('{$APP.COLUMNS_CANNOT_BE_DUPLICATED}');
					document.getElementById(cvselect_array[iloop]).selectedIndex = 0;
					return false;
					{literal}
				}
			}
		}
	}
	return true;
}

function stdfilterdateValidate() {
	if (!dateValidate('startdate', alert_arr.STDFILTER+' - '+alert_arr.STARTDATE, 'OTH')) {
		getObj('startdate').focus()
		return false;
	} else if (!dateValidate('enddate', alert_arr.STDFILTER+' - '+alert_arr.ENDDATE, 'OTH')) {
		getObj('enddate').focus()
		return false;
	} else {
		if (!dateComparison('enddate', alert_arr.STDFILTER+' - '+alert_arr.ENDDATE, 'startdate', alert_arr.STDFILTER+' - '+alert_arr.STARTDATE, 'GE')) {
			getObj('enddate').focus()
			return false
		} else {
			return true;
		}
	}
}
standardFilterDisplay();
{/literal}
</script>
