<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

// Add Zend library path
global $root_directory;
set_include_path($root_directory .'/include' . PATH_SEPARATOR . get_include_path());

vimport('~~/include/Zend/Oauth.php');

vimport('~~/include/Zend/Oauth/Consumer.php');
vimport('~~/include/Zend/Gdata.php');
vimport('~~/include/Zend/Crypt/Rsa/Key/Private.php');
vimport('~~/include/Zend/Gdata/Query.php');

class Google_Oauth_Connector {

    var $db = false;
    var $userId = false;
    protected $_scopes = array(
        'Contacts' => 'http://www.google.com/m8/feeds',
        'Calendar' => 'http://www.google.com/calendar/feeds',
            // ADD MORE...
    );
    protected $_oauthOptions = array(
        'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
        'version' => '1.0',
        'consumerKey' => '639253257022.apps.googleusercontent.com',
        'consumerSecret' => 'CxnOsnYx_RNyTWVfzTIenmhQ',
        'signatureMethod' => 'HMAC-SHA1',
        'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
        'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
        'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
        'callbackUrl' => '' // Will be updated at runtime if not specified.
    );

    function __construct($callbackUrl, $userId = false) {
//		if (empty($this->_oauthOptions['callbackUrl'])) {
//			$this->_oauthOptions['callbackUrl'] = $this->getCurrentUrl();
//		}
        self::initializeSchema();
        $this->userId = $userId;
        $this->_oauthOptions['callbackUrl'] = $callbackUrl;
        $this->db = PearDatabase::getInstance();
    }

    protected function getCurrentUrl() {
        global $_SERVER;
        /**
         * Filter php_self to avoid a security vulnerability.
         */
        $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $host = $_SERVER['HTTP_HOST'];
        if ($_SERVER['SERVER_PORT'] != '' &&
                (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
                ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        } else {
            $port = '';
        }
        return $protocol . $host . $port . $php_request_uri;
    }

    function hasStoredToken($service, $accessToken = false, $requestToken = false) {
        if(!$this->userId)
            $this->userId = Users_Record_Model::getCurrentUserModel()->getId();
        
        if (!$accessToken && !$requestToken){
            $query = "SELECT  1 FROM vtiger_google_oauth WHERE  userid=? and service=?";
            $params = array($this->userId, $service);
        }
        else if ($accessToken){
            $query = "SELECT  access_token FROM vtiger_google_oauth WHERE  userid=? and service=? AND access_token<>? AND access_token IS NOT NULL";
            $params = array($this->userId, $service, '');
        }
        else if ($requestToken){
            $query = "SELECT  request_token FROM vtiger_google_oauth WHERE  userid=? and service=? AND request_token<>? AND request_token IS NOT NULL";
            $params = array($this->userId, $service, '');
        }
        $result = $this->db->pquery($query, $params);
        if ($this->db->num_rows($result) > 0) {

            return true;
        }
        return false;
    }

    /**
     * TODO:
     * Store token-data in DB instead of serializing in session.
     * Rebuild object with the token-data stored.
     */
    protected function storeAccessToken($service, $token) {
        $user = Users_Record_Model::getCurrentUserModel();
        $query = "INSERT INTO vtiger_google_oauth(service,access_token,userid) VALUES(?,?,?)";
        $params = array($service, base64_encode(serialize($token)), $user->getid());
        if (self::hasStoredToken($service, false, true)) {
            $query = "UPDATE vtiger_google_oauth SET access_token=? WHERE userid=? AND  service=?";
            $params = array(base64_encode(serialize($token)), $user->getId(), $service);
        }

        $this->db->pquery($query, $params);
    }

    protected function retreiveAccessToken($service) {
        if(!$this->userId)
            $this->userId = Users_Record_Model::getCurrentUserModel()->getId();
        
        $query = "SELECT access_token FROM vtiger_google_oauth WHERE userid=? AND service =?";
        $params = array($this->userId, $service);

        $result = $this->db->pquery($query, $params);
        $data = $this->db->fetch_array($result);
        $token = unserialize(base64_decode($data['access_token']));
        return $token;
    }

    protected function storeRequestToken($service, $token) {
        $user = Users_Record_Model::getCurrentUserModel();
        $query = "DELETE FROM vtiger_google_oauth where service=? and userid=?";
        $this->db->pquery($query, array($service, $user->getId()));

        $query = "INSERT INTO vtiger_google_oauth(service,request_token,userid) values(?,?,?)";
        $this->db->pquery($query, array($service, base64_encode(serialize($token)), $user->getId()));

    }

    protected function retrieveRequestToken($service) {
        $user = Users_Record_Model::getCurrentUserModel();

        $query = "SELECT request_token FROM vtiger_google_oauth WHERE userid=? AND service =?";
        $params = array($user->getId(), $service);

        $result = $this->db->pquery($query, $params);
        $data = $this->db->fetch_array($result);
        $token = unserialize(base64_decode($data['request_token']));
        return $token;
    }

    function getHttpClient($service) {

        $token = NULL;
        if (!$this->hasStoredToken($service, true, false, $this->userId)) {
            $consumer = new Zend_Oauth_Consumer($this->_oauthOptions);

            if (isset($_GET['oauth_token'])) {

                $token = $consumer->getAccessToken($_GET, $this->retrieveRequestToken($service));
                $this->storeAccessToken($service, $token);
            } else {

                $scope = isset($this->_scopes[$service]) ? $this->_scopes[$service] : false;

                if ($scope === false) {
                    throw new Exception("Invalid scope specified");
                }

                $token = $consumer->getRequestToken(array('scope' => $scope));
                $this->storeRequestToken($service, $token);
                $consumer->redirect();
                exit;
            }
        } else {
            $token = $this->retreiveAccessToken($service);
        }

        return $token->getHttpClient($this->_oauthOptions);
    }
    
    
	 public static function initializeSchema(){
		 if(!Vtiger_Utils::CheckTable('vtiger_google_oauth')) {
                Vtiger_Utils::CreateTable('vtiger_google_oauth',
                        '(service varchar(64),request_token text,access_token text,userid int)',true);
            }
	 }

}

