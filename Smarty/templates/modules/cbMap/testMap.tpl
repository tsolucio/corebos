{*<!--
/*********************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 ***********************************************************************************
 *  Module       : Business Map
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 ********************************************************************************/
-->*}
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
	<td>
		{include file='Buttons_List.tpl'}
		<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		<tr>
			<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
			<td class="showPanelBg" valign=top width=100%>
				<div class="small" style="padding:10px" >
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
					<tr><td>
						<span class="dvHeaderText">[ {$ID} ] {$NAME} -  {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</span>
						&nbsp;&nbsp;&nbsp;<span class="small">{$UPDATEINFO}</span>
					</td></tr>
					</table>
					<br>
					{include file='applicationmessage.tpl'}
					<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
					<tr>
						<td>
							<b>{$MAPTYPE}</b>
						</td>
					</tr>
					<tr>
						<td>
							{foreach key=xmlkey item=xmlinfo from=$MAPINFO}
								<table style="margin-left: 6px;text-align: left;" border=0>
								<tr><th colspan="3">{$xmlkey}</th></tr>
								{foreach key=xmlikey item=xmliinfo from=$xmlinfo}
								<tr>
									<td width="6px">&nbsp;</td>
									<td><b>{$xmlikey}</b></td>
									<td>
										{if $xmliinfo|is_array}
											<table style="margin-left: 6px;text-align: left;" border=0>
											<tr><th colspan="3">{$xmlikey}</th></tr>
											{foreach key=xmldkey item=xmldinfo from=$xmliinfo}
											<tr>
												<td width="6px">&nbsp;</td>
												<td><b>{$xmldkey}</b></td>
												<td>
													{if $xmldinfo|is_array}
														<table style="margin-left: 6px;text-align: left;" border=0>
														<tr><th colspan="3">{$xmldkey}</th></tr>
														{foreach key=xmlekey item=xmleinfo from=$xmldinfo}
														<tr>
															<td width="6px">&nbsp;</td>
															<td><b>{$xmlekey}</b></td>
															<td>
																{if $xmleinfo|is_array}
																	<table style="margin-left: 6px;text-align: left;" border=0>
																	<tr><th colspan="3">{$xmlekey}</th></tr>
																	{foreach key=xmlfkey item=xmlfinfo from=$xmleinfo}
																	<tr>
																		<td width="6px">&nbsp;</td>
																		<td><b>{$xmlfkey}</b></td>
																		<td>
																			{if $xmlfinfo|is_array}
																				<table style="margin-left: 6px;text-align: left;" border=0>
																				<tr><th colspan="3">{$xmlfkey}</th></tr>
																				{foreach key=xmlgkey item=xmlginfo from=$xmlfinfo}
																				<tr>
																					<td width="6px">&nbsp;</td>
																					<td><b>{$xmlgkey}</b></td>
																					<td>
																						{$xmlginfo}
																					</td>
																				</tr>
																				{/foreach}
																				</table>
																			{else}
																				{$xmlfinfo}
																			{/if}
																		</td>
																	</tr>
																	{/foreach}
																	</table>
																{else}
																	{$xmleinfo}
																{/if}
															</td>
														</tr>
														{/foreach}
														</table>
													{else}
														{$xmldinfo}
													{/if}
												</td>
											</tr>
											{/foreach}
											</table>
										{else}
											{$xmliinfo}
										{/if}
									</td>
								</tr>
								{/foreach}
								</table>
							{/foreach}
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