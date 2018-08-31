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
		 {foreach name=outer item=popi from=$PopupJS}
			{foreach item=hist from=$popi}
			 var temparray = {};
			 {foreach key=key item=item from=$hist}
				 temparray['{$key}']='{$item}';
			 {/foreach}
			 App.popupJson.push({'{'}temparray{'}'});
			{/foreach}
			HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
			App.popupJson.length=0;
		{/foreach}

		// if (App.popupJson.length>0)
		// { 
		//    for (var i = 0; i <= App.popupJson.length-1; i++) {
		//      var module=App.popupJson[i].temparray[`DefaultText`];
		//      var typeofppopup=App.popupJson[i].temparray['JsonType'];
		//      var divinsert= App.utils.DivPopup(i,module,"LoadShowPopup",typeofppopup);
		//      $('#LoadShowPopup').append(divinsert);
		//    } 
		// }else{
		//   alert(mv_arr.MappingFiledValid);
		//  }
		
		SavehistoryCreateViewportalIOMap('LoadHistoryPopup','LoadShowPopup');
		ShowHistoryDataIOMap(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
		App.countsaveMap=2;
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
							<header class="slds-media--center slds-has-flexi-truncate">
								<h1 id="mapNameLabel"  class="slds-page-header__title slds-m-right--small slds-truncate">
									{if $NameOFMap neq ''} {$NameOFMap} {/if}
								</h1>
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.TypeMapIOMap}</p>
							</header>
							<div class="slds-no-flex">
								<div class="slds-section-title--divider">
									{if $HistoryMap neq ''}
										{* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
									{else}
										{* saveFieldDependency *}
										<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
									{/if}
									&nbsp;
									<button class="slds-button slds-button--small slds-button--brand" data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveTypeIOMap" data-send-saveas="true" data-loading="true" data-loading-divid="LoadingDivId"  data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-send-savehistory-functionname="SavehistoryCreateViewportalIOMap" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup">{$MOD.CreateMap}</button>
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
								<div id="selJoin" class="iomap-container">
									<div id="sel1" class="iomap-input-fields">
										<div class="slds-form-element slds-text-align--left">
											<div class="slds-form-element__control">
												<label class="slds-form-element__label" for="AllFieldsInput">{$MOD.inputFileds}</label>
												<div id="SecondDiv">
													<select id="AllFieldsInput" name="AllFieldsInput" data-select-load="false" onchange="split_popups(this);" data-module="MapGenerator" data-second-module-id="AllFieldsOutputselect" data-second-module-file="AllFields_File" data-add-button-popup="false" data-add-button-validate="AllFieldsInput" class="slds-select" data-add-type="Input" data-add-relation-id="AllFieldsInput,AllFieldsInput,AllFieldsInput" data-show-id="AllFieldsInput" data-show-modul-id="AllFieldsInput" data-div-show="LoadShowPopup">
														{$allfields}
													</select>
													<div class="slds-combobox_container slds-has-object-switcher">
														<div id="firstInp" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" style="display:none;" aria-expanded="false" aria-haspopup="listbox" role="combobox">
															<div class="slds-combobox__form-element">
																<input type="text" id="AllFieldsInputByhand" placeholder="Insert a default value" id="defaultvalue" class="slds-input slds-combobox__input">
															</div>
														</div>
														<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
															<button data-add-type="Input" data-add-relation-id="AllFieldsInputByhand,AllFieldsInputByhand,AllFieldsInputByhand" data-show-id="AllFieldsInputByhand" data-div-show="LoadShowPopup" data-show-modul-id="" data-add-button-popup="false"  data-add-button-validate="AllFieldsInputByhand" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add " onclick="split_popups(this);clearInput('AllFieldsInputByhand');">
																<img src="themes/images/btnL3Add.gif" width="16">
															</button>
														</div>
													</div>
												</div>
												<div class="toggle-field">
													<a href="#" data-showhide-load="true" data-tools-id="AllFieldsInput,firstInp">
														<i class="fa fa-refresh fa-2x" aria-hidden="true"></i>
													</a>
												</div>
											</div>
										</div>
									</div>

									{* <div id="centerJoin"> </div> *}

									<div id="sel2" class="iomap-output-fields">
										<div class="slds-form-element">
											<div class="slds-form-element__control slds-text-align--left">
												<label class="slds-form-element__label" for="AllFieldsOutputselect">{$MOD.outputFields}</label>
												<div id="SecondDiv">
													<select id="AllFieldsOutputselect" name="AllFieldsOutput" data-add-button-popup="false" data-add-button-validate="AllFieldsOutputselect" onchange="split_popups(this);" class="slds-select" data-add-type="Output" data-add-relation-id="AllFieldsOutputselect,AllFieldsOutputselect,AllFieldsOutputselect" data-show-id="AllFieldsOutputselect" data-show-modul-id="AllFieldsOutputselect"  data-div-show="LoadShowPopup" class="slds-select">
														{$allfields}
													</select>
													<div class="slds-combobox_container slds-has-object-switcher">
														<div id="secondOutput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" style="display:none;" aria-expanded="false" aria-haspopup="listbox" role="combobox">
															<div class="slds-combobox__form-element">
																<input type="text" id="AllFieldsOutputbyHand" placeholder="Insert a default value" id="defaultvalue" class="slds-input slds-combobox__input">
															</div>
														</div>
														<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
															<button data-add-relation-id="AllFieldsOutputbyHand,AllFieldsOutputbyHand,AllFieldsOutputbyHand" data-show-id="AllFieldsOutputbyHand" data-div-show="LoadShowPopup" data-add-button-validate="AllFieldsOutputbyHand" data-add-button-popup="false" data-add-type="Output" class="slds-button slds-button_icon" onclick="split_popups(this);clearInput('AllFieldsOutputbyHand')" aria-haspopup="true" title="Click to add">
																<img src="themes/images/btnL3Add.gif" width="16">
															</button>
														</div>
													</div>
												</div>
												<div class="toggle-field">
													<a href="#" data-showhide-load="true" data-tools-id="AllFieldsOutputselect,secondOutput">
														<i class="fa fa-refresh fa-2x" aria-hidden="true"></i>
													</a>
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
											</div>{*End div contenitorejoin*}
										</div>
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
	<div id="contentJoinButtons" style="width: 70%;height: 100%;float: left;">
		<!--     <div class="slds-grid slds-grid--vertical slds-navigation-list--vertical"
		 style="float:left; overflow: hidden;width:20%" id="buttons">

		<ul id="LDSstyle">
		
		<li><button class="slds-button slds-button--brand"  data-send-data-id="ListData,MapName"   data-send="true"  data-send-url="MapGenerator,saveTypeIOMap" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" style="width:98%;margin:5px;">{$MOD.CreateMap}</button></li>

		{if $HistoryMap neq ''}
			 <li><button data-modal-saveas-open="true" id="SaveAsButton" class="slds-button slds-button--brand" style="width:98%;margin:5px;">{$MOD.SaveAsMap}</button></li>
		{else}
			<li><button data-modal-saveas-open="true" id="SaveAsButton" class="slds-button slds-button--brand" disabled style="width:98%;margin:5px;">{$MOD.SaveAsMap}</button></li>
		{/if}
		
		</ul>

	</div> -->
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
		<div data-div-load-automatic="true" id="ModalShow">
		</div>
		<div id="ModalDiv">
			{if $Modali neq ''}
				<div>
				 {$Modali}
				</div>
			{/if}
		</div>
		<div id="contenitoreJoin" style="width: 100%; display: inline-flex;">
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
	</div>