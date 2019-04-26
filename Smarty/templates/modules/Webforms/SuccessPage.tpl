<html>
	<head>
		<title>Vtiger {$MODULE} Webforms</title>
		<link REL="SHORTCUT ICON" HREF="themes/images/vtigercrm_icon.ico">
		<style type="text/css">
		{literal}
table { font: inherit; }
.small {
	color:#000000;
	font-family:Arial,Helvetica,sans-serif;
	font-size:12px;
}

.big {
	font-family: Arial, Helvetica, sans-serif;
	font-size:14px;
}
.dvtCellLabel, .cellLabel {
	background-color:#c7FFc7;
	border:1px solid #DEDEDE;
	color:#545454;
	padding-left:10px;
	padding-right:10px;
	white-space:nowrap;
	text-align: right;
}
.bold{
	font-weight: bold;
}
{/literal}
		</style>
	</head>
	<body class="small">
		<div id="vtigerWebforms">
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tbody>
					<tr>
						<td colspan="2" style="text-align: right;">
							<img src="{$PATH}{$IMAGEPATH}vtiger.jpg" alt="vtiger logo"/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="dvtCellLabel big bold" style="text-align: center;">
							<img src="{$PATH}{$IMAGEPATH}migration_sucess.jpg" style="float: left;width 10%;width: 36px;"/>
							{$MODULE} {$MOD.LBL_SUCCESS}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>