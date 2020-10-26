/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/****
* InventoryLine
* @author: MajorLabel <info@majorlabel.nl>
* @license GNU
*/
(function inventorylineModule(factory){

	if (typeof define === "function" && define.amd) {
		define(factory);
	} else if (typeof module != "undefined" && typeof module.exports != "undefined") {
		module.exports = factory();
	} else {
		window["InventoryLine"] = factory();
	}

})(function inventorylineFactory(){

	/**
	 * @class InventoryLine
	 * @param {element}
	 * @param {root} : InventoryBlock instance that is parent
	 */
	function InventoryLine(el, rootObj){
		/* Public properties */
		this.el 		= el,
		this.root		= rootObj,
		this.u 			= rootObj.utils,
		this.extraLine	= this.u.getFirstClass(el, this.root.lineClass + "__extra"),
		this.extraTool 	= _getTool(el, "extra"),
		this.discType 	= 'p',
		this.comboBoxes	= [],
		this.discCombo 	= {},
		this.fields 	= {},
		this.productId	= 0,
		this.crmid		= 0,
		this.divisible	= true;

		/* Private properties */
		var copyTool 	= _getTool(el, "copy"),
			delTool 	= _getTool(el, "delete"),
			comboBoxes	= el.getElementsByClassName("slds-combobox-picklist"),
			inputs 		= el.getElementsByTagName("input"),
			_this 		= this;

		/* Instance Constructor */
		var construct 	= function(me) {
			me.id = me.root.inventoryLines.seq + 1,
			me.root.inventoryLines.seq++,
			me.root.inventoryLines[me.id] = me,
			me.crmid = me.el.getAttribute('data-crmid');
			me.productId = me.el.getAttribute('data-productid');

			if (me.root.editmode == 'EditView') {
				new ProductAutocomplete(me.u.getFirstClass(me.el, "cbds-product-search--hasroot"), me, false, rootObj);
			}

			for (var i = 0; i < comboBoxes.length; i++) {
				var comboBox = new ldsCombobox(comboBoxes[i], {
					'enabled': me.root.editmode == 'EditView'
				});
				comboBox.onSelect = me.handleComboSelects.bind(comboBox, me);
				me.comboBoxes.push(comboBox);
			}

			for (var i = 0; i < inputs.length; i++) {
				var field = new InventoryField(inputs[i], rootObj, {
					"decimals" : 2,
					"decSep" : window.userDecimalSeparator,
					"curSep" : window.userCurrencySeparator
				});
				if (field.getFieldName() !== undefined) {
					me.fields[field.getFieldName()] = field;
				}
			}

			ldsCheckbox.setUnique();
		}
		construct(this);

		/* Instance listeners */
		this.u.on(copyTool, "click", this.copy, this);
		this.u.on(delTool, "click", this.delete, this);
		this.u.on(this.extraTool, "click", this.toggleExtra, this);
		this.u.on(el, "keyup", this.handleInput, this);
	}

	InventoryLine.prototype = {
		constructor: InventoryLine,

		copy: function(){
			var original = this.el,
				newNode = original.cloneNode(true);

			this.u.insertAfter(original, newNode);
			let line = new InventoryLine(newNode, this.root);
			line.crmid = 0;
			this.root.updateCount();
			this.root.updateAggr();
			this.root.updateHiddenDomFields();
		},

		delete: function() {
			let cont = this.root.el.getElementsByClassName(this.root.linesContClass + '__todelete')[0],
				input = _getHiddenInputForField(this.crmid, this.crmid, 'deletelines');
			cont.appendChild(input);
			this.el.parentNode.removeChild(this.el);
			this.u.off(this.extraTool, "click", this.toggleExtra);
			delete this.root.inventoryLines[this.id];

			this.el = null;
			this.root.updateCount();
			this.root.updateAggr();
		},

		toggleExtra: function(e) {
			this.extraLine.classList.toggle(this.root.lineClass + "__extra_expanded");
			this.extraTool.children[0].classList.toggle("cbds-exp-coll-icon_expanded");
			let headingsWrapper = this.el.getElementsByClassName(`${this.root.lineClass}__headingswrapper`)[0];
			headingsWrapper.classList.toggle(`${this.root.lineClass}__headingswrapper_show`);
		},

		expandExtra: function() {
			this.extraLine.classList.add(this.root.lineClass + "__extra_expanded");
			this.extraTool.children[0].classList.add("cbds-exp-coll-icon_expanded");
			let headingsWrapper = this.el.getElementsByClassName(`${this.root.lineClass}__headingswrapper`)[0];
			headingsWrapper.classList.add(`${this.root.lineClass}__headingswrapper_show`);
		},

		collExtra: function() {
			this.extraLine.classList.remove(this.root.lineClass + "__extra_expanded");
			this.extraTool.children[0].classList.remove("cbds-exp-coll-icon_expanded");
			let headingsWrapper = this.el.getElementsByClassName(`${this.root.lineClass}__headingswrapper`)[0];
			headingsWrapper.classList.remove(`${this.root.lineClass}__headingswrapper_show`);
		},

		handleInput: function(e) {
			var input = this.getInputObj(e.target);
			if (input !== undefined) {
				input.format(e);
				var validated = input.validate();

				if (!validated) {
					input.setState("error");
				} else {
					this.calcLine();
				}
			}
		},

		getInputObj: function(node) {
			for (field in this.fields) {
				if (this.fields[field].el.isSameNode(node)) {
					return this.fields[field];
				}
			}
		},

		setDiscType: function(newType) {
			var div = this.u.getFirstClass(this.el, "cbds-inventoryline__symbol--discount_amount")
			div.innerText = newType == 'p' ? '%' : '€';
			this.calcLine();
		},

		setTaxType: function() {
			var type = this.root.taxTypeCombo._val,
				taxCol = this.u.getFirstClass(this.el, this.root.linePrefix + "_taxcol"),
				commentCol = this.u.getFirstClass(this.el, this.root.linePrefix + "--commentcol");

			if (type == "individual") {
				taxCol.classList.remove(this.root.linePrefix + "_taxcol-hidden");
				commentCol.classList.remove("slds-size_6-of-12");
			} else if (type == "group") {
				taxCol.classList.add(this.root.linePrefix + "_taxcol-hidden");
				commentCol.classList.add("slds-size_6-of-12");
			}
		},

		calcLine: function() {
			var validated = this.validate();

			if (validated) {
				this.calcCostPrice();
				this.calcLineGross();
				this.calcDiscount();
				this.calcLineNet();
				this.setTotal();
				this.root.updateAggr();
				this.root.updateHiddenDomFields();
			}
		},

		setTotal: function() {
			this.setField("linetotal", Number(this.fields.extnet.getValue()) + this.calcLineTax());
		},

		validate: function() {
			var validated = true;
			for (field in this.fields) {
				if (!this.fields[field].validate()) {
					validated = false;
					this.fields[field].setState("error");
				} else {
					this.fields[field].setState("normal");
				}
			}
			return validated;
		},

		calcCostPrice: function() {
			if (this.fields.cost_price != undefined && this.fields.cost_gross != undefined) {
				this.setField("cost_gross", this.fields.cost_price.getValue() * this.fields.quantity.getValue());
			}
		},

		calcLineGross: function() {
			this.setField("extgross", this.fields.quantity.getValue() * this.fields.listprice.getValue());
		},

		calcDiscount: function() {
			var gross = this.fields.extgross.getValue(),
				disc = this.fields.discount_amount.getValue(),
				amount = this.discType == "p" ? _getPerc(gross, disc) : disc;

			this.setField("discount_total", amount);
		},

		calcLineNet: function() {
			var gross = this.fields.extgross.getValue(),
				disc = this.fields.discount_total.getValue();

			this.setField("extnet", (gross - disc));
		},

		calcLineTax: function() {
			var totalTax = 0;
			this.getTaxFields().forEach((taxfield) => {
				totalTax = totalTax + this.calcIndivTax(taxfield);
			});
			return this.root.taxTypeCombo._val == "individual" ? Number(totalTax.toFixed(2)) : 0;
		},

		calcIndivTax: function(taxfield) {
			var taxPercent = taxfield.getValue(),
				lineNet = this.fields.extnet.getValue(),
				taxAmount = taxfield.active ? _getPerc(lineNet, taxPercent) : 0;

			this.setField(`sum_${this.getBareTaxName(taxfield)}`, taxAmount);
			return taxAmount;
		},

		getTaxFields: function() {
			var flds = [];
			for (field in this.fields) {
				if (/tax[0-9]{1,2}_perc$/.test(field)) {
					flds.push(this.fields[field])
				}
			}
			return flds;
		},

		getBareTaxName: function(taxfield) {
			return taxfield.getFieldName().replace('_perc', '');
		},

		setField: function(fieldname, newVal) {
			if (this.fields[fieldname] !== undefined) {
				this.fields[fieldname].el.value = newVal;
				this.fireJsInput(fieldname);
			}
		},

		fireJsInput	: function(fieldname) {
			var evt = new CustomEvent("jsInput");
			this.fields[fieldname].el.dispatchEvent(evt);
		},

		getSeq: function() {
			let us = [...this.root.el.getElementsByClassName(this.root.lineClass)],
				seq = 1;
			us.forEach((line) => {
				if (line.isSameNode(this.el)) {
					this.seq = seq;
				}
				seq++;
			})
			return this.seq;
		},

		updateDomContainer: function(cont) {
			let seq = this.getSeq()
				extraFields = {
					'productid': this.productId,
					'crmid': this.crmid,
					'linetax': 0,
					'tax_percent': 0
				};
			for (field in this.fields) {
				if (this.fields[field].active === true) {
					let saveName = this.fields[field].getSaveName(),
						input = _getHiddenInputForField(seq, saveName, 'idlines');
					cont.appendChild(input);
					input.value = this.fields[field].getValue();
				}
			}
			if (this.root.taxTypeCombo._val === 'individual') {
				this.getTaxFields().forEach((taxfield) => {
					let baretaxname = this.getBareTaxName(taxfield),
						input = _getHiddenInputForField(seq, `id_${baretaxname}_perc`, 'idlines'),
						p_val = taxfield.active ? taxfield.getValue() : 0,
						a_val = taxfield.active ? this.fields[`sum_${baretaxname}`].getValue() : 0;

					cont.appendChild(input);
					extraFields.tax_percent += input.value = p_val;
					extraFields.linetax += a_val;
				});
			}
			for (field in extraFields) {
					let input = _getHiddenInputForField(seq, field, 'idlines');
					cont.appendChild(input);
					input.value = extraFields[field];
			}
		},

		actualizeLineTaxes: function(productTaxes) {
			this.getTaxFields().forEach((field) => field.hide());
			productTaxes.forEach((tax) => {
				this.fields[tax.taxname].show();
				this.fields[`sum_${tax.taxname}`].show();
			});
			// Because this is the result of an AJAX call
			// we need to re-calculate the line.
			this.calcLine();
		},

		handleComboSelects: function(lineObj) {
			lineObj.findFieldByCombo(this)._val = this._val;
			if (this.valueHolder.classList.contains(`${lineObj.root.inputPrefix}--discount_type`)) {
				lineObj.discType = this._val;
				lineObj.setDiscType(this._val);
			}
		},

		findFieldByCombo: function(combo) {
			for (field in this.fields) {
				if (this.fields[field].el.isSameNode(combo.input)) {
					return this.fields[field];
				}
			}
			return {};
		}
	}

	/**
		 * Section with factory tools
		 */

	function _getTool(root, sort) {
		var tool = root.getElementsByClassName("cbds-detail-line-" + sort + "tool")[0];
		return tool === undefined ? document.createElement("div") : tool;
	}

	function _deductPerc(base, percentage) {
		return (base * (1 - (percentage / 100)));
	}

	function _getPerc(base, percentage) {
		return base * (percentage / 100);
	}

	function _getHiddenInputForField(seq, fieldName, groupName) {
		let input = document.createElement('INPUT');
		input.name = `${groupName}[${seq}][${fieldName}]`,
		input.type = 'hidden';
		return input;
	}

	/*
	* Export
	*/
	return InventoryLine;
});

