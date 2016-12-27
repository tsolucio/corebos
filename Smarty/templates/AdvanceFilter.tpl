<!--*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->
<style>
	.wizard .layerPopup {
		border: 0;
		position: absolute;
		z-index: 50;
		display: none;
		width: 100%;
		height:100%;
		background:transparent;
		top: 0 !important;
		left:0 !important;
	}

	.wizard .layerPopupBlock {
		width: 300px;
		position: absolute;
		top: 50%;
		left: 0;
		right: 0;
	}
</style>
<script src="include/js/json.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="include/js/advancefilter.js"></script>
{if empty($JS_DATEFORMAT)}
	{assign var="JS_DATEFORMAT" value=$APP.NTC_DATE_FORMAT|@parse_calendardate}
{/if}
<input type="hidden" id="jscal_dateformat" name="jscal_dateformat" value="{$JS_DATEFORMAT}" />
<input type="hidden" id="image_path" name="image_path" value="{$IMAGE_PATH}" />
<input type="hidden" name="advft_criteria" id="advft_criteria" value="" />
<input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value="" />
<script>
	{if $SOURCE neq 'reports'}
		var BLOCKJS = "";
		var BLOCKCRITERIA = "";
	{if $SOURCE neq 'customview'}	var COL_BLOCK =  '{$COLUMNS_BLOCK}';
        {else} var COL_BLOCK =  "{$COLUMNS_BLOCK}" {/if};
		var FOPTION_ADV = "";
		var MOMENT = "";
	{/if}
</script>
<script type="text/JavaScript">
function addColumnConditionGlue(columnIndex) {ldelim}

	var columnConditionGlueElement = document.getElementById('columnconditionglue_'+columnIndex);

	if(columnConditionGlueElement) {ldelim}
		columnConditionGlueElement.innerHTML = "<select name='fcon"+columnIndex+"' id='fcon"+columnIndex+"' class='detailedViewTextBox'>"+
			"<option value='and'>{'LBL_CRITERIA_AND'|@getTranslatedString:$MODULE}</option>"+
			"<option value='or'>{'LBL_CRITERIA_OR'|@getTranslatedString:$MODULE}</option>"+
			"</select>";
	{rdelim}
{rdelim}

