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

<input type="button" name="next" value="{'LBL_IMPORT_MORE'|@getTranslatedString:$MODULE}" class="crmButton big create"
	   onclick="location.href='index.php?module={$FOR_MODULE}&action=Import&return_module={$FOR_MODULE}&return_action=index'" />
&nbsp;&nbsp;
<input type="button" name="next" value="{'LBL_VIEW_LAST_IMPORTED_RECORDS'|@getTranslatedString:$MODULE}" class="crmButton big cancel"
	   onclick="return window.open('index.php?module={$FOR_MODULE}&action={$FOR_MODULE}Ajax&file=Import&mode=listview&start=1&foruser={$OWNER_ID}','test','width=700,height=650,resizable=1,scrollbars=0,top=150,left=200');" />
&nbsp;&nbsp;
{if $MERGE_ENABLED eq '0'}
<input type="button" name="next" value="{'LBL_UNDO_LAST_IMPORT'|@getTranslatedString:$MODULE}" class="crmButton big delete"
	   onclick="location.href='index.php?module={$FOR_MODULE}&action=Import&mode=undo_import&foruser={$OWNER_ID}'" />
&nbsp;&nbsp;
{/if}
<input type="button" name="cancel" value="{'LBL_FINISH_BUTTON_LABEL'|@getTranslatedString:$MODULE}" class="crmButton big edit"
	   onclick="location.href='index.php?module={$FOR_MODULE}&action=index'" />