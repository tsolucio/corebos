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
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
	<title>{$MOD.TITLE_VTIGERCRM_CREATE_REPORT}</title>
	<link href="{$THEME_PATH}style.css" rel="stylesheet" type="text/css">
	<link href="include/jquery.steps.css" rel="stylesheet">
	<style>
	.relmodscolumns {ldelim}
		margin-left: 10px;
		margin-right: 10px;
		width: 100%;
		-moz-column-count: 2;
		-moz-column-gap: 10px;
		-moz-column-rule: none;
		-webkit-column-count: 2;
		-webkit-column-gap: 10px;
		-webkit-column-rule: none;
		column-count: 2;
		column-gap: 10px;
		column-rule: none;
	{rdelim}
	.reportButtonFooter {ldelim}
		position: fixed;
		bottom: 0;
		width: 100%;
	{rdelim}
	.grouping_section {ldelim}
		border: 0;
		cellpadding: 5;
		cellspacing : 0:
		height: 500px;
		width: 100%;
	{rdelim}
	</style>
	<script type='text/javascript' src='include/jquery/jquery.js'></script>
	<script type="text/javascript" src="include/jquery/jquery.steps.min.js"></script>
	{include file='BrowserVariables.tpl'}
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
	<script type="text/javascript" src="include/js/json.js"></script>
	<script type="text/javascript" src="modules/Reports/Reports.js"></script>

