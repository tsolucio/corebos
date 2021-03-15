<table class="layerHeadingULine" cellpadding="5" width="100%">
	<tr>
	<td class="genHeaderSmall" width="90%" align="center">{'Transfer'|@getTranslatedString:$MODULE}</td>
	<td width="10%" align="right"><img src="themes/images/close.gif" onclick="hide('bankatransfer');location.href='index.php?module=BankAccounts&action=ListView'" id="closebankatransfer" border="0"></td>
	</tr>
</table>
<table class="layerHeadingULine" cellpadding="5" width="100%">
	<tr>
	<td class="dvtCellLabel" align="center">{'From'|@getTranslatedString:$MODULE}</td>
	<td>&nbsp;</td>
	<td class="dvtCellLabel" align="center">{'To'|@getTranslatedString:$MODULE}</td>
	</tr>
	<tr>
	<td class="dvtCellInfo" align="center">
	<input type="text" id="transferfrom" name="transferfrom" value="{$transfromacc}" class=detailedViewTextBox readonly>
	<input type="hidden" name="transferfromid" id='transferfromid' value='{$tfromID}'>
	</td>
	<td align="center"><img src="Smarty/templates/modules/BankAccounts/swap.png" onclick="swaptransferaccounts();" id="swapbankatransfer" border="0"></td>
	<td class="dvtCellInfo" align="center">
	<input type="text" id="transferto" name="transferto" value="{$transtoacc}" class=detailedViewTextBox readonly>
	<input type="hidden" name="transfertoid" id='transfertoid' value='{$ttoID}'>
	</td>
	</tr>
</table>
<table class="layerHeadingULine" cellpadding="5" width="100%">
	<tr>
	<td class="dvtCellLabel" align="center">{'Amount'|@getTranslatedString:$MODULE}</td>
	<td class="dvtCellInfo" align="left">
	<input type="text" id="transferamount" name="transferamount" tabindex="1" value="" class=detailedViewTextBox>
	</td>
	</tr>
</table>
<table width="100%" class="layerPopupTransport" cellpadding="5" align="center">
	<tr>
	<td class="small" align="center">
	<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="if (numValidate('transferamount','{'Amount'|@getTranslatedString:$MODULE}','any',false)) createtransferrecords(); else alert(alert_arr.LBL_ENTER_VALID_NO);" type="submit" name="button" id="trfsavebutton" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
	<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="hide('bankatransfer');location.href='index.php?module=BankAccounts&action=ListView'" type="button" name="button" id="trfclosebutton" value="{$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
	</td>
	</tr>
</table>
