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
$adb = PearDatabase::getInstance();

function cbmqtm_testSubscribePS() {
	$cbmq = coreBOS_MQTM::getInstance();
	$cbmq->subscribeToChannel('cbTestChannel', 'cbmqtm', 'cbmqtm', array('file'=>'include/cbmqtm/cbmqtm_dbdisttest.php','method'=>'cbmqtm_testSubscribeConsume1'));
	$cbmq->subscribeToChannel('cbTestChannel', 'cbmqtm', 'cbmqtm', array('file'=>'include/cbmqtm/cbmqtm_dbdisttest.php','method'=>'cbmqtm_testSubscribeConsume2'));

	$cbmq->sendMessage('cbTestChannel', 'cbmqtm', 'cbmqtm', 'Data', 'P:S', 1, 30, 1, 1, 'informationPS');
}

function cbmqtm_testSubscribe1M() {
	$cbmq = coreBOS_MQTM::getInstance();
	$cbmq->subscribeToChannel('cbTestChannel', 'cbmqtm', 'cbmqtm', array('file'=>'include/cbmqtm/cbmqtm_dbdisttest.php','method'=>'cbmqtm_testSubscribeConsume1'));

	$cbmq->sendMessage('cbTestChannel', 'cbmqtm', 'cbmqtm', 'Data', '1:M', 1, 30, 1, 1, 'information1M');
}

function cbmqtm_testSubscribe1MC() {
	$cbmq = coreBOS_MQTM::getInstance();
	$cbmq->subscribeToChannel('cbTestChannel', 'cbmqtm', 'cbmqtm', array('file'=>'include/cbmqtm/cbmqtm_dbdisttestclass.php','class'=>'cbmqtm_dbdisttestclass','method'=>'consume'));

	$cbmq->sendMessage('cbTestChannel', 'cbmqtm', 'cbmqtm', 'Data', '1:M', 1, 30, 1, 1, 'information1MC');
}

function cbmqtm_testSubscribeMemoryLog() {
	$cbmq = coreBOS_MQTM::getInstance();
	$cbmq->subscribeToChannel('cbMemoryUsageChannel', 'cbmqtmmemusage', 'cbmqtmmemusage', array('file'=>'include/cbmqtm/cbmqtm_memusage.php','method'=>'cbmqtm_logMemoryUsage'));

	$cbmq->sendMessage('cbMemoryUsageChannel', 'cbmqtmmemusage', 'cbmqtmmemusage', 'Command', '1:M', 1, 380, 300, 1, 'logMemoryUsage');
}

function cbmqtm_testSubscribeConsume1() {
	$cbmq = coreBOS_MQTM::getInstance();

	$msg = $cbmq->getMessage('cbTestChannel', 'cbmqtm', 'cbmqtm');
	error_log('Consume1', 3, 'cbmqtest.log');
	error_log(print_r($msg, true), 3, 'cbmqtest.log');
}

function cbmqtm_testSubscribeConsume2() {
	$cbmq = coreBOS_MQTM::getInstance();

	$msg = $cbmq->getMessage('cbTestChannel', 'cbmqtm', 'cbmqtm');
	error_log('Consume2', 3, 'cbmqtest.log');
	error_log(print_r($msg, true), 3, 'cbmqtest.log');
}

if (!empty($argc)) {
	if ($argc==2 && !empty($argv[1])) {
		// Run specific service
		if ($argv[1] == '1M') {
			cbmqtm_testSubscribe1M();
		} elseif ($argv[1] == '1MC') {
			cbmqtm_testSubscribe1MC();
		} elseif ($argv[1] == 'ML') {
			cbmqtm_testSubscribeMemoryLog();
		} else {
			cbmqtm_testSubscribePS();
		}
	} else {
		// run both
		cbmqtm_testSubscribe1M();
		cbmqtm_testSubscribePS();
	}
}