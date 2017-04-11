{*<!--
/*********************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 ********************************************************************************/
-->*}
<form name="GoogleContacts">
	<div id="GoogleContacts" style="z-index:12;display:inline-table;width:200px;display: none;" class="layerPopup">
		<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
			<tr>
				<td width="90%" align="left" class="genHeaderSmall">{$MOD.GOOGLE_CONTACTS}&nbsp;</td>
				<td width="10%" align="right">
					<a href="javascript:fninvsh('GoogleContacts');"><img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
				</td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
			<tr><td class="small">
				<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
					<tr>
						<td align="left">
							<div style="height:80px;overflow-y:auto;overflow-x:hidden;" align="center">
								<input type="button" name="SYNCH{$APP.SYNCH_NOW}" value=" SYNCH{$APP.SYNCH_NOW} " class="crmbutton small create" onClick="googleContactsSynch('{$MODULE}',this);"/>&nbsp;&nbsp;
							</div>
						</td>
					</tr>
				</table>
			</td></tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr><td align=center class="small">
				<input type="button" name="{$APP.LBL_CANCEL_BUTTON_LABEL}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="fninvsh('GoogleContacts');" />
			</td></tr>
		</table>
	</div>
</form>
