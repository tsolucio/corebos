<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">
			{* {include file='Buttons_List.tpl'} *}
		</td>
	</tr>
</table>
<div class="slds-page-header" role="banner">
	<div class="slds-grid">
		<div class="slds-col slds-has-flexi-truncate">
			<div class="slds-media slds-no-space slds-grow">
				<div class="slds-media__figure">
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right--small slds-align-middle slds-truncate"
						title="{$TITLE_MESSAGE}">{$TITLE_MESSAGE}</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
{if $ISADMIN}
<form role="form" style="margin:0 100px;">
<input type="hidden" name="module" value="Utilities">
<input type="hidden" name="action" value="integration">
<input type="hidden" name="_op" value="setconfigsendgrid">
<div class="slds-form-element">
	<label class="slds-checkbox--toggle slds-grid">
	<span class="slds-form-element__label slds-m-bottom--none">{'_active'|@getTranslatedString:$MODULE}</span>
	<input type="checkbox" name="sendgrid_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
	<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
		<span class="slds-checkbox--faux"></span>
		<span class="slds-checkbox--on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
		<span class="slds-checkbox--off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
	</span>
	</label>
</div>
<div class="slds-grid slds-gutters">
    <div class="slds-col slds-size_1-of-2">
        <h1 class="slds-page-header__title">{'LBL_CONFIG_INCOMING_MAIL_SERVER'|@getTranslatedString:'vtsendgrid'}</h1>
        <h2 class="small">{'LBL_SUBSTITUTE_INCOMING_MAIL_SERVER'|@getTranslatedString:'vtsendgrid'}</h2>
        <hr />
        <br />
        <div class="slds-form-element">
            <div class="slds-form-element__control">
                <div class="slds-checkbox">
                <input type="checkbox" name="ic_mail_server_active" id="ic_mail_server_active" {if $ic_mail_server_active}checked{/if}/>
                <label class="slds-checkbox__label" for="ic_mail_server_active">
                    <span class="slds-checkbox_faux"></span>
                    <span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
                </label>
                </div>
            </div>
        </div>
         <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="ic_mail_server_displayname">{'LBL_SELECT_SERVER_TYPE'|@getTranslatedString:$MODULE}</label>
            <div class="slds-form-element__control">
                <select id="ic_mail_server_displayname" name="ic_mail_server_displayname" class="slds-input">
                    {* <option value="Contacts" {if $relateDealWith eq 'Contacts'}checked{/if}>{'Contacts'|@getTranslatedString:'Contacts'}</option>
                    <option value="Accounts" {if $relateDealWith eq 'Accounts'}checked{/if}>{'Accounts'|@getTranslatedString:'Accounts'}</option> *}
                </select>
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <font color="red">*</font>&nbsp;
            <label class="slds-form-element__label" for="ic_mail_server_name">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="ic_mail_server_name" name="ic_mail_server_name" class="slds-input" value="{$ic_mail_server_name}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="ic_mail_server_username">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="ic_mail_server_username" name="ic_mail_server_username" class="slds-input" value="{$ic_mail_server_username}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="ic_mail_server_password">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="password" id="ic_mail_server_password" name="ic_mail_server_password" class="slds-input" value="{$ic_mail_server_password}" />
            </div>
        </div>
         <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="ic_mail_server_refresh_time">{'LBL_REFRESH_TIME'|@getTranslatedString:$MODULE}</label>
            <div class="slds-form-element__control">
                <select id="ic_mail_server_refresh_time" name="ic_mail_server_refresh_time" class="slds-input">
                    {* <option value="Contacts" {if $relateDealWith eq 'Contacts'}checked{/if}>{'Contacts'|@getTranslatedString:'Contacts'}</option>
                    <option value="Accounts" {if $relateDealWith eq 'Accounts'}checked{/if}>{'Accounts'|@getTranslatedString:'Accounts'}</option> *}
                </select>
            </div>
        </div>
    </div>
    <div class="slds-col slds-size_1-of-2">
        <h1 class="slds-page-header__title">{'LBL_CONFIG_OUTGOING_MAIL_SERVER'|@getTranslatedString:'vtsendgrid'}</h1>
        <h2 class="small">{'LBL_SUBSTITUTE_OUTGOING_MAIL_SERVER'|@getTranslatedString:'vtsendgrid'}</h2>
        <hr />
        <br />
        <div class="slds-form-element">
            <div class="slds-form-element__control">
                <div class="slds-checkbox">
                <input type="checkbox" name="og_mail_server_active" id="og_mail_server_active" {if $og_mail_server_active}checked{/if}/>
                <label class="slds-checkbox__label" for="og_mail_server_active">
                    <span class="slds-checkbox_faux"></span>
                    <span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
                </label>
                </div>
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <font color="red">*</font>&nbsp;
            <label class="slds-form-element__label" for="og_mail_server_username">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="og_mail_server_username" name="og_mail_server_username" class="slds-input" value="{$og_mail_server_username}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="og_mail_server_username">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="og_mail_server_username" name="og_mail_server_username" class="slds-input" value="{$og_mail_server_username}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="og_mail_server_password">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="password" id="og_mail_server_password" name="og_mail_server_password" class="slds-input" value="{$og_mail_server_password}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="og_mail_server_from_email">{'LBL_FROM_EMAIL'|@getTranslatedString:$MODULE}</label>
            <div class="slds-form-element__control">
                <input type="text" id="og_mail_server_from_email" name="og_mail_server_from_email" class="slds-input" value="{$og_mail_server_from_email}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="og_mail_server_smtp_auth">{'LBL_REQUIRES_AUTHENTICATION'|@getTranslatedString:$MODULE}</label>
            <div class="slds-form-element__control">
                <select id="og_mail_server_smtp_auth" name="og_mail_server_smtp_auth" class="slds-input">
                    {* <option value="Contacts" {if $relateDealWith eq 'Contacts'}checked{/if}>{'Contacts'|@getTranslatedString:'Contacts'}</option>
                    <option value="Accounts" {if $relateDealWith eq 'Accounts'}checked{/if}>{'Accounts'|@getTranslatedString:'Accounts'}</option> *}
                </select>
            </div>
        </div>
    </div>
</div>
<br />
<div class="slds-m-top--large">
	<button type="submit" class="slds-button slds-button--brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
</div>
</form>
{/if}