function addConditionRow(groupIndex) {ldelim}
	var groupColumns = column_index_array[groupIndex];
	if(typeof(groupColumns) != 'undefined') {ldelim}
		for(var i=groupColumns.length - 1; i>=0; --i) {ldelim}
			var prevColumnIndex = groupColumns[i];
			if(document.getElementById('conditioncolumn_'+groupIndex+'_'+prevColumnIndex)) {ldelim}
				addColumnConditionGlue(prevColumnIndex);
				break;
			{rdelim}
		{rdelim}
	{rdelim}

	var columnIndex = advft_column_index_count+1;
	var nextNode = document.getElementById('groupfooter_'+groupIndex);

	var newNode = document.createElement('tr');
	newNodeId = 'conditioncolumn_'+groupIndex+'_'+columnIndex;
  	newNode.setAttribute('id',newNodeId);
  	newNode.setAttribute('name','conditionColumn');
	nextNode.parentNode.insertBefore(newNode, nextNode);

	node1 = document.createElement('td');
	node1.setAttribute('class', 'dvtCellLabel');
	node1.setAttribute('width', '25%');
	newNode.appendChild(node1);
	{if $SOURCE eq 'reports'}
		node1.innerHTML = '<select name="fcol'+columnIndex+'" id="fcol'+columnIndex+'" onchange="updatefOptions(this, \'fop'+columnIndex+'\');addRequiredElements('+columnIndex+');updateRelFieldOptions(this, \'fval_'+columnIndex+'\');" class="detailedViewTextBox">'+
							'<option value="">{'LBL_NONE'|@getTranslatedString:$MODULE}</option>'+COL_BLOCK+
						'</select>';
	{else}
		node1.innerHTML = "<select name='fcol"+columnIndex+"' id='fcol"+columnIndex+"' onchange='updatefOptions(this, \"fop"+columnIndex+"\");addRequiredElements("+columnIndex+");' class='detailedViewTextBox'>"+
							"<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>"+COL_BLOCK+
						"</select>";
	{/if}
	node2 = document.createElement('td');
	node2.setAttribute('class', 'dvtCellLabel');
	node2.setAttribute('width', '25%');

	node2.innerHTML = '<select name="fop'+columnIndex+'" id="fop'+columnIndex+'" class="repBox" style="width:100px;" onchange="addRequiredElements('+columnIndex+');">'+FOPTION_ADV+
							'<option value="">{'LBL_NONE'|@getTranslatedString:$MODULE}</option>'+
						'</select>';
	newNode.appendChild(node2);
	node3 = document.createElement('td');
	node3.setAttribute('class', 'dvtCellLabel');
	newNode.appendChild(node3);
	{if $SOURCE eq 'reports'}
		node3.innerHTML = '<input name="fval'+columnIndex+'" id="fval'+columnIndex+'" class="repBox" type="text" value="">'+
						'<img height=20 width=20 align="absmiddle" style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src="themes/images/terms.gif" onClick="hideAllElementsByName(\'relFieldsPopupDiv\'); fnShowDrop(\'show_val'+columnIndex+'\');"/>'+
						'<img align="absmiddle" style="cursor: pointer;" onclick="document.getElementById(\'fval'+columnIndex+'\').value=\'\';return false;" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>'+
						'<div class="layerPopup" id="show_val'+columnIndex+'" name="relFieldsPopupDiv" >'+
							'<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg layerPopupBlock">'+
								'<tr>'+
									'<td>'+
										'<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">'+
											'<tr background="themes/images/qcBg.gif" class="mailSubHeader">'+
												'<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>'+
												'<td align=right>'+
													'<img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="hideAllElementsByName(\'relFieldsPopupDiv\');"/>'+
												'</td>'+
											'</tr>'+
										'</table>'+

										'<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">'+
											'<tr>'+
												'<td>'+
													'<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">'+
														'<tr>'+
															'<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>'+
															'<td width="30%" align="left" class="cellText">'+
																'<select name="fval_'+columnIndex+'" id="fval_'+columnIndex+'" onChange="AddFieldToFilter('+columnIndex+',this);" class="detailedViewTextBox">'+
																	'<option value="">{$MOD.LBL_NONE}</option>'+
																	REL_FIELDS+
																'</select>'+
															'</td>'+
														'</tr>'+
													'</table>'+
													'<!-- save cancel buttons -->'+
													'<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">'+
														'<tr>'+
															'<td width="50%" align="center">'+
																'<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="hideAllElementsByName(\'relFieldsPopupDiv\');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>'+
															'</td>'+
														'</tr>'+
													'</table>'+
												'</td>'+
											'</tr>'+
										'</table>'+
									'</td>'+
								'</tr>'+
							'</table>'+
						'</div>';
	{else}
		node3.innerHTML = '<input name="fval'+columnIndex+'" id="fval'+columnIndex+'" class="repBox" type="text" value="">'+ '<img align="absmiddle" style="cursor: pointer;" onclick="document.getElementById(\'fval'+columnIndex+'\').value=\'\';return false;" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>';
	{/if}

	node4 = document.createElement('td');
	node4.setAttribute('class', 'dvtCellLabel');
	node4.setAttribute('id', 'columnconditionglue_'+columnIndex);
	node4.setAttribute('width', '60px');
	newNode.appendChild(node4);

	node5 = document.createElement('td');
	node5.setAttribute('class', 'dvtCellLabel');
	node5.setAttribute('width', '30px');
	newNode.appendChild(node5);
	node5.innerHTML = '<a onclick="deleteColumnRow('+groupIndex+','+columnIndex+');" href="javascript:;">'+
					'<img src="themes/images/delete.gif" align="absmiddle" border="0"></a>';

	if(document.getElementById('fcol'+columnIndex)) updatefOptions(document.getElementById('fcol'+columnIndex), 'fop'+columnIndex);
	if(typeof(column_index_array[groupIndex]) == 'undefined') column_index_array[groupIndex] = [];
	column_index_array[groupIndex].push(columnIndex);
	advft_column_index_count++;

{rdelim}

function addGroupConditionGlue(groupIndex) {ldelim}

	var groupConditionGlueElement = document.getElementById('groupconditionglue_'+groupIndex);
	if(groupConditionGlueElement) {ldelim}
		groupConditionGlueElement.innerHTML = "<select name='gpcon"+groupIndex+"' id='gpcon"+groupIndex+"' class='small'>"+
			"<option value='and'>{'LBL_CRITERIA_AND'|@getTranslatedString:$MODULE}</option>"+
			"<option value='or'>{'LBL_CRITERIA_OR'|@getTranslatedString:$MODULE}</option>"+
		"</select>";
	{rdelim}
{rdelim}

