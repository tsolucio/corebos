/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/

function set_focus() {
	document.querySelector('input[name="user_name"]');
	if (document.querySelector('input[name="user_2facode"]') != undefined) {
		document.querySelector('input[name="user_2facode"]').focus();
	} else if (document.querySelector('input[name="user_name"]').value != '') {
		var pwd = document.querySelector('input[name="user_password"]');
		if (pwd) {
			pwd.focus();
			pwd.select();
		}
	} else {
		document.querySelector('input[name="user_name"]').focus();
	}
}

function checkCaps(b) {
	var a=0, c=!1, a=document.all?b.keyCode:b.which, c=b.shiftKey;
	b=document.getElementById('pwcaps');
	var d=65<=a&&90>=a, a=97<=a&&122>=a;
	if (d && !c || a && c) {
		b.style.display='block';
	} else if (a && !c || d && c) {
		b.style.display='none';
	}
}

function sendnew2facode(authuserid) {
	fetch('index.php?module=Utilities&action=sendnew2facode&authuserid=' + authuserid, {
		credentials: 'same-origin'
	}).then(function (response) {
		return response.text();
	}).then(function (data) {
		alert(data);
	});
}
