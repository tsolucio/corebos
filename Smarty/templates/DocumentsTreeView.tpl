<form name="massdelete" method="POST" id="massdelete" onsubmit="VtigerJS_DialogBox.block();">
	<input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
	<input name="idlist" id="idlist" type="hidden">
	<input name="action" id="action" type="hidden">
	<input name="massedit1x1" id="massedit1x1" type="hidden" value="">
	<input name="where_export" type="hidden" value="{$export_where}">
	<input name="step" type="hidden">
	<input name="excludedRecords" type="hidden" id="excludedRecords" value="">
	<input name="numOfRows" id="numOfRows" type="hidden" value="">
	<input name="allids" type="hidden" id="allids" value="{if isset($ALLIDS)}{$ALLIDS}{/if}">
	<input name="selectedboxes" id="selectedboxes" type="hidden" value="{$SELECTEDIDS}">
	<input name="allselectedboxes" id="allselectedboxes" type="hidden" value="{$ALLSELECTEDIDS}">
	<input name="current_page_boxes" id="current_page_boxes" type="hidden" value="{$CURRENT_PAGE_BOXES}">
</form>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
	let folders = JSON.parse('{$FOLDERS|json_encode}');
	let fid = folders[0][0];
	let fname = folders[0][1];
	DocumentsView.Folders(fid, fname);
}, false);
</script>
<div id="basicsearchcolumns" style="display:none;"></div>
<div class="slds-vertical-tabs">
	<ul class="slds-vertical-tabs__nav" role="tablist" aria-orientation="vertical" id="folder-list" style="height: 600px;overflow-y: auto;">
		{foreach from=$FOLDERS item=$folder}
		<li class="slds-vertical-tabs__nav-item" title="{$folder[1]}" role="presentation" onclick="DocumentsView.Folders({$folder[0]}, '{$folder[1]}')" id="folder-{$folder[0]}">
			<a class="slds-vertical-tabs__link" role="tab" tabindex="0" aria-selected="true" aria-controls="slds-vertical-tabs-0" id="slds-vertical-tabs-0__nav">
				<span class="slds-vertical-tabs__left-icon">
					<span class="slds-icon_container slds-icon-standard-opportunity">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#open_folder"></use>
						</svg>
					</span>
				</span>
				<span class="slds-truncate" title="{$folder[1]}">{$folder[1]}</span>
				<span class="slds-vertical-tabs__right-icon"></span>
			</a>
		</li>
		{/foreach}
	</ul>
	<div class="slds-vertical-tabs__content slds-show" id="slds-vertical-tabs-0" role="tabpanel" aria-labelledby="slds-vertical-tabs-0__nav">
		<div id="listview-tui-grid"></div>
	</div>
</div>