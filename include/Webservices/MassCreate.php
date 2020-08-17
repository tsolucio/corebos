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
 *************************************************************************************************/

require_once 'include/Webservices/Create.php';

function MassCreate($elements, $user) {
    $failedCreates = [];
    $successCreates = [];
    
    $records = array();
    foreach ($elements as &$element) {
        mcProcessReference($element, $elements, $records);
    }

    foreach ($records as &$record) {
        foreach ($record['element'] as $key => $value) {
            if (strpos($value, '@{') !== false) {
                $start = '@{';
                $end = '.';
                preg_match_all("/$start([a-zA-Z0-9_]*)$end/", $value, $match);
                if (isset($match[1][0])) {
                    $reference = $match[1][0];
                    $id = mcGetRecordId($records, $reference);
                    $record['element'][$key] = $id;
                }
            }
        }
        try {
            $rec = vtws_create($record['elementType'],$record['element'], $user);
            $record['id'] = $rec['id'];
            $successCreates[] = $rec;
        } catch (Exception $e) {
			$failedCreates[] = [
				'record' => $record,
				'code' => $e->getCode(),
				'message' => $e->getMessage()
			];
		}
    }

    return [
		'success_creates' => $successCreates,
		'failed_creates' => $failedCreates
	];
}

function mcGetRecordId($arr, $reference) {
    $id = "";
    foreach ($arr as $ar) {
        if ($ar['referenceId'] == $reference) {
            if (isset($ar['id'])) {
                $id = $ar['id'];
            }
            break;
        }
    }
    return $id;
}

function mcGetReferenceRecord($arr, $reference) {
    $array = array();
    $index = null;
    for ($x = 0; $x < count($arr); $x++) {
        if (isset($arr[$x])) {
            if ($arr[$x]['referenceId'] == $reference) {
                $array = $arr[$x];
                $index = $x;
                break;
            }
        }
    }
    return array($index, $array);
}

function mcProcessReference($element, &$elements, &$records) {
    foreach ($element['element'] as $key => $value) {
        if (strpos($value, '@{') !== false) {
            $start = '@{';
            $end = '.';
            preg_match_all("/$start([a-zA-Z0-9_]*)$end/", $value, $match);
            if (isset($match[1][0])) {
                $reference = $match[1][0];
                list($index, $array) = mcGetReferenceRecord($elements, $reference);
                if ($index && $array) {
                    mcProcessReference($array, $elements, $records);
                    unset($elements[$index]);
                }
            }
        }
    }
    $records[] = $element;
}