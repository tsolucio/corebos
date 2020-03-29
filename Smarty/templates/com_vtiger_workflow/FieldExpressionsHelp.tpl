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

{extends file='Components/TooltipInfo.tpl'}
{block name=TOOLTIPInfo}
	<span style="width:50rem;">
		<header class="slds-popover__header">
			<h2 class="slds-text-heading_small">{$MOD.LBL_RAW_TEXT}</h2>
		</header>
		<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
			<p>2000<br>any text</p>
		</div>
		<header class="slds-popover__header">
			<h2 class="slds-text-heading_small">{$MOD.LBL_FIELD}</h2>
		</header>
		<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
			<p>annual_revenue<br>accountname</p>
		</div>
		<header class="slds-popover__header">
			<h2 class="slds-text-heading_small">{$MOD.LBL_EXPRESSION}</h2>
		</header>
		<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
			<p>annual_revenue / 12<br>
			<span style="color:blue;">if</span> mailingcountry == 'Spain' <span style="color:blue;">then</span> <span style="color:blue;">concat</span>(firstname,' ',lastname) <span style="color:blue;">else</span> <span style="color:blue;">concat</span>(lastname,' ',firstname) <span style="color:blue;">end</span>
			</p>
		</div>
		<header class="slds-popover__header">
			<h2 class="slds-text-heading_small">{$APP.LBL_MORE}</h2>
		</header>
		<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
			<p>See the <code>testexpression</code> variable in <a href="https://github.com/tsolucio/coreBOSTests/blob/master/modules/com_vtiger_workflow/expression_engine/VTExpressionEvaluaterTest.php" target="_blank">the unit tests</a>.</p>
		</div>
	</span>
{/block}
