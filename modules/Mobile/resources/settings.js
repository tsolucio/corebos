/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
var crmtogo_Settings_Js = {
	registerEventsForSettingsView : function (event) {
		$('li').removeClass('ui-corner-bottom');
		$('ul')
			.addClass('ui-corner-top')
			.removeClass('ui-corner-all')
			.sortable({
				'containment': 'parent',
				'opacity': 0.6,
				update: function (event, ui) {
					if ($('.ui-page-active').prop('id')=='settings_page') {
						var idsInOrder = $('#sortable').sortable('toArray');
					} else {
						var idsInOrder = $('#homesortable').sortable('toArray');
					}
					$.ajax({
						method: 'POST',
						url: 'index.php?_operation=changeGUISettings',
						dataType: 'json',
						data: {
							idsInOrder: idsInOrder,
							operation: 'changeorder'
						}
					}).done(function (msg) {
						return false;
					}).fail(function () {
						alert('Module Order Saving Error, please contact your CRM Administrator.');
						return false;
					});
				}
			});

		function flipChanged(e) {
			var moduleid = this.id, checkvalue = this.checked;
			$.ajax({
				method: 'POST',
				url: 'index.php?_operation=changeGUISettings',
				dataType: 'json',
				data: {
					moduleid: moduleid,
					checkvalue: checkvalue,
					operation: 'changemodule'
				}
			}).done(function (msg) {
				return false;
			}).fail(function () {
				alert('Module Display Saving Error, please contact your CRM Administrator.');
				return false;
			});
			console.log(moduleid + ' has been changed! ' + checkvalue);
		}
		//console.log($('[id*=flip_]'));
		$('[id*=flip_]').on('change', flipChanged);

		$('#navislider').bind('change', function () {
			$('#navislider').slider({
				stop: function (event, ui) {
					var sliderVar = $('#navislider').val();
					$.ajax({
						method: 'POST',
						url: 'index.php?_operation=changeGUISettings',
						dataType: 'json',
						data: {
							sliderVar: sliderVar,
							operation: 'changenavi'
						}
					}).done(function (msg) {
						return false;
					}).fail(function () {
						alert('Navigation Limit Saving Error, please contact your CRM Administrator.');
						return false;
					});
				}
			});
		});

		$('#themecolor').bind('change', function () {
			var theme = $('#themecolor input[type=\'radio\']:checked').val();
			$.ajax({
				method: 'POST',
				url: 'index.php?_operation=changeGUISettings',
				dataType: 'json',
				data: {
					theme: theme,
					operation: 'changetheme'
				}
			}).done(function (msg) {
				$('#footer').removeAttr('data-theme');
				$('#footer').prop('data-theme', theme);
				$('#header').removeAttr('data-theme');
				$('#header').prop('data-theme', theme);
				//todo set theme color by trigger
				//$('#settings_page').trigger('create');
				var white = '#eee';
				var black = '#3e3e3e';
				var blue = '#5e87b0';
				if (theme == 'a') {
					$('#footer').css('background', black);
					$('#header').css('background', black);
					$('#footer').css('color', white);
					$('#header').css('color', white);
				} else if (theme == 'b') {
					$('#footer').css('background', blue);
					$('#header').css('background', blue);
					$('#footer').css('color', white);
					$('#header').css('color', white);
				} else {
					$('#footer').css('background', white);
					$('#header').css('background', white);
					$('#footer').css('color', black);
					$('#header').css('color', black);
				}
				return false;
			}).fail(function () {
				alert('Theme Saving Error, please contact your CRM Administrator.');
				return false;
			});
		});
	},
	registerSettingsEvents: function () {
		this.registerEventsForSettingsView();
	}
};

$(document).delegate('#settings_page', 'pageinit', function () {
	crmtogo_Settings_Js.registerSettingsEvents();
});
