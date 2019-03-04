<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Tests.
 * The MIT License (MIT)
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';

function cbmqtm_logMemoryUsage() {
	$mem = sprintf(
		'Time=%s; Memory: Usage=%s, Peak=%s, Growth=%s',
		date('Y-m-d H:i:s'),
		convert2kbytes(memory_get_usage(), 2),
		convert2kbytes(memory_get_peak_usage(), 2),
		0 //convert2kbytes(memory_get_usage() - $initialMemory, 2)
	)."\n";
	error_log($mem, 3, 'cbmqtest.log');
	$cbmq = coreBOS_MQTM::getInstance();
	$msg = $cbmq->getMessage('cbMemoryUsageChannel', 'cbmqtmmemusage', 'cbmqtmmemusage');
	$cbmq->sendMessage('cbMemoryUsageChannel', 'cbmqtmmemusage', 'cbmqtmmemusage', 'Command', '1:M', 1, 380, 300, 1, 'logMemoryUsage');
}

function convert2kbytes($num, $precision = 2, $suffix = null) {
	static $_suffix = null;
	if (is_array($suffix) && $suffix) {
		$_suffix = $suffix;
	}
	if (empty($_suffix)) {
		//$_suffix = [' B', ' KB', ' MB', ' GB', ' TB'];
		$_suffix = ['b', 'k', 'm', 'g', 't'];
	}
	if (!is_numeric($precision)) {
		$precision = 2;
	}
	$i = 0;
	$negative = $num < 0;
	$num = abs($num);
	if (!$num) {
		return '0' . $_suffix[0];
	}
	while ($num >= 1024 && ($i < count($_suffix))) {
		$num /= 1024;
		$i++;
	}
	return sprintf("%s%." . $precision . "f", $negative ? '-' : '', $num) . $_suffix[$i];
}
