<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L.  --  This file is a part of coreBOS
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
* Allows a web service client to send an OpenOffice/LibreOffice document for it to be converted
* into any format supported by unoconv and retrieve the resulting file
* params:
*   file: file structure
*     name: filename
*.....size: size
*.....type: type of the document
*.....content: base 64 encoded content of the file
*   convert_format: string, format to convert input file.
* returns json string:
*   result: result of operation (success|error)
*   file: resulting file structure
*     name: filename
*.....size: size
*.....type: type of the document
*.....content: base 64 encoded content of the file
 *************************************************************************************************/
require_once 'modules/evvtgendoc/OpenDocument.php';

function cbws_convert($file, $convert_format, $user) {
	global $adb, $root_directory;
	if (GlobalVariable::getVariable('Webservice_GenDocConversion_Active', 0, 'evvtgendoc', $user->id)==0) {
		return array(
			'result' => 'success',
			'errormessage' => 'Service deactivated',
		);
	}
	$globaltime_start = microtime(true);
	$tmpplace = $root_directory.'cache/gd'.uniqid();
	mkdir($tmpplace);
	$filename = basename($file['name']);
	$tmppath = $tmpplace.'/'.$filename;
	$partsfile = explode('.', $filename);
	$resfile = $partsfile[0].'.'.str_replace(['.',',','/',' '], '', $convert_format);
	$result = file_put_contents($tmppath, base64_decode($file['content']));
	if ($result === false) {
		$ret = array(
			'result' => 'error',
			'errormessage' => 'Can\'t load input file'
		);
	} elseif ($result == $file['size']) {
		$resultpath = $tmpplace.'/'.$resfile;
		$doc = new OpenDocument($tmppath);
		$converttime_start = microtime(true);
		$doc->convert($tmppath, $resultpath, $convert_format);
		$converttime = microtime(true)-$converttime_start;
		if (file_exists($resultpath)) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mtype = finfo_file($finfo, $resultpath);
			$ret = array(
				'result' => 'success',
				'file' => array(
					'name' => $resfile,
					'size' => filesize($resultpath),
					'type' => $mtype,
					'content' => base64_encode(file_get_contents($resultpath))
				),
			);
		} else {
			$ret = array(
				'result' => 'error',
				'errormessage' => "Can't convert {$file['name']} to $convert_format format"
			);
		}
	} else {
		$ret = array(
			'result' => 'error',
			'errormessage' => 'Error saving input file, filesize differ'
		);
	}
	array_map('unlink', glob($tmpplace.'/*'));
	rmdir($tmpplace);
	$globaltime = microtime(true)-$globaltime_start;
	$logarray = array(
		'ip' => $_SERVER['REMOTE_ADDR'],
		'user' => $user->id,
		'docsize' => $file['size'],
		'params' => json_encode(array('format' => $convert_format)),
		'gdtime' => $converttime,
		'totaltime' => $globaltime
	);
	$adb->query('CREATE TABLE IF NOT EXISTS `cb_evvtgendoc_log` (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`date` DATETIME,
		`ip` VARCHAR(16),
		`user_id` INT(9),
		`docsize` INT(9),
		`params` VARCHAR(250),
		`gdtime` DECIMAL(25,20),
		`totaltime` DECIMAL(25,20),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
	$adb->pquery('insert into cb_evvtgendoc_log (`date`, `ip`, `user_id`, `docsize`, `params`, `gdtime`, `totaltime`) values (?,?,?,?,?,?,?)', $logarray);
	return $ret;
}
?>