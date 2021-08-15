<div class="slds-page-header">
<div class="slds-page-header__row">
<div class="slds-page-header__col-title">
<div class="slds-media">
<div class="slds-media__figure">
<span class="slds-icon_container" title="{'SyncHelpDesk'|@getTranslatedString:$MODULE}">
<img src="include/LD/assets/icons/utility/sync_60.png" alt="{'SyncHelpDesk'|@getTranslatedString:$MODULE}" width="48" height="48" border="0" title="{'SyncHelpDesk'|@getTranslatedString:$MODULE}">
<span class="slds-assistive-text">{'SyncHelpDesk'|@getTranslatedString:$MODULE}</span>
</span>
</div>
<div class="slds-media__body">
<div class="slds-page-header__name">
<div class="slds-page-header__name-title">
<h1>
<span class="slds-page-header__title slds-truncate" title="{'SyncHelpDesk'|@getTranslatedString:$MODULE}">{'SyncHelpDesk'|@getTranslatedString:$MODULE}</span>
</h1>
</div>
</div>
<p class="slds-page-header__name-meta">{'SyncHelpDeskDescription'|@getTranslatedString:$MODULE}</p>
</div>
</div>
</div>
</div>
</div>
<form name="myform" action="index.php" method="POST">
	<input type="hidden" name="module" value="ServiceContracts">
	<input type="hidden" name="action" value="HDSync">
	<input type="hidden" name="mode" value="Save">
	<div class="slds-form-element slds-m-top_small">
		<label class="slds-checkbox_toggle slds-grid">
		<span class="slds-form-element__label slds-m-bottom_none">{'SyncHelpDesk'|@getTranslatedString:$MODULE}</span>
		<input type="checkbox" name="synchd" aria-describedby="synchd" {$hdsyncactive} onchange="document.myform.submit();" />
		<span id="synchd" class="slds-checkbox_faux_container" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on"></span>
		<span class="slds-checkbox_off"></span>
		</span>
		</label>
	</div>
</form>
