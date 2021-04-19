<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by vtiger are Copyright (C) coreBOS.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'include/ListView/GridUtils.php';

$mdaction = empty($_REQUEST['mdaction']) ? 'list' : $_REQUEST['mdaction'];
switch ($_REQUEST['mdaction']) {
	case 'delete':
		break;
	case 'move':
		break;
	case 'list':
	default:
		echo json_encode(
			array(
				'data' => array(
					'contents' => [
						[
							'projecttaskname' => 'Beautiful Lies',
							'projecttaskpriority' => 'Birdy',
							'startdate' => '2016.03.26',
							'enddate' => 'Po',
							'record_module' => 'ProjectTask',
							'record_id' => 1,
						],
						[
							'projecttaskname' => 'Lies Beautiful',
							'projecttaskpriority' => 'dBirdy',
							'startdate' => '2015.03.26',
							'enddate' => 'Po44',
							'record_module' => 'ProjectTask',
							'record_id' => 2,
						],
						[
							'projecttaskname' => 'Lies 3',
							'projecttaskpriority' => '3',
							'startdate' => '2013.03.26',
							'enddate' => '3',
							'record_module' => 'ProjectTask',
							'record_id' => 44570,
						],
					],
					'pagination' => array(
						'page' => 1,
						'totalCount' => 1,
					),
				),
				'result' => true,
			)
		);
		break;
}
