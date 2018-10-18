<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Documentation.
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
 *  Module       : coreBOS Documentation
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

$uitypes = array(
	'1'=>'string',
	'2'=>'string',
	'4'=>'autonumber',
	'5'=>'date',
	'6'=>'datetime (internal)',
	'70'=>'datetime',
	'7'=>'number',
	'71'=>'currency',
	'72'=>'currency',
	'9'=>'percentage',
	'10'=>'relation',
	'12'=>'email',
	'13'=>'email',
	'14'=>'time',
	'15'=>'picklist',
	'16'=>'picklist',
	'1613'=>'module picklist',
	'1614'=>'all module picklist',
	'1615'=>'all picklist picklist',
	'17'=>'url',
	'19'=>'text',
	'20'=>'text',
	'21'=>'text',
	'22'=>'text',
	'23'=>'date',
	'24'=>'text',
	'26'=>'documents folder',
	'33'=>'multipicklist',
	'3313'=>'module multi-list',
	'3314'=>'all module multi-list',
	'51'=>'relation (account)',
	'53'=>'assigned to',
	'56'=>'checkbox',
	'57'=>'relation(contact)',
	'66'=>'picklist(calendar)',
	'255'=>'picklist(salutation)',
);

if ($argc==2 && !empty($argv[1])) {
	$cjson = file_get_contents($argv[1].'/composer.json');
	if ($cjson===false) {
		echo "\nCannot read file ".$argv[1]."/composer.json\n\n";
		exit(1);
	}
	$def = json_decode($cjson, true);
	if ($def===false || is_null($def)) {
		echo "\nCannot convert composer.json file contents to JSON\n\n";
		exit(2);
	}
	echo $def['properties']['name']."\n=======\n\n";
	if (!empty($def['properties']['description'])) {
		$desc = str_replace('\\', '\\\\', $def['properties']['description']);
	} else {
		$desc = '';
	}
	echo $desc.($desc!=''?"\n\n":'');
	echo "**Name** : ".$def['name']."\n\n";
	echo "**Type** : ".$def['type']."\n\n";
	if (isset($def['properties'])) {
		if (!empty($def['properties']['keywords'])) {
			if (is_array($def['properties']['keywords'])) {
				echo "**Keywords** : ".implode(',', $def['properties']['keywords'])."\n\n";
			} else {
				echo "**Keywords** : ".$def['properties']['keywords']."\n\n";
			}
		}
		if (!empty($def['properties']['version'])) {
			echo "**Version** : ".$def['properties']['version']."\n\n";
		}
		if (!empty($def['properties']['homepage'])) {
			echo "[**Homepage**](".$def['properties']['homepage'].")\n\n";
		}
		if (!empty($def['properties']['time'])) {
			echo "**Release date** : ".$def['properties']['time']."\n\n";
		}
		if (!empty($def['properties']['license'])) {
			if (is_array($def['properties']['license'])) {
				echo "**Licenses** : ".implode(',', $def['properties']['license'])."\n\n";
			} else {
				echo "**Licenses** : ".$def['properties']['license']."\n\n";
			}
		}
		if (isset($def['properties']['extra'])) {
			if (!empty($def['properties']['extra']['price'])) {
				echo "**Price** : ".$def['properties']['extra']['price']."\n\n";
			}
			if (!empty($def['properties']['extra']['buyemail'])) {
				echo "**Purchase email** : ".$def['properties']['extra']['buyemail']."\n\n";
			}
			if (!empty($def['properties']['extra']['buyurl'])) {
				echo "[**Purchase site**](".$def['properties']['extra']['buyurl'].")\n\n";
			}
			if (!empty($def['properties']['extra']['distribution'])) {
				echo "**Distribution** : ".$def['properties']['extra']['distribution']."\n\n";
			}
		}
		if (isset($def['properties']['authors']) && is_array($def['properties']['authors']) && count($def['properties']['authors'])>0) {
			if (!empty($def['properties']['authors'][0]['name'])) {
				echo "**Author name** : ".$def['properties']['authors'][0]['name']."\n\n";
			}
			if (!empty($def['properties']['authors'][0]['email'])) {
				echo "**Author email** : ".$def['properties']['authors'][0]['email']."\n\n";
			}
			if (!empty($def['properties']['authors'][0]['homepage'])) {
				echo "**Author homepage** : ".$def['properties']['authors'][0]['homepage']."\n\n";
			}
		}
		if (isset($def['properties']['support'])) {
			if (!empty($def['properties']['support']['email'])) {
				echo "**Support email** : ".$def['properties']['support']['email']."\n\n";
			}
			if (!empty($def['properties']['support']['issues'])) {
				echo "[**Support issues**](".$def['properties']['support']['issues'].")\n\n";
			}
			if (!empty($def['properties']['support']['forum'])) {
				echo "[**Support forum**](".$def['properties']['support']['forum'].")\n\n";
			}
			if (!empty($def['properties']['support']['wiki'])) {
				echo "[**Support wiki**](".$def['properties']['support']['wiki'].")\n\n";
			}
			if (!empty($def['properties']['support']['irc'])) {
				echo "[**Support IRC](".$def['properties']['support']['irc'].")\n\n";
			}
			if (!empty($def['properties']['support']['docs'])) {
				echo "[**Support docs**](".$def['properties']['support']['docs'].")\n\n";
			}
		}
	}
	echo "\n";
	exit(0);
} else {
	echo "\ncomposer2readme converts a module composer.json file into a github README.md page\n";
	echo "\n\n  php composer2readme.php [module_directory]\n\n";
	echo "Where module_directory is a directory which contains a valid coreBOS module composer.json file.\n\n";
}
?>