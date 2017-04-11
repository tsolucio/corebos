/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/  
jQuery.Class("Contact",{
    _init:function(){
        if(jQuery('#sync_button')){
            jQuery('#sync_button').on('click',function(){
                jQuery('#sync_button b').text(app.vtranslate('LBL_SYNCRONIZING'));
                jQuery('#sync_button').attr("disabled", "disabled")
                jQuery('#synctime').remove();
                var imagePath = app.vimage_path('Sync.gif')
                jQuery('#sync_details').html('<img src='+imagePath+' style="margin-left:40px"/>');
                var url = jQuery('#sync_button').data('url');
                if(jQuery('#firsttime').val() == 'yes'){
                     var win=window.open(url,'','height=600,width=600,channelmode=1');
                     //http://stackoverflow.com/questions/1777864/how-to-run-function-of-parent-window-when-child-window-closes 
                     window.sync = function() {
                        jQuery('#sync_details').html('<img src='+imagePath+' style="margin-left:40px"/>'); 
                        AppConnector.request(url).then(
                           function(data) {
                               jQuery('#sync_button b').text(app.vtranslate('LBL_SYNC_BUTTON'));
                               jQuery('#sync_button').removeAttr("disabled");
                               jQuery('#sync_details').html(data);
                               if(jQuery('#norefresh').length == 0){
                                listInstance  =  Vtiger_List_Js.getInstance();
                                listInstance.getListViewRecords();
                               }
                               jQuery('#firsttime').val('no');
                               jQuery('#removeSyncBlock').show();
                           }
                           );
                     }
                     
                     
                     win.onunload = function(){
                         jQuery('#sync_button b').text(app.vtranslate('LBL_SYNC_BUTTON'));
                         jQuery('#sync_button').removeAttr("disabled");
                         jQuery('#sync_details').html(app.vtranslate('LBL_NOT_SYNCRONIZED'));
                     }
                         
                     
                } else {
                    AppConnector.request(url).then(
						function(data) {
                            var response;
                            try {
                                response = JSON.parse(data);
                            } catch (e) {
                                
                            }
                            if(response && response.error.code == '401') {
                                jQuery('#firsttime').val('yes');
                                jQuery('#removeSyncBlock').hide();
                                jQuery('#sync_button').click();
                                
                            } else {
                            jQuery('#sync_button b').text(app.vtranslate('LBL_SYNC_BUTTON'));
                            jQuery('#sync_button').removeAttr("disabled");
                            jQuery('#sync_details').html(data);
                            if(jQuery('#norefresh').length == 0){
                                listInstance  =  Vtiger_List_Js.getInstance();
                                listInstance.getListViewRecords()
                            }
                        }
                    });
                }
                
            });
            jQuery('#remove_sync').on('click',function(){
                var url = jQuery('#remove_sync').data('url');
                AppConnector.request(url).then(
						function(data) {
                            jQuery('#firsttime').val('yes');
                            jQuery('#removeSyncBlock').hide();
                            var params = {
                                title : app.vtranslate('JS_MESSAGE'),
                                text: app.vtranslate('SYNC_REMOVED_SUCCESSFULLY'),
                                animation: 'show',
                                type: 'info'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                        );
                
            });
        }
		var data=jQuery('#mappingTable').html();
		jQuery('#popid').popover({
			'html':true,
			'content': data,
			'title':app.vtranslate('FIELD_MAPPING')
		});
        
        jQuery('#removePop').popover({
			'html':true,
			'content': app.vtranslate('REMOVE_SYNCHRONIZATION_MESSAGE'),
			'title': app.vtranslate('REMOVE_SYNCHRONIZATION')
		});
		
    },
    
    _showMessage : function(){
       
    },
    _exit:function(){
        
    }
},{
    
    packFieldmappingsForSubmit : function(container) {
        var rows = container.find('div#googlesyncfieldmapping > table > tbody > tr');
        var mapping = {};
        jQuery.each(rows,function(index,row) {
            var tr = jQuery(row);
            var vtiger_field_name = tr.find('.vtiger_field_name').not('.select2-container').val();
            var google_field_name = tr.find('.google_field_name').val();
            var googleTypeElement = tr.find('.google-type').not('.select2-container');
            var google_field_type = '';
            var google_custom_label = '';
            if(googleTypeElement.length) {
                google_field_type = googleTypeElement.val();
                var customLabelElement = tr.find('.google-custom-label');
                if(google_field_type == 'custom' && customLabelElement.length) {
                    google_custom_label = customLabelElement.val();
                }
            }
            var map = {};
            map['vtiger_field_name'] = vtiger_field_name;
            map['google_field_name'] = google_field_name;
            map['google_field_type'] = google_field_type;
            map['google_custom_label'] = google_custom_label;
            mapping[index] = map;
        });
        return JSON.stringify(mapping);
    },
    
    validateFieldMappings : function(container) {
        var aDeferred = jQuery.Deferred();
        
        var customMapElements = jQuery('select.vtiger_field_name');
        var mappedCustomFields = [];
        jQuery.each(customMapElements,function(i,elem) {
            mappedCustomFields.push(jQuery(elem).val());
        });
        
        var customFieldLabels = jQuery('input.google-custom-label',container).filter('input:text[value=""]').filter(function() {
            return jQuery(this).css('visibility') == 'visible';
        });
        if(customFieldLabels.length) {
            aDeferred.reject(customFieldLabels);
        } else {
            aDeferred.resolve();
        }
        return aDeferred.promise();
    },
    
    registerSaveSettingsEvent : function(container) {
        var thisInstance = this;
        container.find('button#save_syncsetting').on('click',function() {
            thisInstance.validateFieldMappings(container).then(function() {
                var progressIndicatorElement = jQuery.progressIndicator();
                var form = container.find('form[name="contactsyncsettings"]');
                var fieldMapping = thisInstance.packFieldmappingsForSubmit(container);
                form.find('#user_field_mapping').val(fieldMapping);
                var serializedFormData = form.serialize();
                AppConnector.request(serializedFormData).then(function(data) {
                    progressIndicatorElement.progressIndicator({mode:'hide'});
                    app.hideModalWindow();
                    Vtiger_Helper_Js.showMessage({text: app.vtranslate('JS_SAVED_SUCCESSFULLY')});
                });
            }, function(inputs) {
                inputs.focusin().focusout();
            });
        });
    },
    
    registerAddCustomFieldMappingEvent : function(container) {
        var thisInstance = this;
        jQuery('.addCustomFieldMapping',container).on('click',function(e) {
            var currentSelectionElement = jQuery(this);
            var googleFields = JSON.parse(container.find('input#google_fields').val());
            var selectionType = currentSelectionElement.data('type');
            var vtigerFields = currentSelectionElement.data('vtigerfields');
            
            var vtigerFieldSelectElement = '<select class="vtiger_field_name" style="width:200px" data-category="'+selectionType+'">';
            if(!Object.keys(vtigerFields).length) {
                alert(app.vtranslate('JS_SUITABLE_VTIGER_FIELD_NOT_AVAILABLE_FOR_MAPPING'));
                return;
            }
            
            var customMapElements = jQuery('select.vtiger_field_name[data-category="'+selectionType+'"]');
            var mappedCustomFields = [];
            jQuery.each(customMapElements,function(i,elem) {
                    mappedCustomFields.push(jQuery(elem).val());
            });
            var numberOfOptions = 0;
            jQuery.each(vtigerFields,function(fieldname,fieldLabel) {
                if(jQuery.inArray(fieldname,mappedCustomFields) === -1) {
                    numberOfOptions++;
                    var option = '<option value="'+fieldname+'">'+fieldLabel+'</option>';
                    vtigerFieldSelectElement += option;
                }
            });
            if(numberOfOptions == 0) {
                alert(app.vtranslate('JS_SUITABLE_VTIGER_FIELD_NOT_AVAILABLE_FOR_MAPPING'));
                return;
            }
            
            vtigerFieldSelectElement += '</select>';
            var googleTypeSelectElement = '';
            if(selectionType != 'custom') {
                googleTypeSelectElement = '<input type="hidden" class="google_field_name" value="'+ googleFields[selectionType]['name'] +'" />\n\
                                               <select class="google-type" style="width:200px;" data-category="'+selectionType+'">';
                
                var allCategorizedSelects = jQuery('select.google-type[data-category="'+selectionType+'"]');
                var selectedValues = [];

                jQuery.each(allCategorizedSelects, function(i, selectElement){
                    if(jQuery(selectElement).val() !== 'custom') {
                        selectedValues.push(jQuery(selectElement).val());
                    }
                });
                jQuery.each(googleFields[selectionType]['types'],function(index,fieldtype) {
                    if(jQuery.inArray(fieldtype, selectedValues) === -1) {
                        var option = '<option value="'+fieldtype+'">'+app.vtranslate(selectionType)+' ('+app.vtranslate(fieldtype)+')'+'</option>';
                        googleTypeSelectElement += option;
                    }
                });
                googleTypeSelectElement += '</select>\n\
                                 &nbsp;&nbsp;<input type="text" class="google-custom-label" style="visibility:hidden;width:190px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />';
            } else {
                googleTypeSelectElement = '<input type="hidden" class="google_field_name" value="'+ googleFields[selectionType]['name'] +'" />';
                googleTypeSelectElement += '<input type="hidden" class="google-type" value="'+selectionType+'" />';
                googleTypeSelectElement += '<input type="text" class="google-custom-label" style="width:190px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />';
            }
            var tabRow = '<tr>\n\
                            <td>' + vtigerFieldSelectElement + '</td>\n\
                            <td>' + googleTypeSelectElement + '<a class="deleteCustomMapping pull-right"><i title="Delete" class="icon-trash"></i></a></td>\n\
                          </tr>';
            var tbodyElement = container.find('div#googlesyncfieldmapping > table > tbody');
            tbodyElement.append(tabRow);
            var lastRow = container.find('div#googlesyncfieldmapping > table > tbody > tr').filter(':last');
            app.showSelect2ElementView(lastRow.find('select'));
            thisInstance.registerDeleteCustomFieldMappingEvent(lastRow);
            thisInstance.registerVtigerFieldSelectOnChangeEvent(container,lastRow.find('select.vtiger_field_name'));
            thisInstance.registerGoogleTypeChangeEvent(container,lastRow.find('select.google-type'));
            lastRow.find('select.vtiger_field_name').trigger('change');
            lastRow.find('select.google-type').trigger('change');
            app.showScrollBar(jQuery('div#googlesyncfieldmapping'),{'height': '350px','scroll':1000000,'railVisible': true});
        });
    },
    
    registerDeleteCustomFieldMappingEvent : function(container) {
        jQuery('.deleteCustomMapping',container).on('click',function() {
            var currentRow = jQuery(this).closest('tr');
            var currentCategory = currentRow.find('select.vtiger_field_name').data('category');
            currentRow.remove();
            jQuery('select.vtiger_field_name[data-category="'+currentCategory+'"]').trigger('change');
            jQuery('select.google-type[data-category="'+currentCategory+'"]').trigger('change');
        });
    },
    
    updateSelectElement : function(allValuesMap, selectedValues, element) {
        var prevSelectedValues = element.val();
        element.html('');
        for(var value in allValuesMap) {
           element.append(jQuery('<option></option>').attr('value', value).text(app.vtranslate(allValuesMap[value])));
        }
        for(var index in selectedValues) {
            if (jQuery.inArray(selectedValues[index], [prevSelectedValues]) === -1) {
                var strInputString = selectedValues[index].replace(/'/g, "\\'");
                element.find("option[value='"+strInputString+"']").remove();
            }
        }
        if(prevSelectedValues) {
            element.select2("val", prevSelectedValues);
        }
   },
    
    removeOptionFromSelectList : function(selectElement,optionValue,category) {
        var sourceSelectElement = jQuery(selectElement);
        var categorisedSelectElements = jQuery('select.vtiger_field_name[data-category="'+category+'"]');
        jQuery.each(categorisedSelectElements,function(index,categorisedSelectElement) {
            var currentSelectElement = jQuery(categorisedSelectElement);
            if(!currentSelectElement.is(sourceSelectElement)) {
                var optionElement = currentSelectElement.find('option[value="'+optionValue+'"]');
                if(optionElement.length) {
                    optionElement.remove();
                    currentSelectElement.select2();
                }
            }
        });
    },
    
    registerVtigerFieldSelectOnChangeEvent : function(container,selectElement) {
        var thisInstance = this;
        if(typeof selectElement === 'undefined') {
            selectElement = jQuery('select.vtiger_field_name',container);   
        }
        selectElement.on('change', function(e){
            var element = jQuery(e.currentTarget);
            var category = element.data('category');
            
            var allCategorizedSelects = jQuery('select.vtiger_field_name[data-category="'+category+'"]');
            var selectedValues = [];
            
            jQuery.each(allCategorizedSelects, function(i, selectElement){
                selectedValues.push($(selectElement).val());
            });
            
            jQuery.each(allCategorizedSelects, function(i, selectElement){
                if(e.currentTarget !== selectElement || allCategorizedSelects.length == 1) {
                    var allCategoryFieldLabelValues = jQuery('li.addCustomFieldMapping[data-type="'+category+'"]').data('vtigerfields');
                    thisInstance.updateSelectElement(allCategoryFieldLabelValues, selectedValues, jQuery(selectElement));
                }
            });
        });
    },
    
    registerGoogleTypeChangeEvent : function(container,selectElement) {
        var thisInstance = this;
        
        if(typeof selectElement === 'undefined') {
            selectElement = jQuery('select.google-type',container);
        }

        selectElement.on('change',function(e) {
            var element = jQuery(e.currentTarget);
            var category = element.data('category');
            
            var currentTarget = element;
            var val = currentTarget.val();
            if(val == 'custom') {
                currentTarget.closest('td').find('input.google-custom-label').css('visibility','visible');
            } else {
                currentTarget.closest('td').find('input.google-custom-label').css('visibility','hidden');
            }
            
            var allCategorizedSelects = jQuery('select.google-type[data-category="'+category+'"]');
            var selectedValues = [];
            
            jQuery.each(allCategorizedSelects, function(i, selectElement){
                if(jQuery(selectElement).val() !== 'custom') {
                    selectedValues.push(jQuery(selectElement).val());
                }
            });

            var googleFields = JSON.parse(container.find('input#google_fields').val());
            var allValues = {};
            jQuery.each(googleFields[category]['types'],function(index,value) {
                allValues[value] = app.vtranslate(category)+' ('+app.vtranslate(value)+')';
            });
            
            jQuery.each(allCategorizedSelects, function(i, selectElement){
                var allCategoryFieldLabelValues = allValues;
                thisInstance.updateSelectElement(allCategoryFieldLabelValues, selectedValues, jQuery(selectElement));
            });
        });
        
    },
    
    registerPostSettingRenderEvents : function(container) {
        jQuery('form[name="contactsyncsettings"]',container).validationEngine(app.validationEngineOptions);
        this.registerAddCustomFieldMappingEvent(container);
        this.registerDeleteCustomFieldMappingEvent(container);
        this.registerSaveSettingsEvent(container);
        this.registerVtigerFieldSelectOnChangeEvent(container);
        this.registerGoogleTypeChangeEvent(container);

        jQuery('select.vtiger_field_name',container).trigger('change');
        jQuery('select.google-type',container).trigger('change');
    },
    
    registerSyncSettingClickEvent : function() {
        var thisInstance = this;
        if(jQuery('a#syncSetting').length) {
            jQuery('a#syncSetting').on('click',function() {
                var progressIndicatorElement = jQuery.progressIndicator();
                var params = {
                    module : 'Google',
                    view : 'Setting',
                    sourcemodule : app.getModuleName()
                }
                AppConnector.request(params).then(function(data) {
                    app.showModalWindow(data, function(container) {
                        app.showScrollBar(jQuery('div#googlesyncfieldmapping'),{'height': '350px'});
                        thisInstance.registerPostSettingRenderEvents(container);
                        progressIndicatorElement.progressIndicator({mode:'hide'});
                    });
                });
            });
        }
    },
    
    registerEvents : function() {
        this.registerSyncSettingClickEvent();
    }
    
});

jQuery('document').ready(function(){
	jQuery('#mappingTable').hide();
  Contact._init();
    var instance = new Contact;
    instance.registerEvents();
});

