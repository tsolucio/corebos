<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Documentation.
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

if ($argc==2 and !empty($argv[1])) {
	$cjson = file_get_contents($argv[1].'/composer.json');
	if ($cjson===false) {
		echo "\nCannot read file ".$argv[1]."/composer.json\n\n";
		exit(1);
	}
	$def = json_decode($cjson,true);
	if ($def===false or is_null($def)) {
		echo "\nCannot convert composer.json file contents to JSON\n\n";
		exit(2);
	}
	$mxml = file_get_contents($argv[1].'/manifest.xml');
	if ($mxml===false) {
		echo "\nCannot read file ".$argv[1]."/manifest.xml\n\n";
		exit(3);
	}
	$xml = simplexml_load_string($mxml);
	if ($xml===false or is_null($xml)) {
		echo "\nCannot convert manifest.xml file contents to XML\n\n";
		exit(4);
	}
	$mod_strings = Array();
	if (file_exists($argv[1].'/modules/'.$xml->name.'/language/en_us.lang.php')) {
		include($argv[1].'/modules/'.$xml->name.'/language/en_us.lang.php');
	}
	if ($mxml===false) {
		echo "\nCannot read file ".$argv[1]."/manifest.xml\n\n";
		exit(3);
	}
	echo "~~NOTOC~~\n====== ".$def['properties']['name']." ======\n\n";
	if (!empty($def['properties']['description'])) {
		$desc = str_replace('\\', '\\\\', $def['properties']['description']);
	} else {
		$desc = '';
	}
	echo $desc.($desc!=''?"\n\\\\\n":'');
	echo "---- dataentry ----\n";
	echo "name : ".$def['name']."\n";
	echo "type : ".$def['type']."\n";
	if (isset($def['properties'])) {
		if (!empty($desc))
			echo "description_wiki: ".$desc."\n";
		if (!empty($def['properties']['keywords'])) {
			if (is_array($def['properties']['keywords'])) {
				echo "keywords_tags : ".implode(',', $def['properties']['keywords'])."\n";
			} else {
				echo "keywords_tags : ".$def['properties']['keywords']."\n";
			}
		}
		if (!empty($def['properties']['version']))
			echo "version : ".$def['properties']['version']."\n";
		if (!empty($def['properties']['homepage']))
			echo "homepage_url : ".$def['properties']['homepage']."\n";
		if (!empty($def['properties']['time']))
			echo "release_dt : ".$def['properties']['time']."\n";
		if (!empty($def['properties']['license'])) {
			if (is_array($def['properties']['license'])) {
				echo "licenses : ".implode(',', $def['properties']['license'])."\n";
			} else {
				echo "licenses : ".$def['properties']['license']."\n";
			}
		}
		if (isset($def['properties']['extra'])) {
			if (!empty($def['properties']['extra']['price']))
				echo "price : ".$def['properties']['extra']['price']."\n";
			if (!empty($def['properties']['extra']['buyemail']))
				echo "buyemail_mail : ".$def['properties']['extra']['buyemail']."\n";
			if (!empty($def['properties']['extra']['buyurl']))
				echo "buyurl_url : ".$def['properties']['extra']['buyurl']."\n";
			if (!empty($def['properties']['extra']['distribution']))
				echo "distribution : ".$def['properties']['extra']['distribution']."\n";
		}
		if (isset($def['properties']['authors']) and is_array($def['properties']['authors']) and count($def['properties']['authors'])>0) {
			if (!empty($def['properties']['authors'][0]['name']))
				echo "authorname : ".$def['properties']['authors'][0]['name']."\n";
			if (!empty($def['properties']['authors'][0]['email']))
				echo "authoremail_mail : ".$def['properties']['authors'][0]['email']."\n";
			if (!empty($def['properties']['authors'][0]['homepage']))
				echo "authorhomepage_url : ".$def['properties']['authors'][0]['homepage']."\n";
		}
		if (isset($def['properties']['support'])) {
			if (!empty($def['properties']['support']['email']))
				echo "supportemail_mail : ".$def['properties']['support']['email']."\n";
			if (!empty($def['properties']['support']['issues']))
				echo "supportissues_url : ".$def['properties']['support']['issues']."\n";
			if (!empty($def['properties']['support']['forum']))
				echo "supportforum_url : ".$def['properties']['support']['forum']."\n";
			if (!empty($def['properties']['support']['wiki']))
				echo "supportwiki_url : ".$def['properties']['support']['wiki']."\n";
			if (!empty($def['properties']['support']['irc']))
				echo "supportirc : ".$def['properties']['support']['irc']."\n";
			if (!empty($def['properties']['support']['source']))
				echo "supportsource_url : ".$def['properties']['support']['source']."\n";
			if (!empty($def['properties']['support']['docs']))
				echo "supportdocs_url : ".$def['properties']['support']['docs']."\n";
		}
	}
	echo "----\n\\\\\n\n";
	echo "====== Fields =====\n\n";
	foreach($xml->blocks->block as $blocknode) {
		$blabel = (string)$blocknode->label;
		$blabel = (isset($mod_strings[$blabel]) ? $mod_strings[$blabel] : $blabel);
		echo "==== ".$blabel." ===\n";
		if (count($blocknode->fields->field)>0) {
			echo "^Field^Type^Values^\n";
			foreach($blocknode->fields->field as $field) {
				$flabel = (string)$field->fieldlabel;
				$flabel = (isset($mod_strings[$flabel]) ? $mod_strings[$flabel] : $flabel);
				$uitype = (string)$field->uitype;
				$uitype = (isset($uitypes[$uitype]) ? $uitypes[$uitype] : $uitype);
				echo "|".$flabel."|".$uitype."|";
				if (in_array($field->uitype, array(10,15,16,33))) {
					$values = array();
					if ($field->uitype==10) {
						foreach ($field->relatedmodules->relatedmodule as $rmod) {
							$values[] = $rmod;
						}
					} else {
						foreach ($field->picklistvalues->picklistvalue as $pval) {
							$plval = (string)$pval;
							$plval = (isset($mod_strings[$plval]) ? $mod_strings[$plval] : $plval);
							$values[] = $plval;
						}
					}
					echo implode(',', $values);
				}
				if ($field->entityidentifier) {
					echo " **Identifier**";
				}
				echo " |\n";
			}
		}
	}
	exit(0);
} else {
	echo "\nmodule2wiki converts a module manifest.xml and composer.json file into a dokuwiki page with a Data Entry\n";
	echo "\n\n  php module2wiki.php [module_directoty]\n\n";
	echo "Where module_directoty is a directory which contains a valid coreBOS module structure.\n\n";
}
?>