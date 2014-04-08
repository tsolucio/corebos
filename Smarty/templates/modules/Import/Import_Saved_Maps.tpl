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

<span class="small">{'LBL_USE_SAVED_MAPPING'|@getTranslatedString:$MODULE}</span>&nbsp;&nbsp;
<select name="saved_maps" id="saved_maps" class="small" onchange="ImportJs.loadSavedMap();">
	<option id="-1" value="" selected>--{'LBL_SELECT'|@getTranslatedString:$MODULE}--</option>
	{foreach key=_MAP_ID item=_MAP from=$SAVED_MAPS}
	<option id="{$_MAP_ID}" value="{$_MAP->getStringifiedContent()}">{$_MAP->getValue('name')}</option>
	{/foreach}
</select>
<span id="delete_map_container" style="display:none;">
	<img valign="absmiddle" src="{'delete.gif'|@vtiger_imageurl:$THEME}" style="cursor:pointer;"
		 onclick="ImportJs.deleteMap('{$FOR_MODULE}');" alt="{'LBL_DELETE'|@getTranslatedString:$FOR_MODULE}" />
</span>