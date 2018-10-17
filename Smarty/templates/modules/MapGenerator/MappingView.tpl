<div style="width: 100%;height:100%">
	<div id="LoadingImage" style="display: none">
		<img src="" />
	</div>
	{if $HistoryMap neq ''}
	<script type="text/javascript">
	App.savehistoryar = '{$HistoryMap}';
	</script>
	{/if}

{if $PopupJson neq ''} 
 <script type="text/javascript"> 
	  {foreach key=profile_name item=$popjs  from=$PopupJson }
		{foreach from=$popjs item=item key=key name=name}
		   App.utils.addINJSON('{$item.FirstModuleval}','{$item.FirstModuletxt}','{$item.FirstFieldval}','{$item.FirstFieldtxt}','{$item.SecondModuleval}','{$item.SecondModuletxt}','{$item.SecondFieldval}','{$item.SecondFieldtext}','{$item.SecondFieldOptionGrup}');
		{/foreach}
		  HistoryPopup.addtoarray(App.JSONForCOndition,"JSONCondition");
		   App.JSONForCOndition.length=0;
	  {/foreach}
	  App.utils.AddtoHistory('LoadHistoryPopup','LoadShowPopup');
	 //console.log(App.SaveHistoryPop.length);
	 var historydata=App.SaveHistoryPop[parseInt(App.SaveHistoryPop.length-1)];
	 App.JSONForCOndition.length=0;
	  for (var i=0;i<=historydata.JSONCondition.length-1;i++){
		App.JSONForCOndition.push(historydata.JSONCondition[i]);
	  }
	  App.utils.ReturnAllDataHistory('LoadShowPopup');
	  App.countsaveMap=2;
	  App.utils.UpdateMapNAme();
	</script>
   
{/if}
	<!-- <div class="subTitleDiv" id="subTitleDivJoin" style="margin-top: 1%">
	<left style="margin-left: 45%"><b>{$MOD.TargetModule}</b></left>
	<right style="margin-left: 10%">{$MOD.OriginModule}</b></right>
