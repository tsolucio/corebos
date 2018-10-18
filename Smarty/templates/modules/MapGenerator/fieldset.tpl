{*ListColumns.tpl*}
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
		    {/foreach}
			HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
		   App.popupJson.length=0;
		{/foreach}
		ShowLocalHistoryFieldSet('LoadHistoryPopup','LoadShowPopup');
		ClickToshowSelecteFieldSet(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
									<p class="slds-text-heading--label slds-line-height--reset">{$MOD.FieldSet}</p>
								</header>
								<div class="slds-no-flex">
									<div class="actionsContainer mapButton">
										<div class="slds-section-title--divider">
											{if $HistoryMap neq ''} {* FieldSet *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
											{else} {* FieldSet *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
											{/if} &nbsp;
											<button class="slds-button slds-button--small slds-button--brand"  data-loading="true" data-loading-divid="LoadingDivId"  data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveFieldSetMap" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryFieldSet" >{$MOD.CreateMap}</button> 
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
									<fieldset class="fieldset-container">
										<legend align="center">{$MOD.fsModule}</legend>
										<div class="fs-container">
											<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="fs-modules"><strong class="slds-text-color--error">*</strong>{$MOD.fsModulePickList}</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="fs-modules" data-select-load="true" data-select-relation-field-id="fs-fields" data-module="MapGenerator" required  name="mod" class="slds-select">
															{$FirstModuleSelected}
														</select>
													</div>
												</div>
											</div>
											<div class="fs-fields-information">
												<div class="slds-form-element slds-text-align--left ws-response-time">
													<label class="slds-form-element__label" for="fs-fields"><strong class="slds-text-color--error">*</strong>{$MOD.fsFields}</label>
													<div class="slds-form-element__control">
														<select id="fs-fields"  required  name="mod" class="slds-select">
														<option value=''>Select field</option>
														</select>
													</div>
												</div>
												<div class="slds-form-element slds-text-align--left ws-response-time">
													<label class="slds-form-element__label" for="fs-information">{$MOD.fsInformation}</label>
													<div class="slds-form-element__control">
														<input id="fs-information" required class="slds-input" placeholder="{$MOD.fsInformationinput}" type="text" />
													</div>
												</div>
												<div class="fs-add_button">
													<button onclick="AddPopupFieldSet(this);RestoreDataEXFIM(this);" data-add-type="Fields" data-add-button-validate="fs-fields" data-show-id="fs-modules" data-div-show="LoadShowPopup"   data-add-relation-id="fs-modules,fs-fields,fs-information" class="slds-button slds-button_icon slds-text-align--center" aria-haspopup="true" title="{$MOD.fsbuttonaddfield}">
														<img src="themes/images/btnL3Add.gif">
													</button>
												</div>
											</div>
											<div class="fs-button-container">
												<button  onclick="AddPopupFieldSetModule(this);RemoveSelectFields(this);" data-div-show="LoadShowPopup"   class="slds-button slds-button--small slds-button--brand" data-add-relation-id="fs-modules,fs-fields,fs-information" aria-haspopup="true" title="{$MOD.fsbuttonmoduleInfo}">{$MOD.fsbuttonmodule}</button>
											</div>
										</div>
									</fieldset>
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
												<div id="contenitoreJoin" class="ws-popup-zone">
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