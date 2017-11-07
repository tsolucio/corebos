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
	<tr>
		<td style="padding-right: 5px;">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<span class="dash_count">1</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_SALES_STAGE_FORM_TITLE']; ?></span>
									</span>
								</h2>
							</div>
						</header>
					</div>
				</article>
			</div>
			<table class="slds-table slds-no-row-hover">
				<tr>
					<td class="dvtCellInfo" height="200"><?php include ("modules/Dashboard/Chart_pipeline_by_sales_stage.php");?></td>
				</tr>
			</table>
		</td>
		<!-- SCEOND CHART  -->
		<td style="padding-left: 5px;">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<span class="dash_count">2</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_MONTH_BY_OUTCOME']; ?></span>
									</span>
								</h2>
							</div>
						</header>
						<div class="slds-no-flex">
							<span class="big"><?php echo $mod_strings['LBL_VERT_BAR_CHART'];?></span>
						</div>
					</div>
				</article>
			</div>
			<table class="slds-table slds-no-row-hover">
				<tr>
					<td class="dvtCellInfo" height="200"><?php include ("modules/Dashboard/Chart_outcome_by_month.php"); ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<!-- SECOND ROW FIRST CHART  -->
	<tr>
		<td style="padding-right: 5px;">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<span class="dash_count">3</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_BY_OUTCOME']; ?></span>
									</span>
								</h2>
							</div>
						</header>
					</div>
				</article>
			</div>
			<table class="slds-table slds-no-row-hover">
				<tr>
					<td class="dvtCellInfo" height="200"><?php include ("modules/Dashboard/Chart_lead_source_by_outcome.php");?></td>
				</tr>
			</table>
		</td>
		<!-- FOURTH CHART  -->
		<td style="padding-left: 5px;">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<span class="dash_count">4</span>
										&nbsp;&nbsp;
										<span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_FORM_TITLE']; ?></span>
									</span>
								</h2>
							</div>
						</header>
					</div>
				</article>
			</div>
			<table class="slds-table slds-no-row-hover">
				<tr>
					<td class="dvtCellInfo" height="200"><?php include ("modules/Dashboard/Chart_pipeline_by_lead_source.php");?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script id="dash_script">
	var gdash_display_type = 'MATRIX';
</script>
