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
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="cvmodule" value="{$CVMODULE}">
<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
<input type="hidden" name="record" value="{$CUSTOMVIEWID}">
<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
<input type="hidden" id="user_dateformat" name="user_dateformat" value="{$DATEFORMAT}">
<script type="text/javascript">
function mandatoryCheck()
{ldelim}
	var mandatorycheck = false;
	var i,j;
	var manCheck = new Array({$MANDATORYCHECK});
	var showvalues = "{$SHOWVALUES}";
		if(manCheck)
		{ldelim}
				var isError = false;
				var errorMessage = "";
				if (trim(document.CustomView.viewName.value) == "") {ldelim}
						isError = true;
						errorMessage += "\n{$MOD.LBL_VIEW_NAME}";
				{rdelim}
				// Here we decide whether to submit the form.
				if (isError == true) {ldelim}
						alert("{$MOD.Missing_required_fields}:" + errorMessage);
						return false;
				{rdelim}
		
		for(i=1;i<=9;i++)
				{ldelim}
						var columnvalue = document.getElementById("column"+i).value;
						if(columnvalue != null)
						{ldelim}
								for(j=0;j<manCheck.length;j++)
								{ldelim}
										if(columnvalue == manCheck[j])
										{ldelim}
												mandatorycheck = true;
										{rdelim}
								{rdelim}
								if(mandatorycheck == true)
								{ldelim}
					if((document.getElementById("jscal_field_date_start").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) || (document.getElementById("jscal_field_date_end").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0))
						return stdfilterdateValidate();
					else
						return true;
								{rdelim}else
								{ldelim}
										mandatorycheck = false;
								{rdelim}
						{rdelim}
				{rdelim}
		{rdelim}
		if(mandatorycheck == false)
		{ldelim}
				alert("{$APP.MUSTHAVE_ONE_REQUIREDFIELD}"+showvalues);
		{rdelim}
		
		return false;
{rdelim}
</script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td>
				<div class="slds-truncate">

					<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
						<tr class="slds-text-title--caps">
							<td style="padding: 0;">
								<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilDesktop" style="height: 70px;">
									<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0); margin-top: 1rem;">
										<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
											<div class="slds-media__body">
												<a class="hdrLink" href="index.php?action=ListView&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a> &gt;
												<span class="slds-text-title slds-line-height--reset" style="opacity: 1;">
												{if $EXIST eq "true" && $EXIST neq ''}
												{$MOD.Edit_Custom_View}
												{else}
													{$MOD.New_Custom_View}
												{/if}
												</span>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
					<br>
					<form name="EditView" method="post" enctype="multipart/form-data" action="index.php">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<td align="left" valign="top">
									<table class="slds-table slds-no-row-hover slds-table-moz">
										<tr class="blockStyleCss">
											<td class="detailViewContainer" valign="top">
												<div class="forceRelatedListSingleContainer">
													<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
														<div class="slds-card__header slds-grid">
															<header class="slds-media slds-media--center slds-has-flexi-truncate">
																<div class="slds-media__body">
																	<h2>
																		<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$header}">
																			<strong>{$MOD.Details}</strong>
																		</span>
																	</h2>
																</div>
															</header>
														</div>
													</article>
												</div>
												<div class="slds-truncate align-left">
													<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
														<tr class="slds-line-height--reset">
															<td class="dvtCellLabel" width="20%">
																<span class="style1">*</span>{$MOD.LBL_VIEW_NAME}
															</td>
															<td class="dvtCellLabel" width="20%">
																<input class="slds-input" type="text" name='viewName' value="{if isset($VIEWNAME)}{$VIEWNAME}{/if}" size="40"/>
															</td>
															<td class="dvtCellLabel" width="20%">
																{if $CHECKED eq 'checked'}
																	<input type="checkbox" name="setDefault" value="1" checked/>{$MOD.LBL_SETDEFAULT}
																{else}
																	<input type="checkbox" name="setDefault" value="0" />{$MOD.LBL_SETDEFAULT}
																{/if}
															</td>
															<td class="dvtCellLabel" width="20%">
																{if $MCHECKED eq 'checked'}
																	<input type="checkbox" name="setMetrics" value="1" checked/>{$MOD.LBL_LIST_IN_METRICS}
																{else}
																	<input type="checkbox" name="setMetrics" value="0" />{$MOD.LBL_LIST_IN_METRICS}
																{/if}
															</td>
															<td class="dvtCellLabel" width="20%">
																{if $STATUS eq '' || $STATUS eq 1}
																	<input type="checkbox" name="setStatus" value="1"/>
																{elseif $STATUS eq 2}
																	<input type="checkbox" name="setStatus" value="2" checked/>
																{elseif $STATUS eq 3 || $STATUS eq 0}
																	<input type="checkbox" name="setStatus" value="3" checked/>
																{/if}
																	{$MOD.LBL_SET_AS_PUBLIC}
															</td>
														</tr>
													</table>
												</div>
											</td>
										</tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr class="blockStyleCss">
											<td class="detailViewContainer" valign="top">
												<div class="forceRelatedListSingleContainer">
													<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
														<div class="slds-card__header slds-grid">
															<header class="slds-media slds-media--center slds-has-flexi-truncate">
																<div class="slds-media__body">
																	<h2>
																		<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$header}">
																			<strong>{$MOD.LBL_STEP_2_TITLE}</strong>
																		</span>
																	</h2>
																</div>
															</header>
														</div>
													</article>
												</div>
												<div class="slds-truncate font-size">
													<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
														{section name=SelectColumn start=1 loop=$FILTERROWS step=1}
														<tr class="slds-line-height--reset {cycle values="dvtCellLabel,dvtCellInfo"}">
															{section name=Column start=1 loop=5 step=1}
															{math equation="(x-1)*4+y" x=$smarty.section.SelectColumn.index y=$smarty.section.Column.index assign="cvcolumn"}
															{math equation="x-1" x=$cvcolumn assign="cvselected"}
																{if $cvcolumn <= $ListView_MaxColumns}
																	<td class="dvtCellLabel" width="25%">
																		<select id="column{$cvcolumn}" name ="column{$cvcolumn}" onChange="checkDuplicate();" class="slds-select">
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
													</table>
												</div>
											</td>
										</tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr>
											<td valign=top style="padding: 0;">
												<div class="slds-truncate">
													<table class="slds-table slds-no-row-hover dvtContentSpace">
														<tr>
															<td valign="top" style="padding: 0;">
																<div class="slds-table--scoped">
																	<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
																		{if $STDCOLUMNSCOUNT neq 0}
																		<li class="slds-tabs--scoped__item selectedTab active" id="pi" onclick="fnLoadCvValues('pi','mi','mnuTab','mnuTab2')" role="presentation">
																			<a class="slds-tabs--scoped__link " href="javascript:void(0);" role="tab" tabindex="0" aria-selected="true"><b>{$MOD.LBL_STEP_3_TITLE}</b></a>
																		</li>
																		<li class="slds-tabs--scoped__item unSelectedTab" id="mi" onclick="fnLoadCvValues('mi','pi','mnuTab2','mnuTab')" role="presentation">
																			<a class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$MOD.LBL_STEP_4_TITLE}</b></a>
																		</li>
																		{else}
																		<li class="slds-tabs--scoped__item selectedTab" id="mi" role="presentation">
																			<a class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$MOD.LBL_STEP_4_TITLE}</b></a>
																		</li>
																		{/if}
																	</ul>
																	{if $STDCOLUMNSCOUNT eq 0}
																		{assign var=stddiv value="style=display:none"}
																		{assign var=advdiv value="style=display:block"}
																	{else}
																		{assign var=stddiv value="style=display:block"}
																		{assign var=advdiv value="style=display:none"}
																	{/if}
																	<div id="mnuTab" {$stddiv} role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
																		<table width="100%" cellspacing="0" cellpadding="5" class="dvtContentSpace">
																			<tr class="blockStyleCss">
																				<td class="detailViewContainer" valign="top">
																					<div class="forceRelatedListSingleContainer">
																						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																							<div class="slds-card__header slds-grid">
																								<header class="slds-media slds-media--center slds-has-flexi-truncate">
																									<div class="slds-media__body">
																										<h2>
																											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$header}">
																												<strong>{$MOD.Simple_Time_Filter}</strong>
																											</span>
																										</h2>
																									</div>
																								</header>
																							</div>
																						</article>
																					</div>
																					<div class="slds-truncate align-left">
																						<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																							<tr class="slds-line-height--reset">
																								<td class="dvtCellLabel" width="50%">
																									<b>{$MOD.LBL_Select_a_Column} :</b>
																								</td>
																								<td class="dvtCellLabel" width="50%">
																									<select name="stdDateFilterField" class="slds-select" onchange="standardFilterDisplay();">
																										{foreach item=stdfilter from=$STDFILTERCOLUMNS}
																											<option {$stdfilter.selected} value={$stdfilter.value}>{$stdfilter.text}</option>
																										{/foreach}
																									</select>
																								</td>
																							</tr>
																							<tr class="slds-line-height--reset">
																								<td class="dvtCellLabel" width="50%">
																									<b>{$MOD.Select_Duration} :</b>
																								</td>
																								<td class="dvtCellLabel" width="50%">
																									<select name="stdDateFilter" id="stdDateFilter" class="slds-select" onchange='showDateRange(this.options[this.selectedIndex].value )'>
																										{foreach item=duration from=$STDFILTERCRITERIA}
																											<option {$duration.selected} value={$duration.value}>{$duration.text}</option>
																										{/foreach}
																									</select>
																								</td>
																							</tr>
																							<tr class="slds-line-height--reset calendar-icon">
																								<td class="dvtCellLabel" width="50%"><b>{$MOD.Start_Date} :</b></td>
																								<td class="dvtCellLabel" width="50%">
																									{if $STDFILTERCRITERIA.0.selected eq "selected" || $CUSTOMVIEWID eq ""}
																										{assign var=img_style value="visibility:visible"}
																										{assign var=msg_style value=""}
																									{else}
																										{assign var=img_style value="visibility:hidden"}
																										{assign var=msg_style value="readonly"}
																									{/if}
																									<input name="startdate" id="jscal_field_date_start" type="text" size="10" class="slds-input" value="{if isset($STARTDATE)}{$STARTDATE}{/if}" {$msg_style} style="width: 25%;">
																									<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start" style={$img_style}>
																									<font size=1><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
																									<script type="text/javascript">Calendar.setup ({ldelim} inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1 {rdelim})</script>
																								</td>
																							</tr>
																							<tr class="slds-line-height--reset calendar-icon">
																								<td class="dvtCellLabel" width="50%"><b>{$MOD.End_Date} :</b></td>
																								<td class="dvtCellLabel" width="50%">
																									<input name="enddate" {$msg_style} id="jscal_field_date_end" type="text" size="10" class="slds-input" value="{if isset($ENDDATE)}{$ENDDATE}{/if}" style="width: 25%;">
																									<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end" style={$img_style}>
																									<font size=1><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
																									<script type="text/javascript">Calendar.setup ({ldelim} inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1 {rdelim})</script>
																								</td>
																							</tr>
																						</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</div>
																	<div id="mnuTab2" {$advdiv} role="tabpanel" aria-labelledby="tab--scoped-2__item" class="slds-tabs--scoped__content slds-truncate">
																		
																		<table width="100%" cellspacing="0" cellpadding="5" class="dvtContentSpace">
																			<tr>
																				<td>
																					<br>
																					<table width="100%" border="0" cellpadding="5" cellspacing="0" align="center">
																						<tr>
																							<td>{include file='AdvanceFilter.tpl' SOURCE='customview'}</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</div>
																</div>
															</td>
														</tr>
													</table>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan="4" style="padding: 5px;">
												<div align="center">
													<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button--small slds-button_success"  name="button2" value="{$APP.LBL_SAVE_BUTTON_LABEL}" type="submit" onClick="return validateCV();"/>
													<input title="{$APP.LBL_NEW_BUTTON_TITLE}" accesskey="{$APP.LBL_NEW_BUTTON_KEY}" class="slds-button slds-button--small slds-button--brand" name="newsave" value="{$APP.LBL_NEW_BUTTON_LABEL}" type="submit" onClick="return validateCV();"/>
													<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--small slds-button--destructive" name="button2" onclick='window.history.back()' value="{$APP.LBL_CANCEL_BUTTON_LABEL}" type="button" />
												</div>
											</td>
										</tr>
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
{literal}
if(document.CustomView.record.value == '') {
	for(k=0;k<manCheck.length;k++) {
		selname = "column"+(k+1);
		selelement = document.getElementById(selname);
		if(selelement == null || typeof selelement == 'undefined') continue;
		colOpts = selelement.options;
		for (l=0;l<colOpts.length;l++) {
			if(colOpts[l].value == manCheck[k]) {
				colOpts[l].selected = true;
			}
		}
	}
}

