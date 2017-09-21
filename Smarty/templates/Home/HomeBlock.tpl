
<div class='hide_tab' id="editRowmodrss_{$HOME_STUFFID}" style="position:absolute;top:30px;right:1px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small" valign="top">
		{if $HOME_STUFFTYPE eq "Module" || $HOME_STUFFTYPE eq "RSS" || $HOME_STUFFTYPE eq "Default"}
		<tr>
			<td align="left" class="homePageMatrixHdr" nowrap width="40%">
				{$MOD.LBL_HOME_SHOW}&nbsp;
				<select id="maxentries_{$HOME_STUFFID}" name="maxid" class="slds-select">
					{section name=iter start=1 loop=13 step=1}
						<option value="{$smarty.section.iter.index}" {if $HOME_STUFF.Maxentries==$smarty.section.iter.index} selected {/if}>
							{$smarty.section.iter.index}
						</option>
					{/section}
				</select>&nbsp;{$MOD.LBL_HOME_ITEMS}
			</td>
		</tr>
		<tr>
			<td align="right" class="homePageMatrixHdr" nowrap width=60%>
				<input type="button" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " name="save" class="slds-button slds-button--small slds-button_success" onclick="saveEntries('maxentries_{$HOME_STUFFID}')">
				<input type="button" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " name="cancel" class="slds-button slds-button--small slds-button--destructive" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			</td>
		</tr>
		{elseif $HOME_STUFFTYPE eq "DashBoard"}
		<tr>
			<td  valign="top" align='center' class="homePageMatrixHdr" width=60%>
				<input type="radio" id="dashradio_0" name="dashradio_{$HOME_STUFFID}" value="horizontalbarchart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'horizontalbarchart'}checked{/if}>{'LBL_HOME_HORIZONTAL'|@getTranslatedString:'Home'}
				<input type="radio" id="dashradio_1" name="dashradio_{$HOME_STUFFID}" value="verticalbarchart"{if $DASHDETAILS.$HOME_STUFFID.Chart eq 'verticalbarchart'}checked{/if}>{'LBL_HOME_VERTICAL'|@getTranslatedString:'Home'}
				<input type="radio" id="dashradio_2" name="dashradio_{$HOME_STUFFID}" value="piechart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}checked{/if}>{'LBL_HOME_PIE'|@getTranslatedString:'Home'}
			</td>
		</tr>
		<tr>
			<td valign="top" align="center" class="homePageMatrixHdr" nowrap  width="40%">
				<input type="button" name="save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " class="slds-button slds-button--small slds-button_success" onclick="saveEditDash({$HOME_STUFFID})">
				<input type="button" name="cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " class="slds-button slds-button--small slds-button--destructive" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			</td>
		</tr>
		{elseif $HOME_STUFFTYPE eq "ReportCharts"}
			<td valign="top" align='center' class="homePageMatrixHdr" width=60%>
				<span class="slds-radio">
					<input type="radio" id="reportradio_{$HOME_STUFFID}_0" name="reportradio_{$HOME_STUFFID}" value="horizontalbarchart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'horizontalbarchart'}checked{/if} onclick="changeGraphType({$HOME_STUFFID},'horizontalbarchart');">
					<label class="slds-radio__label" for="reportradio_{$HOME_STUFFID}_0">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">{'LBL_HOME_HORIZONTAL'|@getTranslatedString:'Home'}</span>
				</span>
				<span class="slds-radio">
					<input type="radio" id="reportradio_{$HOME_STUFFID}_1" name="reportradio_{$HOME_STUFFID}" value="verticalbarchart"{if $DASHDETAILS.$HOME_STUFFID.Chart eq 'verticalbarchart'}checked{/if} onclick="changeGraphType({$HOME_STUFFID},'verticalbarchart');">
					<label class="slds-radio__label" for="reportradio_{$HOME_STUFFID}_1">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">{'LBL_HOME_VERTICAL'|@getTranslatedString:'Home'}</span>
				</span>
				<span class="slds-radio">
					<input type="radio" id="reportradio_{$HOME_STUFFID}_2" name="reportradio_{$HOME_STUFFID}" value="piechart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}checked{/if} onclick="changeGraphType({$HOME_STUFFID},'piechart');">
					<label class="slds-radio__label" for="reportradio_{$HOME_STUFFID}_2">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">{'LBL_HOME_PIE'|@getTranslatedString:'Home'}</span>
				</span>
			</td>
		</tr>
		<tr>
			<td valign="top" align="center" class="homePageMatrixHdr" nowrap width="40%">
				<input type="button" name="save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " class="slds-button slds-button--small slds-button_success" onclick="saveEditReportCharts({$HOME_STUFFID})">
				<input type="button" name="cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " class="slds-button slds-button--small slds-button--destructive" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			</td>
		</tr>
		{/if}
		</tr>
	</table>
