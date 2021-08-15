<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : SAML Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'modules/Users/Users.php';
require_once 'modules/Users/UserPrivilegesWriter.php';
require 'vendor/autoload.php';

define('ONELOGIN_CUSTOMPATH', 'include/integrations/saml/');

class corebos_saml {

	// Configuration Keys
	const KEY_ISACTIVE = 'SAML_ISACTIVE';
	const KEY_ISACTIVEWS = 'SAML_ISACTIVEWS';
	const KEY_SPEID = 'SAML_SPEID';
	const KEY_SPACS = 'SAML_SPACS';
	const KEY_SPSLO = 'SAML_SPSLO';
	const KEY_SPNID = 'SAML_SPNID';
	const KEY_IPEID = 'SAML_IPEID';
	const KEY_IPSSO = 'SAML_IPSSO';
	const KEY_IPSLO = 'SAML_IPSLO';
	const KEY_IP509 = 'SAML_IP509';
	const KEY_RWURL = 'SAML_RWURL';
	const KEY_RWURL2 = 'SAML_RWURL2';
	const KEY_RWURL3 = 'SAML_RWURL3';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	public $samlclient = null;

	public function __construct() {
		global $current_language, $default_language;
		$current_language = $default_language;
		if ($this->isActiveEither()) {
			$this->samlclient = new \OneLogin\Saml2\Auth($this->getSSOSettings());
		}
	}

	public function findUser($attributes) {
		global $adb;
		$email = $clientid = '';
		foreach ($attributes as $attributeName => $attributeValues) {
			if ($email=='' && stripos($attributeName, 'email')!==false) {
				foreach ($attributeValues as $attributeValue) {
					if (filter_var($attributeValue, FILTER_VALIDATE_EMAIL) && $email=='') {
						$email = $attributeValue;
					}
					break;
				}
			}
			if (stripos($attributeName, 'clientid')!==false || stripos($attributeName, 'ldap')!==false) {
				foreach ($attributeValues as $attributeValue) {
					$clientid = $attributeValue;
					break;
				}
			}
		}
		$userid = 0;
		$cnacc=$adb->getColumnNames('vtiger_users');
		if ($clientid!='') {
			if (in_array('clientid', $cnacc)) {
				$userid = getSingleFieldValue('vtiger_users', 'id', 'clientid', $clientid);
			}
			if (in_array('ldap', $cnacc)) {
				$userid = getSingleFieldValue('vtiger_users', 'id', 'ldap', $clientid);
			}
			if (in_array('lmldaplogin', $cnacc)) {
				$userid = getSingleFieldValue('vtiger_users', 'id', 'lmldaplogin', $clientid);
			}
		}
		if ($email!='' && $userid==0) {
			$userid = getSingleFieldValue('vtiger_users', 'id', 'email1', $email);
		}
		return $userid;
	}

	public function findUserPortal($attributes) {
		global $adb;
		$email = $clientid = '';
		foreach ($attributes as $attributeName => $attributeValues) {
			if ($email=='' && stripos($attributeName, 'email')!==false) {
				foreach ($attributeValues as $attributeValue) {
					if (filter_var($attributeValue, FILTER_VALIDATE_EMAIL) && $email=='') {
						$email = $attributeValue;
					}
					break;
				}
			}
			if (stripos($attributeName, 'clientid')!==false || stripos($attributeName, 'ldap')!==false) {
				foreach ($attributeValues as $attributeValue) {
					$clientid = $attributeValue;
					break;
				}
			}
		}
		$userid = 0;
		if ($clientid!='') {
			$cnacc=$adb->getColumnNames('vtiger_contactdetails');
			if (in_array('username', $cnacc)) {
				$rs = $adb->pquery(
					'select contactid from vtiger_contactdetails inner join vtiger_crmentity on crmid=contactid where deleted=0 and username=?',
					array($clientid)
				);
				if ($rs && $adb->num_rows($rs)>0) {
					$userid = $adb->query_result($rs, 0, 'contactid');
				}
			}
		}
		if ($email!='' && $userid==0) {
			$rs = $adb->pquery(
				'select contactid from vtiger_contactdetails inner join vtiger_crmentity on crmid=contactid where deleted=0 and email=?',
				array($email)
			);
			if ($rs && $adb->num_rows($rs)>0) {
				$userid = $adb->query_result($rs, 0, 'contactid');
			}
		}
		if ($userid==0) {
			$userid = $this->findUser($attributes);
		}
		return $userid;
	}

