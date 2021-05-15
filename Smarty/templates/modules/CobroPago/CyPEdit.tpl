<div class="slds-card slds-m-around_small">
	<div class="slds-page-header">
		<div class="slds-page-header__row">
			<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__figure">
						<span class="slds-icon_container slds-icon-standard-opportunity" title="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}">
							<img src="modules/CobroPago/settings.png" alt="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}" width="48" height="48" border="0" title="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}">
							<span class="slds-assistive-text">{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}</span>
						</span>
					</div>
					<div class="slds-media__body">
						<div class="slds-page-header__name">
							<div class="slds-page-header__name-title">
								<h1>
								<span class="slds-page-header__title slds-truncate" title="{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}">{'SERVER_CONFIGURATION'|@getTranslatedString:$MODULE}</span>
								</h1>
							</div>
						</div>
						<p class="slds-page-header__name-meta">{'PreventEdit'|@getTranslatedString:$MODULE}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form name="myform" action="index.php" method="POST">
		<input type="hidden" name="module" value="CobroPago">
		<input type="hidden" name="action" value="CobroPagoConfigServer">
		<input type="hidden" name="mode" value="Save">
		<div class="slds-form-element slds-m-around_small">
			<label class="slds-checkbox_toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom_none">{'PreventEdit'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="block_paid" aria-describedby="block_paid" {$ts_bpaid} onchange="document.myform.submit();" />
			<span id="block_paid" class="slds-checkbox_faux_container" aria-live="assertive">
			<span class="slds-checkbox_faux"></span>
			<span class="slds-checkbox_on"></span>
			<span class="slds-checkbox_off"></span>
			</span>
			</label>
		</div>
	</form>
</div>