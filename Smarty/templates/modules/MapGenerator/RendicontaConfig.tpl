<div id="LoadingImage" style="display: none">
		<img src="" />
</div>

{if $HistoryMap neq ''}
	<script type="text/javascript">
	  App.savehistoryar = '{$HistoryMap}';
	</script>
{/if}

{if $PopupJS neq ''}
  <script type="text/javascript">
      {foreach from=$PopupJS item=allitems key=key name=name}
           var temparray = {};
            {foreach key=key item=item from=$allitems}
                temparray['{$key}']='{$item}';
            {/foreach}
            App.popupJson.push({'{'}temparray{'}'});          
           HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
          App.popupJson.length=0;
      {/foreach}
    
     if (App.SaveHistoryPop.length>0)
    { 
       ShowLocalHistoryRendiConfig('LoadHistoryPopup','LoadShowPopup');
       App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryCorrect);
    }else{
       App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryError);
     }

      ShowRendicontConfig(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.RendicontaConfig}</p>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider">
										{if $HistoryMap neq ''} {* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
										{else} {* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
										{/if} &nbsp;
										<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-send="true" data-loading="true" data-loading-divid="LoadingDivId" data-send-url="MapGenerator,saveRendicontaConfig" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-send-savehistory-functionname="ShowLocalHistoryRendiConfig" data-save-history-show-id-relation="LoadShowPopup">{$MOD.CreateMap}</button>
									</div>
								</div>
							</div>
						</div>
					</article>
				</div>
				<div class="slds-truncate">
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
						<tr class="slds-line-height--reset map-generator-cell-container">
							<!-- THE MODULE Zone -->
							<td class="dvtCellLabel" valign="top">
								<div class="rendiconda-config-container">
									<!-- Module and Status Field -->
									<div class="rc-module-fields">
										<!-- Select Module -->
										<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="FirstModule">{$MOD.ChosseMOdule}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="FirstModule" data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-second-module-id="PickListFields" data-select-relation-field-id="statusfield,processtemp,causalefield" data-module="MapGenerator" name="mod" class="slds-select">
																{$FirstModuleSelected}
														</select>
													</div>
												</div>
										</div>
										<!-- Select Status Field -->
										<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="statusfield">{$MOD.statusfield}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="statusfield" data-reset="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" name="mod" class="slds-select">
																{$allfields}
														</select>
													</div>
												</div>
										</div>
									</div>
									<!-- Proccess Template and Casual Field -->
									<div class="rc-template-fields">
										<!-- Select Process Template -->
										<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="processtemp">{$MOD.processtemp}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="processtemp" data-reset="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" name="mod" class="slds-select">
																{$allfields}
														</select>
													</div>
												</div>
										</div>
										<!-- Select Casual field (optional) -->
										<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="causalefield">{$MOD.causalefield}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="causalefield" data-reset="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" name="mod" class="slds-select">
																{$allfields}
														</select>
													</div>
												</div>
										</div>
									</div>
									<!-- Rendiconda Config Add Button -->
									<div class="rc-add-button">
											<button data-add-button-popup="false" data-add-type="RendicontaConfig" data-add-relation-id="FirstModule,statusfield,processtemp,causalefield" data-div-show="LoadShowPopup" onclick="AdDPOpupRendicontaConfig(this);" class="slds-button slds-button--neutral slds-button--brand" style="float: right;">{$MOD.Add}</button>
									</div>
								</div>
							</td>
							<!-- Popups and History Zone -->
							<td class="dvtCellInfo" align="left">
								<!-- Popup Zone -->
								<div class="flexipageComponent">
									<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
										<div class="slds-card__header slds-grid">
											<header class="slds-media slds-media--center slds-has-flexi-truncate">
												<div class="slds-media__body">
													<h2 class="header-title-container"> 
														<span class="slds-text-heading--small slds-truncate actionLabel">
															<b>{$MOD.PopupZone}</b>
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
								<!-- History Zone -->
								<div class="flexipageComponent">
									<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
										<div class="slds-card__header slds-grid">
											<header class="slds-media slds-media--center slds-has-flexi-truncate">
												<div class="slds-media__body">
													<h2 class="header-title-container"> 
														<span class="slds-text-heading--small slds-truncate actionLabel">
															<b>{$MOD.HistoryZone}</b>
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