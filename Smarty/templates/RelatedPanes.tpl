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
	<div class="detailview_utils_table_tab detailview_utils_table_tab_selected detailview_utils_table_tab_selected_{$tabposition}">{$RLARR.label}</div>
{else}
	<div class="detailview_utils_table_tab detailview_utils_table_tab_unselected detailview_utils_table_tab_unselected_{$tabposition}"
	{if $tabposition eq 'top'}onmouseout="fnHideDrop('More_Information_pane{$RLTAB}_List');" onmouseover="fnDropDown(this,'More_Information_pane{$RLTAB}_List');"{/if}>
	<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&RelatedPane={$RLTAB}">{$RLARR.label}</a>
	{if empty($tabcache) || $tabcache neq 'dvtTabCacheBottom'}
	<div onmouseover="fnShowDrop('More_Information_pane{$RLTAB}_List')" onmouseout="fnHideDrop('More_Information_pane{$RLTAB}_List')"
				 id="More_Information_pane{$RLTAB}_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		{foreach key=_BLOCK_ID item=_RELATED_BLOCK from=$RLARR.blocks}
			<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&RelatedPane={$RLTAB}&selected_header={$_RELATED_BLOCK.loadfrom}&relation_id={if isset($_RELATED_BLOCK.relatedid)}{$_RELATED_BLOCK.relatedid}{/if}#tbl_{$MODULE}_{$_RELATED_BLOCK.loadfrom}">{$_RELATED_BLOCK.label|@getTranslatedString:$_RELATED_BLOCK.label}</a></td></tr>
		{/foreach}
		</table>
	</div>
	{/if}
	</div>
{/if}
{/foreach}
