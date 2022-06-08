<script type="text/javascript" src="include/chart.js/Chart.bundle.js"></script>
<div class="slds-card slds-m-around_small">
	<div class="slds-page-header">
		<div class="slds-page-header__row">
			<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__figure">
						<span class="slds-icon_container slds-icon-standard-opportunity" title="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}">
							<img src="modules/Documents/images/HardDrive4848.png" alt="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}" width="48" height="48" border="0" title="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}">
							<span class="slds-assistive-text">{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}</span>
						</span>
					</div>
					<div class="slds-media__body">
						<div class="slds-page-header__name">
							<div class="slds-page-header__name-title">
								<h1>
								<span class="slds-page-header__title slds-truncate" title="{'STORAGESIZE_CONFIGURATION'|@getTranslatedString:$MODULE}">{'STORAGESIZE_CONFIGURATION'|@getTranslatedString:$MODULE}</span>
								</h1>
							</div>
						</div>
						<p class="slds-page-header__name-meta">{'STORAGESIZE_CONFIGURATION_DESCRIPTION'|@getTranslatedString:$MODULE}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-grid slds-gutters">
		<div class="slds-col slds-size_1-of-2 slds-m-around_small">
		<form name="myform" action="index.php" method="GET">
			<input type="hidden" name="module" value="Documents">
			<input type="hidden" name="action" value="StorageConfig">
			<input type="hidden" name="formodule" value="Documents">
			<input type="hidden" name="mode" value="Save">
				<strong>{'Total'|@getTranslatedString:'Documents'}:</strong>&nbsp;&nbsp;{$SISTORAGESIZELIMIT} Gb<br>
				<strong>{'Occupied'|@getTranslatedString:'Documents'}:</strong>&nbsp;&nbsp;{$SISTORAGESIZE} Gb<br>
				<strong>{'Free'|@getTranslatedString:'Documents'}:</strong>&nbsp;&nbsp;{$SISTORAGESIZELIMIT-$SISTORAGESIZE} Gb<br>
				</td>
				{if !empty($coreBOSOnDemandActive)}
				<strong>{'NewSize'|@getTranslatedString:'Documents'}:</strong>
				<input type="text" name='storagenewsize' id='storagenewsize' style="width:55px;" maxlength=2 value="{$SISTORAGESIZELIMIT}" class="slds-input"> Gb<br>
				<div class="slds-checkbox" style="text-align:center;width:100%">
					<input type="checkbox" name="accept_charge" id="accept_charge">
					<label class="slds-checkbox__label" for="accept_charge">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-form-element__label">{'accept_charge'|@getTranslatedString:'Documents'}</span>
					</label>
				</div>
				<p width=90% align=center>
				<button class="slds-button slds-button_neutral" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" type="submit" onclick="return jQuery('#accept_charge').is(':checked');">
				{$APP.LBL_SAVE_BUTTON_LABEL}
				</button>
				</p>
				{include file=$LICENSEFILE}<br/><br/><br/>
				{/if}
		</form>
		</div>
		<div class="slds-col slds-size_1-of-2 slds-m-around_small">
			<div style="display: block; width: 570px; height: 285px;"><canvas id="chart-area"></canvas></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var config = {
		type: 'pie',
		data: {
			datasets: [{
				data: [
					{$SISTORAGESIZE},
					{$SISTORAGESIZELIMIT-$SISTORAGESIZE}
				],
				backgroundColor: [
					'#A52A2A',
					'#228B22'
				],
				label: '{'Total'|@getTranslatedString:'Documents'}'
			}],
			labels: [
				"{'Occupied'|@getTranslatedString:'Documents'}",
				"{'Free'|@getTranslatedString:'Documents'}"
			]
		},
		options: {
			responsive: true
		}
	};
	var ctx = document.getElementById('chart-area').getContext('2d');
	var storagegrph = new Chart(ctx, config);
</script>
