<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/logging.php';

global $app_strings, $mod_strings, $currentModule, $theme;

require 'modules/Dashboard/graphdefinitions.php';

$log = LoggerManager::getLogger('dashboard');
if (isset($_REQUEST['type']) && $_REQUEST['type'] != '') {
	$dashboard_type = $_REQUEST['type'];
} else {
	$dashboard_type = 'DashboardHome';
}
?>

<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>
<tr><td style="height:2px"></td></tr>
<tr>
	<td style="padding-left:10px;padding-right:30px" class="moduleName" width="20%" nowrap>
		<a class="hdrLink" href="index.php?action=index&parenttab=Analytics&module=Dashboard"><?php echo $app_strings['Dashboard'] ?></a>
	</td>

	<td nowrap width="8%">
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td class="sep1" style="width:1px;"></td>
			<td class=small>
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					<td style="padding-right:0px;padding-left:10px;"><img src="<?php echo vtiger_imageurl('btnL3Add-Faded.gif', $theme); ?>" border=0></td>
					<td style="padding-right:10px"><img src="<?php echo vtiger_imageurl('btnL3Search-Faded.gif', $theme); ?>" border=0></td>
				</tr>
				</table>
	</td>
			</tr>
			</table>
	</td>
	<td width="1">&nbsp;</td>
	<td class="small" width="10%" align="left">
		<table border=0 cellspacing=0 cellpadding=5>
			<tr>
<?php
if (GlobalVariable::getVariable('Application_Display_Mini_Calendar', 1, $currentModule)) {
?>
	<td style="padding-right:0px;padding-left:10px;">
		<a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab=My Home Page");'>
		<img src="<?php echo vtiger_imageurl('btnL3Calendar.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_CALENDAR_ALT']; ?>" title="<?php echo $app_strings['LBL_CALENDAR_TITLE']; ?>" border=0>
		</a>
	</td>
<?php
}
if (GlobalVariable::getVariable('Application_Display_World_Clock', 1, $currentModule)) {
?>
	<td style="padding-right:0px">
		<a href="javascript:;">
		<img src="<?php echo vtiger_imageurl('btnL3Clock.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_CLOCK_ALT']; ?>" title="<?php echo $app_strings['LBL_CLOCK_TITLE']; ?>" border=0 onClick="fnvshobj(this,'wclock');">
		</a>
	</td>
<?php
}
if (GlobalVariable::getVariable('Application_Display_Calculator', 1, $currentModule)) {
?>
	<td style="padding-right:0px">
		<a href="#">
		<img src="<?php echo vtiger_imageurl('btnL3Calc.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_CALCULATOR_ALT']; ?>" title="<?php echo $app_strings['LBL_CALCULATOR_TITLE']; ?>" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();">
		</a>
	</td>
<?php
}
?>
</td>
				<td style="padding-right: 10px;">
					<img src="<?php echo vtiger_imageurl('btnL3Tracker.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_LAST_VIEWED']; ?>" title="<?php echo $app_strings['LBL_LAST_VIEWED']; ?>" onclick="fnvshobj(this,'tracker');" style="cursor:pointer;" border="0">
				</td>
			</tr>
		</table>
	</td>
	<td width="1">&nbsp;</td>
	<td class="small" align="left" width="5%">
		<table border=0 cellspacing=0 cellpadding=5>
			<tr>
				<td style="padding-right:0px;padding-left:10px;"><img src="<?php echo vtiger_imageurl('tbarImport-Faded.gif', $theme) ?>" border="0"></td>
				<td style="padding-right:10px"><img src="<?php echo vtiger_imageurl('tbarExport-Faded.gif', $theme) ?>" border="0"></td>
			</tr>
		</table>
	</td>
	<td width="20">&nbsp;</td>
	<td class="small" align="left"></td>
	</tr>
	</table>
	</td>
</tr>
<tr><td style="height:2px"></td></tr>
</TABLE>
<br>

