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
		document.getElementById(target).style.display="block";
		showdata_element.style.display="none";
      	hidedata_element.style.display="block";
		document.getElementById('delete_'+imagesuffix).style.display="block";
		return;
	}
	var indicator = 'indicator_'+imagesuffix;
	var indicator_element = document.getElementById(indicator);
	indicator_element.style.display="block";
	document.getElementById('delete_'+imagesuffix).style.display="block";
	
	var target_element = document.getElementById(target);
	
	jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urldata,
		}).done(function (response) {
					var responseData = trim(response);
      				target_element.update(responseData);
					target_element.style.display="block";
      				showdata_element.style.display="none";
      				hidedata_element.style.display="block";
      				indicator_element.style.display="none";
					if(document.getElementById('return_module').value == 'Campaigns'){
						var obj = document.getElementsByName(imagesuffix+'_selected_id');
						var relatedModule = imagesuffix.replace('Campaigns_',"");
						document.getElementById(relatedModule+'_count').innerHTML = numofRows;
						if(selectallActivation == 'true'){
							document.getElementById(imagesuffix+'_selectallActivate').value='true';
							document.getElementById(imagesuffix+'_linkForSelectAll').style.display="block";
							document.getElementById(imagesuffix+'_selectAllRec').style.display='none';
							document.getElementById(imagesuffix+'_deSelectAllRec').style.display='inline';
							var exculdedArray=excludedRecords.split(';');
							if (obj) {
								var viewForSelectLink = showSelectAllLink(obj,exculdedArray);
								document.getElementById(imagesuffix+'_selectCurrentPageRec').checked = viewForSelectLink;
								document.getElementById(imagesuffix+'_excludedRecords').value = document.getElementById(imagesuffix+'_excludedRecords').value+excludedRecords;
							}
						}else{
							document.getElementById(imagesuffix+'_linkForSelectAll').style.display="none";
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
	
	var target_element = document.getElementById(target);
	if(target_element){
		target_element.style.display="none";
	}
	hidedata_element.style.display="none";
	showdata_element.style.display="block";
	document.getElementById('delete_'+imagesuffix).style.display="none";
}

function disableRelatedListBlock(urldata,target,imagesuffix){
	var showdata = 'show_'+imagesuffix;
	var showdata_element = document.getElementById(showdata);

	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = document.getElementById(hidedata);

	var indicator = 'indicator_'+imagesuffix;
	var indicator_element = document.getElementById(indicator);
	indicator_element.style.display="block";
	
	var target_element = document.getElementById(target);
	jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urldata,
		}).done(function (response) {
					var responseData = trim(response);
					target_element.style.display="none";
					document.getElementById('delete_'+imagesuffix).style.display="none";
					hidedata_element.style.display="none";
					showdata_element.style.display="block";
					indicator_element.style.display="none";
			}
	);
}

{/literal}
</script>

{foreach key=header item=detail from=$RELATEDLISTS}

{assign var=rel_mod value=$header}
{assign var="HEADERLABEL" value=$header|@getTranslatedString:$rel_mod}

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small lvt">
	<tr>
		<td class="dvInnerHeader">
			<div style="font-weight: bold;height: 1.75em;">
				<span>
					<a href="javascript:loadRelatedListBlock(
						'module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}',
						'tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
						<img id="show_{$MODULE}_{$header|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Display" title="Display"/>
					</a>
					<a href="javascript:hideRelatedListBlock('tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');">
						<img id="hide_{$MODULE}_{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;display:none;" alt="Display" title="Display"/>
					</a>					
				</span>
				&nbsp;{$HEADERLABEL}&nbsp;
				<img id="indicator_{$MODULE}_{$header|replace:' ':''}" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
				<div style="float: right;width: 2em;">
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
		<td>
			<div id="tbl_{$MODULE}_{$header|replace:' ':''}"></div>
		</td>
	</tr>
</table>
<br />
{if $SELECTEDHEADERS neq '' && $header|in_array:$SELECTEDHEADERS}
<script type='text/javascript'>
{if $smarty.request.ajax neq 'true'}
	jQuery( window ).load(function() {ldelim}
		loadRelatedListBlock('module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}&start={$smarty.request.start|@vtlib_purify}','tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');
	{rdelim});
{else}
	loadRelatedListBlock('module={$MODULE}&action={$MODULE}Ajax&file=DetailViewAjax&record={$ID}&ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$detail.relationId}&actions={$detail.actions}&parenttab={$CATEGORY}&start={$smarty.request.start|@vtlib_purify}','tbl_{$MODULE}_{$header|replace:' ':''}','{$MODULE}_{$header|replace:' ':''}');
{/if}
</script>
{/if}
{/foreach}