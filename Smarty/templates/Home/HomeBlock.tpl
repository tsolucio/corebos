
<div class='hide_tab' id="editRowmodrss_{$HOME_STUFFID}" style="position:absolute; top:0px;left:0px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small" valign="top">
	<tr>
{if $HOME_STUFFTYPE eq "Module" || $HOME_STUFFTYPE eq "RSS" || $HOME_STUFFTYPE eq "Default"}
		<td align="left" class="homePageMatrixHdr" style="height:28px;" nowrap width="40%">
			{$MOD.LBL_HOME_SHOW}&nbsp;
			<select id="maxentries_{$HOME_STUFFID}" name="maxid" class="small" style="width:40px;">
	{section name=iter start=1 loop=13 step=1}
				<option value="{$smarty.section.iter.index}" {if $HOME_STUFF.Maxentries==$smarty.section.iter.index} selected {/if}>
					{$smarty.section.iter.index}
				</option>
	{/section}
			</select>&nbsp;{$MOD.LBL_HOME_ITEMS}
		</td>
		<td align="right" class="homePageMatrixHdr" nowrap style="height:28px;" width=60%>
			<input type="button" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " name="save" class="crmbutton small save" onclick="saveEntries('maxentries_{$HOME_STUFFID}')">
			<input type="button" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " name="cancel" class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			{if $HOME_STUFFTYPE eq 'Module'}
			{$HOME_STUFF.ModuleName|@getTranslatedString:$HOME_STUFF.ModuleName}::{$HOME_STUFF.cvidname|@getTranslatedString:$HOME_STUFF.ModuleName}
			{/if}
		</td>
{elseif $HOME_STUFFTYPE eq "DashBoard"}
		<td  valign="top" align='center' class="homePageMatrixHdr" style="height:28px;" width=60%>
			<input type="radio" id="dashradio_0" name="dashradio_{$HOME_STUFFID}" value="horizontalbarchart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'horizontalbarchart'}checked{/if}>{'LBL_HOME_HORIZONTAL'|@getTranslatedString:'Home'}
			<input type="radio" id="dashradio_1" name="dashradio_{$HOME_STUFFID}" value="verticalbarchart"{if $DASHDETAILS.$HOME_STUFFID.Chart eq 'verticalbarchart'}checked{/if}>{'LBL_HOME_VERTICAL'|@getTranslatedString:'Home'}
			<input type="radio" id="dashradio_2" name="dashradio_{$HOME_STUFFID}" value="piechart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}checked{/if}>{'LBL_HOME_PIE'|@getTranslatedString:'Home'}
		</td>
		</tr>
		<tr>
			<td  valign="top" align="center" class="homePageMatrixHdr" nowrap style="height:28px;" width="40%">
			<input type="button" name="save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " class="crmbutton small save" onclick="saveEditDash({$HOME_STUFFID})">
			<input type="button" name="cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			</td>
		</tr>
{elseif $HOME_STUFFTYPE eq "ReportCharts"}
		<td  valign="top" align='center' class="homePageMatrixHdr" style="height:28px;" width=60%>
			<input type="radio" id="reportradio_{$HOME_STUFFID}_0" name="reportradio_{$HOME_STUFFID}" value="horizontalbarchart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'horizontalbarchart'}checked{/if} onclick="changeGraphType({$HOME_STUFFID},'horizontalbarchart');">{'LBL_HOME_HORIZONTAL'|@getTranslatedString:'Home'}
			<input type="radio" id="reportradio_{$HOME_STUFFID}_1" name="reportradio_{$HOME_STUFFID}" value="verticalbarchart"{if $DASHDETAILS.$HOME_STUFFID.Chart eq 'verticalbarchart'}checked{/if} onclick="changeGraphType({$HOME_STUFFID},'verticalbarchart');">{'LBL_HOME_VERTICAL'|@getTranslatedString:'Home'}
			<input type="radio" id="reportradio_{$HOME_STUFFID}_2" name="reportradio_{$HOME_STUFFID}" value="piechart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}checked{/if} onclick="changeGraphType({$HOME_STUFFID},'piechart');">{'LBL_HOME_PIE'|@getTranslatedString:'Home'}
		</td>
	</tr>
	<tr>
		<td  valign="top" align="center" class="homePageMatrixHdr" nowrap style="height:28px;" width="40%">
			<input type="button" name="save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " class="crmbutton small save" onclick="saveEditReportCharts({$HOME_STUFFID})">
			<input type="button" name="cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
		</td>
	</tr>
{/if}
	</tr>
	</table>