<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script src="include/chart.js/Chart.bundle.min.js"></script>
<script src="include/chart.js/randomColor.js"></script>
<script type="text/javascript" src="modules/Dashboard/Dashboard.js"></script>
<a name="top"></a>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
	<td valign=top><img src="<?php echo vtiger_imageurl('showPanelTopLeft.gif', $theme) ?>"></td>

	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="100%">
			<!-- DASHBOARD DEGINS HERE -->
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small">
			<tr>
				<td class="dash_top" colspan="3">
				<!-- TOP SELECT OPTION -->
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="3%">&nbsp;</td>
						<td align="left">
							<table border="0" cellpadding="0" cellspacing="5" class="dashSelectBg">
							<tr>
								<td>
								<select name="dashordlists" id="dashboard_combo" onChange="loadDashBoard(this);">
								<?php
								foreach ($graph_array as $key => $value) {
									if ($dashboard_type == $key) {
										$dash_board_title = $value;
										?><option selected value="<?php echo $key;?>"><?php echo $value;?></option><?php
									} else {
										?><option value="<?php echo $key;?>"><?php echo $value;?></option>
									<?php
									}
								} ?>
								</select>
								</td>
							</tr>
							</table>
						</td>
						<td align="right" class="dashHeading"><?php echo $mod_strings['LBL_DASHBOARD'] ?></td>
						<td width="3%">&nbsp;</td>

									</tr>
								</table>
							<!-- END OF TOP SELECTION -->
						</td>
					</tr>
					<tr>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
						<td class="hdrNameBg small" style="height: 12px;" width="98%">&nbsp;</td>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>

					</tr>
					<tr>
						<td class="dash_border">&nbsp;</td>
						<td class="dash_white genHeaderBig dash_bdr_btm">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="90%" nowrap>
							<?php echo $app_strings['Dashboard']; ?> &gt; <?php echo $app_strings['Home'];?> &gt; <span id="dashTitle_div"><?php echo $dash_board_title; ?></span>
							</td>
							<td align="right" width="10%">
							<img alt="<?php echo $mod_strings['NORMALVIEW'];?>" title="<?php echo $mod_strings['NORMALVIEW'];?>" style="cursor:pointer;" onClick="changeView('NORMAL');" src="<?php echo vtiger_imageurl('dboardNormalView.gif', $theme); ?>" align="absmiddle" border="0">
							&nbsp;|&nbsp;
							<img alt="<?php echo $mod_strings['GRIDVIEW'];?>" title="<?php echo $mod_strings['GRIDVIEW'];?>" style="cursor:pointer;" onClick="changeView('MATRIX');" src="<?php echo vtiger_imageurl('dboardMatrixView.gif', $theme); ?>" align="absmiddle" border="0">
							</td>
						</tr>
						</table>
						</td>
						<td class="dash_border">&nbsp;</td>
					</tr>

					<tr>
						<td class="dash_border">&nbsp;</td>
						<td class="dash_white"  style="height:500px;"><div id="dashChart">
							<!-- NAVIGATION TABLE -->
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="45%" align="right">&nbsp;
									</td>
								</tr>

							</table>
							<!-- END OF NAVIGATION -->
							<!-- CHART ONE TABLE -->
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td height="300">
								<?php
								if (!isset($_REQUEST['type'])) {
									if (isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX') {
										require_once 'modules/Dashboard/DashboardHome_matrix.php';
									} else {
										require_once 'modules/Dashboard/DashboardHome.php';
									}
								} else {
									require_once 'modules/Dashboard/loadDashBoard.php';
								}
								?>
								&nbsp;</td>
							</tr>
							</table>
							<!-- End of CHART 1 -->
						</div></td>
						<td class="dash_border">&nbsp;</td>
					</tr>

					<tr>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
						<td class="dash_white" width="98%">&nbsp;</td>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
					</tr>
					<tr>
						<td colspan="3" class="dash_bottom">
						<!-- BOTTOM NAVICATION -->
							<table width="100%" cellpadding="0" cellspacing="0" border="0">

									<tr>
										<td width="3%">&nbsp;</td>
										<td align="left">
											<table border="0" cellpadding="0" cellspacing="5" class="dashSelectBg">
											<tr>
												<td><select name="dashordlists" id="dashboard_combo1" onChange="loadDashBoard(this);">
									<?php
									foreach ($graph_array as $key => $value) {
										if ($dashboard_type == $key) {
											$dash_board_title = $value;
										?>
										<option selected value="<?php echo $key;?>"><?php echo $value;?></option>
									<?php
										} else {
										?>
										<option value="<?php echo $key;?>"><?php echo $value;?></option>
										<?php   }
									} ?>
											</select>
											</td>
										</tr>
										</table></td>
										<td align="right">&nbsp;</td>
										<td width="3%">&nbsp;</td>
									</tr>
								</table>
						<!-- END OF BOTTOM NAVIGATION -->
						</td>
					</tr>
					<tr>

						<td colspan="3">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="112"><img src="<?php echo vtiger_imageurl('dash_btm_left.jpg', $theme); ?>" border="0" align="absmiddle"></td>
									<td width="100%" class="dash_btm">&nbsp;</td>
									<td width="129"><img src="<?php echo vtiger_imageurl('dash_btm_right.jpg', $theme); ?>" border="0" align="absmiddle"></td>
								</tr>
							</table>
						</td>

					</tr>
				</table>
			<!-- END -->
		</td>
	</tr>
</table>
</td>
<td valign=top><img src="<?php echo vtiger_imageurl('showPanelTopRight.gif', $theme) ?>"></td>
</tr>
</table>

</body>
</html>
