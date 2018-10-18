
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
		
		if (App.SaveHistoryPop.length>0)
		{ 
				ShowLocalHistoryCE('LoadHistoryPopup','LoadShowPopup');
			 App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryCorrect);
		}else{
			 App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryError);
		}

		   ClickToshowSelectedCE(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
			App.utils.UpdateMapNAme();


		var valuesinput=document.getElementById('FunctionName').value;
		if (valuesinput && valuesinput.length>=4)
		{
			$('#Firstmodule2').removeAttr('disabled');
			$('#Firstfield2').removeAttr('disabled');
			$('#DefaultValueFirstModuleField_1').removeAttr('disabled')
		}else {
			$('#Firstmodule2').attr('disabled', 'disabled');
			$('#Firstfield2').attr('disabled', 'disabled');
			$('#DefaultValueFirstModuleField_1').attr('disabled', 'disabled');
		}
		App.countsaveMap=2;
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
								<p class="slds-text-heading--label slds-line-height--reset"><b>{$MOD.ConditionExpression}</b></p>
							</header>
							<div class="slds-no-flex">
								<div class="slds-section-title--divider">
									{if $HistoryMap neq ''} {* saveFieldDependency *}
									<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
									{else} {* saveFieldDependency *}
									<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
									{/if} &nbsp;
									<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-loading="true" data-loading-divid="LoadingDivId"  data-send="true"  data-send-url="MapGenerator,saveConditionExpresion" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryCE">{$MOD.CreateMap}</button>
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
								<div class="wrapper condition-expression-container">
									<!--  Expression Tab -->
									<div class="tab">
										<input id="tab-one" type="radio" {if $Expresionshow eq '' } checked="checked" {/if}  name="tabs">
										<label class="expression-label" for="tab-one">{$MOD.expression}</label>
										<div class="tab-content">
											<input type="hidden" id="TypeExpresion" value="Expression" name="">
											<div class="exp-module-fields">
												<div class="slds-form-element slds-text-align--left exp-select-module">
													<label class="slds-form-element__label" for="FirstModule">{$MOD.SelectModule}</label>
													<div class="slds-form-element__control">
														<div class="slds-select_container">
															<select  data-select-load="true" id="FirstModule" onchange="enableExpressionFields(this)" data-reset-all="true" data-reset-id-popup="LoadShowPopup"  data-select-relation-field-id="Firstfield" data-module="MapGenerator" name="mod" class="slds-select">
																{$FirstModuleSelected}
															</select>
														</div>
													</div>
												</div>
												<div id="OickList" class="exp-select-fields">
													<div class="slds-form-element slds-text-align--left">
														<label  class="slds-form-element__label" for="Firstfield">{$MOD.SelectField}</label>
														<div class="slds-form-element__control">
															<div class="slds-select_container">
																<select id="Firstfield" name="mod" class="slds-select" disabled="disabled" data-load-element="true" data-load-element-idget="Firstfield" data-load-element-idset="expresion">
																	{$FirstModuleFields}
																</select>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="slds-form-element slds-text-align--left exp-textarea-container">
												<label class="slds-form-element__label" for="expresion">{$MOD.writetheexpresion}</label>
												<div class="slds-form-element__control">
													<textarea id="expresion" class="slds-textarea" disabled="disabled" onfocus="removeselect('Firstfield')"  placeholder="{$MOD.writetheexpresion}">{$Expresionshow}</textarea>
												</div>
											</div>
											{*
												{if $HistoryMap neq ''}
													<button class="slds-button slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButtonExpresion" >{$MOD.SaveAsMap}</button> 
												{else}
													<button class="slds-button slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButtonExpresion" disabled >{$MOD.SaveAsMap}</button>
												{/if}
											*}
											<div class="exp-add-button">
												<button class="slds-button slds-button--small slds-button--brand" data-add-button-popup="false" data-add-type="Expression" data-add-relation-id="FirstModule,Firstfield,expresion" data-add-replace="true" data-show-id="expresion" data-div-show="LoadShowPopup" onclick="AddResponsabileFieldsCE(this);removearrayselected('Function','Parameter')">{$MOD.Add}</button>
											</div>
										</div>
									</div>
									<!-- Function Tab -->
									<div class="tab">
										<input id="tab-two" type="radio" {if $FunctionNameshow neq '' } checked="checked" {/if} name="tabs">
										<label class="function-label" for="tab-two">{$MOD.function}</label>
										<div class="tab-content">
											<input type="hidden" id="TypeFunction" value="Function" name="">
											<div id="divfunctionname">
												<!--  Write function name and select module container -->
												<div class="func-name-module">
													<!-- Write function name -->
													<div class="slds-form-element slds-text-align--left">
														<label class="slds-form-element__label" for="divfunctionname">{$MOD.writethefunctionname}</label>
														<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon--right">
															<input id="FunctionName" onblur="removearrayselectedall()" oninput="checkfunctionname(this)" class="slds-input" placeholder="{$MOD.writethefunctionname}" value="{$FunctionNameshow}" />
															<button data-message-show="true" data-message-show-id="help" class="slds-input__icon slds-input__icon_right slds-button slds-button_icon">
																<svg class="slds-button__icon slds-icon-text-light" aria-hidden="true">
																	<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info" ></use>
																</svg>
																<span class="slds-assistive-text">Clear</span>
															</button>
															<div class="slds-popover slds-popover_tooltip slds-nubbin_bottom-left" id="help" role="tooltip" style="display: none;">
																<div class="slds-popover__body slds-text-longform">
																	<p class="slds-text-color--error">{$MOD.InfoFunctionName}</p>
																</div>
															</div>
														</div>
													</div>
													<!-- Select module -->
													<div class="slds-form-element slds-text-align--left">
														<label class="slds-form-element__label" for="Firstmodule2">{$MOD.SelectModule}</label>
														<div class="slds-form-element__control">
															<div class="slds-select_container">
																<select class="slds-select" id="Firstmodule2" data-reset-all="true" data-reset-id-popup="LoadShowPopup" disabled="disabled" data-select-load="true" data-select-relation-field-id="Firstfield2" data-module="MapGenerator">
																	{$FirstModuleSelected}
																	<option>Select One</option>
																</select>
															</div>
														</div>
													</div>
												</div>
												<!-- Select fields or write your own parameter  -->
												<div class="func-select-or-write-params">
													<!-- Select Parameter -->
													<div class="slds-form-element slds-text-align--left">
														<label class="slds-form-element__label" for="Firstfield2">{$MOD.SelectFieldOrwritetheparameters}</label>
														<div class="slds-form-element__control">
															<div class="slds-select_container">
																<select id="Firstfield2" name="mod" class="slds-select" data-add-button-popup="false" onchange="AddResponsabileFieldsCE(this);" data-add-type="Function" disabled="disabled" data-add-relation-id="Firstfield2,Firstmodule2,FunctionName" data-show-id="Firstfield2" data-div-show="LoadShowPopup" onclick="removearrayselected('','Expression')">
																	{$FirstModuleFields}
																</select>
															</div>
														</div>
													</div>
													<!-- Write Parameter -->
													<div id="ShowmoreInput" class="slds-text-align--left">
														<label class="slds-form-element__label" for="SecondInput">{$MOD.putParameter}</label>
														<div class="slds-combobox_container slds-has-object-switcher">
															<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																<div class="slds-combobox__form-element">
																	<input type="text" disabled="disabled" id="DefaultValueFirstModuleField_1" placeholder="{$MOD.AddAValues}" id="defaultvalue" class="slds-input slds-combobox__input" onfocus="removearrayselected('','Expression')">
																</div>
															</div>
															<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
																<button onclick="AddResponsabileFieldsCE(this);Empydata(this);" class="slds-button slds-button_icon" aria-haspopup="true" title="Add more Values" data-add-button-popup="false" data-add-type="Parameter" data-add-relation-id="DefaultValueFirstModuleField_1,Firstmodule2,FunctionName" data-show-id="DefaultValueFirstModuleField_1" data-div-show="LoadShowPopup">
																	<img src="themes/images/btnL3Add.gif" >
																</button>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div id="showpopupmodal" style="width: 100%;height: 100%;"></div>
										</div>
										{*   <div class="slds-modal__footer">
											div class="slds-modal__footer">
											{if $HistoryMap neq ''}
												<button class="slds-button slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButtonFunction" >{$MOD.SaveAsMap}</button> 
											{else}
												<button class="slds-button slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButtonFunction" disabled >{$MOD.SaveAsMap}</button>
											{/if}                        
											<button id="AddToArray" class="slds-button slds-button--neutral slds-button--brand" data-send-data-id="ListData,FunctionName,Firstmodule2,MapName,Firstfield2,DefaultValueFirstModuleField_1,TypeFunction"   data-send="true"  data-send-url="MapGenerator,saveConditionExpresion" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButtonFunction" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup">{$MOD.CreateMap}</button>
										</div> *}
									</div>
								</div>
							</td>

							<td class="dvtCellInfo" align="left" valign="top">
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
<div id="contentJoinButtons" style="width: 70%;height: 100%;float: left;"></div>
<div id="generatedquery">
	<div id="results" style="margin-top: 1%;"></div>
</div>
<div id="null"></div>
<div>
	<div id="queryfrommap"></div>
</div>