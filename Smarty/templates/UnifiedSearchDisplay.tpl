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
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/ListView.js"></script>
<div id="searchResultContainerId">
<input name="globalSearchText" id="globalSearchText" type="hidden" value="{$SEARCH_STRING}" />
<input name="tagSearchText" id="tagSearchText" type="hidden" value="{$TAG_SEARCH}" />
{include file='UnifiedSearchAjax.tpl'}
</div>