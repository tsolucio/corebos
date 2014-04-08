/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
jQuery.noConflict();
function editexpressionscript($){
	function errorDialog(message){
		alert(message);
	}

	function handleError(fn){
		return function(status, result){
			if(status){
				fn(result);
			}else{
				errorDialog('Failure:'+result);
			}
		};
	}


	var ep;//ep is defined in the document.ready block.

	function errorDialog(message){
		alert(message);
	}


	function jsonget(operation, params, callback){
		var obj = {
				module:'FieldFormulas',
				action:'FieldFormulasAjax',
				file:operation, ajax:'true'};
		$.each(params,function(key, value){
			obj[key] = value;
		});
		$.get('index.php', obj,
			function(result){
				var parsed = JSON.parse(result);
				callback(parsed);
		});
	}
	
	function getfieldlabel(fieldname) {
		var fieldlabel = false;
		if(typeof(meta_fieldnames) != 'undefined' && typeof(meta_fieldlabels) != 'undefined') {
			fieldlabel = meta_fieldlabels[meta_fieldnames.indexOf(fieldname)]; 
		}
		if(!fieldlabel) fieldlabel = fieldname;
		return fieldlabel;
	}

	function addFieldExpression(moduleName, fieldName, fieldLabel, expression){
		editLink = format('<img border="0" title="Edit" alt="Edit" \
								style="cursor: pointer;" id="expressionlist_editlink_%s" \
								src="themes/images/editfield.gif"/>', fieldName);
		deleteLink = format('<img border="0" title="Delete" alt="Delete"\
		 					src="themes/images/delete.gif" \
							style="cursor: pointer;" id="expressionlist_deletelink_%s"/>', fieldName);
		row = format('<tr class="expressionlistrow" id="expressionlistrow_%s"> \
					<td class="listTableRow small" valign="top" nowrap="">%s</td>\
					<td class="listTableRow small" valign="top" nowrap="">%s</td>\
					<td class="listTableRow small" valign="top" nowrap="">%s | %s</td>\
				</tr>', fieldName, fieldLabel, expression.replace("\n","<BR/>"), editLink, deleteLink);
		$('#expressionlist').append(row);
		$(format('#expressionlist_deletelink_%s', fieldName)).click(function(){
			if(confirm(strings.LBL_DELETE_EXPRESSION_CONFIRM)) {
				$('#status').show();
				
				jsonget('deleteexpressionjson',
					{modulename:moduleName, fieldname:fieldName},
					function(result){
						$('#status').hide();
						
						if(result.status=='success'){
							$(format('#expressionlistrow_%s', fieldName)).remove();
						}else{
							errorDialog(result.message);
						}
					}
				);
			}
		});
		$(format('#expressionlist_editlink_%s', fieldName)).click(function(){
			ep.edit(fieldName, expression);
		});
	}

	format = fn.format;
	var moduleName;
	function editpopup(){
			function close(){
				$('#editpopup').css('display', 'none');
				$('#editpopup_expression').text('');
			}

			function show(module){
				$('#editpopup').css('display', 'block');
				center($('#editpopup'));
			}
			
			function center(el){
				el.css({position: 'absolute'});
				el.width("650px");
				el.height("310px");
				placeAtCenter(el.get(0));
			}

			$('#editpopup_close').bind('click', close);
			$('#editpopup_save').bind('click', function(){
				var expression = $('#editpopup_expression').attr('value');
				var fieldName = $('#editpopup_field').attr('value');
				var fieldLabel = getfieldlabel(fieldName);
				var moduleName = $('#pick_module').attr('value');
				
				expression = expression.replace(/<script(.|\s)*?\/script>/g, "");
				if(expression == '') return false;
				
				VtigerJS_DialogBox.block();
				$.get('index.php', {
						module:'FieldFormulas',
						action:'FieldFormulasAjax',
						file:'saveexpressionjson', ajax:'true',
						modulename: moduleName, fieldname:fieldName,
						expression:expression
						},
					function(result){
						VtigerJS_DialogBox.unblock();
						try {
							var parsed = JSON.parse(result);
							if(parsed.status=='success'){
								$("#expressionlistrow_"+fieldName).remove();
								addFieldExpression(moduleName, fieldName, fieldLabel, expression);
								close();
							}else{
								errorDialog('save failed because '+parsed.message);
							}
						} catch(error) {
							alert(error);
						}
					});
				});

			$('#editpopup_cancel').bind('click', close);

			$('#editpopup_fieldnames').bind('change', function(){
				var textarea = $('#editpopup_expression').get(0);
				var value = $(this).attr('value');
				if(value != '') value += ' ';
				//http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
				if (document.selection) {
					textarea.focus();
					var sel = document.selection.createRange();
					sel.text = value;
					textarea.focus();
				}else if (textarea.selectionStart || textarea.selectionStart == '0') {
					var startPos = textarea.selectionStart;
					var endPos = textarea.selectionEnd;
					var scrollTop = textarea.scrollTop;
					textarea.value = textarea.value.substring(0, startPos)
										+ value
										+ textarea.value.substring(endPos,
											textarea.value.length);
					textarea.focus();
					textarea.selectionStart = startPos + value.length;
					textarea.selectionEnd = startPos + value.length;
					textarea.scrollTop = scrollTop;
				}	else {
					textarea.value += value;
					textarea.focus();
				}
				// Reset the selected option (to enable next selection)
				this.value = '';

			});


			jsonget('getfunctionsjson',
				{modulename:moduleName},
				function(result){
					var functions = $('#editpopup_functions');
					$.each(result, function(label, template){
						functions.append(format('<option value="%s">%s</option>', template, label));
					});
					$('#editpopup_functions').bind('change', function(){
						var textarea = $('#editpopup_expression').get(0);
						var value = $(this).attr('value');
						//http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
						if (document.selection) {
							textarea.focus();
							var sel = document.selection.createRange();
							sel.text = value;
							textarea.focus();
						}else if (textarea.selectionStart || textarea.selectionStart == '0') {
							var startPos = textarea.selectionStart;
							var endPos = textarea.selectionEnd;
							var scrollTop = textarea.scrollTop;
							textarea.value = textarea.value.substring(0, startPos)
												+ value
												+ textarea.value.substring(endPos,
													textarea.value.length);
							textarea.focus();
							textarea.selectionStart = startPos + value.length;
							textarea.selectionEnd = startPos + value.length;
							textarea.scrollTop = scrollTop;
						}else {
							textarea.value += value;
							textarea.focus();
						}
						// Reset the selected option (to enable next selection)
						this.value = '';

					});

				}
			);


			return {
				create: show,
				edit: function(field, expression){
					$("#editpopup_field").attr('value', field);
					$("#editpopup_expression").attr('value', expression);
					show();
				},
				close:close,
				changeModule: function(moduleName, exprFields, moduleFields){
					var field = $('#editpopup_field');
					field.children().remove();
					$.each(exprFields, function(fieldName, fieldLabel){
						field.append(format('<option value="%s">%s</option>', fieldName, fieldLabel));
					});

					var fieldNames = $('#editpopup_fieldnames');
					fieldNames.children().remove();
					fieldNames.append(format('<option value="">%s</options>', strings.LBL_USE_FIELD_VALUE_DASHDASH));
					$.each(moduleFields, function(fieldName, fieldLabel){
						fieldNames.append(format('<option value="%s">%s</option>', fieldName, fieldLabel));
					});
				}
			};
	}

	$(document).ready(
	    function(){
	    	Drag.init(document.getElementById('editpopup_draghandle'), document.getElementById('editpopup'));
			toExec();
		}
    );

    function toExec(){
		ep = editpopup();
		function setModule(moduleName){
			$.get('index.php', {
					module:'FieldFormulas',
					action:'FieldFormulasAjax',
					file:'getfieldsjson', ajax:'true',
					modulename:moduleName},
				function(result){					
					var parsed = JSON.parse(result);
					ep.changeModule($(this).attr("value"), parsed['exprFields'], parsed['moduleFields']);
					
					$('#new_field_expression_busyicon').hide();
					$('#new_field_expression').show();
					
					if(parsed['exprFields'].length!=0){
						$('#new_field_expression').attr('disabled', false);
						$('#new_field_expression').attr('class', 'crmButton create small');
						$('#new_field_expression').bind('click', function(){
							ep.create();
						});
						$('#status_message').html('');
						$('#status_message').hide();
					}else{
					    $('#new_field_expression').hide();
					    $('#status_message').show();
						$('#status_message').html(strings.NEED_TO_ADD_A + ' <a href="index.php?module=Settings&action=LayoutBlockList&parenttab=Settings&formodule='+moduleName+'" target="_blank"> ' + strings.CUSTOM_FIELD +'</a>');
					}

			jsonget('getexpressionlistjson',
				{modulename:moduleName},
				function(result){
					$('#expressionlist_busyicon').hide();
					
					var exprFields = parsed['exprFields'];
					$('.expressionlistrow').remove();
					$.each(result, function(fieldName, expression){
						var fieldLabel = getfieldlabel(fieldName);
						if(exprFields[fieldName]){
							addFieldExpression(moduleName, fieldName, fieldLabel, expression);
						}else{
						  	jsonget('deleteexpressionjson',
						 			{modulename:moduleName, fieldname:fieldName},
								function(){});
						}

					});
				}
			);
			});
			ep.close();


		}

		$('#pick_module').bind('change', function(){
			var moduleName =  $(this).attr("value");
			setModule(moduleName);
		});
		setModule($('#pick_module').attr('value'));



	};
}
editexpressionscript(jQuery);
