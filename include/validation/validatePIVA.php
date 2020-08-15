<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

function checkAccountPIVA($fieldname, $fieldvalue, $params, $entity) {
	$type = $entity[$params[0]];
	$piva = $entity[$fieldname];
	$pattern1 = '/^IT\d{11}$/';
	$pattern2 = '/^(AF|AX|AL|DZ|VI|AD|AO|AI|AQ|AG|AR|AM|AW|AU|AT|AZ|BS|BH|BD|BB|BY|BE|BZ|BJ|BM|BT|BO|BA|BW|BV|BR|IO|VG|\
BN|BG|BF|BI|KH|CM|CA|CV|KY|CF|TD|CL|CN|CX|CC|CO|KM|CG|CD|CK|CR|HR|CU|CY|CZ|DK|DJ|DM|DO|AN|TL|EC|EG|SV|GQ|\
ER|EE|ET|FK|FO|FJ|FI|FR|GF|PF|TF|GA|GM|GE|DE|GH|GI|GR|EL|GL|GD|GP|GU|GT|GG|GN|GW|GY|HT|HM|HN|HK|HU|IS|IN|\
ID|IR|IQ|IE|IM|IL|CI|JM|JP|JE|JO|KZ|KE|KI|KW|KG|LA|LV|LB|LS|LR|LY|LI|LT|LU|MO|MG|MW|MY|MV|ML|MT|MH|MQ|MR|\
MU|YT|MX|FM|UM|MD|MC|MN|ME|MS|MA|MZ|MM|MP|NA|NR|NP|NL|NC|NZ|NI|NE|NG|NU|XX|NF|KP|NO|OM|PK|PW|PS|PA|PG|PY|\
PE|PH|PN|PL|PT|PR|QA|MK|RE|RO|RW|RU|GS|ST|BL|MF|AS|SM|SA|SN|RS|SC|SL|SG|SK|SI|SB|SO|ZA|KR|ES|LK|KN|SH|LC|\
VC|PM|SD|SR|SJ|SZ|SE|CH|SY|TW|TJ|TZ|TH|TG|TK|TO|TT|TN|TR|TM|TC|TV|UG|UA|AE|GB|UY|US|UZ|VU|VA|VE|VN|WF|EH|\
WS|YE|ZM|ZW)[A-Za-z0-9_]*$/';

	if (!empty($piva) && ($type == 'Societa' || $type == 'Ditta Individuale') && preg_match($pattern1, $piva)) {
		return true;
	} elseif (empty($piva) && $type == 'Persona Fisica') {
		return true;
	} elseif (!empty($piva) && $type == 'Soggetto Estero' && preg_match($pattern2, $piva)) {
		return true;
	} else {
		return false;
	}
}