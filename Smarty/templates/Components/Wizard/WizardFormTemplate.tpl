<script type="text/javascript">
window.addEventListener('DOMContentLoaded', () => {
	wizard.FormModule.push('{$ModuleName}');
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.FormModule.push('{$ModuleName}');
	wizard.loader('hide');
});
wizard.Suboperation[{$WizardStep}] = '{$WizardSuboperation}';
document.getElementById('codewithhbtnswitch').remove();
</script>
<div class="slds-p-horizontal_medium" style="margin-top: -3%;">
{foreach from=$Rows item=$row key=$k}
	{if !empty($k)}
	<div class="slds-section slds-is-open">
		<h3 class="slds-section__title slds-theme_shade">
			<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
			</svg>
			<span class="slds-truncate slds-p-horizontal_small" title="{$k}">{$k}</span>
		</h3>
	</div>
	{/if}
	<div class="slds-grid slds-gutters slds-m-around_small">
		{foreach from=$row item=$i}
		<script type="text/javascript">
			wizard.FormFields.push('{$i.name}');
		</script>
		<div class="slds-col slds-m-bottom_large">
			<div class="slds-form-element">
				<label class="slds-form-element__legend slds-form-element__label">{$i.label} {if $i.mandatory eq 1}<abbr class="slds-required" title="required">* </abbr>{/if}</label>
				<div class="slds-form-element__control" id="{$i.name}_form_block">
					{if $i.uitype eq 1 || $i.uitype eq 2}
					<input type="text" data-label="{$i.label}" id="{$i.name}_formtemplate_{$WizardStep}" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 5}
					<input type="date" data-label="{$i.label}" id="{$i.name}_formtemplate_{$WizardStep}" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 10}
					<input id="{$i.name}_formtemplate_{$WizardStep}" data-label="{$i.label}" type="hidden" {if $i.mandatory eq 1}required{/if}>
					<span style="display:none;" id="{$i.name}_hidden"></span>
					<input class="slds-input slds-input__custom" value="" id="{$i.name}_display_{$WizardStep}" name="{$i.name}_display" readonly="" type="text" style="width: 90%;border:1px solid #c9c9c9"onclick="return window.open('index.php?module={$i.searchin}&action=Popup&html=Popup_picker&form=Wizard&forfield={$i.name}&srcmodule={$ModuleName}&forrecord=&index={$WizardStep}', 'vtlibui10', cbPopupWindowSettings);">
					<button class="slds-button slds-button_icon" title="Select" type="button" onclick="return window.open('index.php?module={$i.searchin}&action=Popup&html=Popup_picker&form=Wizard&forfield={$i.name}&srcmodule={$ModuleName}&forrecord=&index={$WizardStep}', 'vtlibui10', cbPopupWindowSettings);">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
						</svg>
						<span class="slds-assistive-text">Select</span>
					</button>
					<button class="slds-button slds-button_icon" type="button" onclick="">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
						</svg>
						<span class="slds-assistive-text">Clear</span>
					</button>
					{elseif $i.uitype eq 14}
					<input type="time" data-label="{$i.label}" id="{$i.name}_formtemplate_{$WizardStep}" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 15 || $i.uitype eq 16}
					<select id="{$i.name}_formtemplate_{$WizardStep}" data-label="{$i.label}" class="slds-select slds-input__custom" {if $i.mandatory eq 1}required{/if}>
						{foreach from=$i.type.picklistValues item=$val}
							<option value="{$val.value}">{$val.label}</option>
						{/foreach}
					</select>
					{elseif $i.uitype eq 50}
					<input type="datetime-local" data-label="{$i.label}" id="{$i.name}_formtemplate_{$WizardStep}" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 53}
					<select id="{$i.name}_formtemplate_{$WizardStep}" data-label="{$i.label}" class="slds-select slds-input__custom" {if $i.mandatory eq 1}required{/if}>
						{foreach from=$i.type.assignto.users.options item=$val}
							<option value="{$val.userid}">{$val.username}</option>
						{/foreach}
					</select>
					{else}
					<input type="text" id="{$i.name}_formtemplate_{$WizardStep}" data-label="{$i.label}" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{/if}
				</div>
			</div>
		</div>
		{/foreach}
	</div>
{/foreach}
</div>