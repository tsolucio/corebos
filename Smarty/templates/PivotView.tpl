{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{if $showDesert}
	{assign var='DESERTInfo' value='LBL_NO_DATA'|@getTranslatedString:$MODULE}
	{include file='Components/Desert.tpl'}
{else}
<script type="text/javascript" src="include/pivottable/d3/d3.min.js"></script>
<script src="include/pivottable/Plotly/plotly-2.8.3.min.js"></script>
<script src="include/pivottable/pivot.js"></script>
<script src="include/pivottable/d3_renderers.js"></script>
<script src="include/pivottable/plotly_renderers.js"></script>
<script src="include/pivottable/export_renderers.js"></script>
<script src="include/pivottable/export_renderers.min.js"></script>
<script src="include/pivottable/nrecopivottableext.js"></script>
<script src="include/pivottable/multifact-pivottable.js"></script>
<link href="include/pivottable/nrecopivottableext.css" rel="stylesheet">
<link href="include/pivottable/pivot.css" rel="stylesheet">
<script type="text/javascript">
{literal}
const bmapname = {/literal}'{$bmapname}'{literal};
$(function() {
	let url = `index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getMapByName&mapname=${bmapname}`;
	fetch(
		url,
		{
			method: 'get',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
		}
	).then(response => response.json()).then(response => {
		let mapContent = response.content;
		var advFilter = Array();
		var sum = $.pivotUtilities.aggregatorTemplates.sum;
		var numberFormat = $.pivotUtilities.numberFormat;
		var intFormat = numberFormat({digitsAfterDecimal: 0});
		var renderers = $.extend(
			$.pivotUtilities.renderers,
			$.pivotUtilities.plotly_renderers,
			$.pivotUtilities.d3_renderers,
			$.pivotUtilities.export_renderers
		);
		let multiAggs = {};
		const aggMap = {/literal}{$aggregations}{literal};
		if (aggMap.length > 0) {
			multiAggs['Multifact Aggregators'] = $.pivotUtilities.multifactAggregatorGenerator(aggMap,[]);
			$.pivotUtilities.multiAggs = multiAggs;
			renderers = $.extend(
				$.pivotUtilities.renderers,
				$.pivotUtilities.plotly_renderers,
				$.pivotUtilities.d3_renderers,
				$.pivotUtilities.export_renderers,
				$.pivotUtilities.gtRenderers
			);
		}
		const nrecoPivotExt = new NRecoPivotTableExtensions({
			drillDownHandler: function (dataFilter) {
				var filterParts = [];
				for (var k in dataFilter) {
					filterParts.push(k+"="+dataFilter[k]);
				}
			}
		});
		let pivotConfig = {
			rows: [{/literal}{$ROWS}{literal}],
			cols: [{/literal}{$COLS}{literal}],
			{/literal}{$aggreg}{literal}
			aggregators: $.extend($.pivotUtilities.aggregators, $.pivotUtilities.multiAggs),
			renderers: renderers,
			rendererOptions: {
				aggregations : {
					defaultAggregations : aggMap
				},
				table: {
					clickCallback: function(e, value, filters, pivotData) {
						let names = Array();
						let fields = Array();
						//get rows and cols from map
						if (typeof mapContent == 'string' && mapContent == 'NOT_PERMITTED') {
							return;
						}
						Object.keys(mapContent).map(function(key, index) {
							if (typeof mapContent[key] == 'object') {
								for (let i in mapContent[key]) {
									if (filters[mapContent[key][i].label] !== undefined) {
										fields.push(mapContent[key][i].name);
										advFilter.push({
											'columnname': mapContent[key][i].name,
											'comparator': 'e',
											'value': filters[mapContent[key][i].label],
											'groupid': 1,
											'columncondition': 'and'
										});
									}
								}
							}
						});
						let url = `index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getFieldsAttributes&modulename=${gVTModule}&fields=${fields}`;
						getData(url, advFilter).then(res => {
							res[0].fields.map(function(cValue) {
								const columnname = `${cValue.tablename}:${cValue.columnname}:${cValue.fieldname}:${gVTModule}_${cValue.fieldlabel}:${cValue.typeofdata}`;
								Object.keys(res[1]).map(function(key, idx) {
									if (res[1][key].columnname == cValue.fieldname) {
										res[1][key].columnname = columnname;
									}
								});
							});
							const Pivot_AdvancedSearch = res[1];
							const urlstring = `${JSON.stringify(Pivot_AdvancedSearch)}&advft_criteria_groups=[null,{"groupcondition":""}]`;
							window.open(`index.php?module=${gVTModule}&action=index&query=true&search=true&searchtype=advance&advft_criteria=${urlstring}`, '_blank');
						});
						advFilter = Array();
						fields = Array();
					}
				}
			}
		};
		const aggregatorName = {/literal}'{$aggregatorName}'{literal};
		const rendererName = {/literal}'{$rendererName}'{literal};
		if (aggregatorName != '') {
			pivotConfig.aggregatorName = aggregatorName;
		}
		if (rendererName != '') {
			pivotConfig.rendererName = rendererName;
		}
		$("#output").pivotUI([{/literal}{$RECORDS}{literal}], pivotConfig);
	});
});
async function getData(url, filter) {
	const response = await fetch(url);
	return Array(
		await response.json(),
		filter
	);
}
let pageWidth = document.querySelector('#page-header');
let screenWidth = pageWidth.offsetWidth - 100;
document.documentElement.style.setProperty(`--screenWidth`, `${screenWidth}px`);
{/literal}
</script>
<div id="output"></div>
{/if}
<div id="pivotdetail" class="layerPopup">
{include file="Pivotdetail.tpl"}
</div>
