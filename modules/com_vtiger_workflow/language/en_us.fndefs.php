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
'function name' => array( // see functions in modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc
	'name' => usually the same as function name but may be different, will be shown to user,
	'desc' => description of the function,
	'params' => parameters of the function, array(
		'param name' => array(
			'type' => 'String' | 'Boolean' | 'Integer' | 'Float' | 'Date' | 'DateTime' | 'Multiple',
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
	'name' => 'concat(a, b,...)',
	'desc' => 'Combine multiple text elements into one',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any literal text string or valid field name',
		),
		array(
			'name' => 'b',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any literal text string or valid field name',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"concat(firstname, ' ', lastname)",
	),
),
'coalesce' => array(
	'name' => 'coalesce(a, b,...)',
	'desc' => 'Returns the first non-missing value found in the list of parameters',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Multiple',
			'optional' => false,
			'desc' => 'any value or valid field name',
		),
		 array(
			'name' => 'b',
			'type' => 'Multiple',
			'optional' => false,
			'desc' => 'any value or valid field name',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'coalesce(email1, email2)'
	),
),
'time_diffdays' => array(
	'name' => 'time_diffdays(a, b)',
	'desc' => 'Calculates the difference of time (in days) between two specific date fields',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'b',
			'type' => 'Date',
			'optional' => true,
			'desc' => 'any valid date or date type field name. if left empty the current date will be used',
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
	'desc' => 'Calculates the difference of time (in years) between two specific date fields',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'b',
			'type' => 'Date',
			'optional' => true,
			'desc' => 'any valid date or date type field name. if left empty the current date will be used',
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
	'desc' => 'Calculates the difference of time (in days) between two specific date fields excluding weekends',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'b',
			'type' => 'Date',
			'optional' => true,
			'desc' => 'any valid date or date type field name. if left empty the current date will be used',
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
	'desc' => 'Calculates the difference of time (in seconds) between two specific date fields',
	'params' => array(
		array(
			'name' => 'a',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'b',
			'type' => 'Date',
			'optional' => true,
			'desc' => 'any valid date or date type field name. if left empty the current date will be used',
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
	'desc' => 'Calculates the difference of time (in days) between two specific date fields, excluding Saturdays and Holidays if given. Unlike networkdays it does not include the enddate so, usually, there is a one day difference',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'endDate',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'include_saturdays',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'if set to 0 saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an InformationMap that contains the holiday dates to exclude<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
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
	'desc' => 'Returns the number of whole working days between start_date and end_date. Working days exclude weekends and any dates identified in holidays. Unlike holidaydifference it includes the enddate so, usually, there is a one day difference',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'endDate',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an InformationMap that contains the holiday dates to exclude<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"networkdays('2020-01-01', '2020-06-30', 'holidays in Spain 2020')",
		"networkdays('2020-01-01', '2020-06-30', 'holidays in France 2020')",
	),
),
'aggregate_time' => array(
	'name' => 'aggregate_time(relatedModuleName, relatedModuleField, conditions)',
	'desc' => 'This function returns aggrigate time of reltedmodule,field and conditions',
	'params' => array(
		array(
			'name' => 'relatedModuleName',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related module for aggregate',
		),
		array(
			'name' => 'relatedModuleField',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related  fieldname for aggregate',
		),
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'condition for aggrigate ',
		),
	),
	'categories' => array('Aggregations'),
	'examples' => array(
		"aggregate_time('InventoryDetails','quantity*listprice')"
	),
),
'add_days' => array(
	'name' => 'add_days(datefield, noofdays)',
	'desc' => 'Compute a new date based on a given date with specified number of days added',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'noofdays',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of days',
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
	'desc' => 'Compute a working days  date based on a given date with specified number of days,saturdays and holidays added',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'numofdays',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of days',
		),
		array(
			'name' => 'addsaturday',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'if set to 0 saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an InformationMap that contains the holiday dates to exclude',
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
	'desc' => 'Compute a new date based on a given date with specified number of days deducted',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'noofdays',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of days',
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
	'desc' => 'Compute a new working days date based on a given date with specified number of days,saturday and holiday deducted',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'numofdays',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of days',
		),
		array(
			'name' => 'removesaturday',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'if set to 0 saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an InformationMap that contains the holiday dates to exclude',
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
	'desc' => 'Compute a new date based on a given date with specified number of month added',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'noofmonths',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of months',
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
	'desc' => 'Compute a new date based on a given date with specified number of months deducted',
	'params' => array(
		array(
			'name' => 'datefield',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'noofmonths',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of months',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"sub_months('2020-10-01','5')",
		"sub_months('2020-10-01','5')",
	),
),
'add_time' => array(
	'name' => 'add_time(timefield, minutes)',
	'desc' => 'Compute a new time based on a given time,with respect to specified minutes added',
	'params' => array(
		array(
			'name' => 'timefield',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid time or time type field name',
		),
		array(
			'name' => 'minutes',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of minutes',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"add_time(start_time,180)",
		"add_time('12:00','40')",
	),
),
'sub_time' => array(
	'name' => 'sub_time(timefield, minutes)',
	'desc' => 'Compute a new time based on a given time; with respect to specified minutes deducted',
	'params' => array(
		array(
			'name' => 'timefield',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid time or time type field name',
		),
		array(
			'name' => 'minutes',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of minutes',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"sub_time(start_time,90)",
		"sub_time('12:00','90')",
	),
),
'today' => array(
	'name' => "get_date('today')",
	'desc' => 'This function allows you to update the task on the current day/time when a given condition is met',
	'params' => array(
		array(
			'name' => 'today',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Returns the current date as a date value',
		),
		array(
			'name' => 'now',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Returns the current date and time as a date value',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('today')'",
		"get_date('now')",
	),
),
'tomorrow' => array(
	'name' => "get_date('tomorrow')",
	'desc' => 'This function allows you to update the task the day after today,tomorrow if the given conditions are met',
	'params' => array(
		array(
			'name' => 'tomorrow',
			'type' => 'String',
			'optional' => false,
			'desc' => 'A word tomorrow',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('tomorrow')",
		"get_date('tomorrow')",
	),
),
'yesterday' => array(
	'name' => "get_date('yesterday')",
	'desc' => 'This function allows you to update the task with the past date,yesterday if the given conditions are met.',
	'params' => array(
		array(
			'name' => 'yesterday',
			'type' => 'String',
			'optional' => false,
			'desc' => 'A word yesterday',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('yesterday')",
		"get_date('yesterday')",
	),
),
'time' => array(
	'name' => "get_date('time')",
	'desc' => 'This function allows you to update the task with the time,time if the given conditions are met.',
	'params' => array(
		array(
			'name' => 'time',
			'type' => 'String',
			'optional' => false,
			'desc' => 'A word time',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('time')"
	),
),
'format_date' => array(
	'name' => 'format_date(date,format)',
	'desc' => 'This function allows you to update the date formats.',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'A date you need to format',
		),
		array(
			'name' => 'format',
			'type' => 'String',
			'optional' => false,
			'desc' => 'A format you need for date format',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"format_date('2020-06-20','d-m-Y')",
		"format_date(array('2020-09-20','d-m-Y H:i:s')",
	),
),
'next_date' => array(
	'name' => 'get_nextdate(startDate,days,holidays,include_weekend)',
	'desc' => 'Compute a next date based on a given date with specified  days,saturday and holiday deducted',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type fieldname',
		),
		array(
			'name' => 'days',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of days',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an InformationMap that contains the holiday dates to include',
		),
		array(
			'name' => 'include_weekend',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'if set to 0 weekend will not be added, if set to any other value, they will be included',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_nextdate('2020-10-01', '15,30','holidays in  2020',2)",
		"get_nextdate('2020-10-01', '30','holidays in  2020' ,1)",
	),
),
'next_date_laborable' => array(
	'name' => 'get_nextdatelaborable(startDate,days,holidays,saturday_laborable)',
	'desc' => 'Compute a next date based on a given date with specified  days, Saturday and holiday deducted',
	'params' => array(
		array(
			'name' => 'startDate',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'days',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of days',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an InformationMap that contains the holiday dates to include',
		),
		array(
			'name' => 'saturday_laborable',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'if set to 0 weekend will not be added, if set to any other value, they will be included',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_nextdate('2020-10-01', '15,30','holidays in  2020',2)",
		"get_nextdate('2020-10-01', '30','holidays in  2020' ,1)",
	),
),
'stringposition' => array(
	'name' => 'stringposition(haystack,needle)',
	'desc' => 'This function allows you to Find the position of the first occurrence of a substring in a string.',
	'params' => array(
		array(
			'name' => 'haystack',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies the string to search',
		),
		array(
			'name' => 'needle',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies the string to find',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"stringposition('abc','a')",
	),
),
'stringlength' => array(
	'name' => 'stringlength(string)',
	'desc' => 'This function returns the length of a string.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies the string to check',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"stringlength('Strings')",
	),
),
'stringreplace' => array(
	'name' => 'stringreplace(search,replace,subject)',
	'desc' => 'This function returns a string or an array with all occurrences of search in subject replaced with the given replace value.',
	'params' => array(
		array(
			'name' => 'search',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The value being searched for',
		),
		array(
			'name' => 'replace',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The replacement value that replaces found search values',
		),
		array(
			'name' => 'subject',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string  being searched and replaced on',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"stringreplace('ERICA','JON','MIKE AND ERICA ')",//wants to replace erica with jon
	),
),
'power(base,exponential)' => array(
	'name' => 'power(base,exponential)',
	'desc' => 'This function is used to calculate the power of any digit such as calculating squares and cube on integer fields.',
	'params' => array(
		array(
			'name' => 'base',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'The base to exponent',
		),
		array(
			'name' => 'exponential',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'The number of the exponent to base',
		),
	),
	'categories' => array('Matrix'),
	'examples' => array(
		power(2, 3),
	),
),
'substring' => array(
	'name' => 'substring(stringfield,start,length)',
	'desc' => 'This function returns the portion of Stringfield specified by the start and length parameters.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The input string',
		),
		array(
			'name' => 'start',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'Specifies where to start in the string,0 Start at the first character in string,A positive number Start at a specified position in the string,A negative number Start at a specified position from the end of the string',
		),
		array(
			'name' => 'length',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'Specifies the length of the returned string,If the length parameter is 0, NULL, or FALSE - it return an empty string',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'substring("Hello world",1,8)',
	),
),
'uppercase' => array(
	'name' => 'uppercase(stringfield)',
	'desc' => 'This function converts a specified string to uppercase.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert to uppercase',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercase('hello world')",
	),
),
'lowercase' => array(
	'name' => 'lowercase(stringfield)',
	'desc' => 'This function converts a specified string to lowercase.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert to lowercase',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"lowercase('HELLO WORLD')",
	),
),
'uppercasefirst' => array(
	'name' => 'uppercasefirst(stringfield)',
	'desc' => 'This function converts the first character of each word in a string to uppercase.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert each first character to uppercase',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercasefirst('hello world')",
	),
),
'num2str' => array(
	'name' => 'num2str(number|field, language)',
	'desc' => 'This function converts a number into a string.',
	'params' => array(
		array(
			'name' => 'number|field',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'valid number or field name',
		),
		array(
			'name' => 'language',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The language you need to convert the number',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"num2str('2017.34','en')",
	),
),
'number_format' => array(
	'name' => 'number_format(number, decimals, decimal_separator, thousands_separator)',
	'desc' => 'This function formats a number with grouped thousands  ie This function supports 1, 2, or 4 parameters (not 3).',
	'params' => array(
		array(
			'name' => 'number',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'The number to be formatted. If no other parameters are set, the number will be formatted without decimals and with comma (,) as the thousands separator',
		),
		array(
			'name' => 'decimals',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies how many decimals. If this parameter is set, the number will be formatted with a dot (.) as decimal point',
		),
		array(
			'name' => 'decimal_separator',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies what string to use for a decimal point',
		),
		array(
			'name' => 'thousands_separator',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies what string to use for thousands separator ie If this parameter is given, all other parameters are required as well',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		number_format("1000000", 2, ",", "."),
		number_format("1000000", 2),
	),
),
'translate' => array(
	'name' => 'translate(string|field)',
	'desc' => 'This function translates a given string.',
	'params' => array(
		array(
			'name' => 'string|field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any string or field name to translate',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"translate('digit_count')",
		"translate('this is my string')"
	),
),
'round' => array(
	'name' => 'round(numericfield,decimals)',
	'desc' => 'This function rounds a floating-point number.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'The value to round',
		),
		array(
			'name' => 'decimals',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'the number of decimal digits to round to',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'round(7.045,2)'
	),
),
'ceil' => array(
	'name' => 'ceil(numericfield)',
	'desc' => 'This function rounds a number UP to the nearest integer.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'The value to round up',
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
	'desc' => 'This function rounds a number DOWN to the nearest integer.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'The number to round down',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"floor(0.60)",
		"floor(-5.1)",
	),
),
'modulo' => array(
	'name' => 'modulo(numericfield)',
	'desc' => 'This function returns the remainder of numericfield.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'value or field for modulo ',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"modulo(5 % 3)",
	),
),
'hash' => array(
	'name' => 'hash(field, method)',
	'desc' => 'This function generates a hash value (message digest).',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..)',
		),
		array(
			'name' => 'method',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Message to be hashed',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"hash('sha256', 'admin')",
	),
),
'globalvariable' => array(
	'name' => 'globalvariable(gvname)',
	'desc' => 'This function converts number into a string.',
	'params' => array(
		array(
			'name' => 'gvname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any globalvariable name',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"globalvariable('adb')",
	),
),
'aggregation' => array(
	'name' => 'aggregation(operation,RelatedModule,relatedFieldToAggregate,conditions)',
	'desc' => 'This is a function where the values of multiple rows are grouped together to form a single summary value.',
	'params' => array(
		array(
			'name' => 'operation',
			'type' => 'String',
			'optional' => false,
			'desc' => 'operation name for aggregation.eg SUM',
		),
		array(
			'name' => 'RelatedModule',
			'type' => 'String',
			'optional' => false,
			'desc' => 'module for aggeration',
		),
		array(
			'name' => 'relatedFieldToAggregate',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any field related to aggregation',
		),
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'condition used for that aggregation',
		),
	),
	'categories' => array('aggregation'),
	'examples' => array(
		"aggregation('min','CobroPago','amount')",
		"aggregation('count','SalesOrder','*','[duedate,h,2018-01-01]')"
	),
),
'aggregation_fields_operation' => array(
	'name' => 'aggregation_fields_operation(operation,RelatedModule,relatedFieldsToAggregateWithOperation,conditions)',
	'desc' => 'This is a function which aggregate operation with a related module, related field to operation and condition.',
	'params' => array(
		array(
			'name' => 'operation',
			'type' => 'String',
			'optional' => false,
			'desc' => 'operation name for aggregation.eg SUM',
		),
		array(
			'name' => 'RelatedModule',
			'type' => 'String',
			'optional' => false,
			'desc' => 'module for aggeration',
		),
		array(
			'name' => 'relatedFieldsToAggregateWithOperation',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any field related to aggregate with the operation',
		),
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'condition used for aggregation',
		),
	),
	'categories' => array('aggregation'),
	'examples' => array(
		"aggregation_fields_operation('sum','InventoryDetails','quantity*listprice')",
	),
),
'getCurrentUserID' => array(
	'name' => 'getCurrentUserID()',
	'desc' => 'This function returns the current user ID.',
	'params' => array(
	),
	'categories' => array('user'),
	'examples' => array(
		"getCurrentUserID()",
	),
),
'getCurrentUserName' => array(
	'name' => 'getCurrentUserName({full})',
	'desc' => 'This function returns the current user name in full.',
	'params' => array(
	),
	'categories' => array('user'),
	'examples' => array(
		"getCurrentUserName('full')"
	),
),
'getCurrentUserField' => array(
	'name' => 'getCurrentUserField(fieldname)',
	'desc' => 'This function returns the current user of a fieldname eg.current user email.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any fieldname or field type name',
		),
	),
	'categories' => array('user'),
	'examples' => array(
		"getCurrentUserField('email1')",
	),
),
'getEntityType' => array(
	'name' => 'getEntityType(field)',
	'desc' => 'This function Converts a number into string.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any field or field type',
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
	'desc' => 'This function returns image url.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any field for image url',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getimageurl('placeone')",
	),
),
'getLatitude' => array(
	'name' => 'getLatitude(address)',
	'desc' => 'This function returns latitude of a gven address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Address to find latitude',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		"getLatitude(address)",
	),
),
'getLongitude' => array(
	'name' => 'getLongitude(address)',
	'desc' => 'This function returns longitude of a given address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Address to find longitude',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		"getLongitude(address)",
	),
),
'getGEODistance' => array(
	'name' => 'getGEODistance(address_from,address_to)',
	'desc' => 'This function returns the distance from one address to another.',
	'params' => array(
		array(
			'name' => 'address_from',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Address from',
		),
		array(
			'name' => 'address_to',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Address to',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		"getGEODistance(address_from,address_to)"
	),
),
'getGEODistanceFromCompanyAddress' => array(
	'name' => 'getGEODistanceFromCompanyAddress(address)',
	'desc' => 'This function returns the distance from company address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Company address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		"getGEODistanceFromCompanyAddress(address)"
	),
),
'getGEODistanceFromUserAddress' => array(
	'name' => 'getGEODistanceFromUserAddress(address)',
	'desc' => 'This function returns the distance from user adress.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'User address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		"getGEODistanceFromUserAddress(address)',",
	),
),
'getGEODistanceFromUser2AccountBilling' => array(
	'name' => 'getGEODistanceFromUser2AccountBilling(account,address_specification)',
	'desc' => 'This function Calculate distance from user to Accountbilling address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Account billing',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of user billing address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromUser2AccountBilling(account,address_specification)'
	),
),
'getGEODistanceFromAssignUser2AccountBilling' => array(
	'name' => 'getGEODistanceFromAssignUser2AccountBilling(account,assigned_user,address_specification)',
	'desc' => 'This function Calculate distance from Assign user to Accountbilling address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Billing Account',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'AssignUser',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of Assign user  billing address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromAssignUser2AccountBilling(account,assigned_user,address_specification)'
	),
),
'getGEODistanceFromUser2AccountShipping' => array(
	'name' => 'getGEODistanceFromUser2AccountShipping(account,address_specification)',
	'desc' => 'This function Calculate distance from user to AccountShipping address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Shipping Account',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of user shipping address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromUser2AccountShipping(account,address_specification)'
	),
),
'getGEODistanceFromAssignUser2AccountShipping' => array(
	'name' => 'getGEODistanceFromAssignUser2AccountShipping(account,assigned_user,address_specification)',
	'desc' => 'This function Calculate distance from Assign User to AccountShipping address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Shipping Account',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'AssignUser',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of user Shipping address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromAssignUser2AccountShipping(account,assigned_user,address_specification)'
	),
),
'getGEODistanceFromUser2ContactBilling' => array(
	'name' => 'getGEODistanceFromUser2ContactBilling(contact,address_specification)',
	'desc' => 'This function Calculate distance from user to ContactBilling address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Billing contact',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of user billing address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromUser2ContactBilling(contact,address_specification)',
	),
),
'getGEODistanceFromAssignUser2ContactBilling' => array(
	'name' => 'getGEODistanceFromAssignUser2ContactBilling(contact,assigned_user,address_specification)',
	'desc' => 'This function Calculate distance from Assign user to ContactBilling address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Billing contact',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'AssignUser',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of Assign user billing address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromAssignUser2ContactBilling(contact,assigned_user,address_specification)',
	),
),
'getGEODistanceFromUser2ContactShipping' => array(
	'name' => 'getGEODistanceFromUser2ContactShipping(contact,address_specification)',
	'desc' => 'This function Calculate distance from user to ContactShipping address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Billing contact',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of user shipping address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromUser2ContactShipping(contact,address_specification)',
	),
),
'getGEODistanceFromAssignUser2ContactShipping' => array(
	'name' => 'getGEODistanceFromAssignUser2ContactShipping(contact,assigned_user,address_specification)',
	'desc' => 'This function Calculate distance from Assign user to ContactShipping address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Billing contact address',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'AssignUser',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specification of Assign user shipping address',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromAssignUser2ContactShipping(contact,assigned_user,address_specification)',
	),
),
'getGEODistanceFromCoordinates' => array(
	'name' => 'getGEODistanceFromCoordinates({lat1},{long1},{lat2},{long2})',
	'desc' => 'This function Calculate distance from coordinates such as latitude and longitudes.',
	'params' => array(
		array(
			'name' => '{lat1}',
			'type' => 'String',
			'optional' => false,
			'desc' => 'latitude one',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'longitude one',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'latitude two',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'longitude two',
		),
	),
	'categories' => array('geodistance'),
	'examples' => array(
		'getGEODistanceFromCoordinates({lat1},{long1},{lat2},{long2})',
	),
),
'getFromContext' => array(
	'name' => 'getFromContext(variablename)',
	'desc' => 'This function get a context from a variablename.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'String',
			'optional' => false,
			'desc' => 'variablename to get context from',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getFromContext('ID')",
	),
),
'setToContext' => array(
	'name' => 'setToContext(variablename, value)',
	'desc' => 'This function set a context to a variablename with its value.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'String',
			'optional' => false,
			'desc' => 'variablename to set context',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value to set in a context',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setToContext('accountname','mortein')",
	),
),
'jsonEncode' => array(
	'name' => 'jsonEncode(field)',
	'desc' => 'This function Encode JSON of a field.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field to encode to JSON',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"jsonEncode('accountname')",
	),
),
'jsonDecode' => array(
	'name' => 'jsonDecode(field)',
	'desc' => 'This function return Decode JSON of a field.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field to decode in JSON',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"jsonDecode(field)",
	),
),
'implode' => array(
	'name' => 'implode(delimiter, field)',
	'desc' => 'This function returns a string from the elements of an array.',
	'params' => array(
		array(
			'name' => 'delimiter',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies what to put between the array elements. Default is "" (an empty string)',
		),
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field  array to join to a string',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		'implode("", array("lastname", "email", "phone"))',
	),
),
'explode' => array(
	'name' => 'explode(delimiter, field)',
	'desc' => 'This function Returns an array of strings, each of which is a substring of field formed by splitting it on boundaries formed by the string delimiter.',
	'params' => array(
		array(
			'name' => 'delimiter',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies where to break the string',
		),
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to split',
		),
	),
	'categories' => array('Strings'),
	'examples' => array(
		"explode(',', 'hello,there')",
	),
),
'sendMessage' => array(
	'name' => 'sendMessage(message, channel, time)',
	'desc' => 'This function sends  a message to a channel in provided time.',
	'params' => array(
		array(
			'name' => 'message',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The body of the message',
		),
		array(
			'name' => 'channel',
			'type' => 'String',
			'optional' => false,
			'desc' => 'channel to send a message',
		),
		array(
			'name' => 'time',
			'type' => 'String',
			'optional' => false,
			'desc' => 'time to send a message',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"sendMessage(message, channel, time)",
	),
),
'readMessage' => array(
	'name' => 'readMessage(channel)',
	'desc' => 'This function allows reading message in a channel.',
	'params' => array(
		array(
			'name' => 'channel',
			'type' => 'String',
			'optional' => false,
			'desc' => 'channel to read a message',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"readMessage(channel)",
	),
),
'getSetting' => array(
	'name' => 'getSetting("setting_key", "default")',
	'desc' => 'This function allows to get a setting key with it default.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'String',
			'optional' => false,
			'desc' => 'setting key',
		),
		array(
			'name' => 'default',
			'type' => 'String',
			'optional' => true,
			'desc' => 'default of setting key',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getSetting('KEY_ACCESSTOKEN', '')",
	),
),
'setSetting' => array(
	'name' => 'setSetting("setting_key", "default")',
	'desc' => 'This function allows to set a setting key with it default value.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'String',
			'optional' => false,
			'desc' => 'setting key',
		),
		array(
			'name' => 'default',
			'type' => 'String',
			'optional' => true,
			'desc' => 'The default value of setting key',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setSetting('hubspot_pollsyncing', 'creating')",
	),
),
'delSetting' => array(
	'name' => 'delSetting("setting_key")',
	'desc' => 'This function delete  setting.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'String',
			'optional' => false,
			'desc' => 'setting key to delete',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setting_key('hubspot_pollsyncing')",
	),
),
'evaluateRule' => array(
	'name' => 'evaluateRule(ruleID)',
	'desc' => 'This function evaluate rule of a rule ID.',
	'params' => array(
		array(
			'name' => 'ruleID',
			'type' => 'String',
			'optional' => false,
			'desc' => 'rule id to excute',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"evaluateRule(ruleID)",
	),
),
'getCRUDMode' => array(
	'name' => 'getCRUDMode()',
	'desc' => 'This function return create, read, update and delete mode.',
	'params' => array(
	),
	'categories' => array('Application'),
	'examples' => array(
		"getCRUDMode()",
	),
),
'isNumeric' => array(
	'name' => 'isNumeric(fieldname)',
	'desc' => 'This function checks if a fieldname is numeric.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to look if its numeric',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"isNumeric(accountname)",
	),
),
'isString' => array(
	'name' => 'isString(fieldname)',
	'desc' => 'This function checks if fieldname is string.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to check if its string',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"isString(account_id)',",
	),
),
'OR' => array(
	'name' => 'OR(condition1, condition2)',
	'desc' => 'This function returns true if any of the provided conidtion are logically true, and false if all of the provided condition are logically false.',
	'params' => array(
		array(
			'name' => 'condition1',
			'type' => 'String',
			'optional' => false,
			'desc' => 'first condition',
		),
		array(
			'name' => 'condition2',
			'type' => 'String',
			'optional' => false,
			'desc' => 'second condition',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"OR(isString($(account_id : (Accounts) accountname)), isNumeric($(account_id : (Accounts) bill_code)))"
	),
),
'AND' => array(
	'name' => 'AND(condition1, condition2)',
	'desc' => 'This function returns true if any of the provided conidtion are both logically true, and false if all of the provided condition are logically false.',
	'params' => array(
		array(
			'name' => 'condition1',
			'type' => 'String',
			'optional' => false,
			'desc' => 'first condition',
		),
		array(
			'name' => 'condition2',
			'type' => 'String',
			'optional' => false,
			'desc' => 'second condition',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"AND(isString($(account_id : (Accounts) accountname)), isNumeric($(account_id : (Accounts) accounttype)))"
	),
),
'NOT' => array(
	'name' => 'NOT(condition1)',
	'desc' => 'This function returns the opposite of a logical value - `NOT(TRUE)` returns `FALSE`; `NOT(FALSE)` returns `TRUE`.',
	'params' => array(
		array(
			'name' => 'condition1',
			'type' => 'String',
			'optional' => false,
			'desc' => 'condition for NOT',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"NOT(isString($(account_id : (Accounts) accountname)))",
	),
),
'regex' => array(
	'name' => 'regex(pattern, subject)',
	'desc' => 'This function return regex of a given pattern and subject.',
	'params' => array(
		array(
			'name' => 'pattern',
			'type' => 'String',
			'optional' => false,
			'desc' => 'patten to subject',
		),
		array(
			'name' => 'subject',
			'type' => 'String',
			'optional' => false,
			'desc' => 'subject to patten',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"regex(numberRegex, msg )",
	),
),
'exists' => array(
	'name' => 'exists(fieldname, value)',
	'desc' => 'This function checks if a record exists or not exists.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to check for its existence',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value of a fieldname',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"exists('accountname', 'Chemex Labs Ltd')",
	),
),
'existsrelated' => array(
	'name' => 'existsrelated(relatedmodule, fieldname, value)',
	'desc' => 'This function check if the record exist to related  module.',
	'params' => array(
		array(
			'name' => 'relatedmodule',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related module',
		),
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to look for its existence',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value of the fieldname',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"existsrelated(Contacts, accountname, Chemex Labs Ltd)",
	),
),
'allrelatedare' => array(
	'name' => 'allrelatedare(relatedmodule, fieldname, value)',
	'desc' => 'This function checks all related  record if exist to  related  module.',
	'params' => array(
		array(
			'name' => 'relatedmodule',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related module',
		),
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to look for its existence',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value of the fieldname',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"allrelatedare(Contacts, accountname, Chemex Labs Ltd)",
	),
),
'min' => array(
	'name' => 'min(fieldname)',
	'desc' => 'This function returns minimum of a fielname.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to ruturn its minimum',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"min(sum_nettotal)",
	),
),
'max' => array(
	'name' => 'max(fieldname)',
	'desc' => 'This function returns maximum of a fielname.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fieldname to ruturn its maximum',
		),
	),
	'categories' => array('Logicalops'),
	'examples' => array(
		"max(employees)",
	),
),


);

foreach (glob('modules/com_vtiger_workflow/language/en_us.fndefs.*.php', GLOB_BRACE) as $tcode) {
	include $tcode;
}
