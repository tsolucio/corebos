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
<script type='text/javascript'>
{literal}

function isRelatedListBlockLoaded(id,urldata){
	var elem = document.getElementById(id);
	if(elem == null || typeof elem == 'undefined' || urldata.indexOf('order_by') != -1 ||
		urldata.indexOf('start') != -1 || urldata.indexOf('withCount') != -1){
		return false;
	}
	var tables = elem.getElementsByTagName('table');
	return tables.length > 0;
}

function loadRelatedListBlock(urldata,target,imagesuffix) {
	if( document.getElementById('return_module').value == 'Campaigns'){
		var selectallActivation = document.getElementById(imagesuffix+'_selectallActivate').value;
		var excludedRecords = document.getElementById(imagesuffix+'_excludedRecords').value = document.getElementById(imagesuffix+'_excludedRecords').value;
		var numofRows = document.getElementById(imagesuffix+'_numOfRows').value;
	}
	var showdata = 'show_'+imagesuffix;
	var showdata_element = document.getElementById(showdata);

	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = document.getElementById(hidedata);
	if(isRelatedListBlockLoaded(target,urldata) == true){
		jQuery('#'+target).show();
		jQuery(showdata_element).hide();
		showdata_element.parentElement.style.display = "none";
		jQuery(hidedata_element).show();
		hidedata_element.parentElement.style.display = "inline-block";
		jQuery('#delete_'+imagesuffix).show();
		return;
	}
	var indicator = 'indicator_'+imagesuffix;
	var indicator_element = document.getElementById(indicator);
	jQuery(indicator_element).show();
	jQuery('#delete_'+imagesuffix).show();

	var target_element = document.getElementById(target);
	
	jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urldata,
		}).done(function (response) {
					var responseData = trim(response);
					target_element.innerHTML=responseData;
					jQuery(target_element).show();
					jQuery(showdata_element).hide();
					showdata_element.parentElement.style.display = "none";
					jQuery(hidedata_element).show();
					hidedata_element.parentElement.style.display = "inline-block";
					jQuery(indicator_element).hide();
					if(document.getElementById('return_module').value == 'Campaigns'){
						var obj = document.getElementsByName(imagesuffix+'_selected_id');
						var relatedModule = imagesuffix.replace('Campaigns_',"");
						document.getElementById(relatedModule+'_count').innerHTML = numofRows;
						if(selectallActivation == 'true'){
							document.getElementById(imagesuffix+'_selectallActivate').value='true';
							jQuery('#'+imagesuffix+'_linkForSelectAll').show();
							document.getElementById(imagesuffix+'_selectAllRec').style.display='none';
							document.getElementById(imagesuffix+'_deSelectAllRec').style.display='inline';
							var exculdedArray=excludedRecords.split(';');
							if (obj) {
								var viewForSelectLink = showSelectAllLink(obj,exculdedArray);
								document.getElementById(imagesuffix+'_selectCurrentPageRec').checked = viewForSelectLink;
								document.getElementById(imagesuffix+'_excludedRecords').value = document.getElementById(imagesuffix+'_excludedRecords').value+excludedRecords;
							}
						}else{
							jQuery('#'+imagesuffix+'_linkForSelectAll').hide();
							//rel_toggleSelect(false,imagesuffix+'_selected_id',relatedModule);
						}
						updateParentCheckbox(obj,imagesuffix);
					}
			}
	);
}

function hideRelatedListBlock(target, imagesuffix) {
	var showdata = 'show_'+imagesuffix;
	var showdata_element = document.getElementById(showdata);
	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = document.getElementById(hidedata);
	jQuery('#'+target).hide();
	jQuery('#hide_'+imagesuffix).hide();
	hidedata_element.parentElement.style.display = 'none';
	jQuery('#show_'+imagesuffix).show();
	showdata_element.parentElement.style.display = 'inline-block';
	jQuery('#delete_'+imagesuffix).hide();
}

function disableRelatedListBlock(urldata,target,imagesuffix){
	jQuery('#indicator_'+imagesuffix).show();
	jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urldata,
		}).done(function (response) {
			var responseData = trim(response);
			jQuery('#'+target).hide();
			jQuery('#delete_'+imagesuffix).hide();
			jQuery('#hide_'+imagesuffix).hide();
			jQuery('#show_'+imagesuffix).show();
			jQuery('#indicator_'+imagesuffix).hide();
		}
	);
}

{/literal}
</script>

{foreach key=header item=detail from=$RELATEDLISTS}

{assign var=rel_mod value=$header}
{assign var="HEADERLABEL" value=$header|@getTranslatedString:$rel_mod}

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small lvt rel_mod_table">
	<tr>
		<td class="dvInnerHeader" class="rel_mod_header_wrapper">
			<div style="font-weight: bold;height: 1.75em;" class="rel_mod_header">
				<span class="toggle_rel_mod_table">
					<a href="javascript:loadRelatedListBlock(
						'module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}',
						'tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
						<span class="exp_coll_block activate"><img id="show_{$MODULE}_{$header|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="{'LBL_Show'|@getTranslatedString:'Settings'}" title="{'LBL_Show'|@getTranslatedString:'Settings'}"/></span>
					</a>
					<a href="javascript:hideRelatedListBlock('tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
						<span class="exp_coll_block inactivate" style="display: none"><img id="hide_{$MODULE}_{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;display:none;" alt="{'LBL_Show'|@getTranslatedString:'Settings'}" title="{'LBL_Show'|@getTranslatedString:'Settings'}"/></span>
					</a>
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
<br />
{if $SELECTEDHEADERS neq '' && $header|in_array:$SELECTEDHEADERS}
<script type='text/javascript'>
{if $smarty.request.ajax neq 'true'}
	jQuery( window ).on('load',function() {ldelim}
		loadRelatedListBlock('module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}&start={$smarty.request.start|@vtlib_purify}','tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');
	{rdelim});
{else}
	loadRelatedListBlock('module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}&start={$smarty.request.start|@vtlib_purify}','tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');
{/if}
</script>
{/if}
{/foreach}