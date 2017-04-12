<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'include/integrations/GContacts/GContacts.php';
require_once 'vtlib/Vtiger/Net/Client.php';

class Google_Oauth2_Connector {

    protected $service_provider = 'Google';

    protected $source_module;

    protected $user_id;

    protected $db;

    protected $table_name = 'its4you_googlesync4you_access';

    protected $service_name;

    protected $client_id;

    protected $client_secret;

    protected $redirect_uri;

    protected $scope;

    protected $state;

    protected $response_type = 'code';

    protected $access_type = 'offline';

    protected $approval_prompt = 'force';

    protected $scopes = array(
        'Contacts' => 'https://www.google.com/m8/feeds',
        'Calendar' => 'https://www.googleapis.com/auth/calendar',
    );

    public $token;

    const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';

    const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';

    const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/revoke';

    public function __construct($module,$userId=false) {
        global $site_URL;
        $this->source_module = $module;
        if($userId) $this->user_id = $userId;
        $this->service_name = $this->service_provider . $module;
        $gc = new corebos_gcontacts();
        $settings = $gc->getSettings();
        $this->client_id = $settings['clientId'];
        $this->client_secret = $settings['clientSecret'];
        $this->redirect_uri = rtrim($site_URL, '/') . '/index.php?module=Contacts&action=ContactsAjax&file=List&operation=sync&sourcemodule=' . $this->source_module . '&service=' . $this->service_name;
        $this->scope = $this->scopes[$this->source_module];
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getClientSecret() {
        return $this->client_secret;
    }

    public function getRedirectUri() {
        return $this->redirect_uri;
    }

    public function getScope() {
        return $this->scope;
    }

    public function getAccessType() {
        return $this->access_type;
    }

    public function getApprovalPrompt() {
        return $this->approval_prompt;
    }

    public function getAccessToken() {
        return json_encode($this->token['access_token']);
    }

    protected function getAuthUrl() {
        $params = array(
            'response_type='.  urlencode($this->response_type),
            'redirect_uri=' . urlencode($this->redirect_uri),
            'client_id=' . urlencode($this->client_id),
            'scope=' . urlencode($this->scope),
            'access_type=' . urlencode($this->access_type),
            'approval_prompt=' . urlencode($this->approval_prompt),
        );
        $queryString = implode('&', $params);
        return self::OAUTH2_AUTH_URL . "?$queryString";
    }

    public function hasStoredToken() {
        if(!isset($this->user_id)) $this->user_id = $_SESSION['authenticated_user_id'];
        if(!isset($this->db)) $this->db = PearDatabase::getInstance();
        $sql = 'SELECT 1 FROM ' . $this->table_name . ' WHERE userid = ? AND service = ?';
        $params = array($this->user_id, $this->service_name);
        $res = $this->db->pquery($sql,$params);
        $hasStoredToken = ($this->db->num_rows($res) > 0) ? true : false;
        return $hasStoredToken;
    }

    public function getState($source) {
        global $site_URL;
        $callbackUri = rtrim($site_URL, '/') . '/index.php?module=Contacts&action=ContactsAjax&file=List&operation=sync&sourcemodule=' . $this->source_module . '&service=' . $source;
        $stateDetails['url'] = $callbackUri;
        $parse = parse_url($site_URL);
        $ipAddr = getHostByName($parse['host']);
        // to prevent domain name forgery
        $stateDetails['dnf'] = md5($ipAddr);
        $state = json_encode($stateDetails,JSON_FORCE_OBJECT);
        return $state;
    }

    protected function setState() {
        $this->state = $this->getState($this->service_name);
    }

    protected function showConsentScreen() {
        header('Location: ' . $this->getAuthUrl());
    }

    protected function decryptAuthCode($cipherText) {
        $publicKey = VtigerConfig::getOD('OAUTHREDIR_PUBK');
        $pubkey_res = openssl_get_publickey($publicKey);
        $base64Decoded = base64_decode($cipherText);
        openssl_public_decrypt($base64Decoded,$decipheredText,$pubkey_res);
        return $decipheredText;
    }

    protected function fireRequest($url,$headers,$params=array(),$method='POST') {
        $httpClient = new Vtiger_Net_Client($url);
        if(count($headers)) $httpClient->setHeaders($headers);
        switch ($method) {
            case 'POST':
                $response = $httpClient->doPost($params);
                break;
            case 'GET':
                $response = $httpClient->doGet($params);
                break;
        }
        return $response;
    }

    protected function exchangeCodeForToken($code) {
        $params = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri
        );
        $response = $this->fireRequest(self::OAUTH2_TOKEN_URI,array(),$params);
        return $response;
    }

