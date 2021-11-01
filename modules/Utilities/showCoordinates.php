<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************/
require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;
use Geocoder\Query\GeocodeQuery;

class showcoordinateswidget {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new showcoordinateswidget_DetailViewBlock());
	}
}

class showcoordinateswidget_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'showCoordinatesWidget';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $adb;
		$this->context = $context;
		$crmid = $this->getFromContext('RECORDID');
		$module = $this->getFromContext('RECORDMODULE');
		$focus = CRMEntity::getInstance($module);
		$focus->retrieve_entity_info($crmid, $module);
		$output = '<strong>'.getTranslatedString('LBL_ADDRESS_INFORMATION', $module).'</strong><br>';
		$output.= getTranslatedString('Billing Address', $module).': '.$focus->column_fields['bill_street'].'<br>';
		$output.= getTranslatedString('Billing City', $module).': '.$focus->column_fields['bill_city'].'<br>';
		$output.= getTranslatedString('Billing State', $module).': '.$focus->column_fields['bill_state'].'<br>';
		$output.= getTranslatedString('Billing Postal Code', $module).': '.$focus->column_fields['bill_code'].'<br>';
		$output.= getTranslatedString('Billing Country', $module).': '.$focus->column_fields['bill_country'].'<br>';
		$output.= '<strong>'.getTranslatedString('LBL_ADDRESS_COORDINATES', $module).'</strong><br>';
		$httpClient = new \Http\Adapter\Guzzle6\Client();
		$provider = new \Geocoder\Provider\Mapbox\Mapbox($httpClient, 'your-api-key');
		$geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');
		$address = $focus->column_fields['bill_street'].','
			.$focus->column_fields['bill_city'].','
			.$focus->column_fields['bill_state'].','
			.$focus->column_fields['bill_code'].','
			.$focus->column_fields['bill_country'];
		$result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
		$latitud = $result->first()->getCoordinates()->getLatitude();
		$longitud = $result->first()->getCoordinates()->getLongitude();
		$output.= getTranslatedString('Latitude', $module).': '.$latitud.'<br>';
		$output.= getTranslatedString('Longitud', $module).': '.$longitud.'<br>';
		return $output;
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new showcoordinateswidget_DetailViewBlock();
	echo $smq->process($_REQUEST);
}
