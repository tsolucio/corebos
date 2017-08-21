{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

{if $TAG_CLOUD_DISPLAY eq 'true'}
<!-- Tag cloud display -->
	<div class="flexipageComponent tagCloud" style="background-color: #fff;">
		<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
			<div class="slds-card__header slds-grid">
				<header class="slds-media slds-media--center slds-has-flexi-truncate">
					<div class="slds-media__body">
						<img src="{$IMAGE_PATH}tagCloudName.gif" border=0 />
					</div>
				</header>
			</div>
			<div class="slds-card__body slds-card__body--inner">
				<div id="tagdiv" style="display:visible;">
					<form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();">
						<input class="textbox slds-input" type="text" id="txtbox_tagfields" name="textbox_First Name" value="" style="width:150px;margin-left:5px;"/>&nbsp;&nbsp;
						<input name="button_tagfileds" type="submit" class="slds-button slds-button_success slds-button--small" value="{$APP.LBL_TAG_IT}"/>
					</form>
				</div>
				<div class="tagCloudDisplay actionData">
					<span id="tagfields"></span>
				</div>
			</div>
		</article>
	</div>
<!-- End Tag cloud display -->
{/if}
