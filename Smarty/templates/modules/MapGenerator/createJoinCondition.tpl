<script type="text/javascript" src="modules/MapGenerator/bsmSelect/js/jquery.bsmselect.js"></script>
<script type="text/javascript" src="modules/MapGenerator/bsmSelect/js/jquery.bsmselect.sortable.js"></script>
<script type="text/javascript" src="modules/MapGenerator/bsmSelect/js/jquery.bsmselect.compatibility.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/script.js"></script>
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/bsmSelect/css/jquery.bsmselect.css">
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/bsmSelect/examples/example.css">
<link rel="stylesheet" type="text/css" href="kendoui/styles/kendo.common.min.css">
<link rel="stylesheet" href="http://icono-49d6.kxcdn.com/icono.min.css">


{*<link type="text/css" href="include/LD/assets/styles/salesforce-lightning-design-system.css" rel="stylesheet"/>*}
{*<link type="text/css" href="modules/MapGenerator/styles/salesforce-lightning-design-system.css" rel="stylesheet"/>*}

<div id="LoadingImage" style="display: none">
	<img src=""/>
</div>

{if $PopupJS neq ''}
	<script type="text/javascript">
		
		{foreach from=$PopupJS item=item key=key name=name}
			
			addINJSON(
				'{$item.FirstModuleJSONtext}',
				'{$item.FirstModuleJSONvalue}',
				'{$item.FirstModuleJSONfield}',
				'{$item.SecondModuleJSONtext}',
				'{$item.SecondModuleJSONvalue}',
				`{$item.SecondModuleJSONfield}`,
				`{$item.Labels}`,
				`{$item.ValuesParagraf}`,
				'{$item.returnvaluesval}',
				'',
				'{$item.returnvaluestetx}'
			);

		{/foreach}

		  var check=false;
			var length_history=JSONForCOndition.length;
			//alert(length_history-1);
			for (var ii = 0; ii <= JSONForCOndition.length-1; ii++) {
				var idd =ii;// JSONForCOndition[ii].idJSON;
				var firmod = JSONForCOndition[ii].FirstModuleJSONtext;
				var secmod = JSONForCOndition[ii].SecondModuleJSONtext;
				var selectedfields = JSONForCOndition[ii].ValuesParagraf;
				
				// console.log(idd+firmod+secmod);
				// console.log(selectedfields);
				if (ii==(length_history-1))
				{
					check=true;
					$('#KippID').val(ii);

				}
				else{
				   check=false;
				}
				var alerstdiv = alertsdiv(idd, firmod, secmod,check);
				$('#AlertsAddDiv').append(alerstdiv);

				// generateJoin();
				// emptycombo();
			}
			App.utils.UpdateMapNAme();
	</script>


{/if}

