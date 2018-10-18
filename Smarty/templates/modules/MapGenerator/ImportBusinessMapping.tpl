
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
			  temparray['{$key}']='{$item}';
		  {/foreach}
		   App.popupJson.push({'{'}temparray{'}'});
				// console.log(temparray);
				{/foreach}
				HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
				App.popupJson.length=0;
		{/foreach}
		ShowLocalHistoryImportBussiness('LoadHistoryPopup','LoadShowPopup');
		ShowImportBussinesMapping(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
								<h1 id="mapNameLabel"  class="slds-page-header__title slds-m-right--small slds-truncate">
									{if $NameOFMap neq ''} {$NameOFMap} {/if}
								</h1>
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.ImportBusinessMapping}</p>
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
										&nbsp;
										<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName,UpdateId" data-send="true"  data-loading="true" data-loading-divid="LoadingDivId"  data-send-url="MapGenerator,saveImportBussinesMapping" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryImportBussiness">{$MOD.CreateMap}</button>
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
								<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
								<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
								<input type="hidden" name="querysequence" id="querysequence" value="">
								<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
								<div data-div-load-automatic="true" id="ModalShow"></div>
								{if $Modali neq ''} <div>{$Modali}</div> {/if}
								<!-- Import Business Mapping Container -->
								<div id="selJoin" class="import-business-mapping-container">
									<!-- Target Module Select Content -->
									<div class="import-business-target-module">
										<div id="sel1">
											<div class="slds-form-element">
												<div class="slds-form-element__control slds-text-align--left">
													<label class="slds-form-element__label" for="FirstModule">{$MOD.TargetModule}</label>
													<div class="slds-select_container slds-text-align--left">
														<select data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-second-module-file="SecondModuleMapping" data-second-module-id="secmodule" data-module="MapGenerator" data-select-relation-field-id="Firstfield,SecondField" id="FirstModule" name="mod" class="slds-select">
															{$FirstModuleSelected}
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Select Fields Matches Content -->
									<div class="import-business-select-fields">
										<div id="sel2">
											<!-- 1st field select -->
											<div class="slds-form-element">
												<div class="slds-form-element__control slds-text-align--left">
													<label class="slds-form-element__label" for="Firstfield">{$MOD.SelectFields}</label>
													<div class="slds-select_container">
														<select id="Firstfield" name="mod" class="slds-select">
															{$allfields}
														</select>
													</div>
												</div>
											</div>

											<!-- matches label -->
											<div id="sel1">
												<label class="slds-form-element__label">{$MOD.Maches}</label>
											</div>

											<!-- 2nd field select -->
											<div class="slds-form-element">
												<div class="slds-form-element__control slds-text-align--left">
													<label class="slds-form-element__label" for="SecondField">{$MOD.SelectFields}</label>
													<div class="slds-select_container" id="SecondDiv">
														<select id="SecondField" name="secmodule" data-add-button-popup="false" data-add-type="Match" data-add-relation-id="FirstModule,Firstfield,SecondField,UpdateId" data-show-id="Firstfield" data-show-modul-id="FirstModule" data-div-show="LoadShowPopup" class="slds-select" onchange="AQddImportBussinessMapping(this)">
															{$allfields}
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- This is display none because has no values-->
									<div id="contenitoreJoin">
										<div id="sectionField">
											<div>
												<div class="testoDiv">
													{* <center><b>{$MOD.SelectField}</b></center> *}
												</div>
												<div class="slds-form-element">
													<div class="slds-form-element__control">
														<div id="AlertsAddDiv" style="margin-top: 10px;width: 50%;">
														</div>
													</div>
													<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
													<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
													<input type="hidden" name="querysequence" id="querysequence" value="">
													<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
												</div>
											</div>
										</div>
									</div>

									<hr class="line-sep">

									<!-- Update Import Business Select Content -->
									<div id="sectionField" class="update-import-business">
										<div id="sel1">
											<div class="slds-form-element">
												<div class="slds-form-element__control slds-text-align--left">
													<label class="slds-form-element__label" for="UpdateId">{$MOD.UpdateInportBussines}</label>
													<div class="slds-select_container">
														<select class="slds-select" id="UpdateId">
															{if $update neq ''}
																{if $update eq 'FIRST'}
																	<option value="FIRST" selected="selected">{$MOD.FIRST}</option>
																	<option value="LAST">{$MOD.LAST}</option>
																	<option value="ALL">{$MOD.ALL}</option>
																{elseif $update eq 'LAST'}
																	<option value="FIRST" >{$MOD.FIRST}</option>
																	<option value="LAST" selected="selected">{$MOD.LAST}</option>
																	<option value="ALL">{$MOD.ALL}</option>
																{elseif $update eq 'ALL'}
																	<option value="FIRST">{$MOD.FIRST}</option>
																	<option value="LAST">{$MOD.LAST}</option>
																	<option value="ALL" selected="selected">{$MOD.ALL}</option>
																{/if}
															{else}
															<option value="FIRST">{$MOD.FIRST}</option>
															<option value="LAST">{$MOD.LAST}</option>
															<option value="ALL">{$MOD.ALL}</option>
															{/if}
														</select>
													</div>
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
												<div id="LoadHistoryPopup">
												</div>
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