{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
-->
*} <script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<!-- header - level 2 tabs -->
{include file='Buttons_List.tpl'}
<form enctype="multipart/form-data" name="SelectExports" method="POST">
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="step" value="export">
	<input type="hidden" name="action" value="{$MODULE}Ajax">
	<input type="hidden" name="file" value="MailerExport">
	<input type="hidden" name="exportwhere" value="{$EXPORTWHERE}">
	<input type="hidden" name="from" value="{if isset($FROM)}{$FROM}{/if}">
	<input type="hidden" name="fieldlist" value="{$FIELDLIST}">
	<input type="hidden" name="typelist" value="{$TYPELIST}">

	<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		<tr>
			<td>
				<table class="slds-table slds-no-row-hover slds-table-moz mailClient importLeadUI">
					<tr class="blockStyleCss">
						<td class="detailViewContainer" valign="middle" align="left">
							<div class="forceRelatedListSingleContainer">
								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
									<div class="slds-card__header slds-grid">
										<header class="slds-media slds-media--center slds-has-flexi-truncate">
											<div class="slds-media__body">
												<h1><span class="slds-text-title--caps slds-truncate genHeaderBig" style="font-size: 1rem;">{$MOD.LBL_MAILER_EXPORT}</span></h1>
											</div>
										</header>
									</div>
								</article>
							</div>
							<div class="slds-truncate" style="padding-top: .5rem; width: 98%;display: inherit;">
								<div class="forceRelatedListSingleContainer">
									<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
										<div class="slds-card__body slds-card__body--inner" style="margin-top: .75rem;">
											<div class="commentData">
												<span class="genHeaderGray">{$MOD.LBL_MAILER_EXPORT_CONTACTS_TYPE}</span>&nbsp;
												<span class="genHeaderSmall">{$MOD.LBL_MAILER_EXPORT_CONTACTS_DESCR}</span>
											</div>
										</div>
									</article>
								</div>
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
									{foreach from=$QUERYFIELDS name=querysel item=myVal}
										{if $smarty.foreach.querysel.index % 2 == 0}
										<tr class="slds-line-height--reset">
										{/if}
											{if $myVal.uitype == 1}
											<td class="dvtCellLabel">
												<input type=text name={$myVal.columnname} size=13>
											{elseif $myVal.uitype == 15 || $myVal.uitype == 56}
											<td class="dvtCellLabel	">
												{html_options name=$myVal.columnname class="slds-select" options=$myVal.value}
											{/if}
											<td class="dvtCellInfo">
												<b>{$myVal.fieldlabel}</b>
											</td>
										{if $smarty.foreach.querysel.index % 2 > 0}
										</tr>
										{/if}
									{/foreach}
									<input type="hidden" name="query" value="{$FIELDLIST}">
								</table>
							</div>
							<div class="forceRelatedListSingleContainer" style="margin-top: 1rem;">
								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
									<div class="slds-card__body slds-card__body--inner" style="margin-top: .75rem;">
										<div class="commentData">
											<span class="genHeaderGray">{$MOD.LBL_MAILER_EXPORT_RESULTS_TYPE}</span>&nbsp; 
											<span class="genHeaderSmall">{$MOD.LBL_MAILER_EXPORT_RESULTS_DESCR}</span>
										</div>
									</div>
								</article>
							</div>
							<div class="slds-truncate" style="width: 98%;display: inherit;">
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel">
											<b>{$MOD.LBL_EXPORT_RESULTS_EMAIL}</b>
										</td>
										<td class="dvtCellInfo">
											<span class="slds-radio" style="margin-bottom: .1rem;">
												<input type="radio" name="export_type" id="{$MOD.LBL_EXPORT_RESULTS_EMAIL}" checked value="email">
												<label class="slds-radio__label" for="{$MOD.LBL_EXPORT_RESULTS_EMAIL}">
													<span class="slds-radio--faux"></span>
												</label>
											</span>
										</td>
									</tr>
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel">
											<b>{$MOD.LBL_EXPORT_RESULTS_EMAIL_CORP}</b>
										</td>
										<td class="dvtCellInfo">
											<span class="slds-radio" style="margin-bottom: .1rem;">
												<input type="radio" name="export_type" id="{$MOD.LBL_EXPORT_RESULTS_EMAIL_CORP}" value="emailplus">
												<label class="slds-radio__label" for="{$MOD.LBL_EXPORT_RESULTS_EMAIL_CORP}">
													<span class="slds-radio--faux"></span>
												</label>
											</span>
										</td>
									</tr>
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel">
											<b>{$MOD.LBL_EXPORT_RESULTS_FULL}</b>
										</td>
										<td class="dvtCellInfo">
											<span class="slds-radio" style="margin-bottom: .1rem;">
												<input type="radio" name="export_type" id="{$MOD.LBL_EXPORT_RESULTS_FULL}" value="full">
												<label class="slds-radio__label" for="{$MOD.LBL_EXPORT_RESULTS_FULL}">
													<span class="slds-radio--faux"></span>
												</label>
											</span>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<table class="slds-table slds-no-row-hover slds-table-moz mailClient importLeadUI">
					<tr>
						<td class="reportCreateBottom" align="center">
							<input title="{$MOD.LBL_EXPORT_RESULTS_GO}" accessKey="" class="slds-button slds-button--small slds-button_success" type="submit" name="button" value=" {$MOD.LBL_EXPORT_RESULTS_GO} &rsaquo; ">
							<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--small slds-button--destructive" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>