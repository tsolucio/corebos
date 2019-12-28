<script src='include/js/clipboard.min.js'></script>
<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_11-of-12 slds-m-left_x-small mermaid" style="word-break: break-all;">
	{$QSQL}
	</div>
	<div class="slds-col slds-size_1-of-12 slds-m-top_x-small">
		<svg aria-hidden="true" class="slds-icon_small slds-button__icon slds-button__icon_small" data-clipboard-text="{$QSQL}" id="clipcopylink">
			<use xmlns:xlink="http://www.w3.org/1999/xlink" href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#copy_to_clipboard"></use>
		</svg>
	</div>
</div>
<script>
var clipcopyobject = new ClipboardJS('#clipcopylink');
clipcopyobject.on('success', function(e) { clipcopyclicked = false; });
clipcopyobject.on('error', function(e) { clipcopyclicked = false; });
</script>