<table class="slds-table slds-no-row-hover slds-table-moz ng-scope" style="border-collapse:separate; border-spacing: 1rem;">
	<tbody>
		<tr class="blockStyleCss" id="DivObjectID">
			<td class="detailViewContainer" valign="top">
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media--center slds-has-flexi-truncate">
								<h1 id="mapNameLabel" class="slds-page-header__title slds-m-right--small slds-truncate">
									{if $NameOFMap neq ''} {$NameOFMap} {/if}
								</h1>
								<p class="slds-text-heading--label slds-line-height--reset">{$MOD.ConditionQuery}</p>
							</header>
							<div id="contentJoinButtons" class="slds-no-flex">
								<div class="actionsContainer mapButton">
									<div class="slds-section-title--divider" >
										<button class="slds-button slds-button--small slds-button--neutral" aria-describedby="entity-header" id="addJoin"   name="radio" onclick="showform(this);">{$MOD.AddJoin}</button>
										<button class="slds-button slds-button--small slds-button--neutral" aria-describedby="entity-header" id="saveasmap" name="radio" onclick="App.UniversalPopup.OpeModalsaveAsMap()">{$MOD.SaveAsMap} </button>
										<button class="slds-button slds-button--small slds-button--brand"   aria-describedby="entity-header"  id="createmap" name="radio" onclick="SaveMap();">{$MOD.CreateMap}</button>
									</div>
									<div class="mailClient mailClientBg addJoinPopup" id="userorgroup" name="userorgroup">
										<table class="slds-table slds-no-row-hover">
											<tr class="slds-line-height--reset">
												<td class="dvtCellLabel">
													<b>{$MOD.addjoin}</b>:
												</td>
												<td class="dvtCellInfo">
													<select name="usergroup" id="usergroup" class="slds-select">
														<option value="none">None</option>
														<option value="user">User</option>
														<option value="group">Group</option>
													</select>
												</td>
											</tr>
											<tr class="slds-line-height--reset">
												<td class="dvtCellLabel">
													<b>{$MOD.addCF}</b>:
												</td>
												<td class="dvtCellInfo">
													<select class="slds-select" name="CFtables" id="cf">
														<option value="none">None</option>
														<option value="cf">CF</option>
													</select>
												</td>
											</tr>
											<tr class="slds-line-height--reset">
												<td colspan="2" class="dvtCellInfo" style="text-align: center;">
													<input class="slds-button slds-button--small slds-button_success" type="button" name="okbutton" id="okbutton" value="OK" onclick="addjouin();">
													<input class="slds-button slds-button--small slds-button--destructive" type="button" name="cancelbutton" id="cancelbutton" value="Cancel" onclick="hidediv('userorgroup');">
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</article>
				</div>

				<div class="slds-truncate">
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
						<tr class="slds-line-height--reset">
							<td class="dvtCellLabel" id="conditionQuery" width="70%" valign="top">
								<div id="selJoin">
									<div id="sel1">
										<div class="slds-form-element">
											<div class="slds-form-element__control slds-text-align--center">
												<label class="slds-form-element__label" for="input-id-01">{$MOD.TargetModule}</label>
												<div class="slds-select_container">
													{*{if !empty($FirstSecModule)}*}
													{*<select id="mod" name="mod" class="slds-select">*}
													{*{foreach from=$FirstSecModule item=v}*}
													{*<option value="{$v['FmoduleName']}">{$v['FmoduleName']}</option>*}
													{*{/foreach}*}
													{*</select>*}
													{*{else}*}
													<select data-select-autolod="true" data-select-method="GetFirstModuleCombo" id="FirstModul" name="mod" class="slds-select">
														{$FirstModuleSelected}
													</select>
													{*{/if}*}
												</div>
											</div>
										</div>
										{*
										<select class="sel" id="mod" name="mod"></select>
										<select class="sel" id="selTab1" name="selTab1" onchange="updateSel('selTab1','selField1')">*} {*
											<option selected="selected" disabled="disabled">Selezionare la prima tabella :</option>
										</select>*} {*{if !empty($FirstSecModule)}*} {*{foreach from=$FirstSecModule item=v}*} {*
										<input type="button" value="{$v['FmoduleID']}" class="slds-button slds-button--neutral sel" *} {*id="selField1" name="selField1" style="padding:0px;">*} {*{/foreach}*} {*{else}*}
										<input type="button" class="slds-button slds-button--neutral sel" id="selField1" value="{$FmoduleID}" name="selField1" style="padding:0px; width: 100%;"> {*{/if}*}
									</div>

									<div id="centerJoin" class="slds-text-align--center">
										<span class="slds-form-element__label">=</span>
									</div>

									<div id="sel2">
										<div class="slds-form-element">
											<div class="slds-form-element__control">
												<center>
													<label class="slds-form-element__label" for="input-id-01">{$MOD.OriginModule}</label>
												</center>
												<div class="slds-select_container">
													{*{if !empty($FirstSecModule)}*} {*
													<select id="secmodule" name="secmodule" class="slds-select">*} {*{foreach from=$FirstSecModule item=v}*} {*
														<option value="{$v['SecmoduleName']}">{$v['SecmoduleName']}</option>*} {*{/foreach}*} {*
													</select>*} {*{else}*}
													<select id="secmodule" data-select-autolod="true" data-select-method="GetSecondModuleCombo" name="secmodule" class="slds-select">
														{$SecondModulerelation}
													</select>
													{*{/if}*}
												</div>
											</div>
										</div>
										{*
										<select class="sel" id="secmodule" name="secmodule">
											<select class="sel" id="selTab2" name="selTab2" onchange="updateSel('selTab2','selField2')">
												<option selected="selected" disabled="disabled">{$MOD.SelectSModule}</option>
											</select>
												*}
											{*{if !empty($FirstSecModule)}*} {*{foreach from=$FirstSecModule item=v}*} {*
											<input type="button" value="{$v['SecmoduleID']}" class="slds-button slds-button--neutral sel" *} {*id="selField2" name="selField2" style="padding:0px;">*} {*{/foreach}*} {*{else}*}
											<input type="button" class="slds-button slds-button--neutral sel" id="selField2" value="{$SmoduleID}" name="selField2" style="padding:0px; width: 100%;"> {*{/if}*}
									</div>
								</div>
								<hr class="line-sep">
								<div id="contenitoreJoin">
									<div id="sectionField">
										<!-- <div class="testoDiv">
											<center><b>{$MOD.SelectField}</b></center>
										</div> -->
										<div class="slds-form-element slds-text-align--center">
											<label class="slds-form-element__label" for="select-01">
												<b>{$MOD.selectlabel}</b>
												<b>{$MOD.selectdoubleclick}</b>
											</label>
											<div class="slds-form-element__control">
												{*{if !empty($Fields)}*}
												{*<select id="selectableFields" multiple="multiple" name="selectableFields[]">*}
												{*<optgroup label="{$MOD.OptionsText}">*}
												{*{foreach from=$Fields item=v}*}
												{*<option value="{$v['fieldname']}">{$v['fieldname']}</option>*}
												{*{/foreach}*}
												{*</optgroup>*}
												{*</select>*}
												{*{else}*}

												<select id="selectableFields" class="slds-select" ondblclick="doubleclickvalue(this)" multiple="multiple" name="selectableFields[]">
													{$FieldsArrayall}
												</select>
												<input type="text" minlength="5" id="ReturnValuesTxt" value="{$ReturnFieldsText}" name="{$ReturnFieldsValue}" class="slds-input" placeholder="Double click to choose a value">

												{*<button style="margin-top:-250px;" class="slds-button slds-button--icon-border-filled" title="Select ">*}
												{*<svg class="slds-button__icon" aria-hidden="true">*}
												{*<use xlink:href="/assets/icons/standard-sprite/svg/symbols.svg#case"></use>*}
												{*</svg>*}
												{*<span class="slds-assistive-text">select</span>*}
												{*</button>*}
												{*<select id="selectableFields1" style="margin-left: 10px;width: 200px;height: 230px;"*}
												{*multiple="multiple" name="selectableFields1[]">*}
												{*</select>*}
												{*{/if}*}
											</div>

											{*{if !empty($FirstSecModule)}*}
											{*<input type="hidden" name="MapID" value="{$MapID}" id="MapID">*}
											{*{else}*}
												<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
												<input type="hidden" name="queryid" value="{$queryid}" id="queryid">
												<input type="hidden" name="querysequence" id="querysequence" value="">
												<input type="hidden" name="MapName" id="MapName" value="{$MapName}">
												<input type="hidden" name="KeppID" id="KippID" value="{$MapName}">
											{*{/if}*}
										</div>

										{*
											<select id="selectableFields" multiple="multiple"  name="selectableFields[]"></select>
											<ol id="leftValues">
											</ol>
										*}

										{*<div class="allinea" id="center">
											<input type="button" id="btnRight" value="&gt;&gt;" />
											<input type="button" id="btnLeft" value="&lt;&lt;" />
										</div>*}
										{*<div class="allinea" id="right">
											<div class="testoDiv"> Campi selezionati</div>
											<select id="rightValues" size="5" multiple></select>
										</div>*}
									</div>
								</div>
							</td>
							<td class="dvtCellLabel" width="30%" valign="top">
								<div class="flexipageComponent">
									<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
										<div id="condition-query-aside" class="slds-card__body slds-card__body--inner">
											<div id="AlertsAddDiv">
												{*<div class="alerts">*}
												{*<span class="closebtns"*}
												{*onclick="this.parentElement.style.display='none';">&times;</span>*}
												{*<strong>Danger!</strong> Indicates a dangerous or potentially negative action.*}
												{*</div>*}
											</div>
										</div>
										{*End div contenitorejoin*}
									</article>
								</div>
							</td>
						</tr>
					</table>
				</div>




