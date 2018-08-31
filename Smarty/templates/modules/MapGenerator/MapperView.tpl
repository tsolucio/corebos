<script src="include/js/json.js" type="text/javascript" charset="utf-8"></script>
<script language="JavaScript" type="text/javascript" src="include/js/advancefilter.js"></script>
{if $JS_DATEFORMAT eq ''}
    {assign var="JS_DATEFORMAT" value=$APP.NTC_DATE_FORMAT|@parse_calendardate}
{/if}
<input type="hidden" id="jscal_dateformat" name="jscal_dateformat" value="{$JS_DATEFORMAT}"/>
<input type="hidden" id="image_path" name="image_path" value="{$IMAGE_PATH}"/>
<input type="hidden" name="advft_criteria" id="advft_criteria" value=""/>
<input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value=""/>

<script language="JavaScript" type="text/JavaScript">
    function addColumnConditionGlue(columnIndex) {ldelim}

        var columnConditionGlueElement = document.getElementById('columnconditionglue_' + columnIndex);

        {*
         <div class="slds-form-element"><div class="slds-form-element__control"> <div class="slds-select_container">
                      <select id="select-01" class="slds-select">
                        <option>Option One</option>
                        <option>Option Two</option>
                        <option>Option Three</option>
                      </select>
                    </div>
                  </div>
                </div>
        *}

        if (columnConditionGlueElement) {ldelim}

            columnConditionGlueElement.innerHTML = " <div class='slds-select_container'><select name='fcon" + columnIndex + "' id='fcon" + columnIndex + "' class='slds-select detailedViewTextBox'>" +
                "<option value='and'>{'LBL_CRITERIA_AND'|@getTranslatedString:$MODULE}</option>" +
                "<option value='or'>{'LBL_CRITERIA_OR'|@getTranslatedString:$MODULE}</option>" +
                "</select></div>";
            {rdelim}
        {rdelim}

    function addConditionRow(groupIndex) {ldelim}

        var groupColumns = column_index_array[groupIndex];
        if (typeof(groupColumns) != 'undefined') {ldelim}
            for (var i = groupColumns.length - 1; i >= 0; --i) {ldelim}
                var prevColumnIndex = groupColumns[i];
                if (document.getElementById('conditioncolumn_' + groupIndex + '_' + prevColumnIndex)) {ldelim}
                    addColumnConditionGlue(prevColumnIndex);
                    break;
                    {rdelim}
                {rdelim}
            {rdelim}

        var columnIndex = advft_column_index_count + 1;
        var nextNode = document.getElementById('groupfooter_' + groupIndex);

        var newNode = document.createElement('tr');
        newNodeId = 'conditioncolumn_' + groupIndex + '_' + columnIndex;
        newNode.setAttribute('id', newNodeId);
        newNode.setAttribute('name', 'conditionColumn');
        nextNode.parentNode.insertBefore(newNode, nextNode);


        openbackets = document.createElement('td');
        openbackets.setAttribute('class', 'dvtCellLabel');
        openbackets.setAttribute('width', '30px');
        newNode.appendChild(openbackets);
        jQuery('#fcol' + columnIndex).selectedIndex = -1;

        openbackets.innerHTML = '<div ><select name="openbackets' + columnIndex + '" id="openbackets' + columnIndex + '" title="Yes if you want to open the backets" class="slds-select repBox" style="width:40px;" onchange="addRequiredElements(' + columnIndex + ');">' +
            '<option value="1">&#123;</option><option value="0">None</option>' +
            '{$FOPTION}' +
            '</select></div>';


        node1 = document.createElement('td');
        node1.setAttribute('class', 'dvtCellLabel');
        node1.setAttribute('width', '25%');
        newNode.appendChild(node1);

        node1.innerHTML = "<div class='slds-select_container'><select name='fcol" + columnIndex + "' id='fcol" + columnIndex + "' onchange='updatefOptions(this, \"fop" + columnIndex + "\");addRequiredElements(" + columnIndex + ");' class=' slds-select  detailedViewTextBox'>" +
            "<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>" +
                {foreach $valueli as $value=>$val}
            "<option value='{$val["Values"]}'>{$val["Texti"]}</option>" +
                {/foreach}
            "</select></div>";
        node2 = document.createElement('td');
        node2.setAttribute('class', 'dvtCellLabel');
        node2.setAttribute('width', '25%');
        newNode.appendChild(node2);
        jQuery('#fcol' + columnIndex).selectedIndex = -1;
        node2.innerHTML = '<div ><select name="fop' + columnIndex + '" id="fop' + columnIndex + '" class="slds-select repBox" style="width:100px;" onchange="addRequiredElements(' + columnIndex + ');">' +
            '<option value="">{'LBL_NONE'|@getTranslatedString:$MODULE}</option>' +
            '{$FOPTION}' +
            '</select></div>';

        node3 = document.createElement('td');
        node3.setAttribute('class', 'dvtCellLabel');
        newNode.appendChild(node3);
        {if $SOURCE eq 'reports'}
        node3.innerHTML = '<div class='
        slds - form - element__control
        '><input name="fval' + columnIndex + '" id="fval' + columnIndex + '" class="slds-input repBox" placeholder='
        Enter
        the
        text
        ' type="text" value=""> </div>' +
        '<img height=20 width=20 align="absmiddle" style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src="themes/images/terms.gif" onClick="hideAllElementsByName(\'relFieldsPopupDiv\'); fnvshobj(this,\'show_val' + columnIndex + '\');"/>' +
        '<input type="image" align="absmiddle" style="cursor: pointer;" onclick="document.getElementById(\'fval' + columnIndex + '\').value=\'\';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>' +
        '<div class="layerPopup" id="show_val' + columnIndex + '" name="relFieldsPopupDiv" style="border:0; position: absolute; width:300px; z-index: 50; display: none;">' +
        '<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">' +
        '<tr>' +
        '<td>' +
        '<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">' +
        '<tr background="themes/images/qcBg.gif" class="mailSubHeader">' +
        '<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>' +
        '<td align=right>' +
        '<img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="hideAllElementsByName(\'relFieldsPopupDiv\');"/>' +
        '</td>' +
        '</tr>' +
        '</table>' +

        '<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">' +
        '<tr>' +
        '<td>' +
        '<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">' +
        '<tr>' +
        '<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>' +
        '<td width="30%" align="left" class="cellText">' +
        '<select name="fval_' + columnIndex + '" id="fval_' + columnIndex + '" onChange="AddFieldToFilter(' + columnIndex + ',this);" class="detailedViewTextBox">' +
        '<option value="">{$MOD.LBL_NONE}</option>' +
        '{$REL_FIELDS}' +
        '</select>' +
        '</td>' +
        '</tr>' +
        '</table>' +
        '<!-- save cancel buttons -->' +
        '<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">' +
        '<tr>' +
        '<td width="50%" align="center">' +
        '<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="hideAllElementsByName(\'relFieldsPopupDiv\');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>' +
        '</td>' +
        '</tr>' +
        '</table>' +
        '</td>' +
        '</tr>' +
        '</table>' +
        '</td>' +
        '</tr>' +
        '</table>' +
        '</div>';
        {else}
        node3.innerHTML = '<div ><input name="fval' + columnIndex + '" id="fval' + columnIndex + '" class="slds-select repBox" type="text" value="">' +
            '<input type="image" align="absmiddle" style="cursor: pointer;" onclick="document.getElementById(\'fval' + columnIndex + '\').value=\'\';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/></div>';
        {/if}

        node4 = document.createElement('td');
        node4.setAttribute('class', 'dvtCellLabel');
        node4.setAttribute('id', 'columnconditionglue_' + columnIndex);
        node4.setAttribute('width', '90px');
        newNode.appendChild(node4);

        node5 = document.createElement('td');
        node5.setAttribute('class', 'dvtCellLabel');
        node5.setAttribute('width', '30px');
        newNode.appendChild(node5);
        node5.innerHTML = '<span class="slds-avatar slds-avatar--medium">' +
            '<a onclick="deleteColumnRow(' + groupIndex + ',' + columnIndex + ');" href="javascript:;">' +
            '<img src="themes/images/delete.gif" align="absmiddle" title="{$MOD.LBL_DELETE}..." border="0">' +
            '</a></span>';

        closeBackets = document.createElement('td');
        closeBackets.setAttribute('class', 'dvtCellLabel');
        closeBackets.setAttribute('width', '40px');
        newNode.appendChild(closeBackets);
        jQuery('#fcol' + columnIndex).selectedIndex = -1;

        closeBackets.innerHTML = '<div ><select name="closeBackets' + columnIndex + '" id="closeBackets' + columnIndex + '" class="slds-select repBox" style="width:40px;" onchange="addRequiredElements(' + columnIndex + ');">' +
            '<option value="0">none</option><option value="1">&#125;</option>' +
            '{$FOPTION}' +
            '</select></div>';


        if (document.getElementById('fcol' + columnIndex)) updatefOptions(document.getElementById('fcol' + columnIndex), 'fop' + columnIndex);
        if (typeof(column_index_array[groupIndex]) == 'undefined') column_index_array[groupIndex] = [];
        column_index_array[groupIndex].push(columnIndex);
        advft_column_index_count++;

        {rdelim}

    function addGroupConditionGlue(groupIndex) {ldelim}

        var groupConditionGlueElement = document.getElementById('groupconditionglue_' + groupIndex);
        if (groupConditionGlueElement) {ldelim}
            groupConditionGlueElement.innerHTML = "<select name='gpcon" + groupIndex + "' id='gpcon" + groupIndex + "' class='slds-select small'>" +
                "<option value='and'>{'LBL_CRITERIA_AND'|@getTranslatedString:$MODULE}</option>" +
                "<option value='or'>{'LBL_CRITERIA_OR'|@getTranslatedString:$MODULE}</option>" +
                "</select>";
            {rdelim}
        {rdelim}

    function addConditionGroup(parentNodeId) {ldelim}

        for (var i = group_index_array.length - 1; i >= 0; --i) {ldelim}
            var prevGroupIndex = group_index_array[i];
            if (document.getElementById('conditiongroup_' + prevGroupIndex)) {ldelim}
                addGroupConditionGlue(prevGroupIndex);
                break;
                {rdelim}
            {rdelim}

        var groupIndex = advft_group_index_count + 1;
        var parentNode = document.getElementById(parentNodeId);
        var newNode = document.createElement('div');
        newNodeId = 'conditiongroup_' + groupIndex;
        newNode.setAttribute('id', newNodeId);
        newNode.setAttribute('name', 'conditionGroup');

        newNode.innerHTML = "<table class='small crmTable' border='0' cellpadding='5' cellspacing='1' width='100%' valign='top' id='conditiongrouptable_" + groupIndex + "'>" +
            "<tr id='groupheader_" + groupIndex + "'>" +
            "<td colspan='5' align='right'>" +
            "<a href='javascript:void(0);' onclick='deleteGroup(\"" + groupIndex + "\");'><img border=0 src={'close.gif'|@vtiger_imageurl:$THEME} alt='{$MOD.LBL_DELETE_GROUP}' title='{$MOD.LBL_DELETE_GROUP}'/></a>" +
            "</td>" +
            "</tr>" +
            "<tr id='groupfooter_" + groupIndex + "'>" +
            "<td colspan='5' align='left'>" +
            "<button class='slds-button slds-button--neutral' onclick='addConditionRow(\"" + groupIndex + "\")'>{$MOD.LBL_NEW_CONDITION}</button>"
            +
            "</td>" +
            "</tr>" +
            "</table>" +
            "<table class='small' border='0' cellpadding='5' cellspacing='1' width='100%' valign='top'>" +
            "<tr><td align='center' id='groupconditionglue_" + groupIndex + "'>" +
            "</td></tr>" +
            "</table>";

        parentNode.appendChild(newNode);

        group_index_array.push(groupIndex);
        advft_group_index_count++;
        {rdelim}
