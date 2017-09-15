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
<script type="text/javascript" src="modules/Import/resources/Import.js"></script>

<form onsubmit="VtigerJS_DialogBox.block();" action="index.php" enctype="multipart/form-data" method="POST" name="importAdvanced">
	<input type="hidden" name="module" value="{$FOR_MODULE}" />
	<input type="hidden" name="action" value="Import" />
	<input type="hidden" name="mode" value="import" />
	<input type="hidden" name="type" value="{$USER_INPUT->getString('type')}" />
	<input type="hidden" name="has_header" value='{$HAS_HEADER}' />
	<input type="hidden" name="file_encoding" value='{$USER_INPUT->getString('file_encoding')}' />
	<input type="hidden" name="delimiter" value='{$USER_INPUT->getString('delimiter')}' />
	<input type="hidden" name="merge_type" value='{$USER_INPUT->getString('merge_type')}' />
	<input type="hidden" name="merge_fields" value='{$USER_INPUT->getString('merge_fields')}' />

	<input type="hidden" id="mandatory_fields" name="mandatory_fields" value='{$ENCODED_MANDATORY_FIELDS}' />

	<table align="center" width="98%">
		<tr>
			<td>
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<h2>
										<span class="slds-text-title--caps slds-truncate heading2 actionLabel">
											<b>{'LBL_IMPORT'|@getTranslatedString:$MODULE} {$FOR_MODULE|@getTranslatedString:$FOR_MODULE}</b>
										</span>
									</h2>
								</div>
							</header>
						</div>
					</article>
				</div>
				<div class="slds-truncate">
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
						{if !empty($ERROR_MESSAGE)}
						<tr class="slds-line-height--reset">
							<td class="style1" align="left" colspan="2">
								{$ERROR_MESSAGE}
							</td>
						</tr>
						{/if}
						<tr class="slds-line-height--reset">
							<td class="dvtCellInfo leftFormBorder1" valign="top" width="100%">
								<div class="slds-truncate">
									{include file='modules/Import/Import_Step4.tpl'}
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr class="slds-line-height--reset">
			<td align="right" colspan="2">
			{include file='modules/Import/Import_Advanced_Buttons.tpl'}
			</td>
		</tr>
	</table>
</form>