<!--     <div class="slds-grid slds-grid--vertical slds-navigation-list--vertical"
		 style="float:left; overflow: hidden;width:20%" id="buttons">
		<ul id="LDSstyle">
			<li><a href="javascript:void(0);" id="addJoin" name="radio" onclick="showform(this);"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.AddJoin}</a></li>
			<li><a href="javascript:void(0);" id="deleteLast" name="radio" onclick="openalertsJoin();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.DeleteLastJoin}</a></li>
			<li><a href="javascript:void(0);" id="create" name="radio" onclick="creaVista();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.CreateMaterializedView}</a></li>
			<li><a href="javascript:void(0);" id="createscript" name="radio" onclick="generateScript();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.CreateScript}</a></li>
			<li><a href="javascript:void(0);" id="createmap" name="radio" onclick="SaveMap();"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.CreateMap}</a></li>
			<li><a href="javascript:void(0);" onclick="App.UniversalPopup.OpeModalsaveAsMap()" id="saveasmap" name="radio"
				   class="slds-navigation-list--vertical__action slds-text-link--reset"
				   aria-describedby="entity-header">{$MOD.SaveAsMap}</a></li>
		</ul>

	</div> -->
   {*
   <!--  <div style="float:left; overflow: hidden;width:20%" id="buttons" >
		<div id="radio">
		<input type="radio" id="addJoin" name="radio"  onclick="generateJoin();"/>
		<label for="addJoin">{$MOD.AddJoin}</label>
		<input type="radio" id="deleteLast" name="radio"  onclick="deleteLastJoin();"/>
		<label for="deleteLast">{$MOD.DeleteLastJoin}</label>
		<input type="radio" id="delete" name="radio" />
		<label for="delete">{$MOD.DeleteQuery}</label>
		<input type="radio" id="create" name="radio"   onclick="creaVista();"/>
		<label for="create">{$MOD.CreateMaterializedView}</label>
		<input type="radio" id="createscript" name="radio"  onclick="generateScript();"/>
		<label for="createscript">{$MOD.CreateScript}</label>
		<input type="radio" id="createmap" name="radio" onclick="generateMap();"/>
		<label for="createmap">{$MOD.CreateMap}</label>
		</div>
	</div> -->
	*}

	<div id="generatedquery">
		<div id="results">
			{if $QUERY neq '' and $valueli neq ''}
				<script src="modules/MapGenerator/js/json.js" type="text/javascript" charset="utf-8"></script>
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

						openbackets.innerHTML = '<div ><select name="openbackets' + columnIndex + '" id="openbackets' + columnIndex + '" title="Yes if you want to open the backets" class="slds-select" style="width:55px;" onchange="addRequiredElements(' + columnIndex + ');">' +
							'<option value="1">&#123;</option><option value="0">None</option>' +
							'{$FOPTION}' +
							'</select></div>';


						node1 = document.createElement('td');
						node1.setAttribute('class', 'dvtCellLabel');
						node1.setAttribute('width', '25%');
						newNode.appendChild(node1);

						node1.innerHTML = "<div class='slds-select_container'><select name='fcol" + columnIndex + "' id='fcol" + columnIndex + "' onchange='updatefOptions(this, \"fop" + columnIndex + "\");addRequiredElements(" + columnIndex + ");' class=' slds-select '>" +
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
						node2.innerHTML = '<div ><select name="fop' + columnIndex + '" id="fop' + columnIndex + '" class="slds-select" onchange="addRequiredElements(' + columnIndex + ');">' +
							'<option value="">{'LBL_NONE'|@getTranslatedString:$MODULE}</option>' +
							'{$FOPTION}' +
							'</select></div>';

						node3 = document.createElement('td');
						node3.setAttribute('class', 'dvtCellLabel');
						node3.setAttribute('width', '25%');
						newNode.appendChild(node3);
						{if $SOURCE eq 'reports'}
						node3.innerHTML = '<div class='
						slds - form - element__control
						'><input name="fval' + columnIndex + '" id="fval' + columnIndex + '" class="slds-input" placeholder='
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
						node3.innerHTML = '<div style="display:inline-flex;"><input name="fval' + columnIndex + '" id="fval' + columnIndex + '" class="slds-select" type="text" style="width:85%;" value="">' +
							'<input type="image" align="absmiddle" style="cursor: pointer; width: 16px;height: 16px;margin-top: 5px;" onclick="document.getElementById(\'fval' + columnIndex + '\').value=\'\';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/></div>';
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
						node5.innerHTML = 
							'<a onclick="deleteColumnRow(' + groupIndex + ',' + columnIndex + ');" href="javascript:;">' +
							'<img src="themes/images/delete.gif" align="absmiddle" width="16" title="{$MOD.LBL_DELETE}" border="0">' +
							'</a>';

						closeBackets = document.createElement('td');
						closeBackets.setAttribute('class', 'dvtCellLabel');
						closeBackets.setAttribute('width', '40px');
						newNode.appendChild(closeBackets);
						jQuery('#fcol' + columnIndex).selectedIndex = -1;

						closeBackets.innerHTML = '<div ><select name="closeBackets' + columnIndex + '" id="closeBackets' + columnIndex + '" class="slds-select" style="width:55px;" onchange="addRequiredElements(' + columnIndex + ');">' +
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
						console.log(parentNode);
						var newNode = document.createElement('div');
						newNodeId = 'conditiongroup_' + groupIndex;
						newNode.setAttribute('id', newNodeId);
						newNode.setAttribute('name', 'conditionGroup');
						newNode.setAttribute('class', 'condition-groups');

						newNode.innerHTML = "<table class='slds-table slds-no-row-hover' valign='top' id='conditiongrouptable_" + groupIndex + "'>" +
							"<tr class='slds-line-height--reset' id='groupheader_" + groupIndex + "'>" +
							"<td class='dvtCellLabel' colspan='8' align='right'>" +
							"<a href='javascript:void(0);' onclick='deleteGroup(\"" + groupIndex + "\");'><img border=0 src={'close.gif'|@vtiger_imageurl:$THEME} alt='{$MOD.LBL_DELETE_GROUP}' title='{$MOD.LBL_DELETE_GROUP}'/></a>" +
							"</td>" +
							"</tr>" +
							"<tr id='groupfooter_" + groupIndex + "'>" +
							"<td class='dvtCellLabel' colspan='8' align='left'>" +
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
				<div id="accordion" >
				<h3>{$MOD.Query}</h3>
					<div id="joinquery">
						<p id="generatedjoin" style="word-break: break-all;">
							{$QUERY}
						</p>
						<p id="generatedConditions"></p>
					</div>
					<h3>{$MOD.conditions}</h3>
					<div id="queryfilters">
						<div id='where_filter_div' name='where_filter_div'>
							<article class="slds-card">
								<div class="slds-card__header slds-grid">
									<header class="slds-media slds-media--center slds-has-flexi-truncate">
										<div class="slds-media__body">
											<h2 id="where-condition-title">
												<a href="javascript:void(0);" class="slds-card__header-link slds-truncate">
													<span class="slds-text-heading--small">{'LBL_ADVANCED_FILTER'|@getTranslatedString:$MODULE}</span>
												</a>
											</h2>
										</div>
									</header>
									<div class="slds-no-flex">
										<button class="slds-button slds-button--neutral" onclick="addNewConditionGroup('where_filter_div');">{'LBL_NEW_GROUP'|@getTranslatedString:$MODULE}</button>
										&nbsp;
										<button class="slds-button slds-button--neutral" name="addwhereconditions" onClick="return validateCV();">{$MOD.generate}</button>
									</div>
								</div>
							</article>
							<br>
							<!-- Add condition Groups here (javascript) -->

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
			{/if}
		</div>
	</div>


			</td>
		</tr>
	</tbody>
