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
 *  Module    : Sendgrid Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'vendor/autoload.php';
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/events/include.inc';

class corebos_sendgrid {
	// Configuration Properties
	private $usesg_transactional = 'a';
	private $srv_transactional = 'wxyz';
	private $user_transactional = '123';
	private $pass_transactional = 'abcde';
	private $usesg_marketing = 'a';
	private $srv_marketing = 'wxyz';
	private $user_marketing = '123';
	private $pass_marketing = 'abcde';
	private $apiurl_transactional;

	// Configuration Keys
	const KEY_ISACTIVE = 'sendgrid_isactive';
	const KEY_USESG_TRANSACTIONAL = 'usesgtransactional';
	const KEY_SRV_TRANSACTIONAL = 'srvtransactional';
	const KEY_USER_TRANSACTIONAL = 'usertransactional';
	const KEY_PASS_TRANSACTIONAL = 'passwordtransactional';
	const KEY_USESG_MARKETING = 'usesgmarketing';
	const KEY_SRV_MARKETING = 'srvmarketing';
	const KEY_USER_MARKETING = 'usermarketing';
	const KEY_PASS_MARKETING = 'passwordmarketing';
	const KEY_APIURL_TRANSACTIONAL = 'apiurl_transactional';

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	public $sendgridapi = null;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->usesg_transactional = coreBOS_Settings::getSetting(self::KEY_USESG_TRANSACTIONAL, '');
		$this->srv_transactional = coreBOS_Settings::getSetting(self::KEY_SRV_TRANSACTIONAL, '');
		$this->user_transactional = coreBOS_Settings::getSetting(self::KEY_USER_TRANSACTIONAL, '');
		$this->pass_transactional = coreBOS_Settings::getSetting(self::KEY_PASS_TRANSACTIONAL, '');
		$this->usesg_marketing = coreBOS_Settings::getSetting(self::KEY_USESG_MARKETING, '');
		$this->srv_marketing = coreBOS_Settings::getSetting(self::KEY_SRV_MARKETING, '');
		$this->user_marketing = coreBOS_Settings::getSetting(self::KEY_USER_MARKETING, '');
		$this->pass_marketing = coreBOS_Settings::getSetting(self::KEY_PASS_MARKETING, '');
		$this->apiurl_transactional = coreBOS_Settings::getSetting(self::KEY_APIURL_TRANSACTIONAL, '');
	}

	public function saveSettings(
		$isactive,
		$usesg_transactional,
		$srv_transactional,
		$user_transactional,
		$pass_transactional,
		$usesg_marketing,
		$srv_marketing,
		$user_marketing,
		$pass_marketing,
		$apiurl_transactional
	) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_USESG_TRANSACTIONAL, $usesg_transactional);
		coreBOS_Settings::setSetting(self::KEY_SRV_TRANSACTIONAL, $srv_transactional);
		coreBOS_Settings::setSetting(self::KEY_USER_TRANSACTIONAL, $user_transactional);
		coreBOS_Settings::setSetting(self::KEY_PASS_TRANSACTIONAL, $pass_transactional);
		coreBOS_Settings::setSetting(self::KEY_USESG_MARKETING, $usesg_marketing);
		coreBOS_Settings::setSetting(self::KEY_SRV_MARKETING, $srv_marketing);
		coreBOS_Settings::setSetting(self::KEY_USER_MARKETING, $user_marketing);
		coreBOS_Settings::setSetting(self::KEY_PASS_MARKETING, $pass_marketing);
		coreBOS_Settings::setSetting(self::KEY_APIURL_TRANSACTIONAL, $apiurl_transactional);
		global $adb;
		$em = new VTEventsManager($adb);
		if (self::useEmailHook()) {
			$em->registerHandler('corebos.filter.systemEmailClass.getname', 'include/integrations/sendgrid/sendgrid.php', 'corebos_sendgrid');
		} else {
			$em->unregisterHandler('corebos_sendgrid');
		}
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'usesg_transactional' => coreBOS_Settings::getSetting(self::KEY_USESG_TRANSACTIONAL, ''),
			'srv_transactional' => coreBOS_Settings::getSetting(self::KEY_SRV_TRANSACTIONAL, ''),
			'user_transactional' => coreBOS_Settings::getSetting(self::KEY_USER_TRANSACTIONAL, ''),
			'pass_transactional' => coreBOS_Settings::getSetting(self::KEY_PASS_TRANSACTIONAL, ''),
			'usesg_marketing' => coreBOS_Settings::getSetting(self::KEY_USESG_MARKETING, ''),
			'srv_marketing' => coreBOS_Settings::getSetting(self::KEY_SRV_MARKETING, ''),
			'user_marketing' => coreBOS_Settings::getSetting(self::KEY_USER_MARKETING, ''),
			'pass_marketing' => coreBOS_Settings::getSetting(self::KEY_PASS_MARKETING, ''),
			'apiurl_transactional' => coreBOS_Settings::getSetting(self::KEY_APIURL_TRANSACTIONAL, 'https://api.sendgrid.com/v3'),
		);
	}

	public static function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public static function emailServerCheck() {
		return self::useEmailHook();
	}

	public static function sendEMail(
		$to_email,
		$from_name,
		$from_email,
		$subject,
		$contents,
		$cc = '',
		$bcc = '',
		$attachment = '',
		$emailid = '',
		$logo = '',
		$replyto = '',
		$qrScan = '',
		$brScan = ''
	) {
		global $adb, $log;
		$inBucketServeUrl = GlobalVariable::getVariable('Debug_Email_Send_To_Inbucket', '');
		if (!empty($inBucketServeUrl)) {
			require_once 'modules/Emails/mail.php';
			require_once 'modules/Emails/Emails.php';
			return send_mail('Email', $to_email, $from_name, $from_email, $subject, $contents, $cc, $bcc, $attachment, $emailid, $logo, $replyto, $qrScan, $brScan);
		} else {
			if (self::useEmailHook()) {
				if (is_array($attachment)) {
					$atts = $attachment;
				} else {
					$atts = getAllAttachments($emailid);
				}
				if (isset($_REQUEST['filename_hidden_docu'])) {
					$file_name = $_REQUEST['filename_hidden_docu'];
					$atts[]=array('fname'=>$file_name,'fpath'=>$file_name);
				}
				if (isset($_REQUEST['filename_hidden_gendoc'])) {
					$file_name = $_REQUEST['filename_hidden_gendoc'];
					$atts[]=array('fname'=>$file_name,'fpath'=>$file_name);
				}
				if ($logo == 1) {
					$cmp = retrieveCompanyDetails();
					$atts[]=array(
						'fname' => basename($cmp['companylogo']),
						'fpath' => $cmp['companylogo'],
						'attachtype' => 'inline',
						'attachmarker' => 'logo', // '<img src="cid:logo" />',
					);
				}
				preg_match_all('/<img src="cid:(qrcode.*)"/', $contents, $matches);
				if ($qrScan == 1 || count($matches[1])>0) {
					foreach ($matches[1] as $qrname) {
						$atts[]=array(
							'fname' => $qrname.'.png',
							'fpath' => 'cache/images/'.$qrname.'.png',
							'attachtype' => 'inline',
							'attachmarker' => $qrname, //'<img src="cid:'.$qrname.'" />',
						);
					}
				}
				preg_match_all('/<img src="cid:(barcode.*)"/', $contents, $matches);
				if ($brScan == 1 || count($matches[1])>0) {
					foreach ($matches[1] as $brname) {
						$atts[]=array(
							'fname' => $brname.'.png',
							'fpath' => 'cache/images/'.$brname.'.png',
							'attachtype' => 'inline',
							'attachmarker' => $brname, //'<img src="cid:'.$brname.'" />',
						);
					}
				}
				$rs = $adb->pquery('select first_name,last_name from vtiger_users where user_name=?', array($from_name));
				if ($adb->num_rows($rs) > 0) {
					$from_name = decode_html($adb->query_result($rs, 0, 'first_name').' '.$adb->query_result($rs, 0, 'last_name'));
				}
				if (!is_array($to_email)) {
					$to_email = trim($to_email, ',');
					$to_email = explode(',', $to_email);
				}
				if (empty($cc)) {
					$cc = array();
				}
				if (!is_array($cc)) {
					$cc = self::setSendGridCCAddress($cc);
				}
				if (empty($bcc)) {
					$bcc = array();
				}
				if (!is_array($bcc)) {
					$bcc = self::setSendGridCCAddress($bcc);
				}
				$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
				$email = new \SendGrid\Mail\Mail();
				$email->setFrom($from_email, $from_name);
				$email->setSubject($subject);
				$alreadysent = array();
				foreach ($to_email as $oneemail) {
					if (!in_array($oneemail, $alreadysent)) {
						$email->addTo($oneemail);
						$alreadysent[] = $oneemail;
					}
				}
				foreach ($cc as $oneemail) {
					if (!in_array($oneemail, $alreadysent)) {
						$email->addCc($oneemail);
						$alreadysent[] = $oneemail;
					}
				}
				foreach ($bcc as $oneemail) {
					if (!in_array($oneemail, $alreadysent)) {
						$email->addBcc($oneemail);
						$alreadysent[] = $oneemail;
					}
				}
				if (!empty($replyto)) {
					$email->setReplyTo($replyto);
				}
				//$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
				$email->addContent('text/html', $contents);
				$email->addAttachments(self::convertAttachmentArray($atts));
				$email->addCategory('Transactional');
				$email->addCustomArg('crmid', (string)$emailid);
				$sendgrid = new \SendGrid(coreBOS_Settings::getSetting(self::KEY_PASS_TRANSACTIONAL, ''));
				try {
					$response = $sendgrid->send($email);
					if ($response->statusCode() > 299) {
						$log->fatal('Caught SENDGRID email error: '.$response->statusCode());
						$log->fatal($response->body());
						return $response->statusCode();
					}
					return 1;
				} catch (Exception $e) {
					$log->fatal('Caught SENDGRID email exception: '. $e->getMessage());
					return 0;
				}
			}
		}
	}

	/** convert coreBOS attachments array to SendGrid attachments array
	 * @param array filename, filepath, "attachment | inline", "inline position marker"
	 * @return array "base64 encoded content2", "filename", "mime type", "attachment | inline", "inline position marker"
	 */
	public static function convertAttachmentArray($attachments) {
		$atts = array();
		if (isset($attachments['direct']) && $attachments['direct']) {
			foreach ($attachments as $att) {
				if (empty($att)) {
					continue;
				}
				if (is_array($att)) {
					foreach ($att as $file) {
						$atts[] = array(
							base64_encode(file_get_contents($file['name'])),
							mime_content_type($file['name']),
							isset($file['filename']) ? $file['filename'] : $file['name'],
							'attachment',
							''
						);
					}
				}
			}
			return $atts;
		}
		foreach ($attachments as $att) {
			if (empty($att)) {
				continue;
			}
			if (is_string($att)) {
				$fname = $att;
				$att = array();
				$att['fpath'] = $fname;
				$att['fname'] = basename($fname);
			}
			$attype = isset($att['attachtype']) ? $att['attachtype'] : 'attachment';
			$atposition = isset($att['attachmarker']) ? $att['attachmarker'] : '';
			$atts[] = array(
				base64_encode(file_get_contents($att['fpath'])),
				mime_content_type($att['fpath']),
				$att['fname'],
				$attype,
				$atposition,
			);
		}
		return $atts;
	}

	/** Function to set the CC or BCC addresses in the mail
	 * @param string $cc_bcc - comma separated list of emails to set as CC or BCC in the mail
	 * @return array of emails
	 */
	public static function setSendGridCCAddress($cc_bcc) {
		global $log;
		$log->debug('> setSendGridCCAddress');
		if ($cc_bcc != '') {
			$address = array();
			$ccmail = explode(',', trim($cc_bcc, ','));
			for ($i=0; $i<count($ccmail); $i++) {
				$addr = $ccmail[$i];
				$cc_name = preg_replace('/([^@]+)@(.*)/', '$1', $addr); // First Part Of Email
				if (stripos($addr, '<')) {
					$name_addr_pair = explode('<', $ccmail[$i]);
					$cc_name = $name_addr_pair[0];
					$addr = trim($name_addr_pair[1], '>');
				}
				if ($ccmail[$i] != '') {
					$address[] = $addr;
				}
			}
			$log->debug('< setSendGridCCAddress');
			return array_unique($address);
		}
		$log->debug('< setSendGridCCAddress');
		return $cc_bcc;
	}

	public static function useEmailHook() {
		$sendgrid = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		$usetrans = coreBOS_Settings::getSetting(self::KEY_USESG_TRANSACTIONAL, '0');
		return ($sendgrid != '0' && $usetrans != '0');
	}

	public function handleFilter($handlerType, $parameter) {
		if ($handlerType == 'corebos.filter.systemEmailClass.getname' && corebos_sendgrid::useEmailHook()) {
			return array('corebos_sendgrid', 'include/integrations/sendgrid/sendgrid.php');
		} else {
			return $parameter;
		}
	}

	public function getEmailTemplates() {
		require_once 'vtlib/Vtiger/Net/Client.php';
		global $log;
		if (self::useEmailHook()) {
			$apiUrl = coreBOS_Settings::getSetting(self::KEY_APIURL_TRANSACTIONAL, '');
			$apiKey = coreBOS_Settings::getSetting(self::KEY_PASS_TRANSACTIONAL, '');
			if (!empty($apiKey) && !empty($apiUrl)) {
				$apiUrl .= '/templates?generations=dynamic';
				$client = new Vtiger_Net_Client($apiUrl);
				$client->setHeaders(array(
					'Authorization' => 'Bearer '.$apiKey,
					'Content-Type' => 'application/json'
				));
				try {
					$response = $client->doGet();
					if (strpos($response, "<h1>406 Not Acceptable</h1>") === false) {
						coreBOS_Settings::setSetting('SG_DYNAMICTPLS', $response);
					}
				} catch (Exception $e) {
					$response = coreBOS_Settings::getSetting('SG_DYNAMICTPLS', '');
					$log->fatal('Exeption Encountered: '. $e->getMessage());
				}
				if (!empty($response)) {
					return $response;
				}
				return '';
			}
		}
	}

	public function sendemailtemplate(
		$templateId,
		$recordId,
		$moduleName,
		$toEmail,
		$subject,
		$attachment,
		$fromName,
		$fromEmail = '',
		$cc = '',
		$bcc = '',
		$replyto = ''
	) {
		require_once 'include/Webservices/Retrieve.php';
		require_once 'data/CRMEntity.php';
		global $current_user, $logbg;
		if (empty($current_user)) {
			$current_user = Users::getActiveAdminUser();
		}
		if (empty($fromEmail)) {
			$fromEmail = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
		}
		$apiKey = coreBOS_Settings::getSetting(self::KEY_PASS_TRANSACTIONAL, '');
		if (self::useEmailHook() && !empty($apiKey)) {
			try {
				$dataArr = array();
				$email = new \SendGrid\Mail\Mail();
				$data = vtws_retrieve($recordId, $current_user);
				if ($data) {
					$dataArr[$moduleName] = $data;
					$relatedMods = $this->checkForRelatedInfo($data);
					for ($y=0; $y < count($relatedMods); $y++) {
						$relcrmId = CRMEntity::getCRMIDfromUUID($relatedMods[$y]['cbuuid']);
						$relmodName = $relatedMods[$y]['modulename'];
						$relwsId = vtws_getEntityId($relmodName).'x'.$relcrmId;
						$reldata = vtws_retrieve($relwsId, $current_user);
						$dataArr[$relmodName] = $reldata;
					}
				}
				if (!empty($attachment) && is_array($attachment)) {
					$email->addAttachments(self::convertAttachmentArray($attachment));
				}
				if (!empty($cc) && !is_array($cc)) {
					$cc = self::setSendGridCCAddress($cc);
				}
				if (!empty($bcc) && !is_array($bcc)) {
					$cc = self::setSendGridCCAddress($bcc);
				}
				$alreadyinList = array();
				if (is_array($cc)) {
					foreach ($cc as $ccemail) {
						if (!in_array($ccemail, $alreadyinList)) {
							$email->addCc($ccemail);
							$alreadyinList[] = $ccemail;
						}
					}
				}
				if (is_array($bcc)) {
					foreach ($bcc as $bccemail) {
						if (!in_array($bccemail, $alreadyinList)) {
							$email->addBcc($bccemail);
							$alreadyinList[] = $bccemail;
						}
					}
				}
				if (!empty($replyto)) {
					$email->setReplyTo($replyto);
				}
				$email->setFrom($fromEmail, $fromName);
				$email->setSubject($subject);
				if (is_array($toEmail)) {
					foreach ($toEmail as $email_to) {
						$email->addTo(
							$email_to,
							$email_to,
							$dataArr
						);
					}
				} else {
					$email->addTo(
						$toEmail,
						$toEmail,
						$dataArr
					);
				}
				$email->setTemplateId($templateId);
				$sendgrid = new \SendGrid($apiKey);
				$response = $sendgrid->send($email);
				if ($response->statusCode() != 202) {
					$logbg->debug("sendemailtemplate\n:". $response->statusCode());
					return $response->statusCode();
				}
				return 1;
			} catch (Exception $e) {
				$logbg->debug('sendemailtemplate:: Exeption Encountered: '. $e->getMessage());
				return 0;
			}
		}
	}
	public function checkForRelatedInfo($dataArr) {
		$relmods = array();
		foreach ($dataArr as $el) {
			if (is_array($el) && !empty($el['module']) && !empty($el['cbuuid']) && !in_array($el['module'], $relmods)) {
				$relmods[] = array(
					'modulename' => $el['module'],
					'cbuuid' => $el['cbuuid']
				);
			}
		}
		return $relmods;
	}
}
?>