</script>
<div id="accordion">
    <h3>{$MOD.Query}</h3>
    <div id="joinquery">
        <p id="generatedjoin">
            {$QUERY}
        </p>
        <p id="generatedConditions"></p>
    </div>
    <h3>{$MOD.conditions}</h3>
    <div id="queryfilters">
        <div style="overflow:auto;" id='where_filter_div' name='where_filter_div'>
            <article class="slds-card">
                <div class="slds-card__header slds-grid">
                    <header class="slds-media slds-media--center slds-has-flexi-truncate">

                        <div class="slds-media__body">
                            <h2>
                                <a href="javascript:void(0);" class="slds-card__header-link slds-truncate">
                                    <span class="slds-text-heading--small">{'LBL_ADVANCED_FILTER'|@getTranslatedString:$MODULE}</span>
                                </a>
                            </h2>
                        </div>
                    </header>
                    <div class="slds-no-flex">
                        <button class="slds-button slds-button--neutral"
                                onclick="addNewConditionGroup('where_filter_div');">{'LBL_NEW_GROUP'|@getTranslatedString:$MODULE}</button>
                    </div>
                    <div class="slds-no-flex">
                        <button class="slds-button slds-button--neutral" name="addwhereconditions"
                                onClick="return validateCV();">{$MOD.generate}
                        </button>
                    </div>
                </div>
                <div class="slds-card__body">
                    <table class="slds-table slds-table--bordered slds-no-row-hover slds-table--cell-buffer"
                           cellpadding="5" cellspacing="0" width="100%">
                        <tr>
                            {*<td class="detailedViewHeader" align="left"><b>{'LBL_ADVANCED_FILTER'|@getTranslatedString:$MODULE}</b></td>*}
                        </tr>
                        <tr>

                        </tr>

                    </table>
                </div>
                <!-- <div class="slds-card__footer"><a href="javascript:void(0);">View All <span class="slds-assistive-text">entity type</span></a></div> -->
            </article>

            {*

             <div class="slds-no-flex">
                       <button class="slds-button slds-button--neutral" onclick="addNewConditionGroup('where_filter_div');" >
                           {'LBL_NEW_GROUP'|@getTranslatedString:$MODULE}
                       </button>
                   </div>
                  <div class="slds-no-flex">
                     <button name="addwhereconditions" id ="addwhereconditions" onClick="return validateCV();" class="slds-button slds-button--neutral">Generate</button>
                  </div>



            <table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
                <tr>
                    <td class="detailedViewHeader" align="left"><b>{'LBL_ADVANCED_FILTER'|@getTranslatedString:$MODULE}</b></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <input type="button" class="crmbutton create small" value="{'LBL_NEW_GROUP'|@getTranslatedString:$MODULE}" onclick="addNewConditionGroup('where_filter_div')" />
                    </td>
                </tr>
            </table>

            <script type="text/javascript">
                addNewConditionGroup('where_filter_div');
            </script>

            {foreach key=GROUP_ID item=GROUP_CRITERIA from=$CRITERIA_GROUPS}
            <script type="text/javascript">
                if(document.getElementById('gpcon{$GROUP_ID}')) document.getElementById('gpcon{$GROUP_ID}').value = '{$GROUP_CRITERIA.condition}';
            </script>
            {/foreach}
            <div id="whereCond" name = "whereCond"></div>
            <div>
                <input type="button" value="Generate"name="addwhereconditions" id ="addwhereconditions" onClick="return validateCV();"/>
            </div>
            *}

        </div>
    </div>
