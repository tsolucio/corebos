<script type="text/javascript">
wizard.WizardRelModules[{$step}] = '{$relatedmodules|json_encode}';
wizard.WizardEntityNames[{$step}] = '{$entitynames|json_encode}';
wizard.WizardCurrentModule[{$step}] = '{$formodule}';
wizard.WizardColumns[{$step}] = '{$Columns|json_encode}';
wizard.WizardActions[{$step}] = '{$WizardActions|json_encode}';
wizard.WizardMode[{$step}] = '{$WizardMode}';
wizard.WizardFilterBy[{$step}] = {$WizardFilterBy|json_encode};
wizard.WizardConditionQuery[{$step}] = '{$WizardConditionQuery}';
wizard.WizardValidate[{$step}] = {$WizardValidate};
wizard.WizardGoBack[{$step}] = {$WizardGoBack};
wizard.WizardRequiredAction[{$step}] = '{$WizardRequiredAction}';
wizard.WizardCustomFunction[{$step}] = '{$WizardCustomFunction}';
wizard.WizardSaveAction[{$step}] = '{$WizardSaveAction}';
wizard.WizardFilterFromContext[{$step}] = '{$WizardContext}';
wizard.ResetWizard[{$step}] = {$ResetWizard};
window.addEventListener('DOMContentLoaded', () => {
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.loader('hide');
});
</script>