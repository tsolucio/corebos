/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class CheckboxRender {

  	constructor(props) {
    	const { grid, rowKey } = props;
    	const label = document.createElement('label');
    	label.className = 'checkbox';
    	label.setAttribute('for', String(rowKey));
    	const Input = document.createElement('input');
    	Input.name = 'selected_id[]';
    	Input.setAttribute('onclick', 'ListView.getCheckedRows("", this);');
    	Input.className = 'hidden-input listview-checkbox';
   	 	Input.id = String(rowKey);
    	label.appendChild(Input);
    	Input.type = 'checkbox';
    	Input.addEventListener('change', () => {
	      	if (Input.checked) {
	        	grid.check(rowKey);
	      	} else {
	        	grid.uncheck(rowKey);
	      	}
    	});
    	this.el = label;
    	this.render(props);
  	}

  	getElement() {
    	return this.el;
  	}

  	render(props) {
    	const Input = this.el.querySelector('.hidden-input');
    	const checked = Boolean(props.value);
    	Input.checked = checked;
  	}
}