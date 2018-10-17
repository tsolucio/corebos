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
		 ShowLocalHistoryMenustructure('LoadHistoryPopup','LoadShowPopup');
		 ClickToshowSelectedFiledsMenustructure(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
		 App.countsaveMap=2;
		 App.utils.UpdateMapNAme();
		 var urlsend1 = [ "MapGenerator", "AllFields_File" ];
		var dat1 = "FirstModul"
		App.GetModuleForMapGenerator.GetFirstModule("ConditionAllFields",
				urlsend1, dat1);
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
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.MENUSTRUCTURE}</p>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider">
										{if $HistoryMap neq ''} {* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
										{else} {* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
										{/if} &nbsp;
										<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveMenuStructure" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryMenustructure">{$MOD.CreateMap}</button>
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
								<div id="selJoin" class="menu-structure-container">
									<!-- Menu Structure Conditions Container -->
									<div class="ms-conditions-container">
										<div class="slds-form-element slds-text-align--left" id="ms-condition-header">
											<div class="slds-form-element__control">
												<span class="slds-checkbox">
													<input name="options" id="add-conditions" value="on" onchange="ConditionChecked(this);" type="checkbox" />
													<label class="slds-checkbox__label" for="add-conditions">
														<span class="slds-form-element__label">{$MOD.MenustructureDoyouWanttoaddcondition}</span>
														<span class="slds-checkbox--faux"></span>
													</label>
												</span>
											</div>
										</div>
										<div id="idFields">
											<div class="ms-conditions" id="IdconditionDiv">
												<div class="slds-form-element slds-text-align--left">
													<label class="slds-form-element__label" for="FirstModule"> <strong class="slds-text-color--error">*</strong>{$MOD.MenustructureChooseFields}</label>
													<div class="slds-form-element__control">
														<div class="slds-select_container">
															<select id="ConditionAllFields" required name="" class="slds-select">
																{$AllFieldsCondition}
															</select>
														</div>
													</div>
												</div>
												<span class="ms-equals slds-text-align--center">
													<img src="themes/images/equals.png">
												</span>
												<div class="slds-form-element slds-text-align--left">
													<label class="slds-form-element__label" for="ms-field_value"><strong class="slds-text-color--error">*</strong>{$MOD.MenustructureVieldValue}</label>
													<div class="slds-form-element__control">
														<input id="ms-field_value" required class="slds-input" placeholder="Enter {$MOD.MenustructureVieldValue} " type="text" />
													</div>
												</div>
												<button data-add-button-popup="false" onclick="AddPopupMenustrusture(this);RestoreDataEXFIM(this);"  data-add-type="Conditions" data-add-relation-id="ms-field_value,ConditionAllFields" data-show-id="LabelName" data-div-show="LoadShowPopup" data-show-modul-id="" class="slds-button slds-button_icon slds-text-align--center" aria-haspopup="true" title="Click to add">
													<img src="themes/images/btnL3Add.gif">
												</button>
											</div>
										</div>
									</div>
									<div id="sel1">
										<!--  Label Name -->
										<div class="slds-form-element slds-text-align--left">
											<label class="slds-form-element__label" for="LabelName">{$MOD.labelName}</label>
											<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon--right">
												<input type="text" id="LabelName" class="slds-input" placeholder="{$MOD.labelName}" />
												<button data-message-show="true" data-message-show-id="help" class="popover-icon slds-input__icon slds-input__icon_right slds-button slds-button_icon">
													<svg class="slds-button__icon slds-icon-text-light" aria-hidden="true">
														<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info" /></use>
													</svg>
													<span class="slds-assistive-text">Clear</span></button>
												<!-- Tooltip -->
												<div class="slds-popover slds-popover_tooltip slds-nubbin_bottom-left" id="help" role="tooltip" style="display: none;">
													<div class="slds-popover__body slds-text-longform">
														<p>{$MOD.writethelabelName}</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="menu-structure-module-add">
										<!-- Choose Module -->
										<div class="slds-form-element slds-text-align--left">
											<div class="slds-form-element__control">
												<label class="slds-form-element__label" for="FirstModule">{$MOD.MenustructureModule}</label>
												<div class="slds-select_container">
													<select data-select-load="true" id="FirstModule" name="mod" class="slds-select">
														{$FirstModuleSelected}
													</select>
												</div>
											</div>
										</div>
										<!--  Add button icon -->
										<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
											<button data-add-button-popup="false" onclick="AddPopupMenustrusture(this)"  data-add-type="Module" data-add-relation-id="LabelName,FirstModule" data-show-id="LabelName" data-div-show="LoadShowPopup" data-show-modul-id="FirstModule" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add">
												<img src="themes/images/btnL3Add.gif">
											</button>
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

	<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
	<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
	<input type="hidden" name="querysequence" id="querysequence" value="">
	<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
	<div data-div-load-automatic="true" id="ModalShow">
	<input type="hidden" name="MapName" id="HistoryValueToShow" value=" ">
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

		{literal}
		<script>
			 
		</script>
		<style>

				#LDSstyle {
						border: 1px solid black;
						margin-right: 0px;
						margin-top: 0px;
						width: 100%;
						height: 100%;
				}

				/*@media(width:1024px){*/
				/*#LDSstyle {*/
				/*font-size: 10*/
				/*}*/
				/*}*/

				#LDSstyle li {
						margin: 0px;
						padding: 0px;
				}

				#LDSstyle a:hover {
						background: #c3cede;
						/*margin-right: 2px;*/
				}

				.ajax_loader {
						background: url("modules/MapGenerator/image/spinner_squares_circle.gif") no-repeat center center transparent;
						width: 100%;
						height: 100%;
				}

				.blue-loader .ajax_loader {
						background: url("modules/MapGenerator/image/ajax-loader_blue.gif") no-repeat center center transparent;
				}

				#feedback {
						font-size: 1.4em;
				}

				#selectable .ui-selecting {
						background: #FECA40;
				}

				#selectable .ui-selected {
						background: #F39814;
						color: white;
				}

				#selectable {
						list-style-type: none;
						margin: 0;
						padding: 0;
						width: 60%;
				}

				#selectable li {
						margin: 3px;
						padding: 0.4em;
						font-size: 1.4em;
						height: 18px;
				}

				/*
				 * The buttonset container needs a width so we can stack them vertically.
				 *
				 */
				#radio {
						width: 85%;
				}

				/*
				 * Make each label stack on top of one another.
				 *
				 */
				.ui-buttonset-vertical label {
						display: block;
				}

				/*
				 * Handle colliding borders. Here, we"re making the bottom border
				 * of every label transparent, except for labels with the
				 * ui-state-active or ui-state-hover class, or if it"s the last label.
				 *
				 */
				.ui-buttonset-vertical label:not(:last-of-type):not(.ui-state-hover):not(.ui-state-active) {
						border-bottom: transparent;
				}

				/*
				 * For lables in the active state, we need to make the top border of the next
				 * label transparent.
				 *
				 */
				.ui-buttonset-vertical label.ui-state-active + input + label {
						border-top: transparent;
				}

				/*
				 * Oddly enough, the above style approach doesn"t work for the
				 * hover state. So we define this class that"s used by our JavaScript
				 * hack.
				 *
				 */
				.ui-buttonset-vertical label.ui-transparent-border-top {
						border-top: transparent;
				}

				select {
						width: 300px;
				}

				.overflow {
						height: 200px;
				}


				.tooltip {
						position: relative;
						display: inline-block;
						border-bottom: 1px dotted black;
				}

				.tooltip .tooltiptext {
						visibility: hidden;
						width: 120px;
						background-color: black;
						color: #fff;
						text-align: center;
						border-radius: 6px;
						padding: 5px 0;

						/* Position the tooltip */
						position: absolute;
						z-index: 1;
						top: -5px;
						right: 105%;
				}

				.tooltip:hover .tooltiptext {
						visibility: visible;
				}

		</style>
 
{/literal}
