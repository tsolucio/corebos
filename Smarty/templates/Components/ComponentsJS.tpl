<script src="./include/components/loadjs.js"></script>
<script src="./include/components/ldsmodal.js"></script>
<script src="./include/components/ldsprompt.js"></script>
<script src="./include/components/toast-ui/pagination/tui-pagination.js"></script>
<script src="./include/components/toast-ui/tui-time-picker/tui-time-picker.js"></script>
<script src="./include/components/toast-ui/tui-date-picker/tui-date-picker.js"></script>
<script src="./include/components/toast-ui/grid/tui-grid.js"></script>
<script src="./include/components/checkboxrenderer.js"></script>
<script src="./include/components/Select2/js/select2.min.js"></script>
<script type="text/javascript">
<!-- Initialize components -->
let currentLang = gVTuserLanguage.substring(0, 2);
if (!['en', 'es', 'ko', 'pt'].includes(currentLang)) {
	currentLang = 'en';
}
tui.Grid.setLanguage(currentLang);
</script>
