<div id="masstag" class="layerPopup" style="display:none;left: 338px; top: 219px; visibility: visible; ">
	<div style="width: 400px;">
		<form method="POST" id="masstag_form" name="masstag_form" action="index.php">
			<input type="hidden" name="module" value="{$MODULE}">
			<input type="hidden" name="action" value="{$MODULE}Ajax">
			<input type="hidden" name="file" value="TagCloud">
			<input type="hidden" name="ajxaction" value="MASSTAG">
			<input type="hidden" name="ids" id="ids" value="">
			<table class="layerHeadingULine" cellpadding="5" width="100%">
				<tr>
					<td class="genHeaderSmall" width="90%" align="left">{$APP.LBL_MASSTAG_FORM_HEADER}</td>
					<td width="10%" align="right"><img src="themes/images/close.gif" onclick="fnhide('masstag');" id="closemasstag" border="0"></td>
				</tr>
			</table>
			<table width="95%" align="center" cellspacing="0" cellpadding="5" border="0">
				<tr>
					<td class="small">
						<table width="100%" bgcolor="white" align="center" cellspacing="0" cellpadding="5" border="0">
							<tr>
								<td align="left">
									<div align="center" style="height:120px;overflow-y:auto;overflow-x:hidden;">
										<table width="90%" cellspacing="0" cellpadding="5" border="0">
											<tr>
												<td align="right">
													<b>{$APP.LBL_ADD_TAG}</b>
												</td>
												<td align="left">
													<input name="add_tag">
												</td>
											</tr>
											<tr>
												<td align="center">
													<b>{$APP.LBL_REMOVE_TAG}</b>
												</td>
												<td align="left">
													<input name="remove_tag">
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
			<table class="layerPopupTransport" width="100%" cellspacing="0" cellpadding="5" border="0">
				<tr>
					<td class="small" align="center">
						<input class="crmbutton small create" type="submit" value="{$APP.LBL_EXECUTE_MASSTAG}" name="Seleccionar">
						<input class="crmbutton small cancel" type="button" onclick="fnhide('masstag');" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="Cancelar">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