</div> -->
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
								<p  class="slds-text-heading--label slds-line-height--reset">{$MOD.TypeMapMapping}</p>
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
										<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-send="true"  data-loading="true" data-loading-divid="LoadingDivId"  data-send-url="MapGenerator,SaveTypeMaps" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup">{$MOD.CreateMap}</button>
									</div>
								</div>
							</div>
						</div>
					</article>
				</div>
				<div class="slds-truncate">
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
						<tr class="slds-line-height--reset map-generator-cell-container">
							<td class="dvtCellLabel mapping-map" valign="top">
								<div class="mailClient mailClientBg" id="userorgroup" name="userorgroup">
									<center><b>{$MOD.addjoin}</b>:
										<select name="usergroup" id="usergroup">
											<option value="none">None</option>
											<option value="user">User</option>
											<option value="group">Group</option>
										</select>
										<br>
										<br><b>{$MOD.addCF}</b>:
										<select name="CFtables" id="cf">
											<option value="none">None</option>
											<option value="cf">CF</option>
										</select>
										<input class="crmbutton small edit" type="button" name="okbutton" id="okbutton" value="OK" onclick="generateJoin();hidediv('userorgroup');openalertsJoin();">
									</center>
								</div>
								<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
								<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
								<input type="hidden" name="querysequence" id="querysequence" value="">
								<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
								<div data-div-load-automatic="true" id="ModalShow"></div>
								<div id="ModalDiv">{if $Modali neq ''} <div> {$Modali} </div> {/if} </div>

								<div id="selJoin">
									<div class="target-origin-container">
										<div id="sel1" class="mapping-target-module">
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<center>
														<label class="slds-form-element__label" for="FirstModule">{$MOD.TargetModule}</label>
													</center>
													<div class="slds-select_container">
														<select data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-second-module-file="SecondModuleMapping" data-second-module-id="secmodule" data-module="MapGenerator" data-select-relation-field-id="Firstfield" id="FirstModule" name="mod" class="slds-select">
															{$FirstModuleSelected}
														</select>
													</div>
												</div>
											</div>

											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<center>
														<label class="slds-form-element__label" for="Firstfield">{$MOD.SelectFields}</label>
													</center>
													<div class="slds-select_container">
														<select id="Firstfield" name="mod" class="slds-select">
															{$FirstModuleFields}
														</select>
													</div>
												</div>
											</div>
										</div>

										{* <div id="centerJoin"> =</div> *}

										<div id="sel2" class="mapping-origin-module">
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<center>
														<label class="slds-form-element__label" for="secmodule">{$MOD.OriginModule}</label>
													</center>
													<div class="slds-select_container">
														<select id="secmodule" data-second-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-second-firstmodule-id="FirstModule" data-module="MapGenerator" data-second-select-relation-id="SecondField" data-second-select-file="mappingFieldRelation" name="secmodule" class="slds-select">
															{$SecondModulerelation}
														</select>
													</div>
												</div>
											</div>
											<div class="slds-form-element">
												<div class="slds-form-element__control">
													<center>
														<label class="slds-form-element__label" for="SecondField">{$MOD.SelectFields}</label>
													</center>
													<div id="SecondDiv">
														<div id="SecondFieldContainer" class="slds-select_container" style="display: block;">
															<select id="SecondField" name="secmodule" data-load-show="true" data-load-show-relation="FirstModule,Firstfield,secmodule" data-div-show="LoadShowPopup" class="slds-select">
																{$SecondModuleFields}
															</select>
														</div>
														<div class="slds-combobox_container slds-has-object-switcher" id="SecondInputContainer" style="display: none;">
															<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																<div class="slds-combobox__form-element">
																	<input type="text" id="DefaultValue" placeholder="Insert a default value" id="defaultvalue" class="slds-input slds-combobox__input">
																</div>
															</div>
															<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
																<button data-load-show="true" data-load-show-relation="FirstModule,Firstfield,secmodule,DefaultValue" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add">
																	<img src="themes/images/btnL3Add.gif" width="16">
																</button>
															</div>
														</div>
													</div>
													<div class="toggle-field">
														<a href="#" data-showhide-load="true" data-tools-id="SecondFieldContainer,SecondInputContainer">
															<i class="fa fa-refresh fa-2x" aria-hidden="true"></i>
														</a>
													</div>
												</div>
											</div>
										</div>
									</div>

									<hr>
									<div id="contenitoreJoin">
										<div id="sectionField" style="width:100%;">
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
<div id="waitingIddiv"></div>
<div id="contentJoinButtons" style="width: 70%;height: 100%;float: left;">
		<!-- 
	<div class="slds-grid slds-grid--vertical slds-navigation-list--vertical"
		 style="float:left; overflow: hidden;width:20%" id="buttons">




		<ul id="LDSstyle">
		<li><button class="slds-button slds-button--brand"  data-send-data-id="ListData,MapName"   data-send="true"  data-send-url="MapGenerator,SaveTypeMaps" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" style="width:98%;margin:5px;">{$MOD.CreateMap}</button></li>

		{if $HistoryMap neq ''}
		   <li><button data-modal-saveas-open="true" id="SaveAsButton" class="slds-button slds-button--brand" style="width:98%;margin:5px;">{$MOD.SaveAsMap}</button></li>
		{else}
			<li><button data-modal-saveas-open="true" id="SaveAsButton" class="slds-button slds-button--brand" disabled style="width:98%;margin:5px;">{$MOD.SaveAsMap}</button></li>
		{/if}
		
		 {*
			<li><a href="javascript:void(0);" id="addJoin" name="radio" onclick="showform(this);"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.AddJoin}</a></li>
			<li><a href="javascript:void(0);" id="deleteLast" name="radio" onclick="openalertsJoin();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.DeleteLastJoin}</a></li>
			<li><a href="javascript:void(0);" id="create" name="radio" onclick="creaVista();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.CreateMaterializedView}</a></li>
			<li><a href="javascript:void(0);" id="createscript" name="radio" onclick="generateScript();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.CreateScript}</a></li>
			<li><a href="javascript:void(0);" id="createmap" name="radio" onclick="SaveMap();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.CreateMap}</a></li>
			<li><a href="javascript:void(0);" id="saveasmap" name="radio"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.SaveAsMap}</a></li>
		 *}


		</ul>

	</div> -->
	</div>
	<div id="generatedquery">
		<div id="results" style="margin-top: 1%;"></div>
	</div>
	<div id="null"></div>
	<div>
		<div id="queryfrommap"></div>
	</div>