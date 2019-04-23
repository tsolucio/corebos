<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

function display_xml_error($error) {
	$return  = '';
	switch ($error->level) {
		case LIBXML_ERR_WARNING:
			$return .= "Warning $error->code: ";
			break;
		case LIBXML_ERR_ERROR:
			$return .= "Error $error->code: ";
			break;
		case LIBXML_ERR_FATAL:
			$return .= "Fatal Error $error->code: ";
			break;
	}

	$return .= trim($error->message) .
	"<br>  Line: $error->line" .
	"<br>  Column: $error->column";

	if ($error->file) {
		$return .= "<br>  File: $error->file <br>";
	}

	return $return;
}

function cbupdater_show_error($errmsg) {
	echo <<<EOM
	<div style="padding:20px;">
	<div style="color: #f85454; font-weight: bold; padding: 10px; border: 1px solid #FF0000; background: #FFFFFF; border-radius: 5px; margin-bottom: 10px;">$errmsg</div>
	</div>
EOM;
}

function cbupdater_show_message($msg) {
	echo <<<EOM
	<div style="padding:20px;">
	<div style="color: olive; font-weight: bold; padding: 10px; border: 1px solid olive; background: #FFFFFF; border-radius: 5px; margin-bottom: 10px;">$msg</div>
	</div>
EOM;
}

function cbupdater_dowork_finishExecution($update_count, $success_count, $failure_count) {
	echo <<<EOT
<br /><br /><hr>
<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
	<tr>
	<td width="75%" align="right" nowrap>Total Number of updates executed : </td>
	<td width="25%" align="left"><b>$update_count</b></td>
	</tr>
	<tr>
	<td align="right">Correct Updates : </td>
	<td align="left"><b style="color:#006600;">$success_count</b></td>
	</tr>
	<tr>
	<td align="right">Failed Updates : </td>
	<td align="left"><b style="color:#FF0000;">$failure_count</b></td>
	</tr>
</table>
EOT;
}
?>