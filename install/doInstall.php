<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

if (isset ($_REQUEST['filename'])) {
	$file_name = htmlspecialchars($_REQUEST['filename']);
}
$total_num_of_steps = 4;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['APP_NAME']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_INSTALLATION_CHECK']?></title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link rel="stylesheet" href="include/install/css/bootstrap.min.css">
	<link rel="stylesheet" href="include/install/css/bootstrap-theme.min.css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<br>
	<!-- Table for cfgwiz starts -->

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>" hspace="20" title="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>"></td>
			<td class="cwHeadBg1" align=right><img src="include/install/images/app_logo.png" alt="<?php echo $installationStrings['APP_NAME']; ?>" title="<?php echo $installationStrings['APP_NAME']; ?>"></td>
			<td class="cwHeadBg1" width=2%></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td background="include/install/images/topInnerShadow.gif" colspan=2 align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center>
							<!-- Right side tabs -->
				    		<table cellspacing=0 cellpadding=2 width=95% align=center>
				    			<tr>
				    				<td align=left class="paddingTop">
				    					<span class="bigHeading"><?php echo $installationStrings['LBL_INSTALLING']; ?></span>
				    					<img alt="installing" src="include/images/installing.gif" style="margin-left: 50px;width: 20px;">
				    					<br>
				    				</td>
									<td align=right valign="middle" class="paddingTop">
									</td>  
								</tr>
								<tr><td colspan=2><hr noshade size=1></td></tr>
				    			<tr>
				    				<td colspan=2 align="left">
				    					<table cellpadding="0" cellspacing="1" align=right width="100%" class="level3">
				    						<tr>
								    			<td width=100%  valign=top >
													<table align=right width="100%" border="0">
														<tr>
															<td  valign=top align=left width=100%>
																<table cellpadding="2" cellspacing="1" align=right width="100%" border="0" class="level1">
																	<tr class='level1'>
																		<td valign=top ><span class="bigHeading" style="color: black;"><?php echo $installationStrings['DoingStep']; ?></span></td>
																		<td  valign=top><span class="bigHeading" style="color: black;" id="stepnumber">1/<?php echo $total_num_of_steps; ?></span></td>
																	</tr>
																	<tr class='level1'>
																		<td valign=top colspan=2><p id="stepdescription" class="level2" style="width: 100%"><?php echo $installationStrings['LBL_GETTING_STARTED']; ?></p></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr><td class="small" colspan=2><br></td></tr>
														<tr><td class="small" colspan=2>
														<div class="progress">
														<div class="progress-bar" role="progressbar" id="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">60%</div>
														</div>
														</td></tr>
													</table>
								    			</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr valign=top>
									<td align=left ></td>
									<td align=right>
										<form action="install.php" method="post" name="form" id="form">
											<input type="hidden" name="file" value="<?php echo $file_name?>" />
											<input type="submit" class="button submit-disabled" style="margin-top: 4px;margin-bottom: 4px;" disabled="disabled" id="nextbutton" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>">
										</form>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			<!-- Master display stops -->
				<br>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
		</tr>
	</table>	
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
      	<tr>
        	<td class=small align=center> <a href="<?php echo $coreBOS_app_url; ?>" target="_blank"><?php echo $coreBOS_app_name; ?></a></td>
      	</tr>
	</table>
<script type="text/javascript">
var timerID = setInterval(function() {
	// ajax to getStep
	if (confirm('ajax to get step')) {
		// if finished call
		clearInterval(timerID);
		$('#nextbutton').prop('disabled',false).removeClass('submit-disabled');
	}
}, 60 * 1000); 
</script>
</body>
</html>	
