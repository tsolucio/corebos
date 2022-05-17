/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

/****
* cbAdvancedFilter
* @author: MajorLabel <info@majorlabel.nl>
* @license MIT
*/
(function cbadvancedfilterModule(factory) {
	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof module != 'undefined' && typeof module.exports != 'undefined') {
		module.exports = factory();
	} else {
		window['cbAdvancedFilter'] = factory();
	}

})(function cbadvancedfilterFactory() {

	/**
	 * @class cbAdvancedFilter
	 * @param {element}: Typically a wrapping element of an advanced filter box
	 */
	function cbAdvancedFilter(el) {
		/* Public attributes */
		this.el         = el;
		this.groups     = [];
		this.condCnt    = 0;
		this.grpCnt     = 1;
		this.conds      = [];
		this.vals       = [];
		this.grpCont    = document.getElementById('cbds-advfilt-groups');
		this.searchForm = _findUp(el, '$FORM');
		this.context    = document.getElementById('cbds-advfilt__context').value;

		/* Startup */
		this.init();

		/* Instance listeners */

		/* Global listeners */
		_on(window, 'click', this.handleClicks, this); // Please don't bind clicks to elements
		_on(window, 'keyup', this.handleKeyUp, this); // Please don't bind keyup to elements
	}

	/*
	* STATIC method: fillFieldTemplate
	* Reportmodals do not provide an array of possible fields
	* to Smarty but expects the list to be populated by JS
	* when needed. We need to provide a method to do this
	* before initializing the advancedfilter block
	*/
	cbAdvancedFilter.fillFieldTemplate = function (blocks) {
		const ul = document.getElementById('cbds-advfilt-template__condition')
			.getElementsByClassName('cbds-advfilt__fieldcombo-list')[0];
		var html = '';

		blocks.forEach((block) => {
			// Block label
			html += `
			<li role="presentation" class="slds-listbox__item">
				<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="presentation">
					<h3 class="slds-text-title_caps" role="presentation">${block.label}</h3>
				</div>
			</li>`;
			// Fields
			if (block.options !== undefined) {
				block.options.forEach((field) => {
					html += `
					<li role="presentation" class="slds-listbox__item" data-value="${field.value}">
						<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
							<span class="slds-media__figure slds-listbox__option-icon"></span>
							<span class="slds-media__body">
								<span class="slds-truncate" title="${field.label}">${field.label}</span>
							</span>
						</div>
					</li>`;
				});
			}
		});
		ul.innerHTML = html;
	};

	cbAdvancedFilter.prototype = {
		constructor: cbAdvancedFilter,

		condClass : 'slds-expression__row',
		valClasses : ['cbds-advfilt-cond__value--validate'],

		/*
		* Method: 'init'
		* Initialize the block
		*
		*/
		init: function () {
			if (this.hasPreExisting()) {
				this.getPreExisting().forEach((group) => {
					Group.add(this, group);
				});
			} else {
				Group.add(this, undefined);
			}
		},

		/*
		* Method: 'hasPreExisting'
		* Checks if there were pre-existing conditions
		* (only applicable when used in filter or report context)
		*/
		hasPreExisting: function () {
			if (this.context != 'listview') {
				const input = document.getElementById('cbds-advfilt_existing-conditions');
				return input.value == 'null' || input.value == '[]' || input.value == '' ? false : true;
			} else {
				return false;
			}
		},

		/*
		* Method: 'getPreExisting'
		* Checks if there were pre-existing conditions
		* (only applicable when used in filter or report context)
		*/
		getPreExisting: function () {
			const input = document.getElementById('cbds-advfilt_existing-conditions'),
				existing = JSON.parse(input.value),
				groups = [];

			for (var group in existing) {
				groups.push({
					'groupNo': group,
					'conds': this.getCondsFromExisting(existing[group].columns),
					'glue': parseInt(group) > 1 ? existing[parseInt(group) - 1].condition : ''
				});
			}
			return groups;
		},

		/*
		* Method: 'getCondsFromExisting'
		* Get conditions from the existing output of the database
		* (only applicable when used in filter or report context)
		*/
		getCondsFromExisting: function (conds) {
			const toReturn = [];
			conds = this.enforceExistingConditionFormat(conds);
			for (var i = 0; i < conds.length; i++) {
				toReturn.push({
					'glue': i > 0 ? conds[i - 1].column_condition : '',
					'op': conds[i].comparator,
					'value': conds[i].value,
					'field': {
						'typeofdata': Field.getType(conds[i].columnname),
						'value': conds[i].columnname
					}
				});
			}
			return toReturn;
		},

		/*
		* Method: 'enforceExistingConditionFormat'
		* The existing conditions from the database don't
		* come in a uniform format (sometimes array, sometimes
		* a single object). We need to enforce a uniform format
		*
		* @param  : condition array or object
		* @return : array of condition objects
		*/
		enforceExistingConditionFormat: function (conds) {
			if (!Array.isArray(conds)) {
				var tmpConds = conds;
				conds = [];
				for (var cond in tmpConds) {
					conds.push(tmpConds[cond]);
				}
			}
			return conds;
		},

		/*
		* Method: 'handleClicks'
		* Handle clicks for the entire advanced search block
		*
		* @param  : event object
		*/
		handleClicks: function (e) {
			var onClick = e.target.getAttribute('data-onclick');
			switch (onClick) {
			case 'add-condition':
				this.getByButton('groups', e.target).addCond();
				break;
			case 'add-group':
				Group.add(this, undefined);
				break;
			case 'delete-group':
				this.getByButton('groups', e.target).delete();
				break;
			case 'delete-cond':
				this.getByButton('conds', e.target).delete();
				break;
			case 'submit-adv-cond-form':
				this.submit();
				break;
			case 'pick-comparison-field':
				this.openComparisonModal(e.target);
				break;
			case 'clear-cond':
				this.getByButton('conds', e.target).clear();
				break;
			default:
				return false;
			}
		},

		/*
		* Method: 'submit'
		* Submit the advanced search
		*
		*/
		submit: function () {
			if (this.validate()) {
				this.updateHiddenFields();
				document.basicSearch.searchtype.searchlaunched='advance';
				callSearch('Advanced');
			}
		},

		/*
		* Method: 'updateHiddenFields'
		*
		* Updates the hidden input fields with the criteria and
		* groups. Returns boolean to integrate in existing code
		* structure.
		*/
		updateHiddenFields: function () {
			var criteria = this.getCriteria(),
				groups = this.getGroups();
			this.searchForm.advft_criteria.value = JSON.stringify(criteria);
			this.searchForm.advft_criteria_groups.value = JSON.stringify(groups);
			return true;
		},

		/*
		* Method: 'getCriteria'
		* Get the criteria in the format the backend wants it
		*
		* @return : (string)
		*/
		getCriteria: function () {
			var crits = [];
			for (var i = 0; i < this.conds.length; i++) {
				if (this.conds[i].isComplete()) {
					crits.push({
						'groupid'         : this.conds[i].group.no.toString(),
						'columnname'      : this.conds[i].fieldCombo.getVal(),
						'comparator'      : this.conds[i].op.combo.getVal(),
						'value'           : this.conds[i].getVals('string'),
						'columncondition' : (
							this.conds[i + 1] != undefined &&
							this.conds[i + 1].glueCombo != undefined
						) ? this.conds[i + 1].glueCombo.getVal() : ''
					});
				}
			}
			return crits.length == 0 ? '' : crits;
		},

		/*
		* Method: 'getGroups'
		* Get the groups in the format the backend wants it
		*
		* @return : (string)
		*/
		getGroups: function () {
			var groups = [null];
			for (var i = 0; i < this.groups.length; i++) {
				if (this.groups[i].isComplete()) {
					groups.push({'groupcondition': (
						this.groups[i + 1] != undefined &&
						this.groups[i + 1].glueCombo != undefined
					) ? this.groups[i + 1].glueCombo.getVal() : ''
					});
				}
			}
			return groups.length == 0 ? '' : groups;
		},

		/*
		* Method: 'handleKeyUp'
		* Handle keyup for the entire advanced search block
		*
		* @param  : event object
		*/
		handleKeyUp: function (e) {
			if (this.needsValidation(e.target)) {
				this.getValByInput(e.target).validate();
			}
		},

		/*
		* Method: 'getValByInput'
		* Get a value object by its input node
		*
		* @param  : input node
		* @return : value object
		*/
		getValByInput: function (inputNode) {
			for (var i = 0; i < this.vals.length; i++) {
				if (this.vals[i].input.isSameNode(inputNode)) {
					return this.vals[i];
				}
			}
		},

		/*
		* Method: 'needsValidation'
		* Does this element need any validation?
		*
		* @param  : event object
		*/
		needsValidation: function (node) {
			for (var i = 0; i < node.classList.length; i++) {
				if (this.valClasses.indexOf(node.classList[i]) > -1) {
					return true;
				}
			}
		},

		/*
		* Method: 'getByButton'
		* Gets the group or condition object for a certain button
		*
		* @param  : group / condition ('groups' or 'conds') (string)
		* @param  : button node
		* @return : group or condition object
		*/
		getByButton: function (type, node) {
			var elName = type == 'groups' ? 'group' : 'row',
				upNode = _findUp(node, '.slds-expression__' + elName),
				obj    = false,
				me     = this;
			for (var i = 0; i < me[type].length; i++) {
				(function (_i) {
					if (me[type][_i].el.isSameNode(upNode)) {
						obj = me[type][_i];
					}
				})(i);
			}
			return obj;
		},

		/*
			* Method: 'searchFields'
			* Search fields into advanced filter
			*/
		searchFields: function (el) {
			const dataValue = el.dataset.colId.replace('column', 'value');
			const dataValueId = this.el.querySelector(`ul[data-value-id="${dataValue}"]`);
			const li = dataValueId.getElementsByTagName('li');
			for (let i = 0; i < li.length; i++) {
				if (li[i].getElementsByTagName('span').length == 3) {
					const list = li[i].getElementsByTagName('span')[2];
					if (list.innerHTML.toLowerCase().startsWith(el.value.toLowerCase()) && list.innerHTML.toLowerCase().indexOf(el.value.toLowerCase()) > -1) {
						li[i].style.display = '';
					} else {
						li[i].style.display = 'none';
					}
				} else {
					li[i].style.display = 'none';
					if (el.value == '') {
						li[i].style.display = '';
					}
				}
			}
		},

		/*
			* Method: 'validate'
			* Validate ALL the values in the block
			*
			* @return : (bool)
			*/
		validate: function () {
			if (this.vals.length == 0) {
				return false;
			}
			var pass = true;
			var i = 0;
			while (i < this.vals.length && pass) {
				if (this.vals[i].active) {
					pass = this.vals[i].validate();
				}
				i++;
			}
			return pass;
		},

		/*
			* Method: 'updateGroupNos'
			* Updates the group numbers for all groups
			*
			* @param : method to obtain group no. (string)
			*/
		updateGroupNos: function (method) {
			for (var i = this.groups.length - 1; i >= 0; i--) {
				this.groups[i].setNo(this.groups[i].getNoFrom(method));
			}
		},

		/*
		* method: countConds
		* Get the number of conditions in the entire block
		*
		* @param : method of retrieving the condition count
		*          - screen : gets the cond count. from the qty of conditions in the advanced filter block on screen
		*          - self   : get the cond count from the current instance
		*/
		countConds: function (method) {
			var count;
			switch (method) {
			case 'screen':
				count = this.el.getElementsByClassName(this.condClass).length - 1; // -1 to exclude the template
				break;
			case 'self':
				count = this.condCnt;
				break;
			}
			return parseInt(count);
		},

		/*
		* method: openComparisonModal
		* In reports we can open a special modal that lets you select
		* fields to compare with. This function provides that
		*/
		openComparisonModal: function (button) {
			var content = `
			<div class="slds-combobox_container" id="cbds-advfilt__fieldcomp-combo">
				<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click cbds-advfilt-cond__field" aria-expanded="false" aria-haspopup="listbox" role="combobox">
					<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
						<input class="slds-input slds-combobox__input slds-combobox__input-value" autocomplete="off" role="textbox" type="text"
						placeholder="" readonly="" value="${cbAdvancedFilter.comparisonFields[0].label}" data-valueholder="nextsibling" />
						<input type="hidden" value="${cbAdvancedFilter.comparisonFields[0].value}" />
						<span class="slds-icon_container slds-icon-utility-down slds-input__icon slds-input__icon_right">
							<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
							</svg>
						</span>
					</div>
					<div class="slds-dropdown slds-dropdown_length-5 slds-dropdown_fluid" role="listbox">
						<ul class="slds-listbox slds-listbox_vertical" role="group">`;
			cbAdvancedFilter.comparisonFields.forEach((field) => {
				content += `
					<li role="presentation" class="slds-listbox__item" data-value="${field.value}" data-selected="false">
						<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
							<span class="slds-media__figure slds-listbox__option-icon">
								<span class="slds-icon_container slds-icon-utility-check slds-current-color slds-hide">
									<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
									</svg>
								</span>
							</span>
							<span class="slds-media__body">
								<span class="slds-truncate" title="${field.label}">${field.label}</span>
							</span>
						</div>
					</li>`;
			});
			content += `
						</ul>
					</div>
				</div>
			</div>
			<div style="height: 13rem;"></div>`;

			ldsModal.show('Select field', content, 'medium', 'AdvancedFilter.onComparisonModalClose()');
			this.comparisonModalCombo = new ldsCombobox(document.getElementById('cbds-advfilt__fieldcomp-combo'), {'isMulti': true});

			this.currentComparisonInput = function () {
				let valueCol = _findUp(button, '.' + this.vals[0].class);
				return valueCol.getElementsByClassName(this.vals[0].inputClass)[0];
			};
		},

		/*
		* method: onComparisonModalClose
		* Callback provided to the field comparison modal
		* (only in reports modal). When the modal closes, the
		* selected values in the combo should be written to
		* the value field, comma separated
		*/
		onComparisonModalClose: function () {
			let stringValues = AdvancedFilter.comparisonModalCombo._val.toString();
			this.currentComparisonInput().value = stringValues;
			ldsModal.close();
		}
	};

	/* ==== Submodules ==== */

	/* Group submodule */
	function Group(node, advfilt, existing) {
		this.parent    = advfilt;
		this.el        = node;
		this.no        = null;
		this.condWrap  = this.el.getElementsByClassName('cbds-advfilt-condwrapper')[0];
		this.condCount = null;
		this.glueCombo = null;
		this.preExist  = existing;
		this.dataVal = null;
		this.dataCols = null;
	}

	/* Group static methods */

	/*
	* method: add
	* Adds a new group to the block
	*
	* @param : parent block object
	* @param : Pre-existing group or undefined if there was no pre-existing
	*/
	Group.add = function (parent, existing) {
		var grpTempl = document.getElementById('cbds-advfilt-template__group').children[0],
			newGroup = grpTempl.cloneNode(true),
			grp      = new Group(newGroup, parent, existing);

		grp.parent.groups.push(grp);
		grp.insert();
		grp.init();

		if (existing !== undefined && grp.glueCombo !== null) {
			grp.glueCombo.setByVal(grp.preExist.glue);
		}
	};

	Group.prototype = {
		constructor: Group,

		class : 'slds-expression__group',
		controlClass : 'cbds-advfilt-group__controls',
		condClass : 'slds-expression__row',
		glueClass : 'cbds-advfilt-group__gluecombo',

		/*
		* method: getNoFrom
		* Gets the group no.
		*
		* @param : How to get the group no. (int)
		*          - data   : gets the no from the data attribute
		*          - screen : gets the no. from the qty of groups in the block
		*          - self   : get the group no. from the current instance
		*/
		getNoFrom: function (method) {
			var no = false;
			switch (method) {
			case 'data':
				no = this.el.getAttribute('data-group-no');
				break;
			case 'screen':
				for (var i = 0; i < this.parent.groups.length; i++) {
					if (this.parent.groups[i].el.isSameNode(this.el)) {
						no = i + 1;
					}
				}
				break;
			case 'self':
				no = this.no;
				break;
			}
			return parseInt(no);
		},

		/*
		* method: setNo
		* Sets the group no. both in the data attribute as the instance
		*
		* @param : (int)
		*/
		setNo: function (no) {
			this.el.setAttribute('data-group-no', no);
			this.no = no;
		},

		/*
		* method: setCondCount
		* Sets the no. of conditions both in the data attribute as the instance
		*
		* @param : (int)
		*/
		setCondCount: function (no) {
			this.dataCols = this.el.querySelectorAll('input[data-cols="search"]');
			this.dataVal = this.el.querySelectorAll('ul[data-col-value="search-value"]');
			this.el.setAttribute('data-condcount', no);
			this.condCount = no;
			if (this.dataCols[no-1]) {
				this.dataCols[no-1].setAttribute('data-col-id', `column__${no}_${this.el.getAttribute('data-group-no')}`);
			}
			if (this.dataVal[no-1]) {
				this.dataVal[no-1].setAttribute('data-value-id', `value__${no}_${this.el.getAttribute('data-group-no')}`);
			}
		},

		/*
		* method: insert
		* Adds a new group noe into the block node
		*
		*/
		insert: function () {
			this.parent.grpCont.appendChild(this.el);
		},

		/*
		* method: init
		* Initializes the group
		*
		*/
		init: function () {
			if (!this.isFirst()) {
				this.setCap('controls', true);
				this.glueCombo = new ldsCombobox(this.el.getElementsByClassName(this.glueClass)[0], {'onSelect' : false});
			}
			this.setNo(this.getNoFrom('screen'));
			if (this.preExist !== undefined) {
				this.preExist.conds.forEach((existingCond) => {
					let newCond = this.addCond();
					newCond.fillWithPreExist(existingCond);
				});
			} else {
				this.addCond();
			}
		},

		/*
		* method: setCap
		* Sets a capability state
		*
		* @param : capability name (string)
		* @param : state (bool)
		*/
		setCap: function (cap, state) {
			if (state) {
				switch (cap) {
				case 'controls':
					this.setControls(true);
					break;
				default:
					return true;
				}
			}
		},

		/*
		* method: setControls
		* Enables or disables the controls for the group (close button, glue)
		*
		* @param : enable/disable (bool)
		*/
		setControls: function (state) {
			_sldsShow(this.el.getElementsByClassName(this.controlClass)[0], state);
		},

		/*
		* method: isFirst
		* Is this group the first in the block?
		*
		* @return : bool
		*/
		isFirst: function () {
			return (this.parent.grpCont.getElementsByClassName(this.class)[0].isSameNode(this.el));
		},

		/*
		* method: addCond
		* Adds a new condition to the group
		*
		*/
		addCond: function () {
			var condTempl = document.getElementById('cbds-advfilt-template__condition').children[0],
				newCond   = condTempl.cloneNode(true),
				cond      = new Cond(newCond, this);

			this.insertCond(cond);
			cond.init(); // Initialize the condition AFTER inserting it, otherwise it won't know if it's the first in the group
			return cond;
		},

		/*
		* method: insertCond
		* Inserts a new condition to the group node
		*
		* @param : condition object
		*/
		insertCond: function (cond) {
			this.condWrap.appendChild(cond.el);
		},

		/*
		* method: countConds
		* Get the number of conditions in this group
		*
		* @param : method of retrieving the condition count
		*          - data   : gets the cond count from the data attribute
		*          - screen : gets the cond count. from the qty of conditions in the group on screen
		*          - self   : get the cond count from the current instance
		*/
		countConds: function (method) {
			var count;
			switch (method) {
			case 'data':
				count = this.el.getAttribute('data-condcount');
				break;
			case 'screen':
				count = this.el.getElementsByClassName(this.condClass).length;
				break;
			case 'self':
				count = this.condCount;
				break;
			}
			return parseInt(count);
		},

		/*
		* method: delete
		* Delete a group
		*
		*/
		delete: function () {
			this.deleteConds();
			this.parent.grpCont.removeChild(this.el);
			this.parent.groups.splice(this.parent.groups.indexOf(this), 1);
			this.parent.updateGroupNos('screen');
		},

		/*
		* method: deleteConds
		* Delete all the conditions that belong to this group
		*
		*/
		deleteConds: function () {
			this.getConds().forEach((cond) => {
				cond.delete();
			});
		},

		/*
		* method: getConds
		* Get all the condition objects that
		* belong to this group
		*/
		getConds: function () {
			return this.parent.conds.filter((cond) => {
				return (this.no == cond.group.no);
			});
		},

		/*
		* method: isComplete
		* Is this group completely filled out?
		* Mainly used to prevent empty 'start' conditions
		* to be saved when creating customviews / filters
		* that don't use an advanced filter.
		*
		*/
		isComplete: function () {
			const conds = this.getConds().filter((cond) => {
				return cond.isComplete();
			});
			return conds.length > 0;
		}
	};

	/* Cond submodule */
	function Cond(node, group) {
		this.parent    = group.parent;
		this.el        = node;
		this.group     = group;
		this.no        = null;
		this.delButton = this.el.getElementsByClassName(this.delButtClass)[0];
		this.glueBox   = this.el.getElementsByClassName(this.glueBoxClass)[0];
		this.glueInput = this.el.getElementsByClassName(this.glueInpClass)[0];
		this.fieldBox  = this.el.getElementsByClassName(this.fieldBoxClass)[0];
		this.opWrapper = this.el.getElementsByClassName(this.opsWrapperClass)[0];
		this.glueCombo = null;
		this.fieldCombo= null;
		this.op        = null;
		this.vals      = [];
		this.parent.conds.push(this);
	}

	Cond.prototype = {
		constructor: Cond,

		class : 'slds-expression__row',
		delButtClass : 'cbds-advfilt-cond__delete',
		glueBoxClass : 'cbds-advfilt-cond__glue',
		glueInpClass : 'cbds-advfilt-cond__glue--input',
		fieldBoxClass : 'cbds-advfilt-cond__field',
		opsWrapperClass : 'cbds-advfilt-cond__opswrapper',
		valueClass : 'cbds-advfilt-cond__value',
		valueInputClass : 'cbds-advfilt-cond__value--input',

		/*
		* method: getNoFrom
		* Gets the condition no. from the node's data attribute
		*
		* @param : method of retrieving the condition no
		*          - data   : gets the cond no from the data attribute
		*          - self   : get the cond no from the current instance
		* @return : int
		*/
		getNoFrom: function (method) {
			var no = false;
			switch (method) {
			case 'data':
				no = this.el.getAttribute('data-cond-no');
				break;
			case 'self':
				no = this.no;
				break;
			}
			return parseInt(no);
		},

		/*
		* method: init
		* Initialize the condition
		*
		*/
		init: function () {
			if (!this.isFirst()) {
				this.setCap('delete', true);
				this.setCap('glue', true);
			}
			this.group.setCondCount(this.group.countConds('screen'));
			this.parent.condCnt = this.parent.countConds('screen');
			this.setNo(this.parent.condCnt);

			this.setCap('field', true);
			this.setOps(Field.getType(this.fieldCombo.getVal()));

			this.getVals('node');
			this.setVals();
		},

		/*
		* method: setNo
		* Set the condition no. both in the object as the data attribute
		*
		*/
		setNo: function (no) {
			this.no = no;
			this.el.getAttribute('data-cond-no', no);
		},

		/*
		* method: getVals
		* Get the value nodes and objects for this condition
		*
		* @param : method of retrieving values (string)
		*           - 'node'   : get the value nodes in this condition node
		*           - 'obj'    : get the value objects that have this condition object set
		*           - 'string' : get a string representation of all the values of the condition
		*
		* @return: value objects, only when method is 'obj' (array)
		*
		*/
		getVals: function (method) {
			var values = [];
			var vals = '';
			var i = 0;
			switch (method) {
			case 'node':
				vals = this.el.getElementsByClassName(this.valueClass);
				for (i = 0; i < vals.length; i++) {
					this.parent.vals.push(new Value(this, vals[i]));
				}
				break;
			case 'obj':
				for (i = 0; i < this.parent.vals.length; i++) {
					if (this.parent.vals[i].cond === this) {
						values.push(this.parent.vals[i]);
					}
				}
				return values;
			case 'string':
				vals = this.getVals('obj');
				for (i = 0; i < vals.length; i++) {
					if (vals[i].active) {
						values.push(vals[i].input.value);
					}
				}
				return values.join(',');
			}
		},

		/*
		* method: setVals
		* Updates the value objects and nodes to reflect the current
		* state of the condition. Does things like enable datepicker
		* when needed, show two value input when needed etc.
		*
		*/
		setVals: function () {
			var curVal  = this.fieldCombo.getVal(),
				curType = Field.getType(curVal),
				curOp   = this.op.combo.getVal();

			for (var i = 0; i < this.parent.vals.length; i++) {
				if (this.parent.vals[i].cond === this) {
					this.parent.vals[i].setup(curType, curOp);
				}
			}
		},

		/*
		* method: fillWithPreExist
		* If there was a pre-existing condition (e.g. existing
		* advanced filter in customview/filter or report), fill
		* it with the existing data
		*/
		fillWithPreExist: function (existingCond) {
			if (this.glueCombo !== null) {
				this.glueCombo.setByVal(existingCond.glue);
			}
			this.fieldCombo.setByVal(existingCond.field.value);
			this.op.combo.setByVal(existingCond.op);

			if (this.op.needsTwoVals(existingCond.op)) {
				const values = existingCond.value.split(','),
					condVals = this.getVals('obj');
				condVals[0].input.value = values[0];
				condVals[1].input.value = values[1];
				condVals.forEach((val) => val.validate());
			} else {
				this.getVals('obj')[0].input.value = existingCond.value;
				this.getVals('obj')[0].validate();
			}
		},

		/*
		* method: setCap
		* Set capability with state
		*
		* @param : Capability name (string)
		* @param : state (bool)
		*/
		setCap: function (cap, state) {
			switch (cap) {
			case 'delete':
				this.setDelete(state);
				break;
			case 'glue':
				this.setGlue(state);
				break;
			case 'field':
				this.setField(state);
				break;
			}
		},

		/*
		* method: setDelete
		* Set delete button capability for the condition
		*
		* @param : state (bool)
		*/
		setDelete: function (state) {
			if (state) {
				_sldsEnable(this.delButton, true);
			} else {
				_sldsEnable(this.delButton, false);
			}
		},

		/*
		* method: setGlue
		* Set capability to glue to previous condition
		*
		* @param : state (bool)
		*/
		setGlue: function (state) {
			if (state) {
				_sldsEnable(this.glueInput, true);
				this.glueCombo = new ldsCombobox(this.glueBox, {'onSelect' : false});
				window.ldsComboBoxes.push(this.glueCombo);
			} else {
				_sldsEnable(this.glueInput, false);
			}
		},

		/*
		* method: setField
		* Set capability to select a field
		*
		* @param : state (bool)
		*/
		setField: function (state) {
			if (state) {
				this.fieldCombo = new ldsCombobox(this.fieldBox, {'onSelect' : this.react.bind(this)});
				window.ldsComboBoxes.push(this.fieldCombo);
			}
		},

		/*
		* method: react
		* General method used when anything about the condition changes, either selection
		* of a field or selection of an operation
		*
		* @param : value of the combo
		*/
		react: function (val) {
			var vals;
			switch (this.constructor.name) {
			case 'Cond':
				this.setOps(val);
				this.setVals();
				vals = this.getVals('obj');
				break;
			case 'Operations':
				// 'this' is bound to the operation instance here
				this.onSelect(val);
				this.cond.setVals();
				vals = this.cond.getVals('obj');
				break;
			}
			for (var i = 0; i < vals.length; i++) {
				vals[i].validate();
			}
		},

		/*
		* method: setOps
		* Responds to the field selector, when a selection is made the operations
		* combo should update with the appropriate values for the selected field
		*
		* @param : val (string)
		*/
		setOps: function (val) {
			this.op   = new Operations(this);
			var fieldType = Field.getType(val);
			this.op.getComboBox(fieldType);
			this.replOps();
		},

		/*
		* method: replOps
		* Inserts the operations combo in the wrapper DIV and
		* removes the current content of the operations wrapper
		*
		*/
		replOps: function (val) {
			this.opWrapper.innerHTML = '';
			const el = this.op.combo.el.querySelectorAll('li:first-child,input');
			const childrens = el[2].getElementsByTagName('span');
			el[0].value = childrens[2].innerHTML;
			el[1].value = el[2].dataset.value;
			this.opWrapper.appendChild(this.op.combo.el);
		},

		/*
			* method: isFirst
			* Is this the first condition in the group?
			*
			* @return : bool
			*/
		isFirst: function () {
			return this.el.isSameNode(this.group.el.getElementsByClassName(this.class)[0]);
		},

		/*
		* method: delete
		* Delete this condition
		*
		*/
		delete: function () {
			this.group.condWrap.removeChild(this.el);
			this.parent.conds.splice(this.parent.conds.indexOf(this), 1);
			this.group.setCondCount(this.group.countConds('screen'));
			this.parent.condCnt = this.parent.countConds('screen');
		},

		/*
		* method: isComplete
		* Is this condition completely filled out?
		* Mainly used to prevent empty 'start' conditions
		* to be saved when creating customviews / filters
		* that don't use an advanced filter
		*/
		isComplete: function () {
			return (
				this.fieldCombo.getValueHolder().value != '' &&
				this.op.combo.getValueHolder().value != ''
			);
		},

		/*
		* method: clear
		* Mainly responding to the clear button. Clears
		* both the field and operator visible and hidden
		* inputs and the value input
		*
		*/
		clear: function () {
			this.fieldCombo._val = '';
			this.fieldCombo.input.value = '';
			this.fieldCombo.valueHolder.value = '';

			this.op.combo._val = '';
			this.op.combo.input.value = '';
			this.op.combo.valueHolder.value = '';

			[...this.el.getElementsByClassName(this.valueInputClass)].forEach((valInput) => {
				valInput.value = '';
			});
		}
	};

	/* Operations submodule */
	function Operations(cond) {
		this.cond  = cond,
		this.combo = null;
	}

	Operations.prototype = {
		constructor : Operations,
		theseNeedTwo : ['bw'],

		/*
		* method: getComboBox
		* Get a new operations ComboBox object based on the field type
		*
		* @param  : type of field (typeofdata) (string)
		* @return : ldsCombobox object
		*/
		getComboBox : function (type) {
			var boxNode  = this.getTempl('box'),
				listNode = boxNode.getElementsByClassName('slds-listbox')[0],
				ops      = this.getOps(type),
				box      = null;

			for (var op in ops) {
				listNode.appendChild(this.buildItemNode(op, ops[op]));
			}
			box = new ldsCombobox(boxNode, {'onSelect' : this.cond.react.bind(this)});
			window.ldsComboBoxes.push(box);
			this.combo = box;
		},

		/*
		* method: onSelect
		* onselect method for the operations combo
		*
		* @param  : value returned from the ldsCombobox instance
		*/
		onSelect : function (val) {
		},

		/*
		* method: buildItemNode
		* Build an item node, provided an operations object
		*
		* @param  : operations value
		* @param  : operations label
		* @return : node
		*/
		buildItemNode : function (val, label) {
			var item = this.getTempl('item'),
				span = item.getElementsByClassName('slds-truncate')[0];
			item.setAttribute('data-value', val);
			span.title = span.innerHTML = label;
			return item;
		},

		/*
		* method: getOps
		* Get an array of operations by using the field type
		*
		* @return : node
		*/
		getOps : function (type) {
			var ops = {};
			for (var i = 0; i < typesofdata[type].length; i++) {
				ops[typesofdata[type][i]] = fLabels[typesofdata[type][i]];
			}
			return ops;
		},

		/*
		* method: getTempl
		* Get a new operations template node
		*
		* @param  : type of template (box / item) (string)
		* @return : either combo no (no items inside) of item node
		*/
		getTempl : function (type) {
			var templ   = document.getElementById('cbds-advfilt-template__operation-' + type).children[0],
				newNode = templ.cloneNode(true);
			return newNode;
		},

		/*
		* method: needsTwoVals
		* Does this operation require two values?
		*
		* @param  : type of operator (string)
		* @return : bool
		*/
		needsTwoVals : function (type) {
			return (this.theseNeedTwo.indexOf(type) > -1);
		},
	};

	/* Field submodule */
	function Field() {

	}

	/*
	* Static method: getType
	* Deduces the field type from a value
	*
	* @param : fieldvalue (string)
	* @return: typeofdata (string)
	*/
	Field.getType = function (val) {
		var valArray = val.split(':');
		return valArray[valArray.length - 1];
	};

	/* Value submodule */
	function Value(cond, node) {
		this.cond       = cond;
		this.condNo     = cond.no;
		this.active     = false;
		this.val        = null;
		this.el         = node;
		this.dpActive   = false;
		this.input      = this.el.getElementsByClassName(this.inputClass)[0];
		this.dateButt   = this.el.getElementsByClassName(this.dateButtClass)[0];
		this.hasError   = false;

		// Will be used by the Calendar date/time picker
		this.input.onchange = this.validate.bind(this);
	}

	Value.prototype = {
		constructor : Value,

		inputClass : 'cbds-advfilt-cond__value--input',
		dateButtClass : 'cbds-advfilt-cond__value--datebutt',
		class: 'cbds-advfilt-cond__value',

		/*
		* method : setup
		* Setup the value according to its requirements
		*
		* @param : current field type of the condition (string)
		* @param : current operation type of the condition (string)
		*/
		setup : function (fieldType, opType) {
			switch (fieldType) {
			case 'D':
				this.setCap('datepick', true);
				break;
			case 'DT':
				this.setCap('datetimepick', true);
				break;
			default:
				this.setCap('datepick', false);
				break;
			}

			this.active = this.getSeq() == 1 ? true : false; // Always set the first value active

			var needsTwo = this.cond.op.needsTwoVals(opType);
			if (needsTwo && (this.getSeq() == 2)) {
				_sldsShow(this.el, true);
				this.active = true;
			} else if (!needsTwo && (this.getSeq() == 2)) {
				_sldsShow(this.el, false);
				this.active = false;
			}
		},

		/*
		* method : getSeq
		* Get the sequence of the value box in respect to its
		* parent condition
		*
		* @return : (int)
		*/
		getSeq : function () {
			var seq  = false,
				vals = this.cond.getVals('obj');

			for (var i = 0; i < vals.length; i++) {
				if (this.el.isSameNode(vals[i].el)) {
					seq = i + 1;
				}
			}
			return parseInt(seq);
		},

		/*
		* method : setCap
		* enable or diable a capability for this value
		*
		* @param : capability name (string)
		* @param : capability state (bool)
		*/
		setCap : function (name, state) {
			switch (name) {
			case 'datepick':
				this.setDTPick('date', state);
				break;
			case 'datetimepick':
				this.setDTPick('datetime', state);
				break;
			}
		},

		/*
		* method : setDTPick
		* set the state and type of the date / datetimepicker for this value
		*
		* @param : type of picker (date/datetime) (string)
		* @param : state (bool)
		*/
		setDTPick : function (name, state) {
			var needsTime  = name == 'datetime' ? true : false,
				dateFormat = this.getDateFormat(needsTime),
				timeFormat = window.userHourFormat != '24' ? window.userHourFormat.substring(0, 2) : '24';

			Calendar.setup ({
				inputField : this.input,
				ifFormat : dateFormat,
				showsTime : needsTime,
				button : this.dateButt,
				singleClick : true,
				step : 1,
				timeFormat: timeFormat
			});

			_sldsEnable(this.dateButt, state);
		},

		/*
		* method: getDateFormat
		* Gets a correctly formatted date string for the datepicker
		* based on the current user settings
		*
		* @param : include the time format in the string? (bool)
		*/
		getDateFormat: function (needsTime) {
			var dateFormat;
			switch (window.userDateFormat) {
			case 'yyyy-mm-dd':
				dateFormat = '%Y-%m-%d';
				break;
			case 'dd-mm-yyyy':
				dateFormat = '%d-%m-%Y';
				break;
			case 'mm-dd-yyyy':
				dateFormat = '%m-%d-%Y';
				break;
			default:
				dateFormat = '%d-%m-%Y';
			}
			if (needsTime) {
				dateFormat += ' %H:%M';
			}
			return dateFormat;
		},

		/*
		* method: validate
		* Validates the current value in the input box
		* Gets the fieldtype and the current value
		*
		* @return : (bool)
		*/
		validate: function () {
			var fieldType = Field.getType(this.cond.fieldCombo.getVal()),
				curVal    = this.input.value;

			if (!this.doValidate(fieldType) || this.allowedChars(curVal)) {
				return !this.setError(false);
			}
			if (cbVal(fieldType, curVal)) {
				return !this.setError(false);
			} else {
				return !this.setError(true);
			}
		},

		/*
		* method: doValidate
		* Decide to do the validation for a field or no
		*
		* @param : (string)
		* @return: (bool)
		*/
		doValidate: function (fieldtype) {
			const doNotValid = ['E'];
			if (doNotValid.includes(fieldtype)) {
				return false;
			}
			return true;
		},

		/*
		* method: allowedChars
		* Allowed chars to search in advanced search
		*
		* @param : (string)
		* @return: (bool)
		*/
		allowedChars: function (char) {
			const chars = ['$'];
			if (chars.includes(char)) {
				return true;
			}
			return false;
		},

		/*
		* method: setError
		* Sets the error state of the current value
		*
		* @param : (bool)
		* @return: (bool)
		*/
		setError: function (state) {
			if (state) {
				this.input.classList.add('slds-has-error');
				this.hasError = state;
			} else {
				this.input.classList.remove('slds-has-error');
				this.hasError = state;
			}
			return state;
		}
	};

	/**
	* Section with factory tools
	*/
	function _on(el, type, func, context) {
		el.addEventListener(type, func.bind(context));
	}

	function _findUp(element, searchterm) {
		return findUp(element, searchterm);
	}

	function _sldsShow(el, state) {
		if (state) {
			el.classList.add('slds-show');
			el.classList.remove('slds-hide');
		} else {
			el.classList.add('slds-hide');
			el.classList.remove('slds-show');
		}
	}

	function _sldsEnable(el, state) {
		if (state) {
			el.removeAttribute('disabled');
		} else {
			el.setAttribute('disabled', 'disabled');
		}
	}

	var typesofdata = new Array();
	typesofdata['V'] = ['e', 'n', 's', 'ew', 'dnsw', 'dnew', 'c', 'k', 'rgxp', 'sx'];
	typesofdata['N'] = ['e', 'n', 'l', 'g', 'm', 'h'];
	typesofdata['T'] = ['e', 'n', 'l', 'g', 'm', 'h', 'bw', 'b', 'a'];
	typesofdata['I'] = ['e', 'n', 'l', 'g', 'm', 'h'];
	typesofdata['C'] = ['e', 'n'];
	typesofdata['D'] = ['e', 'n', 'l', 'g', 'm', 'h', 'bw', 'b', 'a'];
	typesofdata['DT'] = ['e', 'n', 'l', 'g', 'm', 'h', 'bw', 'b', 'a'];
	typesofdata['NN'] = ['e', 'n', 'l', 'g', 'm', 'h'];
	typesofdata['E'] = ['e', 'n', 's', 'ew', 'dnsw', 'dnew', 'c', 'k', 'rgxp', 'sx'];

	var fLabels = new Array();
	fLabels['e'] = alert_arr.EQUALS;
	fLabels['n'] = alert_arr.NOT_EQUALS_TO;
	fLabels['s'] = alert_arr.STARTS_WITH;
	fLabels['ew'] = alert_arr.ENDS_WITH;
	fLabels['dnsw'] = alert_arr.DOES_NOT_START_WITH;
	fLabels['dnew'] = alert_arr.DOES_NOT_END_WITH;
	fLabels['c'] = alert_arr.CONTAINS;
	fLabels['k'] = alert_arr.DOES_NOT_CONTAINS;
	fLabels['l'] = alert_arr.LESS_THAN;
	fLabels['g'] = alert_arr.GREATER_THAN;
	fLabels['m'] = alert_arr.LESS_OR_EQUALS;
	fLabels['h'] = alert_arr.GREATER_OR_EQUALS;
	fLabels['bw'] = alert_arr.BETWEEN;
	fLabels['b'] = alert_arr.BEFORE;
	fLabels['a'] = alert_arr.AFTER;
	fLabels['rgxp'] = alert_arr.REGEXP;
	fLabels['sx'] = alert_arr.SOUNDEX;

	/*
	* Globals
	*/

	return cbAdvancedFilter;
});