</table>

	<div>
		<div class="slds insert-map-name-modal">

			<div class="slds-modal" aria-hidden="false" role="dialog" id="modal">
				<div class="slds-modal__container">
					<div class="slds-modal__header">
						<button class="slds-button slds-button--icon-inverse slds-modal__close" data-modal-saveas-close="true">
							<svg aria-hidden="true" class="slds-button__icon slds-button__icon--large">
								<use xlink:href="include/LD//assets/icons/action-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{$MOD.close}</span>
						</button>
						<h2 class="slds-text-heading--medium">{$MOD.mapname}</h2>
					</div>
					<div class="slds-modal__content slds-p-around--medium">
						<div class="slds-form-element">
							<label class="slds-form-element__label" for="SaveasMapTextImput">
								{$MOD.required}<b class="slds-text-color--error">*</b>
								<!-- <abbr id="ErrorVAlues" class="slds-required" title="{$MOD.requiredstring}">*</abbr> -->
							</label>
							<input type="text" id="SaveasMapTextImput" required="" class="slds-input" placeholder="{$MOD.mapname}" data-controll="true" data-controll-idlabel="ErrorLabelModal" data-controll-file="MapGenerator,CheckNameOfMap" data-controll-id-relation="SendDataButton" name="nameView">
						</div>
						<label id="ErrorLabelModal" class="slds-form-element__label slds-text-color--error"></label>
					</div>
					<div class="slds-modal__footer">
						<button  class="slds-button slds-button--neutral" onclick="closeModalwithoutcheck();">{$MOD.cancel}</button>
						<button onclick="closeModal();" id="SendDataButton" disabled class="slds-button slds-button--neutral slds-button--brand">{$MOD.save}</button>
					</div>
				</div>
			</div>
			<div class="slds-backdrop" id="backdrop"></div>

			<!-- Button To Open Modal -->
			{*<button class="slds-button slds-button--brand" id="toggleBtn">Open Modal</button>*}
		</div>
	</div>

	
