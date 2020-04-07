{*<!--
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
-->*}

{include file='Buttons_List.tpl'}

<div class="slds-card slds-m-left_small slds-m-right_small slds-m-bottom_small slds-p-around_small">
	<div class="slds-card slds-p-around_small">
	<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php">
		<input type="hidden" name="module" value="cbupdater">
		<input type="hidden" name="action" value="importxml">
		<input name="zipfile" type="file" value="" tabindex="1"/>
		<input name="zipfile_hidden" type="hidden" value=""/>
		<br><br>
		<button class="slds-button slds-button_neutral" type="submit" id="save" name="import" >
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#upload"></use>
			</svg>
			{'ImportXML'|@getTranslatedString:'cbupdater'}
		</button>
	</form>
	</div>
</div>