function validateCV() {
	if(checkDuplicate()) {
		return checkAdvancedFilter();
	}
	return false;
}

function checkDuplicate() {
	if(getObj('viewName').value.toLowerCase() == 'all') {
		alert(alert_arr.ALL_FILTER_CREATION_DENIED);
		return false;
	}
	var cvselect_array = new Array();
	for (var cols=1;cols<={/literal}{$ListView_MaxColumns}{literal};cols++) {
		cvselect_array.push('column'+cols);
	}
	for(var loop=0;loop < cvselect_array.length-1;loop++) {
		selected_cv_columnvalue = document.getElementById(cvselect_array[loop]).options[document.getElementById(cvselect_array[loop]).selectedIndex].value;
		if(selected_cv_columnvalue != '') {
			for(var iloop=loop+1;iloop < cvselect_array.length;iloop++) {
				selected_cv_icolumnvalue = document.getElementById(cvselect_array[iloop]).options[document.getElementById(cvselect_array[iloop]).selectedIndex].value;
				if(selected_cv_columnvalue == selected_cv_icolumnvalue) {
					{/literal}
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

function stdfilterdateValidate()
{
	if(!dateValidate("startdate",alert_arr.STDFILTER+" - "+alert_arr.STARTDATE,"OTH"))
	{
		getObj("startdate").focus()
		return false;
	}
	else if(!dateValidate("enddate",alert_arr.STDFILTER+" - "+alert_arr.ENDDATE,"OTH"))
	{
		getObj("enddate").focus()
		return false;
	}
	else
	{
		if (!dateComparison("enddate",alert_arr.STDFILTER+" - "+alert_arr.ENDDATE,"startdate",alert_arr.STDFILTER+" - "+alert_arr.STARTDATE,"GE")) {
						getObj("enddate").focus()
						return false
				} else return true;
	}
}
standardFilterDisplay();
{/literal}
</script>
