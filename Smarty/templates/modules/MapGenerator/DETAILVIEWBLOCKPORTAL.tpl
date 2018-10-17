<div style="width: 100%;height: 100%">
	<div id="LoadingImage" style="display: none">
		<img src=""/>
</div>

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
								{if $key eq 'rows'}
									rows=new Array();
									allfieldsval=[];
									allfieldstetx=[];
										{foreach from=$item item=itemi key=keyes}
											checkifexist={};
											fieldsval=[];
											fieldstetx=[];
												{foreach from=$itemi.fields item=items key=key name=name}
												 fieldsval.push('{$items}');
											{/foreach}
											{foreach from=$itemi.texts item=items key=key name=name}
												 fieldstetx.push('{$items}');
											{/foreach}                    
											{literal}
												allfieldsval.push(fieldsval);
												allfieldstetx.push(fieldstetx);
											{/literal}
									{/foreach}
									{literal}
										temparray["rows"]={fields:allfieldsval,texts:allfieldstetx};
									{/literal}
								{else}
								 temparray['{$key}']='{$item}';
								{/if}
						{/foreach}
						App.popupJson.push({'{'}temparray{'}'});
						// console.log(temparray);
					{/foreach}
					 HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
					App.popupJson.length=0;
			{/foreach}

		if (App.SaveHistoryPop.length>0)
		{
			 $('#LoadHistoryPopup div').remove();
				for (var i = 0; i <=App.SaveHistoryPop.length - 1; i++) {
					$('#LoadHistoryPopup').append(showLocalHistory(i,App.SaveHistoryPop[i].PopupJSON[0].temparray['FirstModule'],'LoadHistoryPopup','LoadShowPopup'));
				}
			 App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryCorrect);
		}else{
			 App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryError);
		}

			ShowHistoryData(App.SaveHistoryPop.length-1,'LoadShowPopup');
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
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.DETAILVIEWBLOCKPORTAL}</p>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider">
										{if $HistoryMap neq ''} {* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
										{else} {* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
										{/if} &nbsp;
										<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveHIstoryDetailViewBlockPortal" data-loading="true" data-loading-divid="LoadingDivId" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="SavehistoryCreateViewportal">{$MOD.CreateMap}</button>
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
								<div class="dvbp-container">

									<!-- Detail View Block Portal Module -->
									<div class="slds-form-element slds-text-align--left dvbp-module">
										<label class="slds-form-element__label" for="FirstModule">Choose the Module</label>
										<div class="slds-form-element__control">
											<div class="slds-select_container">
												<select data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-select-relation-field-id="FieldsForRow" data-module="MapGenerator" id="FirstModule" name="mod" class="slds-select">
													{$FirstModuleSelected}
												</select>
											</div>
										</div>
									</div>

									<!-- Detail View Block Portal Block Name -->
									<div class="slds-form-element slds-text-align--left dvbp-block_name">
										<label class="slds-form-element__label" for="BlockName">{$MOD.writeBlockName}</label>
										<div class="slds-form-element__control">
											<input id="BlockName" type="text" class="slds-input" minlength="5" placeholder="{$MOD.writeBlockName}" />
										</div>
									</div>

									<!-- Detail View Block Portal Choose Field Container  and Add Button -->
									<div id="divForAddRows" class="dvbp-choose_fields-add_button">
										<!-- Detail View Block Portal Choose Field Container -->
										<div class="slds-form-element slds-text-align--left dvbp-choose-fields">
											<label class="slds-form-element__label" for="FieldsForRow">{$MOD.chooseanotherfieldsforthisrow}</label>
											<div class="slds-form-element__control">
												<select id="FieldsForRow" name="mod" class="slds-select" multiple="multiple">
													{$FirstModuleFields}
												</select>
											</div>
										</div>

										<!-- Detail View Block Portal Add Button Container -->
										<div class="slds-form-element dvbp-add-button">
											{*<label class="slds-form-element__label" for="inputSample3">{$MOD.SelectShowFields}</label> *}
											<div class="slds-form-element__control">
												<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
													<button data-add-type="Rows" data-add-relation-id="FieldsForRow" data-div-show="LoadShowPopup" onclick="addrows(this)" class="slds-button slds-button_icon" aria-haspopup="true" title="Add more Rows">
														<img src="themes/images/btnL3Add.gif">
													</button>
												</div>
												<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
													<button data-add-type="Block" data-add-relation-id="FirstModule,BlockName" data-div-show="LoadShowPopup" onclick="showpopupCreateViewPortal(this);resetFieldCreateViewPortal();" class="slds-button slds-button--neutral slds-button--brand slds-dvbp--button">
														{$MOD.Addsection}
													</button>
												</div>
											</div>
										</div>
									</div>

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
											<div id="contenitoreJoin" class="dvbp-popup-zone">
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
											<div id="contenitoreJoin" class="dvbp-history-zone">
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

		<div>
			<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
			<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
			<input type="hidden" name="querysequence" id="querysequence" value="">
			<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
		</div>
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