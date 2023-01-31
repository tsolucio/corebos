<script type="text/javascript">
window.addEventListener('DOMContentLoaded', () => {
	wizard.FormModule.push('{$ModuleName}');
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.FormModule.push('{$ModuleName}');
	wizard.loader('hide');
});
document.getElementById('codewithhbtnswitch').remove();
</script>
<style type="text/css">
	.slds-input__custom {
		border-top: 0px;
		border-left: 0px;
		border-right: 0px;
		border-radius: 0px;
   		-webkit-box-shadow: none !important;
    	box-shadow: none !important;
	}
</style>
<div class="slds-p-horizontal_medium">
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
					<input type="text" id="{$i.name}_formtemplate" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 5}
					<input type="date" id="{$i.name}_formtemplate" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 10}
					<input type="text" id="{$i.name}_formtemplate" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 14}
					<input type="time" id="{$i.name}_formtemplate" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 15 || $i.uitype eq 16}
					<select id="{$i.name}_formtemplate" class="slds-select slds-input__custom" {if $i.mandatory eq 1}required{/if}>
						{foreach from=$i.type.picklistValues item=$val}
							<option value="{$val.value}">{$val.label}</option>
						{/foreach}
					</select>
					{elseif $i.uitype eq 50}
					<input type="datetime-local" id="{$i.name}_formtemplate" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{elseif $i.uitype eq 53}
					<select id="{$i.name}_formtemplate" class="slds-select slds-input__custom" {if $i.mandatory eq 1}required{/if}>
						{foreach from=$i.type.assignto.users.options item=$val}
							<option value="{$val.userid}">{$val.username}</option>
						{/foreach}
					</select>
					{else}
					<input type="text" id="{$i.name}_formtemplate" class="slds-input slds-input__custom" {if $i.mandatory eq 1}required{/if}>
					{/if}
				</div>
			</div>
		</div>
		{/foreach}
	</div>
{/foreach}
</div>