	public function authenticate() {
		global $adb, $log, $application_unique_key;
		if ($this->isActive()) {
			$userid = $this->findUser($_SESSION['samlUserdata']);
			if ($userid) {
				$usrrs = $adb->pquery('select status,deleted,failed_login_attempts from vtiger_users where id=?', array($userid));
				if ($adb->query_result($usrrs, 0, 'status')!='Inactive' && $adb->query_result($usrrs, 0, 'deleted')=='0') {
					$focus = new Users();
					$focus->retrieve_entity_info($userid, 'Users');
					$focus->authenticated = true;
					coreBOS_Session::destroy();
					//Inserting entries for audit trail during login
					if (coreBOS_Settings::getSetting('audit_trail', false)) {
						$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
						$query = 'insert into vtiger_audit_trial values(?,?,?,?,?,?)';
						$params = array($adb->getUniqueID('vtiger_audit_trial'), $focus->id, 'Users','Authenticate','',$date_var);
						$adb->pquery($query, $params);
					}
					cbEventHandler::do_action('corebos.audit.authenticate', array($focus->id, 'Users', 'Authenticate', $focus->id, date('Y-m-d H:i:s')));

					// Recording the login info
					require_once 'modules/Users/LoginHistory.php';
					$loghistory=new LoginHistory();
					$loghistory->user_login($focus->column_fields['user_name'], Vtiger_Request::get_ip(), date('Y/m/d H:i:s'));

					require_once 'include/utils/UserInfoUtil.php';
					UserPrivilegesWriter::setUserPrivileges($focus->id);

					coreBOS_Session::delete('login_password');
					coreBOS_Session::delete('login_error');
					coreBOS_Session::delete('login_user_name');

					coreBOS_Session::set('authenticated_user_id', $focus->id);
					coreBOS_Session::set('app_unique_key', $application_unique_key);
					$connection_id = uniqid();
					coreBOS_Session::set('conn_unique_key', $connection_id);
					coreBOS_Settings::setSetting('cbodUserConnection'.$focus->id, $connection_id);

					//Enabled session variable for KCFINDER
					coreBOS_Session::setKCFinderVariables();

					// store the user's theme in the session
					if (!empty($focus->column_fields['theme'])) {
						$authenticated_user_theme = $focus->column_fields['theme'];
					} else {
						$authenticated_user_theme = $default_theme;
					}

					// store the user's language in the session
					if (!empty($focus->column_fields['language'])) {
						$authenticated_user_language = $focus->column_fields['language'];
					} else {
						$authenticated_user_language = $default_language;
					}

					coreBOS_Session::set('vtiger_authenticated_user_theme', $authenticated_user_theme);
					coreBOS_Session::set('authenticated_user_language', $authenticated_user_language);
					cbEventHandler::do_action('corebos.login', array($focus));

					$log->debug("authenticated_user_theme is $authenticated_user_theme");
					$log->debug("authenticated_user_language is $authenticated_user_language");
					$log->debug('authenticated_user_id is '. $focus->id);
					$log->debug("app_unique_key is $application_unique_key");

					// Reset number of failed login attempts
					$adb->pquery('UPDATE vtiger_users SET failed_login_attempts=0 where user_name=?', array($focus->column_fields['user_name']));

					// Clear all uploaded import files for this user if it exists
					global $import_dir;

					$tmp_file_name = $import_dir. 'IMPORT_'.$focus->id;

					if (file_exists($tmp_file_name)) {
						unlink($tmp_file_name);
					}
					if (isset($_SESSION['lastpage'])) {
						header('Location: index.php?'.$_SESSION['lastpage']);
					} else {
						header('Location: index.php');
					}
					die();
				}
				$failed_login_attempts = $adb->query_result($usrrs, 0, 'failed_login_attempts');
				$maxFailedLoginAttempts = GlobalVariable::getVariable('Application_MaxFailedLoginAttempts', 5, $userid);
				// Increment number of failed login attempts
				$adb->pquery('UPDATE vtiger_users SET failed_login_attempts=COALESCE(failed_login_attempts,0)+1 where id=?', array($userid));
				if (empty($_SESSION['login_error'])) {
					if ($failed_login_attempts >= $maxFailedLoginAttempts) {
						$errstr = getTranslatedString('ERR_MAXLOGINATTEMPTS', 'Users');
					} else {
						$errstr = getTranslatedString('ERR_INVALID_PASSWORD', 'Users');
					}
					coreBOS_Session::set('login_error', $errstr);
				}
				cbEventHandler::do_action('corebos.audit.login.attempt', array(0, $focus->column_fields['user_name'], 'Login Attempt', 0, date('Y-m-d H:i:s')));
			} else {
				coreBOS_Session::set('login_error', getTranslatedString('ERR_INVALIDUSERID', 'Users'));
			}
		}
		header('Location: index.php?module=Users&action=Login&nativelogin=1');
	}

