<script type="text/javascript">
window.addEventListener('DOMContentLoaded', () => {
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.loader('hide');
});
document.getElementById('codewithhbtnswitch').remove();
</script>
{foreach from=$Rows item=$row key=$k}
{if !empty($k)}
<div class="slds-section slds-is-open">
	<h3 class="slds-section__title slds-theme_shade">
		<span class="slds-truncate slds-p-horizontal_small" title="{$k}">{$k}</span>
	</h3>
</div>
{/if}
<div class="slds-grid slds-gutters slds-m-around_small">
	{foreach from=$row item=$i}
	<div class="slds-col">
		<div class="slds-form-element">
			<label class="slds-form-element__legend slds-form-element__label">{$i.label} {if $i.mandatory eq 1}<abbr class="slds-required" title="required">* </abbr>{/if}</label>
			<div class="slds-form-element__control">
				<input type="text" name="{$i.name}" class="slds-input">
			</div>
		</div>
	</div>
	{/foreach}
</div>
{/foreach}