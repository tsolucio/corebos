/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Dashboard Charts
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

function loadDashBoard(oSelect)
{
	jQuery("#dashChart").fadeOut();
	var oCombo = document.getElementById('dashboard_combo');
	var oCombo1 = document.getElementById('dashboard_combo1');
	oCombo.selectedIndex = oSelect.selectedIndex;
	oCombo1.selectedIndex = oSelect.selectedIndex;
	var type = oSelect.options[oSelect.selectedIndex].value;
	if(type != 'DashboardHome')
		url = 'module=Dashboard&action=DashboardAjax&display_view='+gdash_display_type+'&file=loadDashBoard&type='+type;
	else
		url = 'module=Dashboard&action=DashboardAjax&file=DashboardHome&display_view='+gdash_display_type;
	jQuery.ajax({
		method:"POST",
		url:'index.php?'+ url
	}).done(function(response) {
		document.getElementById("dashChart").innerHTML=response;
		vtlib_executeJavascriptInElement(document.getElementById("dashChart"))
		jQuery("#dashChart").fadeIn(500);
		document.getElementById("dashTitle_div").innerHTML = oCombo.options[oCombo.selectedIndex].text;
	});
}

function changeView(displaytype)
{
	gdash_displaytype = displaytype;
	var oCombo = document.getElementById('dashboard_combo');
	var type = oCombo.options[oCombo.selectedIndex].value;
	var currenttime = new Date();
	var time="&time="+currenttime.getTime();
	if(type == 'DashboardHome')
	{
		if(displaytype == 'MATRIX')
			url = 'index.php?module=Dashboard&action=index&display_view=MATRIX';
		else
			url = 'index.php?module=Dashboard&action=index&display_view=NORMAL';
	}
	else
	{
		if(displaytype == 'MATRIX')
			url = 'index.php?module=Dashboard&action=index&display_view=MATRIX&type='+type;
		else
			url = 'index.php?module=Dashboard&action=index&display_view=NORMAL&type='+type;
	}
	window.document.location.href = url+time;
}

function getRandomColor() {
	return randomColor({
		luminosity: 'dark',
		hue: 'random'
	});
}
