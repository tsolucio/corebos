<style>
.bld-visible {
	visibility: visible;
	opacity: 1;
	transition: opacity 2s linear;
}
.bld-hidden {
	visibility: hidden;
	opacity: 0;
	transition: visibility 0s 2s, opacity 2s linear;
}
.bldcontainer-visible {
	max-height: 350px;
	transition: max-height 4.25s ease-in;
}
.bldcontainer-hidden {
	max-height: 0px;
	transition: max-height 2.25s ease-out;
}
</style>
<form name="EditView" action="index.php" method="POST" onsubmit="VtigerJS_DialogBox.block();">
{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=DetailView&module={$MODULE}&record={$ID}">
						<span class="{$MODULEICON.__ICONContainerClass}" title="{$MODULE|@getTranslatedString:$MODULE}">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/{$MODULEICON.__ICONLibrary}-sprite/svg/symbols.svg#{$MODULEICON.__ICONName}" />
							</svg>
							<span class="slds-assistive-text">{$MODULELABEL}</span>
						</span>
					</a>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span>{$MODULELABEL}</span>
								<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
									<a class="hdrLink" href="index.php?action=DetailView&module={$MODULE}&record={$ID}">{'Question Builder'|@getTranslatedString:$MODULE}</a>
								</span>
							</h1>
							<p class="slds-page-header__row slds-page-header__name-meta">
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
			<div class="slds-grid slds-gutters slds-m-around_xxx-small">
				<div class="slds-col">
					<button class="slds-button slds-button_success" type="submit" id='save'>
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
						</svg>
						{$APP.LBL_SAVE_BUTTON_LABEL}
					</button>
					<button class="slds-button slds-button_success" type="submit" id='savenew'>
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
						</svg>
						{$APP.LBL_NEW_BUTTON_TITLE}
					</button>
					<button class="slds-button slds-button_destructive" type="button" onclick="gotourl('{$cancelgo}');">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use>
						</svg>
						{$APP.LBL_CANCEL_BUTTON_LABEL}
					</button>
				</div>
			</div>
		</div>
		<div id="page-header-surplus">
		</div>
	</div>
</div>

<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher slds-card slds-m-around_medium">
<div class="slds-p-around_x-small slds-grid slds-gutters">
	<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
		<legend class="slds-form-element__legend slds-form-element__label">{'qname'|@getTranslatedString:'cbQuestion'}</legend>
		<div class="slds-form-element__control">
			<input id="bqname" required name="bqname" class="slds-input slds-page-header__meta-text" value="{$bqname}" />
		</div>
	</div>
	<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
		<legend class="slds-form-element__legend slds-form-element__label">{'qcollection'|@getTranslatedString:'cbQuestion'}</legend>
		<div class="slds-form-element__control">
			<input id="bqcollection" required name="bqcollection" class="slds-input slds-page-header__meta-text" value="{$bqcollection}" />
		</div>
	</div>
</div>
<div class="slds-p-around_x-small slds-grid slds-gutters">
	<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
		<legend class="slds-form-element__legend slds-form-element__label">{'LBL_MODULE'|@getTranslatedString:'cbMap'}</legend>
		<div class="slds-form-element__control">
			<input id="bqmodule" required name="bqmodule" class="slds-input slds-page-header__meta-text" value="{$targetmodule}" onchange="changecbqModule(this.value);"/>
		</div>
	</div>
	<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
		<legend class="slds-form-element__legend slds-form-element__label">{'LBL_SYSTEMMODULES'|@getTranslatedString:'cbQuestion'}</legend>
		<div class="slds-form-element__control">
			<div class="slds-select_container">
				<select name="msmodules" class="slds-select slds-page-header__meta-text" onchange="document.getElementById('bqmodule').value=this.value;document.getElementById('bqmodule').dispatchEvent(new Event('change'));">
					{foreach item=arr from=$MODULES}
						<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
</div>

<div class="slds-page-header" onclick="toggleBlock('bqfieldgridblock');">
<div class="slds-grid slds-gutters">
<div class="slds-col slds-size_1-of-2">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{'msgt_fields'|@getTranslatedString:'MsgTemplate'}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{'msgt_fields'|@getTranslatedString:'MsgTemplate'}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#merge_field"></use>
						</svg>
						</span>
					</span>
					{'msgt_fields'|@getTranslatedString:'MsgTemplate'}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
	<button class="slds-button slds-button_neutral slds-float_right" type="button" id='addfield_button' onclick="appendEmptyFieldRow(); event.stopPropagation();">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
		</svg>
		{'LBL_ADD_FIELD'|getTranslatedString:'com_vtiger_workflow'}
	</button>
</div>
</div>
</div>
<span id="bqfieldgridblock">
<div class="slds-grid slds-gutters slds-m-top_small slds-m-bottom_x-small">
	<div class="slds-col slds-size_1-of-1 slds-page-header__meta-text slds-m-left_x-small" id="fieldgrid" style="width:99%;"></div>
</div>
</span>

<div class="slds-page-header" onclick="toggleBlock('condsandsql');">
<div class="slds-grid slds-gutters">
<div class="slds-col slds-size_1-of-2">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#rules"></use>
						</svg>
						</span>
					</span>
					{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{'SQL'|@getTranslatedString:'cbQuestion'}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{'SQL'|@getTranslatedString:'cbQuestion'}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#question"></use>
						</svg>
						</span>
					</span>
					{'SQL'|@getTranslatedString:'cbQuestion'}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
</div>
</div>
<span id="condsandsql">
<div class="slds-grid slds-gutters slds-m-top_small">
	<div class="slds-col slds-size_1-of-2 slds-page-header__meta-text">
		<div id="workflow_loading" class="slds-align_absolute-center" style="height:5rem;">
		<b>{'LBL_LOADING'|@getTranslatedString:'com_vtiger_workflow'}</b>
		</div>
		<div id="startwhennoconditions" class="slds-align_absolute-center" style="height:5rem;display:none;">
			<button class="slds-button slds-button_neutral" type="button" id="startwhennoconditionsbutton">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
				</svg>
				{'LBL_NEW_CONDITION_GROUP_BUTTON_LABEL'|@getTranslatedString:'com_vtiger_workflow'}
			</button>
		</div>
		<div id="save_conditions"></div>
		<br>
		{include file="com_vtiger_workflow/FieldExpressions.tpl"}
	</div>
	<div class="slds-col slds-size_1-of-2 slds-page-header__meta-text">
		<div class="slds-grid slds-gutters slds-m-around_xxx-small">
			<div class="slds-col slds-page-header__meta-text">
				<button class="slds-button slds-button_neutral" type="button" onclick="copysql();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#copy"></use>
					</svg>
					{$APP.LBL_COPY_BUTTON}
				</button>
				<button class="slds-button slds-button_neutral" type="button" onclick="testBuilderSQL();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>
					{'Test SQL'|@getTranslatedString:'cbQuestion'}
				</button>
				<div class="slds-form-element" style="display:inline-flex;vertical-align:top;">
					<label class="slds-checkbox_toggle slds-grid" onclick="toggleSQLView();">
						<span class="slds-form-element__label slds-m-bottom_none"></span>
						<input type="checkbox" id="checkboxsqlwsq" aria-describedby="show sql or web service query" />
						<span id="checkbox-toggle-16" class="slds-checkbox_faux_container" aria-live="assertive">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-checkbox_on">SQL</span>
						<span class="slds-checkbox_off">Web Service</span>
						</span>
					</label>
				</div>
			</div>
		</div>
		<div class="slds-grid slds-gutters slds-m-around_xxx-small bldcontainer-hidden" id="cbqmsgdiv">
			<div class="slds-col slds-page-header__meta-text">
				<div class="slds-notify slds-notify_alert slds-theme_info slds-theme_alert-texture bld-visible bld-hidden" role="alert" style="padding:0.1rem;" id="sqlmsgdiv" >
					<h2>
						<svg class="slds-icon slds-icon_small slds-m-right_x-small" aria-hidden="true" id="sqlmsgicon">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
						</svg>
						<span id="sqlmsg">MESSAGE</span>
					</h2>
				</div>
			</div>
		</div>
		<fieldset class="slds-form-element slds-m-around_x-small">
		<textarea id="bqsql" class="slds-textarea" style="display:none;height:280px;">{'Launch a test to get the SQL'|getTranslatedString:'cbQuestion'}</textarea>
		<textarea id="bqwsq" class="slds-textarea" style="height:280px;"></textarea>
		</fieldset>
	</div>
</div>
</span>

<div class="slds-page-header" onclick="toggleBlock('bqoptionsblock');">
<div class="slds-grid slds-gutters">
<div class="slds-col">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{'Options'|@getTranslatedString:'cbQuestion'}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{'Options'|@getTranslatedString:'cbQuestion'}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#answer"></use>
						</svg>
						</span>
					</span>
					{'Options'|@getTranslatedString:'cbQuestion'}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
</div>
</div>
<span id="bqoptionsblock">
<div class="slds-p-around_x-small slds-grid slds-gutters">
	<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
		<div class="slds-form-element__control">
			<div class="slds-checkbox">
			<input type="checkbox" name="sqlquery" id="sqlquery" class="slds-input slds-page-header__meta-text" {if $sqlquery=='1'}checked{/if} />
			<label class="slds-checkbox__label" for="sqlquery">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-form-element__label">{'SQLQuery'|@getTranslatedString:'cbQuestion'}</span>
			</label>
			</div>
			<legend class="slds-form-element__legend slds-form-element__label slds-m-top_medium">{'qpagesize'|@getTranslatedString:'cbQuestion'}</legend>
			<div class="slds-form-element__control">
				<input id="qpagesize" name="qpagesize" type="number" class="slds-input slds-page-header__meta-text" style="width:fit-content;" value="{$qpagesize}" onclick="updateWSSQL();" />
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
		<legend class="slds-form-element__legend slds-form-element__label">{'qtype'|@getTranslatedString:'cbQuestion'}</legend>
		<div class="slds-form-element__control">
			<div class="slds-select_container">
				<select name="qtype" id="qtype" class="slds-select slds-page-header__meta-text">
					{foreach item=arr key=val from=$QTYPES}
						<option value="{$val}" {if $qtype==$val}selected{/if}>{$arr}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<legend class="slds-form-element__legend slds-form-element__label">{'Type Properties'|@getTranslatedString:'cbQuestion'}</legend>
		<div class="slds-form-element__control">
			<textarea id="qprops" class="slds-textarea">{$typeprops}</textarea>
		</div>
	</div>
</div>
</span>

<div class="slds-page-header" onclick="toggleBlock('bqresultsblock');">
<div class="slds-grid slds-gutters">
<div class="slds-col slds-size_1-of-2">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{'RESULTS'|@getTranslatedString:'cbQuestion'}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{'RESULTS'|@getTranslatedString:'cbQuestion'}">
						<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#answer"></use>
						</svg>
						</span>
					</span>
					{'RESULTS'|@getTranslatedString:'cbQuestion'}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
	<button class="slds-button slds-button_neutral slds-float_right" type="button" id='launchsearch_button' onclick="getQuestionResults(); event.stopPropagation();">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
		</svg>
		{$APP.LBL_SEARCH}
	</button>
</div>
</div>
</div>
<span id="bqresultsblock">
<div class="slds-grid slds-gutters slds-m-top_small">
	<div class="slds-col slds-size_4-of-6 slds-page-header__meta-text">
		<div class="slds-col slds-slds-page-header__meta-text slds-m-left_x-small" id="resultsgrid" style="width:99%;"></div>
	</div>
	<div class="slds-col slds-size_2-of-6 slds-page-header__meta-text">
		<div class="slds-p-top_xx-small slds-form-element slds-form-element_horizontal">
			<label class="slds-form-element__label" for="evaluatewith_type">{'Context Module'|@getTranslatedString:'cbQuestion'}</label>
			<span>
				<select name="evaluatewith_type" id="evaluatewith_type" class="slds-select" style="width:70%;">
				{foreach from=$rel1tom item=item}
					<option value="{$item['name']}">{$item['label']}</option>
				{/foreach}
				</select>
			</span>
		</div>
		<div class="slds-p-top_xx-small slds-form-element slds-form-element_horizontal">
			<label class="slds-form-element__label" for="evaluatewith_display">{'Query Context'|@getTranslatedString:'cbQuestion'}</label>
			<span>
				<input id="evaluatewith" name="evaluatewith" type="hidden" value="">
				<input
					id="evaluatewith_display"
					name="evaluatewith_display"
					readonly
					type="text"
					class="slds-input"
					style="width:70%;border:1px solid #bababa;"
					onclick='return vtlib_open_popup_window("", "evaluatewith", "{if $targetmodule=='Workflow'}com_vtiger_workflow{else}{$targetmodule}{/if}", "");'
					value="">&nbsp;
				<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick='return vtlib_open_popup_window("", "evaluatewith", "{$targetmodule}", "");'>
				<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
				</svg>
				</span>
			</span>
		</div>
		<div id="cqanswer" class="slds-m-around_xx-small slds-p-around_xx-small slds-badge_lightest slds-scrollable"></div>
	</div>
</div>
</span>
</section>
</form>
<span id="dump" style="display:none;"></span>
<script src="modules/cbQuestion/resources/mermaid.min.js"></script>
<script src="include/chart.js/Chart.min.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="include/chart.js/Chart.min.css">
<script src="include/chart.js/randomColor.js"></script>
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/cbQuestion/resources/editbuilder.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="modules/com_vtiger_workflow/resources/style.css" type="text/css" />
<script src="modules/cbQuestion/resources/Builder.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	var moduleName = '{$targetmodule}';
	{if isset($cbqconditons)}
	var conditions = {$cbqconditons};
	{else}
	var conditions = null;
	{/if}
	var builderconditions = editbuilderscript(jQuery, conditions);
	document.getElementById('evalid_type').value = (moduleName=='Workflow' ? 'com_vtiger_workflow' : moduleName);
	var fieldData = {$fieldData};
	var arrayOfFields = {$fieldArray};
	var validOperations = {$validOperations};
	var fieldNEcolumn = {$fieldNEcolumn};
	var fieldTableRelation = {$fieldTableRelation};
</script>