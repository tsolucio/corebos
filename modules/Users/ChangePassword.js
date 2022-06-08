/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

function loadPassword(userid) {
	loadJS('index.php?module=Users&action=UsersAjax&file=getjslanguage');
	const headText = `${alert_arr['LBL_CHANGE_PASSWORD']}`;
	const tooltip = `
	<a href="javascript:void(0)" aria-describedby="help" onmouseover="showTooltip('help-passowrd')" onmouseout="hideTooltip('help-passowrd')">
		<span class="slds-icon_container slds-icon-utility-info">
			<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
			</svg>
		</span>
		<div class="slds-popover slds-popover_tooltip slds-nubbin_bottom-left" role="tooltip" id="help-passowrd" style="position:fixed;display: none;overflow:hidden">
			<div class="slds-popover__body">
				<div class="slds-popover__body">
				${alert_arr['PASSWORD REQUIREMENTS']}
				<br>----------------------------------------<br>
				${alert_arr['REQUIRED']}:<br> 
				${alert_arr['Min. 8 characters'].replace('8', gvPasswordLength)}<br><br>
				${alert_arr['Contains3of4']}<br> 
				${alert_arr['Min. 1 uppercase']}<br> 
				${alert_arr['Min. 1 lowercase']}<br> 
				${alert_arr['Min. 1 number']}<br> 
				${alert_arr['Min. 1 special character']}
				</div>
			</div>
		</div>
	</a>`;
	const Content = `
		<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_error" id="show-err_msg" style="margin-bottom: 20px;display: none">
			<span class="slds-assistive-text">error</span>
			<h2 id="err_msg"></h2>
			<div class="slds-notify__close">
				<button class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse" onclick="hideTooltip('show-err_msg')">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
				</button>
			</div>
		</div>
		<div class="slds-grid">
			<div class="slds-grid slds-col slds-size_1-of-4" style="font-size: 15px"> 
				<label for="${alert_arr['LBL_NEW_PASSWORD']}">${alert_arr['LBL_NEW_PASSWORD']} ${tooltip}</label>
			</div>
			<div class="slds-grid slds-col slds-size_2-of-4">
				<input name='new_password' class='slds-input' type='password' id="new_password">
				<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="showPassword(this.id, 'new_password')" id="btn_new_password">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
					</svg>
				</button>
				<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false" onclick="generatePassword()">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
					</svg>
				</button>
			</div>
		</div>`;
	ldsModal.show(headText, Content, 'small', `changepassword(1, ${userid})`);
}

function generatePassword() {
	const randPass = corebos_Password.getPassword(12, true, true, true, true, false, true, true, true, false);
	document.getElementById('new_password').value = randPass;
}

function showPassword(btnid, inputid) {
	const password = document.getElementById(inputid);
	const btn = document.getElementById(btnid);
	if (password.type === 'password') {
		btn.innerHTML = `
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#hide"></use>
			</svg>`;
		password.type = 'text';
	} else {
		btn.innerHTML = `
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
			</svg>`;
		password.type = 'password';
	}
}

function changepassword(is_admin, userid) {
	let err_msg = '';
	let new_password = document.getElementById('new_password').value;
	if (new_password == '') {
		err_msg += alert_arr['ERR_ENTER_NEW_PASSWORD'];
	}
	if (err_msg != '') {
		document.getElementById('show-err_msg').style.display = 'block';
		document.getElementById('err_msg').innerHTML = err_msg;
		return;
	}
	new_password = new_password.substring(0, 1024);
	let password = corebos_Password.passwordChecker(new_password);
	if (!password) {
		err_msg = alert_arr['PASSWORD REQUIREMENTS NOT MET'];
		document.getElementById('show-err_msg').style.display = 'block';
		document.getElementById('err_msg').innerHTML = err_msg;
	} else {
		const data = {
			record: userid,
			new_password: new_password
		};
		jQuery.ajax({
			method:'POST',
			url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=setNewPassword',
			data: data
		}).done(function (response) {
			const res = JSON.parse(response);
			if (!res.password) {
				document.getElementById('show-err_msg').style.display = 'block';
				let msg = typeof(res.msg) == 'undefined' ? alert_arr['Old password is incorrect'] : res.msg;
				document.getElementById('err_msg').innerHTML = msg;
			} else {
				window.location.href = `index.php?module=Users&action=DetailView&record=${userid}`;
			}
		});
	}
}

function showTooltip(id) {
	document.getElementById(id).style.display = 'block';
}

function hideTooltip(id) {
	document.getElementById(id).style.display = 'none';
}