</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
<tr>
	<td>
		<form name="NewReport" method="POST" ENCTYPE="multipart/form-data" action="index.php" style="margin:0px">
		<input type="hidden" name="module" value="Reports">
		<input type="hidden" name="primarymodule" value="{$REP_MODULE}">
		<input type="hidden" name="secondarymodule" value>
		<input type="hidden" name="record" id="record" value="{$RECORDID}">
		<input type="hidden" name='folder' value="{$FOLDERID}"/>
		<input type="hidden" name='reload' value='true'/>
		<input type="hidden" name="action" value="Save">
		<input type="hidden" name='saveashidden' value='saveas'/>
		<input type="hidden" name='newreportname' id='newreportname' value=''/>
		<div id="report-steps" class="jquery-steps">

			<!-- STEP 1 -->
			<h3>{$MOD.LBL_REPORT_DETAILS}</h3>
			<section>
				<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#FFFFFF" height="500" class="small">
					<tr>
						<td colspan="2">
							<span class="genHeaderGray">{$MOD.LBL_REPORT_DETAILS}</span><br>
							{$MOD.LBL_TYPE_THE_NAME} &amp; {$MOD.LBL_DESCRIPTION_FOR_REPORT}<hr>
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REPORT_NAME} : </b></td>
						<td width="75%" align="left" style="padding-left:5px;"><input type="text" name="reportName" class="txtBox" value="{$REPORTNAME}"></td>
					</tr>
					<tr>
						<td width="25%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REP_FOLDER} : </b></td>
						<td width="75%" align="left" style="padding-left:5px;">
							<select name="reportfolder" class="txtBox">
							{foreach item=folder from=$REP_FOLDERS}
							{if $FOLDERID eq $folder.id}
								<option value="{$folder.id}" selected>{$folder.name}</option>
							{else}
								<option value="{$folder.id}">{$folder.name}</option>
							{/if}
							{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" style="padding-right:5px;" valign="top"><b>{$MOD.LBL_DESCRIPTION}: </b></td>
						<td align="left" style="padding-left:5px;"><textarea name="reportDesc" class="txtBox" rows="5">{$REPORTDESC}</textarea></td>
					</tr>
					<tr>
						<td align="center" colspan="2" height="30" class="step_error" id="step1_error" style="color:red;">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" height="310">&nbsp;</td>
					</tr>
				</table>
			</section>

			<!-- STEP 2 -->
			<h3>{$MOD.LBL_RELATIVE_MODULE}</h3>
			<section>
				<div id="step2">
					<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
						<tr height='10%'>
							<td colspan="2">
								<span class="genHeaderGray">{$MOD.LBL_RELATIVE_MODULE}</span><br>
								{$MOD.LBL_SELECT_RELATIVE_MODULE_FOR_REPORT}<hr>
							</td>
						</tr>
						{if $RESTRICTEDMODULES neq ''}
						<tr class='small' height='5%'>
							<td colspan="2"><div class='dvtCellInfo' style='margin-left: 10px;'>{$MOD.LBL_NOT_ACTIVE}<font color="red"><b> {$RESTRICTEDMODULES} </b></font></div></td>
						</tr>
						{/if}
						<tr valign=top height="70%">
							{if $RELATEDMODULES|@count > 0}
								<td style="padding-left: 5px; " align="left" width="100%">
									<div class="small relmodscolumns">
									{foreach item=relmod from=$RELATEDMODULES}
										<input type='checkbox' class="secondarymodule" name="secondarymodule_{$relmod}" {if isset($SEC_MODULE.$relmod) && $SEC_MODULE.$relmod eq 1}checked {/if}value="{$relmod}" />&nbsp;{$relmod|@getTranslatedString:$relmod}<br>
									{/foreach}
									</div>
								</td>
							{else}
								<td style="padding-right: 5px;" align="left" nowrap width="25%"><b>{$MOD.NO_REL_MODULES}</b></td>
							{/if}
						</tr>
						<tr>
							<td align="center" colspan="2" height="30" class="step_error" id="step2_error" style="color:red;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2" height="350">&nbsp;</td>
						</tr>
					</table>
				</div>
			</section>

			<!-- STEP 3 -->
			<h3>{$MOD.LBL_REPORT_TYPE}</h3>
			<section>
				<div id="step3">
					{include file='ReportsType.tpl' SOURCE='reports'}
				</div>
			</section>

			<!-- STEP 4 -->
			<h3>{$MOD.LBL_SELECT_COLUMNS}</h3>
			<section>
				{include file='ReportColumns.tpl' SOURCE='reports'}
			</section>

			<!-- STEP 6 -->
			<h3>{$MOD.LBL_CALCULATIONS}</h3>
			<section>
				{include file='ReportColumnsTotal.tpl' SOURCE='reports'}
			</section>

			<!-- STEP 7 -->
			<h3>{$MOD.LBL_FILTERS}</h3>
			<section>
				{include file='ReportFilters.tpl' SOURCE='reports'}
			</section>

			<!-- STEP 8 -->
			<h3>{$MOD.LBL_SHARING}</h3>
			<section>
				{include file='ReportSharing.tpl' SOURCE='reports'}
			</section>

			<!-- STEP 9 -->
			<h3>{$MOD.LBL_SCHEDULE_EMAIL}</h3>
			<section>
				{include file='ReportsScheduleEmail.tpl' SOURCE='reports'}
			</section>
		</div>
		{include file='ReportGrouping.tpl' SOURCE='reports'}
		</form>
		<!-- premission warning -->
		<div style="display: none;" id="not_premitted">
			<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'>
				<tr>
					<td align='center'>
						<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>
							<table border='0' cellpadding='5' cellspacing='0' width='98%'>
								<tbody>
									<tr>
										<td rowspan='2' width='11%'><img src="{'denied.gif'|@vtiger_imageurl:$THEME}"></td>
										<td style='border-bottom: 1px solid rgb(204, 204, 204);'  width='70%'><span id="deny_msg" class='genHeaderSmall'></span></td>
									</tr>
									<tr>
										<td class='small' align='right' nowrap='nowrap'>
											<a onclick="reports_goback()">{$APP.LBL_GO_BACK}</a><br>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</td>

</tr>
</table>

</body>
{literal}

<script>

	var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;

	// Labels
	var LBL_NONE = {/literal}"{$MOD.LBL_NONE}"{literal};
	var NO_COLUMN = {/literal}"{$MOD.NO_COLUMN}"{literal};
	var LBL_NO_PERMISSION = {/literal}"{$MOD.LBL_NO_PERMISSION}"{literal}

	var wizard = $("#report-steps");
	var new_steps_count = 0;
	var COL_BLOCK;
	var BLOCKCRITERIA;
	var BLOCKJS;
	var CRITERIA_GROUP;
	var FOPTION_ADV;
	var BLOCKJS;
	var REL_FIELDS;
	var updated_grouping_criteria = false;

	// Filter vars
	var userIdArr;
	var userNameArr;
	var grpIdArr;
	var grpNameArr;

	wizard.steps({
		headerTag: "h3",
		bodyTag: "section",
		transitionEffect: "slideLeft",
		stepsOrientation: "vertical",

		onInit: function (event, currentIndex) {

			// Add Cancel button
			var cancel_button = $("<a>",{"type":"button","onclick":"self.close();","href":"#cancel"});
			cancel_button.html("cancel");
			var li = $("<li>");
			li.append(cancel_button)
			$(".actions ul").append(li);

			//Add <Save As> button
			if( document.NewReport.record.value !== "") {
				var save_as_button = $("<a>",{"type":"button","onclick":"saveas();","href":"#saveas"});
				save_as_button.html("save as");
				var li = $("<li>",{"style":"display:none","id":"save_as_button"});
				li.append(save_as_button);
				$(".actions ul li:nth-child(3)").after(li);
			}

		},

		onStepChanging: function (event, currentIndex, newIndex) {

			// Clear Previous errors
			$(".step_error").html("");

			// Let user go one step back
			if(currentIndex > newIndex) {
				return true;
			}

			// Check for errors on step 3
			if(currentIndex == 3 && newIndex == 4) {
				if(selectedColumnsObj.options.length == 0) {
					var $error_selector = $("#step4_error");
					$error_selector.html(alert_arr.COLUMNS_CANNOT_BE_EMPTY);
					return false;
				}
				if(new_steps_count > 0)
					return true;
			}

			if(currentIndex == 0 && newIndex == 1) {
				var $error_selector = $("#step1_error");
				var can_pass = false;

				// Check if report name field is not empty
				if(document.NewReport.reportName.value =="") {
					$error_selector.html(alert_arr.MISSING_REPORT_NAME);
					return can_pass;
				}

				// Check if there is any Report with the same name
				jQuery.ajax({
					method: 'POST',
					async: false,
					url: 'index.php?action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=reportCheck&reportName='+encodeURIComponent(document.NewReport.reportName.value)+'&reportid='+document.NewReport.record.value
				}).done(function (response) {
						if(response!=0)
							$error_selector.html(alert_arr.REPORT_NAME_EXISTS);
						else
							can_pass = true;
				});

				return can_pass;

			} else if(currentIndex == 1 && newIndex == 2) {
				var can_pass = false;
				var $error_selector = $("#step2_error");
				var modsselected = $('.secondarymodule:checkbox:checked').length;

				if (modsselected<=2) {
					var data = setStepData(3);

					// Ajax Call
					jQuery.ajax({
						method: 'POST',
						async: false,
						data: data,
						url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
						dataType: "json",
					}).done(function (response) {
						can_pass = setReportType(response);
					});
				} else {
					$error_selector.html(alert_arr.MAXIMUM_OF_TWO_MODULES_PERMITTED);
					can_pass = false;
				}

				return can_pass;

			} else if(currentIndex == 2 && newIndex == 3) {

				var can_pass = false;
				var $error_selector = $("#step3_error");
				var data = setStepData(4);

				// Ajax Call
				jQuery.ajax({
					method: 'POST',
					async: false,
					data: data,
					url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
					dataType: "json",
				}).done(function (response) {
					can_pass = fillSelectedColumns(response);
				});

				return can_pass;

			} else if(currentIndex - new_steps_count == 3  && newIndex - new_steps_count == 4) {
				var can_pass = false;
				var $error_selector = $("#step4_error");
				var data = setStepData(5);

				// Ajax Call
				jQuery.ajax({
					method: 'POST',
					async: false,
					data: data,
					url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
					dataType: "json",
				}).done(function (response) {
					fillReportColumnsTotal(response.BLOCK1);
					can_pass = true;
				});

				return can_pass;

			} else if(currentIndex - new_steps_count == 4  && newIndex - new_steps_count == 5 ) {
				var can_pass = false;
				var data = setStepData(6);

				// Ajax Call
				jQuery.ajax({
					method: 'POST',
					async: false,
					data: data,
					url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
					dataType: "json",
				}).done(function (response) {
					can_pass = fillFilterInfo(response);
				});
				return can_pass;

			} else if(currentIndex - new_steps_count == 5  && newIndex - new_steps_count == 6 ) {

				var can_pass = false;
				var data = setStepData(7);

				if(!validateDate())
					return can_pass;

				// Ajax Call
				jQuery.ajax({
					method: 'POST',
					async: false,
					data: data,
					url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
					dataType: "json",
				}).done(function(response) {
					can_pass = fillGroupingInfo(response);
				});

				return can_pass;

			} else if( currentIndex - new_steps_count >= 5 ) {
				return true;
			}

		},

		onStepChanged: function (event, currentIndex, priorIndex) {

			if(currentIndex == 3 && priorIndex == 2 ) {
				if(document.NewReport.reportType.value =="summary") {
					// Ajax Call
					var data = setStepData("grouping");
					jQuery.ajax({
						method: 'POST',
						data: data,
						url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
						dataType: "json",
					}).done(function(response) {
						//Summarize information lists
						fillFullList(response.BLOCK1,"Group1",true,LBL_NONE);
						fillFullList(response.BLOCK2,"Group2",true,LBL_NONE);
						fillFullList(response.BLOCK3,"Group3",true,LBL_NONE);

						// Group By time
						$("#Group1time").css("display",response.GRBYTIME1.display);
						fillList(response.GRBYTIME1.options,"groupbytime1");
						$("#Group2time").css("display",response.GRBYTIME2.display);
						fillList(response.GRBYTIME2.options,"groupbytime2");
						$("#Group3time").css("display",response.GRBYTIME3.display);
						fillList(response.GRBYTIME3.options,"groupbytime3");

						// Group Order
						fillList(response.ORDER1,"Sort1");
						fillList(response.ORDER2,"Sort2");
						fillList(response.ORDER3,"Sort3");

					});

					// Add another step
					if(wizard.find('section').length == 8) {
						wizard.steps("insert",4, {
							title: {/literal}"{$MOD.LBL_SPECIFY_GROUPING}"{literal},
							content: "<table class='grouping_section'>" + $("#grouping_section").html() + "</table>"
						});
						new_steps_count++;
					}
				}
				// Remove step if report type is tabular
				else if(document.NewReport.reportType.value =="tabular" && wizard.find('section').length == 9) {
					wizard.steps("remove",4);
					new_steps_count--;
				}
			}

			// Display <Save as> button
			if(document.NewReport.record.value !== "") {
				if ( currentIndex - new_steps_count >= 6) {
					$("#save_as_button").css("display","block")
				} else {
					$("#save_as_button").css("display","none")
				}
			}

		},

		onFinishing: function (event,currentIndex) {
			return ScheduleEmail();
		},

		onFinished: function (event, currentIndex) {
	        saveAndRunReport();
	    }
	});

</script>
{/literal}
</html>