	public function acs($sessionManager = null, $API_VERSION = '0.22', $portal = false) {
		if ($this->isActiveEither()) {
			if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
				$requestID = $_SESSION['AuthNRequestID'];
			} else {
				$requestID = null;
			}

			$this->samlclient->processResponse($requestID);

			$errors = $this->samlclient->getErrors();

			if (!empty($errors)) {
				echo json_encode(array(
					'success' => false,
					'error' => implode(', ', $errors),
				));
				exit();
			}

			if (!$this->samlclient->isAuthenticated()) {
				echo json_encode(array(
					'success' => false,
					'error' => 'Not authenticated',
				));
				exit();
			}

			coreBOS_Session::set('samlUserdata', $this->samlclient->getAttributes());
			coreBOS_Session::set('samlNameId', $this->samlclient->getNameId());
			coreBOS_Session::set('samlNameIdFormat', $this->samlclient->getNameIdFormat());
			coreBOS_Session::set('samlNameIdNameQualifier', $this->samlclient->getNameIdNameQualifier());
			coreBOS_Session::set('samlNameIdSPNameQualifier', $this->samlclient->getNameIdSPNameQualifier());
			coreBOS_Session::set('samlSessionIndex', $this->samlclient->getSessionIndex());
			coreBOS_Session::delete('AuthNRequestID');
			if (isset($_POST['RelayState'])) {
				$_SESSION['lastpage'] = $_POST['RelayState'];
			}
			if ($this->isActive()) {
				$this->authenticate();
			} else {
				$this->authenticateWS($sessionManager, $API_VERSION, $portal);
			}
		}
	}

	public function metadata() {
		if ($this->isActiveEither()) {
			try {
				$settings = new \OneLogin\Saml2\Settings($this->getSSOSettings(), true);
				$metadata = $settings->getSPMetadata();
				$errors = $settings->validateMetadata($metadata);
				if (empty($errors)) {
					header('Content-Type: text/xml');
					echo $metadata;
				} else {
					throw new OneLogin_Saml2_Error(
						'Invalid SP metadata: '.implode(', ', $errors),
						OneLogin_Saml2_Error::METADATA_SP_INVALID
					);
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}

	public function login($redirectTo = null) {
		if ($this->isActiveEither()) {
			$this->samlclient->login($redirectTo);
		}
	}

	public function logout() {
		if ($this->isActive()) {
			$nameId = isset($_SESSION['samlNameId']) ? $_SESSION['samlNameId'] : null;
			$nameIdFormat = isset($_SESSION['samlNameIdFormat']) ? $_SESSION['samlNameIdFormat'] : null;
			$nameIdNameQualifier = isset($_SESSION['samlNameIdNameQualifier']) ? $_SESSION['samlNameIdNameQualifier'] : null;
			$nameIdSPNameQualifier = isset($_SESSION['samlNameIdSPNameQualifier']) ? $_SESSION['samlNameIdSPNameQualifier'] : null;
			$sessionIndex = isset($_SESSION['samlSessionIndex']) ? $_SESSION['samlSessionIndex'] : null;
			$this->samlclient->logout(null, array(), $nameId, $sessionIndex, false, $nameIdFormat, $nameIdNameQualifier, $nameIdSPNameQualifier);
		}
	}

	public function logoutWS($sessionId, $userid) {
		global $app_strings, $current_user;
		if ($this->isActiveWS()) {
			require_once 'include/Webservices/Logout.php';
			if (!empty($userid)) {
				$seed_user = new Users();
				$current_user = $seed_user->retrieveCurrentUserInfoFromFile($userid);
				if (!empty($current_user->language)) {
					$app_strings = return_application_language($current_user->language);
				}
			} else {
				$current_user = Users::getActiveAdminUser();
			}
			vtws_logout($sessionId, $current_user);
			$nameId = isset($_SESSION['samlNameId']) ? $_SESSION['samlNameId'] : null;
			$nameIdFormat = isset($_SESSION['samlNameIdFormat']) ? $_SESSION['samlNameIdFormat'] : null;
			$nameIdNameQualifier = isset($_SESSION['samlNameIdNameQualifier']) ? $_SESSION['samlNameIdNameQualifier'] : null;
			$nameIdSPNameQualifier = isset($_SESSION['samlNameIdSPNameQualifier']) ? $_SESSION['samlNameIdSPNameQualifier'] : null;
			$sessionIndex = isset($_SESSION['samlSessionIndex']) ? $_SESSION['samlSessionIndex'] : null;
			$this->samlclient->logout(null, array(), $nameId, $sessionIndex, false, $nameIdFormat, $nameIdNameQualifier, $nameIdSPNameQualifier);
		}
	}

	public function authenticateWS($sessionManager, $API_VERSION, $portal = false) {
		global $adb;
		if ($this->isActiveWS()) {
			$settings = $this->getSettings();
			$redirectTo = $settings['WSRURL'];
			if (isset($_POST['RelayState'])) {
				$vars = array();
				parse_str(parse_url('?'.$_POST['RelayState'], PHP_URL_QUERY), $vars);
				if (!empty($vars['RTURL']) && is_numeric($vars['RTURL']) && $vars['RTURL']>1) {
					$redirectTo = $settings['WSRURL'.$vars['RTURL']];
				}
			}
			if ($portal!='LoginPortal') {
				$userid = $this->findUser($sessionManager->get('samlUserdata'));
			} else {
				$userid = $this->findUserPortal($sessionManager->get('samlUserdata'));
			}
			if ($userid) {
				$ctoid = $tpllang = '';
				$sql = "select template_language, portalloginuser
					from vtiger_portalinfo
					inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
					inner join vtiger_contactdetails on vtiger_portalinfo.id=vtiger_contactdetails.contactid
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
					where vtiger_crmentity.deleted=0 and contactid=? and isactive=1 and vtiger_customerdetails.portal=1
						and vtiger_customerdetails.support_start_date <= ? and vtiger_customerdetails.support_end_date >= ?";
				$current_date = date('Y-m-d');
				$ctors = $adb->pquery($sql, array($userid, $current_date, $current_date));
				if ($ctors && $adb->num_rows($ctors)==1) {
					$ctoid = $userid;
					$userid = $adb->query_result($ctors, 0, 'portalloginuser');
					$tpllang = $adb->query_result($ctors, 0, 'template_language');
				}
				$usrrs = $adb->pquery('select status,deleted from vtiger_users where id=?', array($userid));
				if ($adb->query_result($usrrs, 0, 'status')!='Inactive' && $adb->query_result($usrrs, 0, 'deleted')=='0') {
					$userDetails = new Users();
					$userDetails->retrieve_entity_info($userid, 'Users');
					$userDetails->authenticated = true;
					$sessionManager->destroy();
					$sessionManager->startSession();
					$sessionManager->set('authenticatedUserId', $userid);
					cbEventHandler::do_action('corebos.login', array($userDetails, $sessionManager, 'webservice'));
					$webserviceObject = VtigerWebserviceObject::fromName($adb, 'Users');
					$userId = vtws_getId($webserviceObject->getEntityId(), $userid);
					$resp = json_encode(array(
						'success' => true,
						'result' => array(
							'sessionName' => $sessionManager->getSessionId(),
							'userId' => $userId,
							'contactid' => vtws_getEntityId(getSalesEntityType($ctoid)).'x'.$ctoid,
							'language' => $tpllang,
						)
					));
					header('Location: '.$redirectTo.(strpos($redirectTo, '?')!==false ? '&' : '?').'response='.$resp);
				} else {
					$resp = json_encode(array(
						'success' => false,
						'error' => 'Invalid username or password',
					));
					cbEventHandler::do_action('corebos.audit.login.attempt', array(0, $userid, 'Login Attempt', 0, date('Y-m-d H:i:s')));
				}
			} else {
				$resp = json_encode(array(
					'success' => false,
					'error' => 'Invalid username or password',
				));
			}
			header('Location: '.$redirectTo.(strpos($redirectTo, '?')!==false ? '&' : '?').'response='.$resp);
			die();
		}
		header('Location: index.php?module=Users&action=Login&nativelogin=1');
	}

	public function saveSettings($isactive, $speid, $spacs, $spslo, $spnid, $ipeid, $ipsso, $ipslo, $ip509, $isactivews, $rwurl, $rwurl2, $rwurl3) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_ISACTIVEWS, $isactivews);
		coreBOS_Settings::setSetting(self::KEY_SPEID, $speid);
		coreBOS_Settings::setSetting(self::KEY_SPACS, $spacs);
		coreBOS_Settings::setSetting(self::KEY_SPSLO, $spslo);
		coreBOS_Settings::setSetting(self::KEY_SPNID, $spnid);
		coreBOS_Settings::setSetting(self::KEY_IPEID, $ipeid);
		coreBOS_Settings::setSetting(self::KEY_IPSSO, $ipsso);
		coreBOS_Settings::setSetting(self::KEY_IPSLO, $ipslo);
		coreBOS_Settings::setSetting(self::KEY_IP509, $ip509);
		coreBOS_Settings::setSetting(self::KEY_RWURL, $rwurl);
		coreBOS_Settings::setSetting(self::KEY_RWURL2, $rwurl2);
		coreBOS_Settings::setSetting(self::KEY_RWURL3, $rwurl3);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0'),
			'isActiveWS' => coreBOS_Settings::getSetting(self::KEY_ISACTIVEWS, '0'),
			'SPentityId' => coreBOS_Settings::getSetting(self::KEY_SPEID, ''),
			'SPACS' => coreBOS_Settings::getSetting(self::KEY_SPACS, ''),
			'SPSLO' => coreBOS_Settings::getSetting(self::KEY_SPSLO, ''),
			'SPNameID' => coreBOS_Settings::getSetting(self::KEY_SPNID, ''),
			'IPentityId' => coreBOS_Settings::getSetting(self::KEY_IPEID, ''),
			'IPSSO' => coreBOS_Settings::getSetting(self::KEY_IPSSO, ''),
			'IPSLO' => coreBOS_Settings::getSetting(self::KEY_IPSLO, ''),
			'IPx509' => coreBOS_Settings::getSetting(self::KEY_IP509, ''),
			'WSRURL' => coreBOS_Settings::getSetting(self::KEY_RWURL, ''),
			'WSRURL2' => coreBOS_Settings::getSetting(self::KEY_RWURL2, ''),
			'WSRURL3' => coreBOS_Settings::getSetting(self::KEY_RWURL3, ''),
		);
	}

	public function getSSOSettings() {
		if ($this->isActive()) {
			$acscsrf = urlencode(base64_encode('acs_'.csrf_get_tokens()));
		} else {
			$acscsrf = 'acs';
		}
		return array(
			'sp' => array (
				'entityId' => coreBOS_Settings::getSetting(self::KEY_SPEID, '').'?mode=metadata',
				'assertionConsumerService' => array (
					'url' => coreBOS_Settings::getSetting(self::KEY_SPACS, '').'?mode='.$acscsrf,
				),
				'singleLogoutService' => array (
					'url' => coreBOS_Settings::getSetting(self::KEY_SPSLO, '').'?mode=slo',
				),
				'NameIDFormat' => coreBOS_Settings::getSetting(self::KEY_SPNID, ''),
			),
			'idp' => array (
				'entityId' => coreBOS_Settings::getSetting(self::KEY_IPEID, ''),
				'singleSignOnService' => array (
					'url' => coreBOS_Settings::getSetting(self::KEY_IPSSO, ''),
				),
				'singleLogoutService' => array (
					'url' => coreBOS_Settings::getSetting(self::KEY_IPSLO, ''),
				),
				'x509cert' => coreBOS_Settings::getSetting(self::KEY_IP509, ''),
			),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function isActiveWS() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVEWS, '0');
		return ($isactive=='1');
	}

	public function isActiveEither() {
		return ($this->isActive() || $this->isActiveWS());
	}
}
?>
