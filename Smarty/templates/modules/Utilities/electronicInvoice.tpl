{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="eInvoice" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="eInvoice" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
	{if $ISADMIN}
		<form role="form" name="new_task" style="margin:0 100px;">
		<input type="hidden" name="module" value="Utilities">
		<input type="hidden" name="action" value="integration">
		<input type="hidden" name="_op" value="setconfigelectronicinvoice">
		<div class="slds-form-element">
			<label class="slds-checkbox_toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom_none">{'_active'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="electronicInvoice_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
			<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
				<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
			</span>
			</label>
		</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label">{getTranslatedString('PublicKey')}</label>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_8-of-12">
					<input id="publickey" name="publickey" type="hidden">
					<input id="publickey_display" class="slds-input" name="publickey_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$pubkey}"
						onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=publickey&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbcredentials%3Aadapter%3Aadapter%3AcbCredentials_Adapter%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22CredentialsFile%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-12">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button"
						onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=publickey&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbcredentials%3Aadapter%3Aadapter%3AcbCredentials_Adapter%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22CredentialsFile%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
					</button>
					<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button"
						onClick="this.form.publickey.value=''; this.form.publickey_display.value=''; return false;">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label">{getTranslatedString('PrivateKey')}</label>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_8-of-12">
					<input id="privatekeyid" name="privatekeyid" type="hidden">
					<input id="privatekeyid_display" class="slds-input" name="privatekeyid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$privkey}"
						onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=privatekeyid&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbcredentials%3Aadapter%3Aadapter%3AcbCredentials_Adapter%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22CredentialsFile%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-12">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button"
						onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=privatekeyid&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbcredentials%3Aadapter%3Aadapter%3AcbCredentials_Adapter%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22CredentialsFile%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
					</button>
					<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button"
						onClick="this.form.privatekeyid.value=''; this.form.privatekeyid_display.value=''; return false;">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label">{getTranslatedString('PFKKey')}	</label>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_8-of-12">
					<input id="pfkkeyid" name="pfkkeyid" type="hidden">
					<input id="pfkkeyid_display" class="slds-input" name="pfkkeyid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$pkey}"
						onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=pfkkeyid&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbcredentials%3Aadapter%3Aadapter%3AcbCredentials_Adapter%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22CredentialsFile%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-12">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button"
						onclick="return window.open('index.php?module=cbCredentials&action=Popup&html=Popup_picker&form=new_task&forfield=pfkkeyid&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbcredentials%3Aadapter%3Aadapter%3AcbCredentials_Adapter%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22CredentialsFile%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
					</button>
					<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button"
						onClick="this.form.pfkkeyid.value=''; this.form.pfkkeyid_display.value=''; return false;">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="passphrase">{'Passphrase'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="passphrase" name="passphrase" class="slds-input" value="{$pphrase}"/>
			</div>
		</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label">{getTranslatedString('Default Administrative Center')}</label>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_8-of-12">
					<input id="admcenter" name="admcenter" type="hidden">
					<input id="admcenter_display" class="slds-input" name="admcenter_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$acenter}"
						onclick="return window.open('index.php?module=AdministrativeCenter&action=Popup&html=Popup_picker&form=new_task&forfield=admcenter&srcmodule=GlobalVariable', 'vtlibui10wf', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-12">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button"
						onclick="return window.open('index.php?module=AdministrativeCenter&action=Popup&html=Popup_picker&form=new_task&forfield=admcenter&srcmodule=GlobalVariable', 'vtlibui10wf', cbPopupWindowSettings);">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
					</button>
					<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button"
						onClick="this.form.admcenter.value=''; this.form.admcenter_display.value=''; return false;">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label">{getTranslatedString('Account Map')}</label>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_8-of-12">
					<input id="accountmap" name="accountmap" type="hidden">
					<input id="accountmap_display" class="slds-input" name="accountmap_display" for="accountmap_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$accmap}"
						onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=accountmap&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbmap%3Amaptype%3Amaptype%3AcbMap_Map_Type%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22Mapping%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-12">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button"
						onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=accountmap&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbmap%3Amaptype%3Amaptype%3AcbMap_Map_Type%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22Mapping%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
					</button>
					<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button"
						onClick="this.form.accountmap.value=''; this.form.accountmap_display.value=''; return false;">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label">{getTranslatedString('Contact Map')}</label>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_8-of-12">
					<input id="contactmap" name="contactmap" type="hidden">
					<input id="contactmap_display" class="slds-input" name="contactmap_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$contmap}"
						onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=contactmap&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbmap%3Amaptype%3Amaptype%3AcbMap_Map_Type%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22Mapping%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-12">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|getTranslatedString}" type="button"
						onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=contactmap&srcmodule=GlobalVariable&query=true&search=true&searchtype=advance&advft_criteria=%5B%7B%22groupid%22%3A1%2C%22columnname%22%3A%22vtiger_cbmap%3Amaptype%3Amaptype%3AcbMap_Map_Type%20%3AV%22%2C%22comparator%22%3A%22e%22%2C%22value%22%3A%22Mapping%22%2C%22columncondition%22%3A%22%22%7D%5D', 'vtlibui10wf', cbPopupWindowSettings);">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_SELECT'|getTranslatedString}</span>
					</button>
					<button class="slds-button slds-button_icon" title="{'LBL_CLEAR'|getTranslatedString}" type="button"
						onClick="this.form.contactmap.value=''; this.form.contactmap_display.value=''; return false;">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|getTranslatedString}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="DefaultSignType">{'DefaultSignType'|@getTranslatedString:$MODULE}</label>
		</div>
		<span class="slds-radio">
			<input name="assigntype" id="assigntypeFACe" checked="checked" value="{$face}" type="radio">
			<label class="slds-radio__label" for="assigntypeFACe">
				<span class="slds-radio_faux"></span>
				<span class="slds-form-element__label">FACe</span>
			</label>
			<input name="assigntype" id="assigntypeFACB2B" value="{$facb2b}" type="radio">
			<label class="slds-radio__label" for="assigntypeFACB2B">
				<span class="slds-radio_faux"></span>
				<span class="slds-form-element__label">FACB2B</span>
			</label>
		</span>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="TimeStamp" style="font-size: 20px;"><b>{'Time Stamp'|@getTranslatedString:$MODULE}</b></label>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="EI_baseurl">{'EI_baseurl'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="EI_baseurl" name="EI_baseurl" class="slds-input" value="{$eibaseurl}"/>
			</div>
		</div>
		<div class="basic-auth-fields">
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="EI_username">{'EI_username'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="EI_username" name="EI_username" class="slds-input" value="{$eiuname}"/>
				</div>
			</div>
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="EI_password">{'EI_password'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="EI_password" name="EI_password" class="slds-input" value="{$eipass}"/>
				</div>
			</div>
		</div>
		<div class="slds-m-top_large">
			<button type="submit"  name="saveInvoiceE" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		</form>
	{/if}
	</div>
</div>
</section>