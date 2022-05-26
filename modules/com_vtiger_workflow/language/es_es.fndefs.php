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
	'categories' => array('Text'),
	'examples' => array(
		"concat(firstname, ' ', lastname)",
	),
),
'coalesce' => array(
	'name' => 'coalesce(a, b,...)',
	'desc' => 'Devuelve el primer valor no vacío encontrado en la lista de parámetros',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Múltiple',
			'optional' => false,
			'desc' => 'cualquier valor o nombre de campo válido',
		),
		array(
			'name' => 'b',
			'type' => 'Múltiple',
			'optional' => true,
			'desc' => 'cualquier valor o nombre de campo válido',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'coalesce(email1, email2)'
	),
),
'time_diffdays' => array(
	'name' => 'time_diffdays(a, b)',
	'desc' => 'Calcula la diferencia de tiempo (en días) entre dos campos de fecha específicos',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'b',
			'type' => 'Fecha',
			'optional' => true,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha. si se deja vacío, se utilizará la fecha actual',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		'time_diffdays(invoicedate, duedate)',
		'time_diffdays(duedate)',
	),
),
'time_diffyears' => array(
	'name' => 'time_diffyears(a, b)',
	'desc' => 'Calcula la diferencia de tiempo (en años) entre dos campos de fecha específicos',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'b',
			'type' => 'Fecha',
			'optional' => true,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha. si se deja vacío, se utilizará la fecha actual',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		'time_diffyears(invoicedate, duedate)',
		'time_diffyears(duedate)',
	),
),
'time_diffweekdays' => array(
	'name' => 'time_diffweekdays(a, b)',
	'desc' => 'Calcula la diferencia de tiempo (en días) entre dos campos de fecha específicos excluyendo los fines de semana',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'b',
			'type' => 'Fecha',
			'optional' => true,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha. si se deja vacío, se utilizará la fecha actual',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		'time_diffweekdays(invoicedate, duedate)',
		'time_diffweekdays(duedate)',
	),
),
'time_diff' => array(
	'name' => 'time_diff(a, b)',
	'desc' => 'Calcula la diferencia de tiempo (en segundos) entre dos campos de fecha específicos',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'b',
			'type' => 'Fecha',
			'optional' => true,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha. si se deja vacío, se utilizará la fecha actual',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		'time_diff(invoicedate, duedate)',
		'time_diff(duedate)',
	),
),
'holidaydifference' => array(
	'name' => 'holidaydifference(startDate, endDate, include_saturdays, holidays)',
	'desc' => 'Calcula la diferencia de tiempo (en días) entre dos campos de fecha específicos, excluyendo sábados y festivos si se dan. A diferencia de los días de red, no incluye la fecha de finalización, por lo que, por lo general, hay una diferencia de un día',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'endDate',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'include_saturdays',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'si se establece en 0 los sábados no se agregarán, si se establece en cualquier otro valor, se agregaránd',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'nombre de un mapa de información que contiene las fechas de vacaciones para excluir<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"holidaydifference('2020-01-01', '2020-06-30', 0, 'holidays in Spain 2020')",
		"holidaydifference('2020-01-01', '2020-06-30', 1, 'holidays in Spain 2020')",
		"holidaydifference('2020-01-01', '2020-06-30', 0, 'holidays in France 2020')",
	),
),
'networkdays' => array(
	'name' => 'networkdays(startDate, endDate, holidays)',
	'desc' => 'Devuelve el número de días laborables completos entre start_date y end_date. Los días laborables excluyen los fines de semana y las fechas identificadas en festivos. A diferencia de la diferencia de vacaciones, incluye la fecha de finalización, por lo que, por lo general, hay una diferencia de un día',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'endDate',
			'type' => 'Fecha',
			'optional' => true,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha, se utiliza HOY si no se da',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'nombre de un mapa de información que contiene las fechas de vacaciones para excluir<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"networkdays('2020-01-01', '2020-06-30', 'holidays in Spain 2020')",
		"networkdays('2020-01-01', '2020-06-30', 'holidays in France 2020')",
	),
),
'isholidaydate' => array(
	'name' => 'isholidaydate(date, saturdayisholiday, holidays)',
	'desc' => 'Devuelve verdadero si la fecha dada cae en un día festivo, domingo o sábado. Si el sábado se considera festivo o no se puede definir.',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo fecha',
		),
		array(
			'name' => 'saturdayisholiday',
			'type' => 'Booleano',
			'optional' => false,
			'desc' => 'si se establece en 1, los sábados se considerarán días no laborables (como el domingo)',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'lista de vacaciones separadas por comas o el nombre de un mapa de información que contiene las fechas de vacaciones<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"isholidaydate('2021-01-01', 0, 'holidays in Spain 2021')",
		"isholidaydate('2021-01-01', 1, 'holidays in Spain 2021')",
		"isholidaydate('2021-01-01', 0, 'holidays in France 2021')",
	),
),
'aggregate_time' => array(
	'name' => 'aggregate_time(relatedModuleName, relatedModuleField, conditions)',
	'desc' => 'Esta función devuelve un tiempo agregado de un campo en un módulo relacionado con filtrado opcional de los registros',
	'params' => array(
		array(
			'name' => 'relatedModuleName',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'módulo relacionado para agregar',
		),
		array(
			'name' => 'relatedModuleField',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'nombre del campo relacionado para agregar',
		),
		array(
			'name' => 'conditions',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'condición opcional usada para filtrar los registros: [field,op,value,glue],[...]',
		),
	),
	'categories' => array('Statistics'),
	'examples' => array(
		"aggregate_time('InventoryDetails','quantity*listprice')"
	),
),
'add_days' => array(
	'name' => 'add_days(datefield, noofdays)',
	'desc' => 'Calcular una nueva fecha basada en una fecha dada con un número de días especificado añadido',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'noofdays',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de días',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"add_days(2020-10-10,60)",
		"add_days(2020-10-12,40)",
	),
),
'add_workdays' => array(
	'name' => 'add_workdays(date, numofdays, addsaturday, holidays)',
	'desc' => 'Calcular una fecha de día laborable basado en una fecha dada con un número de días especificado, sábados y festivos agregados',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'numofdays',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de días',
		),
		array(
			'name' => 'addsaturday',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'if set to 0, Saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'nombre de un mapa de información que contiene las fechas de vacaciones para excluir',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"add_dadd_workdaysays('2020-10-01', '40', 2, 'holidays in  2020')",
		"add_workdays('2020-10-01', '60', 3, 'holidays in  2020')",
	),
),
'sub_days' => array(
	'name' => 'sub_days(datefield, noofdays)',
	'desc' => 'Calcular una nueva fecha basada en una fecha dada con un número de días restado',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'noofdays',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de días',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"sub_days(2020-10-01,60)",
		"sub_days(2020-10-01,40)",
	),
),
'sub_workdays' => array(
	'name' => 'sub_workdays(date, numofdays, removesaturday, holidays)',
	'desc' => 'Calcular una nueva fecha de día laborable basado en una fecha determinada con un número de días especificado, sábado y festivos deducidos',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'numofdays',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de días',
		),
		array(
			'name' => 'removesaturday',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'if set to 0, Saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'nombre de un mapa de información que contiene las fechas de vacaciones para excluir',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"sub_workdays('2020-10-01', '60', 0, 'holidays in  2020')",
		"sub_workdays('2020-10-01', '60', 0, 'holidays in  2020')",
	),
),
'add_months' => array(
	'name' => 'add_months(datefield, noofmonths)',
	'desc' => 'Calcular una nueva fecha basada en una fecha dada con un número específico de meses añadidos',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'noofmonths',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de meses',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"add_months('2020-10-01', '10')",
		"add_months('2020-10-01', '5')",
	),
),
'sub_months' => array(
	'name' => 'sub_months(datefield, noofmonths)',
	'desc' => 'Calcular una nueva fecha basada en una fecha dada con un número específico de meses restados',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'noofmonths',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de meses',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"sub_months('2020-10-01', 5)",
	),
),
'add_time' => array(
	'name' => 'add_time(timefield, minutes)',
	'desc' => 'Calcular un nuevo tiempo basado en un tiempo dado, con los minutos especificados añadidos',
	'params' => array(
		array(
			'name' => 'timefield',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier hora válida o nombre de campo de tipo de hora',
		),
		array(
			'name' => 'minutes',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de minutos',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"add_time(start_time, 180)",
		"add_time('12:00', 40)",
	),
),
'sub_time' => array(
	'name' => 'sub_time(timefield, minutes)',
	'desc' => 'Calcular un nuevo tiempo basado en un tiempo dado, con los minutos especificados restados',
	'params' => array(
		array(
			'name' => 'timefield',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier hora válida o nombre de campo de tipo de hora',
		),
		array(
			'name' => 'minutes',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de minutos',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"sub_time(start_time, 90)",
		"sub_time('12:00', 90)",
	),
),
'today' => array(
	'name' => "get_date('today')",
	'desc' => 'Esta función devuelve la fecha actual como un valor de fecha',
	'params' => array(
		array(
			'name' => 'today',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'la cadena today',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('today')'",
	),
),
'today' => array(
	'name' => "get_date('now')",
	'desc' => 'Esta función devuelve la fecha y hora actual como un valor de fecha',
	'params' => array(
		array(
			'name' => 'now',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'la cadena now',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('now')",
	),
),
'tomorrow' => array(
	'name' => "get_date('tomorrow')",
	'desc' => 'Esta función devuelve la fecha de mañana como un valor de fecha',
	'params' => array(
		array(
			'name' => 'tomorrow',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena tomorrow',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('tomorrow')",
	),
),
'yesterday' => array(
	'name' => "get_date('yesterday')",
	'desc' => 'Esta función devuelve la fecha de ayer como valor de fecha.',
	'params' => array(
		array(
			'name' => 'yesterday',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena yesterday',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('yesterday')",
	),
),
'time' => array(
	'name' => "get_date('time')",
	'desc' => 'Esta función devuelve la hora actual.',
	'params' => array(
		array(
			'name' => 'time',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena time',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('time')"
	),
),
'format_date' => array(
	'name' => 'format_date(date,format)',
	'desc' => 'Esta función aplica un formato específico a una fecha.',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'la fecha que necesitas formatear',
		),
		array(
			'name' => 'format',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Especificación de formato de fecha PHP',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"format_date('2020-06-20','d-m-Y')",
		"format_date(due_date,'d-m-Y H:i:s')",
	),
),
'next_date' => array(
	'name' => 'get_nextdate(startDate, days, holidays, include_weekend)',
	'desc' => 'Calcular una próxima fecha basada en una fecha determinada con días específicos, sábado y festivos excluidos',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'days',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de días',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'nombre de un mapa de información que contiene las fechas de vacaciones para incluir',
		),
		array(
			'name' => 'include_weekend',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'si se establece en 0, el fin de semana no se agregará, si se establece en cualquier otro valor, se incluirán',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_nextdate('2020-10-01', '15,30', 'holidays in  2020', 0)",
		"get_nextdate('2020-10-01', '30', 'holidays in  2020' ,1)",
	),
),
'next_date_laborable' => array(
	'name' => 'get_nextdatelaborable(startDate,days,holidays,saturday_laborable)',
	'desc' => 'Calcular una próxima fecha laborable basada en una fecha determinada con días específicos, sábado y festivos excluidos',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Fecha',
			'optional' => false,
			'desc' => 'cualquier fecha válida o nombre de campo de tipo de fecha',
		),
		array(
			'name' => 'days',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de días',
		),
		array(
			'name' => 'holidays',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'nombre de un mapa de información que contiene las fechas de vacaciones para incluir',
		),
		array(
			'name' => 'saturday_laborable',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'si se establece en 0, el fin de semana no se agregará, si se establece en cualquier otro valor, se incluirán',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_nextdate('2020-10-01', '15,30', 'holidays in  2020', 0)",
		"get_nextdate('2020-10-01', '30', 'holidays in  2020', 1)",
	),
),
'stringposition' => array(
	'name' => 'stringposition(haystack,needle)',
	'desc' => 'Esta función le permite encontrar la posición de la primera aparición de una subcadena en una cadena.',
	'params' => array(
		array(
			'name' => 'haystack',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Especifica la cadena en la que se busca',
		),
		array(
			'name' => 'needle',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Especifica la cadena a buscar',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"stringposition('abc','a')",
	),
),
'stringlength' => array(
	'name' => 'stringlength(string)',
	'desc' => 'Esta función devuelve la longitud de una cadena.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Especifica la cadena a medir',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"stringlength('Strings')",
	),
),
'stringreplace' => array(
	'name' => 'stringreplace(search,replace,subject)',
	'desc' => 'Esta función devuelve una cadena con todas las apariciones de búsqueda en el asunto reemplazadas con el valor de reemplazo dado.',
	'params' => array(
		array(
			'name' => 'search',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el valor que se busca',
		),
		array(
			'name' => 'replace',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el valor a sustituir',
		),
		array(
			'name' => 'subject',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena en la que se busca y sustituye',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"stringreplace('ERICA','JON','MIKE AND ERICA ') // cambia erica por jon",
	),
),
'regexreplace' => array(
	'name' => 'regexreplace(pattern,replace,subject)',
	'desc' => 'Esta función devuelve una cadena con todas las apariciones de la expresión regular en el asunto reemplazadas con el valor de reemplazo dado.',
	'params' => array(
		array(
			'name' => 'pattern',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la expresión regular para buscar',
		),
		array(
			'name' => 'replace',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el valor a sustituir',
		),
		array(
			'name' => 'subject',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena en la que se busca y sustituye',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"regexreplace('[A-za-z]+','J','MIKE AND ERICA ')  //todo Js"
	),
),
'randomstring' => array(
	'name' => 'randomstring(length)',
	'desc' => 'Esta función devuelve una cadena aleatoria de la longitud indicada.',
	'params' => array(
		array(
			'name' => 'length',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'número de caracteres aleatorios a devolver',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'randomstring(12)  // 02E373931343',
	),
),
'power' => array(
	'name' => 'power(base, exponential)',
	'desc' => 'Esta función se usa para calcular la potencia de cualquier número, como calcular cuadrados y cubos en campos enteros.',
	'params' => array(
		array(
			'name' => 'base',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'la base al exponente',
		),
		array(
			'name' => 'exponential',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'el número de exponente a la base',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'power(2, 3)',
	),
),
'log' => array(
	'name' => 'log(número, base)',
	'desc' => 'Esta función se utiliza para calcular el logaritmo de cualquier número con la base dada.',
	'params' => array(
		array(
			'name' => 'número',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'el número a calcular el logaritmo',
		),
		array(
			'name' => 'base',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'base del logaritmo, si no se da se utilizará el logaritmo natural',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'log(10)',
		'log(10, 10)',
	),
),
'substring' => array(
	'name' => 'substring(stringfield,start,length)',
	'desc' => 'Esta función devuelve la parte del campo de cadena especificada por los parámetros de inicio y longitud.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'La cadena de la que extraer la subcadena',
		),
		array(
			'name' => 'start',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'Especifica dónde empezar en la cadena, 0 es el primer carácter de la cadena. Un número negativo cuenta hacia atrás desde el final de la cadena.',
		),
		array(
			'name' => 'length',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'Especifica la longitud de la cadena devuelta. Si el parámetro de longitud es 0, NULL o FALSE, devuelve una cadena vacía',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'substring("Hello world",1,8)',
	),
),
'uppercase' => array(
	'name' => 'uppercase(stringfield)',
	'desc' => 'Esta función convierte una cadena especificada a mayúsculas.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena a convertir a mayúsculas',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercase('hello world')",
	),
),
'lowercase' => array(
	'name' => 'lowercase(stringfield)',
	'desc' => 'Esta función convierte una cadena especificada a minúsculas.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena a convertir a minúsculas',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"lowercase('HELLO WORLD')",
	),
),
'uppercasefirst' => array(
	'name' => 'uppercasefirst(stringfield)',
	'desc' => 'Esta función convierte el primer carácter de una cadena a mayúsculas.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena en la que convertir el primer carácter a mayúsculas',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercasefirst('hello world')",
	),
),
'uppercasewords' => array(
	'name' => 'uppercasewords(stringfield)',
	'desc' => 'Esta función convierte el primer carácter de cada palabra en una cadena a mayúsculas.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena en la que convertir cada primer carácter a mayúsculas',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercasewords('hello world')",
	),
),
'num2str' => array(
	'name' => 'num2str(number|field, language)',
	'desc' => 'Esta función convierte un número en su representación textual.',
	'params' => array(
		array(
			'name' => 'number|field',
			'type' => 'Número',
			'optional' => false,
			'desc' => 'número válido o nombre de campo',
		),
		array(
			'name' => 'language',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'El idioma en el que quieres la representación textual',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"num2str('2017.34','en')",
	),
),
'number_format' => array(
	'name' => 'number_format(number, decimals, decimal_separator, thousands_separator)',
	'desc' => 'Esta función formatea un número con miles agrupados.',
	'params' => array(
		array(
			'name' => 'number',
			'type' => 'Número',
			'optional' => false,
			'desc' => 'El número a formatear. Si no se establecen otros parámetros, el número se formateará sin decimales y con una coma (,) como separador de miles',
		),
		array(
			'name' => 'decimals',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'Especifica cuántos decimales. Si se establece solo este parámetro, el número se formateará con un punto (.) Como punto decimal',
		),
		array(
			'name' => 'decimal_separator',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'Especifica el carácter que se utilizará como punto decimal.',
		),
		array(
			'name' => 'thousands_separator',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'Especifica el carácter que se utilizará como separador de miles',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"number_format(1999.2345, 2, ',', '.')",
		"number_format(1999.2345, 2)",
	),
),
'translate' => array(
	'name' => 'translate(string|field)',
	'desc' => 'Esta función traduce una cadena dada.',
	'params' => array(
		array(
			'name' => 'string|field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier cadena o nombre de campo para traducir',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"translate('digit_count')",
		"translate('this is my string')"
	),
),
'round' => array(
	'name' => 'round(numericfield,decimals)',
	'desc' => 'Esta función redondea un número de punto flotante.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Número',
			'optional' => false,
			'desc' => 'El valor a redondear',
		),
		array(
			'name' => 'decimals',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'el número de dígitos decimales a redondear',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'round(7045.2)'
	),
),
'ceil' => array(
	'name' => 'ceil(numericfield)',
	'desc' => 'Esta función redondea un número HACIA ARRIBA al entero más cercano.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Número',
			'optional' => false,
			'desc' => 'El valor para redondear',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"ceil(0.60)",
		"ceil(0.40)",
	),
),
'floor' => array(
	'name' => 'floor(numericfield)',
	'desc' => 'Esta función redondea un número HACIA ABAJO al entero más cercano.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Número',
			'optional' => false,
			'desc' => 'El valor para redondear',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"floor(0.60)",
		"floor(-5.1)",
	),
),
'modulo' => array(
	'name' => 'modulo(numericfield,numericfield)',
	'desc' => 'Esta función devuelve el resto de la división de los parámetros.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'valor o campo',
		),
		array(
			'name' => 'numericfield',
			'type' => 'Entero',
			'optional' => false,
			'desc' => 'valor o campo',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"modulo(5, 3)",
	),
),
'hash' => array(
	'name' => 'hash(field, method)',
	'desc' => 'Esta función genera un valor hash (resumen del mensaje).',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Mensaje al que aplicar el hash',
		),
		array(
			'name' => 'method',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'algoritmo hash a aplicar: "md5", "sha1", "crc32"',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"hash('admin', 'sha1')",
	),
),
'globalvariable' => array(
	'name' => 'globalvariable(gvname)',
	'desc' => 'Devuelve el valor de una variable global.',
	'params' => array(
		array(
			'name' => 'gvname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier nombre de variable global',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"globalvariable('Application_ListView_PageSize')",
	),
),
'aggregation' => array(
	'name' => 'aggregation(operation, RelatedModule, relatedFieldToAggregate, conditions)',
	'desc' => 'Varias filas se agrupan para formar un solo valor de resumen de un campo.',
	'params' => array(
		array(
			'name' => 'operation',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'sum, min, max, avg, count, std, variance, group_concat, time_to_sec',
		),
		array(
			'name' => 'RelatedModule',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'módulo relacionado para la agregación',
		),
		array(
			'name' => 'relatedFieldToAggregate',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo relacionado para agregar',
		),
		array(
			'name' => 'conditions',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'condición opcional usada para filtrar los registros: [field,op,value,glue],[...] Ten en cuenta que la evaluación del valor se realiza con un proceso simple que no admite funciones, si necesitas usar el lenguaje de expresión de flujos de trabajo, tienes que añadir un parámetro con la palabra "expression" para forzar la evaluación del valor como una expresión.',
		),
	),
	'categories' => array('Statistics'),
	'examples' => array(
		"aggregation('min','CobroPago','amount')",
		"aggregation('count','SalesOrder','*','[duedate,h,2018-01-01]')",
		"aggregation('count','SalesOrder','*','[duedate,h,get_date('today'),or,expression]')"
	),
),
'aggregation_fields_operation' => array(
	'name' => 'aggregation_fields_operation(operation, RelatedModule, relatedFieldsToAggregateWithOperation, conditions)',
	'desc' => 'Varias filas se agrupan para formar un solo valor de resumen en una operación de campos.',
	'params' => array(
		array(
			'name' => 'operation',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'sum, min, max, avg, count, std, variance, group_concat, time_to_sec',
		),
		array(
			'name' => 'RelatedModule',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'módulo relacionado para la agregación',
		),
		array(
			'name' => 'relatedFieldsToAggregateWithOperation',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Expresión SQL para ejecutar y agregar',
		),
		array(
			'name' => 'conditions',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'condición opcional usada para filtrar los registros: [field,op,value,glue],[...]',
		),
	),
	'categories' => array('Statistics'),
	'examples' => array(
		"aggregation_fields_operation('sum','InventoryDetails','quantity*listprice')",
	),
),
'getCurrentUserID' => array(
	'name' => 'getCurrentUserID()',
	'desc' => 'Esta función devuelve el ID de usuario actual.',
	'params' => array(
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentUserID()",
	),
),
'getCurrentUserName' => array(
	'name' => 'getCurrentUserName({full})',
	'desc' => 'Esta función devuelve el nombre de usuario actual.',
	'params' => array(
		array(
			'name' => 'full',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'la cadena full, si se proporciona, se devolverá el nombre completo (nombre y apellido) en lugar del nombre de usuario de la aplicación',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentUserName('full')"
	),
),
'getCurrentUserField' => array(
	'name' => 'getCurrentUserField(fieldname)',
	'desc' => 'Esta función devuelve un valor de campo del usuario actual',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier nombre de campo de usuario',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentUserField('email1')",
	),
),
'getCRMIDFromWSID' => array(
	'name' => 'getCRMIDFromWSID(id)',
	'desc' => 'Esta función devuelve el id de un registro',
	'params' => array(
		array(
			'name' => 'id',
			'type' => 'Cadena(WSID)',
			'optional' => false,
			'desc' => 'ID de un registro en formato de servicio web',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getCRMIDFromWSID('33x2222')",
	),
),
'average' => array(
	'name' => 'average(number,...)',
	'desc' => 'Esta funcion devuelve la media de una lista de números',
	'params' => array(
		array(
			'name' => 'number',
			'type' => 'Número',
			'optional' => false,
			'desc' => 'Lista de números',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"average(1,2,3)",
),
),
'getEntityType' => array(
	'name' => 'getEntityType(field)',
	'desc' => 'Esta función devuelve el nombre del módulo de la ID dada.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier campo de relación',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getEntityType(related_to)",
		"getEntityType(id)"
	),
),
'getimageurl' => array(
	'name' => 'getimageurl(field)',
	'desc' => 'Esta función devuelve la URL de la imagen contenida en un campo de imagen.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'cualquier campo de imagen',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getimageurl(placeone)",
	),
),
'getLatitude' => array(
	'name' => 'getLatitude(address)',
	'desc' => 'Esta función devuelve la latitud de una dirección dada.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Dirección para encontrar la latitud',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getLatitude(address)",
	),
),
'getLongitude' => array(
	'name' => 'getLongitude(address)',
	'desc' => 'Esta función devuelve la longitud de una dirección dada.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Dirección para encontrar la longitud',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getLongitude(address)",
	),
),
'getGEODistance' => array(
	'name' => 'getGEODistance(address_from, address_to)',
	'desc' => 'Esta función devuelve la distancia de una dirección a otra.',
	'params' => array(
		array(
			'name' => 'address_from',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Dirección de',
		),
		array(
			'name' => 'address_to',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Dirección a',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistance(address_from, address_to)"
	),
),
'getGEODistanceFromCompanyAddress' => array(
	'name' => 'getGEODistanceFromCompanyAddress(address)',
	'desc' => 'Esta función devuelve la distancia desde la dirección dada a la dirección establecida de la empresa.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromCompanyAddress(address)"
	),
),
'getGEODistanceFromUserAddress' => array(
	'name' => 'getGEODistanceFromUserAddress(address)',
	'desc' => 'Esta función devuelve la distancia desde la dirección dada a la dirección de usuario establecida.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUserAddress('address')",
	),
),
'getGEODistanceFromUser2AccountBilling' => array(
	'name' => 'getGEODistanceFromUser2AccountBilling(account, address_specification)',
	'desc' => 'Esta función calcula la distancia desde la dirección del usuario hasta la dirección de facturación de la cuenta.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Cuenta',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de facturación',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2AccountBilling(account, 'address')"
	),
),
'getGEODistanceFromAssignUser2AccountBilling' => array(
	'name' => 'getGEODistanceFromAssignUser2AccountBilling(account, assigned_user, address_specification)',
	'desc' => 'Esta función calcula la distancia desde el usuario asignado a la dirección de facturación de la cuenta.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Cuenta',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Usuario',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de facturación',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2AccountBilling(account, assigned_user, 'address')"
	),
),
'getGEODistanceFromUser2AccountShipping' => array(
	'name' => 'getGEODistanceFromUser2AccountShipping(account, address_specification)',
	'desc' => 'Esta función calcula la distancia desde el usuario actual hasta la dirección de envío de la cuenta.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Cuenta',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de envío',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2AccountShipping(account, 'address')"
	),
),
'getGEODistanceFromAssignUser2AccountShipping' => array(
	'name' => 'getGEODistanceFromAssignUser2AccountShipping(account, assigned_user, address_specification)',
	'desc' => 'Esta función calcula la distancia desde Asignar usuario a la dirección de envío de la cuenta.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Cuenta',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Usuario',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de envío',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2AccountShipping(account, assigned_user, 'address')"
	),
),
'getGEODistanceFromUser2ContactBilling' => array(
	'name' => 'getGEODistanceFromUser2ContactBilling(contact, address_specification)',
	'desc' => 'Esta función calcula la distancia desde el usuario hasta la dirección de facturación del contacto.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Contacto',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de facturación',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2ContactBilling(contact, 'address')",
	),
),
'getGEODistanceFromAssignUser2ContactBilling' => array(
	'name' => 'getGEODistanceFromAssignUser2ContactBilling(contact, assigned_user, address_specification)',
	'desc' => 'Esta función calcula la distancia desde el usuario asignado hasta la dirección de facturación del contacto.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Contacto',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Usuario',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de facturación',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2ContactBilling(contact, assigned_user, 'address')",
	),
),
'getGEODistanceFromUser2ContactShipping' => array(
	'name' => 'getGEODistanceFromUser2ContactShipping(contact, address_specification)',
	'desc' => 'Esta función calcula la distancia desde un usuario a una dirección de envío de contacto.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Contacto',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de envío',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2ContactShipping(contact, 'address')",
	),
),
'getGEODistanceFromAssignUser2ContactShipping' => array(
	'name' => 'getGEODistanceFromAssignUser2ContactShipping(contact, assigned_user, address_specification)',
	'desc' => 'Esta función calcula la distancia desde el usuario asignado a la dirección de envío del contacto.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Contacto',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID Usuario',
		),
		array(
			'name' => 'address_specification',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campos que contienen la dirección de envío',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2ContactShipping(contact, assigned_user, 'address')",
	),
),
'getGEODistanceFromCoordinates' => array(
	'name' => 'getGEODistanceFromCoordinates(lat1, long1, lat2, long2)',
	'desc' => 'Esta función calcula la distancia entre dos coordenadas.',
	'params' => array(
		array(
			'name' => 'lat1',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'latitud desde',
		),
		array(
			'name' => 'long1',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'longitud desde',
		),
		array(
			'name' => 'lat2',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'latitud a',
		),
		array(
			'name' => 'long2',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'longitud a',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		'getGEODistanceFromCoordinates(lat1, long1, lat2, long2)',
	),
),
'getIDof' => array(
	'name' => 'getIDof(module, searchon, searchfor)',
	'desc' => 'Esta función busca en el módulo dado un registro con el valor `searchfor` en el campo `searchon` y devuelve el ID de ese registro si se encuentra o 0 si no. El objetivo de esta función es establecer valores de campos relacionados en tareas de creación/actualización.',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el nombre del módulo en el que buscar.',
		),
		array(
			'name' => 'searchon',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo del módulo en el que buscar',
		),
		array(
			'name' => 'searchfor',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor para buscar',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getIDof('Contacts', 'firstname', 'Amy')",
		"getIDof('Accounts', 'siccode', 'xyhdmsi33')",
		"getIDof('Accounts', 'siccode', algun_campo)",
	),
),
'getRelatedIDs' => array(
	'name' => 'getRelatedIDs(module, recordid)',
	'desc' => 'Esta función devuelve un array de IDs de registros del módulo dado que están relacionados con el registro que activa el flujo de trabajo',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el nombre del módulo relacionado en el que buscar.',
		),
		array(
			'name' => 'recordid',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'el ID de registro principal para obtener los registros relacionados, si no se proporciona se utilizará el registro actual del flujo de trabajo',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedIDs('Contacts')",
		"getRelatedIDs('Accounts')",
		"getRelatedIDs('Contacts', 943)",
	),
),
'getRelatedMassCreateArray' => array(
	'name' => 'getRelatedMassCreateArray(module, recordid)',
	'desc' => 'Obtener una estructura JSON de creación masiva de servicio web para el ID de registro dado y sus registros de módulo relacionados',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'el nombre del módulo relacionado para obtener registros de',
		),
		array(
			'name' => 'recordid',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'el ID del registro principal para obtener los registros relacionados, si no se proporciona el registro actual que desencadena el flujo de trabajo, se utilizará',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedMassCreateArray('Contacts', 943)",
	),
),
'getRelatedMassCreateArrayConverting' => array(
	'name' => 'getRelatedMassCreateArrayConverting(module, MainModuleDestination, RelatedModuleDestination, recordid)',
	'desc' => 'Obtener una estructura JSON de creación masiva de servicio web para el ID de registro dado y sus registros de módulo relacionados aplicando mapas de conversión',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'el nombre del módulo relacionado para obtener registros de',
		),
		array(
			'name' => 'MainModuleDestination',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'módulo destino registros del módulo principal',
		),
		array(
			'name' => 'RelatedModuleDestination',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'módulo destino para módulos relacionados',
		),
		array(
			'name' => 'recordid',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'el ID del registro principal para obtener los registros relacionados, si no se proporciona el registro actual que desencadena el flujo de trabajo, se utilizará',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedMassCreateArrayConverting('Contacts','Products','PurchaseOrder',943)",
	),
),
'getRelatedRecordCreateArrayConverting' => array(
	'name' => 'getRelatedRecordCreateArrayConverting(module, RelatedModuleDestination, recordid)',
	'desc' => 'Obtener una estructura JSON de Maestro-Detalle de servicio web para el ID de registro dado y sus registros de módulo relacionados aplicando mapas de conversión',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'el nombre del módulo relacionado para obtener registros de',
		),
		array(
			'name' => 'RelatedModuleDestination',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'módulo destino para módulos relacionados',
		),
		array(
			'name' => 'recordid',
			'type' => 'Entero',
			'optional' => true,
			'desc' => 'el ID del registro principal para obtener los registros relacionados, si no se proporciona el registro actual que desencadena el flujo de trabajo, se utilizará',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedRecordCreateArrayConverting('Contacts','PurchaseOrder',943)",
	),
),
'getISODate' => array(
	'name' => 'getISODate(año, semana, diadesemana)',
	'desc' => 'Obtiene Fecha en formato ISO a partir de un año, semana y día determinados en una semana',
	'params' => array(
		array(
			'name' => 'year',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'Año',
		),
		array(
			'name' => 'weeks',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'número de semana',
		),
		array(
			'name' => 'dayInWeek',
			'type' => 'Cadena',
			'optional' => false,
			'desc' => 'número del día de la semana (1-7)',
		)
	),
	'categories' => array('Application'),
	'examples' => array(
		"getISODate('2022','10','4',)",
	),
),
'getFieldsOF' => array(
	'name' => 'getFieldsOF(id, módulo, campos)',
	'desc' => 'Dado el ID de un registro existente, esta función devolverá una matriz con todos los valores de los campos a los que tiene acceso el usuario. Si especificas los campos que quieres en la función, solo se devolverán esos valores.',
	'params' => array(
		array(
			'name' => 'id',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el ID en el que buscar',
		),
		array(
			'name' => 'módulo',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el módulo en el que buscar',
		),
		array(
			'name' => 'campos',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'campos a devolver',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getFieldsOF('8509', 'Contacts')",
		"getFieldsOF('8509', 'Contacts', 'field1,field2,...,fieldN')",
	),
),
'getFromContext' => array(
	'name' => 'getFromContext(variablename)',
	'desc' => 'Esta función obtiene el valor de la variable de contexto variablename.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'el nombre de la variable para leer del contexto. acepta sintaxis de puntos en el nombre de la variable y se puede especificar más de una variable separándolas con comas. Si se proporciona más de una variable, se devolverá una matriz codificada en JSON con los valores',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getFromContext('ID')",
		"getFromContext('ID,firstname,lastname')",
		"getFromContext('response.property.index.field')",
		"getFromContext('response.data.2.label')",
	),
),
'getFromContextSearching' => array(
	'name' => 'getFromContextSearching(variablename, searchon, searchfor, returnthis)',
	'desc' => 'Esta función obtiene el valor de returnthis del contexto pero busca la entrada correcta en una matriz indicada por nombre de la variable. Esta función recorrerá la variable en el contexto y llegará a una matriz, luego buscará en la matriz un elemento que tenga la propiedad searchon establecida al valor de searchfor, una vez encontrado, devolverá la propiedad indicada por `returnnthis`. Se supone que la matriz contiene objetos o matrices indexadas para buscar.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la ruta a una matriz en contexto. acepta sintaxis de puntos en el nombre de la variable y se puede especificar más de una variable separándolas con comas. Si se proporciona más de una variable, se devolverá una matriz codificada en JSON con los valores',
		),
		array(
			'name' => 'searchon',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'propiedad del elemento de matriz para buscar',
		),
		array(
			'name' => 'searchfor',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor para buscar',
		),
		array(
			'name' => 'returnthis',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'propiedad del elemento de matriz para devolver',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getFromContextSearching('response.data.2.custom_fields', 'label', 'Servizio_di_portineria', 'fleet_data')",
	),
),
'setToContext' => array(
	'name' => 'setToContext(variablename, value)',
	'desc' => 'Esta función establece un valor en una variable de contexto.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'variable para establecer en el contexto',
		),
		array(
			'name' => 'value',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor para establecer',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setToContext('accountname','mortein')",
	),
),
'jsonEncode' => array(
	'name' => 'jsonEncode(field)',
	'desc' => 'Esta función JSON codifica la variable dada.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para codificar en JSON',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"jsonEncode('accountname')",
	),
),
'jsonDecode' => array(
	'name' => 'jsonDecode(field)',
	'desc' => 'Esta función devuelve la decodificación JSON de una variable.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'variable para decodificar desde JSON',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"jsonDecode(field)",
	),
),
'implode' => array(
	'name' => 'implode(delimiter, field)',
	'desc' => 'Esta función devuelve una cadena de concatenación de los elementos de una matriz.',
	'params' => array(
		array(
			'name' => 'delimiter',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'Especifica qué caracter poner entre los elementos de la matriz. El valor predeterminado es una cadena vacía',
		),
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo de tipo matriz o variable para unir',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"implode(' ', somearrayfield)",
		"implode(' ', getFromContext('array_response'))",
	),
),
'explode' => array(
	'name' => 'explode(delimiter, field)',
	'desc' => 'Esta función devuelve una matriz de cadenas, cada una de las cuales es una subcadena de campo formada al dividirla en los límites formados por el delimitador de cadena.',
	'params' => array(
		array(
			'name' => 'delimiter',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'especifica dónde separar la cadena',
		),
		array(
			'name' => 'field',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'la cadena a separar',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"explode(',', 'hello,there')",
		"setToContext('array_response', explode(',', 'hello,there'))",
	),
),
'sendMessage' => array(
	'name' => 'sendMessage(message, channel, time)',
	'desc' => 'TEsta función envía un mensaje al canal de cola de mensajes coreBOS.',
	'params' => array(
		array(
			'name' => 'message',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'El cuerpo del mensaje',
		),
		array(
			'name' => 'channel',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'canal al que enviar el mensaje',
		),
		array(
			'name' => 'time',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'tiempo de expiración del mensaje',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"sendMessage('message', 'somechannel', 90)",
	),
),
'readMessage' => array(
	'name' => 'readMessage(channel)',
	'desc' => 'Esta función lee un mensaje de un canal de cola de mensajes coreBOS.',
	'params' => array(
		array(
			'name' => 'channel',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'canal del cual leer el mensaje',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"readMessage('somechannel')",
	),
),
'getSetting' => array(
	'name' => "getSetting('setting_key', 'default')",
	'desc' => 'Esta función lee una variable del almacén de clave-valor de coreBOS, con un valor predeterminado si no se encuentra.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'calve',
		),
		array(
			'name' => 'default',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'valor a devolver si la clave no se encuentra',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getSetting('KEY_ACCESSTOKEN', 'some default value')",
	),
),
'setSetting' => array(
	'name' => "setSetting('setting_key', value)",
	'desc' => 'Esta función permite establecer un valor en el almacén de clave-valor de coreBOS.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'clave',
		),
		array(
			'name' => 'value',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor para establecer en la clave',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setSetting('hubspot_pollsyncing', 'creating')",
	),
),
'delSetting' => array(
	'name' => 'delSetting("setting_key")',
	'desc' => 'Esta función elimina una clave del almacén de clave-valor de coreBOS.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'clave a eliminar',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setting_key('hubspot_pollsyncing')",
	),
),
'evaluateRule' => array(
	'name' => 'evaluateRule(ruleID)',
	'desc' => 'Esta función evalúa una regla coreBOS.',
	'params' => array(
		array(
			'name' => 'ruleID',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'ID de la regla a ejecutar',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"evaluateRule(ruleID)",
	),
),
'executeSQL' => array(
	'name' => 'executeSQL(query, parameters...)',
	'desc' => 'Ejecuta una consulta SQL.',
	'params' => array(
		array(
			'name' => 'query',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'una consulta SQL preparada',
		),
		array(
			'name' => 'parameters',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'cualquier número de parámetros que necesite la consulta SQL',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"executeSQL('select siccode from vtiger_accounts where accountname=?', campo)",
	),
),
'getCRUDMode' => array(
	'name' => 'getCRUDMode()',
	'desc' => 'Esta función devuelve create o edit dependiendo de la acción que se esté realizando.',
	'params' => array(
	),
	'categories' => array('Application'),
	'examples' => array(
		"getCRUDMode()",
	),
),
'Importing' => array(
	'name' => 'Importing()',
	'desc' => 'Esta función devuelve verdadero si la ejecución está dentro de un proceso de importación o falso en caso contrario.',
	'params' => array(
	),
	'categories' => array('Application'),
	'examples' => array(
		"Importing()",
	),
),
'isNumeric' => array(
	'name' => 'isNumeric(fieldname)',
	'desc' => 'Esta función comprueba si un campo es numérico.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para evaluar',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"isNumeric(accountname)",
	),
),
'isString' => array(
	'name' => 'isString(fieldname)',
	'desc' => 'Esta función comprueba si el campo es una cadena.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para comprobar si es una cadena',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"isString(account_id)",
	),
),
'OR' => array(
	'name' => 'OR(condition1, condition2, {conditions})',
	'desc' => 'Esta función devuelve verdadero si alguna de las condiciones proporcionadas es lógicamente verdadera, y falso si todas las condiciones proporcionadas son lógicamente falsas.',
	'params' => array(
		array(
			'name' => 'condition1',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'primera condición',
		),
		array(
			'name' => 'condition2',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'segunda condición',
		),
		array(
			'name' => 'conditions',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'conjunto de condiciones',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"OR(isString($(account_id : (Accounts) accountname)), isNumeric($(account_id : (Accounts) bill_code)))"
	),
),
'AND' => array(
	'name' => 'AND(condition1, condition2, {conditions})',
	'desc' => 'Esta función devuelve verdadero si todas las condiciones proporcionadas son lógicamente verdaderas, y falso si alguna de las condiciones proporcionadas es lógicamente falsa.',
	'params' => array(
		array(
			'name' => 'condition1',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'primera condición',
		),
		array(
			'name' => 'condition2',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'segunda condición',
		),
		array(
			'name' => 'conditions',
			'type' => 'Texto',
			'optional' => true,
			'desc' => 'conjunto de condiciones',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"AND(isString($(account_id : (Accounts) accountname)), isNumeric($(account_id : (Accounts) accounttype)))"
	),
),
'NOT' => array(
	'name' => 'NOT(condition)',
	'desc' => 'Esta función devuelve el opuesto de un valor lógico - `NOT(TRUE)` devuelve `FALSE`; `NOT(FALSE)` devuelve `TRUE`.',
	'params' => array(
		array(
			'name' => 'condition',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'condición',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"NOT(isString($(account_id : (Accounts) accountname)))",
	),
),
'regex' => array(
	'name' => 'regex(pattern, subject)',
	'desc' => 'Esta función devuelve el resultado de un patrón de expresiones regulares en la cadena dada.',
	'params' => array(
		array(
			'name' => 'pattern',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'patrón de expresiones regulares',
		),
		array(
			'name' => 'subject',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'texto sobre el que aplicar el patrón',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"regex('[a-z]+', msg )",
	),
),
'exists' => array(
	'name' => 'exists(fieldname, value)',
	'desc' => 'Esta función verifica si existe o no un registro con el valor dado en el campo dado.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para comprobar su existencia',
		),
		array(
			'name' => 'value',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor que debe tener el campo',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"exists('accountname', 'Chemex Labs Ltd')",
	),
),
'existsrelated' => array(
	'name' => 'existsrelated(relatedmodule, fieldname, value)',
	'desc' => 'Esta función verifica si existe o no un registro de módulo relacionado con el valor dado en el campo dado.',
	'params' => array(
		array(
			'name' => 'relatedmodule',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'módulo relacionado',
		),
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para filtrar registros',
		),
		array(
			'name' => 'value',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor del campo',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"existsrelated('Contacts', 'accountname', 'Chemex Labs Ltd')",
	),
),
'allrelatedare' => array(
	'name' => 'allrelatedare(relatedmodule, fieldname, value)',
	'desc' => 'Esta función verifica si todos los registros en el módulo relacionado tienen el valor dado en el campo dado.',
	'params' => array(
		array(
			'name' => 'relatedmodule',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'módulo relacionado',
		),
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para filtrar registros',
		),
		array(
			'name' => 'value',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor del campo',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"allrelatedare('Contacts', 'accountname', 'Chemex Labs Ltd')",
	),
),
'allrelatedarethesame' => array(
	'name' => 'allrelatedarethesame(relatedmodule, fieldname, value)',
	'desc' => 'Esta función verifica si todos los registros en el módulo relacionado tienen un único valor. Si además se proporciona un valor, todos los registros tendrán que tener ese valor.',
	'params' => array(
		array(
			'name' => 'relatedmodule',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'módulo relacionado',
		),
		array(
			'name' => 'fieldname',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'campo para filtrar registros',
		),
		array(
			'name' => 'value',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'valor del campo',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"allrelatedarethesame('Contacts', 'accountname', 'Chemex Labs Ltd')",
	),
),
'min' => array(
	'name' => 'min(value1, value2, values)',
	'desc' => 'Esta función devuelve el valor mínimo de los valores dados.',
	'params' => array(
		array(
			'name' => 'values',
			'type' => 'Múltiple',
			'optional' => false,
			'desc' => 'campos y valores para comprobar',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'min(sum_nettotal, sum_total)',
	),
),
'max' => array(
	'name' => 'max(value1, value2, values)',
	'desc' => 'Esta función devuelve el valor máximo de los valores dados.',
	'params' => array(
		array(
			'name' => 'values',
			'type' => 'Múltiple',
			'optional' => false,
			'desc' => 'campos y valores para comprobar',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'max(employees, breakpoint)',
	),
),
'getCurrentConfiguredTaxValues' => array(
	'name' => 'getCurrentConfiguredTaxValues(impuesto)',
	'desc' => 'Devuelve el valor numérico del impuesto dado.',
	'params' => array(
		array(
			'name' => 'impuesto',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'nombre del impuesto',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentConfiguredTaxValues('impuesto')"
	),
),
'getCurrencyConversionValue' => array(
	'name' => 'getCurrencyConversionValue(moneda)',
	'desc' => 'Devuelve el valor numérico de la moneda dada.',
	'params' => array(
		array(
			'name' => 'moneda',
			'type' => 'Texto',
			'optional' => false,
			'desc' => 'nombre de la moneda',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrencyConversionValue('moneda')"
	),
),
);

foreach (glob('modules/com_vtiger_workflow/language/es_es.fndefs.*.php', GLOB_BRACE) as $tcode) {
	include $tcode;
}
