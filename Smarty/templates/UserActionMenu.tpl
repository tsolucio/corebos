<div class="slds-button-group" role="group">
{if isset($EDIT_BUTTON)}
	{$EDIT_BUTTON}
{/if}
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
{if $IS_ADMIN eq 'true' && isset($DUPLICATE_BUTTON)}
	<li class="slds-dropdown__item" role="presentation">
	{$DUPLICATE_BUTTON}
	</li>
{/if}
{if $IS_ADMIN eq 'true' && !$mustChangePassword}
	<li class="slds-dropdown__item" role="presentation">
	<a class="slds-has-warning" href="javascript:void(0);" role="menuitem" onclick="VtigerJS_DialogBox.block();window.document.location.href='index.php?module=Users&action=UsersAjax&file=CalculatePrivilegeFiles&record={$ID}';">
	<span class="slds-truncate" title="{$MOD.LBL_RECALCULATE_BUTTON}">
	<span class="slds-assistive-text">{$MOD.LBL_RECALCULATE_BUTTON}</span>
	<span>{$MOD.LBL_RECALCULATE_BUTTON}</span>
	</span>
	</a>
	</li>
	<li class="slds-dropdown__item" role="presentation">
	<a href="javascript:void(0);" role="menuitem" onclick="gotourl('index.php?module=cbLoginHistory&action=ListView&page=1&user_list={$ID}');">
	<span class="slds-truncate" title="{$MOD.LBL_LOGIN_HISTORY_DETAILS}">
	<span class="slds-assistive-text">{$MOD.LBL_LOGIN_HISTORY_DETAILS}</span>
	<span>{$MOD.LBL_LOGIN_HISTORY_DETAILS}</span>
	</span>
	</a>
	</li>
	<li class="slds-dropdown__item" role="presentation">
	<a href="javascript:void(0);" role="menuitem" onclick="gotourl('index.php?module=cbAuditTrail&action=ListView&page=1&user_list={$ID}');">
	<span class="slds-truncate" title="{$MOD.LBL_VIEW_AUDIT_TRAIL}">
	<span class="slds-assistive-text">{$MOD.LBL_VIEW_AUDIT_TRAIL}</span>
	<span>{$MOD.LBL_VIEW_AUDIT_TRAIL}</span>
	</span>
	</a>
	</li>
{/if}
<li class="slds-dropdown__item" role="presentation">
<a href="javascript:void(0);" role="menuitem" onclick="gotourl('index.php?module=Utilities&action=integration&_op=getconfig2fa&user_list={$ID}');">
<span class="slds-truncate" title="{'GoTo2FAActivation'|getTranslatedString:'Utilities'}">
<span class="slds-assistive-text">{'GoTo2FAActivation'|getTranslatedString:'Utilities'}</span>
<span>{'GoTo2FAActivation'|getTranslatedString:'Utilities'}</span>
</span>
</a>
</li>
{if $ID neq 1 && $ID neq $CURRENT_USERID && !$cbodUserBlocked}
	<li class="slds-dropdown__item" role="presentation">
	<a class="slds-has-error" href="javascript:void(0);" role="menuitem" onclick="deleteUser({$ID});">
	<span class="slds-truncate" title="{$UMOD.LBL_DELETE}">
	<span class="slds-assistive-text">{$UMOD.LBL_DELETE}</span>
	<span>{$UMOD.LBL_DELETE}</span>
	</span>
	</a>
	</li>
{/if}
{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
	<li class="slds-has-divider_top-space" role="separator"></li>
	{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
		{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
		{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
		{if $customlink_label eq ''}
			{assign var="customlink_label" value=$customlink_href}
		{else}
			{* Pickup the translated label provided by the module *}
			{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
		{/if}
		<li class="slds-dropdown__item" role="presentation">
		<a href="{$customlink_href}" role="menuitem">
			<span class="slds-truncate" title="{$customlink_label}">
				<span class="slds-assistive-text">{$customlink_label}</span>
				<span>{$customlink_label}</span>
			</span>
		</a>
		</li>
		</tr>
	{/foreach}
{/if}
</ul>
</div>
</div>
</div>