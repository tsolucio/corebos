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
								{if $key eq 'Anotherdata'}
									rows=new Array();
									allfieldsval=[];
									allfieldstetx=[];
										{foreach from=$item item=itemi key=keyes}
											checkifexist={};
											fieldsval=[];
											fieldstetx=[];
											{foreach from=$itemi item=items key=key name=name}
												checkifexist['DataValues']=`{$itemi.DataValues}`;
												checkifexist['DataText']=`{$itemi.DataText}`;
											{/foreach}
											{literal}
												allfieldsval.push(checkifexist);
											{/literal}
									{/foreach}
									{literal}
										temparray["Anotherdata"]=allfieldsval;
									{/literal}
								{else}
								 temparray['{$key}']='{$item}';
								{/if}
						{/foreach}
						App.popupJson.push({'{'}temparray{'}'});
						// console.log(temparray);
					{/foreach}
					 HistoryPopup.addtoarray(App.popupJson,"PopupJSON");
					App.popupJson.length=0;
			{/foreach}
			ShowLocalHistoryWS('LoadHistoryPopup','LoadShowPopup');
			ClickToshowSelectedWS(parseInt(App.SaveHistoryPop.length-1),'LoadShowPopup');
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
									<p class="slds-text-heading--label slds-line-height--reset">{$MOD.TypeMapWS}</p>
								</header>
								<div class="slds-no-flex">
									<div class="actionsContainer mapButton">
										<div class="slds-section-title--divider">
											{if $HistoryMap neq ''} {* TypeMapWS *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton">{$MOD.SaveAsMap}</button>
											{else} {* TypeMapWS *}
											<button class="slds-button slds-button--small slds-button--neutral" data-modal-saveas-open="true" id="SaveAsButton" disabled>{$MOD.SaveAsMap}</button>
											{/if} &nbsp;
											<button class="slds-button slds-button--small slds-button--brand"  data-loading="true" data-loading-divid="LoadingDivId"  data-send-data-id="ListData,MapName" data-send="true" data-send-url="MapGenerator,saveWebServiceMap" data-send-saveas="true" data-send-saveas-id-butoni="SaveAsButton" data-send-savehistory="true" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="ShowLocalHistoryWS" >{$MOD.CreateMap}</button> 
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
									<div class="ws-accordion">
										<!-- WS Configuration Panel -->
										<section  class="ws-accordion-item ws-active" id="ws-section-configuration">
											<div class="ws-accordion-header">
												<a onclick="showhideblocks(this);" id="aConfiguration" data-div-show="LoadShowPopup" >
													<span class="ws-accordion-toggle" id="ws-configuration">
														<i class="fa fa-arrow-right" id="ws-hide" style="display: none;"></i>
														<i class="fa fa-arrow-down"  id="ws-show" style="display: block;"></i>
													</span>
													<h4 class="ws-accordion-title">{$MOD.WSConfirguration}</h4>
												</a>
											</div>
											<div class="ws-accordion-item-content" style="display: block;">
												<div class="ws-configuration-container">
													<!-- WS Module container -->
													<div class="ws-module-container">
														<div class="ws-module">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="FirstModule"><strong class="slds-text-color--error">*</strong> {$MOD.wsModule}</label>
																<div class="slds-form-element__control">
																	<div class="slds-select_container">
																		<select id="FirstModule" required data-second-select-load="true" data-second-firstmodule-id="FirstModule" data-module="MapGenerator" data-second-select-relation-id="ws-select-multiple,ws-output-select-multiple" data-second-select-file="mappingFieldRelation"  name="mod" class="slds-select">
																			{$FirstModuleSelected}
																		</select>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- WS URL and method container -->
													<div class="ws-url-container">
														<!-- URL input-->
														<div class="ws-url-input">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="url-input"><strong class="slds-text-color--error">*</strong> {$MOD.wsURL}</label>
																<div class="slds-form-element__control slds-input-has-icon_left">
																	<span class="slds-icon slds-input__icon slds-input__icon_left slds-icon-text-default" id="fixed-text-addon-pre">{$MOD.wshttps}</span>
																	<input id="url-input" class="slds-input" placeholder="Enter {$MOD.wsURL}" type="text" required aria-describedby="fixed-text-addon-pre fixed-text-addon-post" />
																</div>
															</div>
														</div>
														<!-- URL Method -->
														<div class="ws-url-method">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="urlMethod"><strong class="slds-text-color--error">*</strong> {$MOD.wsMethod}</label>
																<div class="slds-form-element__control">
																	<div class="slds-select_container">
																		<select id="urlMethod" required  name="mod" class="slds-select">
																				<option value="CONNECT">CONNECT</option>
																				<option value="DELETE">DELETE</option>
																				<option selected value="GET">GET</option>
																				<option value="HEAD">HEAD</option>
																				<option value="OPTIONS">OPTIONS</option>
																				<option value="PATCH">PATCH</option>
																				<option value="POST">POST</option>
																				<option value="PUT">PUT</option>
																				<option value="TRACE">TRACE</option>
																		</select>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- WS Configuration options container-->
													<div class="ws-config-options">
														<div class="ws-response-user-password">
															<!-- Response time -->
															<div class="slds-form-element slds-text-align--left ws-response-time">
																<label class="slds-form-element__label" for="ws-response-time">{$MOD.wsResponseTime}</label>
																<div class="slds-form-element__control">
																	<input id="ws-response-time" class="slds-input" placeholder="{$MOD.wsResponseTime}" type="text" />
																</div>
															</div>
															<!-- User -->
															<div class="slds-form-element slds-text-align--left ws-user">
																<label class="slds-form-element__label" for="ws-user">{$MOD.wsUser} </label>
																<div class="slds-form-element__control">
																	<input id="ws-user" class="slds-input" placeholder="{$MOD.wsUser}" type="text" />
																</div>
															</div>
															<!-- Password -->
															<div class="slds-form-element slds-text-align--left ws-password">
																<label class="slds-form-element__label" for="ws-password">{$MOD.wsPassword} </label>
																<div class="slds-form-element__control">
																	<input id="ws-password" class="slds-input" placeholder="{$MOD.wsPassword}" type="text" />
																</div>
															</div>
														</div>
														<div class="ws-host-port-tag">
															<!-- Proxy Host -->
															<div class="slds-form-element slds-text-align--left ws-host">
																<label class="slds-form-element__label" for="ws-proxy-host">{$MOD.wsProxyHost}</label>
																<div class="slds-form-element__control">
																	<input id="ws-proxy-host" class="slds-input" placeholder="{$MOD.wsProxyHost}" type="text" />
																</div>
															</div>
															<!-- Proxy Port -->
															<div class="slds-form-element slds-text-align--left ws-port">
																<label class="slds-form-element__label" for="ws-proxy-port">{$MOD.wsProxyPort}</label>
																<div class="slds-form-element__control">
																	<input id="ws-proxy-port" class="slds-input" placeholder="{$MOD.wsProxyPort}" type="text" />
																</div>
															</div>
															<!-- Start Tag -->
															<div class="slds-form-element slds-text-align--left ws-tag">
																<label class="slds-form-element__label" for="ws-start-tag">{$MOD.wsStartTag}</label>
																<div class="slds-form-element__control">
																	<input id="ws-start-tag" class="slds-input" placeholder="{$MOD.wsStartTag}" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Input/Output type container -->
													<div class="ws-input-output">
														<div class="ws-input-type">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-type"><strong class="slds-text-color--error">*</strong> {$MOD.wsInputType}</label>
																<div class="slds-form-element__control">
																	<div class="slds-select_container">
																		<select id="ws-input-type" required  name="mod" class="slds-select">
																				{$listdtat}
																		</select>
																	</div>
																</div>
															</div>
														</div>
														<div class="ws-output-type">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-output-type"><strong class="slds-text-color--error">*</strong> {$MOD.wsOutputType}</label>
																<div class="slds-form-element__control">
																	<div class="slds-select_container">
																		<select id="ws-output-type" required  name="mod" class="slds-select">
																				{$listdtat}
																		</select>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- WS help text container -->
													<div class="ws-configuration-help">
														<label class="slds-form-element__label slds-text-color--error">
															{$MOD.wsrequiredFields}
														</label>
													</div>
													<!-- WS Add headers & Add button container -->
													<div class="ws-config-buttons">
														<!-- Add headers Modal -->
														<div class="slds-form-element slds-text-align--left ws-add-headers">
															<button class="slds-button slds-button--small slds-button--brand" id='ws-addheaders' data-modal-saveas-open="true" data-modal-id="ws-configuration-headers-modal" data-modal-backdrop-id="ws-configuration-headers-backdrop" disabled>{$MOD.wsAddHeaders}</button>
														</div>
														<!-- Add button -->
														<div class="slds-form-element slds-text-align--right ws-add-button">
															<button class="slds-button slds-button--small slds-button--brand"onclick="AddPopupForConfiguration(this);" data-add-button-popup="false" data-add-type="Configuration" data-add-button-validate="url-input,urlMethod,ws-input-type,ws-output-type"  data-add-relation-id="FirstModule,fixed-text-addon-pre,url-input,urlMethod,ws-response-time,ws-user,ws-password,ws-proxy-host,ws-proxy-port,ws-start-tag,ws-input-type,ws-output-type"  data-div-show="LoadShowPopup" data-add-replace="true" >{$MOD.wsAdd}</button>
														</div>
													</div>
												</div>
											</div>
										</section>
										<!-- WS Input Panel -->
										<section class="ws-accordion-item" id="ws-section-input">
											<div class="ws-accordion-header">
												<a onclick="showhideblocks(this);" id="aInput" data-div-show="LoadShowPopup">
													<span class="ws-accordion-toggle" id="ws-input">
														<i class="fa fa-arrow-right" id="ws-hide" style="display: block;"></i>
														<i class="fa fa-arrow-down"  id="ws-show" style="display: none;"></i>
													</span>
													<h4 class="ws-accordion-title">{$MOD.WSInputFields}</h4>
												</a>
											</div>
											<div class="ws-accordion-item-content" style="display: none;">
												<div class="ws-input-container">
													<!-- WS INPUT Name  -->
													<div class="ws-input-name">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-name">
																	<strong class="slds-text-color--error">*</strong>{$MOD.wsName}
																</label>
																<div class="slds-form-element__control">
																	<input id="ws-input-name" placeholder="{$MOD.wsName}" class="slds-input" type="text" required />
																</div>
															</div>
													</div>
													<!-- WS Multiple Fields & Static Value -->
													<div class="ws-input-multiple-value-container">
														<div class="ws-input-multiple">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-select-multiple">
																	<strong class="slds-text-color--error">*</strong>{$MOD.wsMultipleSelect}
																</label>
																<div class="slds-form-element__control">
																	<select id="ws-select-multiple" class="slds-select ws-select-multiple" multiple="multiple" name="selectableFields[]"> 
																		{$FirstModuleFields}
																	</select>
																</div>
															</div>
														</div>
														<div class="ws-input-static-value">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-static">{$MOD.wsValue}</label>
																<div class="slds-form-element__control">
																	<input id="ws-input-static" placeholder="{$MOD.wsOpyionalValue}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Origin & Attribute -->
													<div class="ws-input-origin-attribute-container">
														<div class="ws-input-organization">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-Origin"><strong class="slds-text-color--error">*</strong>{$MOD.wsOrigin}</label>
																<div class="slds-form-element__control">
																	<div class="slds-select_container">
																		<select id="ws-input-Origin" class="slds-select" required name="ws-input-Origin"> 
																			<option selected value="crm">crm</option>
																			<option value="map">map</option>
																		</select>
																	</div>
																</div>
															</div>
														</div>
														<div class="ws-input-attribute">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-attribute">{$MOD.wsAttribute}</label>
																<div class="slds-form-element__control">
																	<input id="ws-input-attribute" placeholder="{$MOD.wsAttribute}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Default & Format container -->
													<div class="ws-input-default-format-container">
														<div class="ws-input-default">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-default">{$MOD.wsDefault}</label>
																<div class="slds-form-element__control">
																	<input id="ws-input-default" placeholder="{$MOD.wsDefault}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
														<div class="ws-input-format">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-format">{$MOD.wsFormat}</label>
																<div class="slds-form-element__control">
																	<input id="ws-input-format" placeholder="{$MOD.wsFormat}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Help Text & Add Button -->
													<div class="ws-input-help_text-add_button">
														<!-- WS help text container -->
														<div class="ws-input-help">
															<label class="slds-form-element__label slds-text-color--error">
																{$MOD.wsrequiredFields}
															</label>
														</div>
														<!-- WS Add button container -->
														<div class="ws-input-buttons">
															<!-- Add button -->
															<div class="slds-form-element slds-text-align--right ws-add-button">
																<button class="slds-button slds-button--small slds-button--brand" onclick="AddPopupForFieldsWS(this);RestoreDataEXFIM(this);" data-add-button-popup="false" data-add-type="Input" data-add-button-validate="ws-input-name" data-show-id="FirstModule"  data-add-relation-id="FirstModule,ws-input-name,ws-select-multiple,ws-input-static,ws-input-Origin,ws-input-attribute,ws-input-default,ws-input-format"  data-div-show="LoadShowPopup" id="addpopupInput" disabled >{$MOD.wsAdd}</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</section>
										<!-- WS Output Panel -->
										<section class="ws-accordion-item" id="ws-section-output">
											<div class="ws-accordion-header">
												<a onclick="showhideblocks(this);" id="aOutput" data-div-show="LoadShowPopup">
													<span class="ws-accordion-toggle" id="ws-input">
														<i class="fa fa-arrow-right" id="ws-hide" style="display: block;"></i>
														<i class="fa fa-arrow-down"  id="ws-show" style="display: none;"></i>
													</span>
													<h4 class="ws-accordion-title">{$MOD.WSOutputFields}</h4>
												</a>
											</div>
											<div class="ws-accordion-item-content" style="display: none;">
												<div class="ws-output-container">
													<!-- WS Output Help Text Container -->
													<div class="ws-output-name-container">
														<div class="ws-output-name">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-input-name">
																	<strong class="slds-text-color--error">*</strong>{$MOD.wsName}
																</label>
																<div class="slds-form-element__control">
																	<input id="ws-output-name" placeholder="{$MOD.wsName}" class="slds-input" type="text" required />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Output Multiple Value Container-->
													<div class="ws-output-multiple-value-container">
														<!-- WS Output Multiple -->
														<div class="ws-output-multiple">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-output-select-multiple">
																	<strong class="slds-text-color--error">*</strong>{$MOD.wsMultipleSelect}
																</label>
																<div class="slds-form-element__control">
																	<select id="ws-output-select-multiple" class="slds-select ws-select-multiple" multiple="multiple" name="selectableFields[]"> 
																		{$FirstModuleFields}
																	</select>
																</div>
															</div>
														</div>
														<!-- WS Output Value -->
														<div class="ws-output-value">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-output-static">{$MOD.wsValue}</label>
																<div class="slds-form-element__control">
																	<input id="ws-output-static" placeholder="{$MOD.wsOpyionalValue}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Output Label Attribute Container -->
													<div class="ws-output-label-attribute-container">
														<!-- WS Output Label -->
														<div class="ws-output-label">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-output-label">{$MOD.wsLabel}</label>
																<div class="slds-form-element__control">
																	<input id="ws-output-label" placeholder="{$MOD.wsLabel}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
														<!-- WS Output Attribute -->
														<div class="ws-output-attribute">
															<div class="slds-form-element slds-text-align--left">
																<label class="slds-form-element__label" for="ws-output-attribute">{$MOD.wsAttribute}</label>
																<div class="slds-form-element__control">
																	<input id="ws-output-attribute" placeholder="{$MOD.wsAttribute}" class="slds-input" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Output Help Text & Add Button Container-->
													<div class="ws-output-help_text-add_button">
														<!-- WS Output Help Text -->
														<div class="ws-output-help">
															<label class="slds-form-element__label slds-text-color--error">
																{$MOD.wsrequiredFields}
															</label>
														</div>
														<!-- WS Output Add Button -->
														<div class="ws-output-buttons">
															<!-- Add button -->
															<div class="slds-form-element slds-text-align--right ws-add-button">
																<button class="slds-button slds-button--small slds-button--brand" onclick="AddPopupForOutputFieldsWS(this);RestoreDataEXFIM(this);" data-add-button-popup="false" data-add-type="Output" data-add-button-validate="ws-output-name" data-show-id="FirstModule"  data-add-relation-id="FirstModule,ws-output-name,ws-output-label,ws-output-attribute,ws-output-select-multiple,ws-output-static"  data-div-show="LoadShowPopup" id="addpopupOutput" disabled >{$MOD.wsAdd}</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</section>
										<!-- WS Value Map Panel -->
										<section class="ws-accordion-item" id="ws-section-valuemap">
											<!-- WS Value Header -->
											<div class="ws-accordion-header">
												<a onclick="showhideblocks(this);" id="aValueMap" data-div-show="LoadShowPopup" >
													<span class="ws-accordion-toggle" id="ws-error">
														<i class="fa fa-arrow-right" style="display: block;" ></i>
														<i class="fa fa-arrow-down" style="display: none;"></i>
													</span>
													<h4 class="ws-accordion-title">{$MOD.wsValuemap}</h4>
												</a>
											</div>
											<!-- WS Value Content -->
											<div class="ws-accordion-item-content" style="display: none;">
												<!-- WS Value container -->
												<div class="ws-value-container">
													<!-- WS Value Field Name & Fields Source Container -->
													<div class="ws-value-field_name-field_source-container">
														<!-- WS Value Field Name Container -->
														<div class="ws-value-field_name">
															<div class="slds-form-element">
																<label class="slds-form-element__label" for="ws-value-map-name"> <strong class="slds-text-color--error">*</strong> {$MOD.wsValuemapName}</label>
																<div class="slds-form-element__control">
																	<input id="ws-value-map-name" class="slds-input" required placeholder="{$MOD.wsValuemapName}" type="text" />
																</div>
															</div>
														</div>
														<!-- WS Value Field Source Container -->
														<div class="ws-value-field_source">
															<div class="slds-form-element">
																<label class="slds-form-element__label" for="ws-value-map-source-input"> <strong class="slds-text-color--error">*</strong> {$MOD.wsValuemapSource}</label>
																<div class="slds-form-element__control">
																	<input id="ws-value-map-source-input" class="slds-input" required placeholder="{$MOD.wsValuemapSource}" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Value Field Destination Container -->s
													<div class="ws-value-field_destination">
														<div class="slds-form-element">
															<label class="slds-form-element__label" for="ws-value-map-destinamtion">
																<strong class="slds-text-color--error">*</strong> {$MOD.wsValuemapDestination}
															</label>
															<div class="slds-form-element__control">
																<input id="ws-value-map-destinamtion" class="slds-input" required placeholder="{$MOD.wsValuemapDestination}" type="text" />
															</div>
														</div>
													</div>
													<!-- WS Value Help Text & Add Button Container -->
													<div class="ws-value-help_text-add_button-container">
														<!-- WS help text container -->
														<div class="ws-value-help">
															<label class="slds-form-element__label slds-text-color--error">
																{$MOD.wsrequiredFields}
															</label>
														</div>
														<!-- WS Add headers & Add button container -->
														<div class="ws-value-buttons">
															<div class="slds-form-element slds-text-align--right ws-add-button">
																<button class="slds-button slds-button--small slds-button--brand" onclick="AddPopupValueMapWS(this);RestoreDataEXFIM(this);" data-add-button-popup="false" data-add-type="Value Map" data-add-button-validate="ws-value-map-name" data-show-id="ws-value-map-name"  data-add-relation-id="ws-value-map-name,ws-value-map-source-input,ws-value-map-destinamtion"  data-div-show="LoadShowPopup" id="idValueMap" disabled >{$MOD.wsAdd}</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</section>
										<!-- WS Error Handler Panel -->
										<section class="ws-accordion-item" id="ws-section-error">
											<!-- WS Error Handler Header -->
											<div class="ws-accordion-header">
												<a onclick="showhideblocks(this);" id="aErrorHandler" data-div-show="LoadShowPopup" >
													<span class="ws-accordion-toggle" id="ws-error">
														<i class="fa fa-arrow-right" style="display: block;" ></i>
														<i class="fa fa-arrow-down" style="display: none;"></i>
													</span>
													<h4 class="ws-accordion-title">{$MOD.WSErrorHandler}</h4>
												</a>
											</div>
											<!-- WS Error Handler Content -->
											<div class="ws-accordion-item-content" style="display: none;">
												<div class="ws-error-handler-container">
													<!-- WS Error Name & Value Container -->
													<div class="ws-error-name-value-container">
														<!-- WS Error Name Container -->
														<div class="ws-error-name">
															<div class="slds-form-element">
																<label class="slds-form-element__label" for="ws-error-name">
																	<strong class="slds-text-color--error">*</strong> {$MOD.wsErrorName}
																</label>
																<div class="slds-form-element__control">
																	<input id="ws-error-name" class="slds-input" required placeholder="{$MOD.wsName}" type="text" />
																</div>
															</div>
														</div>
														<!-- WS Error Value Container -->
														<div class="ws-error-value">
															<div class="slds-form-element">
																<label class="slds-form-element__label" for="ws-error-value">
																	<strong class="slds-text-color--error">*</strong> {$MOD.wsErrorValue}
																</label>
																<div class="slds-form-element__control">
																	<input id="ws-error-value" class="slds-input" required placeholder="{$MOD.wsErrorValue}" type="text" />
																</div>
															</div>
														</div>
													</div>
													<!-- WS Error Message container -->
													<div class="ws-error-message">
														<div class="slds-form-element">
															<label class="slds-form-element__label" for="ws-error-message"> {$MOD.wsErrorMessage}</label>
															<div class="slds-form-element__control">
																<textarea id="ws-error-message" class="slds-textarea" placeholder="Error Message"></textarea>
															</div>
														</div>
													</div>
													<!-- WS Error Help Text & Add Button Container -->
													<div class="ws-error-help_text-add_button-container">
														<!-- WS Error Help Text Container -->
														<div class="ws-error-help">
															<label class="slds-form-element__label slds-text-color--error">
																{$MOD.wsrequiredFields}
															</label>
														</div>
														<!-- WS Error Add button Container -->
														<div class="ws-error-buttons">
															<div class="slds-form-element slds-text-align--right ws-add-button">
																<button class="slds-button slds-button--small slds-button--brand" onclick="AddPopupForErrorHandlerWS(this);RestoreDataEXFIM(this);" data-add-button-popup="false" data-add-type="Error Handler" data-add-button-validate="ws-error-name" data-show-id="FirstModule"  data-add-relation-id="ws-error-name,ws-error-value,ws-error-message"  data-div-show="LoadShowPopup" id="addpopupError" disabled >{$MOD.wsAdd}</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</section>
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

	<!-- Add Configuration Headers Modal -->
	<div class="ws-configuration-headers">
		<div class="slds-modal" aria-hidden="false" role="dialog" id="ws-configuration-headers-modal">
			<div class="slds-modal__container">
				<div class="slds-modal__header">
					<button class="slds-button slds-button--icon-inverse slds-modal__close" data-modal-saveas-close="true" data-modal-close-id="ws-configuration-headers-modal" data-modal-close-backdrop-id="ws-configuration-headers-backdrop" >
						<svg aria-hidden="true" class="slds-button__icon slds-button__icon--large">
							<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
						</svg>
						<span class="slds-assistive-text">{$MOD.close}</span>
					</button>
					<h2 class="slds-text-heading--medium">{$MOD.wsAddHeaders}</h2>
				</div>
				<div class="slds-modal__content slds-p-around--medium ws-modal-container">
					<!-- Key Name -->
					<div class="slds-form-element">
						<label class="slds-form-element__label" for="ws-key-name">{$MOD.wsKeyName}</label>
						<div class="slds-form-element__control">
							<input id="ws-key-name" name="mod" class="slds-input" required placeholder="insert {$MOD.wsKeyName}"/>
						</div>
					</div>
					<!-- Key Value -->
					<div class="slds-form-element">
						<label class="slds-form-element__label" for="ws-key-value">{$MOD.wsKeyValue}</label>
						<div class="slds-form-element__control">
							<input id="ws-key-value" name="mod" required class="slds-input" placeholder="insert {$MOD.wsKeyValue}"/>
						</div>
					</div>
				</div>
				<div class="slds-modal__footer">
					<button class="slds-button slds-button--small slds-button--brand" onclick="AddPopupForHeaders(this);RestoreDataEXFIM(this)" data-add-button-popup="false" data-add-type="Header" data-add-button-validate="ws-key-name"  data-add-relation-id="ws-key-name,ws-key-value" data-show-id="" data-div-show="LoadShowPopup">
						{$MOD.Add}
					</button>
					<button class="slds-button slds-button--small slds-button--destructive" data-modal-saveas-close="true" data-modal-close-id="ws-configuration-headers-modal" data-modal-close-backdrop-id="ws-configuration-headers-backdrop" >{$MOD.cancel}</button>
				</div>
			</div>
		</div>
		<div class="slds-backdrop" id="ws-configuration-headers-backdrop"></div>
	</div>

	<div>
		<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
		<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
		<input type="hidden" name="querysequence" id="querysequence" value="">
		<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
	</div>
</div>