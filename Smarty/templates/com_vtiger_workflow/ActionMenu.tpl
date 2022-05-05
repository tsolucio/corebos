<div class="slds-button-group" role="group">
<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-button_last">
<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" title="{'LBL_ACTIONS'|@getTranslatedString}" style="color:#0070d2;width:5rem;" type="button">
{'LBL_ACTIONS'|@getTranslatedString}
<svg class="slds-button__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
</svg>
<span class="slds-assistive-text">{'LBL_ACTIONS'|@getTranslatedString}</span>
</button>
<div class="slds-dropdown slds-dropdown_right">
<ul class="slds-dropdown__list" role="menu">
	<li class="slds-dropdown__item" role="presentation">
	<a href="javascript:void(0);" role="menuitem" onclick="wfActivateList();">
	<span class="slds-truncate" title="{$APP.LBL_ACTIVATE}">
	<span class="slds-assistive-text">{$APP.LBL_ACTIVATE}</span>
	<span>{$APP.LBL_ACTIVATE}</span>
	</span>
	</a>
	</li>
	<li class="slds-dropdown__item" role="presentation">
	<a href="javascript:void(0);" role="menuitem" onclick="wfDeactivateList();">
	<span class="slds-truncate" title="{$APP.LBL_DEACTIVATE}">
	<span class="slds-assistive-text">{$APP.LBL_DEACTIVATE}</span>
	<span>{$APP.LBL_DEACTIVATE}</span>
	</span>
	</a>
	</li>
	<li class="slds-dropdown__item" role="presentation">
	<a href="javascript:void(0);" role="menuitem" onclick="gotourl('index.php?module=com_vtiger_workflow&action=Import');">
	<span class="slds-truncate" title="{$APP.LBL_IMPORT}">
	<span class="slds-assistive-text">{$APP.LBL_IMPORT}</span>
	<span>{$APP.LBL_IMPORT}</span>
	</span>
	</a>
	</li>
	<li class="slds-dropdown__item" role="presentation">
	<a href="javascript:void(0);" role="menuitem" onclick="wfExportList();">
	<span class="slds-truncate" title="{$APP.LBL_EXPORT}">
	<span class="slds-assistive-text">{$APP.LBL_EXPORT}</span>
	<span>{$APP.LBL_EXPORT}</span>
	</span>
	</a>
	</li>
	<li class="slds-dropdown__item" role="presentation">
	<a class="slds-has-error" href="javascript:void(0);" role="menuitem" onclick="wfDeleteList();">
	<span class="slds-truncate" title="{'LBL_DELETE'|@getTranslatedString:'Users'}">
	<span class="slds-assistive-text">{'LBL_DELETE'|@getTranslatedString:'Users'}</span>
	<span>{'LBL_DELETE'|@getTranslatedString:'Users'}</span>
	</span>
	</a>
	</li>
</ul>
</div>
</div>
</div>