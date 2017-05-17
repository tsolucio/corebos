{*<!--
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************//
-->*}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
</script>
<h2>{'LBL_ACTION'|@getTranslatedString:'com_vtiger_workflow'}</h2>
&nbsp;&nbsp;<input type="radio" name="addrel" value="1" {if isset($task->addrel) && $task->addrel eq 1}checked{/if}> {'Add Tag'|@getTranslatedString:'com_vtiger_workflow'}<br>
&nbsp;&nbsp;<input type="radio" name="addrel" value="0" {if isset($task->addrel) && $task->addrel eq 0}checked{/if}> {'Delete Tag'|@getTranslatedString:'com_vtiger_workflow'}<br><br>
&nbsp;&nbsp;<input type="radio" name="forallusers" value="1" {if isset($task->forallusers) && $task->forallusers eq 1}checked{/if}> {'ForAllUsers'|@getTranslatedString:'com_vtiger_workflow'}
&nbsp;&nbsp;{'LBL_OR'|@getTranslatedString}
&nbsp;&nbsp;<input type="radio" name="forallusers" value="0" {if isset($task->forallusers) && $task->forallusers eq 0}checked{/if}> {'ForCurrentUser'|@getTranslatedString:'com_vtiger_workflow'}<br>
<h2>{'Tags'|@getTranslatedString:'com_vtiger_workflow'}</h2>
<input id="listoftags" name="listoftags" type="text" value="{if isset($task->listoftags)}{$task->listoftags}{/if}">
