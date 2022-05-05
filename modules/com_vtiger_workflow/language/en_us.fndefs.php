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
 # translators: translate 'desc' and 'type' entries only
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
	'desc' => 'Returns the first non-empty value found in the list of parameters',
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
			'optional' => true,
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
			'desc' => 'if set to 0, Saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an Information Map that contains the holiday dates to exclude<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
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
			'desc' => 'any valid date or date type field name, today will be used if not given',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => true,
			'desc' => 'name of an Information Map that contains the holiday dates to exclude<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
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
	'desc' => 'Returns true if the given date falls on a holiday, Sunday or Saturday. If Saturday is considered a holiday or not can be defined.',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'any valid date or date type field name',
		),
		array(
			'name' => 'saturdayisholiday',
			'type' => 'Boolean',
			'optional' => false,
			'desc' => 'if set to 1, Saturdays will be considered as non-work days (like Sunday)',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => true,
			'desc' => 'comma-separated list of holidays or the name of an Information Map that contains the holiday dates<br>'.nl2br(htmlentities("<map>\n<information>\n<infotype>Holidays in France 2020</infotype>\n<value>date1</value>\n<value>date2</value>\n</information>\n</map>")).'</pre>',
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
	'desc' => 'This function returns an aggregate time of a field on a related module with optional filtering of the records',
	'params' => array(
		array(
			'name' => 'relatedModuleName',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related module to aggregate',
		),
		array(
			'name' => 'relatedModuleField',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related field name to aggregate',
		),
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'optional condition used to filter the records: [field,op,value,glue],[...]',
		),
	),
	'categories' => array('Statistics'),
	'examples' => array(
		"aggregate_time('InventoryDetails','quantity*listprice')"
	),
),
'add_days' => array(
	'name' => 'add_days(datefield, noofdays)',
	'desc' => 'Compute a new date based on a given date with a specified number of days added',
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
	'desc' => 'Compute a working days date based on a given date with a specified number of days, Saturdays and holidays added',
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
			'desc' => 'if set to 0, Saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an Information Map that contains the holiday dates to exclude',
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
	'desc' => 'Compute a new date based on a given date with a specified number of days deducted',
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
	'desc' => 'Compute a new working days date based on a given date with a specified number of days, Saturday and holiday deducted',
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
			'desc' => 'if set to 0, Saturdays will not be added, if set to any other value, they will be added',
		),
		array(
			'name' => 'holidays',
			'type' => 'String',
			'optional' => false,
			'desc' => 'name of an Information Map that contains the holiday dates to exclude',
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
	'desc' => 'Compute a new date based on a given date with a specified number of months added',
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
	'desc' => 'Compute a new date based on a given date with a specified number of months deducted',
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
		"sub_months('2020-10-01', 5)",
	),
),
'add_time' => array(
	'name' => 'add_time(timefield, minutes)',
	'desc' => 'Compute a new time based on a given time, with the specified minutes added',
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
		"add_time(start_time, 180)",
		"add_time('12:00', 40)",
	),
),
'sub_time' => array(
	'name' => 'sub_time(timefield, minutes)',
	'desc' => 'Compute a new time based on a given time, with the specified minutes deducted',
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
		"sub_time(start_time, 90)",
		"sub_time('12:00', 90)",
	),
),
'today' => array(
	'name' => "get_date('today')",
	'desc' => 'This function returns the current date as a date value',
	'params' => array(
		array(
			'name' => 'today',
			'type' => 'String',
			'optional' => true,
			'desc' => 'the string today',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('today')'",
	),
),
'today' => array(
	'name' => "get_date('now')",
	'desc' => 'This function returns the current date-time as a date value',
	'params' => array(
		array(
			'name' => 'now',
			'type' => 'String',
			'optional' => true,
			'desc' => 'the string now',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('now')",
	),
),
'tomorrow' => array(
	'name' => "get_date('tomorrow')",
	'desc' => 'This function returns tomorrow date as a date value',
	'params' => array(
		array(
			'name' => 'tomorrow',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the word tomorrow',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('tomorrow')",
	),
),
'yesterday' => array(
	'name' => "get_date('yesterday')",
	'desc' => 'This function returns yesterday date as a date value.',
	'params' => array(
		array(
			'name' => 'yesterday',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the word yesterday',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('yesterday')",
	),
),
'time' => array(
	'name' => "get_date('time')",
	'desc' => 'This function returns the current time.',
	'params' => array(
		array(
			'name' => 'time',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the word time',
		),
	),
	'categories' => array('Date and Time'),
	'examples' => array(
		"get_date('time')"
	),
),
'format_date' => array(
	'name' => 'format_date(date,format)',
	'desc' => 'This function applies a specific format to a date.',
	'params' => array(
		array(
			'name' => 'date',
			'type' => 'Date',
			'optional' => false,
			'desc' => 'the date you need to format',
		),
		array(
			'name' => 'format',
			'type' => 'String',
			'optional' => false,
			'desc' => 'PHP date format specification',
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
	'desc' => 'Compute a next date based on a given date with specified days, Saturday and holiday excluded',
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
			'optional' => true,
			'desc' => 'name of an Information Map that contains the holiday dates to include',
		),
		array(
			'name' => 'include_weekend',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'if set to 0, weekend will not be added, if set to any other value, they will be included',
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
	'desc' => 'Compute a next working date based on a given date with specified days, Saturday and holiday excluded',
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
			'optional' => true,
			'desc' => 'name of an Information Map that contains the holiday dates to include',
		),
		array(
			'name' => 'saturday_laborable',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'if set to 0, weekend will not be added, if set to any other value, they will be included',
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
	'desc' => 'This function allows you to find the position of the first occurrence of a substring in a string.',
	'params' => array(
		array(
			'name' => 'haystack',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies the string to search in',
		),
		array(
			'name' => 'needle',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies the string to find',
		),
	),
	'categories' => array('Text'),
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
			'desc' => 'Specifies the string to measure',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"stringlength('Strings')",
	),
),
'stringreplace' => array(
	'name' => 'stringreplace(search,replace,subject)',
	'desc' => 'This function returns a string with all occurrences of search in subject replaced with the given replace value.',
	'params' => array(
		array(
			'name' => 'search',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the value being searched for',
		),
		array(
			'name' => 'replace',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the replacement value',
		),
		array(
			'name' => 'subject',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the string being searched and replaced on',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"stringreplace('ERICA','JON','MIKE AND ERICA ')  //wants to replace erica with jon",
	),
),
'regexreplace' => array(
	'name' => 'regexreplace(pattern,replace,subject)',
	'desc' => 'This function returns a string with all occurrences of regex pattern in subject replaced with the given replace value.',
	'params' => array(
		array(
			'name' => 'pattern',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the regex pattern being searched for',
		),
		array(
			'name' => 'replace',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the replacement value',
		),
		array(
			'name' => 'subject',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the string being searched and replaced on',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"regexreplace('[A-za-z]+','J','MIKE AND ERICA ')  //will return all Js"
	),
),
'randomstring' => array(
	'name' => 'randomstring(length)',
	'desc' => 'This function returns a random string of the given length.',
	'params' => array(
		array(
			'name' => 'length',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'number of random characters to return',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'randomstring(12)  // 02E373931343',
	),
),
'power' => array(
	'name' => 'power(base, exponential)',
	'desc' => 'This function is used to calculate the power of any number such as calculating squares and cube on integer fields.',
	'params' => array(
		array(
			'name' => 'base',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'the base to exponent',
		),
		array(
			'name' => 'exponential',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'the number of exponent to base',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'power(2, 3)',
	),
),
'log' => array(
	'name' => 'log(number, base)',
	'desc' => 'This function is used to calculate the logarithm of any number with the given base.',
	'params' => array(
		array(
			'name' => 'number',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'the number to logarithm',
		),
		array(
			'name' => 'base',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'logarithm base, if not given the natural logarithm will be used',
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
	'desc' => 'This function returns the portion of stringfield specified by the start and length parameters.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string from which to extract the substring',
		),
		array(
			'name' => 'start',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'Specifies where to start in the string, 0 is the first character in the string. A negative number counts backward from the end of the string',
		),
		array(
			'name' => 'length',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'Specifies the length of the returned string. If the length parameter is 0, NULL, or FALSE it returns an empty string',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		'substring("Hello world",1,8)',
	),
),
'uppercase' => array(
	'name' => 'uppercase(stringfield)',
	'desc' => 'This function converts a specified string to upper case.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert to upper case',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercase('hello world')",
	),
),
'lowercase' => array(
	'name' => 'lowercase(stringfield)',
	'desc' => 'This function converts a specified string to lower case.',
	'params' => array(
		array(
			'name' => 'string',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert to lower case',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"lowercase('HELLO WORLD')",
	),
),
'uppercasefirst' => array(
	'name' => 'uppercasefirst(stringfield)',
	'desc' => 'This function converts the first character of the given string to upper case.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert first character to upper case',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercasefirst('hello world')",
	),
),
'uppercasewords' => array(
	'name' => 'uppercasewords(stringfield)',
	'desc' => 'This function converts the first character of each word in a string to upper case.',
	'params' => array(
		array(
			'name' => 'stringfield',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to convert each first character to upper case',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"uppercasewords('hello world')",
	),
),
'num2str' => array(
	'name' => 'num2str(number|field, language)',
	'desc' => 'This function converts a number into its textual representation.',
	'params' => array(
		array(
			'name' => 'number|field',
			'type' => 'Number',
			'optional' => false,
			'desc' => 'valid number or field name',
		),
		array(
			'name' => 'language',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The language you want the textual representation in',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"num2str('2017.34','en')",
	),
),
'number_format' => array(
	'name' => 'number_format(number, decimals, decimal_separator, thousands_separator)',
	'desc' => 'This function formats a number with grouped thousands.',
	'params' => array(
		array(
			'name' => 'number',
			'type' => 'Number',
			'optional' => false,
			'desc' => 'The number to be formatted. If no other parameters are set, the number will be formatted without decimals and with a comma (,) as the thousands separator',
		),
		array(
			'name' => 'decimals',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies how many decimals. If only this parameter is set, the number will be formatted with a dot (.) as a decimal point',
		),
		array(
			'name' => 'decimal_separator',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies the character to use as a decimal point',
		),
		array(
			'name' => 'thousands_separator',
			'type' => 'String',
			'optional' => true,
			'desc' => 'Specifies the character to use as thousands separator',
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
	'desc' => 'This function translates a given string.',
	'params' => array(
		array(
			'name' => 'string|field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any string or field name to translate',
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
	'desc' => 'This function rounds a floating-point number.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Number',
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
		'round(7045.2)'
	),
),
'ceil' => array(
	'name' => 'ceil(numericfield)',
	'desc' => 'This function rounds a number UP to the nearest integer.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Number',
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
			'type' => 'Number',
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
	'name' => 'modulo(numericfield,numericfield)',
	'desc' => 'This function returns the remainder of the division of the parameters.',
	'params' => array(
		array(
			'name' => 'numericfield',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'value or field',
		),
		array(
			'name' => 'numericfield',
			'type' => 'Integer',
			'optional' => false,
			'desc' => 'value or field',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"modulo(5, 3)",
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
			'desc' => 'Message to be hashed',
		),
		array(
			'name' => 'method',
			'type' => 'String',
			'optional' => false,
			'desc' => 'selected hashing algorithm: "md5", "sha1", "crc32"',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"hash('admin', 'sha1')",
	),
),
'globalvariable' => array(
	'name' => 'globalvariable(gvname)',
	'desc' => 'Returns the value of a global variable.',
	'params' => array(
		array(
			'name' => 'gvname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any global variable name',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"globalvariable('Application_ListView_PageSize')",
	),
),
'aggregation' => array(
	'name' => 'aggregation(operation, RelatedModule, relatedFieldToAggregate, conditions)',
	'desc' => 'Multiple rows are grouped together to form a single summary value of a field.',
	'params' => array(
		array(
			'name' => 'operation',
			'type' => 'String',
			'optional' => false,
			'desc' => 'sum, min, max, avg, count, std, variance, group_concat, time_to_sec',
		),
		array(
			'name' => 'RelatedModule',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related module for aggregation',
		),
		array(
			'name' => 'relatedFieldToAggregate',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related field to aggregate',
		),
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'optional condition used to filter the records: [field,op,value,glue],[...] Note that the evaluation of the value is done with a simple render that does not support functions, if you need to use workflow expression language you must add a parameter with the word "expression" to force the evaluation of the value as an expression.',
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
	'desc' => 'Multiple rows are grouped together to form a single summary value on an operation of fields.',
	'params' => array(
		array(
			'name' => 'operation',
			'type' => 'String',
			'optional' => false,
			'desc' => 'sum, min, max, avg, count, std, variance, group_concat, time_to_sec',
		),
		array(
			'name' => 'RelatedModule',
			'type' => 'String',
			'optional' => false,
			'desc' => 'related module for aggregation',
		),
		array(
			'name' => 'relatedFieldsToAggregateWithOperation',
			'type' => 'String',
			'optional' => false,
			'desc' => 'SQL expression to execute and aggregate',
		),
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'optional condition used to filter the records: [field,op,value,glue],[...]',
		),
	),
	'categories' => array('Statistics'),
	'examples' => array(
		"aggregation_fields_operation('sum','InventoryDetails','quantity*listprice')",
	),
),
'getCurrentUserID' => array(
	'name' => 'getCurrentUserID()',
	'desc' => 'This function returns the current user ID.',
	'params' => array(
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentUserID()",
	),
),
'getCurrentUserName' => array(
	'name' => 'getCurrentUserName({full})',
	'desc' => 'This function returns the current user name.',
	'params' => array(
		array(
			'name' => 'full',
			'type' => 'String',
			'optional' => true,
			'desc' => 'the string full, if given, the full name will be returned (first and last) instead of the application user name',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentUserName('full')"
	),
),
'getCurrentUserField' => array(
	'name' => 'getCurrentUserField(fieldname)',
	'desc' => 'This function returns a field value of the current user',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any user field name',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentUserField('email1')",
	),
),
'getCRMIDFromWSID' => array(
	'name' => 'getCRMIDFromWSID(id)',
	'desc' => 'This function returns the id of a record',
	'params' => array(
		array(
			'name' => 'id',
			'type' => 'string(WSID)',
			'optional' => false,
			'desc' => 'ID of record in web service format',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getCRMIDFromWSID('33x2222')",
	),
),
'average' => array(
	'name' => 'average(number,...)',
	'desc' => 'This function returns the average from a list of numbers',
	'params' => array(
		array(
			'name' => 'number',
			'type' => 'Number',
			'optional' => false,
			'desc' => 'List of numbers',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		"average(1,2,3)",
),
),
'getEntityType' => array(
	'name' => 'getEntityType(field)',
	'desc' => 'This function returns the module name of the given ID.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any relation field',
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
	'desc' => 'This function returns the URL of the image contained in an image field.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'any image field',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getimageurl(placeone)",
	),
),
'getLatitude' => array(
	'name' => 'getLatitude(address)',
	'desc' => 'This function returns the latitude of a given address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Address to find the latitude',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getLatitude(address)",
	),
),
'getLongitude' => array(
	'name' => 'getLongitude(address)',
	'desc' => 'This function returns the longitude of a given address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Address to find the longitude',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getLongitude(address)",
	),
),
'getGEODistance' => array(
	'name' => 'getGEODistance(address_from, address_to)',
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
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistance(address_from, address_to)"
	),
),
'getGEODistanceFromCompanyAddress' => array(
	'name' => 'getGEODistanceFromCompanyAddress(address)',
	'desc' => 'This function returns the distance from the given address to the established company address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromCompanyAddress(address)"
	),
),
'getGEODistanceFromUserAddress' => array(
	'name' => 'getGEODistanceFromUserAddress(address)',
	'desc' => 'This function returns the distance from the given address to the established user address.',
	'params' => array(
		array(
			'name' => 'address',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUserAddress('address')",
	),
),
'getGEODistanceFromUser2AccountBilling' => array(
	'name' => 'getGEODistanceFromUser2AccountBilling(account, address_specification)',
	'desc' => 'This function calculates the distance from the user address to the account billing address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Account ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the billing address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2AccountBilling(account, 'address')"
	),
),
'getGEODistanceFromAssignUser2AccountBilling' => array(
	'name' => 'getGEODistanceFromAssignUser2AccountBilling(account, assigned_user, address_specification)',
	'desc' => 'This function calculates the distance from the assigned user to the account billing address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Account ID',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'User ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the billing address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2AccountBilling(account, assigned_user, 'address')"
	),
),
'getGEODistanceFromUser2AccountShipping' => array(
	'name' => 'getGEODistanceFromUser2AccountShipping(account, address_specification)',
	'desc' => 'This function calculates the distance from the current user to the account Shipping address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Account ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the shipping address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2AccountShipping(account, 'address')"
	),
),
'getGEODistanceFromAssignUser2AccountShipping' => array(
	'name' => 'getGEODistanceFromAssignUser2AccountShipping(account, assigned_user, address_specification)',
	'desc' => 'This function Calculate distance from Assign User to AccountShipping address.',
	'params' => array(
		array(
			'name' => 'account',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Account ID',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'User ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the shipping address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2AccountShipping(account, assigned_user, 'address')"
	),
),
'getGEODistanceFromUser2ContactBilling' => array(
	'name' => 'getGEODistanceFromUser2ContactBilling(contact, address_specification)',
	'desc' => 'This function calculates the distance from the user to the contact Billing address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Contact ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the billing address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2ContactBilling(contact, 'address')",
	),
),
'getGEODistanceFromAssignUser2ContactBilling' => array(
	'name' => 'getGEODistanceFromAssignUser2ContactBilling(contact, assigned_user, address_specification)',
	'desc' => 'This function calculates the distance from the assigned user to the contact Billing address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Contact ID',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'User ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the billing address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2ContactBilling(contact, assigned_user, 'address')",
	),
),
'getGEODistanceFromUser2ContactShipping' => array(
	'name' => 'getGEODistanceFromUser2ContactShipping(contact, address_specification)',
	'desc' => 'This function calculates the distance from a user to a contact Shipping address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Contact ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the shipping address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromUser2ContactShipping(contact, 'address')",
	),
),
'getGEODistanceFromAssignUser2ContactShipping' => array(
	'name' => 'getGEODistanceFromAssignUser2ContactShipping(contact, assigned_user, address_specification)',
	'desc' => 'This function calculates the distance from the assigned user to the contact Shipping address.',
	'params' => array(
		array(
			'name' => 'contact',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Contact ID',
		),
		array(
			'name' => 'assigned_user',
			'type' => 'String',
			'optional' => false,
			'desc' => 'User ID',
		),
		array(
			'name' => 'address_specification',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the shipping address',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getGEODistanceFromAssignUser2ContactShipping(contact, assigned_user, 'address')",
	),
),
'getGEODistanceFromCoordinates' => array(
	'name' => 'getGEODistanceFromCoordinates(lat1, long1, lat2, long2)',
	'desc' => 'This function calculates the distance between two coordinates.',
	'params' => array(
		array(
			'name' => 'lat1',
			'type' => 'String',
			'optional' => false,
			'desc' => 'latitude from',
		),
		array(
			'name' => 'long1',
			'type' => 'String',
			'optional' => false,
			'desc' => 'longitude from',
		),
		array(
			'name' => 'lat2',
			'type' => 'String',
			'optional' => false,
			'desc' => 'latitude to',
		),
		array(
			'name' => 'long2',
			'type' => 'String',
			'optional' => false,
			'desc' => 'longitude to',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		'getGEODistanceFromCoordinates(lat1, long1, lat2, long2)',
	),
),
'getIDof' => array(
	'name' => 'getIDof(module, searchon, searchfor)',
	'desc' => 'This function searches the given module for a record with the `searchfor` value in the `searchon` field and returns the ID of that record if found or 0 if not. The goal of this function is to set related field values in create/update tasks.',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the module name to search in.',
		),
		array(
			'name' => 'searchon',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field of the module to search in',
		),
		array(
			'name' => 'searchfor',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value to search for',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getIDof('Contacts', 'firstname', 'Amy')",
		"getIDof('Accounts', 'siccode', 'xyhdmsi33')",
		"getIDof('Accounts', 'siccode', some_field)",
	),
),
'getRelatedIDs' => array(
	'name' => 'getRelatedIDs(module, recordid)',
	'desc' => 'This function returns an array of record IDs in the given module, related to the record triggering the workflow',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the related module name to search in.',
		),
		array(
			'name' => 'recordid',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'the main record ID to get the related records from, if not given the current record triggering the workflow will be used',
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
	'desc' => 'Obtain a web service Mass Create JSON structure for the given recordid and its related module records',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the related module name to get records from',
		),
		array(
			'name' => 'recordid',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'the main record ID to get the related records from, if not given the current record triggering the workflow will be used',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedMassCreateArray('Contacts', 943)",
	),
),
'getRelatedMassCreateArrayConverting' => array(
	'name' => 'getRelatedMassCreateArrayConverting(module, MainModuleDestination, RelatedModuleDestination, recordid)',
	'desc' => 'Obtain a web service Mass Create JSON structure for the given recordid and its related module records applying conversion mappings',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the related module name to get records from',
		),
		array(
			'name' => 'MainModuleDestination',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Destination module for main module',
		),
		array(
			'name' => 'RelatedModuleDestination',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Destination module for the related modules',
		),
		array(
			'name' => 'recordid',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'the main record ID to get the related records from, if not given the current record triggering the workflow will be used',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedMassCreateArrayConverting('Contacts','Products','PurchaseOrder',943)",
	),
),
'getRelatedRecordCreateArrayConverting' => array(
	'name' => 'getRelatedRecordCreateArrayConverting(module, RelatedModuleDestination, recordid)',
	'desc' => 'Obtain a web service Master-Detail JSON structure for the given recordid and its related module records applying conversion mappings',
	'params' => array(
		array(
			'name' => 'module',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the related module name to get records from',
		),
		array(
			'name' => 'RelatedModuleDestination',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Destination module for the related modules',
		),
		array(
			'name' => 'recordid',
			'type' => 'Integer',
			'optional' => true,
			'desc' => 'the main record ID to get the related records from, if not given the current record triggering the workflow will be used',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getRelatedRecordCreateArrayConverting('Contacts','PurchaseOrder',943)",
	),
),
'getISODate' => array(
	'name' => 'getISODate(year, weeks, dayInWeek)',
	'desc' => 'Obtain DateTime in ISO format from given year, weeks and day in a week',
	'params' => array(
		array(
			'name' => 'year',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Year',
		),
		array(
			'name' => 'weeks',
			'type' => 'String',
			'optional' => false,
			'desc' => 'number of weeks',
		),
		array(
			'name' => 'dayInWeek',
			'type' => 'String',
			'optional' => false,
			'desc' => 'number of a day in a week (1-7)',
		)
	),
	'categories' => array('Application'),
	'examples' => array(
		"getISODate('2022','10','4',)",
	),
),
'getFieldsOF' => array(
	'name' => 'getFieldsOF(id, module, fields)',
	'desc' => 'Given the ID of an existent record, this function will return an array with all the values of the fields the user has access to. If you specify the fields you want in the function, only those values will be returned.',
	'params' => array(
		array(
			'name' => 'id',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the ID to search in',
		),
		array(
			'name' => 'module',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the module to search in',
		),
		array(
			'name' => 'fields',
			'type' => 'String',
			'optional' => true,
			'desc' => 'comma-separated list of fields to get the values from',
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
	'desc' => 'This function gets the value of the variablename context variable.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the variable name to get from context. dot syntax supported in variable names and more than one variable can be specified separating them with commas. If more than one variable is given a JSON encoded array with the values will be returned.',
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
	'desc' => 'This function gets the value of the returnthis context variable but searches for the correct entry in an array indicated by variablename. This function will traverse the variable in the context and arrive at an array, then it will search in the array for an element that has searchon property set to searchfor value, once found it will return the property indicated by `returnthis`. The array is supposed to contain objects or indexed arrays to search on.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the path to an array in context. dot syntax supported in variable names and more than one variable can be specified separating them with commas. If more than one variable is given a JSON encoded array with the values will be returned.',
		),
		array(
			'name' => 'searchon',
			'type' => 'String',
			'optional' => false,
			'desc' => 'property of the array element to search on',
		),
		array(
			'name' => 'searchfor',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value to search for',
		),
		array(
			'name' => 'returnthis',
			'type' => 'String',
			'optional' => false,
			'desc' => 'property of the array element to return',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getFromContextSearching('response.data.2.custom_fields', 'label', 'Servizio_di_portineria', 'fleet_data')",
	),
),
'setToContext' => array(
	'name' => 'setToContext(variablename, value)',
	'desc' => 'This function sets a value in a context variable.',
	'params' => array(
		array(
			'name' => 'variablename',
			'type' => 'String',
			'optional' => false,
			'desc' => 'variable to set in the context',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value to set',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setToContext('accountname','mortein')",
	),
),
'jsonEncode' => array(
	'name' => 'jsonEncode(field)',
	'desc' => 'This function JSON encodes the given variable.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field to encode to JSON',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"jsonEncode('accountname')",
	),
),
'jsonDecode' => array(
	'name' => 'jsonDecode(field)',
	'desc' => 'This function returns the JSON decode of a variable.',
	'params' => array(
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'variable to decode from JSON',
		),
	),
	'categories' => array('Text'),
	'examples' => array(
		"jsonDecode(field)",
	),
),
'implode' => array(
	'name' => 'implode(delimiter, field)',
	'desc' => 'This function returns a concatenation string from the elements of an array.',
	'params' => array(
		array(
			'name' => 'delimiter',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies what to put between the array elements. Default is an empty string',
		),
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'array field or variable to join',
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
	'desc' => 'This function returns an array of strings, each of which is a substring of field formed by splitting it on boundaries formed by the string delimiter.',
	'params' => array(
		array(
			'name' => 'delimiter',
			'type' => 'String',
			'optional' => false,
			'desc' => 'Specifies where to break the string',
		),
		array(
			'name' => 'field',
			'type' => 'String',
			'optional' => false,
			'desc' => 'The string to split',
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
	'desc' => 'This function sends a message to coreBOS message queue channel.',
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
			'desc' => 'channel to send the message to',
		),
		array(
			'name' => 'time',
			'type' => 'String',
			'optional' => false,
			'desc' => 'expire time of the message',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"sendMessage('message', 'somechannel', 90)",
	),
),
'readMessage' => array(
	'name' => 'readMessage(channel)',
	'desc' => 'This function reads a message from a coreBOS message queue channel.',
	'params' => array(
		array(
			'name' => 'channel',
			'type' => 'String',
			'optional' => false,
			'desc' => 'channel to read the message from',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"readMessage('somechannel')",
	),
),
'getSetting' => array(
	'name' => "getSetting('setting_key', 'default')",
	'desc' => 'This function reads a variable from the coreBOS key-value store, with a default if not found.',
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
			'desc' => 'value to return if key is not found',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"getSetting('KEY_ACCESSTOKEN', 'some default value')",
	),
),
'setSetting' => array(
	'name' => "setSetting('setting_key', value)",
	'desc' => 'This function allows to set a vaue in the coreBOS key-value store.',
	'params' => array(
		array(
			'name' => 'setting_key',
			'type' => 'String',
			'optional' => false,
			'desc' => 'setting key',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value to set in the key',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"setSetting('hubspot_pollsyncing', 'creating')",
	),
),
'delSetting' => array(
	'name' => 'delSetting("setting_key")',
	'desc' => 'This function deletes a key from the coreBOS key-value store.',
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
	'desc' => 'This function evaluates a coreBOS rule.',
	'params' => array(
		array(
			'name' => 'ruleID',
			'type' => 'String',
			'optional' => false,
			'desc' => 'the rule ID to execute',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"evaluateRule(ruleID)",
	),
),
'executeSQL' => array(
	'name' => 'executeSQL(query, parameters...)',
	'desc' => 'Execute an SQL statement.',
	'params' => array(
		array(
			'name' => 'query',
			'type' => 'String',
			'optional' => false,
			'desc' => 'a prepared SQL statement',
		),
		array(
			'name' => 'parameters',
			'type' => 'String',
			'optional' => true,
			'desc' => 'any number of parameters the SQL may need',
		),
	),
	'categories' => array('Application'),
	'examples' => array(
		"executeSQL('select siccode from vtiger_accounts where accountname=?', field)",
	),
),
'getCRUDMode' => array(
	'name' => 'getCRUDMode()',
	'desc' => 'This function returns create or edit depending on the action being done.',
	'params' => array(
	),
	'categories' => array('Application'),
	'examples' => array(
		"getCRUDMode()",
	),
),
'Importing' => array(
	'name' => 'Importing()',
	'desc' => 'This function returns true if the execution is inside an import process or false otherwise.',
	'params' => array(
	),
	'categories' => array('Application'),
	'examples' => array(
		"Importing()",
	),
),
'isNumeric' => array(
	'name' => 'isNumeric(fieldname)',
	'desc' => 'This function checks if a field is numeric.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field to evaluate',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"isNumeric(accountname)",
	),
),
'isString' => array(
	'name' => 'isString(fieldname)',
	'desc' => 'This function checks if field is a string.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field to check if it is a string',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"isString(account_id)",
	),
),
'OR' => array(
	'name' => 'OR(condition1, condition2, {conditions})',
	'desc' => 'This function returns true if any of the provided conditions are logically true, and false if all of the provided conditions are logically false.',
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
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'set of conditions',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"OR(isString($(account_id : (Accounts) accountname)), isNumeric($(account_id : (Accounts) bill_code)))"
	),
),
'AND' => array(
	'name' => 'AND(condition1, condition2, {conditions})',
	'desc' => 'This function returns true if all of the provided conditions are logically true, and false if any of the provided conditions are logically false.',
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
		array(
			'name' => 'conditions',
			'type' => 'String',
			'optional' => true,
			'desc' => 'set of conditions',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"AND(isString($(account_id : (Accounts) accountname)), isNumeric($(account_id : (Accounts) accounttype)))"
	),
),
'NOT' => array(
	'name' => 'NOT(condition)',
	'desc' => 'This function returns the opposite of a logical value - `NOT(TRUE)` returns `FALSE`; `NOT(FALSE)` returns `TRUE`.',
	'params' => array(
		array(
			'name' => 'condition',
			'type' => 'String',
			'optional' => false,
			'desc' => 'condition',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"NOT(isString($(account_id : (Accounts) accountname)))",
	),
),
'regex' => array(
	'name' => 'regex(pattern, subject)',
	'desc' => 'This function returns the result of a regex pattern on the given subject.',
	'params' => array(
		array(
			'name' => 'pattern',
			'type' => 'String',
			'optional' => false,
			'desc' => 'regex pattern',
		),
		array(
			'name' => 'subject',
			'type' => 'String',
			'optional' => false,
			'desc' => 'subject to apply the pattern on',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"regex('[a-z]+', msg )",
	),
),
'exists' => array(
	'name' => 'exists(fieldname, value)',
	'desc' => 'This function checks if a record with the given value in the given field exists or not.',
	'params' => array(
		array(
			'name' => 'fieldname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'field to check for its existence',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value the field must have',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"exists('accountname', 'Chemex Labs Ltd')",
	),
),
'existsrelated' => array(
	'name' => 'existsrelated(relatedmodule, fieldname, value)',
	'desc' => 'This function checks if a related module record with the given value in the given field exists or not.',
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
			'desc' => 'field to filter records',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value of the field',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"existsrelated('Contacts', 'accountname', 'Chemex Labs Ltd')",
	),
),
'allrelatedare' => array(
	'name' => 'allrelatedare(relatedmodule, fieldname, value)',
	'desc' => 'This function checks if all records on the related module have the given value in the given field.',
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
			'desc' => 'field to filter records',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value of the field',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"allrelatedare('Contacts', 'accountname', 'Chemex Labs Ltd')",
	),
),
'allrelatedarethesame' => array(
	'name' => 'allrelatedarethesame(relatedmodule, fieldname, value)',
	'desc' => 'This function checks if all records on the related module have only one unique value in the given field. If a value is given, the unique value in the related records must match the given value to return true.',
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
			'desc' => 'field to filter records',
		),
		array(
			'name' => 'value',
			'type' => 'String',
			'optional' => false,
			'desc' => 'value of the field',
		),
	),
	'categories' => array('Logical'),
	'examples' => array(
		"allrelatedarethesame('Contacts', 'accountname', 'Chemex Labs Ltd')",
	),
),
'min' => array(
	'name' => 'min(value1, value2, values)',
	'desc' => 'This function returns the minimum value of the given values.',
	'params' => array(
		array(
			'name' => 'values',
			'type' => 'Multiple',
			'optional' => false,
			'desc' => 'fields and values to search in',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'min(sum_nettotal, sum_total)',
	),
),
'max' => array(
	'name' => 'max(value1, value2, values)',
	'desc' => 'This function returns the maximum value of the given values.',
	'params' => array(
		array(
			'name' => 'values',
			'type' => 'Multiple',
			'optional' => false,
			'desc' => 'fields and values to search in',
		),
	),
	'categories' => array('Math'),
	'examples' => array(
		'max(employees, breakpoint)',
	),
),
'getCurrentConfiguredTaxValues' => array(
	'name' => 'getCurrentConfiguredTaxValues(taxname)',
	'desc' => 'This function returns the Current Configured Tax Values.',
	'params' => array(
		array(
			'name' => 'taxname',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the tax name',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrentConfiguredTaxValues('taxname')"
	),
),
'getCurrencyConversionValue' => array(
	'name' => 'getCurrencyConversionValue(currency_code)',
	'desc' => 'This function returns the Currency Conversion Value.',
	'params' => array(
		array(
			'name' => 'Currency code',
			'type' => 'String',
			'optional' => false,
			'desc' => 'fields that contain the currency code',
		),
	),
	'categories' => array('Information'),
	'examples' => array(
		"getCurrencyConversionValue('currency_code')"
	),
),
);

foreach (glob('modules/com_vtiger_workflow/language/en_us.fndefs.*.php', GLOB_BRACE) as $tcode) {
	include $tcode;
}
