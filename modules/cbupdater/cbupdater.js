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
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
if (typeof(cbupdater) == 'undefined') {
	var cbupdater = {

		getselected : function (record) {
			var select_options = document.getElementById('allselectedboxes').value;
			var numOfRows = document.getElementById('numOfRows').value;
			var excludedRecords = document.getElementById('excludedRecords').value;
			var count = 0;
			if (select_options=='all') {
				var idstring = select_options;
				var skiprecords = excludedRecords.split(';');
				count = skiprecords.length;
				if (count > 1) {
					count = numOfRows - count + 1;
				} else {
					count = numOfRows;
				}
				if (count > getMaxMassOperationLimit()) {
					var confirm_str = alert_arr.MORE_THAN_500;
					if (confirm(confirm_str)) {
						var confirm_status = true;
					} else {
						return false;
					}
				} else {
					confirm_status = true;
				}
				if (confirm_status) {
					return idstring;
				}
			} else {
				var x = select_options.split(';');
				count = x.length;
				if (count > 1) {
					idstring = select_options;
					if (count > getMaxMassOperationLimit()) {
						confirm_str = alert_arr.MORE_THAN_500;
						if (confirm(confirm_str)) {
							confirm_status = true;
						} else {
							return false;
						}
					} else {
						confirm_status = true;
					}
					if (confirm_status) {
						return idstring;
					}
				} else {
					alert(alert_arr.SELECT);
					return false;
				}
			}
			return false;
		},

		applyselected : function () {
			if (idstring = this.getselected()) { // this is actually an assignment inside the condition
				gotourl('index.php?module=cbupdater&action=dowork&idstring='+idstring);
			}
		},

		undoselected : function () {
			if (idstring = this.getselected()) { // this is actually an assignment inside the condition
				gotourl('index.php?module=cbupdater&action=dowork&doundo=1&idstring='+idstring);
			}
		},

		exportselected : function () { // this is actually an assignment inside the condition
			if (idstring = this.getselected()) {
				gotourl('index.php?module=cbupdater&action=cbupdaterAjax&file=exportxml&idstring='+idstring);
			}
		},
	};
}