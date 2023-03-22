{*<!--
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

{foreach key=RLTAB item=RLARR from=$RLTabs name=rltab}
{if $RETURN_RELATEDPANE eq $RLTAB}
	<li class="slds-tabs_{$TABSCOPED}__item slds-is-active" role="presentation">
		<a class="slds-tabs_{$TABSCOPED}__link"role="tab" tabindex="0" aria-selected="true" style="font-weight: 600;font-size: 13px">
			{$RLARR.label}
		</a>
	</li>
{else}
{if !isset($rlmode)}
<div class="slds-tabs_{$TABSCOPED} slds-tabs_medium">
	<ul class="slds-tabs_{$TABSCOPED}__nav" role="tablist">
	{/if}
		{if $smarty.foreach.rltab.index eq 0 && $rlmode neq 'RelatedPane'}
		<li class="slds-tabs_{$TABSCOPED}__item slds-is-active" role="presentation">
			<a class="slds-tabs_{$TABSCOPED}__link" role="tab" tabindex="0" aria-selected="true" style="font-weight: 600;font-size: 13px">
				<span class="{$currentModuleIcon['containerClass']}">
					<svg class="slds-icon slds-icon_small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/{$currentModuleIcon['library']}-sprite/svg/symbols.svg#{$currentModuleIcon['icon']}"></use>
					</svg>
				</span>
				{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}
			</a>
		</li>
		{/if}
		<li class="slds-tabs_{$TABSCOPED}__item" role="presentation">
			<div class="slds-dropdown-trigger slds-dropdown-trigger_hover">
				<a class="slds-tabs_{$TABSCOPED}__link" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&RelatedPane={$RLTAB}" style="font-size: 12px">
					{$RLARR.label}
				</a>
				{if empty($tabcache) || $tabcache neq 'dvtTabCacheBottom'}
				<div class="slds-dropdown slds-dropdown_left">
					<ul class="slds-dropdown__list slds-dropdown_length-with-icon-10 cbds-scrollbar" role="menu">
						{foreach key=_BLOCK_ID item=_RELATED_BLOCK from=$RLARR.blocks}
						<li class="slds-dropdown__item" role="presentation">
							<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&RelatedPane={$RLTAB}&selected_header={$_RELATED_BLOCK.loadfrom}&relation_id={if isset($_RELATED_BLOCK.relatedid)}{$_RELATED_BLOCK.relatedid}{/if}#tbl_{$MODULE}_{$_RELATED_BLOCK.loadfrom}" role="menuitem" tabindex="-1">
								<span class="slds-truncate">
									<span class="slds-media slds-media_center">
										<span class="slds-media__body" style="font-size: 12px">
											{$_RELATED_BLOCK.label|@getTranslatedString:$_RELATED_BLOCK.label}
										</span>
									</span>
								</span>
							</a>
						</li>
						{/foreach}
					</ul>
				</div>
				{/if}
			</div>
		</li>
		{if !isset($rlmode)}
	</ul>
</div>
{/if}
{/if}
{/foreach}
