<script type="text/javascript">
window.addEventListener('DOMContentLoaded', () => {
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.loader('hide');
});
wizard.Suboperation[{$WizardStep}] = '{$WizardSuboperation}';
document.getElementById('codewithhbtnswitch').remove();
</script>
<div id="calendar-{$WizardStep}"></div>