</div>


<div id="null"></div>
<div>
  <div id="queryfrommap"></div>
  <div>
		 <div class="slds">
			 <div class="slds-modal" aria-hidden="false" role="dialog" id="modalrezultquerymodal">
				<div class="slds-modal__container">
					 <div class="slds-modal__header">
						 <button class="slds-button slds-button--icon-inverse slds-modal__close" onclick="closemodalrezultquery()">
							 <svg aria-hidden="true" class="slds-button__icon slds-button__icon--large">
								 <use xlink:href="/assets/icons/action-sprite/svg/symbols.svg#close"></use>
							 </svg>
							 <span class="slds-assistive-text">{$MOD.close}</span>
						 </button>
						 <h2 class="slds-text-heading--medium">The result of query</h2>
					 </div>
					 <div class="slds-modal__content slds-p-around--medium">
						 <div class="slds-scrollable">
							 <!-- <div class="slds-form-element"> -->
								<table class="slds-table slds-table_bordered slds-table_cell-buffer" id="insertintobodyrezult">
								 </table>
							 <!-- </div> -->
						 </div>
					 </div>
					 <div class="slds-modal__footer">
						 <button class="slds-button slds-button--neutral" onclick="closeModalwithoutcheckrezultquery();">{$MOD.cancel}
						 </button>
						 <button onclick="closemodalrezultquery();" class="slds-button slds-button--neutral slds-button--brand">
							 {$MOD.save}
						 </button>
					 </div>
				 </div>
			 </div>
			 <div class="slds-backdrop" id="backdropquery"></div>

			 <!-- Button To Open Modal -->
			 {*<button class="slds-button slds-button--brand" id="toggleBtn">Open Modal</button>*}
		 </div>

	 </div>
{literal}
	<style>

		#LDSstyle {
			border: 1px solid black;
			margin-right: 0px;
			margin-top: 0px;
			width: 100%;
			height: 100%;
		}

		/*@media(width:1024px){*/
		/*#LDSstyle {*/
		/*font-size: 10*/
		/*}*/
		/*}*/

		#LDSstyle li {
			margin: 0px;
			padding: 0px;
		}

		#LDSstyle a:hover {
			background: #c3cede;
			/*margin-right: 2px;*/
		}

		.ajax_loader {
			background: url("modules/MapGenerator/image/spinner_squares_circle.gif") no-repeat center center transparent;
			width: 100%;
			height: 100%;
		}

		.blue-loader .ajax_loader {
			background: url("modules/MapGenerator/image/ajax-loader_blue.gif") no-repeat center center transparent;
		}

		#feedback {
			font-size: 1.4em;
		}

		#selectable .ui-selecting {
			background: #FECA40;
		}

		#selectable .ui-selected {
			background: #F39814;
			color: white;
		}

		#selectable {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 60%;
		}

		#selectable li {
			margin: 3px;
			padding: 0.4em;
			font-size: 1.4em;
			height: 18px;
		}

		/*
		 * The buttonset container needs a width so we can stack them vertically.
		 *
		 */
		#radio {
			width: 85%;
		}

		/*
		 * Make each label stack on top of one another.
		 *
		 */
		.ui-buttonset-vertical label {
			display: block;
		}

		/*
		 * Handle colliding borders. Here, we"re making the bottom border
		 * of every label transparent, except for labels with the
		 * ui-state-active or ui-state-hover class, or if it"s the last label.
		 *
		 */
		.ui-buttonset-vertical label:not(:last-of-type):not(.ui-state-hover):not(.ui-state-active) {
			border-bottom: transparent;
		}

		/*
		 * For lables in the active state, we need to make the top border of the next
		 * label transparent.
		 *
		 */
		.ui-buttonset-vertical label.ui-state-active + input + label {
			border-top: transparent;
		}

		/*
		 * Oddly enough, the above style approach doesn"t work for the
		 * hover state. So we define this class that"s used by our JavaScript
		 * hack.
		 *
		 */
		.ui-buttonset-vertical label.ui-transparent-border-top {
			border-top: transparent;
		}

		select {
			width: 300px;
		}

		.overflow {
			height: 200px;
		}


		.tooltip {
			position: relative;
			display: inline-block;
			border-bottom: 1px dotted black;
		}

		.tooltip .tooltiptext {
			visibility: hidden;
			width: 120px;
			background-color: black;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 5px 0;

			/* Position the tooltip */
			position: absolute;
			z-index: 1;
			top: -5px;
			right: 105%;
		}

		.tooltip:hover .tooltiptext {
			visibility: visible;
		}

	</style>
	<script>

		//        $('#selectableFields').on('change', function() {
		//            var value = $(this).val();
		//            $('#selectableFields1').val(value);
		//        });

		//        $(document).ready(function () {
		//
		//            $("#selectableFields option:selected").click(function (e) {
		//                alert('click');
		//            });
		//
		//        });
	   

	</script>
{/literal}
