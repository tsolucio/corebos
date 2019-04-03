<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">
			{include file='Buttons_List.tpl'}
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
<div class="slds-form-element slds-m-top--small">
	<label class="slds-form-element__label" for="sg_user">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
	<div class="slds-form-element__control">
		<input type="text" id="sg_user" name="sg_user" class="slds-input" value="{$sg_user}" />
	</div>
</div>
<div class="slds-form-element slds-m-top--small">
	<label class="slds-form-element__label" for="sg_pass">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
	<div class="slds-form-element__control">
		<input type="password" id="sg_pass" name="sg_pass" class="slds-input" value="{$sg_pass}" />
	</div>
</div>
<br />
<br />
<div class="slds-grid slds-gutters">
    <div class="slds-col slds-size_1-of-2">
        <h1 class="slds-page-header__title">{'TransEmail_title'|@getTranslatedString:'vtsendgrid'}</h1>
        <h2 class="small">{'TransEmail_subtitle'|@getTranslatedString:'vtsendgrid'}</h2>
        <hr />
        <br />
        <div class="slds-form-element">
            <div class="slds-form-element__control">
                <div class="slds-checkbox">
                <input type="checkbox" name="usesg_transactional" id="usesg_transactional" {if $usesg_transactional}checked{/if}/>
                <label class="slds-checkbox__label" for="usesg_transactional">
                    <span class="slds-checkbox_faux"></span>
                    <span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
                </label>
                </div>
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <font color="red">*</font>&nbsp;
            <label class="slds-form-element__label" for="srv_transactional">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="srv_transactional" name="srv_transactional" class="slds-input" value="{$srv_transactional}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="user_transactional">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="user_transactional" name="user_transactional" class="slds-input" value="{$user_transactional}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="pass_transactional">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="password" id="pass_transactional" name="pass_transactional" class="slds-input" value="{$pass_transactional}" />
            </div>
        </div>
    </div>
    <div class="slds-col slds-size_1-of-2">
        <h1 class="slds-page-header__title">{'MktEmail_title'|@getTranslatedString:'vtsendgrid'}</h1>
        <h2 class="small">{'MktEmail_subtitle'|@getTranslatedString:'vtsendgrid'}</h2>
        <hr />
        <br />
        <div class="slds-form-element">
            <div class="slds-form-element__control">
                <div class="slds-checkbox">
                <input type="checkbox" name="usesg_marketing" id="usesg_marketing" {if $usesg_marketing}checked{/if}/>
                <label class="slds-checkbox__label" for="usesg_marketing">
                    <span class="slds-checkbox_faux"></span>
                    <span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
                </label>
                </div>
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <font color="red">*</font>&nbsp;
            <label class="slds-form-element__label" for="srv_marketing">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="srv_marketing" name="srv_marketing" class="slds-input" value="{$srv_marketing}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="user_marketing">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="text" id="user_marketing" name="user_marketing" class="slds-input" value="{$user_marketing}" />
            </div>
        </div>
        <div class="slds-form-element slds-m-top--small">
            <label class="slds-form-element__label" for="pass_marketing">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
            <div class="slds-form-element__control">
                <input type="password" id="pass_marketing" name="pass_marketing" class="slds-input" value="{$pass_marketing}" />
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