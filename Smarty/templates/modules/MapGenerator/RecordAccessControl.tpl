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

						
					 ShowLocalHistoryRecordAccessControll('LoadHistoryPopup','LoadShowPopup')
				 ClickToshowSelectedFileds(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.RecordAccessControl}</p>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider">
										{if $HistoryMap neq ''} {* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
										{else} {* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
										{/if} &nbsp;
											<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName,duplicatecheckDetailView,editcheckDetailView,deletecheckDetailView,viewcheckDetailView,viewcheckListview,editcheckListview,deletecheckListview,AddcheckListview" data-send="true" data-send-url="MapGenerator,saveRecordAccessControl" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-send-savehistory-functionname="ShowLocalHistoryRecordAccessControll" data-save-history-show-id-relation="LoadShowPopup" data-loading="true" data-loading-divid="LoadingDivId">{$MOD.CreateMap}</button>
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
								<!-- Record Access Control Container -->
								<div id="selJoin" class="rac-container">
									<!-- Record Access Control Container -->
									<div class="rac-target_module">
										<div class="slds-form-element slds-text-align--left">
											<label class="slds-form-element__label" for="FirstModule">{$MOD.TargetModule}</label>
											<div class="slds-form-element__control">
												<div class="slds-select_container">
													<select data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-select-load="true" data-second-module-id="relatedModule" data-second-module-file="RelatedModuleRecordAccess" data-module="MapGenerator" id="FirstModule" name="mod" class="slds-select" onchange="allcheckbosChecked();">
														{$FirstModuleSelected}
													</select>
												</div>
											</div>
										</div>
									</div>
									<!-- List View & Detail View Checkboxes -->
									<div class="rac-lv-dv-container">
										<!-- List View Checkboxes -->
										<div class="rac-lv-container">
											<h5><strong>{$MOD.ListView}</strong></h5>
											<div class="rac-lv-checkboxes">
												<!-- View Checkbox -->
												<div class="slds-form-element slds-text-align--center">
													<label class="slds-checkbox--toggle slds-grid">
														<span class="slds-form-element__label" >{$MOD.View}</span>
														<input onchange="Removechecked(this)" data-all-id="editcheckListview,deletecheckListview" id="viewcheckListview" name="checkbox" type="checkbox" {if $viewcheckListview eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
														<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
															<span class="slds-checkbox--faux"></span>
														</span>
													</label>
												</div>
												<!-- Add Checkbox -->
												<div class="slds-form-element slds-text-align--left">
													<label class="slds-checkbox--toggle slds-grid">
															<span class="slds-form-element__label" >{$MOD.Add}</span>
															<input id="AddcheckListview" name="checkbox" type="checkbox" {if $AddcheckListview eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
															<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																<span class="slds-checkbox--faux"></span>
															</span>
													</label>
												</div>
												<!-- Edit Checkbox -->
												<div class="slds-form-element slds-text-align--left">
													<label class="slds-checkbox--toggle slds-grid">
															<span class="slds-form-element__label" >{$MOD.Edit}</span>
															<input id="editcheckListview" name="checkbox" type="checkbox" {if $editcheckListview eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
															<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																<span class="slds-checkbox--faux"></span>
															</span>
													</label>
												</div>
												<!-- Delete Checkbox -->
												<div class="slds-form-element slds-text-align--left">
													<label class="slds-checkbox--toggle slds-grid">
															<span class="slds-form-element__label" >{$MOD.Delete}</span>
															<input id="deletecheckListview" name="checkbox" type="checkbox" {if $deletecheckListview eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
															<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																<span class="slds-checkbox--faux"></span>
															</span>
													</label>
												</div>
											</div>
										</div>
										<!-- Detail View Checkboxes -->
										<div class="rac-dv-container">
											<h5><strong>{$MOD.DetailView}</strong></h5>
											<div class="rac-dv-checkboxes">
												<!-- View Checkbox -->
												<div class="slds-form-element slds-text-align--center">
													<label class="slds-checkbox--toggle slds-grid">
														<span class="slds-form-element__label" >{$MOD.View}</span>
														<input id="viewcheckDetailView" name="checkbox" onchange="Removechecked(this)" data-all-id="duplicatecheckDetailView,editcheckDetailView,deletecheckDetailView" type="checkbox" {if $viewcheckDetailView eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
														<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
															<span class="slds-checkbox--faux"></span>
														</span>
													</label>
												</div>
												<!-- Duplicate Checkbox -->
												<div class="slds-form-element slds-text-align--center">
													<label class="slds-checkbox--toggle slds-grid">
														<span class="slds-form-element__label" >{$MOD.Duplicate}</span>
														<input id="duplicatecheckDetailView" name="checkbox" type="checkbox" {if $duplicatecheckDetailView eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
														<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
															<span class="slds-checkbox--faux"></span>
														</span>
													</label>
												</div>
												<!-- Edit Checkbox -->
												<div class="slds-form-element slds-text-align--center">
													<label class="slds-checkbox--toggle slds-grid">
														<span class="slds-form-element__label" >{$MOD.Edit}</span>
														<input id="editcheckDetailView" name="checkbox" type="checkbox" {if $editcheckDetailView eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
														<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
															<span class="slds-checkbox--faux"></span>
														</span>
													</label>
												</div>
												<!-- Delete Checkbox -->
												<div class="slds-form-element slds-text-align--center">
													<label class="slds-checkbox--toggle slds-grid">
														<span class="slds-form-element__label" >{$MOD.Delete}</span>
														<input id="deletecheckDetailView" name="checkbox" type="checkbox" {if $deletecheckDetailView eq '1'} checked="checked" {else} {/if} aria-describedby="toggle-desc" />
														<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
															<span class="slds-checkbox--faux"></span>
														</span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<!-- Related List Container -->
									<div id="contenitoreJoins" class="rac-related_list-container">
										<div id="sectionField" class="rac-related_module-container">
											<h5><strong>{$MOD.RelatedList}</strong></h5>
										</div>
										<div class="rac-related_module-checkboxes">
											<!-- Related Module Select -->
											<div class="slds-form-element slds-text-align--left rac-related_module">
												<div class="slds-form-element__control">
													<label class="slds-form-element__label" for="relatedModule">{$MOD.RelatedModule}</label>
													<div class="slds-select_container">
														<select name="relatedModule" id="relatedModule" class="slds-select">
															{$AllModulerelated}
														</select>
													</div>
												</div>
											</div>
											<!-- Related Module Checkboxes -->
											<div id="sel1" class="rac-checkboxes">
														<div class="slds-form-element">
																<label class="slds-checkbox--toggle slds-grid">
																		<input id="viewcheckRelatedlist" onchange="Removechecked(this)" data-all-id="addcheckRelatetlist,editcheckrelatetlist,deletecheckrelatedlist,selectcheckrelatedlist" name="checkbox" checked="checked" type="checkbox" aria-describedby="toggle-desc" />
																		<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																			<span>{$MOD.View}</span>
																			<span class="slds-checkbox--faux"></span>
																		</span>
																</label>
														</div>

														<div class="slds-form-element">
																<label class="slds-checkbox--toggle slds-grid">
																		<input id="addcheckRelatetlist" name="checkbox" type="checkbox" checked="checked" aria-describedby="toggle-desc" />
																		<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																			<span>{$MOD.Add}</span>
																			<span class="slds-checkbox--faux"></span>
																		</span>
																</label>
														</div>

														<div class="slds-form-element">
																<label class="slds-checkbox--toggle slds-grid">
																		<input id="editcheckrelatetlist" name="checkbox" checked="checked" type="checkbox" aria-describedby="toggle-desc" />
																		<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																			<span>{$MOD.Edit}</span>
																			<span class="slds-checkbox--faux"></span>
																		</span>
																</label>
														</div>

														<div class="slds-form-element">
																<label class="slds-checkbox--toggle slds-grid">
																		<input id="deletecheckrelatedlist" name="checkbox" checked="checked" type="checkbox" aria-describedby="toggle-desc" />
																		<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																			<span>{$MOD.Delete}</span>
																			<span class="slds-checkbox--faux"></span>
																		</span>
																</label>
														</div>

														<div class="slds-form-element">
																<label class="slds-checkbox--toggle slds-grid">
																		<input id="selectcheckrelatedlist" name="checkbox" checked="checked" type="checkbox" aria-describedby="toggle-desc" />
																		<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
																			<span>{$MOD.Select}</span>
																			<span class="slds-checkbox--faux"></span>
																		</span>
																</label>
														</div>

														<div class="slds-form-element rac-add-button">
																<label class="slds-checkbox--toggle slds-grid">
																		<button data-add-button-popup="false" onclick="AddPopupRecordAccessControl(this)" data-add-type="Related" data-add-relation-id="FirstModule,duplicatecheckDetailView,editcheckDetailView,deletecheckDetailView,viewcheckDetailView,viewcheckListview,editcheckListview,deletecheckListview,AddcheckListview,addcheckRelatetlist,editcheckrelatetlist,deletecheckrelatedlist,selectcheckrelatedlist,viewcheckRelatedlist,relatedModule" data-show-id="relatedModule" data-show-modul-id="FirstModule" data-add-button-validate="relatedModule" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="{$MOD.ClickAdd}">
																				<img src="themes/images/btnL3Add.gif">
																		</button>
																</label>
														</div>
													</div>
												<!-- <div id="SecondDiv"> -->
											<!-- </div> -->

										</div>
									</div>





												<div id="sel2" style="display: none;">
												</div>

												<div id="contenitoreJoins" style="display: none;">
														<div id="sectionField" style="width:100%; float: left; margin-top: -15px">
																<div class="testoDiv">
																</div>
														</div>
												</div>

											<!--  Edmondi's comment -->
											<!--{*
												<div id="selJoin" style="float:left;overflow: hidden;width: 100%;height: 100%;">
												<label style="margin-top: initial;font-size: 14px;font-family: unset;font-style: oblique;color: indigo;font-style: oblique;">{$MOD.SelectedField}</label>
												<div class="slds-grid slds-grid--vertical slds-navigation-list--vertical"
												style="float:left;overflow: hidden;width: 73%;height: 100px;" id="buttons">
												<div id="LoadShowPopup" style="width: 100%;height: 100%;overflow: auto;background-color: moccasin;" >        
												<div id="LoadShowPopup" style="margin:auto;display: block; width: 20%;">
												</div>
												</div>{*End div LoadShowPopup*}
												<div id="LoadHistoryPopup">
												</div>
												</div>   
												</div>
											*}-->

								</div><!-- #selJoin -->
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

		<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
		<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
		<input type="hidden" name="querysequence" id="querysequence" value="">
		<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
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

* {
	box-sizing: border-box;
}

body {
	margin: 0;
}

/* Create two equal columns that floats next to each other */
.column {
	float: left;
	width: 50%;
	padding: 10px;
	height: 300px; /* Should be removed. Only for demonstration */
}

/* Clear floats after the columns */
.row:after {
	content: "";
	display: table;
	clear: both;
}

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