</div>
<input type=hidden id="test_{$HOME_STUFFID}" value = {$HOME_STUFFTYPE}/>
{if $HOME_STUFFTYPE eq "Module"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.ModuleName}"/>
	<input type=hidden id=cvid_{$HOME_STUFFID} value="{$HOME_STUFF.cvid}">
	<table border=0 cellspacing=0 cellpadding=2 width=100%>
	{assign var='cvid' value=$HOME_STUFF.cvid}
	{assign var='modulename' value=$HOME_STUFF.ModuleName}
	<tr>
		<td width=5%>
			&nbsp;
		</td>
		{foreach item=header from=$HOME_STUFF.Header}
		<td align="left">
			<b>{$header}</b>
		</td>
		{/foreach}
	</tr>
	{if $HOME_STUFF.Entries|@count > 0}
		{foreach item=row key=crmid from=$HOME_STUFF.Entries}
 	<tr>
		<td>
			<a href="index.php?module={$HOME_STUFF.ModuleName}&action=DetailView&record={$crmid}">
				<img src="{'bookMark.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0" alt="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}"/>
			</a>
		</td>
			{foreach item=element from=$row}
		<td align="left"/>
			{$element}
		</td>
			{/foreach}
	</tr>
		{/foreach}
	{else}
		<div class="componentName">{$APP.LBL_NO_DATA}</div>
	{/if}
	</table>

{elseif $HOME_STUFFTYPE eq "Default"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{if isset($HOME_STUFF.Details.ModuleName)}{$HOME_STUFF.Details.ModuleName}{/if}"/>
	{if $HOME_STUFF.Details.Entries|@count > 0}
		<table border=0 cellspacing=0 cellpadding=2 width=100%>
		<tr>
			<td width=5%>&nbsp;</td>
			{foreach item=header from=$HOME_STUFF.Details.Header}
				<td align="left"><b>{$header}</b></td>
			{/foreach}
		</tr>
		{foreach item=row key=crmid from=$HOME_STUFF.Details.Entries}
			{if isset($HOME_STUFF.Details.Title)}
			<tr>
				<td>
					{if $HOME_STUFF.Details.Title.1 eq "My Sites"}
					<img src="{'bookMark.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0" alt="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}"/>
					{elseif $HOME_STUFF.Details.Title.1 neq "Key Metrics" && $HOME_STUFF.Details.Title.1 neq "My Group Allocation"}
					<img src="{'bookMark.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0" alt="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}"/>
					{elseif $HOME_STUFF.Details.Title.1 eq "Key Metrics"}
					<img src="{'bookMark.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0" alt="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION} "/>
					{elseif $HOME_STUFF.Details.Title.1 eq "My Group Allocation"}
					<img src="{'bookMark.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0" alt="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}"/>
					{/if}
				</td>
				{foreach item=element from=$row}
				<td align="left"/> {$element}</td>
				{/foreach}
			</tr>
			{/if}
		{/foreach}
	{else}
		<div class="componentName">{$APP.LBL_NO_DATA}</div>
	{/if}
	</table>

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
				data: [{/literal}{foreach item=CVALUE name=chartvalues from=$HOME_STUFF.yaxisData}"{$CVALUE}"{if not $smarty.foreach.chartvalues.last},{/if}{/foreach}{literal}],
				backgroundColor: [{/literal}{foreach item=CVALUE name=chartvalues from=$HOME_STUFF.yaxisData}getRandomColor(){if not $smarty.foreach.chartvalues.last},{/if}{/foreach}{literal}]
			}]
		};
		Chart.scaleService.updateScaleDefaults('linear', {
			ticks: {
				min: 0,
				max: Math.max.apply(Math, chartDataObject.datasets[0].data)+1
			}
		});
		window.schart{/literal}{$HOME_STUFFID}{literal} = new Chart(stuffchart,{
			type: charttype,
			data: chartDataObject,
			options: {
				responsive: true,
				legend: {
					position: "right",
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

</td></tr></table>
{/if}
{if isset($HOME_STUFF.Details) && $HOME_STUFF.Details|@is_array == 'true'}
<input id='search_qry_{$HOME_STUFFID}' name='search_qry_{$HOME_STUFFID}' type='hidden' value='{if isset($HOME_STUFF.Details.search_qry)}{$HOME_STUFF.Details.search_qry}{/if}' />
{/if}