function addConditionGroup(parentNodeId) {ldelim}

	for(var i=group_index_array.length - 1; i>=0; --i) {ldelim}
		var prevGroupIndex = group_index_array[i];
		if(document.getElementById('conditiongroup_'+prevGroupIndex)) {ldelim}
			addGroupConditionGlue(prevGroupIndex);
			break;
		{rdelim}
	{rdelim}

	var groupIndex = advft_group_index_count+1;
	var parentNode = document.getElementById(parentNodeId);

	var newNode = document.createElement('div');
	newNodeId = 'conditiongroup_'+groupIndex;
  	newNode.setAttribute('id',newNodeId);
  	newNode.setAttribute('name','conditionGroup');

  	newNode.innerHTML = "<table class='small crmTable' border='0' cellpadding='5' cellspacing='1' width='100%' valign='top' id='conditiongrouptable_"+groupIndex+"'>"+
			"<tr id='groupheader_"+groupIndex+"'>"+
				"<td colspan='5' align='right'>"+
					"<a href='javascript:void(0);' onclick='deleteGroup(\""+groupIndex+"\");'><img border=0 src={'close.gif'|@vtiger_imageurl:$THEME} alt='{$APP.LBL_DELETE_GROUP}' title='{$APP.LBL_DELETE_GROUP}'/></a>"+
				"</td>"+
			"</tr>"+
			"<tr id='groupfooter_"+groupIndex+"'>"+
				"<td colspan='5' align='left'>"+
					"<input type='button' class='crmbutton edit small' value='{$APP.LBL_NEW_CONDITION}' onclick='addConditionRow(\""+groupIndex+"\")' />"+
				"</td>"+
			"</tr>"+
		"</table>"+
		"<table class='small' border='0' cellpadding='5' cellspacing='1' width='100%' valign='top'>"+
			"<tr><td align='center' id='groupconditionglue_"+groupIndex+"'>"+
			"</td></tr>"+
		"</table>";

	parentNode.appendChild(newNode);

	group_index_array.push(groupIndex);
	advft_group_index_count++;
{rdelim}

function add_grouping_criteria(grouping_criteria) {ldelim}
	if(grouping_criteria == null)
		return false;
	var grouping_criteria_length = Object.keys(grouping_criteria).length;

	if(grouping_criteria_length > 0) {
		for(var i = 1;i <= grouping_criteria_length; i++) {
			var group_columns = grouping_criteria[i].columns;
			addConditionGroup('adv_filter_div');
			for (var key in group_columns) {
				if (group_columns.hasOwnProperty(key)) {

					addConditionRow(i);
					var conditionColumnRowElement = document.getElementById('fcol'+advft_column_index_count);

					conditionColumnRowElement.value = group_columns[key].columnname;
					updatefOptions(conditionColumnRowElement, 'fop'+advft_column_index_count);
					document.getElementById('fop'+advft_column_index_count).value = group_columns[key].comparator;
					addRequiredElements(advft_column_index_count);
					{if $SOURCE eq 'reports'}
						updateRelFieldOptions(conditionColumnRowElement, 'fval_'+advft_column_index_count);
					{/if}
					var columnvalue = group_columns[key].value;
					if(group_columns[key].comparator == 'bw' && columnvalue != '') {
						var values = columnvalue.split(",");
						document.getElementById('fval'+advft_column_index_count).value = values[0];
						if(values.length == 2 && document.getElementById('fval_ext'+advft_column_index_count))
							document.getElementById('fval_ext'+advft_column_index_count).value = values[1];
					} else {
						document.getElementById('fval'+advft_column_index_count).value = columnvalue;
					}

					if(document.getElementById('fcon'+key))
						document.getElementById('fcon'+key).value = group_columns[key].column_condition;
				}
			}
			if(document.getElementById('gpcon'+i))
				document.getElementById('gpcon'+i).value = grouping_criteria[i].condition;
		}
	} else {
		addNewConditionGroup('adv_filter_div');
	}
{rdelim}
</script>

<div style="overflow:auto;margin-top: 30px;" id='adv_filter_div' name='adv_filter_div'>
	<table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td class="detailedViewHeader" align="left"><b>{'LBL_ADVANCED_FILTER'|@getTranslatedString:$MODULE}</b></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="button" class="crmbutton create small" value="{'LBL_NEW_GROUP'|@getTranslatedString:$MODULE}" onclick="addNewConditionGroup('adv_filter_div')" />
			</td>
		</tr>
	</table>
<script>
	{if $SOURCE neq 'reports'}
		{if $CRITERIA_GROUPS|@count > 0}
			add_grouping_criteria({json_encode($CRITERIA_GROUPS)});
		{else}
			addNewConditionGroup('adv_filter_div');
		{/if}
	{/if}
</script>
</div>