</div>
<input type=hidden id="test_{$HOME_STUFFID}" value={$HOME_STUFFTYPE}/>
{if $HOME_STUFFTYPE eq "Module"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.ModuleName}"/>
	<input type=hidden id=cvid_{$HOME_STUFFID} value="{$HOME_STUFF.cvid}">
	{assign var='cvid' value=$HOME_STUFF.cvid}
	{assign var='modulename' value=$HOME_STUFF.ModuleName}
	<table class="slds-table slds-table_bordered">
	<thead>
	<tr>
		<th style="width:5%;">&nbsp;</th>
		{foreach item=header from=$HOME_STUFF.Header}
			<th><b>{$header}</b></th>
		{/foreach}
	</tr>
	</thead>
	<tbody>
	{if $HOME_STUFF.Entries|@count > 0}
		{foreach item=row key=crmid from=$HOME_STUFF.Entries}
		<tr>
			<td>
				<a href="index.php?module={$HOME_STUFF.ModuleName}&action=DetailView&record={$crmid}">
					<img src="{'bookMark.gif'|@vtiger_imageurl:$THEME}" style="max-width:unset;" alt="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}"/>
				</a>
			</td>
			{foreach item=element from=$row}
			<td> {$element}</td>
			{/foreach}
		</tr>
		{/foreach}
	{else}
		<tr>
		<td colspan="20"><div class="componentName">{$APP.LBL_NO_DATA}</div></td>
		</tr>
	{/if}
	</tbody>
	</table>
{elseif $HOME_STUFFTYPE eq "CustomWidget"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.ModuleName}"/>
	<input type=hidden id=cvid_{$HOME_STUFFID} value="{$HOME_STUFF.cvid}">
	<table border=0 cellspacing=0 cellpadding=2 width=100%>
	{assign var='cvid' value=$HOME_STUFF.cvid}
	{assign var='modulename' value=$HOME_STUFF.ModuleName}
	<tr>
		<td width=4%>
			&nbsp;
		</td>
		{foreach item=header from=$HOME_STUFF.Header}
		<td width=40% align="left">
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$header}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
		</td>
		{/foreach}
	</tr>
		{foreach item=row key=crmid from=$HOME_STUFF.Entries}
		<tr>
			<td width=4%>
				&nbsp;
			</td>
			{foreach item=element from=$row name=aggrow}
			<td {if $smarty.foreach.aggrow.last}style="text-align: right;"{/if} nowrap width=40%>
				{$element}
			</td>
			{/foreach}
		</tr>
		{/foreach}
	</table>

