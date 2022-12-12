
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
 *************************************************************************************************/
-->*}
<script type="text/javascript">
$(document).ready(function () {
	document.getElementById('closeBtn').addEventListener('click', function() {
	document.getElementById('denorm_responsebody').style.display = 'none';
});
});
</script>
<section id="denorm_responsebody" role="dialog" tabindex="-1" aria-labelledby="modal-heading-01" aria-modal="true" aria-describedby="modal-content-id-1" class="slds-modal slds-fade-in-open">
<div class="slds-modal__container" style="height:fit-content; border:20px;">
	<header class="slds-modal__header">
		<h2 id="modal-heading-01" class="slds-modal__title slds-hyphenate">{'denormalize response'|@getTranslatedString}</h2>
	</header>
	<div class="slds-modal__content slds-p-around_medium" id="modal-content-id-1">
		<p>{$DENORM_RESPONSE}</p>
		</div>
	<footer class="slds-modal__footer">
		<button id="closeBtn" class="slds-button slds-button_brand">{'LBL_CLOSE'|@getTranslatedString:'Settings'}</button>
	</footer>
</div>
</section>