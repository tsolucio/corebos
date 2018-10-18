
<div>
  
  <div id="LoadingImage" style="display: none">
	<img src=""/>
</div>
{if $HistoryMap neq ''}
  <script type="text/javascript">
	App.savehistoryar='{$HistoryMap}';
  </script>
{/if}

{if $PopupJson neq ''} 
 <script type="text/javascript"> 
	  {foreach key=profile_name item=$popjs  from=$PopupJson }
	   App.utils.addINJSON('{$popjs.FirstModuleval}','{$popjs.FirstModuletxt}','{$popjs.FirstFieldval}','{$popjs.FirstFieldtxt}','{$popjs.SecondModuleval}','{$popjs.SecondModuletxt}','{$popjs.SecondFieldval}','{$popjs.SecondFieldtext}','{$popjs.SecondFieldOptionGrup}');
		  
	  {/foreach}
	  App.utils.ReturnAllDataHistory('LoadShowPopup');
	  App.utils.UpdateMapNAme();
	</script>
   
{/if}


  <table class="slds-table slds-no-row-hover slds-table-moz map-generator-table">
		<tbody>
			<tr class="blockStyleCss" id="DivObjectID">
				<td class="detailViewContainer" valign="top">
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<h1 id="mapNameLabel" class="slds-page-header__title slds-m-right--small slds-truncate"></h1>
									<p class="slds-text-heading--label slds-line-height--reset">{$MOD.TypeMapMapping}</p>
								</header>
								<div class="slds-no-flex">
									<div class="actionsContainer mapButton">
										<div class="slds-section-title--divider">
											<button class="slds-button slds-button--small slds-button--brand"  data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,SaveTypeMaps" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-loading="true" data-loading-divid="LoadingDivId" data-send-savehistory="true">{$MOD.CreateMap}</button>
											{if $HistoryMap neq ''}
												<button data-modal-saveas-open="true" id="SaveAsButton" class="slds-button slds-button--small slds-button--brand">{$MOD.SaveAsMap}</button>
											{else}
												<button data-modal-saveas-open="true" id="SaveAsButton" class="slds-button slds-button--small slds-button--brand" disabled>{$MOD.SaveAsMap}</button>
											{/if}
										</div>
									<div class="mailClient mailClientBg" style="position: absolute; width: 350px; height:110px;z-index: 90000; display: none;" id="userorgroup" name="userorgroup">
										<center><b>{$MOD.addjoin}</b>:
											<select name="usergroup" id="usergroup" style="width:30%">
												<option value="none">None</option>
												<option value="user">User</option>
												<option value="group">Group</option>
											</select>
											<br>
											<br><b>{$MOD.addCF}</b>:
											<select name="CFtables" id="cf" style="width:30%">
												<option value="none">None</option>
												<option value="cf">CF</option>
											</select>
											<br>
											<br>
											<br>
											<input class="crmbutton small edit" type="button" name="okbutton" id="okbutton" value="OK" onclick="generateJoin();hidediv('userorgroup');openalertsJoin();">
										</center>
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
									<div id="selJoin" style="float:left; overflow: hidden;width:80%">
										<div style="float:left; overflow: hidden;width:45%" id="sel1">
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select data-select-load="true" data-second-module-file="SecondModuleMasterDetail" data-second-module-id="secmodule" data-module="MapGenerator" onchange="resetdata(this)" data-select-relation-field-id="Firstfield" id="FirstModule" name="mod" class="slds-select">
															{$FirstModuleSelected}
														</select>
													</div>
												</div>
											</div>
											<br>
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="Firstfield" name="mod" class="slds-select">
															{$FirstModuleFields}
														</select>
													</div>
												</div>
											</div>
										</div>
										<div style="float:left; overflow: hidden;width:3%; margin-left: 2%; margin-right: 2%;" id="centerJoin"> =</div>
										<div style="float:left; overflow: hidden;width:45%" id="sel2">
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select id="secmodule" data-second-select-load="true" data-module="MapGenerator" data-second-select-relation-id="SecondField" data-second-select-file="AllRelation" name="secmodule" class="slds-select">
															{$SecondModulerelation}
														</select>
													</div>
												</div>
											</div>
											<br>
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<div class="" id="SecondDiv" style="float: left;width: 92%;">
														<select id="SecondField" name="secmodule" data-load-show="true" data-load-show-relation="FirstModule,Firstfield,secmodule" data-div-show="LoadShowPopup" class="slds-select">
															{$SecondModuleFields}
														</select>
														<div class="slds-combobox_container slds-has-object-switcher" style="width: 100%;margin-top:0px;">
															<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" style="display:none;" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																<div class="slds-combobox__form-element">
																	<input type="text" id="DefaultValue" placeholder="Insert a default value and click add" id="defaultvalue" style="width:268px;height: 33px;padding: 0px;margin: 0px;" class="slds-input slds-combobox__input">
																</div>
															</div>
															<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click" style="margin: 0px;padding: 0px;width: 40px;height: 40px;">
																<button data-load-show="true" data-load-show-relation="FirstModule,Firstfield,secmodule,DefaultValue" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add " style="width:2.1rem;">
																	<img src="themes/images/btnL3Add.gif" style="width: 100%;">
																</button>
															</div>
														</div>
													</div>
													<div style="float:right;">
														<a href="#" data-showhide-load="true" data-tools-id="SecondField,SecondInput" style="margin-top: 6px;"><i style="margin-top: 5px;" class="fa fa-refresh fa-2x" aria-hidden="true"></i></a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</td>

								<td class="dvtCellInfo" align="left">
									<div class="flexipageComponent">
										<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
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
										<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
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

								<td class="dvtCellLabel" width="100%">
									<div id="waitingIddiv"></div>
									<div id="contentJoinButtons"></div>
									<div id="generatedquery">
										<div id="results"></div>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<div id="null"></div>
	<div>
		<div id="queryfrommap"></div>
	</div>


  <input type="hidden" name="MapID" value="{$MapID}" id="MapID">
	<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
	<input type="hidden" name="querysequence" id="querysequence" value="">
	<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
	<div data-div-load-automatic="true" id="ModalShow">
	  
	</div>

	{if $Modali neq ''}
	  <div>
		{$Modali}
	  </div>
	{/if}
	<div id="contenitoreJoin">

		<div id="sectionField">

			<div>
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
</div>
   
{literal}
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
  

</div>