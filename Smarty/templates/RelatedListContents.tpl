{*<!--
/*+*******************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
-->*}
<script type='text/javascript' src='include/js/Mail.js'></script>

{foreach key=header item=detail from=$RELATEDLISTS}

{if is_numeric($header)}
	{if $detail.type eq 'CodeWithHeader'}
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small lvt rel_mod_table">
		<tr class="detailview_block_header">{strip}
			<td colspan=4 class="dvInnerHeader">
				<div style="float:left;font-weight:bold;"><div style="float:left;"><a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
					<span class="exp_coll_block inactivate">
					<img id="aid{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="{'LBL_Hide'|@getTranslatedString:'Settings'}" title="{'LBL_Hide'|@getTranslatedString:'Settings'}"/>
					</span>
					</a></div><b>&nbsp;{$detail.label}</b>
				</div>
			</td>{/strip}
		</tr>
		<tr>
			<td class="rel_mod_content_wrapper">
				<div id="tbl{$header|replace:' ':''}" class="rel_mod_content" style="display:block;">{include file=$detail.loadfrom}</div>
			</td>
		</tr>
	</table>
	{elseif $detail.type eq 'CodeWithoutHeader'}
		<div id="tbl{$header|replace:' ':''}" class="rel_mod_content" style="display:block;">{include file=$detail.loadfrom}</div>
	{elseif $detail.type eq 'Widget'}
		{process_widget widgetLinkInfo=$detail.instance}
	{/if}
{else}
	{assign var=rel_mod value=$header}
	{assign var="HEADERLABEL" value=$header|@getTranslatedString:$rel_mod}
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small lvt rel_mod_table">
		<tr>
			<td class="dvInnerHeader" class="rel_mod_header_wrapper">
				<div style="font-weight: bold;height: 1.75em;" class="rel_mod_header">
					<span class="toggle_rel_mod_table">
					{strip}
						<a href="javascript:loadRelatedListBlock(
							'module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}',
							'tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
							<span class="exp_coll_block activate"><img id="show_{$MODULE}_{$header|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="{'LBL_Show'|@getTranslatedString:'Settings'}" title="{'LBL_Show'|@getTranslatedString:'Settings'}"/></span>
						</a>
						<a href="javascript:hideRelatedListBlock('tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
							<span class="exp_coll_block inactivate" style="display: none"><img id="hide_{$MODULE}_{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;display:none;" alt="{'LBL_Show'|@getTranslatedString:'Settings'}" title="{'LBL_Show'|@getTranslatedString:'Settings'}"/></span>
						</a>
					{/strip}
					</span>
					&nbsp;{$HEADERLABEL}&nbsp;
					<img id="indicator_{$MODULE}_{$header|replace:' ':''}" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
					<div style="float: right;width: 2em;" class="disable_rel_mod_table">
						<a href="javascript:disableRelatedListBlock(
							'module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&ajxaction=DISABLEMODULE&relation_id={$detail.relationId}&header={$header}',
							'tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
							<img id="delete_{$MODULE}_{$header|replace:' ':''}" style="display:none;" src="{'windowMinMax.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
						</a>
					</div>
					{if $MODULE eq 'Campaigns'}
					<input id="{$MODULE}_{$header|replace:' ':''}_numOfRows" type="hidden" value="">
					<input id="{$MODULE}_{$header|replace:' ':''}_excludedRecords" type="hidden" value="">
					<input id="{$MODULE}_{$header|replace:' ':''}_selectallActivate" type="hidden" value="false">
					{/if}
				</div>
			</td>
		</tr>
		<tr>
			<td class="rel_mod_content_wrapper">
				<div id="tbl_{$MODULE}_{$header|replace:' ':''}" class="rel_mod_content"></div>
			</td>
		</tr>
	</table>
	{if $SELECTEDHEADERS neq '' && $header|in_array:$SELECTEDHEADERS}
	<script type='text/javascript'>
	{if empty($smarty.request.ajax) || $smarty.request.ajax neq 'true'}
		jQuery( window ).on('load',function() {ldelim}
			loadRelatedListBlock('module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}&start={if isset($smarty.request.start)}{$smarty.request.start|@vtlib_purify}{/if}','tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');
		{rdelim});
	{else}
		loadRelatedListBlock('module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}&start={if isset($smarty.request.start)}{$smarty.request.start|@vtlib_purify}{/if}','tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');
	{/if}
	</script>
	{/if}
{/if}
<br />
{/foreach}