/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

if (typeof(ImportJs) == 'undefined') {
    /*
	 * Namespaced javascript class for Import
	 */
    ImportJs = {

		toogleMergeConfiguration: function() {
			var mergeChecked = jQuery('#auto_merge').is(':checked');
			if(mergeChecked) {
				jQuery('#duplicates_merge_configuration').show();
			} else {
				jQuery('#duplicates_merge_configuration').hide();
			}
		},

		checkFileType: function() {
			var filePath = jQuery('#import_file').val();
			if(filePath != '') {
				var fileExtension = filePath.split('.').pop();
				jQuery('#type').val(fileExtension);
				ImportJs.handleFileTypeChange();
			}
		},

		handleFileTypeChange: function() {
			var fileType = jQuery('#type').val();
			if(fileType != 'csv') {
				jQuery('#delimiter_container').hide();
				jQuery('#has_header_container').hide();
			} else {
				jQuery('#delimiter_container').show();
				jQuery('#has_header_container').show();
			}
		},

        uploadAndParse: function() {
			if(!ImportJs.validateFilePath()) return false;
			if(!ImportJs.validateMergeCriteria()) return false;
			return true;
        },

		validateFilePath: function() {
			var filePath = jQuery('#import_file').val();
			if(jQuery.trim(filePath) == '') {
				alert('Import File '+alert_arr.CANNOT_BE_EMPTY)
				jQuery('#import_file').focus();
				return false;
			}
			if(!ImportJs.uploadFilter("import_file", "csv|vcf")) {
				return false;
			}
			return true;
		},

		uploadFilter: function(elementId, allowedExtensions) {
			var obj = jQuery('#'+elementId);
			if(obj) {
				var filePath = obj.val();
				var fileParts = filePath.toLowerCase().split('.');
				var fileType = fileParts[fileParts.length-1];
				var validExtensions = allowedExtensions.toLowerCase().split('|');

				if(validExtensions.indexOf(fileType) < 0) {
					alert(alert_arr.PLS_SELECT_VALID_FILE+' '+validExtensions);
					obj.focus();
					return false;
				}
			}
			return true;
		},

		validateMergeCriteria: function() {
			$mergeChecked = jQuery('#auto_merge').is(':checked');
			if($mergeChecked) {
				var selectedOptions = jQuery('#selected_merge_fields option');
				if(selectedOptions.length == 0) {
					alert(alert_arr.ERR_SELECT_ATLEAST_ONE_MERGE_CRITERIA_FIELD);
					return false;
				}
			}
			convertOptionsToJSONArray('selected_merge_fields', 'merge_fields');
			return true;
		},

		sanitizeAndSubmit: function() {
			if(!ImportJs.sanitizeFieldMapping()) return false;
			if(!ImportJs.validateCustomMap()) return false;
			return true;
		},

		sanitizeFieldMapping: function() {
			var fieldsList = jQuery('.fieldIdentifier');
			var mappedFields = {};
			var mappedDefaultValues = {};
			for(var i=0; i<fieldsList.length; ++i) {
				var fieldElement = jQuery(fieldsList.get(i));
				var rowId = jQuery('[name=row_counter]', fieldElement).get(0).value;
				var selectedFieldElement = jQuery('select option:selected', fieldElement);
				var selectedFieldName = selectedFieldElement.val();
				var selectedFieldDefaultValueElement = jQuery('#'+selectedFieldName+'_defaultvalue', fieldElement);
				var defaultValue = '';
				if(selectedFieldDefaultValueElement.attr('type') == 'checkbox') {
					defaultValue = selectedFieldDefaultValueElement.is(':checked');
				} else {
					defaultValue = selectedFieldDefaultValueElement.val();
				}
				if(selectedFieldName != '') {
					if(selectedFieldName in mappedFields) {
						alert(alert_arr.ERR_FIELDS_MAPPED_MORE_THAN_ONCE + ' "' + selectedFieldElement.html() +'"');
						return false;
					}
					mappedFields[selectedFieldName] = rowId-1;
					if(defaultValue != '') {
						mappedDefaultValues[selectedFieldName] = defaultValue;
					}
				}
			}

			var mandatoryFields = JSON.parse(jQuery('#mandatory_fields').val());
			var missingMandatoryFields = [];
			for(var mandatoryFieldName in mandatoryFields) {
				if(mandatoryFieldName in mappedFields) {
					continue;
				} else {
					missingMandatoryFields.push('"'+mandatoryFields[mandatoryFieldName]+'"');
				}
			}
			if(missingMandatoryFields.length > 0) {
				alert(alert_arr.ERR_PLEASE_MAP_MANDATORY_FIELDS + ' : ' + missingMandatoryFields.join(','));
				return false;
			}
			jQuery('#field_mapping').val(JSON.stringify(mappedFields));
			jQuery('#default_values').val(JSON.stringify(mappedDefaultValues));
			return true;
		},

		validateCustomMap: function() {
			var saveMap = jQuery('#save_map').is(':checked');
			if(saveMap) {
				var mapName = jQuery('#save_map_as').val();
				if(jQuery.trim(mapName) == '') {
					alert(alert_arr.ERR_MAP_NAME_CANNOT_BE_EMPTY);
					return false;
				}
				var mapOptions = jQuery('#saved_maps option');
				for(var i=0; i<mapOptions.length; ++i) {
					var mapOption = jQuery(mapOptions.get(i));
					if(mapOption.html() == mapName) {
						alert(alert_arr.ERR_MAP_NAME_ALREADY_EXISTS);
						return false;
					}
				}
			}
			return true;
		},

		loadSavedMap: function() {
			var selectedMapElement = jQuery('#saved_maps option:selected');
			var mapId = selectedMapElement.attr('id');
			var fieldsList = jQuery('.fieldIdentifier');
			fieldsList.each(function(i, element) {
				var fieldElement = jQuery(element);
				jQuery('[name=mapped_fields]', fieldElement).val('');
			});
			if(mapId == -1) {
				jQuery('#delete_map_container').hide();
				return;
			}
			jQuery('#delete_map_container').show();
			var mappingString = selectedMapElement.val()
			if(mappingString == '') return;
			var mappingPairs = mappingString.split('&');
			var mapping = {};
			for(var i=0; i<mappingPairs.length; ++i) {
				var mappingPair = mappingPairs[i].split('=');
				var header = mappingPair[0];
				header = header.replace(/\/eq\//g, '=');
				header = header.replace(/\/amp\//g, '&');
				mapping["'"+header+"'"] = mappingPair[1];
			}
			fieldsList.each(function(i, element) {
				var fieldElement = jQuery(element);
				var rowId = jQuery('[name=row_counter]', fieldElement).get(0).value;
				var headerNameElement = jQuery('[name=header_name]', fieldElement).get(0);
				var headerName = jQuery(headerNameElement).html();
				if("'"+headerName+"'" in mapping) {
					jQuery('[name=mapped_fields]', fieldElement).val(mapping["'"+headerName+"'"]);
				} else if(rowId in mapping) {
					jQuery('[name=mapped_fields]', fieldElement).val($rowId);
				}
				ImportJs.loadDefaultValueWidget(fieldElement.attr('id'));
			});
		},

		deleteMap : function(module) {
			if(confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)) {
				var selectedMapElement = jQuery('#saved_maps option:selected');
				var mapId = selectedMapElement.attr('id');
				jQuery('#status').show();
				jQuery.ajax( {
					url  : 'index.php',
					type : 'POST',
					data : {module: module,
							action: module+'Ajax',
							file: 'Import',
							mode: 'delete_map',
							mapid: mapId,
							ajax: true},
					complete : function(response) {
						jQuery('#savedMapsContainer').html(response.responseText);
						jQuery('#status').hide();
					}
				});
			}
		},

		loadListViewPage: function(module, pagenum, userid) {
			jQuery('#status').show();
			jQuery.ajax( {
                url  : 'index.php',
                type : 'POST',
                data : {module: module,
						action: module+'Ajax',
						file: 'Import',
						mode: 'listview',
						start: pagenum,
						foruser: userid,
						ajax: true},
                complete : function(response) {
					jQuery('#import_listview_contents').html(response.responseText);
					jQuery('#status').hide();
                }
            });
		},

		loadListViewSelectedPage: function(module, userid) {
			var pagenum = jQuery('#page_num').val();
			ImportJs.loadListViewPage(module, pagenum, userid);
		},

		loadDefaultValueWidget: function(rowIdentifierId) {
			var affectedRow = jQuery('#'+rowIdentifierId);
			if(typeof affectedRow == 'undefined' || affectedRow == null) return;
			var selectedFieldElement = jQuery('[name=mapped_fields]', affectedRow).get(0);
			var selectedFieldName = jQuery(selectedFieldElement).val();
			var defaultValueContainer = jQuery(jQuery('[name=default_value_container]', affectedRow).get(0));
			var allDefaultValuesContainer = jQuery('#defaultValuesElementsContainer');
			if(defaultValueContainer.children.length > 0) {
				var copyOfDefaultValueWidget = jQuery(':first', defaultValueContainer).detach();
				copyOfDefaultValueWidget.appendTo(allDefaultValuesContainer);
			}
			var selectedFieldDefValueContainer = jQuery('#'+selectedFieldName+'_defaultvalue_container', allDefaultValuesContainer);
			var defaultValueWidget = selectedFieldDefValueContainer.detach();
			defaultValueWidget.appendTo(defaultValueContainer);
		},

		loadDefaultValueWidgetForMappedFields: function() {
			var fieldsList = jQuery('.fieldIdentifier');
			fieldsList.each(function(i, element) {
				var fieldElement = jQuery(element);
				var mappedFieldName = jQuery('[name=mapped_fields]', fieldElement).val();
				if(mappedFieldName != '') {
					ImportJs.loadDefaultValueWidget(fieldElement.attr('id'));
				}
			});

		}
    }

	jQuery(document).ready(function() {
		ImportJs.toogleMergeConfiguration();
		ImportJs.loadDefaultValueWidgetForMappedFields();
	});
}