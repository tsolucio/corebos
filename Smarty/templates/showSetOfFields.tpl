{*<!--
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
-->*}
{assign var="slds_role" value=""}
<div id="setfieldsdiv" class="slds-m-top_x-small slds-m-bottom_x-small">
	{if !empty($LAYOUT_DATA.data)}
		{if $LAYOUT_DATA.type eq "FieldList"}
			<article class="slds-card">
				<ul class="slds-accordion">
					<li class="slds-accordion__list-item">
						<section class="slds-accordion__section slds-is-open" id ="fieldlistdiv">
							<div class="slds-accordion__summary">
								<h2 class="slds-accordion__summary-heading">
									<button class="slds-button slds-button_reset slds-accordion__summary-action" id="fldbtnswitch" onClick="handleToggle('fieldlistdiv', 'fldbtnswitch');" aria-controls="fieldlistDiv" aria-expanded="true" title="">
										<svg class="slds-accordion__summary-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
										</svg>
										<span class="slds-accordion__summary-content">{$LAYOUT_DATA.blocklabel|@getTranslatedString:$LAYOUT_DATA.blockmodule}</span>
									</button>
								</h2>
							</div>
							<div class="slds-accordion__content" id="fieldlistDiv">
								<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
									<tbody>
										{foreach item=field from=$LAYOUT_DATA.data}
											<tr class="slds-hint-parent">
												{foreach item=fieldvalue from=$field}
													{if $fieldvalue.uitype eq '19'} {*Needs Review *}
														<td data-label="{$fieldvalue.labelraw}"style="background:#F7F7F7;border-left:1px solid #DEDEDE">
															<div class="slds-truncate" title="{$fieldvalue.label}">{$fieldvalue.label}</div>
														</td>
														<td data-label="" style="word-break: break-all;text-overflow: ellipsis;">
															<div class="slds-truncate" title="">{$fieldvalue.value}</div>
														</td>
													{else}
														<td data-label="{$fieldvalue.labelraw}"style="background:#F7F7F7;border-left:1px solid #DEDEDE">
															<div class="slds-truncate" title="{$fieldvalue.label}">{$fieldvalue.label}</div>
														</td>
														<td data-label="">
															<div class="slds-truncate" title="">{$fieldvalue.value}</div>
														</td>
													{/if}
												{/foreach}
											</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
						</section>
					</li>
				</ul>
			</article>
		{/if}
		{if $LAYOUT_DATA.type eq "ApplicationFields"}
			<article class="slds-card">
				<ul class="slds-accordion">
					<li class="slds-accordion__list-item">
						<section class="slds-accordion__section slds-is-open" id ="appfieldlistdiv">
							<div class="slds-accordion__summary">
								<h2 class="slds-accordion__summary-heading">
									<button class="slds-button slds-button_reset slds-accordion__summary-action" id="appfbtnswitch" onClick="handleToggle('appfieldlistdiv', 'appfbtnswitch');" aria-controls="appfieldlistDiv" aria-expanded="true" title="">
										<svg class="slds-accordion__summary-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
										</svg>
										<span class="slds-accordion__summary-content">{$LAYOUT_DATA.blocklabel|@getTranslatedString:$LAYOUT_DATA.blockmodule}</span>
									</button>
								</h2>
							</div>
							<div class="slds-accordion__content" id="appfieldlistDiv">
								<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered">
									<tbody>
										{foreach item=field from=$LAYOUT_DATA.data}
											<tr class="slds-hint-parent">
												{if $field.uitype eq '19'}
													<article class="slds-card">
													<div class="slds-card__header slds-grid">
														<header class="slds-media slds-media_center slds-has-flexi-truncate">
														<div class="slds-media__body">
															<h2 class="slds-card__header-title">
																<span>{$field.label}</span>
															</h2>
														</div>
														</header>
													</div>
													<div class="slds-card__body slds-card__body_inner">{$field.value}</div>
													</article>
												{else}
													<td data-label="{$field.labelraw}"style="background:#F7F7F7;border-left:1px solid #DEDEDE">
														<div class="slds-truncate" title="{$field.label}">{$field.label}</div>
													</td>
													<td data-label="">
														<div class="slds-truncate" title="">{$field.value}</div>
													</td>
												{/if}
												</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
						</section>
					</li>
				</ul>
			</article>
		{/if}
		{if $LAYOUT_DATA.type eq "RelatedList"}
			{include file='RelatedListNew.tpl' RELATEDLISTS=$LAYOUT_DATA.data RELLISTID=$LAYOUT_DATA.relatedlistname}
		{/if}
		{if $LAYOUT_DATA.type eq "Widget"}
			<article class="slds-card">
				<ul class="slds-accordion">
					<li class="slds-accordion__list-item">
						<section class="slds-accordion__section slds-is-open" id ="widgetdiv">
							<div class="slds-accordion__summary">
								<h2 class="slds-accordion__summary-heading">
									<button class="slds-button slds-button_reset slds-accordion__summary-action" id="widbtnswitch" onClick="handleToggle('widgetdiv', 'widbtnswitch');" aria-controls="widgetDiv" aria-expanded="true" title="">
										<svg class="slds-accordion__summary-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
										</svg>
										<span class="slds-accordion__summary-content">{$LAYOUT_DATA.data->linklabel|@getTranslatedString: $LAYOUTMODULE}</span>
									</button>
								</h2>
							</div>
							<div class="slds-accordion__content" id="widgetDiv">
								{if preg_match("/^block:\/\/.*/", $LAYOUT_DATA.data->linkurl)}
									{process_widget widgetLinkInfo=$LAYOUT_DATA.data}
								{/if}
							</div>
						</section>
					</li>
				</ul>
			</article>
		{/if}
		{if $LAYOUT_DATA.type eq "CodeWithHeader"}
			<article class="slds-card">
				<ul class="slds-accordion">
					<li class="slds-accordion__list-item">
						<section class="slds-accordion__section slds-is-open" id="codewithhdiv">
							<div class="slds-accordion__summary">
								<h2 class="slds-accordion__summary-heading">
									<button class="slds-button slds-button_reset slds-accordion__summary-action" id="codewithhbtnswitch" onClick="handleToggle('codewithhdiv', 'codewithhbtnswitch');" aria-controls="codewithhDiv" aria-expanded="true" title="">
										<svg class="slds-accordion__summary-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
										</svg>
										<span class="slds-accordion__summary-content">{$LAYOUT_DATA.label|@getTranslatedString: $LAYOUTMODULE}</span>
									</button>
								</h2>
							</div>
							<div class="slds-accordion__content" id="codewithDiv">
								{$LAYOUT_DATA.data}
							</div>
						</section>
					</li>
				</ul>
			</article>
		{/if}
		{if $LAYOUT_DATA.type eq "CodeWithoutHeader"}
			<article class="slds-card">
				<div id="codewithNoDiv">
					{$LAYOUT_DATA.data}
				</div>
			</article>
		{/if}
	{/if}
</div>
<script type ="text/javascript">
	window.handleToggle = function (divId, btnId) {
		var idInstance = document.getElementById(divId);
		if (idInstance.className == 'slds-accordion__section slds-is-open') {
			idInstance.className = 'slds-accordion__section';
			document.getElementById(btnId).setAttribute('aria-expanded', 'false');
		} else {
			idInstance.className = 'slds-accordion__section slds-is-open';
			document.getElementById(btnId).setAttribute('aria-expanded', 'true');
		}
	}
</script>