const ModuleBuilder = {
    SaveModule: (step) => {
        if (step == 1) {
            const modulename = document.getElementById('modulename').value;
            const modulelabel = document.getElementById('modulelabel').value;
            const parentmenu = document.getElementById('parentmenu').value;
            if (modulename == '' || modulelabel == '') {
                document.getElementById('ErrorMessage').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('ErrorMessage').style.display = 'none';
                }, 3000);
                return false;
            } else {
                var data = {
                    modulename: modulename,
                    modulelabel: modulelabel,
                    parentmenu: parentmenu,
                    step: step
                };
            }
        }
        if (step == 2) {
            var blocks_label = [];
            const number_block = document.getElementById('number_block').value;
            for (var i = 1; i <= number_block; i++) {
                blocks_label[i] = document.getElementById('blocks_label_' + i).value;
            }
            var data = {
                blocks: blocks_label,
                step: step
            }
        }
        jQuery.ajax({
            method: 'POST',
            url: 'index.php?module=Settings&action=SettingsAjax&file=SaveModuleBuilder',
            data: data
        }).done(function(response) {
            document.getElementById('SuccessMessage').style.display = 'block';
            document.getElementById('step-' + step).style.display = 'none';
            var nextstep = step + 1;
            var progress = parseInt(nextstep) * 20 - 20;
            document.getElementById('progress').style.width = progress + '%';
            document.getElementById('progresstext').innerHTML = 'Progress: ' + progress + '%';
            document.getElementById('step-' + nextstep).style.display = 'block';
            setTimeout(function() {
                document.getElementById('SuccessMessage').style.display = 'none';
            }, 3000);
        });
    },
    updateProgress: (id, step) => {
        if (step == 1) {
            const data = {
                modulename: document.getElementById('modulename').value,
                modulelabel: document.getElementById('modulelabel').value,
                parentmenu: document.getElementById('parentmenu').value,
                moduleicon: document.getElementById('moduleicon').value,
            }
            var NULL = [];
            for (var i in data) {
                if (data[i] == '') {
                    NULL[i] = i;
                }
            }
            var size = Object.keys(NULL).length;
            var progress = (20 - (parseInt(size) * 5));
            document.getElementById('progress').style.width = progress + '%';
            document.getElementById('progresstext').innerHTML = 'Progress: ' + progress + '%';
            if (progress == 20) {
                document.getElementById('btn-step-1').removeAttribute('disabled');
            } else {
                document.getElementById('btn-step-1').setAttribute('disabled', '');
            }
        }
    },
    generateInput: () => {
        var number_block = document.getElementById('number_block').value;
        number_block = parseInt(number_block) + 1;
        document.getElementById('number_block').value = number_block;
        var input = document.createElement("input");
        input.setAttribute('type', 'text');
        input.setAttribute('id', 'blocks_label_' + number_block);
        input.setAttribute('placeholder', 'LBL_BLOCKNAME_INFORMATION');
        input.setAttribute('class', 'slds-input');
        document.getElementById('blocks_inputs').appendChild(input);
    },
    generateFields: () => {
        var number_field = document.getElementById('number_field').value;
        number_field = parseInt(number_field) + 1;
        document.getElementById('number_field').value = number_field;

        var table = document.getElementById("Table");
        var row = table.insertRow(0);
        row.setAttribute('style', 'border: 1px solid #e4e4e4;');
        var cell = row.insertCell(0);
        cell.setAttribute('id', 'fields_inputs_' + number_field);
        cell.setAttribute('style', 'padding: 20px');

        ModuleBuilder.loadBlocks(table, number_field);
        var text = [
            "fieldname",
            "uitype",
            "columnname",
            "generatedtype",
            "fieldlabel",
            "sequence",
            "maximumlength",
            "info_type",
            "helpinfo",
            "entityidentifier",
            "entityidfield",
            "entityidcolumn",
            "relatedmodules"
        ];
        for (var i = 0; i < text.length; i++) {
            var input = document.createElement("input");
            input.setAttribute('type', 'text');
            input.setAttribute('id', text[i] + '_' + number_field);
            input.setAttribute('placeholder', text[i]);
            input.setAttribute('class', 'slds-input');
            input.setAttribute('style', 'width: 15%; margin: 5px');
            document.getElementById('fields_inputs_' + number_field).appendChild(input);
        }

        var select = [
            "Readonly",
            "Presence",
            "Selected",
            "Typeofdata",
            "Quickcreate",
            "Quickcreatesequence",
            "Displaytype",
            "Masseditable",
        ];
        for (var i = 0; i < select.length; i++) {
            var selecttype = document.createElement("select");
            selecttype.setAttribute('id', select[i] + '_' + number_field);
            selecttype.setAttribute('class', 'slds-input');
            selecttype.setAttribute('style', 'width: 15%; margin: 5px');
            document.getElementById('fields_inputs_' + number_field).appendChild(selecttype);
            var option = document.createElement("option");
            var values = [select[i], "0", "1"];
            for (var j = 0; j < values.length; j++) {
                var option = document.createElement("option");
                option.value = values[j];
                option.text = values[j];
                if (j == 0) {
                    option.setAttribute('disabled', '');
                    option.setAttribute('selected', '');
                }
                selecttype.appendChild(option);
            }
        }
    },
    openModal: () => {
        document.getElementById('moduleListsModal').style.display = '';
    },
    closeModal: () => {
        document.getElementById('moduleListsModal').style.display = 'none';
    },
    loadBlocks: (tableInstance, number_field) => {
        jQuery.ajax({
            method: 'GET',
            url: 'index.php?module=Settings&action=SettingsAjax&file=loadBlocks',
        }).done(function(response) {
            var res = JSON.parse(response);
            var row = tableInstance.insertRow(0);
            row.setAttribute('id', 'for-field-' + number_field);
            //create fieldset
            var getCell = document.getElementById('for-field-' + number_field);
            var Createfieldset = document.createElement('fieldset');
            Createfieldset.setAttribute('class', 'slds-form-element');
            Createfieldset.setAttribute('id', 'for-fieldset-' + number_field);
            getCell.appendChild(Createfieldset);
            //create legend
            var getfieldset = document.getElementById('for-fieldset-' + number_field);
            var legend = document.createElement('legend');
            legend.setAttribute('class', 'slds-form-element__legend slds-form-element__label');
            legend.innerHTML = 'Choose block for field ' + number_field;
            getfieldset.appendChild(legend);
            //create ParentDiv
            var parentDiv = document.createElement('div');
            parentDiv.setAttribute('class', 'slds-form-element__control');
            parentDiv.setAttribute('id', 'for-parentdiv-' + number_field);
            getfieldset.appendChild(parentDiv);
            //create child div
            var getParent = document.getElementById('for-parentdiv-' + number_field);
            var childDiv = document.createElement('div');
            childDiv.setAttribute('class', 'slds-radio_button-group');
            childDiv.setAttribute('id', 'for-child-' + number_field);
            getParent.appendChild(childDiv);
            var getDIV = document.getElementById('for-child-' + number_field);
            for (var i = 0; i < res.length; i++) {
                //create span
                var span = document.createElement('span');
                span.setAttribute('class', 'slds-button slds-radio_button');
                span.setAttribute('id', 'for-span-' + i + '-' + number_field);
                getDIV.appendChild(span);
                //create Input
                var inputBlocks = document.createElement('input');
                inputBlocks.setAttribute('type', 'radio');
                inputBlocks.setAttribute('name', 'parentmenu-' + number_field);
                inputBlocks.setAttribute('id', 'input-parentmenu-' + number_field + '-' + res[i].blocksid);
                inputBlocks.setAttribute('value', res[i].blocks_label);
                var getSpan = document.getElementById('for-span-' + i + '-' + number_field);
                getSpan.appendChild(inputBlocks);
                //create label
                var label = document.createElement('label');
                label.setAttribute('class', 'slds-radio_button__label');
                label.setAttribute('for', 'input-parentmenu-' + number_field + '-' + res[i].blocksid);
                label.setAttribute('id', 'label-parentmenu-' + number_field + '-' + res[i].blocksid);
                getSpan.appendChild(label);
                //create span
                var span = document.createElement('span');
                span.setAttribute('class', 'slds-radio_faux');
                span.innerHTML = res[i].blocks_label;
                var getLabel = document.getElementById('label-parentmenu-' + number_field + '-' + res[i].blocksid);
                getLabel.appendChild(span);
            }
        });
    },
};