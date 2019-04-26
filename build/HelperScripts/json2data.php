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

if ($argc==2 && !empty($argv[1])) {
	$contents = file_get_contents($argv[1]);
	if ($contents===false) {
		echo "\nCannot read file ".$argv[1]."\n\n";
		exit(1);
	} else {
		$def = json_decode($contents, true);
		if ($def===false || is_null($def)) {
			echo "\nCannot convert file contents to JSON\n\n";
			exit(2);
		} else {
			echo "---- dataentry ----\n";
			echo "name : ".$def['name']."\n";
			echo "type : ".$def['type']."\n";
			if (isset($def['properties'])) {
				if (!empty($def['properties']['description'])) {
					echo "description_wiki: ".$def['properties']['description']."\n";
				}
				if (!empty($def['properties']['keywords'])) {
					if (is_array($def['properties']['keywords'])) {
						echo "keywords_tags : ".implode(',', $def['properties']['keywords'])."\n";
					} else {
						echo "keywords_tags : ".$def['properties']['keywords']."\n";
					}
				}
				if (!empty($def['properties']['version'])) {
					echo "version : ".$def['properties']['version']."\n";
				}
				if (!empty($def['properties']['homepage'])) {
					echo "homepage_url : ".$def['properties']['homepage']."\n";
				}
				if (!empty($def['properties']['time'])) {
					echo "release_dt : ".$def['properties']['time']."\n";
				}
				if (!empty($def['properties']['license'])) {
					if (is_array($def['properties']['license'])) {
						echo "licenses : ".implode(',', $def['properties']['license'])."\n";
					} else {
						echo "licenses : ".$def['properties']['license']."\n";
					}
				}
				if (isset($def['properties']['extra'])) {
					if (!empty($def['properties']['extra']['price'])) {
						echo "price : ".$def['properties']['extra']['price']."\n";
					}
					if (!empty($def['properties']['extra']['buyemail'])) {
						echo "buyemail_mail : ".$def['properties']['extra']['buyemail']."\n";
					}
					if (!empty($def['properties']['extra']['buyurl'])) {
						echo "buyurl_url : ".$def['properties']['extra']['buyurl']."\n";
					}
					if (!empty($def['properties']['extra']['distribution'])) {
						echo "distribution : ".$def['properties']['extra']['distribution']."\n";
					}
				}
				if (isset($def['properties']['authors']) && is_array($def['properties']['authors']) && count($def['properties']['authors'])>0) {
					if (!empty($def['properties']['authors'][0]['name'])) {
						echo "authorname : ".$def['properties']['authors'][0]['name']."\n";
					}
					if (!empty($def['properties']['authors'][0]['email'])) {
						echo "authoremail_mail : ".$def['properties']['authors'][0]['email']."\n";
					}
					if (!empty($def['properties']['authors'][0]['homepage'])) {
						echo "authorhomepage_url : ".$def['properties']['authors'][0]['homepage']."\n";
					}
				}
				if (isset($def['properties']['support'])) {
					if (!empty($def['properties']['support']['email'])) {
						echo "supportemail_mail : ".$def['properties']['support']['email']."\n";
					}
					if (!empty($def['properties']['support']['issues'])) {
						echo "supportissues_url : ".$def['properties']['support']['issues']."\n";
					}
					if (!empty($def['properties']['support']['forum'])) {
						echo "supportforum_url : ".$def['properties']['support']['forum']."\n";
					}
					if (!empty($def['properties']['support']['wiki'])) {
						echo "supportwiki_url : ".$def['properties']['support']['wiki']."\n";
					}
					if (!empty($def['properties']['support']['irc'])) {
						echo "supportirc : ".$def['properties']['support']['irc']."\n";
					}
					if (!empty($def['properties']['support']['source'])) {
						echo "supportsource_url : ".$def['properties']['support']['source']."\n";
					}
					if (!empty($def['properties']['support']['docs'])) {
						echo "supportdocs_url : ".$def['properties']['support']['docs']."\n";
					}
				}
			}
			echo "----\n";
			exit(0);
		}
	}
} else {
	echo "\njson2data converts a composer.json file into a dokuwiki Data Entry\n";
	echo "\n\n  php json2data.php [filename|URI]\n\n";
	echo "Where filename is a valid composer.json file and URI is a valid URL to download the composer.json file from.\n\n";
}
?>