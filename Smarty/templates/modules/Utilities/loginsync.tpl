{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#change_record_type"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<form role="form" action="index.php" method="post" id="srvsform">
	<input type="hidden" name="module" value="Utilities">
	<input type="hidden" name="action" value="integration">
	<input type="hidden" name="_op" value="setconfigloginsync">
	<div class="slds-p-around_x-small slds-form-element slds-modal__content slds-app-launcher__content" id="funcdiv">
		<div class="slds-p-around_x-small slds-grid slds-gutters slds-grid_vertical">
			<div class="slds-col slds-size_10-of-12 slds-form-element">
				<label class="slds-form-element__label" for="servers">{'cbInstalls'|@getTranslatedString:'Utilities'}</label>
				<div class="slds-form-element__control" id="serversdiv">
					{foreach item=srvpk key=srv from=$SERVERS}
						<div style="display:flex;" data-name="serverrow">
							<div
							class="slds-m-top_xx-small slds-m-right_xx-small slds-theme_{if $srvpk}success{else}error{/if}"
							style="width:18px;height:30px;text-align:center;padding-top:9px;"
							title="{if $srvpk}{'PrivateKeyIsSet'|@getTranslatedString:'Utilities'}{else}{'PrivateKeyNotSet'|@getTranslatedString:'Utilities'}{/if}"
							>
								<input type="radio" name="srvsetpk" value="{$srv}" />
							</div>
							<input type="text" name="server[]" class="slds-m-top_xx-small slds-input" onchange="setParamInputs();" value="{$srv}"/>
						</div>
					{/foreach}
						<div style="display:flex;" data-name="serverrow">
							<div class="slds-m-top_xx-small slds-m-right_xx-small slds-theme_error" style="width:18px;height:30px;text-align:center;padding-top:9px;">
								<input type="radio" name="srvsetpk" value="" />
							</div>
							<input type="text" name="server[]" class="slds-m-top_xx-small slds-input" onchange="setParamInputs();" value=""/>
						</div>
				</div>
			</div>
			<div class="slds-col slds-size_12-of-12 slds-form-element slds-m-top_x-small" style="display:flex;">
			<button
				class="slds-button slds-button_brand slds-m-right_xx-small"
				style="width:250px;"
				title="{'setPrivateKey'|@getTranslatedString:'Utilities'}"
				onclick="savePrivateKey()"
				type="button"
				name="button">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>
					{'setPrivateKey'|@getTranslatedString:'Utilities'}
			</button>
			<input type="text" id="spkey" name="spkey" class="slds-m-top_xx-small slds-input" value=""/>
			</div>
			<div class="slds-col slds-size_10-of-12 slds-form-element slds-m-top_x-small">
			<button
				class="slds-button slds-button_success"
				title="{'LBL_SAVE_BUTTON_TITLE'|@getTranslatedString:$MODULE}"
				accessKey="{'LBL_SAVE_BUTTON_KEY'|@getTranslatedString:$MODULE}"
				onclick="saveServers()"
				type="button"
				name="button">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>
					{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}
			</button>
			</div>
		</div>
	</div>
	</form>
</div>
</section>

<script>
var i18nPkeyNotSet = '{'PrivateKeyNotSet'|@getTranslatedString:'Utilities'}';
var i18nNoneSelected = '{'noServerSelected'|@getTranslatedString:'Utilities'}';
var i18nnoServerValue = '{'noServerValue'|@getTranslatedString:'Utilities'}';
{literal}
var newRow = `<div class="slds-m-top_xx-small slds-m-right_xx-small slds-theme_error" style="width:18px;height:30px;text-align:center;padding-top:9px;" title="${i18nPkeyNotSet}">
	<input type="radio" name="srvsetpk" value="" />
</div>
<input type="text" name="server[]" class="slds-m-top_xx-small slds-input" onchange="setParamInputs();" value=""/>`;
{/literal}
function setParamInputs() {
	const prms = document.querySelectorAll('div[data-name="serverrow"]');
	var n = document.getElementById('serversdiv');
	for (var cnt=prms.length-1; cnt>=0; cnt--) {
		var srv = prms[cnt].querySelector('input[name="server[]"]');
		if (srv.value=='') {
			n.removeChild(prms[cnt]);
		}
	}
	var inp = document.createElement('div');
	inp.dataset.name='serverrow';
	inp.style.display='flex';
	inp.innerHTML=newRow;
	n.appendChild(inp);
}

function savePrivateKey() {
	const rsrv = document.querySelectorAll('input[name="server[]"]');
	var rbtn = document.querySelectorAll('input[name="srvsetpk"]');
	for (r=0; r<rbtn.length; r++) {
		rbtn[r].value = rsrv[r].value;
	}
	let pkey = document.getElementById('spkey');
	if (pkey.value == '') {
		ldsPrompt.show(alert_arr['ERROR'], i18nPkeyNotSet);
		return false;
	}
	rbtn = document.querySelectorAll('input[name="srvsetpk"]:checked');
	if (rbtn.length==0) {
		ldsPrompt.show(alert_arr['ERROR'], i18nNoneSelected);
		return false;
	}
	if (rbtn[0].value=='') {
		ldsPrompt.show(alert_arr['ERROR'], i18nnoServerValue);
		return false;
	}
	document.getElementById('srvsform').submit();
}

function saveServers() {
	const rbtn = document.querySelectorAll('input[name="srvsetpk"]');
	for (r=0; r<rbtn.length; r++) {
		rbtn[r].checked = false;
	}
	document.getElementById('srvsform').submit();
}
</script>
