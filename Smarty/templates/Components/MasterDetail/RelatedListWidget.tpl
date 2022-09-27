<article class="slds-card">
	<div class="slds-card__header slds-grid">
	<header class="slds-has-flexi-truncate">
		<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
					<div class="slds-media">
						<div class="slds-media__body">
							<div class="slds-page-header__name">
							<div class="slds-page-header__name-title">
								<h1>
								<span class="slds-page-header__title slds-truncate">
									{if $title eq ''}
										{$originmodule} -> {$targetmodule}
									{else}
										{$title}
									{/if}
								</span>
								</h1>
							</div>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-page-header__col-actions">
					<div class="slds-page-header__controls">
						<div class="slds-page-header__control">
						<div class="slds-button-group" role="group">
							<button type="button" class="slds-button slds-button_neutral" onclick="relatedlistgrid.upsert('rlgrid{$originmodule}-{$targetmodule}', '{$originmodule}', '', {$CurrentRecord})">
								{$APP.LBL_CREATE_BUTTON_LABEL} {$originmodule}
							</button>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	</div>
	<div class="slds-card__body slds-card__body_inner">
	<div id="{$originmodule}-{$targetmodule}" style="display: inline;"></div>
	</div>
</article>
<script>
if (mapname === undefined) {
	var mapname = Array();
}
if (tooltip === undefined) {
	var tooltip = Array();
}
if (FieldLables === undefined) {
	var FieldLables = Array();
}
if (origin_related_fieldname === undefined) {
	var origin_related_fieldname = Array();
}
if (target_related_fieldname === undefined) {
	var target_related_fieldname = Array();
}
origin_related_fieldname['{$originmodule}'] = '{$origin_related_fieldname}';
target_related_fieldname['{$targetmodule}'] = '{$target_related_fieldname}';
target_related_fieldname['{$targetmodule}-{$targetmodule}'] = '{$sub_related_fieldname}';
tooltip['{$originmodule}'] = '{$tooltip}';
mapname['rlgrid{$originmodule}-{$targetmodule}'] = '{$mapname}';
FieldLables['{$originmodule}'] = '{$FieldLables}';
function loadRLGrid{$originmodule}{$targetmodule}() {
	RLInstance['rlgrid{$originmodule}-{$targetmodule}'] = new tui.Grid({
		el: document.getElementById('{$originmodule}-{$targetmodule}'),
		treeColumnOptions: {
			name: 'parentaction',
			useIcon: false,
			useCascadingCheckbox: false
		},
		columns: [
			{
				header: 'Parent',
				name: 'parentaction',
				renderer: {
					type: RLinkRender
				},
				width: 140
			},
			{foreach from=$RelatedListWidgetMap.targetmodule.listview item=rlfield name=mdhdr}
			{
				header: '{$rlfield.fieldinfo.label}',
				name: '{$rlfield.fieldinfo.name}',
				{if !empty($rlfield.sortingType)}
				sortingType: '{$rlfield.sortingType}',
				{/if}
				{if !empty($rlfield.editor)}
				editor: {$rlfield.editor},
				{/if}
				whiteSpace: 'normal',
				renderer: {
					type: RLinkRender
				},
			},
			{/foreach}
			{if !empty($cbgridactioncol)}
				{$cbgridactioncol}
			{/if}
		],
		data: {
			api: {
				readData: {
					url: 'index.php?module=Utilities&action=UtilitiesAjax&file=RelatedListWidgetActions&rlaction=list&pid={$ID}&mapname={$mapname}&currentmodule='+gVTModule,
					method: 'GET'
				}
			}
		},
		useClientSort: false,
		rowHeight: 'auto',
		bodyHeight: 'auto',
		scrollX: false,
		scrollY: false,
		columnOptions: {
			resizable: true
		},
		header: {
			align: 'left',
		},
		contextMenu: null
	});

	tui.Grid.applyTheme('striped');
	RLInstance['rlgrid{$originmodule}-{$targetmodule}'].on('editingFinish', relatedlistgrid.inlineedit);
}
loadRLGrid{$originmodule}{$targetmodule}();
</script>