{*ListColumns.tpl*}

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

		if (App.SaveHistoryPop.length>0)
		{
			App.utils.AddtoHistory('LoadHistoryPopup','LoadShowPopup');
			App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryCorrect);
		}
		else
		{
			App.utils.ShowNotification("snackbar",4000,mv_arr.LoadHIstoryError);
		}

		var historydata=App.SaveHistoryPop[parseInt(App.SaveHistoryPop.length-1)];
		App.popupJson.length=0;
		ShowLocalHistoryFD('LoadHistoryPopup','LoadShowPopup');
		ClickToshowSelectedFD(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
									<p class="slds-text-heading--label slds-line-height--reset">{$MOD.FieldDependency}</p>
								</header>
								<div class="slds-no-flex">
									<div class="actionsContainer mapButton">
										<div class="slds-section-title--divider">
											{if $HistoryMap neq ''} {* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
											{else} {* saveFieldDependency *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
											{/if} &nbsp;
											<button class="slds-button slds-button--small slds-button--brand"  data-loading="true" data-loading-divid="LoadingDivId"  data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveFieldDependency" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryFD" >{$MOD.CreateMap}</button> 
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
									<div class="field-dependency-container">
										<!-- Choose module -->
										<div class="field-dependency-module">
											<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label">Choose the Module</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select data-select-load="true" data-reset-all="true" data-reset-id-popup="LoadShowPopup" data-second-module-id="PickListFields"  data-select-relation-field-id="Firstfield,Firstfield2" data-module="MapGenerator"  id="FirstModule" data-second-module-file="getPickList" name="mod" class="slds-select">
																{$FirstModuleSelected}
														</select>
													</div>
												</div>
											</div>
										</div>
										<!-- Choose field and insert value -->
										<div class="field-dependency-field">
											<div class="slds-form-element slds-text-align--left">
												<label class="slds-form-element__label" for="Firstfield">Choose the field</label>
												<div class="slds-form-element__control">
													<div class="slds-select_container">
														<select  id="Firstfield" name="mod" class="slds-select">
															{$FirstModuleFields}
														</select>
													</div>
												</div>
											</div>
											<div class="slds-form-element__control operators-select">
												<div class="slds-select_container">
													<select id="Conditionalfield" name="mod" onchange="CheckChoise(this);" class="slds-select">
														<option value="equal">{$MOD.equals}</option>
														<option value="not equal">{$MOD.not_equals}</option>
														<option value="empty">{$MOD.empty}</option>
														<option value="not empty">{$MOD.not_empty}</option>
													</select>
												</div>
											</div>
											<div class="field-dependency-insert-value">
												<div class="slds-form-element slds-text-align--left">
													<label id="labelforinputDefaultValueResponsibel" class="slds-form-element__label" for="DefaultValueResponsibel">{$MOD.AddAValues}</label>
													<div class="slds-form-element__control">
														<div class="slds-combobox_container slds-has-object-switcher" >
															<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
																<div class="slds-combobox__form-element">
																	<input type="text" id="DefaultValueResponsibel" required placeholder="{$MOD.AddAValues}" class="slds-input slds-combobox__input">
																</div>
															</div>
															<div id="divbutton" class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
																<button onclick="AddResponsabileFieldsFD(this);clearInput('DefaultValueResponsibel');" id="AddbuttonFDP" data-add-button-popup="false" data-add-type="Responsible" data-add-button-validate="Firstfield" data-add-relation-id="FirstModule,DefaultValueResponsibel,Firstfield,Conditionalfield" data-show-id="Firstfield" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add">
																	<img src="themes/images/btnL3Add.gif" width="16">
																</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- Buttons -->
										<div class="add-fields-picklist-block">
											<button class="slds-button slds-button--small slds-button--brand" data-modal-saveas-open="true" data-modal-id="fields" data-modal-check-id="FirstModule" data-modal-backdrop-id="fieldsbackdrop">{$MOD.AddFields}</button>
											<button class="slds-button slds-button--small slds-button--brand" data-modal-saveas-open="true" data-modal-id="Picklist" data-modal-check-id="FirstModule" data-modal-backdrop-id="Picklistbackdrop">{$MOD.AddPickList}</button>
											{* <h3 class="slds-section-title--divider">{$MOD.ChoseResponsabile}</h3> *}
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

	<!-- Add field Modal -->
	<div class="slds">
		<div class="slds-modal" aria-hidden="false" role="dialog" id="fields">
			<div class="slds-modal__container">
				<div class="slds-modal__header">
					<button class="slds-button slds-button--icon-inverse slds-modal__close" data-modal-saveas-close="true" data-modal-close-id="fields" data-modal-close-backdrop-id="fieldsbackdrop" >
						<svg aria-hidden="true" class="slds-button__icon slds-button__icon--large">
							<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
						</svg>
						<span class="slds-assistive-text">{$MOD.close}</span>
					</button>
					<h2 class="slds-text-heading--medium">{$MOD.SectionOriginFileds}</h2>
				</div>
				<div class="slds-modal__content slds-p-around--medium" >
					{* <h3 class="slds-section-title--divider">{$MOD.SectionOriginFileds}</h3> *}
					<div class="slds-form-element">
						<label class="slds-form-element__label" for="Firstfield2">Choose the field</label>
						<div class="slds-form-element__control">
							<div class="slds-select_container">
								<select  id="Firstfield2" name="mod" class="slds-select">
										{$FirstModuleFields}
								</select>
							</div>
						</div>
					</div>
					<div class="radio-buttons-content">
						<div class="slds-form-element">
							<div class="slds-form-element__control">
								<div id="SecondDiv">
									<!--SLDS Checkbox Toggle Element Start-->
									
									<!-- Readonly -->
									<div class="slds-form-element">
										<label class="slds-checkbox--toggle slds-grid toggle-readonly">
											<input id="Readonlycheck" onchange="fieldDependencyCheck(this);" data-all-id="ShowHidecheck,mandatorychk" name="checkbox" type="checkbox" aria-describedby="toggle-desc" />
											<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
												<span class="slds-checkbox--faux"></span>
												<span class="slds-checkbox--on" >{$MOD.Readonly}-{$MOD.YES}</span>
												<span class="slds-checkbox--off">{$MOD.Readonly}-{$MOD.NO}</span>
												<!-- <span class="slds-checkbox--of">editable-false</span> -->
											</span>
										</label>
									</div>
									<!-- Hidden -->
									<div class="slds-form-element">
										<label class="slds-checkbox--toggle slds-grid toggle-hidden">
											<input  onchange="fieldDependencyCheck(this)" data-all-id="mandatorychk" id="ShowHidecheck" name="checkbox"  type="checkbox" aria-describedby="toggle-desc" />
											<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
												<span class="slds-checkbox--faux"></span>
												<span class="slds-checkbox--on" >{$MOD.Hidden}</span>
												<span class="slds-checkbox--off">{$MOD.Show}</span>
											</span>
										</label>
									</div>

									<!-- Mandatory -->
									<div class="slds-form-element">
										<label class="slds-checkbox--toggle slds-grid toggle-mandatory">
											<input id="mandatorychk" name="checkbox" checked="checked" type="checkbox" aria-describedby="toggle-desc" />
											<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
												<span class="slds-checkbox--faux"></span>
												<span class="slds-checkbox--on"  >{$MOD.Mandatory}-{$MOD.YES}</span>
												<span class="slds-checkbox--off">{$MOD.Mandatory}-{$MOD.NO}</span>
												<!-- <span class="slds-checkbox--of">editable-false</span> -->
											</span>
										</label>
									</div>

									{*
										<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
											<!--  <button data-add-button-popup="true" data-add-relation-id="FirstModule,FirstfieldID,Firstfield,secmodule,SecondfieldID,sortt6ablechk,editablechk,mandatorychk,hiddenchk" data-div-show="LoadShowPopup" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add " style="width:2.1rem;">
												<img src="themes/images/btnL3Add.gif" style="width: 100%;">
											</button> -->
											<button  onclick="GenearteMasterDetail()" class="slds-button slds-button_icon" aria-haspopup="true" title="Click to add " style="width:2.1rem;">
												<img src="themes/images/btnL3Add.gif" style="width: 100%; height: 29">
											</button>
										</div>
									*}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-modal__footer">
					{* <label id="ErrorLabelModal"></label> *}
					<button class="slds-button slds-button--small slds-button--brand" onclick="AddResponsabileFieldsFD(this);" data-add-button-popup="false" data-add-type="Field" data-add-button-validate="Firstfield2"  data-add-relation-id="FirstModule,Firstfield2,ShowHidecheck,Readonlycheck,mandatorychk" data-show-id="Firstfield2" data-div-show="LoadShowPopup">
						{$MOD.Add}
					</button>
					<!-- data-send-savehistory="{$savehistory}" -->
					<button class="slds-button slds-button--small slds-button--destructive" data-modal-saveas-close="true" data-modal-close-id="fields" data-modal-close-backdrop-id="fieldsbackdrop"  >{$MOD.cancel}</button>
				</div>
			</div>
		</div>

		<div class="slds-backdrop" id="fieldsbackdrop"></div>

		<!-- Button To Open Modal -->
		{*<button class="slds-button slds-button--brand" id="toggleBtn">Open Modal</button>*}
	</div>

	<!-- Picklist Modal -->
	<div class="slds">
		<div class="slds-modal" aria-hidden="false" role="dialog" id="Picklist">
			<div class="slds-modal__container">
				<!-- Modal Header -->
					<div class="slds-modal__header">
						<button class="slds-button slds-button--icon-inverse slds-modal__close" data-modal-saveas-close="true" data-modal-close-backdrop-id="Picklistbackdrop" data-modal-close-id="Picklist">
							<svg aria-hidden="true" class="slds-button__icon slds-button__icon--large">
								<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{$MOD.close}</span>
						</button>
						<h2 class="slds-text-heading--medium">{$MOD.AddPickList}  (optional)</h2>
					</div>
				<!-- End Modal Header -->

				<!-- Modal Body -->
					<div class="slds-modal__content slds-p-around--medium picklist-modal-content">
						{* <h3 class="slds-section-title--divider">{$MOD.SectionOriginFileds}</h3> *}
						<div class="picklist-modal-fields-content">
							<div class="slds-form-element">
								<label class="slds-form-element__label" for="PickListFields">Choose the field</label>
								<div class="slds-form-element__control">
									<div class="slds-select_container">
										<select  id="PickListFields" onchange="checkIfAdded(this);" name="mod" class="slds-select">
												{$Picklistdropdown}
										</select>
									</div>
								</div>
							</div>
							<div id="ShowmoreInput">
								<div class="slds-combobox_container slds-has-object-switcher">
									<div id="SecondInput" class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click"  aria-expanded="false" aria-haspopup="listbox" role="combobox">
										<div class="slds-combobox__form-element">
											<input type="text" id="DefaultValueFirstModuleField_1" placeholder="{$MOD.AddAValues}" id="defaultvalue" class="slds-input slds-combobox__input">
										</div>
									</div>
									<div class="slds-listbox_object-switcher slds-dropdown-trigger slds-dropdown-trigger_click">
										<button class="slds-button slds-button_icon" onclick="Addmorevalues(this)" aria-haspopup="true" title="Add more Values">
											<img src="themes/images/btnL3Add.gif" width="16">
										</button>
									</div>
								</div>
							</div>
						</div>
						<div id="showpopupmodal"></div>
					</div>
				<!-- End Modal Body -->

				<!-- Modal Footer -->
					<div class="slds-modal__footer">
						{* <label id="ErrorLabelModal"></label> *}
						<button id="AddToArray" data-add-button-popup="false" data-add-type="Picklist" onclick="AddResponsabileFieldsFD(this);removedataafterclick()" data-add-button-validate="PickListFields"  data-add-relation-id="FirstModule,PickListFields,DefaultValueFirstModuleField_1" data-show-id="PickListFields" data-div-show="LoadShowPopup" onclick="removedataafterclick();"  class="slds-button slds-button--small slds-button--brand">
							{$MOD.Add}
						</button>  <!-- data-send-savehistory="{$savehistory}" -->
						<button class="slds-button slds-button--small slds-button--destructive" data-modal-saveas-close="true" data-modal-close-backdrop-id="Picklistbackdrop" data-modal-close-id="Picklist" >{$MOD.cancel}</button>
					</div>
				<!-- End Modal Footer -->
			</div>
		</div>
		<div class="slds-backdrop" id="Picklistbackdrop"></div>

		<!-- Button To Open Modal -->
		{*<button class="slds-button slds-button--brand" id="toggleBtn">Open Modal</button>*}
	</div>

	<div>
		<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
		<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
		<input type="hidden" name="querysequence" id="querysequence" value="">
		<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
	</div>

<div style="float: right;width: 25%;margin-left: 20px;">
	<div id="LoadShowPopup"></div>
</div>

<div id="LoadHistoryPopup"  style="/* position: absolute; */margin-top: 6%;float: left;width: 71%;"></div>



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