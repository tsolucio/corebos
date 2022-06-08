{*<!--
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.
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
<script src='modules/com_vtiger_workflow/resources/vtigerwebservices.js' charset="utf-8"></script>
<script>
	var moduleName = '{$entityName}';
</script>
<script src="modules/com_vtiger_workflow/resources/onesignalworkflowtaskscript.js" type="text/javascript" charset="utf-8"></script>
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="notification_heading"><b>{'LBL_ONESIGNAL_NOTIFICATION_HEADING'|@getTranslatedString:'com_vtiger_workflow'}</b></label>
			<div class="slds-form-element__control">
				<input type="text" class="slds-input slds-page-header__meta-text" name="notification_heading" id="notification_heading" value="{$task->notification_heading}" />
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="notification_subtitle"><b>{'LBL_ONESIGNAL_NOTIFICATION_SUBTITLE'|@getTranslatedString:'com_vtiger_workflow'}</b></label>
			<div class="slds-form-element__control">
				<input type="text" class="slds-input slds-page-header__meta-text" name="notification_subtitle" id="notification_subtitle" value="{$task->notification_subtitle}" />
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-gutters slds-grid_vertical-align-center slds-p-horizontal_x-large">
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="external_user_id" value="{if isset($task->external_user_id)}{$task->external_user_id}{/if}" id="save_fromname"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfieldsfrmname-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="task-emailfieldsfrmname" style="display: none;" class="slds-select slds-page-header__meta-text">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>

{* <div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="notification_include_segment"><b>{'LBL_ONESIGNAL_NOTIFICATION_INCLUDE_SEGMENT'|@getTranslatedString:'com_vtiger_workflow'}</b></label>
			<div class="slds-form-element__control">
				<input type="text" class="slds-input slds-page-header__meta-text" name="notification_include_segment" id="notification_include_segment" value="{$task->notification_include_segment}" />
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="notification_excluded_segment"><b>{'LBL_ONESIGNAL_NOTIFICATION_EXCLUDED_SEGMENT'|@getTranslatedString:'com_vtiger_workflow'}</b></label>
			<div class="slds-form-element__control">
				<input type="text" class="slds-input slds-page-header__meta-text" name="notification_excluded_segment" id="notification_excluded_segment" value="{$task->notification_excluded_segment}" />
			</div>
		</div>
	</div>
</div> *}

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="notification_web_url"><b>{'pushnoturl'|@getTranslatedString:'com_vtiger_workflow'}</b></label>
			<div class="slds-form-element__control">
				<input type="text" class="slds-input slds-page-header__meta-text" name="notification_web_url" id="notification_web_url" value="{$task->notification_web_url}" />
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-gutters slds-grid_vertical-align-center slds-p-horizontal_x-large">
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
				<div class="slds-select_container">
					<select  id='task-fieldnames' style="display: none;" class="slds-select slds-page-header__meta-text">
						<option value=''> {$MOD.LBL_SELECT_OPTION_DOTDOTDOT} </option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="task_timefields" name="metavariable" class="slds-select slds-page-header__meta-text">
						<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
						{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
						<option value="{$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="notification_content"><b>{'LBL_NOTIFICATION_CONTENT'|@getTranslatedString:'com_vtiger_workflow'}</b></label>
			<div class="slds-form-element__control">
			<textarea class="slds-textarea" name="notification_content" rows="55" cols="40" id="notification_content"> {$task->notification_content} </textarea>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" defer="1">
	var textAreaName = 'notification_content';
	CKEDITOR.replace( textAreaName,	{ldelim}
		customConfig: '../../modules/com_vtiger_workflow/resources/onesignalckeditor.js'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>