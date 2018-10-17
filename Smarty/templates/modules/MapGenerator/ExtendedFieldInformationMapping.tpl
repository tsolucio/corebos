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

		App.ModulLabel='Module';
		App.FieldLabel='Value';
		App.DefaultValue='Value';
		ShowLocalHistoryExtendetFieldMap('LoadHistoryPopup','LoadShowPopup');
		ClickToshowSelectedFiledsExtendetFieldMap(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
		<tr class="blockStyleCss">
			<td class="detailViewContainer" valign="top"> 
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media--center slds-has-flexi-truncate">
								<h1 id="mapNameLabel" class="slds-page-header__title slds-m-right--small slds-truncate">
									{if $NameOFMap neq ''} {$NameOFMap} {/if}
								</h1>
								<p class="slds-text-heading--label slds-line-height--reset"> {$MOD.ExtendedFieldInformationMapping}  </p>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider">
										{if $HistoryMap neq ''}
											{* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
										{else}
											{* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
										{/if}
											<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName"   data-send="true"  data-loading="true" data-loading-divid="LoadingDivId"   data-send-url="MapGenerator,saveExtendetFieldInformation" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-send-savehistory-functionname="ShowLocalHistoryExtendetFieldMap" data-save-history-show-id-relation="LoadShowPopup" >{$MOD.CreateMap}</button>
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
								<div class="extended-field-info-container">
									<!-- Extended Field Information Choose module & field -->
									<div class="exim-choose-module-field-content">
										<div class="exim-choose-module">
											<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="FirstModule">{$MOD.ExtendedFieldInformationMappingModule}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select class="slds-select" id="FirstModule" data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-module="MapGenerator" data-select-relation-field-id="FirstFields" >{$FirstModuleSelected}</select>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Extended Field Information Insert name & value -->
									<div class="exim-insert-name-value-content">
										<!-- Second select -->
										<div class="exim-choose-field">
											<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="FirstFields">{$MOD.ExtendedFieldInformationMappingField}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select class="slds-select" id="FirstFields">{$FirstModuleFields}</select>
													</div>
												</div>
											</div>
										</div>
										<!-- Name input -->
										<div class="slds-form-element slds-text-align--left first-input">
											<label class="slds-form-element__label" for="NameInput">{$MOD.ExtendedFieldInformationMappingname}</label>
											<div class="slds-form-element__control">
												<input id="NameInput" class="slds-input" placeholder="{$MOD.ExtendedFieldInformationMappingInsertname}" type="text" />
											</div>
										</div>
										<!-- Field input -->
										<div class="slds-form-element slds-text-align--left second-input">
											<label class="slds-form-element__label" for="ValueInput">{$MOD.ExtendedFieldInformationMappingvalue}</label>
											<div class="slds-form-element__control">
												<input id="ValueInput" class="slds-input" placeholder="{$MOD.ExtendedFieldInformationMappingInsertvalue}" type="text" />
											</div>
										</div>
										<!-- Add button content -->
										<div class="add-button-content slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
											<button class="slds-button slds-button--small slds-button_icon" id="addPopupButton"
													data-add-button-popup="false" 
													data-add-type="Field" 
													data-add-relation-id="FirstModule,FirstFields,NameInput,ValueInput" 
													data-show-id="FirstFields" 
													data-show-modul-id="FirstModule" 
													data-add-button-validate="FirstFields" 
													onclick="addExtendetFieldMap(this);RestoreDataEXFIM(this)"
													data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="{$MOD.ClickAdd}">
												<img src="themes/images/btnL3Add.gif">
											</button>
										</div>
									</div>

									<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
									<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
									<input type="hidden" name="querysequence" id="querysequence" value="">
									<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
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
															<b>PopUp</b>
														</span>
													</h2>
												</div>
											</header>
										</div>
										<div class="slds-card__body slds-card__body--inner">
											<div id="contenitoreJoin">
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