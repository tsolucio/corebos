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
<!-- BEGIN: main -->
<div id="roleLay" style="z-index:12;display:inline-table;width:400px;" class="layerPopup">
	<input name="excludedRecords" type="hidden" id="excludedRecords" value="{$EXE_REC}">
	<input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
	<input name='viewid' id="viewid" type='hidden' value='{$VIEWID}'>
	<input name='recordid' id="recordid" type='hidden' value='{$RECORDID}'>
	<table class="slds-table slds-no-row-hover layerHeadingULine" width="100%">
		<tr class="slds-text-title--header">
			<th scope="col">
				<div class="slds-truncate moduleName">
					{$MOD.SELECT_EMAIL}
				</div>
			</th>
			<th scope="col">
				<div class="slds-truncate">
					<span>
						{if $ONE_RECORD neq 'true'}
							({$MOD.LBL_MULTIPLE} {$FROM_MODULE|getTranslatedString:$FROM_MODULE})
						{/if}
					</span>
				</div>
			</th>
			<th scope="col" style="padding: .5rem;">
				<div class="slds-truncate">
					<a href="javascript:fninvsh('roleLay');">
						<img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" />
					</a>
				</div>
			</th>
		</tr>
	</table>
	<table width="100%">
		<tr class="slds-line-height--reset">
			<td class="small">
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<span class="slds-truncate slds-m-right--xx-small">
										{if $ONE_RECORD eq 'true'}
											<b>{$ENTITY_NAME}</b> {$MOD.LBL_MAILSELECT_INFO}.
										{else}
											{$MOD.LBL_MAILSELECT_INFO1} {$FROM_MODULE|getTranslatedString:$FROM_MODULE}.{$MOD.LBL_MAILSELECT_INFO2}
										{/if}
									</span>
								</div>
							</header>
						</div>
					</article>
				</div>
				<div style="height:120px;overflow-y:auto;overflow-x:hidden;" align="center">
							<table class="slds-table slds-no-row-hover">
								{foreach name=emailids key=fieldid item=elements from=$MAILINFO}
								<tr class="slds-line-height--reset">
									{if $smarty.foreach.emailids.iteration eq 1}
										<td class="dvtCellLabel">
											<span class="slds-checkbox">
												<input type="checkbox" checked value="{$fieldid}" id="email_{$fieldid}" name="semail"/>
												<label class="slds-checkbox__label" for="email_{$fieldid}">
													<span class="slds-checkbox--faux"></span>
												</label>
											</span>
										</td>
									{else}
										<td class="dvtCellLabel">
											<span class="slds-checkbox">
												<input type="checkbox" value="{$fieldid}" id="email_{$fieldid}" name="semail"/>
												<label class="slds-checkbox__label" for="other_{$fieldid}">
													<span class="slds-checkbox--faux"></span>
												</label>
											</span>
										</td>
									{/if}
									{if $PERMIT eq '0'}
										{if $ONE_RECORD eq 'true'}
											<td class="dvtCellInfo"><b>{$elements.0}</b><br>{$MAILDATA[$smarty.foreach.emailids.index]}</td>
										{else}
											<td class="dvtCellInfo"><b>{$elements.0}</b></td>
										{/if}
									{else}
										<td class="dvtCellInfo"><b>{$elements.0}</b><br>{$MAILDATA[$smarty.foreach.emailids.index]}</td>
									{/if}
								</tr>
								{/foreach}
							</table>
				</div>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<tr class="slds-line-height--reset">
			<td align=center style="padding: .5rem;">
				<input type="button" name="{$APP.LBL_SELECT_BUTTON_LABEL}" value=" {$APP.LBL_SELECT_BUTTON_LABEL} " class="slds-button slds-button--small slds-button_success" onClick="validate_sendmail('{$IDLIST}','{$FROM_MODULE}');"/>&nbsp;&nbsp;
				<input type="button" name="{$APP.LBL_CANCEL_BUTTON_LABEL}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('roleLay');" />
			</td>
		</tr>
	</table>
</div>
