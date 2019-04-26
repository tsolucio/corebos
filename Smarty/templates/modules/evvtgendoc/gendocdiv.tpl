<div id="generatedocument" class="layerPopup" style="display: none; left: 338px; top: 219px; visibility: visible; z-index: 2;">
	<div style="width: 400px;">
		<form method="POST" id="generatedoc" name="generatedoc" action="index.php?module=evvtgendoc&action=odt&record={if isset($ID)}{$ID}{/if}" onsubmit="return checkOneTplSelected();">
			<table class="layerHeadingULine" cellpadding="5" width="100%">
				<tr>
					<td class="genHeaderSmall" width="90%" align="left">{'LBL_SELECT_TEMPLATE'|@getTranslatedString:'evvtgendoc'}</td>
					<td width="10%" align="right"><img src="themes/images/close.gif" onclick="hide('generatedocument');" id="closegendoc" border="0"></td>
				</tr>
			</table>
			<table width="95%" align="center" cellpadding="5">
				<tr>
					<td id="gendoc"></td>
				</tr>
			</table>
			<table width="100%" class="layerPopupTransport" cellpadding="5" align="center">
				<tr>
					<td class="small" align="center">
						<input name='recordval_type' type='hidden' value='{$MODULE}'>
						<input name='recordval' id='recordval' type='hidden' value='{if isset($ID)}{$ID}{/if}'>
						<input name='gdformat' id='gdformat' type='hidden' value='oo'>
						<input name='compilelang' id='compilelang' type='hidden' value="">
						<input type='submit' class='crmbutton small edit' onclick="document.getElementById('gdformat').value='oo';document.getElementById('compilelang').value=gVTuserLanguage.split('_')[0];" value='{'Export Doc'|@getTranslatedString:'evvtgendoc'}' title='{'Export Doc'|@getTranslatedString:'evvtgendoc'}'>
						&nbsp;&nbsp;
						{if $gendoc_active}
						<input type='submit' class='crmbutton small edit' onclick="document.getElementById('gdformat').value='pdf';document.getElementById('compilelang').value=gVTuserLanguage.split('_')[0];" value='{'Export PDF'|@getTranslatedString:'evvtgendoc'}' title='{'Export PDF'|@getTranslatedString:'evvtgendoc'}'>
						&nbsp;&nbsp;
						<input type='submit' class='crmbutton small edit' onclick="document.getElementById('gdformat').value='onepdf';document.getElementById('compilelang').value=gVTuserLanguage.split('_')[0];" value='{'Export to one PDF'|@getTranslatedString:'evvtgendoc'}' title='{'Export to one PDF'|@getTranslatedString:'evvtgendoc'}'>
						{/if}
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