{elseif $HOME_STUFFTYPE eq "Default"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{if isset($HOME_STUFF.Details.ModuleName)}{$HOME_STUFF.Details.ModuleName}{/if}"/>
	{if is_array($HOME_STUFF.Details.Entries) && $HOME_STUFF.Details.Entries|@count > 0}
		{if isset($smarty.request.standalone)}
		<div class="slds-card slds-m-around_small">
		{/if}
		<table class="slds-table slds-table_bordered">
		<thead>
		<tr>
			{foreach item=header from=$HOME_STUFF.Details.Header}
				<th><b>{$header}</b></th>
			{/foreach}
		</tr>
		</thead>
		<tbody>
		{foreach item=row key=crmid from=$HOME_STUFF.Details.Entries}
			<tr>
				{foreach item=element from=$row}
				<td> {$element}</td>
				{/foreach}
			</tr>
		{/foreach}
		</tbody>
		</table>
		{if isset($smarty.request.standalone)}
		</div>
		{/if}
	{else}
		<div class="componentName">{$APP.LBL_NO_DATA}</div>
	{/if}

{elseif $HOME_STUFFTYPE eq "RSS"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.Entries.More}"/>
	<table border=0 cellspacing=0 cellpadding=2 width=100%>
		{foreach item="details" from=$HOME_STUFF.Entries.Details}
			<tr>
				<td align="left">
					<a href="{$details.1}" target="_blank">
						{$details.0|truncate:50}...
					</a>
				</td>
			</tr>
		{/foreach}
	</table>

{elseif $HOME_STUFFTYPE eq "DashBoard"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$DASHDETAILS[$HOME_STUFFID].DashType}"/>
	<table border=0 cellspacing=0 cellpadding=5 width=100%>
		<tr>
			<td align="left">{$HOME_STUFF}</td>
		</tr>
	</table>
{elseif $HOME_STUFFTYPE eq 'ReportCharts' && isset($HOME_STUFF.error)}
	{$HOME_STUFF.error}
{elseif $HOME_STUFFTYPE eq "ReportCharts"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$DASHDETAILS[$HOME_STUFFID].ReportId}"/>
	<table border=0 cellspacing=0 cellpadding=5 width=100%>
		<tr>
			<td align="left">
			<canvas id="homechart{$HOME_STUFFID}" style="width:500px;height:250px;margin:auto;padding:10px;"></canvas>
<script type="text/javascript">
window.doChart{$HOME_STUFFID} = function(charttype) {ldelim}
	let stuffchart = document.getElementById('homechart{$HOME_STUFFID}');
	let stuffcontext = stuffchart.getContext('2d');
	stuffcontext.clearRect(0, 0, stuffchart.width, stuffchart.height);
{literal}
	let chartDataObject = {
		labels: [{/literal}{foreach item=LABEL name=chartlabels from=$HOME_STUFF.xaxisData}"{$LABEL}"{if not $smarty.foreach.chartlabels.last},{/if}{/foreach}{literal}],
		datasets: [{
			data: [{/literal}{foreach item=CVALUE name=chartvalues from=$HOME_STUFF.yaxisData}"{$CVALUE}"{if not $smarty.foreach.chartvalues.last},{/if}{/foreach}{literal}]
		}]
	};
	const arrSum = chartDataObject.datasets[0].data.reduce((a,b) => Number(a) + Number(b), 0);
	const maxnum = Math.max.apply(Math, chartDataObject.datasets[0].data);
	const maxgrph = Math.ceil(maxnum + (6 * maxnum / 100));
	Chart.scaleService.updateScaleDefaults('linear', {
		ticks: {
			min: 0,
			max: maxgrph,
			precision: 0
		}
	});{/literal}
	{if !empty($GRAPHCOLORSCHEME) && $DASHDETAILS.$HOME_STUFFID.Chart neq 'piechart'}
	chartDataObject.datasets[0].backgroundColor = Chart['colorschemes'].{$GRAPHCOLORSCHEME};
	{/if}
	window.schart{$HOME_STUFFID} = new Chart(stuffchart,{
		type: charttype,
		data: chartDataObject,
		options: {
			plugins: {
				{if !empty($GRAPHCOLORSCHEME) && $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}
				colorschemes: {
					scheme: '{$GRAPHCOLORSCHEME}'
				},
				{/if}
				datalabels: {
					{if $GRAPHSHOW=='None'}
					display: false,
					{/if}
					color: '{$GRAPHSHOWCOLOR}',
					font: {
						size: 14,
						weight: 'bold'
					},
					{if $GRAPHSHOW=='Percentage' || $GRAPHSHOW=='ValuePercentage'}
					formatter: function(value, context) {
						{if $GRAPHSHOW=='ValuePercentage'}
						return value + ' (' + Math.round(value*100/arrSum) + '%)';
						{else}
						return Math.round(value*100/arrSum) + '%';
						{/if}
					}
					{/if}{literal}
				}
			},
			responsive: true,
			legend: {
				position: 'right',
				display: (charttype=='pie'),
				labels: {
					fontSize: 11,
					boxWidth: 18
				}
			}
		}
	});
	stuffchart.addEventListener('click',function(evt) {
		let activePoint = schart{/literal}{$HOME_STUFFID}{literal}.getElementAtEvent(evt);
		let clickzone = {
			{/literal}{foreach item=CLICKVALUE key=CLICKINDEX name=clickvalues from=$HOME_STUFF.targetLink}{$CLICKINDEX}:"{$CLICKVALUE}"{if not $smarty.foreach.clickvalues.last},{/if}{/foreach}{literal}
		};
		let a = document.createElement("a");
		a.target = "_blank";
		a.href = clickzone[activePoint[0]._index];
		document.body.appendChild(a);
		a.click();
	});
}
{/literal}
{if $DASHDETAILS.$HOME_STUFFID.Chart eq 'horizontalbarchart'}
let charttype = 'horizontalBar';
{elseif $DASHDETAILS.$HOME_STUFFID.Chart eq 'verticalbarchart'}
let charttype = 'bar';
{elseif $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}
let charttype = 'pie';
{else}
let charttype = 'verticalbarchart';
{/if}
doChart{$HOME_STUFFID}(charttype);
</script>
			</td>
		</tr>
	</table>
{/if}
{if isset($HOME_STUFF.Details) && $HOME_STUFF.Details|@is_array == 'true'}
<input id='search_qry_{$HOME_STUFFID}' name='search_qry_{$HOME_STUFFID}' type='hidden' value='{if isset($HOME_STUFF.Details.search_qry)}{$HOME_STUFF.Details.search_qry}{/if}' />
{/if}
