/*************************************************************************************************
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
 *************************************************************************************************/

var moveupLinkObj, moveupDisabledObj, movedownLinkObj, movedownDisabledObj;

var wizard = $('#report-steps');
var new_steps_count = 0;
var COL_BLOCK;
var BLOCKCRITERIA;
var BLOCKJS;
var CRITERIA_GROUP;
var FOPTION_ADV;
var BLOCKJS;
var REL_FIELDS;
var updated_grouping_criteria = false;

// Filter vars
var userIdArr;
var userNameArr;
var grpIdArr;
var grpNameArr;

wizard.steps({
	headerTag: 'h3',
	bodyTag: 'section',
	transitionEffect: 'slideLeft',
	stepsOrientation: 'vertical',
	labels: {
		cancel: alert_arr.JSLBL_CANCEL,
		current: alert_arr.JSLBL_CURRENT,
		pagination: alert_arr.JSLBL_PAGINATION,
		finish: alert_arr.JSLBL_FINISH,
		next: alert_arr.JSLBL_NEXT,
		previous: alert_arr.JSLBL_PREVIOUS,
		loading: alert_arr.JSLBL_Loading
	},

	onInit: function (event, currentIndex) {

		// Add Cancel button
		var cancel_button = $('<a>', {'type':'button', 'onclick':'self.close();', 'href':'#cancel'});
		cancel_button.html(alert_arr.JSLBL_CANCEL);
		var li = $('<li>');
		li.append(cancel_button);
		$('.actions ul').append(li);

		//Add <Save As> button
		if ( document.NewReport.record.value !== '') {
			var save_as_button = $('<a>', {'type':'button', 'onclick':'saveas();', 'href':'#saveas'});
			save_as_button.html(alert_arr.JSLBL_SAVEAS);
			var li = $('<li>', {'style':'display:none', 'id':'save_as_button'});
			li.append(save_as_button);
			$('.actions ul li:nth-child(1)').after(li);
		}

	},

	onStepChanging: function (event, currentIndex, newIndex) {

		// Clear Previous errors
		$('.step_error').html('');

		// Let user go one step back
		if (currentIndex > newIndex) {
			return true;
		}

		// Check for errors on step 3
		if (currentIndex == 3 && newIndex == 4) {
			var cbreporttype = document.getElementById('cbreporttype').value;
			if (selectedColumnsObj.options.length == 0 && cbreporttype != 'external' && cbreporttype != 'directsql') {
				var $error_selector = $('#step4_error');
				$error_selector.html(alert_arr.COLUMNS_CANNOT_BE_EMPTY);
				return false;
			}
			if (new_steps_count > 0) {
				return true;
			}
		}
		var cbreporttype = document.getElementById('cbreporttype').value;
		if (currentIndex == 0 && newIndex == 1) {
			var $error_selector = $('#step1_error');
			var can_pass = false;

			// Check if report name field is not empty
			if (document.NewReport.reportName.value =='') {
				$error_selector.html(alert_arr.MISSING_REPORT_NAME);
				return can_pass;
			}

			// Check if there is any Report with the same name
			jQuery.ajax({
				method: 'POST',
				async: false,
				url: 'index.php?action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=reportCheck&reportName='+encodeURIComponent(document.NewReport.reportName.value)+'&reportid='+document.NewReport.record.value
			}).done(function (response) {
				if (response!=0) {
					$error_selector.html(alert_arr.REPORT_NAME_EXISTS);
				} else {
					can_pass = true;
				}
			});

			return can_pass;
		} else if (currentIndex == 1 && newIndex == 2) {
			var can_pass = false;
			var $error_selector = $('#step2_error');
			var modsselected = $('.secondarymodule:checkbox:checked').length;

			if ((cbreporttype != 'crosstabsql' && modsselected<=Report_MaxRelated_Modules) || (cbreporttype == 'crosstabsql' && modsselected == 1)) {
				var data = setStepData(3);
				jQuery.ajax({
					method: 'POST',
					async: false,
					data: data,
					url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
					dataType: 'json',
				}).done(function (response) {
					can_pass = setReportType(response);
					if (cbreporttype == 'crosstabsql') {
						// Skip Report Type step
					}
				});
			} else {
				if (cbreporttype == 'crosstabsql' && modsselected == 0) {
					$error_selector.html(alert_arr.MUST_SELECT_ONE_MODULE_FOR_REPORT);
				} else if (cbreporttype == 'crosstabsql') {
					$error_selector.html(alert_arr.ONLY_ONE_MODULE_PERMITTED_FOR_REPORT);
				} else {
					$error_selector.html(alert_arr.MAXIMUM_OF_MODULES_PERMITTED);
				}
				can_pass = false;
			}

			return can_pass;
		} else if (currentIndex == 2 && newIndex == 3) {
			var can_pass = false;
			var $error_selector = $('#step3_error');
			var data = setStepData(4);

			jQuery.ajax({
				method: 'POST',
				async: false,
				data: data,
				url: 'index.php?action=ReportsAjax&file=steps&module=Reports&cbreporttype='+cbreporttype,
				dataType: 'json',
			}).done(function (response) {
				can_pass = fillSelectedColumns(response);
				if (cbreporttype == 'crosstabsql') {
					document.getElementById('pivotfield').value = response.pivotfield;
				}
			});

			return can_pass;
		} else if (currentIndex - new_steps_count == 3  && newIndex - new_steps_count == 4) {
			var can_pass = false;
			var $error_selector = $('#step4_error');
			var data = setStepData(5);

			jQuery.ajax({
				method: 'POST',
				async: false,
				data: data,
				url: 'index.php?action=ReportsAjax&file=steps&module=Reports&cbreporttype='+cbreporttype,
				dataType: 'json',
			}).done(function (response) {
				if (cbreporttype == 'crosstabsql') {
					document.getElementById('cbreptypenotctsubtitle').style.display = 'none';
					document.getElementById('cbreptypectsubtitle').style.display = 'block';
					document.getElementById('cbreptypenotcttrow').style.display = 'none';
					document.getElementById('cbreptypecttrow').style.display = 'table-row';
					document.getElementById('aggfield').value = response.aggfield;
					document.getElementById('crosstabaggfunction').value = response.crosstabaggfunction;
				} else {
					fillReportColumnsTotal(response.BLOCK1);
				}
				can_pass = true;
			});

			return can_pass;
		} else if (currentIndex - new_steps_count == 4  && newIndex - new_steps_count == 5 ) {
			var can_pass = false;
			var data = setStepData(6);

			jQuery.ajax({
				method: 'POST',
				async: false,
				data: data,
				url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
				dataType: 'json',
			}).done(function (response) {
				can_pass = fillFilterInfo(response);
			});
			return can_pass;
		} else if (currentIndex - new_steps_count == 5  && newIndex - new_steps_count == 6 ) {
			var can_pass = false;
			var data = setStepData(7);

			if (!validateDate()) {
				return can_pass;
			}

			jQuery.ajax({
				method: 'POST',
				async: false,
				data: data,
				url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
				dataType: 'json',
			}).done(function (response) {
				can_pass = fillGroupingInfo(response);
			});

			return can_pass;
		} else if ( currentIndex - new_steps_count >= 5 ) {
			return true;
		}
	},

	onStepChanged: function (event, currentIndex, priorIndex) {
		var cbreporttype = document.getElementById('cbreporttype').value;
		if (cbreporttype == 'crosstabsql') {
			if (currentIndex == 2) {
				wizard.steps('next');
			}
		}
		if (cbreporttype == 'external' || cbreporttype == 'directsql') {
			if (currentIndex >= 1 && currentIndex <= 6) {
				wizard.steps('setStep', 6);
			}
		}

		if (currentIndex == 3 && priorIndex == 2 ) {
			if (document.NewReport.reportType.value =='summary') {
				// Ajax Call
				var data = setStepData('grouping');
				jQuery.ajax({
					method: 'POST',
					data: data,
					url: 'index.php?action=ReportsAjax&file=steps&module=Reports',
					dataType: 'json',
				}).done(function (response) {
					//Summarize information lists
					fillFullList(response.BLOCK1, 'Group1', true, alert_arr.LBL_NONE);
					fillFullList(response.BLOCK2, 'Group2', true, alert_arr.LBL_NONE);
					fillFullList(response.BLOCK3, 'Group3', true, alert_arr.LBL_NONE);

					// Group By time
					$('#Group1time').css('display', response.GRBYTIME1.display);
					fillList(response.GRBYTIME1.options, 'groupbytime1');
					$('#Group2time').css('display', response.GRBYTIME2.display);
					fillList(response.GRBYTIME2.options, 'groupbytime2');
					$('#Group3time').css('display', response.GRBYTIME3.display);
					fillList(response.GRBYTIME3.options, 'groupbytime3');

					// Group Order
					fillList(response.ORDER1, 'Sort1');
					fillList(response.ORDER2, 'Sort2');
					fillList(response.ORDER3, 'Sort3');

				});

				// Add another step
				if (wizard.find('section').length == 8) {
					wizard.steps('insert', 4, {
						title: LBL_SPECIFY_GROUPING,
						content: '<table class=\'grouping_section\'>' + $('#grouping_section').html() + '</table>'
					});
					new_steps_count++;
				}
			} else if (document.NewReport.reportType.value =='tabular' && wizard.find('section').length == 9) {
				// Remove step if report type is tabular
				wizard.steps('remove', 4);
				new_steps_count--;
			}
		}

		// Display <Save as> button
		if (document.NewReport.record.value !== '') {
			if ( currentIndex - new_steps_count >= 6) {
				$('#save_as_button').css('display', 'block');
			} else {
				$('#save_as_button').css('display', 'none');
			}
		}

	},

	onFinishing: function (event, currentIndex) {
		return ScheduleEmail();
	},

	onFinished: function (event, currentIndex) {
		saveAndRunReport();
	}
});
jQuery.fn.steps.setStep = function (step) {
	var currentIndex = $(this).steps('getCurrentIndex');
	for (var i = 0; i < Math.abs(step - currentIndex); i++) {
		if (step > currentIndex) {
			$(this).steps('next');
		} else {
			$(this).steps('previous');
		}
	}
};
