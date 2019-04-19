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
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><a name="1"></a>
			<table width="20%"  border="0" cellspacing="3" cellpadding="0" align="left">
			<tr>
				<td rowspan="2"><span class="dash_count">1</span>&nbsp;&nbsp;</td>
				<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_SALES_STAGE_FORM_TITLE']; ?></span></td>
			</tr>
			</table>
		</td>
		<td align="right">
			<table cellpadding="0" cellspacing="0" border="0" class="small">
				<tr>
					<td class="small"><?php echo $mod_strings['VIEWCHART'];?> :&nbsp;</td>
					<td class="dash_row_sel">1</td>
					<td class="dash_row_unsel"><a class="dash_href" href="#2">2</a></td>
					<td class="dash_row_unsel"><a class="dash_href" href="#3">3</a></td>
					<td class="dash_row_unsel"><a class="dash_href" href="#4">4</a></td>
					<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="200" colspan="2"><?php include 'modules/Dashboard/Chart_pipeline_by_sales_stage.php';?></td>
	</tr>
	<tr>
		<td colspan="2" class="dash_chart_btm">&nbsp;</td>
	</tr>

	<!-- SCEOND CHART  -->
	<tr>
		<td><a name="2"></a>
			<table width="20%"  border="0" cellspacing="5" cellpadding="0" align="left">
				<tr>
					<td rowspan="2"><span class="dash_count">2</span>&nbsp;&nbsp;</td>
					<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_MONTH_BY_OUTCOME'];?></span></td>
				</tr>
			</table>
		</td>
		<td align="right">
			<table cellpadding="0" cellspacing="0" border="0" class="small">
				<tr>
					<td class="small"><?php echo $mod_strings['VIEWCHART'];?> :&nbsp;</td>
					<td class="dash_row_unsel"><a class="dash_href" href="#1">1</a></td>
					<td class="dash_row_sel">2</td>
					<td class="dash_row_unsel"><a class="dash_href" href="#3">3</a></td>
					<td class="dash_row_unsel"><a class="dash_href" href="#4">4</a></td>
					<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="200" colspan="2"><?php include 'modules/Dashboard/Chart_outcome_by_month.php'; ?></td>
	</tr>
	<tr>
		<td colspan="2" class="dash_chart_btm">&nbsp;</td>
	</tr>
	<!-- THIRD CHART  -->
	<tr>
		<td><a name="3"></a><table width="20%"  border="0" cellspacing="5" cellpadding="0" align="left">
			<tr>
				<td rowspan="2"><span class="dash_count">3</span>&nbsp;&nbsp;</td>
				<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_BY_OUTCOME'];?></span></td>
			</tr>
			</table>
		</td>
		<td align="right">
			<table cellpadding="0" cellspacing="0" border="0" class="small">
				<tr>
					<td class="small"><?php echo $mod_strings['VIEWCHART']; ?> :&nbsp;</td>
					<td class="dash_row_unsel"><a class="dash_href" href="#1">1</a></td>
					<td class="dash_row_unsel"><a class="dash_href" href="#2">2</a></td>
					<td class="dash_row_sel">3</td>
					<td class="dash_row_unsel"><a class="dash_href" href="#4">4</a></td>
					<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="200" colspan="2"><?php include 'modules/Dashboard/Chart_lead_source_by_outcome.php';?></td>
	</tr>
	<tr>
		<td colspan="2" class="dash_chart_btm">&nbsp;</td>
	</tr>
	<!-- FOURTH CHART  -->
	<tr>
		<td><a name="4"></a><table width="20%"  border="0" cellspacing="5" cellpadding="0" align="left">
			<tr>
				<td rowspan="2"><span class="dash_count">4</span>&nbsp;&nbsp;</td>
				<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_FORM_TITLE'];?></span></td>
			</tr>
			</table>
		</td>
		<td align="right">
			<table cellpadding="0" cellspacing="0" border="0" class="small">
				<tr>
					<td class="small"><?php echo $mod_strings['VIEWCHART'];?> :&nbsp;</td>
					<td class="dash_row_unsel"><a class="dash_href" href="#1">1</a></td>
					<td class="dash_row_unsel"><a class="dash_href" href="#2">2</a></td>
					<td class="dash_row_unsel"><a class="dash_href" href="#3">3</a></td>
					<td class="dash_row_sel">4</td>
					<td class="dash_switch"><a href="#top"><img align="absmiddle" src="<?php echo vtiger_imageurl('dash_scroll_up.jpg', $theme) ?>" border="0"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="200" colspan="2"><?php include 'modules/Dashboard/Chart_pipeline_by_lead_source.php'; ?></td>
	</tr>
	<tr>
		<td colspan="2" class="dash_chart_btm">&nbsp;</td>
	</tr>
</table>
<script id="dash_script">
	var gdash_display_type = 'NORMAL';
</script>
