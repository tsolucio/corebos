<script>
function saveModuleMapAction() {
	let params = 'mapid={$MapID}&moduleset=';
	const mods = document.getElementById('msmodules');
	const sels = document.getElementById('msmodules').querySelectorAll('option:checked');
	const vals = Array.from(sels).map(el => el.value);
	params = params + vals;
	saveMapAction(params);
}
</script>
<div>
	<table class="slds-table slds-no-row-hover slds-table-moz map-generator-table">
		<tbody>
			<tr id="DivObjectID">
				<td class="detailViewContainer" valign="top">
					<div>
						<article class="slds-card" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<header class="slds-media_center slds-has-flexi-truncate">
									<h1 id="mapNameLabel" class="slds-page-header__title slds-m-right_small slds-truncate">
										{if $NameOFMap neq ''} {$NameOFMap} {/if}
									</h1>
									<p class="slds-text-heading_label slds-line-height_reset">{$MapFields.maptype|@getTranslatedString:$MODULE}</p>
								</header>
								<div class="slds-no-flex">
									<div class="slds-section-title_divider">
										<button class="slds-button slds-button_small slds-button_neutral" id="SaveAsButton" onclick="saveModuleMapAction();">{'LBL_SAVE_LABEL'|@getTranslatedString}</button>
									</div>
								</div>
							</div>
						</article>
					</div>
					<div class="slds-truncate">
						<table class="slds-table slds-table_cell-buffer slds-no-row-hover slds-table_bordered slds-table_fixed-layout small detailview_table">
							<tr class="slds-line-height_reset">
								<td class="dvtCellLabel" valign="top">
									<fieldset>
										<legend align="center">{'LBL_MODULE'|@getTranslatedString}</legend>
										<div class="slds-form-element slds-text-align_left">
											<div class="slds-form-element__control">
												<div class="slds-select_container">
													<select id="msmodules" required name="msmodules" class="slds-select" multiple size=15>
														{foreach item=arr from=$MODULES}
															<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
														{/foreach}
													</select>
												</div>
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
		<input type="hidden" name="MapName" id="MapName" value="{$NameOFMap}">
	</div>
</div>