<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Third Party Integration Access point
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

if (!isset($_REQUEST['_op'])) {
	$_REQUEST['_op'] = 'HELP';
}
switch ($_REQUEST['_op']) {
	case 'Success':
		$smarty = new vtigerCRM_Smarty();
		$titlemessage = getTranslatedString('SUCCESSFUL_REGISTRATION_TITLE', $currentModule);
		$smarty->assign('TITLE_MESSAGE', $titlemessage);
		$smarty->assign('MESSAGE', sprintf(getTranslatedString('SUCCESSFUL_REGISTRATION_MESSAGE', $currentModule), vtlib_purify($_REQUEST['integration'])));
		$smarty->assign('ERROR_CLASS', '');
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('THEME', $theme);
		include 'include/integrations/forcedButtons.php';
		$smarty->assign('CHECK', $tool_buttons);
		$smarty->display('modules/Utilities/integration.tpl');
		break;
	case 'Error':
		$smarty = new vtigerCRM_Smarty();
		$titlemessage = getTranslatedString('UNSUCCESSFUL_REGISTRATION_TITLE', $currentModule);
		$smarty->assign('TITLE_MESSAGE', $titlemessage);
		$smarty->assign('MESSAGE', sprintf(getTranslatedString('UNSUCCESSFUL_REGISTRATION_MESSAGE', $currentModule), vtlib_purify($_REQUEST['integration'])).
			'<br>'.vtlib_purify($_REQUEST['error_description']).' ('.vtlib_purify($_REQUEST['error_code']).')');
		$smarty->assign('ERROR_CLASS', 'slds-theme_error');
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('THEME', $theme);
		include 'include/integrations/forcedButtons.php';
		$smarty->assign('CHECK', $tool_buttons);
		$smarty->display('modules/Utilities/integration.tpl');
		break;
	case 'getconfighubspot':
	case 'setconfighubspot':
		include_once 'include/integrations/hubspot/settings.php';
		break;
	case 'getconfigzendesk':
	case 'setconfigzendesk':
		include_once 'include/integrations/zendesk/settings.php';
		break;
	case 'getconfig2fa':
	case 'setconfig2fa':
		include_once 'include/integrations/2fa/settings.php';
		break;
	case 'getconfiggcontact':
	case 'setconfiggcontact':
		include_once 'include/integrations/GContacts/settings.php';
		break;
	case 'getconfigwhatsapp':
	case 'setconfigwhatsapp':
		include_once 'include/integrations/whatsapp/settings.php';
		break;
	case 'getconfigsaml':
	case 'setconfigsaml':
		include_once 'include/integrations/saml/settings.php';
		break;
	case 'getconfigsendgrid':
	case 'setconfigsendgrid':
		include_once 'include/integrations/sendgrid/settings.php';
		break;
	case 'getconfigsmtp':
	case 'setconfigsmtp':
		include_once 'include/integrations/smtp/settings.php';
		break;
	case 'getconfiggmp':
	case 'setconfiggmp':
		include_once 'include/integrations/gmp/settings.php';
		break;
	case 'getconfigrecordversioning':
	case 'setconfigrecordversioning':
		include_once 'include/integrations/recordversioning/settings.php';
		break;
	case 'getconfigelasticsearch':
	case 'setconfigelasticsearch':
		include_once 'include/integrations/elasticsearch/settings.php';
		break;
	case 'getconfiglogall':
	case 'setconfiglogall':
		include_once 'include/integrations/logall/settings.php';
		break;
	case 'getconfigloginsync':
	case 'setconfigloginsync':
		include_once 'include/integrations/loginsync/settings.php';
		break;
	case 'getconfigstripepayment':
	case 'setconfigstripepayment':
		include_once 'include/integrations/stripepayment/settings.php';
		break;
	case 'getconfigsclickhouse':
	case 'setconfigsclickhouse':
		include_once 'include/integrations/clickhouse/settings.php';
		break;
	case 'getconfigdenormalization':
	case 'setconfigdenormalization':
		include_once 'include/integrations/denormalize/settings.php';
		break;
	case 'getconfigonesignal':
	case 'setconfigonesignal':
		include_once 'include/integrations/onesignal/settings.php';
		break;
	case 'getconfigcache':
	case 'setconfigcache':
		include_once 'include/integrations/cache/settings.php';
		break;
	case 'getconfigwc':
	case 'setconfigwc':
		include_once 'include/integrations/woocommerce/settings.php';
		break;
	case 'getconfigmautic':
	case 'setconfigmautic':
		include_once 'include/integrations/mautic/settings.php';
		break;
	default:
		$smarty = new vtigerCRM_Smarty();
		$titlemessage = getTranslatedString('Available Integrations', $currentModule);
		$intgr = array(
			array(
				'abbr' => '2F',
				'title' => getTranslatedString('GoTo2FAActivation', 'Utilities'),
				'desc' => getTranslatedString('GoTo2FAActivation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfig2fa',
			),
			array(
				'abbr' => 'SSO',
				'title' => getTranslatedString('SAML Activation', 'Utilities'),
				'desc' => getTranslatedString('SAML Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigsaml',
			),
			array(
				'abbr' => 'GC',
				'title' => getTranslatedString('GOOGLE_CONTACTS', 'Contacts'),
				'desc' => getTranslatedString('GOOGLE_CONTACTS_Desc', 'Contacts'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfiggcontact',
			),
			array(
				'abbr' => 'HS',
				'title' => getTranslatedString('HubSpot Activation', 'Utilities'),
				'desc' => getTranslatedString('HubSpot Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfighubspot',
			),
			array(
				'abbr' => 'EM',
				'title' => getTranslatedString('SMTP Configuration', 'Utilities'),
				'desc' => getTranslatedString('SMTP Configuration_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigsmtp&savemode=false',
			),
			array(
				'abbr' => 'RV',
				'title' => getTranslatedString('Record Versioning', 'Utilities'),
				'desc' => getTranslatedString('Record Versioning_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigrecordversioning',
			),
			// array(
			// 	'abbr' => 'ZD',
			// 	'title' => getTranslatedString('Zendesk Activation', 'Utilities'),
			// 	'desc' => getTranslatedString('Zendesk Activation_Desc', 'Utilities'),
			// 	'url' => 'index.php?action=integration&module=Utilities&_op=getconfigzendesk',
			// ),
			array(
				'abbr' => 'WA',
				'title' => getTranslatedString('Whatsapp Activation', 'Utilities'),
				'desc' => getTranslatedString('Whatsapp Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigwhatsapp',
			),
			array(
				'abbr' => 'SG',
				'title' => getTranslatedString('SendGrid Activation', 'Utilities'),
				'desc' => getTranslatedString('SendGrid Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigsendgrid',
			),
			array(
				'abbr' => 'GM',
				'title' => getTranslatedString('GMP Activation', 'Utilities'),
				'desc' => getTranslatedString('GMP Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfiggmp',
			),
			array(
				'abbr' => 'ES',
				'title' => getTranslatedString('ElasticSearch Activation', 'Utilities'),
				'desc' => getTranslatedString('Elasticsearch_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigelasticsearch',
			),
			array(
				'abbr' => 'LA',
				'title' => getTranslatedString('LogAll Activation', 'Utilities'),
				'desc' => getTranslatedString('LogAll_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfiglogall',
			),
			array(
				'abbr' => 'DM',
				'title' => getTranslatedString('Denormalization Activation', 'Utilities'),
				'desc' => getTranslatedString('Denormalize_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigdenormalization',
			),
			array(
				'abbr' => 'CA',
				'title' => getTranslatedString('Cache Activation', 'Utilities'),
				'desc' => getTranslatedString('Cache Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigcache',
			),
			array(
				'abbr' => 'WC',
				'title' => getTranslatedString('Woocommerce Activation', 'Utilities'),
				'desc' => getTranslatedString('Woocommerce Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigwc',
			),
			array(
				'abbr' => 'SP',
				'title' => getTranslatedString('Stripe Payment Activation', 'Utilities'),
				'desc' => getTranslatedString('StripePayment_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigstripepayment',
			),
			array(
				'abbr' => 'MA',
				'title' => getTranslatedString('Mautic Activation', 'Utilities'),
				'desc' => getTranslatedString('Mautic Activation_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigmautic',
			),
			array(
				'abbr' => 'CH',
				'title' => getTranslatedString('ClickHouse', 'Utilities'),
				'desc' => getTranslatedString('ClickHouse_Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigsclickhouse',
			),
		);
		if (file_exists('build/wsChanges/LoginSession.php')) {
			$intgr[] = array(
				'abbr' => 'LS',
				'title' => getTranslatedString('Login Sync Activation', 'Utilities'),
				'desc' => getTranslatedString('Login_Synic Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigloginsync',
			);
		}
		if (file_exists('OneSignalSDKUpdaterWorker.js') && file_exists('OneSignalSDKWorker.js')) {
			$intgr[] = array(
				'abbr' => 'OS',
				'title' => getTranslatedString('OneSignal Activation', 'Utilities'),
				'desc' => getTranslatedString('OneSignal Desc', 'Utilities'),
				'url' => 'index.php?action=integration&module=Utilities&_op=getconfigonesignal',
			);
		}
		$smarty->assign('integrations', $intgr);
		$smarty->assign('TITLE_MESSAGE', $titlemessage);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('THEME', $theme);
		include 'include/integrations/forcedButtons.php';
		$smarty->assign('CHECK', $tool_buttons);
		$smarty->display('modules/Utilities/integrationhelp.tpl');
		break;
}
