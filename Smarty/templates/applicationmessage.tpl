{*<!--
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
{if !empty($ERROR_MESSAGE)}
{if empty($ERROR_MESSAGE_CLASS) || $ERROR_MESSAGE_CLASS eq "cb-alert-danger"}
	{assign var="slds_role" value="error"}
{elseif $ERROR_MESSAGE_CLASS eq "cb-alert-warning"}
	{assign var="slds_role" value="warning"}
{elseif $ERROR_MESSAGE_CLASS eq "cb-alert-info"}
	{assign var="slds_role" value="info"}
{elseif $ERROR_MESSAGE_CLASS eq "cb-alert-success"}
	{assign var="slds_role" value="success"}
{else}
	{assign var="slds_role" value="error"}
{/if}
<div class="slds-m-top_x-small slds-m-bottom_x-small">
	<div class="slds-notify slds-notify_alert slds-theme_{$slds_role} slds-theme_alert-texture" role="alert" style="padding:0.1rem">
	<h2>
		<svg class="slds-icon slds-icon_small slds-m-right_x-small" aria-hidden="true">
		<use xlink:href="include/LD//assets/icons/utility-sprite/svg/symbols.svg#{$slds_role}"></use>
		</svg>{$ERROR_MESSAGE}
	</h2>
	</div>
</div>
{/if}
