{if $NUMBER > 0}
	<select class='slds-select' size=6 id='dqrevision' name='dqrevision' ondblclick="dqrevRecover('{$ID}','{$MODULE}');">
	{foreach key=label item=key from=$REVISIONES}
		<option value='{$key.unique}'>{$key.revision} ({$key.modifiedtime})</option>
	{/foreach}
	</select>
{/if}
<div class="slds-grid slds-gutters slds-grid_vertical slds-m-top_x-small">
{if $NUMBER > 0}
<div class="slds-col slds-size_1-of-1 slds-m-bottom_x-small">
	<button class="slds-button slds-button_brand slds-button_stretch" onClick="dqrevRecover('{$ID}','{$MODULE}');">{'Recover'|@getTranslatedString:'Recover'}</button>
</div>
{/if}
<div class="slds-col slds-size_1-of-1 slds-m-bottom_x-small">
	<button class="slds-button slds-button_success slds-button_stretch" onClick="dqrevCreate('{$ID}','{$MODULE}');">{'CRevision'|@getTranslatedString:'CRevision'}</button>
</div>
</div>
<div class="cb-alert-info" id="dqrevisionmsg" style="display:none;">{'CreatingRevision'|@getTranslatedString:'CreatingRevision'}</div>
