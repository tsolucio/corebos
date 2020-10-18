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

class corebos_saml {

	// Configuration Keys
	const KEY_ISACTIVE = 'SAML_ISACTIVE';
	const KEY_SPEID = 'SAML_SPEID';
	const KEY_SPACS = 'SAML_SPACS';
	const KEY_SPSLO = 'SAML_SPSLO';
	const KEY_SPNID = 'SAML_SPNID';
	const KEY_IPEID = 'SAML_IPEID';
	const KEY_IPSSO = 'SAML_IPSSO';
	const KEY_IPSLO = 'SAML_IPSLO';
	const KEY_IP509 = 'SAML_IP509';

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
		if ($this->isActive()) {
			$this->samlclient = new \OneLogin\Saml2\Auth($this->getSSOSettings());
		}
	}

	public function authenticate() {
		global $adb, $log, $application_unique_key;
		if ($this->isActive()) {
			$attributes = $_SESSION['samlUserdata'];
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
			if ($userid) {
				$usrrs = $adb->pquery('select status,deleted,failed_login_attempts from vtiger_users where id=?', array($userid));
				if ($adb->query_result($usrrs, 0, 'status')!='Inactive' && $adb->query_result($usrrs, 0, 'deleted')=='0') {
					$focus = new Users();
					$focus->retrieve_entity_info($userid, 'Users');
					$focus->loadPreferencesFromDB($focus->column_fields['user_preferences']);
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
				}
				$failed_login_attempts = $adb->query_result($usrrs, 0, 'failed_login_attempts');
				$maxFailedLoginAttempts = GlobalVariable::getVariable('Application_MaxFailedLoginAttempts', 5);
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

	public function acs() {
		if ($this->isActive()) {
			if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
				$requestID = $_SESSION['AuthNRequestID'];
			} else {
				$requestID = null;
			}

			$this->samlclient->processResponse($requestID);

			$errors = $this->samlclient->getErrors();

			if (!empty($errors)) {
				echo '<p>',implode(', ', $errors),'</p>';
			}

			if (!$this->samlclient->isAuthenticated()) {
				echo "<p>Not authenticated</p>";
				echo "AAA: ".$this->samlclient->getNameId();
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
			$this->authenticate();
		}
	}

	public function metadata() {
		if ($this->isActive()) {
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

	public function login() {
		if ($this->isActive()) {
			$this->samlclient->login();
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

	public function saveSettings($isactive, $speid, $spacs, $spslo, $spnid, $ipeid, $ipsso, $ipslo, $ip509) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_SPEID, $speid);
		coreBOS_Settings::setSetting(self::KEY_SPACS, $spacs);
		coreBOS_Settings::setSetting(self::KEY_SPSLO, $spslo);
		coreBOS_Settings::setSetting(self::KEY_SPNID, $spnid);
		coreBOS_Settings::setSetting(self::KEY_IPEID, $ipeid);
		coreBOS_Settings::setSetting(self::KEY_IPSSO, $ipsso);
		coreBOS_Settings::setSetting(self::KEY_IPSLO, $ipslo);
		coreBOS_Settings::setSetting(self::KEY_IP509, $ip509);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'SPentityId' => coreBOS_Settings::getSetting(self::KEY_SPEID, ''),
			'SPACS' => coreBOS_Settings::getSetting(self::KEY_SPACS, ''),
			'SPSLO' => coreBOS_Settings::getSetting(self::KEY_SPSLO, ''),
			'SPNameID' => coreBOS_Settings::getSetting(self::KEY_SPNID, ''),
			'IPentityId' => coreBOS_Settings::getSetting(self::KEY_IPEID, ''),
			'IPSSO' => coreBOS_Settings::getSetting(self::KEY_IPSSO, ''),
			'IPSLO' => coreBOS_Settings::getSetting(self::KEY_IPSLO, ''),
			'IPx509' => coreBOS_Settings::getSetting(self::KEY_IP509, ''),
		);
	}

	public function getSSOSettings() {
		$acscsrf = urlencode(base64_encode('acs_'.csrf_get_tokens()));
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
}
?>
