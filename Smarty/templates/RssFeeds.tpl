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
<div class="flexipageComponent">
	<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
		<div class="slds-card__header slds-grid">
			<header class="slds-media slds-media--center slds-has-flexi-truncate">
				<div class="slds-media__body">
					<h2 class="header-title-container">
						<span class="slds-text-heading--small slds-truncate actionLabel"><b>{$MOD.LBL_FEEDS_LIST} {$TITLE}</b></span>
					</h2>
				</div>
			</header>
		</div>
	</article>
</div>
<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
	<tr class="slds-line-height--reset">
		<td colspan="2" valign="top" align="center" style="padding: 5px;">
			<input type="button" name="delete" value=" {$MOD.LBL_DELETE_BUTTON} " class="slds-button slds-button--small slds-button--destructive" onClick="DeleteRssFeeds('{$ID}');"/>
			<input type="button" name="setdefault" value=" {$MOD.LBL_SET_DEFAULT_BUTTON}  " class="slds-button slds-button--small slds-button--brand" onClick="makedefaultRss('{$ID}');"/>
		</td>
	</tr>
	<tr class="slds-line-height--reset">
		<td class="" colspan="2" align="left">
			<div id="rssScroll">{$RSSDETAILS}</div>
		</td>
	</tr>
</table>