</div>
{literal}
    <style>
        .ui-accordion-header {
            text-align: left;
        }
    </style>
    <script>
        function validateCV() {
            return checkAdvancedFilter();
        }


        function checkAdvancedFilter() {
            var box = new ajaxLoader(document.body, {classOveride: 'blue-loader'});
            var escapedOptions = new Array('account_id', 'contactid', 'contact_id', 'product_id', 'parent_id', 'campaignid', 'potential_id', 'assigned_user_id1', 'quote_id', 'accountname', 'salesorder_id', 'vendor_id', 'time_start', 'time_end', 'lastname');

            var conditionColumns = vt_getElementsByName('tr', "conditionColumn");
            var criteriaConditions = [];
            for (var i = 0; i < conditionColumns.length; i++) {

                var columnRowId = conditionColumns[i].getAttribute("id");
                var columnRowInfo = columnRowId.split("_");
                var columnGroupId = columnRowInfo[1];
                var columnIndex = columnRowInfo[2];

                var columnId = "fcol" + columnIndex;
                var columnObject = getObj(columnId);
                var selectedColumn = trim(columnObject.value);
                var selectedColumnIndex = columnObject.selectedIndex;
                var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;

                var openbacketsid = "openbackets" + columnIndex;
                var openbacketsObject = getObj(openbacketsid);
                var openbackets0Selected = trim(openbacketsObject.value);

                var closeBacketsid = "closeBackets" + columnIndex;
                var closeBacketsObject = getObj(closeBacketsid);
                var closeBacketsSelected = trim(closeBacketsObject.value);


                var comparatorId = "fop" + columnIndex;
                var comparatorObject = getObj(comparatorId);
                var comparatorValue = trim(comparatorObject.value);

                var valueId = "fval" + columnIndex;
                var valueObject = getObj(valueId);
                var specifiedValue = trim(valueObject.value);

                var extValueId = "fval_ext" + columnIndex;
                var extValueObject = getObj(extValueId);
                if (extValueObject) {
                    extendedValue = trim(extValueObject.value);
                }

                var glueConditionId = "fcon" + columnIndex;
                var glueConditionObject = getObj(glueConditionId);
                var glueCondition = '';
                if (glueConditionObject) {
                    glueCondition = trim(glueConditionObject.value);
                }

                // If only the default row for the condition exists without user selecting any advanced criteria, then skip the validation and return.
                if (conditionColumns.length == 1 && selectedColumn == '' && comparatorValue == '' && specifiedValue == '')
                    return true;

                if (!emptyCheck(columnId, " Column ", "text"))
                    return false;
                if (!emptyCheck(comparatorId, selectedColumnLabel + " Option", "text"))
                    return false;

                var col = selectedColumn.split(":");
                if (escapedOptions.indexOf(col[3]) == -1) {
                    if (col[4] == 'T' || col[4] == 'DT') {
                        var datime = specifiedValue.split(" ");
                        if (specifiedValue.charAt(0) != "$" && specifiedValue.charAt(specifiedValue.length - 1) != "$") {
                            if (datime.length > 1) {
                                if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH")) {
                                    return false
                                }
                                if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS")) {
                                    return false
                                }
                            } else if (col[0] == 'vtiger_activity' && col[2] == 'date_start') {
                                if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                    return false
                            } else {
                                if (!re_patternValidate(datime[0], selectedColumnLabel + " (Time)", "TIMESECONDS")) {
                                    return false
                                }
                            }
                        }

                        if (extValueObject) {
                            var datime = extendedValue.split(" ");
                            if (extendedValue.charAt(0) != "$" && extendedValue.charAt(extendedValue.length - 1) != "$") {
                                if (datime.length > 1) {
                                    if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH")) {
                                        return false
                                    }
                                    if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS")) {
                                        return false
                                    }
                                } else if (col[0] == 'vtiger_activity' && col[2] == 'date_start') {
                                    if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                        return false
                                } else {
                                    if (!re_patternValidate(datime[0], selectedColumnLabel + " (Time)", "TIMESECONDS")) {
                                        return false
                                    }
                                }
                            }
                        }
                    }
                    else if (col[4] == 'D') {
                        if (specifiedValue.charAt(0) != "$" && specifiedValue.charAt(specifiedValue.length - 1) != "$") {
                            if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                return false
                        }
                        if (extValueObject) {
                            if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                return false
                        }
                    } else if (col[4] == 'I') {
                        if (!intValidate(valueId, selectedColumnLabel + " (Integer Criteria)" + i))
                            return false
                    } else if (col[4] == 'N') {
                        if (!numValidate(valueId, selectedColumnLabel + " (Number) ", "any", true))
                            return false
                    } else if (col[4] == 'E') {
                        if (!patternValidate(valueId, selectedColumnLabel + " (Email Id)", "EMAIL"))
                            return false
                    }
                }

                //Added to handle yes or no for checkbox fields in reports advance filters.
                if (col[4] == "C") {
                    if (specifiedValue == "1")
                        specifiedValue = getObj(valueId).value = 'yes';
                    else if (specifiedValue == "0")
                        specifiedValue = getObj(valueId).value = 'no';
                }
                if (extValueObject && extendedValue != null && extendedValue != '') specifiedValue = specifiedValue + ',' + extendedValue;

                criteriaConditions[columnIndex] = {
                    "groupid": columnGroupId,
                    "columnname": selectedColumn,
                    "comparator": comparatorValue,
                    "value": specifiedValue,
                    "columncondition": glueCondition,
                    "closebackets": closeBacketsSelected,
                    "openbackets": openbackets0Selected
                };
            }
            $('#advft_criteria').val(JSON.stringify(criteriaConditions));

            var conditionGroups = vt_getElementsByName('div', "conditionGroup");
            var criteriaGroups = [];
            for (var i = 0; i < conditionGroups.length; i++) {
                var groupTableId = conditionGroups[i].getAttribute("id");
                var groupTableInfo = groupTableId.split("_");
                var groupIndex = groupTableInfo[1];

                var groupConditionId = "gpcon" + groupIndex;
                var groupConditionObject = getObj(groupConditionId);
                var groupCondition = '';
                if (groupConditionObject) {
                    groupCondition = trim(groupConditionObject.value);
                }
                criteriaGroups[groupIndex] = {"groupcondition": groupCondition};

            }
            $('#advft_criteria_groups').val(JSON.stringify(criteriaGroups));
            var dbname = $("#dbName").val();
            var url = "index.php?module=MapGenerator&action=MapGeneratorAjax&file=generateFilterSql";
            $.ajax({
                type: "POST",
                url: url,
                data: "criteriaGroups=" + JSON.stringify(criteriaGroups) + "&criteriaConditions=" + JSON.stringify(criteriaConditions) + "&dbname=" + dbname,
                dataType: "html",
                success: function (msg) {
                    jQuery("#generatedConditions").html(msg);
                    if (box) box.remove();
                },
                error: function () {
                    alert("{/literal}{$MOD.failedcall}{literal}");
                }
            });
            return true;
        }

        jQuery(function () {
            var accordinPanel = jQuery("#accordion").accordion({
                heightStyle: "content",
                collapsible: true
            });
            jQuery("#addwhereconditions").button();
        });
    </script>
{/literal}
