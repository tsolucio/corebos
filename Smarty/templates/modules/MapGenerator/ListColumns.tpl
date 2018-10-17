{*ListColumns.tpl*}
<div>
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
        ShowLocalHistoryListColumns('LoadHistoryPopup','LoadShowPopup');
        App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryCorrect);
        }else{
        App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryError);
        }
        App.DefaultValue='Field';
        var historydata=App.SaveHistoryPop[parseInt(App.SaveHistoryPop.length-1)];
        App.popupJson.length=0;
        for (var i=0;i<=historydata.PopupJSON.length-1;i++){
        App.popupJson.push(historydata.PopupJSON[i]);
        }
        App.utils.ReturnDataSaveHistory('LoadShowPopup');
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
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.ListColumns}</p>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider">
										{if $HistoryMap neq ''} {* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
										{else} {* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
										{/if} &nbsp;
										<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,SaveListColumns" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-loading="true" data-loading-divid="LoadingDivId" data-send-savehistory-functionname="ShowLocalHistoryListColumns">{$MOD.CreateMap}</button>
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
								<div id="selJoin">
									<div id="sel1" class="list-columns-container">
										<div class="slds-form-element">
											<div class="slds-form-element__control slds-text-align--center">
												<label class="slds-form-element__label">{$MOD.TargetModule}</label>
												<div class="slds-select_container">
													<select data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-second-module-id="secmodule" data-select-relation-field-id="Firstfield" data-select-fieldid="FirstfieldID" data-module="MapGenerator" data-second-module-file="ListColumnsRelationData" id="FirstModule" name="mod" class="slds-select">
														{$FirstModuleSelected}
													</select>
												</div>
											</div>
											<div class="lc-target-module">
												<label>ID</label>
												<input type="button" value="{$FmoduleID}" class="slds-button slds-button--neutral sel" id="FirstfieldID" name="FirstfieldID">
											</div>
										</div>
										<br>
											{*
											<div class="slds-form-element">
													<div class="slds-form-element__control">
															<div class="slds-select_container">
																	<select id="SecondField" data-label-change-load="true" data-module="MapGenerator" data-select-filename="GetLabelName" data-set-value-to="DefaultValue" name="mod" class="slds-select">
																			{if $SecondModuleFields neq ''} {$SecondModuleFields} {else}
																			<option value="">(Select fields)</option>
																			{/if}
																	</select>
															</div>
													</div>
											</div>
											*}
										<br>
									</div>

									<div id="centerJoin"> </div>

									<div id="sel2">
										{*
											<div class="testoDiv">
													<center><b>{$MOD.popupPlace}</b></center>
											</div>
										*}
										<div class="slds-form-element">
											<div class="slds-form-element__control slds-text-align--center">
												<label class="slds-form-element__label">{$MOD.OriginModule}</label>
												<div class="slds-select_container">
													<select id="secmodule" data-second-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-module="MapGenerator" data-second-select-relation-id="SecondField" data-select-fieldid="SecondfieldID" name="secmodule" class="slds-select">
														{if $SecondModulerelation neq ''} {$SecondModulerelation} {else}
															<option value="">(Select a module)</option>
														{/if}
													</select>
												</div>
											</div>
											<div class="lc-origin-module">
												<label>ID</label>
												<input type="button" class="slds-button slds-button--neutral sel" value="{$SmoduleID}" id="SecondfieldID" name="SecondfieldID">
											</div>
										</div>

										<br>
											{*
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<div class="" id="SecondDiv">
														<div class="slds-combobox_container slds-has-object-switcher">
															<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																<div class="slds-combobox__form-element">
																		<input type="text" id="DefaultValue" placeholder="Change label if you want and after click button" id="defaultvalue" class="slds-input slds-combobox__input">
																</div>
															</div>
															<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
																<button data-add-button-popup="true" data-add-type="Related" data-add-relation-id="FirstModule,secmodule,SecondField,FirstfieldID,SecondfieldID,DefaultValue" data-show-id="SecondField" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add">
																	<img src="themes/images/btnL3Add.gif">
																</button>
															</div>
														</div>
													</div>
												</div>
											</div>
											*}
										<br>
									</div>
								</div>

								<div id="sectionField" class="related-list-content">
									<div class="testoDiv">
										<center><b>{$MOD.RelatedList}</b></center>
									</div>
									<hr class="related-list-line">

									<div class="slds-form-element">
											<div class="slds-form-element__control">
													<div id="AlertsAddDiv">
													</div>
											</div>
									</div>

									<div class="related-list-container">
											<div class="slds-form-element">
													<div class="slds-form-element__control">
															<div class="slds-select_container">
																	<select id="SecondField" data-label-change-load="true" data-module="MapGenerator" data-select-filename="GetLabelName" data-set-value-to="DefaultValue" name="mod" class="slds-select">
																			{if $SecondModuleFields neq ''} {$SecondModuleFields} {else}
																			<option value="">(Select fields)</option>
																			{/if}
																	</select>
															</div>
													</div>
											</div>

											<div class="slds-form-element">
													<div class="slds-form-element__control">
															<div id="SecondDiv">
																	<div class="slds-combobox_container slds-has-object-switcher">
																			<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																					<div class="slds-combobox__form-element">
																							<input type="text" id="DefaultValue" placeholder="Change label if you want and after click button" id="defaultvalue" class="slds-input slds-combobox__input">
																					</div>
																			</div>
																			<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
																					<button data-add-button-popup="true" data-add-type="Related List" data-add-relation-id="FirstModule,secmodule,SecondField,FirstfieldID,SecondfieldID,DefaultValue" data-show-id="DefaultValue" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add">
																							<img src="themes/images/btnL3Add.gif">
																					</button>
																			</div>
																	</div>
															</div>
													</div>
											</div>
									</div>
								</div>

								<div id="contenitoreJoins" class="popup-screen-content">
										<div id="sectionField">
												<div class="testoDiv">
														<center><b>{$MOD.popupPlace}</b></center>
												</div>
												<hr class="popup-screen-line">
												<div class="slds-form-element">
														<div class="slds-form-element__control">
																<div id="AlertsAddDiv">
																</div>
														</div>
														<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
														<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
														<input type="hidden" name="querysequence" id="querysequence" value="">
														<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
												</div>
										</div>
										<div class="popup-screen-container">
												<div id="sel1" class="popup-screen-select">
														<div class="slds-form-element">
																<div class="slds-form-element__control">
																		<div class="slds-select_container">
																				<select id="Firstfield" data-label-change-load="true" data-module="MapGenerator" data-select-filename="GetLabelName" data-set-value-to="DefaultValueFirstModuleField" name="mod" class="slds-select">
																						{if $FirstModuleFields neq ''} {$FirstModuleFields} {else}
																						<option value="">(Select fields)</option>
																						{/if}
																				</select>
																		</div>
																</div>
														</div>
												</div>
												<!-- <div id="centerJoin"></div> -->
												<div id="sel2" class="popup-screen-field">
														<div class="slds-form-element">
																<div class="slds-form-element__control">
																		<div id="SecondDiv">
																				<div class="slds-combobox_container slds-has-object-switcher">
																						<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																								<div class="slds-combobox__form-element">
																										<input type="text" id="DefaultValueFirstModuleField" placeholder="Change label if you want and after click button" id="defaultvalue" class="slds-input slds-combobox__input">
																								</div>
																						</div>
																						<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
																								<button data-add-button-popup="true" data-add-type="Popup Screen" data-add-relation-id="FirstModule,FirstfieldID,secmodule,Firstfield,DefaultValueFirstModuleField" data-show-id="DefaultValueFirstModuleField" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add">
																										<img src="themes/images/btnL3Add.gif">
																								</button>
																						</div>
																				</div>
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
		<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
		<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
		<input type="hidden" name="querysequence" id="querysequence" value="">
		<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
		</div>
		</div>{*End div contenitorejoin*}
		<!--     <div id="selJoin" style="float:left;overflow: hidden;width: 100%;height: 100%;">
		
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
	</div> -->
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

.ui-buttonset-vertical label.ui-state-active+input+label {
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