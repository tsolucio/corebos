{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
-->*}
<table width="100%" class="layerPopupTransport" cellpadding="5">
	<tr>
		<td align="left" class="small">
			{$recordListRange}
		</td>
		<td align="right" class="small">
			<a href="javascript:;" onClick="ImportJs.loadListViewPage('{$FOR_MODULE}', 1, '{$FOR_USER}');" title="{'LBL_FIRST'|@getTranslatedString:$FOR_MODULE}">
				<img src="{'start.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{'LBL_FIRST'|@getTranslatedString:$FOR_MODULE}">
			</a>
			<a href="javascript:;" onClick="ImportJs.loadListViewPage('{$FOR_MODULE}', {$CURRENT_PAGE}-1, '{$FOR_USER}');" title="{'LNK_LIST_PREVIOUS'|@getTranslatedString:$FOR_MODULE}">
				<img src="{'previous.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{'LNK_LIST_PREVIOUS'|@getTranslatedString:$FOR_MODULE}">
			</a>
			<input class="small" id="page_num" type="text" value="{$CURRENT_PAGE}" style="width: 3em;margin-right: 0.7em;"
				   onchange="ImportJs.loadListViewSelectedPage('{$FOR_MODULE}', '{$FOR_USER}');"
				   onkeypress="return VT_disableFormSubmit(event);" />
			<a href="javascript:;" onClick="ImportJs.loadListViewPage('{$FOR_MODULE}', {$CURRENT_PAGE}+1, '{$FOR_USER}');" title="{'LNK_LIST_NEXT'|@getTranslatedString:$FOR_MODULE}">
				<img src="{'next.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{'LNK_LIST_NEXT'|@getTranslatedString:$FOR_MODULE}">
			</a>
			<a href="javascript:;" onClick="ImportJs.loadListViewPage('{$FOR_MODULE}', 'last', '{$FOR_USER}');" title="{'LBL_LAST'|@getTranslatedString:$FOR_MODULE}">
				<img src="{'end.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{'LBL_LAST'|@getTranslatedString:$FOR_MODULE}">
			</a>
		</td>
	</tr>
</table>
<table border=0 cellspacing=1 cellpadding=3 width=100% class="lvt small">
	<tr>
		{foreach name="listviewforeach" item=header from=$LISTHEADER}
		<td class="lvtCol">{$header|@strip_tags}</td>
		{/foreach}
	</tr>
	{foreach item=entity key=entity_id from=$LISTENTITY}
		<tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" id="row_{$entity_id}">
			{foreach item=data from=$entity}
			{* vtlib customization: Trigger events on listview cell *}
			<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$data}</td>
			{* END *}
	        {/foreach}
		</tr>
	{foreachelse}
		<tr>
			<td style="background-color:#efefef;height:340px" align="center" colspan="{$smarty.foreach.listviewforeach.iteration+1}">
				<div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 10000000;">
					<table border="0" cellpadding="5" cellspacing="0" width="98%">
						<tr>
							<td rowspan="2" width="25%"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
							<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%">
								<span class="genHeaderSmall">
								{'LBL_NO'|@getTranslatedString:$FOR_MODULE} {$FOR_MODULE|@getTranslatedString:$FOR_MODULE} {'LBL_FOUND'|@getTranslatedString:$FOR_MODULE} !
								</span>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	{/foreach}
</table>