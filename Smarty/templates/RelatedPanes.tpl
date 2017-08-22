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

{foreach key=RLTAB item=RLARR from=$RLTabs}
{if $RETURN_RELATEDPANE eq $RLTAB}
	<li class="slds-tabs--scoped__item" title="{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}" role="presentation">
		<a class="slds-tabs--scoped__link " href="javascript:void(0);" role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1" id="tab--scoped--1__item">{$RLARR.label}</a>
	</li>
{else}
	<li class="slds-tabs--scoped__item slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open" role="presentation">
		<a class="slds-tabs--scoped__link" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&RelatedPane={$RLTAB}" role="tab" tabindex="-1" aria-selected="false" aria-controls="tab--scoped-2">{$RLARR.label}</a>
		{if empty($tabcache) || $tabcache neq 'dvtTabCacheBottom'}
			<div class="slds-dropdown slds-dropdown--left" style="margin-top: 0;">
				<ul class="slds-dropdown__list slds-dropdown--length-7" role="menu">
					{foreach key=_BLOCK_ID item=_RELATED_BLOCK from=$RLARR.blocks}
						<li class="slds-dropdown__item" role="presentation">
							<a role="menuitem" tabindex="-1" class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&RelatedPane={$RLTAB}&selected_header={$_RELATED_BLOCK.loadfrom}&relation_id={$_RELATED_BLOCK.relatedid}#tbl_{$MODULE}_{$_RELATED_BLOCK.loadfrom}">{$_RELATED_BLOCK.label|@getTranslatedString:$MODULE}</a>
						</li>
					{/foreach}
				</ul>
			</div>
		{/if}
	</li>
{/if}
{/foreach}