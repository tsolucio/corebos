/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var fieldExpressionPopup_LOADED = false;
function fieldExpressionPopup(moduleName, $) {

	if (fieldExpressionPopup_LOADED != false) {
		return fieldExpressionPopup_LOADED;
	}
	var format = fn.format;
	var opType;

	var ownersList = {};

	function close() {
		$('#editpopup').css('display', 'none');
		$('#editpopup_expression').text('');
	}

	function show() {
		$('#editpopup').css('display', 'block');
		center($('#editpopup'));
	}

	function center(el) {
		el.css({
			position: 'absolute'
		});
		el.css({
			width: '650px'
		});
		placeAtCenter(el.get(0));
	}

	function showElement(ele, displaytype) {
		if (displaytype == null || displaytype == '' || displaytype == 'undefined') {
			displaytype = 'inline';
		}
		if (typeof ele.css != 'function') {
			ele.style.display = displaytype;
		} else {
			ele.css('display', displaytype);
		}
	}

	function hideElement(ele) {
		if (typeof ele.css != 'function') {
			ele.style.display = 'none';
		} else {
			ele.css('display', 'none');
		}
	}

	function map(fn, list) {
		var out = [];
		$.each(list, function (i, v) {
			out[out.length]=fn(v);
		});
		return out;
	}

	function implode(sep, arr) {
		var out = '';
		$.each(arr, function (i, v) {
			out+=v;
			if (i<arr.length-1) {
				out+=sep;
			}
		});
		return out;
	}

	function handleExpressionType(ele) {
		var value = ele.val();
		var helpElements = $('.layerPopup .helpmessagebox');
		$.each(helpElements, function (index, helpElement) {
			hideElement(helpElement);
		});
		if (value == 'fieldname') {
			showElement($('#fieldname_help'), 'block');
			showElement($('#editpopup_fieldnames'));
			hideElement($('#editpopup_functions'));
			setFieldType('string')(opType);
		} else if (value == 'expression') {
			showElement($('#expression_help'), 'block');
			showElement($('#editpopup_fieldnames'));
			showElement($('#editpopup_functions'));
			setFieldType('string')(opType);
		} else {
			showElement($('#text_help'), 'block');
			hideElement($('#editpopup_fieldnames'));
			hideElement($('#editpopup_functions'));
			var fieldType = $('#editpopup_field_type').val();
			setFieldType(fieldType)(opType);
		}
	}

	$('#editpopup_close').bind('click', close);
	$('#editpopup_save').bind('click', function () {
		var expression = $('#editpopup_expression').val();
		expression = expression.replace(/<script(.|\s)*?\/script>/g, '');
		expression = expression.replace(/\n/g, '<br>');  // convert \n to <br> for saving

		var fieldElementId = $('#editpopup_field').val();
		$('#'+fieldElementId).val(expression);

		var expressionType = $('#editpopup_expression_type').val();
		$('#'+fieldElementId+'_type').val(expressionType);

		close();
	});

	$('#editpopup_cancel').bind('click', close);

	$('#editpopup_expression_type').bind('change', function () {
		handleExpressionType($(this));
	});

	$('#editpopup_fieldnames').bind('change', function () {
		var textarea = $('#editpopup_expression').get(0);
		var value = $(this).val();
		if (value != '') {
			value += ' ';
		}
		//http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
		if (document.selection) {
			textarea.focus();
			var sel = document.selection.createRange();
			sel.text = value;
			textarea.focus();
		} else if (textarea.selectionStart || textarea.selectionStart == '0') {
			var startPos = textarea.selectionStart;
			var endPos = textarea.selectionEnd;
			var scrollTop = textarea.scrollTop;
			textarea.value = textarea.value.substring(0, startPos) + value + textarea.value.substring(endPos, textarea.value.length);
			textarea.focus();
			textarea.selectionStart = startPos + value.length;
			textarea.selectionEnd = startPos + value.length;
			textarea.scrollTop = scrollTop;
		} else {
			textarea.value += value;
			textarea.focus();
		}
		// Reset the selected option (to enable next selection)
		this.value = '';
	});

	function setFieldType(fieldType) {

		function forPicklist(opType) {
			var value = $('#editpopup_expression');
			var options = implode(
				'',
				map(
					function (e) {
						return '<option value="'+e.value+'">'+e.label+'</option>';
					},
					opType['picklistValues']
				)
			);
			value.replaceWith('<select id="editpopup_expression" class="value">'+options+'</select>');
		}

		function forInteger(opType) {
			var value = $('#editpopup_expression');
			value.replaceWith('<input type="text" id="editpopup_expression" value="0" class="value">');
		}
		function forOwnerField(opType) {
			var value = $('#editpopup_expression');
			var options = implode(
				'',
				map(
					function (e) {
						return '<option value="'+e.value+'">'+e.label+'</option>';
					},
					ownersList
				)
			);
			value.replaceWith('<select id="editpopup_expression" class="value">'+options+'</select>');
		}
		function forDateField(opType) {
			var value = $('#editpopup_expression');
			value.replaceWith('<input type="text" id="editpopup_expression" class="value"> \
				<img border=0 src="themes/softed/images/btnL3Calendar.gif" alt="SET DATE" title="SET DATE" id="jscal_trigger_editpopup_expression">');
			Calendar.setup(
				{inputField : 'editpopup_expression', ifFormat : '%Y-%m-%d', showsTime : false, button : 'jscal_trigger_editpopup_expression', singleClick : true, step : 1}
			);
		}
		function forReferenceField(opType) {
			var value = $('#editpopup_expression');
			var refcode = '<span id="editpopup_expression" class="value"><input id="wfrelfield" name="wfrelfield" type="hidden" value="">'
				+ '<select name="wfrelfield_type" id="wfrelfield_type" class="small" onchange="this.form.wfrelfield.value=\'\';this.form.wfrelfield_display.value=\'\';">';
			for (var mod=0; mod<opType.refersTo.length; mod++) {
				refcode = refcode + '<option value="' + opType.refersTo[mod] + '">' + opType.refersTo[mod] + '</option>';
			}
			refcode = refcode + '</select>&nbsp;'
				+ '<input id="wfrelfield_display" name="wfrelfield_display" readonly type="text" style="border:1px solid #bababa;width:200px;" value="">&nbsp;'
				+ '<img id="entity" src="themes/softed/images/select.gif" alt="SELECT" title="SELECT" align="absmiddle" style="cursor:hand;cursor:pointer" '
				+ 'onClick=\'return vtlib_open_popup_window("","wfrelfield","com_vtiger_workflow","");\'>'
				+ '<input type="image" src="themes/images/clear_field.gif"'
				+ 'alt="CLEAR" title="CLEAR" onClick="this.form.wfrelfield.value=\'\';'
				+ 'this.form.wfrelfield_display.value=\'\'; return false;" align="absmiddle" style="cursor:hand;cursor:pointer"></span>';
			value.replaceWith(refcode);
		}
		function forTimeField(opType) {
			var value = $('#editpopup_expression');
			value.replaceWith('<input type="text" id="editpopup_expression" value="0" class="value">');
		}
		function forDateTimeField(opType) {
			var value = $('#editpopup_expression');
			value.replaceWith('<input type="text" id="editpopup_expression" class="value" readonly> \
				<img align="absmiddle" border=0 src="themes/softed/images/btnL3Calendar.gif" alt="SET DATE" title="SET DATE" id="jscal_trigger_editpopup_expression">');
			Calendar.setup(
				{inputField : 'editpopup_expression', ifFormat : '%Y-%m-%d', showsTime : true, button : 'jscal_trigger_editpopup_expression', singleClick : true, step : 1}
			);
		}
		var functions = {
			string:function (opType) {
				var value = $('#editpopup_expression');
				value.replaceWith('<textarea name="Name" rows="10" cols="50" id="editpopup_expression"></textarea>');
			},
			'boolean': function (opType) {
				var value = $('#editpopup_expression');
				value.replaceWith(
					'<select id="editpopup_expression" value="true" class="value"> \
						<option value="true:boolean">'+alert_arr.YES+'</option>\
						<option value="false:boolean">'+alert_arr.NO+'</option>\
					</select>'
				);
			},
			integer: forInteger,
			picklist: forPicklist,
			multipicklist: forPicklist,
			owner: forOwnerField,
			date: forDateField,
			datetime: forDateTimeField,
			reference: forReferenceField,
			time: forTimeField
		};

		if ($('#jscal_trigger_editpopup_expression')) {
			$('#jscal_trigger_editpopup_expression').remove();
		}
		var ret = functions[fieldType];
		if (ret==null) {
			ret = functions['string'];
		}
		return ret;
	}

	$.get(
		'index.php',
		{
			module:'com_vtiger_workflow',
			action:'com_vtiger_workflowAjax',
			file:'WorkflowComponents',
			ajax:'true',
			modulename:moduleName,
			mode:'getownerslist'
		},
		function (result) {
			result = JSON.parse(result);
			ownersList = result;
		}
	);

	$.get(
		'index.php',
		{
			module:'com_vtiger_workflow',
			action:'com_vtiger_workflowAjax',
			file:'WorkflowComponents',
			ajax:'true',
			modulename:moduleName,
			mode:'getfunctionsjson'
		},
		function (result) {
			result = JSON.parse(result);
			var functions = $('#editpopup_functions');
			$.each(result, function (label, template) {
				functions.append(format('<option value="%s">%s</option>', template, label));
			});
			$('#editpopup_functions').bind('change', function () {
				var textarea = $('#editpopup_expression').get(0);
				var value = $(this).val();
				//http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
				if (document.selection) {
					textarea.focus();
					var sel = document.selection.createRange();
					sel.text = value;
					textarea.focus();
				} else if (textarea.selectionStart || textarea.selectionStart == '0') {
					var startPos = textarea.selectionStart;
					var endPos = textarea.selectionEnd;
					var scrollTop = textarea.scrollTop;
					textarea.value = textarea.value.substring(0, startPos) + value + textarea.value.substring(endPos, textarea.value.length);
					textarea.focus();
					textarea.selectionStart = startPos + value.length;
					textarea.selectionEnd = startPos + value.length;
					textarea.scrollTop = scrollTop;
				} else {
					textarea.value += value;
					textarea.focus();
				}
				// Reset the selected option (to enable next selection)
				this.value = '';
			});
		}
	);

	fieldExpressionPopup_LOADED = {
		create: show,
		edit: function (fieldelementid, expression, fieldtype) {
			$('#editpopup_field').val(fieldelementid);
			if (fieldtype.realname != undefined && fieldtype.realname == 'owner') {
				$('#editpopup_field_type').val(fieldtype.realname);
			} else {
				$('#editpopup_field_type').val(fieldtype.name);
			}

			opType = fieldtype;
			var expressionTypeElement = $('#'+fieldelementid+'_type');
			var expressionType = expressionTypeElement.val();
			if (expressionType != '') {
				$('#editpopup_expression_type').val(expressionTypeElement.val());
			} else {
				$('#editpopup_expression_type').val('rawtext');
			}
			handleExpressionType(expressionTypeElement);
			expression = expression.replace(/<br\s*\/?>/mg, '\n'); // convert <br> to \n for easy editing
			if (expression != '') {
				$('#editpopup_expression').val(expression);
			}
			$('#editpopup_expression').focus();
			show();
		},
		close:close,
		setModule: function (moduleName) {
			$.get(
				'index.php',
				{
					module:'com_vtiger_workflow',
					action:'com_vtiger_workflowAjax',
					file:'WorkflowComponents',
					ajax:'true',
					modulename:moduleName,
					mode:'getfieldsjson'
				},
				function (result) {
					var parsed = JSON.parse(result);
					var moduleFields = parsed['moduleFields'];
					var fieldNames = $('#editpopup_fieldnames');
					var existingFieldNames = fieldNames.children();
					var firstChildNode = existingFieldNames[0];
					fieldNames.children().remove();
					fieldNames.append(firstChildNode);
					$.each(moduleFields, function (fieldName, fieldLabel) {
						fieldNames.append(format('<option value="%s">%s</option>', fieldName, fieldLabel));
					});
				}
			);
		}
	};
	return fieldExpressionPopup_LOADED;
}

function com_vtiger_workflowsetValueFromCapture(recordid, value, target_fieldname) {
	$('#editpopup_expression').val(recordid);
	document.getElementById('wfrelfield').value = recordid;
	document.getElementById('wfrelfield_display').value = value;
}