/****
* ldsCheckbox
* @author: MajorLabel <info@majorlabel.nl>
* @license GNU
*/
(function ldscheckboxModule(factory){

	if (typeof define === "function" && define.amd) {
		define(factory);
	} else if (typeof module != "undefined" && typeof module.exports != "undefined") {
		module.exports = factory();
	} else {
		window["ldsCheckbox"] = factory();
	}

})(function ldscheckboxFactory(){

	/**
	 * @class ldsCheckbox
	 */
	function ldsCheckbox() {
		/* Public attributes */
	}

	/*
		* Static methods
		*/
	ldsCheckbox.setUnique = function() {
		var ldsCheckboxes = document.getElementsByClassName("slds-checkbox");
		for (var i = 0; i < ldsCheckboxes.length; i++) {
			ldsCheckboxes[i].getElementsByTagName("input")[0].id = "slds-checkbox-" + i,
			ldsCheckboxes[i].getElementsByClassName("slds-checkbox__label")[0].setAttribute("for", "slds-checkbox-" + i);
		}
	}

	ldsCheckbox.prototype = {
		constructor: ldsCheckbox,
	}

	/*
		* Export
		*/
	return ldsCheckbox;

});

/****
* InventoryField
* @author: MajorLabel <info@majorlabel.nl>
* @license GNU
*/
(function inventoryfieldModule(factory){

	if (typeof define === "function" && define.amd) {
		define(factory);
	} else if (typeof module != "undefined" && typeof module.exports != "undefined") {
		module.exports = factory();
	} else {
		window["InventoryField"] = factory();
	}

})(function inventoryfieldFactory(){

	/**
	 * @class InventoryField
	 * @param {element}
	 */
	function InventoryField(el, rootObj, params){
		var defaults 	= {
			"decimals" 	: 2,
			"curSep"	: ".",
			"decSep"	: ","
		};

		this.el 		= el,
		this.root 		= rootObj,
		this.u 			= rootObj.utils,
		this.formEl 	= this.u.findUp(this.el, ".slds-form-element"),
		this.helpCont	= this.formEl != undefined ? this.u.getFirstClass(this.formEl, "slds-form-element__help") : undefined,
		this.errorSet 	= false,
		this.errorMess  = this.el.hasAttribute("data-error-mess") ? this.el.getAttribute("data-error-mess") : "",
		this.type 		= this.getType(),
		this._val 		= _sanitizeNumberString(this.el.value),
		this.specialKeys= [",", ".", "Backspace", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", "Numpad -", "Numpad +"],
		this.decimals 	= params.decimals || defaults.decimals,
		this.decSep 	= params.decSep || defaults.decSep,
		this.curSep 	= params.curSep || defaults.curSep,
		this.curr		= this.curr(this),
		this.active		= true;

		this.u.on(this.el, "jsInput", this.format, this);
	}

	InventoryField.prototype = {
		constructor : InventoryField,

		getType : function() {
			return this.el.hasAttribute("data-type") ? this.el.getAttribute("data-type") : false;
		},

		getFieldName : function(prefix) {
			var classes = this.el.classList,
				prefix = prefix || this.root.inputPrefix;
			for (var i = 0; i < classes.length; i++) {
				if (classes[i].indexOf(prefix) > -1)
					return classes[i].split("--")[1];
			}
		},

		getSaveName: function() {
			if (this.el.hasAttribute('data-savefield') && this.el.getAttribute('data-savefield') != '') {
				return this.el.getAttribute('data-savefield');
			} else {
				return this.getFieldName();
			}
		},

		isReadOnly : function() {
			return this.el.hasAttribute("readonly");
		},

		getData : function(type) {
			return this.el.getAttribute("data-" + type);
		},

		getValue : function() {
			if (this.getType() != "text") {
				return cbNumber.isCurr(this.el.value) ? cbNumber.curToNum(this.el.value) : this.el.value;
			} else {
				return this.el.value;
			}
		},

		update : function(value) {
			var type = this.getType();
			switch (type) {
				case "currency":
					try {
						if (!cbNumber.isFloat(value) && !cbNumber.isInt(value))
							throw "'" + value + "' is not a correct number";
						else
							this._val = value;
							this.el.value = cbNumber.numToCurr(value);
					}
					catch(err) {
						console.log(err);
					}
					break;
			}
		},

		validate : function() {
			if (this.isReadOnly()) return true;
			var type = this.getType();
			switch(type) {
				case "number":
					return this.valNumber();
					break;
				case "currency":
					return cbNumber.isCurr(this.el.value);
					break;
			}
			return true;
		},

		valNumber : function() {
			toCheck = _sanitizeNumberString(this.el.value);
			if (!_isNumber(toCheck)) {
				return false;
			} else {
				var min = this.getData("min") != null ? Number(this.getData("min")) : (0 - Number.MAX_SAFE_INTEGER),
					max = this.getData("max") != null ? Number(this.getData("max")) : Number.MAX_SAFE_INTEGER;
				if (Number(toCheck) >= min && Number(toCheck) <= max)
					return true;
				else
					return false;
			}
		},

		format : function(e) {
			var type = this.getType();
			switch(type) {
				case "currency":
					if (e.type == "jsInput") {
						this.handleCurJsInput();
					} else if (e.type == "keyup" && !this.isReadOnly()) {
						this.handleCurKeyUp(e);
					}
			}
		},

		setState : function(state) {
			switch(state) {
				case "error":
					this.setError();
					break;
				case "normal":
					this.setNormal();
					break;
			}
		},

		setError : function() {
			if (!this.errorSet)
				this.formEl.classList.add("slds-has-error");
				this.setHelp(this.errorMess);
				this.errorSet = true;
		},

		setNormal : function() {
			if (this.errorSet)
				this.formEl.classList.remove("slds-has-error");
				this.setHelp("");
				this.errorSet = false;
		},

		setHelp : function(text) {
			if (this.helpCont != undefined)
				this.helpCont.innerHTML = text;
		},

		handleCurKeyUp : function(e) {
			var key = keyCodeMap[e.keyCode];
			if (this.isSpecialKey(key) && cbNumber.isInt(key)) {
				this.curr.add(key);
			} else if (this.isSpecialKey(key) && !cbNumber.isInt(key)) {
				switch (key) {
					case "Backspace":
						if (this.curr.isLast("."))
							this.curr.remove(2);
						else
							this.curr.remove(1);
						break;
					case ".":
						if (this.curr.charNum(".") < 1)
							this.curr.add(".");
						break;
					case ",":
						if (this.curr.charNum(".") < 1)
							this.curr.add(".");
						break;
					case "-":
						if (this.curr.charNum("-") == 0)
							this._val = "-" + this._val;
						break;
					case "Numpad -":
						if (this.curr.charNum("-") == 0)
							this._val = "-" + this._val;
						break;
					case "+":
						if (this.curr.charNum("-") > 0)
							this._val = this._val.replace(/^-/, "");
						break;
					case "Numpad +":
						if (this.curr.charNum("-") > 0)
							this._val = this._val.replace(/^-/, "");
						break;
				}
			}
			this._val = this._val.replace(/^0*/, "");
			this.el.value = cbNumber.numToCurr(this._val || "0.00");
		},

		handleCurJsInput : function() {
			this._val = this.el.value;
			this.el.value = cbNumber.numToCurr(this._val);
		},

		curr : function(parent) {
			var isLast = function(digit) {
				return parent._val.indexOf(digit) == (parent._val.length-1) ? true : false;
			}
			var add = function(key) {
				parent._val = cbNumber.decimalNum(parent._val) < 2 ? parent._val + key : parent._val;
			}
			var remove = function(n) {
				parent._val = parent._val.substring(0, parent._val.length - n);
			}
			var charNum = function(c) {
				var r = c == "." || c == "," ? new RegExp("\\" + c, "g") : new RegExp(c, "g");
				return (parent._val.match(r) || []).length
			}

			return {
				"isLast" : isLast,
				"add" : add,
				"remove" : remove,
				"charNum" : charNum
			};
		},

		/*
			* Method: 'isSpecialKey'
			* Tests if a key is in the list of special keys for the class.
			* Takes either a keyname from the keycodeMap or a keyCode
			*
			* @param: Keyname OR keyCode
			*/
		isSpecialKey: function(key) {
			return this.specialKeys.indexOf(key) != -1 || this.specialKeys.indexOf(keyCodeMap[key]) != -1 ? true : false;
		},

		show: function() {
			this.active = true;
			this.u.findUp(this.el, '.slds-form-element__row').classList.remove('slds-hide');
			this.u.findUp(this.el, '.slds-panel__section').classList.remove('slds-hide');

		},

		hide: function() {
			this.active = false;
			this.u.findUp(this.el, '.slds-form-element__row').classList.add('slds-hide');
			this.u.findUp(this.el, '.slds-panel__section').classList.add('slds-hide');
		}
	}

	/*
		* Factory tools
		*/
	function _isNumber(val) {
		val = _sanitizeNumberString(val),
		val = val == "" ? "This was an empty string" : val;
		return isNaN(val) ? false : true;
	}

	function _sanitizeNumberString(numberString) {
		numberString = isNaN(numberString) ? numberString : Number(numberString).toFixed(2),
		numberString = numberString.toString(),
		numberString = numberString.replace(/(,)([0-9]{1,2})$/g, ".$2").replace(/(,)([0-9]{3})/g, "$2").replace(/(\.)([0-9]{3})/g, "$2");
		return numberString.replace(/ /g, "").replace(/,/g, "");
	}

	var keyCodeMap = {8:"Backspace",9:"Tab",13:"Enter",16:"Shift",17:"Ctrl",18:"Alt",19:"Pause/Break",20:"Caps Lock",27:"Esc",32:"Space",33:"Page Up",34:"Page Down",35:"End",36:"Home",37:"Left",38:"Up",39:"Right",40:"Down",45:"Insert",46:"Delete",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",61:"+",65:"A",66:"B",67:"C",68:"D",69:"E",70:"F",71:"G",72:"H",73:"I",74:"J",75:"K",76:"L",77:"M",78:"N",79:"O",80:"P",81:"Q",82:"R",83:"S",84:"T",85:"U",86:"V",87:"W",88:"X",89:"Y",90:"Z",91:"Windows",93:"Right Click",96:"Numpad 0",97:"Numpad 1",98:"Numpad 2",99:"Numpad 3",100:"Numpad 4",101:"Numpad 5",102:"Numpad 6",103:"Numpad 7",104:"Numpad 8",105:"Numpad 9",106:"Numpad *",107:"Numpad +",109:"Numpad -",110:"Numpad .",111:"Numpad /",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"Num Lock",145:"Scroll Lock",173:"-",182:"My Computer",183:"My Calculator",186:";",187:"=",188:",",189:"-",190:".",191:"/",192:"`",219:"[",220:"\\",221:"]",222:"'"};

	/*
		* Export
		*/
	return InventoryField;
});

/****
* InventoryBlock
* @author: MajorLabel <info@majorlabel.nl>
* @license GNU
*/
(function inventoryblockModule(factory){

	if (typeof define === "function" && define.amd) {
		define(factory);
	} else if (typeof module != "undefined" && typeof module.exports != "undefined") {
		module.exports = factory();
	} else {
		window["InventoryBlock"] = factory();
	}

})(function inventoryblockFactory(){

	/**
	 * @class InventoryBlock
	 * @param {element} (class: "cbds-inventory-block")
	 */
	function InventoryBlock(el, params){

		var defaults = {
			"linesContClass" : "cbds-inventorylines",
			"lineClass" : "cbds-inventoryline",
			"linePrefix" : "cbds-inventoryline",
			"inputPrefix" : "cbds-inventoryline__input",
			"aggrPrefix" : "cbds-inventoryaggr",
			"aggrInputPrefix" : "cbds-inventoryaggr__input"
		};

		params = params == undefined ? defaults : params;

		/* Public properties */
		this.linesContClass = params.linesContClass,
		this.lineClass = params.lineClass,
		this.linePrefix = params.linePrefix,
		this.inputPrefix = params.inputPrefix,
		this.aggrPrefix = params.aggrPrefix,
		this.aggrInputPrefix = params.aggrInputPrefix,

		this.el = el,
		this.editmode = params.editmode,
		this.linesContainer = el.getElementsByClassName(this.linesContClass)[0],
		this.inventoryLines = {},
		this.inventoryLines.seq = 0,
		this.countCont = this.utils.getFirstClass(el, "cbds-inventoryaggr--linecount"),
		this.aggrCont = this.utils.getFirstClass(el, this.aggrPrefix);

		// Aggregation fields
		this.fields = {};
		var inputs = el.getElementsByTagName("input"),
			r = new RegExp(this.aggrInputPrefix + "--[\\w]*", "g");
		for (var i = 0; i < inputs.length; i++) {
			if ((inputs[i].className.match(r) || []).length != 0) {
				var field = new InventoryField(inputs[i], this, {
					"decimals" : 2,
					"decSep" : window.userDecimalSeparator,
					"curSep" : window.userCurrencySeparator
				});
				this.fields[field.getFieldName(this.aggrInputPrefix)] = field;
			}
		}

		// Construction
		this.utils.on(window, "click", this.handleClicks, this);
		this.utils.on(this.aggrCont, "keyup", this.handleAggrInput, this);
		if (this.editmode == 'EditView') {
			this.startSortable();
		}
		this.startLines();
		ldsCheckbox.setUnique();

		var taxtypeInput = this.utils.getFirstClass(el, "cbds-inventory-block__input--taxtype");
		this.taxTypeCombo = new ldsCombobox(this.utils.findUp(taxtypeInput, ".slds-combobox-picklist"), {
			"onSelect" : this.changeTaxType.bind(this),
			'enabled': this.editmode == 'EditView'
		});

		var ttCom = this.taxTypeCombo;
		this.fields.taxtype = {
			'getValue': function() {return ttCom._val},
			'getSaveName': function(){return 'taxtype'}
		}
		this.updateAggr();
	}

	InventoryBlock.prototype = {
		constructor	: InventoryBlock,

		handleClicks: function(e) {
			var functionElement = this.utils.findUp(e.target, "data-clickfunction");
			if (functionElement !== undefined) {
				switch (functionElement.getAttribute("data-clickfunction")) {
					case "expandAllLines":
						this.expandAllLines();
						break;
					case "collAllLines":
						this.collAllLines();
						break;
					case "insertNewLine":
						this.insertNew(this);
						break;
					case "deleteAllLines":
						if (confirm("Are you sure you want to delete ALL lines?"))
							this.deleteAllLines();
						break;
				}
			}
		},

		handleAggrInput: function(e) {
			var validated = true;
			for (field in this.fields) {
				if (_isTrueField(this.fields[field])) {
					this.fields[field].setNormal();
					if (!this.fields[field].validate()) {
						validated = false;
						this.fields[field].setError();
						break;
					}
				}
			}

			if (validated) {
				this.updateAggr();
			}
		},

		startSortable: function() {
			Sortable.create(this.linesContainer, {
				draggable: "." + this.lineClass,
				handle: ".cbds-detail-line-dragtool",
				animation: 100,
				onEnd: this.updateAggr.bind(this)
			});
		},

		startLines : function() {
			var lines = this.linesContainer.getElementsByClassName(this.lineClass);
			for (var i = 0; i < lines.length; i++) {
				var line = new InventoryLine(lines[i], this);
			}
			this.updateCount();
		},

		expandAllLines : function() {
			this.setAllExtraStates(1);
		},

		collAllLines : function() {
			this.setAllExtraStates(0);
		},

		setAllExtraStates : function(state) {
			for (prop in this.inventoryLines) {
				if (typeof this.inventoryLines[prop].expandExtra == "function") {
					if (state == 1) this.inventoryLines[prop].expandExtra();
					if (state == 0) this.inventoryLines[prop].collExtra();
				}
			}
		},

		deleteAllLines : function() {
			for (prop in this.inventoryLines) {
				if (prop != "seq")
					this.inventoryLines[prop].delete();
			}
			this.updateCount();
		},

		insertNew : function() {
			var template = document.getElementsByClassName(this.lineClass + "_template")[0];
			var container = document.getElementsByClassName(this.linesContClass)[0];
			var newNode = template.cloneNode(true);
			newNode.classList.remove(this.lineClass + "_template");
			container.appendChild(newNode);
			new InventoryLine(newNode, this);
			this.updateCount();
		},

		updateCount : function() {
			this.countCont.innerHTML = this.el.getElementsByClassName(this.lineClass).length;
		},

		updateAggr : function() {
			this.calcGross();
			this.calcTotalLineDiscount();
			this.calcTotalDiscount();
			this.calcGroupTaxes();
			this.setTotalTax();
			this.calcTotal();
			this.calcGrandTotal();
			this.updateHiddenDomFields();
		},

		updateHiddenDomFields: function() {
			let domFieldsContainer = this.utils.getFirstClass(this.el, `${this.linesContClass}__domfields`),
				aggrFieldsContainer = this.utils.getFirstClass(this.el, `${this.aggrPrefix}__domfields`);
			domFieldsContainer.innerHTML = '';
			aggrFieldsContainer.innerHTML = '';

			for (line in this.inventoryLines) {
				if (typeof this.inventoryLines[line].updateDomContainer === 'function') {
					this.inventoryLines[line].updateDomContainer(domFieldsContainer);
				}
			}
			for (field in this.fields) {
				let fldName = this.fields[field].getSaveName(),
					input = _getHiddenInputForField(`aggr_fields[${fldName}]`);
				aggrFieldsContainer.appendChild(input);
				input.value = this.fields[field].getValue();
			}
		},

		calcGross : function() {
			this.fields.grosstotal.update(this.getLinesSum("extgross"));
		},

		calcTotalLineDiscount : function() {
			this.fields.pl_dto_line.update(this.getLinesSum("discount_total"));
		},

		calcTotalDiscount: function() {
			this.fields.totaldiscount.update(this.fields.pl_dto_line.getValue() + this.fields.pl_dto_global.getValue());
		},

		calcGroupTaxes : function() {
			for (field in this.fields) {
				if (field.match(/^(sh)?tax[\d]{1,2}$/) != null) {
					this.calcTax(field);
				}
			}
		},

		setTotalTax : function() {
			this.fields.taxtotal.update(this.getTaxes());
			this.fields.shtaxtotal.update(this.getSHTaxes());
		},

		calcTotal : function() {
			this.fields.sum_nettotal.update(this.getLinesSum("linetotal")); // bGD
			this.fields.subtotal.update(
				this.fields.sum_nettotal.getValue() - this.fields.pl_dto_global.getValue() // aGD
			);
		},

		calcGrandTotal : function() {
			if (this.taxTypeCombo._val == "group")
				this.fields.total.update(
					this.fields.subtotal.getValue() +
					this.fields.taxtotal.getValue() +
					this.fields.pl_adjustment.getValue() +
					this.fields.pl_sh_total.getValue() +
					this.fields.shtaxtotal.getValue()
				);
			else if (this.taxTypeCombo._val == "individual")
				this.fields.total.update(
					this.fields.subtotal.getValue() +
					this.fields.pl_adjustment.getValue() +
					this.fields.pl_sh_total.getValue() +
					this.fields.shtaxtotal.getValue()
				);
		},

		calcTax: function(name) {
			var base = name.indexOf('sh') === 0 ? this.fields.pl_sh_total.getValue() : this.fields.grosstotal.getValue() - this.fields.totaldiscount.getValue();
			var taxAmount = this.utils.getPerc(base, this.fields[name].getValue());
			this.fields['sum_' + name].update(taxAmount);
		},

		getLinesSum : function(fieldname) {
			var sum = 0;
			for (line in this.inventoryLines) {
				sum = sum + (this.inventoryLines[line].fields != undefined ? Number(this.inventoryLines[line].fields[fieldname]._val) : 0);
			}
			return sum;
		},

		getTaxes : function() {
			var sum = 0,
				r = new RegExp("^sum_tax[\\d]{1,2}", ""),
				type = this.taxTypeCombo._val;

			if (type == "individual") {
				for (line in this.inventoryLines) {
					for (field in this.inventoryLines[line].fields) {
						if ((field.match(r) || []).length > 0) {
							sum = sum + Number(this.inventoryLines[line].fields[field]._val);
						}
					}
				}
			} else if (type == "group") {
				for (field in this.fields) {
					if ((field.match(r) || []).length > 0)
						sum = sum + Number(this.fields[field]._val);
				}
			}
			return sum;
		},

		getSHTaxes : function() {
			var sum = 0,
				r = new RegExp("^sum_shtax[\\d]{1,2}", "");
			for (field in this.fields) {
				if ((field.match(r) || []).length > 0)
					sum = sum + Number(this.fields[field].getValue());
			}
			return sum;
		},

		changeTaxType : function(val) {
			if (val == "individual") {
				this.utils.getFirstClass(this.el, this.aggrPrefix + "__taxes_group").classList.remove("active");
			} else if (val == "group") {
				this.utils.getFirstClass(this.el, this.aggrPrefix + "__taxes_group").classList.add("active");
			}

			for (line in this.inventoryLines) {
				if (line != "seq") {
					this.inventoryLines[line].setTaxType();
					this.inventoryLines[line].setTotal();
				}
			}
			this.fields.taxtype._val = val;
			this.updateAggr();
		},

		/*
			* Class utilities
			*/
		utils : {
			/*
				* Util: 'findUp'
				* Returns the first element up the DOM that matches the search
				*
				* @param: element: 	the node to start from
				* @param: searchterm: 	Can be a class (prefix with '.'), ID (prefix with '#')
				*						or an attribute (default when no prefix)
				*/
			findUp : function(element, searchterm) {
				element = element.children[0] != undefined ? element.children[0] : element; // Include the current element
				while (element = element.parentElement) {
					if ( (searchterm.charAt(0) === "#" && element.id === searchterm.slice(1) )
						|| ( searchterm.charAt(0) === "." && element.classList.contains(searchterm.slice(1) ) 
						|| ( element.hasAttribute(searchterm) ))) {
						return element;
					} else if (element == document.body) {
						break;
					}
				}
			},
			/*
				* Util: 'getFirstClass'
				* Returns the first element from the root that matches
				* the classname
				*
				* @param: root: 		the node to start from
				* @param: className: 	The classname to search for
				*/
			getFirstClass: function(root, className) {
				return root.getElementsByClassName(className)[0] != undefined ? root.getElementsByClassName(className)[0] : {};
			},
			/*
				* Util: 'on'
				* Adds an event listener
				*
				* @param: el: 			The node to attach the listener to
				* @param: type: 		The type of event
				* @param: func: 		The function to perform
				* @param: context: 	The context to bind the listener to
				*/
			on: function(el,type,func,context) {
				el.addEventListener(type, func.bind(context));
			},
			/*
				* Util: 'off'
				* Removes an event listener
				*
				* @param: el: 			The node to remove the listener from
				* @param: type: 		The type of event
				* @param: func: 		The function to remove
				*/
			off: function(el,type,func) {
				el.removeEventListener(type, func);
			},
			/*
				* Util: 'insertAfter'
				* Inserts a new node after the given
				*
				* @param: referenceNode: 	The node to insert after
				* @param: newNode: 		The node to insert
				*/
			insertAfter: function(referenceNode, newNode) {
				referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling)
			},
			/*
				* Util: 'deductPerc'
				* deducts a percentage from a number
				*
				* @param: base: 		The base '100%' number
				* @param: percentage: 	The percentage to deduct
				*/
			deductPerc: function(base, percentage) {
				return (base * (1 - (percentage / 100)));
			},
			/*
				* Util: 'getPerc'
				* Returns a percentage of a base no.
				*
				* @param: base: 		The base '100%' number
				* @param: percentage: 	The percentage to return
				*/
			getPerc: function(base, percentage) {
				return base * (percentage / 100);
			}
		}
	}

	/*
		* Factory tools
		*/
	function _getHiddenInputForField(fieldName) {
		let input = document.createElement('INPUT');
		input.name = fieldName,
		input.type = 'hidden';
		return input;
	}

	function _isTrueField(field) {
		return field.constructor.name === 'InventoryField';
	}

	/*
		* Export
		*/
	return InventoryBlock;

});

/****
* cbNumber
* @author: MajorLabel <info@majorlabel.nl>
* @license GNU / GPL v2
*/
(function cbnumberModule(factory){

	if (typeof define === "function" && define.amd) {
		define(factory);
	} else if (typeof module != "undefined" && typeof module.exports != "undefined") {
		module.exports = factory();
	} else {
		window["cbNumber"] = factory();
	}

})(function cbnumberFactory(){

	/**
	 * @class ldsCheckbox
	 */
	function cbNumber() {
		/* Public attributes */
	}

	/*
		* Static properties
		*/
	cbNumber.decSep = window.userDecimalSeparator,
	cbNumber.curSep = window.userCurrencySeparator,
	cbNumber.decNum = Number(window.userNumberOfDecimals);

	/*
		* Static methods
		*/

	/*
		* curToNumString
		*--------------------------
		* Turns a currency formatted string into a number formatted
		* string. Respects the currently selected user format
		*
		* @return: Number formatted string
		*/
	cbNumber.curToNumString = function(cur) {
		var c = cur.toString(),
			curR = new RegExp("\\"+this.curSep, "g"),
			decR = new RegExp("(\\"+this.decSep+")([0-9]{2})", "g"),
			c = c.replace(curR, "").replace(decR, ".$2");
		return parseFloat(c).toFixed(this.decNum).toString();
	}

	/*
		* curToNum
		*--------------------------
		* Turns a currency formatted string into a number.
		* Respects the currently selected user format
		*
		* @return: Number
		*/
	cbNumber.curToNum = function(cur) {
		return parseFloat(cbNumber.curToNumString(cur));
	}

	/*
		* isCur
		*--------------------------
		* Tests if a string is formatted to the current
		* user's currency settings. Respects the fact that
		* decimals are optional. Also respects negative
		* currencies.
		*
		* @return: Bool
		*/
	cbNumber.isCurr = function(cur) {
		cur = cur.replace(/^-/, "");
		var r = new RegExp("^\\d{1,3}(\\" + this.curSep + "\\d{3})*(\\" + this.decSep + "\\d{" + this.decNum + "})?$", "");
		return (cur.match(r) || []).length == 0 ? false : true;
	}

	/*
		* numToCurr
		*--------------------------
		* Turns a number into a currencu formatted string.
		* Respects the user settings, but does NOT add decimals
		* if the number is an integer
		*
		* @return: Currency formatted string
		*/
	cbNumber.numToCurr = function(n){
		var n = n, 
			c = cbNumber.decimalNum(n) == 0 ? 0 : 2, 
			d = this.decSep == undefined ? "." : this.decSep, 
			t = this.curSep == undefined ? "," : this.curSep, 
			s = n < 0 ? "-" : "", 
			i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
			j = (j = i.length) > 3 ? j % 3 : 0;
			return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	}

	/*
		* decimalNum
		*--------------------------
		* Takes a number (or string formatted as a number)
		* and returns the number of decimals it has
		*
		*/	
	cbNumber.decimalNum = function(num) {
		var match = (''+num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
		if (!match) { return 0; }
		return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
	}

	/*
		* isFloat
		*--------------------------
		* Takes a number (or string formatted as a number)
		* and returns a boolean indicating whether it's a
		* floating point no. or not
		*
		*/
	cbNumber.isFloat = function(num) {
		return this.decimalNum(num) > 0 ? true : false;
	}

	/*
		* isInt
		*--------------------------
		* Takes a number (or string formatted as a number)
		* and returns a boolean indicating whether it's a
		* integer or not
		*
		*/
	cbNumber.isInt = function(num) {
		return this.decimalNum(num) === 0 && !isNaN(num) ? true : false;
	}

	cbNumber.prototype = {
		constructor: cbNumber,
	}

	/*
		* Export
		*/
	return cbNumber;

}); 