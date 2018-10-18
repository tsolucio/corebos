<div>

    {* <style scope>
         @import "modules/MapGenerator/css/WSstyle.css"; 
    </style> *}
    <div id="LoadingImage" style="display: none"><img src=""/></div>

    {if $HistoryMap neq ''}
        <script type="text/javascript">
            App.savehistoryar='{$HistoryMap}';
        </script>
    {/if}


{if $PopupJS neq ''}
	<script type="text/javascript">
		{foreach from=$PopupJS item=allitems key=key name=name}
            {foreach name=outer item=popi from=$allitems}  
                var temparray = {};
                {foreach key=key item=item from=$popi}
                    temparray['{$key}']='{$item}';
                {/foreach}
                App.popupJson.push({'{'}temparray{'}'});
                // console.log(temparray);
            {/foreach}
            HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
            App.popupJson.length=0;
        {/foreach}
		ShowLocalHistoryRelatedPanes('LoadHistoryPopup','LoadShowPopup');
		ClickToshowSelecteRelationPane(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
		App.countsaveMap=2;
		App.utils.UpdateMapNAme();
	</script>
{/if}

    <div id="ModalDiv">
        {if $Modali neq ''}
            <div>
            {$Modali}
            </div>
        {/if}
    </div>
	<table class="slds-table slds-no-row-hover slds-table-moz map-generator-table">
		<tbody>
			<tr class="blockStyleCss" id="DivObjectID">
				<td class="detailViewContainer" valign="top">
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<header class="slds-media--center slds-has-flexi-truncate">
									<h1 id="mapNameLabel" class="slds-page-header__title slds-m-right--small slds-truncate">
										{if $NameOFMap neq ''} {$NameOFMap} {/if}
									</h1>
									<p class="slds-text-heading--label slds-line-height--reset">{$MOD.RpRelatedPanes}</p>
								</header>
								<div class="slds-no-flex">
									<div class="actionsContainer mapButton">
										<div class="slds-section-title--divider">
											{if $HistoryMap neq ''} {* RpRelatedPanes *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
											{else} {* RpRelatedPanes *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
											{/if} &nbsp;
											<button class="slds-button slds-button--small slds-button--brand"  data-loading="true" data-loading-divid="LoadingDivId"  data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveRelatedPanes" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryRelatedPanes" >{$MOD.CreateMap}</button> 
										</div>
									</div>
								</div>
							</div>
						</article>
					</div>
					<div class="slds-truncate">
						<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
							<tr class="slds-line-height--reset map-generator-cell-container">
								<td class="dvtCellLabel" valign="top">
									<!-- THE MODULE Zone -->
									<div class="related-panes-container">
										<!-- Related Panes Origin Module -->
										<div class="rp-origin-module">
											<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="select-01"><strong class="slds-text-color--error">*</strong>{$MOD.RpOriginModule}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select class="slds-select" required id="FirstModule" data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup">
														{$FirstModuleSelected}
														<select>
													</div>
												</div>
											</div>
										</div>
										<!-- Panes -->
										<fieldset class="rp-panes-container">
											<legend class="rp-panes-title slds-text-align--center" align="center">{$MOD.RpPanes}</legend>
											<div class="rp-panes-body-content">
												<div class="rp-panes-body">
													<div class="slds-form-element slds-text-align--left" id="labelinputdiv">
														<label class="slds-form-element__label" for="rp-label"><strong class="slds-text-color--error">*</strong> {$MOD.RpLabel}</label>
														<div class="slds-form-element__control">
															<input id="rp-label" class="slds-input" required placeholder="Enter {$MOD.RpLabel} " type="text" />
														</div>
													</div>
													<div class="slds-form-element slds-text-align--left">
														<label class="slds-form-element__label" for="rp-panes-sequence"><strong class="slds-text-color--error">*</strong> {$MOD.RpSequence}</label>
														<div class="slds-form-element__control">
															<input id="rp-panes-sequence" class="slds-input" required  type="number" value="0" />
														</div>
													</div>
													<div class="slds-form-element slds-text-align--center">
														<div class="slds-form-element__control">
															<span class="slds-checkbox">
																<input name="options" id="MoreInformationChb" onchange="moreinformationchecked(this)" data-all-id="rp-label,rp-panes-sequence,rp-block-sequence,blockType,rp-block-loadfrom" value="on" type="checkbox" />
																<label class="slds-checkbox__label" for="MoreInformationChb">
																	<span class="slds-checkbox--faux"></span>
																	<span class="slds-form-element__label">{$MOD.RpMoreInformation}</span>
																</label>
															</span>
														</div>
													</div>
												</div>
												<!-- Blocks -->
												<fieldset class="rp-blocks-container">
													<legend class="rp-blocks-title slds-text-align--center" align="center">Blocks</legend>
													<div class="rp-blocks-body-content">
														<div class="rp-blocks-body_1">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="rp-label"> {$MOD.RpBlockLabel}</label>
																<div class="slds-form-element__control">
																	<input id="rp-block-label" class="slds-input" placeholder="Enter {$MOD.RpBlockLabel}" type="text" />
																</div>
															</div>
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="rp-block-sequence"> <strong class="slds-text-color--error">*</strong> {$MOD.RpBlockSequence}</label>
																<div class="slds-form-element__control">
																	<input id="rp-block-sequence" required class="slds-input" type="number" value="0" />
																</div>
															</div>
														</div>
														<div class="rp-blocks-body_2">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="FirstModule"> <strong class="slds-text-color--error">*</strong> {$MOD.RpBlockType}</label>
																<div class="slds-form-element__control">
																	<div class="slds-select_container">
																		<select id="blockType" required   name="mod" class="slds-select">
																			<option selected value="">Select Type</option>
																			<option selected value="RelatedList">RelatedList</option>
																			<option value="Widget">Widget</option>
																			<option value="CodeWithoutHeader">CodeWithoutHeader</option>
																			<option value="CodeWithHeader">CodeWithHeader</option>
																		</select>
																	</div>
																</div>
															</div>
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="rp-block-loadfrom"><strong class="slds-text-color--error">*</strong>  {$MOD.RpBlockLoadForm}</label>
																<div class="slds-form-element__control">
																	<input id="rp-block-loadfrom" class="slds-input" required placeholder="Enter {$MOD.RpBlockLoadForm} " type="text" />
																</div>
															</div>
														</div>
														<!-- Buttons -->
														<div class="rp-button-group-blocks">
															<button class="slds-button slds-button--small slds-button--brand"  onclick="AddPopupRelatedFieldBlock(this);RestoreDataRelatedFields(this);" data-add-button-popup="false" data-add-type="Block" data-add-button-validate="rp-block-label" data-show-id="FirstModule"  data-add-relation-id="FirstModule,rp-label,rp-panes-sequence,MoreInformationChb,rp-block-label,rp-block-sequence,blockType,rp-block-loadfrom"  data-div-show="LoadShowPopup"  id="addBlockButton"  >{$MOD.RpAddBlock}</button>
														</div>
													</div>
												</fieldset>
												<div class="rp-validation-buttons">
													<!-- WS validation Help Text Container -->
													<div class="rp-val-help slds-text-align--left">
														<label class="slds-form-element__label slds-text-color--error">
															{$MOD.RprequiredFileds}
														</label>
													</div>
													<!-- Buttons -->
													<div class="rp-button-group-panes">
														<button class="slds-button slds-button--small slds-button--brand" onclick="AddPopupRelatedFieldsPanes(this);RestoreDataRelatedFields(this,true);" data-add-type="Pane" data-add-relation-id="FirstModule,rp-label,rp-panes-sequence,MoreInformationChb,rp-block-label,rp-block-sequence,blockType,rp-block-loadfrom" data-add-panes="false" id="AddButtonPanes" data-div-show="LoadShowPopup" >{$MOD.RpAddPanes}</button>													</div>
												</div>
											</div>
										</fieldset>
									</div>
								</td>
								<td class="dvtCellInfo" align="left">
									<div class="flexipageComponent">
										<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2 class="header-title-container"> 
															<span class="slds-text-heading--small slds-truncate actionLabel">
																<b>Popup</b>
															</span> 
														</h2>
													</div>
												</header>
											</div>
											<div class="slds-card__body slds-card__body--inner">
												<div id="contenitoreJoin" class="rp-popup-zone">
													<div id="LoadShowPopup"></div>
												</div>
											</div>
											{*End div contenitorejoin*}
										</article>
									</div>
									<br/>
									<div class="flexipageComponent">
										<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2 class="header-title-container"> 
															<span class="slds-text-heading--small slds-truncate actionLabel">
																<b>History</b>
															</span> 
														</h2>
													</div>
												</header>
											</div>
											<div class="slds-card__body slds-card__body--inner">
												<div id="contenitoreJoin">
													<div id="LoadHistoryPopup"></div>
												</div>{*End div contenitorejoin*}
											</div>
										</article>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<div id="waitingIddiv"></div>
	<div id="contentJoinButtons" style="width: 70%;height: 100%;float: left;">
	</div>
	<div id="generatedquery">
		<div id="results" style="margin-top: 1%;"></div>
	</div>
	<div id="null"></div>
	<div>
		<div id="queryfrommap"></div>
	</div>
	<div>
		<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
		<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
		<input type="hidden" name="querysequence" id="querysequence" value="">
		<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
	</div>
</div>