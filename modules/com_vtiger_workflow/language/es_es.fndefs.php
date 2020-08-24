<?php
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
 *************************************************************************************************
 *  Module       : Workflow Expression Function Definitions
 *  Version      : 1.0

 * Definition Template *
'function name' => array( // should be aligned with function sin modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc
	'name' => usually the same as function name but may be different, will be shown to user,
	'desc' => description of the function,
	'params' => parameters of the function, array(
		'param name' => array(
			'type' => 'String' | 'Boolean' | 'Integer' | 'Float' | 'Multiple',
			'optional' => true/false,
			'desc' => 'description of the parameter',
		)
	),
	'categories' => array of categories the function belongs to
	'examples' => array of one-line examples,
)

 # the list of categories can be found n modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc (expressionFunctionCategories)
 *************************************************************************************************/
$WFExpressionFunctionDefinitons = array(
'concat' => array(
	'name' => 'concat(a,b,...)',
	'desc' => 'Combina varios elementos de texto en uno solo',
	'params' => array(
		'a' => array(
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier cadena de texto literal o nombre de campo válido',
		),
		'b' => array(
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier cadena de texto literal o nombre de campo válido',
		),
	),
	'categories' => array('Texto'),
	'examples' => array(
		"concat(firstname, ' ', lastname)",
	),
),

);

foreach (glob('modules/com_vtiger_workflow/language/en_us.fndefs.*.php', GLOB_BRACE) as $tcode) {
	include $tcode;
}
