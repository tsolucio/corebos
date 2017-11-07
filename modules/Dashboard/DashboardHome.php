<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<!-- FIRST CHART  -->
	<tr>
		<td colspan="2" class="big">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<a name="1"></a>
										<span class="dash_count">1</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_SALES_STAGE_FORM_TITLE']; ?></span>
									</span>
								</h2>
							</div>
						</header>
						<div class="slds-no-flex">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" class="small chart-numbers">
								<tr>
									<td class="small"><?php echo $mod_strings['VIEWCHART'];?> :&nbsp;</td>
									<td class="dash_row_sel"><a type="button" class="disabled dash_href slds-button slds-button--x-small slds-button--destructive">1</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button_success" href="#2">2</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--brand" href="#3">3</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--warning" href="#4">4</a></td>
									<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
								</tr>
							</table>
						</div>
					</div>
				</article>
			</div>
		</td>
	</tr>
	<tr>
		<td class="dvtCellInfo" height="200" colspan="2"><?php include ("modules/Dashboard/Chart_pipeline_by_sales_stage.php");?></td>
	</tr>
	<tr>
		<!-- <td colspan="2" class="dash_chart_btm">&nbsp;</td> -->
		<td colspan="2">&nbsp;</td>
	</tr>

	<!-- SCEOND CHART  -->
	<tr>
		<td colspan="2" class="big">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<a name="2"></a>
										<span class="dash_count">2</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_MONTH_BY_OUTCOME'];?></span>
									</span>
								</h2>
							</div>
						</header>
						<div class="slds-no-flex">
							<table cellpadding="0" cellspacing="0" border="0" class="small chart-numbers">
								<tr>
									<td class="small"><?php echo $mod_strings['VIEWCHART'];?> :&nbsp;</td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--destructive" href="#1">1</a></td>
									<td class="dash_row_sel"><a type="button" class="disabled dash_href slds-button slds-button--x-small slds-button_success">2</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--brand" href="#3">3</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--warning" href="#4">4</a></td>
									<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
								</tr>
							</table>
						</div>
					</div>
				</article>
			</div>
		</td>
	</tr>
	<tr>
		<td class="dvtCellInfo" height="200" colspan="2"><?php include ("modules/Dashboard/Chart_outcome_by_month.php"); ?></td>
	</tr>
	<tr>
		<!-- <td colspan="2" class="dash_chart_btm">&nbsp;</td> -->
		<td colspan="2">&nbsp;</td>
	</tr>
	<!-- THIRD CHART  -->
	<tr>
		<td colspan="2" class="big">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<a name="3"></a>
										<span class="dash_count">3</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_BY_OUTCOME'];?></span>
									</span>
								</h2>
							</div>
						</header>
						<div class="slds-no-flex">
							<table cellpadding="0" cellspacing="0" border="0" class="small chart-numbers">
								<tr>
									<td class="small"><?php echo $mod_strings['VIEWCHART']; ?> :&nbsp;</td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--destructive" href="#1">1</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button_success" href="#2">2</a></td>
									<td class="dash_row_sel"><a type="button" class="disabled dash_href slds-button slds-button--x-small slds-button--brand">3</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--warning" href="#4">4</a></td>
									<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
								</tr>
							</table>
						</div>
					</div>
				</article>
			</div>
		</td>
	</tr>
	<tr>
		<td class="dvtCellInfo" height="200" colspan="2"><?php include ("modules/Dashboard/Chart_lead_source_by_outcome.php");?></td>
	</tr>
	<tr>
		<!-- <td colspan="2" class="dash_chart_btm">&nbsp;</td> -->
		<td colspan="2">&nbsp;</td>
	</tr>
	<!-- FOURTH CHART  -->
	<tr>
		<td colspan="2" class="big">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<a name="4"></a>
										<span class="dash_count">4</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_FORM_TITLE'];?></span>
									</span>
								</h2>
							</div>
						</header>
						<div class="slds-no-flex">
							<table cellpadding="0" cellspacing="0" border="0" class="small chart-numbers">
								<tr>
									<td class="small"><?php echo $mod_strings['VIEWCHART'];?> :&nbsp;</td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--destructive" href="#1">1</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button_success" href="#2">2</a></td>
									<td class="dash_row_unsel"><a type="button" class="dash_href slds-button slds-button--x-small slds-button--brand" href="#3">3</a></td>
									<td class="dash_row_sel"><a type="button" class="disabled dash_href slds-button slds-button--x-small slds-button--warning">4</a></td>
									<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
								</tr>
							</table>
						</div>
					</div>
				</article>
			</div>
		</td>
	</tr>
	<tr>
		<td class="dvtCellInfo" height="200" colspan="2"><?php include("modules/Dashboard/Chart_pipeline_by_lead_source.php") ?></td>
	</tr>
</table>

<script id="dash_script">
	var gdash_display_type = 'NORMAL';
</script>
