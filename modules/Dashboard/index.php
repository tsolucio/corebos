<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/logging.php');

global $app_strings, $mod_strings, $currentModule, $theme;

require 'modules/Dashboard/graphdefinitions.php';

$log = LoggerManager::getLogger('dashboard');
if(isset($_REQUEST['type']) && $_REQUEST['type'] != '')
{
	$dashboard_type = $_REQUEST['type'];
}else
{
	$dashboard_type = 'DashboardHome';
}
?>

<TABLE class="slds-table slds-no-row-hover lds-img slds-table-buttons">
	<tr>
		<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
			<div class="slds-truncate moduleName" title="{$MODULELABEL}">
				<a class="hdrLink" href="index.php?action=index&parenttab=Analytics&module=Dashboard"><?php echo $app_strings['Dashboard'] ?></a>
			</div>
		</th>
		<td width=100% nowrap>
			<table border="0" cellspacing="0" cellpadding="0" class="slds-table-buttons">
				<tr>
					<td class="small">
						<table border="0" cellspacing="0" cellpadding="0" class="slds-table-buttons">
							<tr>
								<td class=small>
									<!-- Add and Search -->
									<table class="slds-table slds-no-row-hover slds-table-buttons">
										<tr>
											<th scope="col">
												<div class="globalCreateContainer oneGlobalCreate">
													<div class="forceHeaderMenuTrigger">
														<div class="LB_Button slds-truncate disabled">
															<img src="<?php echo vtiger_imageurl('btnL3Add.gif', $theme); ?>">
														</div>
													</div>
												</div>
											</th>
											<th scope="col">
												<div class="globalCreateContainer oneGlobalCreate">
													<div class="forceHeaderMenuTrigger">
														<div class="LB_Button slds-truncate disabled">
															<img src="<?php echo vtiger_imageurl('btnL3Search.gif', $theme); ?>">
														</div>
													</div>
												</div>
											</th>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:20px;" class="LB_Divider">&nbsp;&nbsp;</td>
					<td class="small">
						<!-- Calendar, Clock and Calculator -->
						<table class="slds-table slds-no-row-hover slds-table-buttons">
							<tr>
								<?php
								if(GlobalVariable::getVariable('Application_Display_Mini_Calendar',1,$currentModule)) {
								?>
									<th scope="col">
										<a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab=My Home Page");'>
											<img src="<?php echo vtiger_imageurl('btnL3Calendar.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_CALENDAR_ALT']; ?>" title="<?php echo $app_strings['LBL_CALENDAR_TITLE']; ?>" border=0>
										</a>
									</th>
								<?php
								}
								if(GlobalVariable::getVariable('Application_Display_World_Clock',1,$currentModule)) {
								?>
									<th scope="col">
										<a href="javascript:;">
											<img src="<?php echo vtiger_imageurl('btnL3Clock.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_CLOCK_ALT']; ?>" title="<?php echo $app_strings['LBL_CLOCK_TITLE']; ?>" border=0 onClick="fnvshobj(this,'wclock');">
										</a>
									</th>
								<?php
								}
								if(GlobalVariable::getVariable('Application_Display_Calculator',1,$currentModule)) {
								?>
									<th scope="col">
										<a href="#">
											<img src="<?php echo vtiger_imageurl('btnL3Calc.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_CALCULATOR_ALT']; ?>" title="<?php echo $app_strings['LBL_CALCULATOR_TITLE']; ?>" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();">
										</a>
									</th>
								<?php
								}
								?>
								<th scope="col">
									<img src="<?php echo vtiger_imageurl('btnL3Tracker.gif', $theme); ?>" alt="<?php echo $app_strings['LBL_LAST_VIEWED']; ?>" title="<?php echo $app_strings['LBL_LAST_VIEWED']; ?>" onclick="fnvshobj(this,'tracker');" style="cursor:pointer;" border="0">
								</th>
							</tr>
						</table>
					</td>
					<td style="width:20px;" class="LB_Divider">&nbsp;</td>
					<td class="small">
						<!-- Import / Export / DuplicatesHandling-->
						<table class="slds-table slds-no-row-hover slds-table-buttons">
							<tr>
								<th scope="col">
									<div class="globalCreateContainer oneGlobalCreate">
										<div class="forceHeaderMenuTrigger">
											<div class="LB_Button slds-truncate disabled">
												<img src="<?php echo vtiger_imageurl('tbarImport.gif', $theme) ?>" border="0">
											</div>
										</div>
									</div>
								</th>
								<th scope="col">
									<div class="slds-truncate disabled">
										<img src="<?php echo vtiger_imageurl('tbarExport.gif', $theme) ?>" border="0">
									</div>
								</th>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</TABLE>
<br>