    protected function storeToken($token) {
        global $current_user;
        if(!isset($this->user_id)) $this->user_id = $current_user->id;
        if(!isset($this->db)) $this->db = PearDatabase::getInstance();
        $decodedToken = json_decode($token,true);
        $refresh_token = $decodedToken['refresh_token'];
        unset($decodedToken['refresh_token']);
        $decodedToken['created'] = time();
        $accessToken = json_encode($decodedToken);
        $params = array($this->service_name,$accessToken,$refresh_token,$this->user_id);
        $sql = 'INSERT INTO ' . $this->table_name . '(service,synctoken,refresh_token,userid)'
                . ' VALUES (' . generateQuestionMarks($params) . ')';
        $this->db->pquery($sql,$params);
    }

    protected function retreiveToken() {
        if(!$this->user_id) $this->user_id = Users_Record_Model::getCurrentUserModel()->getId();
        $query = 'SELECT synctoken,refresh_token FROM ' . $this->table_name . ' WHERE userid=? AND service =?';
        $params = array($this->user_id, $this->service_name);
        $result = $this->db->pquery($query, $params);
        $data = $this->db->fetch_array($result);
        $decodedAccessToken = json_decode(decode_html($data['synctoken']),true);
        $refreshToken = decode_html($data['refresh_token']);
        return array(
            'access_token' => $decodedAccessToken,
            'refresh_token' => $refreshToken
        );
    }

    protected function setToken($token) {
        $this->token = $token;
    }

    public function isTokenExpired() {
        if (null == $this->token || null == $this->token['access_token']) return true;
        // If the token is set to expire in the next 30 seconds.
        $expired = ($this->token['access_token']['created']
            + ($this->token['access_token']['expires_in'] - 30)) < time();
        return $expired;
    }

    protected function updateAccessToken($accesstoken,$refreshtoken) {
        if(!isset($this->db)) $this->db = PearDatabase::getInstance();
        $sql = 'UPDATE ' . $this->table_name . ' SET synctoken = ? WHERE refresh_token = ? AND service = ?';
        $params = array($accesstoken,$refreshtoken,$this->service_name);
        $this->db->pquery($sql,$params);
    }

    public function refreshToken() {
        if($this->token['refresh_token'] == null) throw new AppException('refresh token is null');
        $params = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->token['refresh_token'],
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        );
        $encodedToken = $this->fireRequest(self::OAUTH2_TOKEN_URI,array(),$params);
        $decodedToken = json_decode($encodedToken,true);
        $decodedToken['created'] = time();
        $token['access_token'] = $decodedToken;
        $token['refresh_token'] = $this->token['refresh_token'];
        $this->updateAccessToken(json_encode($decodedToken),$token['refresh_token']);
        $this->setToken($token);
    }

    public function authorize() {
        if($this->hasStoredToken()) {
            $token = $this->retreiveToken();
            $this->setToken($token);
            if($this->isTokenExpired()) $this->refreshToken();
            return $this;
        } else {
            if($_REQUEST['service'] && $_REQUEST['code']) {
                $authCode = $_REQUEST['code'];
                $token = $this->exchangeCodeForToken($authCode);
                $this->storeToken($token);
                echo '<script>window.opener.googleContactsSynch("Contacts",window.opener.document.getElementById("LISTVIEWBASIC_GOOGLE_CONTACTS"));window.close();</script>'; exit;
            } else if(!empty($_REQUEST['service']) and $_REQUEST['service']) {
                echo '<script>window.close();</script>'; exit;
            } else {
                $this->setState();
                $this->showConsentScreen();
            }
        }
    }

}

?>