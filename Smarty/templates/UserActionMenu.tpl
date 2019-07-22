<input type="button" onclick="gotourl('index.php?module=Utilities&action=integration&_op=getconfig2fa&user_list={$ID}');" value="{'GoTo2FAActivation'|getTranslatedString:'Utilities'}" class="crmButton small save"></input>
{if $IS_ADMIN eq 'true' && !$mustChangePassword}
	<input type="button" onclick="gotourl('index.php?module=cbLoginHistory&action=ListView&page=1&user_list={$ID}');" value="{$MOD.LBL_LOGIN_HISTORY_DETAILS}" class="crmButton small save"></input>
	<input type="button" onclick="gotourl('index.php?module=cbAuditTrail&action=ListView&page=1&user_list={$ID}');" value="{$MOD.LBL_VIEW_AUDIT_TRAIL}" class="crmButton small save"></input>
	<input type="button" onclick="VtigerJS_DialogBox.block();window.document.location.href = 'index.php?module=Users&action=UsersAjax&file=CalculatePrivilegeFiles&record={$ID}';" value="{$MOD.LBL_RECALCULATE_BUTTON}" class="crmButton small cancel"></input>
{/if}
{if $IS_ADMIN eq 'true' && isset($DUPLICATE_BUTTON)}
	{$DUPLICATE_BUTTON}
{/if}
{if isset($EDIT_BUTTON)}
	{$EDIT_BUTTON}
{/if}
{if $CATEGORY eq 'Settings' && $ID neq 1 && $ID neq $CURRENT_USERID && !$cbodUserBlocked}
	<input type="button" onclick="deleteUser({$ID});" class="crmButton small cancel" value="{$UMOD.LBL_DELETE}"></input>
{/if}