<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script src="include/chart.js/Chart.bundle.min.js"></script>
<script src="include/chart.js/randomColor.js"></script>
<script type="text/javascript" src="modules/Dashboard/Dashboard.js"></script>
<a name="top"></a>

		<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
			<tr>
				<!-- {*<td valign=top><img src="<?php //echo vtiger_imageurl('showPanelTopLeft.gif', $theme) ?>"></td>*} -->
				<td>
					<!-- DASHBOARD DEGINS HERE -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="100%">
								<!-- TOP SELECT OPTION -->
								<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
									<tr class="slds-text-title--caps">
										<td style="padding: 0;">
											<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="padding-top: 2rem;">
												<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
													<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
														<!-- Title and help text -->
														<div class="slds-media__body">
															<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																<span class="uiOutputText">
																	<?php echo $mod_strings['LBL_DASHBOARD'] ?>
																</span>
															</h1>
														</div>
														<div class="slds-no-flex">
															<select name="dashordlists" id="dashboard_combo" class="slds-select" onChange="loadDashBoard(this);">
																<?php foreach($graph_array as $key=>$value) {
																	if($dashboard_type == $key) { $dash_board_title = $value; ?>
																	<option selected value="<?php echo $key;?>"><?php echo $value;?></option>
																<?php } else { ?>
																	<option value="<?php echo $key;?>"><?php echo $value;?></option>
																<?php } } ?>
															</select>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>
								<!-- END OF TOP SELECTION -->

								<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small">
									<!-- <tr>
										<td class="dash_border" width="1%"><img src="<?php //echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
										<td class="hdrNameBg small" style="height: 12px;" width="98%">&nbsp;</td>
										<td class="dash_border" width="1%"><img src="<?php //echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
									</tr> -->
									<tr>
										<!-- <td class="dash_border">&nbsp;</td> -->
										<td class="dash_white genHeaderBig dash_bdr_btm" colspan="3">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="90%" nowrap>
														<div class="forceRelatedListSingleContainer">
															<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																<div class="slds-card__header slds-grid">
																	<header class="slds-media slds-media--center slds-has-flexi-truncate">
																		<div class="slds-media__body">
																			<h2>
																				<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																					<?php echo $app_strings['Dashboard']; ?> &gt; <?php echo $app_strings['Home'];?> &gt; 
																					<span id="dashTitle_div"><?php echo $dash_board_title; ?></span>
																				</span>
																			</h2>
																		</div>
																	</header>
																	<div class="slds-no-flex list-grid-view">
																		<img alt="<?php echo $mod_strings['NORMALVIEW'];?>" title="<?php echo $mod_strings['NORMALVIEW'];?>" style="cursor:pointer;" onClick="changeView('NORMAL');" src="<?php echo vtiger_imageurl('dboardNormalView.gif', $theme); ?>" align="absmiddle" border="0">
																		&nbsp;|&nbsp;
																		<img alt="<?php echo $mod_strings['GRIDVIEW'];?>" title="<?php echo $mod_strings['GRIDVIEW'];?>" style="cursor:pointer;" onClick="changeView('MATRIX');" src="<?php echo vtiger_imageurl('dboardMatrixView.gif', $theme); ?>" align="absmiddle" border="0">
																	</div>
																</div>
															</article>
														</div>

														<table width="100%" align="center">
															<tr>
																<td class="dash_white" colspan="3" style="height:500px;padding-bottom: 0;">
																	<div id="dashChart">

																		<!-- NAVIGATION TABLE -->
																		<!-- CHART ONE TABLE -->
																		<table width="100%" border="0" cellpadding="0" cellspacing="0">
																			<tr>
																				<td height="300">
																					<?php
																						if(!isset($_REQUEST['type']))
																						{
																							if(isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX')
																								require_once('modules/Dashboard/DashboardHome_matrix.php');
																							else
																								require_once('modules/Dashboard/DashboardHome.php');
																						} else
																							require_once('modules/Dashboard/loadDashBoard.php');
																					?>
																				</td>
																			</tr>
																		</table>

																	</div>
																</td>
															</tr>
														</table>

													</td>
												</tr>
											</table>
										</td>
										<!-- <td class="dash_border">&nbsp;</td> -->
									</tr>
									<tr>
										<!-- <td class="dash_border">&nbsp;</td> -->
										
										<!-- <td class="dash_border">&nbsp;</td> -->
									</tr>
									<!-- <tr>
										<td class="dash_border" width="1%"><img src="<?php //echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
										<td class="dash_white" width="98%">&nbsp;</td>
										<td class="dash_border" width="1%"><img src="<?php //echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
									</tr> -->
									<tr>
										<!-- BOTTOM NAVICATION -->
										<td colspan="3" class="dashSelectBg">
											<div class="forceRelatedListSingleContainer">
												<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
													<div class="slds-card__header slds-grid">
														<header class="slds-media slds-media--center slds-has-flexi-truncate">
															<div class="slds-media__body">
																<h2></h2>
															</div>
														</header>
														<div class="slds-no-flex">
															<select name="dashordlists" id="dashboard_combo1" class="slds-select" onChange="loadDashBoard(this);">
																<?php foreach($graph_array as $key=>$value) { if($dashboard_type == $key) { $dash_board_title = $value; ?>
																	<option selected value="<?php echo $key;?>"><?php echo $value;?></option>
																<?php } else { ?>
																	<option value="<?php echo $key;?>"><?php echo $value;?></option>
																<?php } } ?>
															</select>
														</div>
													</div>
												</article>
											</div>
											<!-- END OF BOTTOM NAVIGATION -->
										</td>
									</tr>
									<!-- <tr>
										<td colspan="3">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="112"><img src="<?php //echo vtiger_imageurl('dash_btm_left.jpg', $theme) ?>" border="0" align="absmiddle"></td>
													<td width="100%" class="dash_btm">&nbsp;</td>
													<td width="129"><img src="<?php //echo vtiger_imageurl('dash_btm_right.jpg', $theme) ?>" border="0" align="absmiddle"></td>
												</tr>
											</table>
										</td>
									</tr> -->
								</table>
								<!-- END -->
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

	</body>
</html>