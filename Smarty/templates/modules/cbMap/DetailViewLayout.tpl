
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
<input type="hidden" name="MapName" id="MapName" value="{$NameOFMap}">

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
									<button class="slds-button slds-button_small slds-button_neutral" id="SaveAsButton" onclick="saveDetailLayoutMapAction();">{'LBL_SAVE_LABEL'|@getTranslatedString}</button>
								</div>
							</div>
						</div>
					</article>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
	<div class="slds-p-around_x-small slds-grid slds-gutters">
		<div class="slds-col slds-size_2-of-4 slds-p-around_xxx-small">
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="msmodules" required name="msmodules" class="slds-select" onchange="detailViewSetValues(); document.getElementById('block_modulename').value=this.value;">
						{foreach item=arr from=$MODULES}
							<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="slds-col slds-size_2-of-4 slds-p-around_xxx-small">
			<button class="slds-button slds-button_neutral" id="addfield" onclick="fillBlocks('after_block');document.getElementById('newBlockcDiv').style.display='';">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
				</svg>{'LBL_ADD_BLOCK'|@getTranslatedString:'Settings'}
			</button>
		</div>
	</div>
	<div class="slds-p-around_x-small slds-form-element" id="blockDiv" >
		<div class="slds-p-around_x-small slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2 slds-form-element">
				<legend class="slds-form-element__label" for="fieldtype">{'Type'|@getTranslatedString:$MODULE}</legend>
				<div class="slds-select_container">
					<select class="slds-select" name="fieldtype" id="fieldtype" onChange="changeFieldTypeListener(this.value)">
						<option value="">{'Select type'|@getTranslatedString}</option>
						<option {if $type eq 'ApplicationFields'} selected {/if} value="ApplicationFields">Application Fields</option>
						<option {if $type eq 'FieldList'} selected {/if} value="FieldList">Field List</option>
						<option {if $type eq 'Widget'} selected {/if} value="Widget">Widget</option>
						<option {if $type eq 'RelatedList'} selected {/if} value="RelatedList">Related List</option>
						<option {if $type eq 'CodeWithHeader'} selected {/if} value="CodeWithHeader">Code With Header</option>
						<option {if $type eq 'CodeWithoutHeader'} selected {/if} value="CodeWithoutHeader">Code Without Header</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	{* New Block section *}
	<div class="slds-p-around_x-small slds-form-element" id="newBlockcDiv" style="display:none">
		<section role="dialog" tabindex="-1" aria-labelledby="modal-heading-01" aria-modal="true" aria-describedby="modal-content-id-1" class="slds-modal slds-fade-in-open">
			<div class="slds-modal__container">
				<header class="slds-modal__header">
					<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="{'Close'|@getTranslatedString:$MODULE}"
					onclick="document.getElementById('newBlockcDiv').style.display='none';">
						<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
						</svg>
						<span class="slds-assistive-text">{'Close'|@getTranslatedString:$MODULE}</span>
					</button>
					<h2 id="modal-heading-01" class="slds-modal__title slds-hyphenate">{'LBL_ADD_BLOCK'|@getTranslatedString:'Settings'}</h2>
				</header>
				<div class="slds-modal__content slds-p-around_medium" id="modal-content-id-1">
					<div class="slds-form-element slds-m-top_small">
						<label class="slds-form-element__label" for="block_modulename">{'LBL_MODULE'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="block_modulename" name="block_modulename" readonly value="" class="slds-input"/>
						</div>
					</div>
					<div class="slds-form-element slds-m-top_small">
						<label class="slds-form-element__label" for="blocklabel">{'LBL_BLOCK_NAME'|@getTranslatedString:'Settings'}</label>
						<div class="slds-form-element__control">
							<input type="text" id="blocklabel" name="blocklabel" value="" class="slds-input"/>
						</div>
					</div>
					<div class="slds-col slds-form-element slds-text-align_center">
						<legend class="slds-form-element__legend slds-form-element__label">{'LBL_AFTER'|@getTranslatedString:'Settings'}</legend>
						<div class="slds-form-element__control">
							<div class="slds-select_container">
								<select id="after_block" name="after_block" class="slds-select"></select>
							</div>
						</div>
					</div>
					<br/>
					<footer class="slds-modal__footer">
						<button class="slds-button slds-button_neutral" onclick="document.getElementById('newBlockcDiv').style.display='none';">{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
						<button class="slds-button slds-button_brand" onClick="saveNewBlock();">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
					</footer>
				</div>
			</div>
		</section>
		<div class="slds-backdrop slds-backdrop_open"></div>
	</div>
	{* AppField Div details *}
	<div class="slds-p-around_x-small slds-grid slds-gutters" id="AppFieldselectedDiv" style="display:none">
		<div class="slds-col slds-size_1-of-2 slds-form-element slds-p-around_xxx-small">
			<legend class="slds-form-element__legend slds-form-element__label">{'LBL_BLOCK_NAME'|@getTranslatedString:'Settings'}</legend>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="appfield_block" name="appfield_block" class="slds-select"></select>
				</div>
			</div>
		</div>
	</div>
	{* FieldList Div details *}
	<div class="slds-p-around_x-small slds-form-element" id="FieldListselectedDiv" style="display:none">
		<div class="slds-p-around_x-small slds-grid slds-gutters">
			<div class="slds-col slds-form-element slds-text-align_center">
				<div class="slds-form-element__control">
					<div class="slds-button-group" role="group">
						<button class="slds-button slds-button_neutral" id="addRowBtn" onclick="fillTempContainer('row')">{'Add Row'|@getTranslatedString:$MODULE}</button>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-grid slds-p-around_x-small slds-gutters">
			<div class="slds-col slds-size_2-of-4 slds-p-around_xxx-small">
				<select id='list_of_fields' class='slds-select'>
				</select>
			</div>
			<div class="slds-col slds-size_2-of-4 slds-p-around_xxx-small">
				<button class="slds-button slds-button_neutral" id="addfield" onclick="fillSelectedField();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
					</svg>{'Add field'|@getTranslatedString:'cbMap'}
				</button>
			</div>
		</div>
		<div class="slds-p-around_x-small slds-form-element slds-form-element" id="contentHolderDiv">
			<div class="slds-col slds-form-element slds-text-align_center">
				<div class="slds-form-element__control">
					<div class="slds-button-group" role="group">
						<input type="hidden" id="originvalue" name="originvalue" value="{if isset($originvalue)}{$originvalue}{else}row$${/if}"/>
						<button class="slds-button slds-button_text-destructive slds-button_outline-brand" id="erase" onclick="document.getElementById('content_holder').value=document.getElementById('originvalue').value">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#redo"></use>
							</svg>{'Reset content'|@getTranslatedString:'cbMap'}
						</button>
						<button class="slds-button slds-button_destructive slds-button_outline-brand" id="erase" onclick="document.getElementById('content_holder').value='row$$'">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
							</svg>{'Clear content'|@getTranslatedString:'cbMap'}
						</button>
					</div>
				</div>
			</div>
			<label class="slds-form-element__label" for="content_holder">{'Content Holder'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<textarea id="content_holder" class="slds-textarea">{if isset($originvalue)}{$originvalue}{else}row$${/if}</textarea>
			</div>
		</div>
	</div>
	{* Widget div details *}
	<div class="slds-p-around_x-small slds-form-element" id="WidgetDiv" style="display:none">
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="widloadfrom">Load from</label>
			<div class="slds-form-element__control">
				<input type="text" id="widloadfrom" name="widloadfrom" value="{if isset($widloadfrom)}{$widloadfrom}{/if}" class="slds-input"/>
			</div>
		</div>
	</div>
	{* RelatedList div details *}
	<div class="slds-p-around_x-small slds-form-element" id="RelatedListDiv" style="display:none">
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="rlidloadfrom">Load from</label>
			<div class="slds-form-element__control">
				<input type="text" id="rlidloadfrom" name="rlidloadfrom" value="{if isset($rlidloadfrom)}{$rlidloadfrom}{/if}" class="slds-input"/>
			</div>
		</div>
	</div>
	{* CodeWithHeader and CodeWithoutHeader div details *}
	<div class="slds-p-around_x-small slds-form-element" id="codeDiv" style="display:none">
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="loadfrom">Load from</label>
			<div class="slds-form-element__control">
				<input type="text" id="loadfrom" name="loadfrom" value="{if isset($loadfrom)}{$loadfrom}{/if}" class="slds-input"/>
			</div>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="handler_class">Handler class</label>
			<div class="slds-form-element__control">
				<input type="text" id="handler_class" name="handler_class" value="{if isset($handler_class)}{$handler_class}{/if}" class="slds-input"/>
			</div>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="handler">Handler Method</label>
			<div class="slds-form-element__control">
				<input type="text" id="handler" name="handler" value="{if isset($handler)}{$handler}{/if}" class="slds-input"/>
			</div>
		</div>
	</div>
</div>
<script src="modules/cbMap/generatemap/DetailViewLayoutMapping.js" type="text/javascript" charset="utf-8"></script>