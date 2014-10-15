<?php




/**
*
* soap_server allows the user to create a SOAP server
* that is capable of receiving messages and returning responses
*
* NOTE: WSDL functionality is experimental
*
* @author   Dietrich Ayala <dietrich@ganx4.com>
* @version  $Id: class.soap_server.php,v 1.48 2005/08/04 01:27:42 snichol Exp $
* @access   public
*/
class soap_server extends nusoap_base {
	/**
	 * HTTP headers of request
	 * @var array
	 * @access private
	 */
	var $headers = array();
	/**
	 * HTTP request
	 * @var string
	 * @access private
	 */
	var $request = '';
	/**
	 * SOAP headers from request (incomplete namespace resolution; special characters not escaped) (text)
	 * @var string
	 * @access public
	 */
	var $requestHeaders = '';
	/**
	 * SOAP body request portion (incomplete namespace resolution; special characters not escaped) (text)
	 * @var string
	 * @access public
	 */
	var $document = '';
	/**
	 * SOAP payload for request (text)
	 * @var string
	 * @access public
	 */
	var $requestSOAP = '';
	/**
	 * requested method namespace URI
	 * @var string
	 * @access private
	 */
	var $methodURI = '';
	/**
	 * name of method requested
	 * @var string
	 * @access private
	 */
	var $methodname = '';
	/**
	 * method parameters from request
	 * @var array
	 * @access private
	 */
	var $methodparams = array();
	/**
	 * SOAP Action from request
	 * @var string
	 * @access private
	 */
	var $SOAPAction = '';
	/**
	 * character set encoding of incoming (request) messages
	 * @var string
	 * @access public
	 */
	var $xml_encoding = '';
	/**
	 * toggles whether the parser decodes element content w/ utf8_decode()
	 * @var boolean
	 * @access public
	 */
    var $decode_utf8 = true;

	/**
	 * HTTP headers of response
	 * @var array
	 * @access public
	 */
	var $outgoing_headers = array();
	/**
	 * HTTP response
	 * @var string
	 * @access private
	 */
	var $response = '';
	/**
	 * SOAP headers for response (text)
	 * @var string
	 * @access public
	 */
	var $responseHeaders = '';
	/**
	 * SOAP payload for response (text)
	 * @var string
	 * @access private
	 */
	var $responseSOAP = '';
	/**
	 * method return value to place in response
	 * @var mixed
	 * @access private
	 */
	var $methodreturn = false;
	/**
	 * whether $methodreturn is a string of literal XML
	 * @var boolean
	 * @access public
	 */
	var $methodreturnisliteralxml = false;
	/**
	 * SOAP fault for response (or false)
	 * @var mixed
	 * @access private
	 */
	var $fault = false;
	/**
	 * text indication of result (for debugging)
	 * @var string
	 * @access private
	 */
	var $result = 'successful';

	/**
	 * assoc array of operations => opData; operations are added by the register()
	 * method or by parsing an external WSDL definition
	 * @var array
	 * @access private
	 */
	var $operations = array();
	/**
	 * wsdl instance (if one)
	 * @var mixed
	 * @access private
	 */
	var $wsdl = false;
	/**
	 * URL for WSDL (if one)
	 * @var mixed
	 * @access private
	 */
	var $externalWSDLURL = false;
	/**
	 * whether to append debug to response as XML comment
	 * @var boolean
	 * @access public
	 */
	var $debug_flag = false;


	/**
	* constructor
    * the optional parameter is a path to a WSDL file that you'd like to bind the server instance to.
	*
    * @param mixed $wsdl file path or URL (string), or wsdl instance (object)
	* @access   public
	*/
	function soap_server($wsdl=false){
		parent::nusoap_base();
		// turn on debugging?
		global $debug;
		global $HTTP_SERVER_VARS;

		if (isset($_SERVER)) {
			$this->debug("_SERVER is defined:");
			$this->appendDebug($this->varDump($_SERVER));
		} elseif (isset($HTTP_SERVER_VARS)) {
			$this->debug("HTTP_SERVER_VARS is defined:");
			$this->appendDebug($this->varDump($HTTP_SERVER_VARS));
		} else {
			$this->debug("Neither _SERVER nor HTTP_SERVER_VARS is defined.");
		}

		if (isset($debug)) {
			$this->debug("In soap_server, set debug_flag=$debug based on global flag");
			$this->debug_flag = $debug;
		} elseif (isset($_SERVER['QUERY_STRING'])) {
			$qs = explode('&', $_SERVER['QUERY_STRING']);
			foreach ($qs as $v) {
				if (substr($v, 0, 6) == 'debug=') {
					$this->debug("In soap_server, set debug_flag=" . substr($v, 6) . " based on query string #1");
					$this->debug_flag = substr($v, 6);
				}
			}
		} elseif (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
			$qs = explode('&', $HTTP_SERVER_VARS['QUERY_STRING']);
			foreach ($qs as $v) {
				if (substr($v, 0, 6) == 'debug=') {
					$this->debug("In soap_server, set debug_flag=" . substr($v, 6) . " based on query string #2");
					$this->debug_flag = substr($v, 6);
				}
			}
		}

		// wsdl
		if($wsdl){
			$this->debug("In soap_server, WSDL is specified");
			if (is_object($wsdl) && (get_class($wsdl) == 'wsdl')) {
				$this->wsdl = $wsdl;
				$this->externalWSDLURL = $this->wsdl->wsdl;
				$this->debug('Use existing wsdl instance from ' . $this->externalWSDLURL);
			} else {
				$this->debug('Create wsdl from ' . $wsdl);
				$this->wsdl = new wsdl($wsdl);
				$this->externalWSDLURL = $wsdl;
			}
			$this->appendDebug($this->wsdl->getDebug());
			$this->wsdl->clearDebug();
			if($err = $this->wsdl->getError()){
				die('WSDL ERROR: '.$err);
			}
		}
	}

	/**
	* processes request and returns response
	*
	* @param    string $data usually is the value of $HTTP_RAW_POST_DATA
	* @access   public
	*/
	function service($data){
		global $HTTP_SERVER_VARS;

		if (isset($_SERVER['QUERY_STRING'])) {
			$qs = $_SERVER['QUERY_STRING'];
		} elseif (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
			$qs = $HTTP_SERVER_VARS['QUERY_STRING'];
		} else {
			$qs = '';
		}
		$this->debug("In service, query string=$qs");

		if (ereg('wsdl', $qs) ){
			$this->debug("In service, this is a request for WSDL");
			if($this->externalWSDLURL){
              if (strpos($this->externalWSDLURL,"://")!==false) { // assume URL
				header('Location: '.$this->externalWSDLURL);
              } else { // assume file
                header("Content-Type: text/xml\r\n");
                $fp = fopen($this->externalWSDLURL, 'r');
                fpassthru($fp);
              }
			} elseif ($this->wsdl) {
				header("Content-Type: text/xml; charset=UTF-8\r\n");
				print $this->wsdl->serialize($this->debug_flag);
				if ($this->debug_flag) {
					$this->debug('wsdl:');
					$this->appendDebug($this->varDump($this->wsdl));
					print $this->getDebugAsXMLComment();
				}
			} else {
				header("Content-Type: text/html; charset=UTF-8\r\n");
				print "This service does not provide WSDL";
			}
		} elseif ($data == '' && $this->wsdl) {
			$this->debug("In service, there is no data, so return Web description");
			print $this->wsdl->webDescription();
		} else {
			$this->debug("In service, invoke the request");
			$this->parse_request($data);
			if (! $this->fault) {
				$this->invoke_method();
			}
			if (! $this->fault) {
				$this->serialize_return();
			}
			$this->send_response();
		}
	}

	/**
	* parses HTTP request headers.
	*
	* The following fields are set by this function (when successful)
	*
	* headers
	* request
	* xml_encoding
	* SOAPAction
	*
	* @access   private
	*/
	function parse_http_headers() {
		global $HTTP_SERVER_VARS;

		$this->request = '';
		$this->SOAPAction = '';
		if(function_exists('getallheaders')){
			$this->debug("In parse_http_headers, use getallheaders");
			$headers = getallheaders();
			foreach($headers as $k=>$v){
				$k = strtolower($k);
				$this->headers[$k] = $v;
				$this->request .= "$k: $v\r\n";
				$this->debug("$k: $v");
			}
			// get SOAPAction header
			if(isset($this->headers['soapaction'])){
				$this->SOAPAction = str_replace('"','',$this->headers['soapaction']);
			}
			// get the character encoding of the incoming request
			if(isset($this->headers['content-type']) && strpos($this->headers['content-type'],'=')){
				$enc = str_replace('"','',substr(strstr($this->headers["content-type"],'='),1));
				if(eregi('^(ISO-8859-1|US-ASCII|UTF-8)$',$enc)){
					$this->xml_encoding = strtoupper($enc);
				} else {
					$this->xml_encoding = 'US-ASCII';
				}
			} else {
				// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
				$this->xml_encoding = 'UTF-8';
			}
		} elseif(isset($_SERVER) && is_array($_SERVER)){
			$this->debug("In parse_http_headers, use _SERVER");
			foreach ($_SERVER as $k => $v) {
				if (substr($k, 0, 5) == 'HTTP_') {
					$k = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($k, 5)))); 	                                         $k = strtolower(substr($k, 5));
				} else {
					$k = str_replace(' ', '-', strtolower(str_replace('_', ' ', $k))); 	                                         $k = strtolower($k);
				}
				if ($k == 'soapaction') {
					// get SOAPAction header
					$k = 'SOAPAction';
					$v = str_replace('"', '', $v);
					$v = str_replace('\\', '', $v);
					$this->SOAPAction = $v;
				} else if ($k == 'content-type') {
					// get the character encoding of the incoming request
					if (strpos($v, '=')) {
						$enc = substr(strstr($v, '='), 1);
						$enc = str_replace('"', '', $enc);
						$enc = str_replace('\\', '', $enc);
						if (eregi('^(ISO-8859-1|US-ASCII|UTF-8)$', $enc)) {
							$this->xml_encoding = strtoupper($enc);
						} else {
							$this->xml_encoding = 'US-ASCII';
						}
					} else {
						// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
						$this->xml_encoding = 'UTF-8';
					}
				}
				$this->headers[$k] = $v;
				$this->request .= "$k: $v\r\n";
				$this->debug("$k: $v");
			}
		} elseif (is_array($HTTP_SERVER_VARS)) {
			$this->debug("In parse_http_headers, use HTTP_SERVER_VARS");
			foreach ($HTTP_SERVER_VARS as $k => $v) {
				if (substr($k, 0, 5) == 'HTTP_') {
					$k = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($k, 5)))); 	                                         $k = strtolower(substr($k, 5));
				} else {
					$k = str_replace(' ', '-', strtolower(str_replace('_', ' ', $k))); 	                                         $k = strtolower($k);
				}
				if ($k == 'soapaction') {
					// get SOAPAction header
					$k = 'SOAPAction';
					$v = str_replace('"', '', $v);
					$v = str_replace('\\', '', $v);
					$this->SOAPAction = $v;
				} else if ($k == 'content-type') {
					// get the character encoding of the incoming request
					if (strpos($v, '=')) {
						$enc = substr(strstr($v, '='), 1);
						$enc = str_replace('"', '', $enc);
						$enc = str_replace('\\', '', $enc);
						if (eregi('^(ISO-8859-1|US-ASCII|UTF-8)$', $enc)) {
							$this->xml_encoding = strtoupper($enc);
						} else {
							$this->xml_encoding = 'US-ASCII';
						}
					} else {
						// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
						$this->xml_encoding = 'UTF-8';
					}
				}
				$this->headers[$k] = $v;
				$this->request .= "$k: $v\r\n";
				$this->debug("$k: $v");
			}
		} else {
			$this->debug("In parse_http_headers, HTTP headers not accessible");
			$this->setError("HTTP headers not accessible");
		}
	}

	/**
	* parses a request
	*
	* The following fields are set by this function (when successful)
	*
	* headers
	* request
	* xml_encoding
	* SOAPAction
	* request
	* requestSOAP
	* methodURI
	* methodname
	* methodparams
	* requestHeaders
	* document
	*
	* This sets the fault field on error
	*
	* @param    string $data XML string
	* @access   private
	*/
	function parse_request($data='') {
		$this->debug('entering parse_request()');
		$this->parse_http_headers();
		$this->debug('got character encoding: '.$this->xml_encoding);
		// uncompress if necessary
		if (isset($this->headers['content-encoding']) && $this->headers['content-encoding'] != '') {
			$this->debug('got content encoding: ' . $this->headers['content-encoding']);
			if ($this->headers['content-encoding'] == 'deflate' || $this->headers['content-encoding'] == 'gzip') {
		    	// if decoding works, use it. else assume data wasn't gzencoded
				if (function_exists('gzuncompress')) {
					if ($this->headers['content-encoding'] == 'deflate' && $degzdata = @gzuncompress($data)) {
						$data = $degzdata;
					} elseif ($this->headers['content-encoding'] == 'gzip' && $degzdata = gzinflate(substr($data, 10))) {
						$data = $degzdata;
					} else {
						$this->fault('Client', 'Errors occurred when trying to decode the data');
						return;
					}
				} else {
					$this->fault('Client', 'This Server does not support compressed data');
					return;
				}
			}
		}
		$this->request .= "\r\n".$data;
		$data = $this->parseRequest($this->headers, $data);
		$this->requestSOAP = $data;
		$this->debug('leaving parse_request');
	}

	/**
	* invokes a PHP function for the requested SOAP method
	*
	* The following fields are set by this function (when successful)
	*
	* methodreturn
	*
	* Note that the PHP function that is called may also set the following
	* fields to affect the response sent to the client
	*
	* responseHeaders
	* outgoing_headers
	*
	* This sets the fault field on error
	*
	* @access   private
	*/
	function invoke_method() {
		$this->debug('in invoke_method, methodname=' . $this->methodname . ' methodURI=' . $this->methodURI . ' SOAPAction=' . $this->SOAPAction);

		if ($this->wsdl) {
			if ($this->opData = $this->wsdl->getOperationData($this->methodname)) {
				$this->debug('in invoke_method, found WSDL operation=' . $this->methodname);
				$this->appendDebug('opData=' . $this->varDump($this->opData));
			} elseif ($this->opData = $this->wsdl->getOperationDataForSoapAction($this->SOAPAction)) {
				// Note: hopefully this case will only be used for doc/lit, since rpc services should have wrapper element
				$this->debug('in invoke_method, found WSDL soapAction=' . $this->SOAPAction . ' for operation=' . $this->opData['name']);
				$this->appendDebug('opData=' . $this->varDump($this->opData));
				$this->methodname = $this->opData['name'];
			} else {
				$this->debug('in invoke_method, no WSDL for operation=' . $this->methodname);
				$this->fault('Client', "Operation '" . $this->methodname . "' is not defined in the WSDL for this service");
				return;
			}
		} else {
			$this->debug('in invoke_method, no WSDL to validate method');
		}

		// if a . is present in $this->methodname, we see if there is a class in scope,
		// which could be referred to. We will also distinguish between two deliminators,
		// to allow methods to be called a the class or an instance
		$class = '';
		$method = '';
		if (strpos($this->methodname, '..') > 0) {
			$delim = '..';
		} else if (strpos($this->methodname, '.') > 0) {
			$delim = '.';
		} else {
			$delim = '';
		}

		if (strlen($delim) > 0 && substr_count($this->methodname, $delim) == 1 &&
			class_exists(substr($this->methodname, 0, strpos($this->methodname, $delim)))) {
			// get the class and method name
			$class = substr($this->methodname, 0, strpos($this->methodname, $delim));
			$method = substr($this->methodname, strpos($this->methodname, $delim) + strlen($delim));
			$this->debug("in invoke_method, class=$class method=$method delim=$delim");
		}

		// does method exist?
		if ($class == '') {
			if (!function_exists($this->methodname)) {
				$this->debug("in invoke_method, function '$this->methodname' not found!");
				$this->result = 'fault: method not found';
				$this->fault('Client',"method '$this->methodname' not defined in service");
				return;
			}
		} else {
			$method_to_compare = (substr(phpversion(), 0, 2) == '4.') ? strtolower($method) : $method;
			if (!in_array($method_to_compare, get_class_methods($class))) {
				$this->debug("in invoke_method, method '$this->methodname' not found in class '$class'!");
				$this->result = 'fault: method not found';
				$this->fault('Client',"method '$this->methodname' not defined in service");
				return;
			}
		}

		// evaluate message, getting back parameters
		// verify that request parameters match the method's signature
		if(! $this->verify_method($this->methodname,$this->methodparams)){
			// debug
			$this->debug('ERROR: request not verified against method signature');
			$this->result = 'fault: request failed validation against method signature';
			// return fault
			$this->fault('Client',"Operation '$this->methodname' not defined in service.");
			return;
		}

		// if there are parameters to pass
		$this->debug('in invoke_method, params:');
		$this->appendDebug($this->varDump($this->methodparams));
		$this->debug("in invoke_method, calling '$this->methodname'");
		if (!function_exists('call_user_func_array')) {
			if ($class == '') {
				$this->debug('in invoke_method, calling function using eval()');
				$funcCall = "\$this->methodreturn = $this->methodname(";
			} else {
				if ($delim == '..') {
					$this->debug('in invoke_method, calling class method using eval()');
					$funcCall = "\$this->methodreturn = ".$class."::".$method."(";
				} else {
					$this->debug('in invoke_method, calling instance method using eval()');
					// generate unique instance name
					$instname = "\$inst_".time();
					$funcCall = $instname." = new ".$class."(); ";
					$funcCall .= "\$this->methodreturn = ".$instname."->".$method."(";
				}
			}
			if ($this->methodparams) {
				foreach ($this->methodparams as $param) {
					if (is_array($param)) {
						$this->fault('Client', 'NuSOAP does not handle complexType parameters correctly when using eval; call_user_func_array must be available');
						return;
					}
					$funcCall .= "\"$param\",";
				}
				$funcCall = substr($funcCall, 0, -1);
			}
			$funcCall .= ');';
			$this->debug('in invoke_method, function call: '.$funcCall);
			@eval($funcCall);
		} else {
			if ($class == '') {
				$this->debug('in invoke_method, calling function using call_user_func_array()');
				$call_arg = "$this->methodname";	// straight assignment changes $this->methodname to lower case after call_user_func_array()
			} elseif ($delim == '..') {
				$this->debug('in invoke_method, calling class method using call_user_func_array()');
				$call_arg = array ($class, $method);
			} else {
				$this->debug('in invoke_method, calling instance method using call_user_func_array()');
				$instance = new $class ();
				$call_arg = array(&$instance, $method);
			}
			$this->methodreturn = call_user_func_array($call_arg, $this->methodparams);
		}
        $this->debug('in invoke_method, methodreturn:');
        $this->appendDebug($this->varDump($this->methodreturn));
		$this->debug("in invoke_method, called method $this->methodname, received $this->methodreturn of type ".gettype($this->methodreturn));
	}

	/**
	* serializes the return value from a PHP function into a full SOAP Envelope
	*
	* The following fields are set by this function (when successful)
	*
	* responseSOAP
	*
	* This sets the fault field on error
	*
	* @access   private
	*/
	function serialize_return() {
		$this->debug('Entering serialize_return methodname: ' . $this->methodname . ' methodURI: ' . $this->methodURI);
		// if fault
		if (isset($this->methodreturn) && (get_class($this->methodreturn) == 'soap_fault')) {
			$this->debug('got a fault object from method');
			$this->fault = $this->methodreturn;
			return;
		} elseif ($this->methodreturnisliteralxml) {
			$return_val = $this->methodreturn;
		// returned value(s)
		} else {
			$this->debug('got a(n) '.gettype($this->methodreturn).' from method');
			$this->debug('serializing return value');
			if($this->wsdl){
				// weak attempt at supporting multiple output params
				if(sizeof($this->opData['output']['parts']) > 1){
			    	$opParams = $this->methodreturn;
			    } else {
			    	// TODO: is this really necessary?
			    	$opParams = array($this->methodreturn);
			    }
			    $return_val = $this->wsdl->serializeRPCParameters($this->methodname,'output',$opParams);
			    $this->appendDebug($this->wsdl->getDebug());
			    $this->wsdl->clearDebug();
				if($errstr = $this->wsdl->getError()){
					$this->debug('got wsdl error: '.$errstr);
					$this->fault('Server', 'unable to serialize result');
					return;
				}
			} else {
				if (isset($this->methodreturn)) {
					$return_val = $this->serialize_val($this->methodreturn, 'return');
				} else {
					$return_val = '';
					$this->debug('in absence of WSDL, assume void return for backward compatibility');
				}
			}
		}
		$this->debug('return value:');
		$this->appendDebug($this->varDump($return_val));

		$this->debug('serializing response');
		if ($this->wsdl) {
			$this->debug('have WSDL for serialization: style is ' . $this->opData['style']);
			if ($this->opData['style'] == 'rpc') {
				$this->debug('style is rpc for serialization: use is ' . $this->opData['output']['use']);
				if ($this->opData['output']['use'] == 'literal') {
					$payload = '<'.$this->methodname.'Response xmlns="'.$this->methodURI.'">'.$return_val.'</'.$this->methodname."Response>";
				} else {
					$payload = '<ns1:'.$this->methodname.'Response xmlns:ns1="'.$this->methodURI.'">'.$return_val.'</ns1:'.$this->methodname."Response>";
				}
			} else {
				$this->debug('style is not rpc for serialization: assume document');
				$payload = $return_val;
			}
		} else {
			$this->debug('do not have WSDL for serialization: assume rpc/encoded');
			$payload = '<ns1:'.$this->methodname.'Response xmlns:ns1="'.$this->methodURI.'">'.$return_val.'</ns1:'.$this->methodname."Response>";
		}
		$this->result = 'successful';
		if($this->wsdl){
			//if($this->debug_flag){
            	$this->appendDebug($this->wsdl->getDebug());
            //	}
			if (isset($opData['output']['encodingStyle'])) {
				$encodingStyle = $opData['output']['encodingStyle'];
			} else {
				$encodingStyle = '';
			}
			// Added: In case we use a WSDL, return a serialized env. WITH the usedNamespaces.
			$this->responseSOAP = $this->serializeEnvelope($payload,$this->responseHeaders,$this->wsdl->usedNamespaces,$this->opData['style'],$encodingStyle);
		} else {
			$this->responseSOAP = $this->serializeEnvelope($payload,$this->responseHeaders);
		}
		$this->debug("Leaving serialize_return");
	}

	/**
	* sends an HTTP response
	*
	* The following fields are set by this function (when successful)
	*
	* outgoing_headers
	* response
	*
	* @access   private
	*/
	function send_response() {
		$this->debug('Enter send_response');
		if ($this->fault) {
			$payload = $this->fault->serialize();
			$this->outgoing_headers[] = "HTTP/1.0 500 Internal Server Error";
			$this->outgoing_headers[] = "Status: 500 Internal Server Error";
		} else {
			$payload = $this->responseSOAP;
			// Some combinations of PHP+Web server allow the Status
			// to come through as a header.  Since OK is the default
			// just do nothing.
			// $this->outgoing_headers[] = "HTTP/1.0 200 OK";
			// $this->outgoing_headers[] = "Status: 200 OK";
		}
        // add debug data if in debug mode
		if(isset($this->debug_flag) && $this->debug_flag){
        	$payload .= $this->getDebugAsXMLComment();
        }
		$this->outgoing_headers[] = "Server: $this->title Server v$this->version";
		ereg('\$Revisio' . 'n: ([^ ]+)', $this->revision, $rev);
		$this->outgoing_headers[] = "X-SOAP-Server: $this->title/$this->version (".$rev[1].")";
		// Let the Web server decide about this
		//$this->outgoing_headers[] = "Connection: Close\r\n";
		$payload = $this->getHTTPBody($payload);
		$type = $this->getHTTPContentType();
		$charset = $this->getHTTPContentTypeCharset();
		$this->outgoing_headers[] = "Content-Type: $type" . ($charset ? '; charset=' . $charset : '');
		//begin code to compress payload - by John
		// NOTE: there is no way to know whether the Web server will also compress
		// this data.
		if (strlen($payload) > 1024 && isset($this->headers) && isset($this->headers['accept-encoding'])) {	
			if (strstr($this->headers['accept-encoding'], 'gzip')) {
				if (function_exists('gzencode')) {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content being gzipped -->";
					}
					$this->outgoing_headers[] = "Content-Encoding: gzip";
					$payload = gzencode($payload);
				} else {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content will not be gzipped: no gzencode -->";
					}
				}
			} elseif (strstr($this->headers['accept-encoding'], 'deflate')) {
				// Note: MSIE requires gzdeflate output (no Zlib header and checksum),
				// instead of gzcompress output,
				// which conflicts with HTTP 1.1 spec (http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.5)
				if (function_exists('gzdeflate')) {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content being deflated -->";
					}
					$this->outgoing_headers[] = "Content-Encoding: deflate";
					$payload = gzdeflate($payload);
				} else {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content will not be deflated: no gzcompress -->";
					}
				}
			}
		}
		//end code
		$this->outgoing_headers[] = "Content-Length: ".strlen($payload);
		reset($this->outgoing_headers);
		foreach($this->outgoing_headers as $hdr){
			header($hdr, false);
		}
		print $payload;
		$this->response = join("\r\n",$this->outgoing_headers)."\r\n\r\n".$payload;
	}

	/**
	* takes the value that was created by parsing the request
	* and compares to the method's signature, if available.
	*
	* @param	string	$operation	The operation to be invoked
	* @param	array	$request	The array of parameter values
	* @return	boolean	Whether the operation was found
	* @access   private
	*/
	function verify_method($operation,$request){
		if(isset($this->wsdl) && is_object($this->wsdl)){
			if($this->wsdl->getOperationData($operation)){
				return true;
			}
	    } elseif(isset($this->operations[$operation])){
			return true;
		}
		return false;
	}

	/**
	* processes SOAP message received from client
	*
	* @param	array	$headers	The HTTP headers
	* @param	string	$data		unprocessed request data from client
	* @return	mixed	value of the message, decoded into a PHP type
	* @access   private
	*/
    function parseRequest($headers, $data) {
		$this->debug('Entering parseRequest() for data of length ' . strlen($data) . ' and type ' . $headers['content-type']);
		if (!strstr($headers['content-type'], 'text/xml')) {
			$this->setError('Request not of type text/xml');
			return false;
		}
		if (strpos($headers['content-type'], '=')) {
			$enc = str_replace('"', '', substr(strstr($headers["content-type"], '='), 1));
			$this->debug('Got response encoding: ' . $enc);
			if(eregi('^(ISO-8859-1|US-ASCII|UTF-8)$',$enc)){
				$this->xml_encoding = strtoupper($enc);
			} else {
				$this->xml_encoding = 'US-ASCII';
			}
		} else {
			// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
			$this->xml_encoding = 'UTF-8';
		}
		$this->debug('Use encoding: ' . $this->xml_encoding . ' when creating soap_parser');
		// parse response, get soap parser obj
		$parser = new soap_parser($data,$this->xml_encoding,'',$this->decode_utf8);
		// parser debug
		$this->debug("parser debug: \n".$parser->getDebug());
		// if fault occurred during message parsing
		if($err = $parser->getError()){
			$this->result = 'fault: error in msg parsing: '.$err;
			$this->fault('Client',"error in msg parsing:\n".$err);
		// else successfully parsed request into soapval object
		} else {
			// get/set methodname
			$this->methodURI = $parser->root_struct_namespace;
			$this->methodname = $parser->root_struct_name;
			$this->debug('methodname: '.$this->methodname.' methodURI: '.$this->methodURI);
			$this->debug('calling parser->get_response()');
			$this->methodparams = $parser->get_response();
			// get SOAP headers
			$this->requestHeaders = $parser->getHeaders();
            // add document for doclit support
            $this->document = $parser->document;
		}
	 }

	/**
	* gets the HTTP body for the current response.
	*
	* @param string $soapmsg The SOAP payload
	* @return string The HTTP body, which includes the SOAP payload
	* @access private
	*/
	function getHTTPBody($soapmsg) {
		return $soapmsg;
	}
	
	/**
	* gets the HTTP content type for the current response.
	*
	* Note: getHTTPBody must be called before this.
	*
	* @return string the HTTP content type for the current response.
	* @access private
	*/
	function getHTTPContentType() {
		return 'text/xml';
	}
	
	/**
	* gets the HTTP content type charset for the current response.
	* returns false for non-text content types.
	*
	* Note: getHTTPBody must be called before this.
	*
	* @return string the HTTP content type charset for the current response.
	* @access private
	*/
	function getHTTPContentTypeCharset() {
		return $this->soap_defencoding;
	}

	/**
	* add a method to the dispatch map (this has been replaced by the register method)
	*
	* @param    string $methodname
	* @param    string $in array of input values
	* @param    string $out array of output values
	* @access   public
	* @deprecated
	*/
	function add_to_map($methodname,$in,$out){
			$this->operations[$methodname] = array('name' => $methodname,'in' => $in,'out' => $out);
	}

	/**
	* register a service function with the server
	*
	* @param    string $name the name of the PHP function, class.method or class..method
	* @param    array $in assoc array of input values: key = param name, value = param type
	* @param    array $out assoc array of output values: key = param name, value = param type
	* @param	mixed $namespace the element namespace for the method or false
	* @param	mixed $soapaction the soapaction for the method or false
	* @param	mixed $style optional (rpc|document) or false Note: when 'document' is specified, parameter and return wrappers are created for you automatically
	* @param	mixed $use optional (encoded|literal) or false
	* @param	string $documentation optional Description to include in WSDL
	* @param	string $encodingStyle optional (usually 'http://schemas.xmlsoap.org/soap/encoding/' for encoded)
	* @access   public
	*/
	function register($name,$in=array(),$out=array(),$namespace=false,$soapaction=false,$style=false,$use=false,$documentation='',$encodingStyle=''){
		global $HTTP_SERVER_VARS;

		if($this->externalWSDLURL){
			die('You cannot bind to an external WSDL file, and register methods outside of it! Please choose either WSDL or no WSDL.');
		}
		if (! $name) {
			die('You must specify a name when you register an operation');
		}
		if (!is_array($in)) {
			die('You must provide an array for operation inputs');
		}
		if (!is_array($out)) {
			die('You must provide an array for operation outputs');
		}
		if(false == $namespace) {
		}
		if(false == $soapaction) {
			if (isset($_SERVER)) {
				$SERVER_NAME = $_SERVER['SERVER_NAME'];
				$SCRIPT_NAME = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
			} elseif (isset($HTTP_SERVER_VARS)) {
				$SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
				$SCRIPT_NAME = isset($HTTP_SERVER_VARS['PHP_SELF']) ? $HTTP_SERVER_VARS['PHP_SELF'] : $HTTP_SERVER_VARS['SCRIPT_NAME'];
			} else {
				$this->setError("Neither _SERVER nor HTTP_SERVER_VARS is available");
			}
			$soapaction = "http://$SERVER_NAME$SCRIPT_NAME/$name";
		}
		if(false == $style) {
			$style = "rpc";
		}
		if(false == $use) {
			$use = "encoded";
		}
		if ($use == 'encoded' && $encodingStyle = '') {
			$encodingStyle = 'http://schemas.xmlsoap.org/soap/encoding/';
		}

		$this->operations[$name] = array(
	    'name' => $name,
	    'in' => $in,
	    'out' => $out,
	    'namespace' => $namespace,
	    'soapaction' => $soapaction,
	    'style' => $style);
        if($this->wsdl){
        	$this->wsdl->addOperation($name,$in,$out,$namespace,$soapaction,$style,$use,$documentation,$encodingStyle);
	    }
		return true;
	}

	/**
	* Specify a fault to be returned to the client.
	* This also acts as a flag to the server that a fault has occured.
	*
	* @param	string $faultcode
	* @param	string $faultstring
	* @param	string $faultactor
	* @param	string $faultdetail
	* @access   public
	*/
	function fault($faultcode,$faultstring,$faultactor='',$faultdetail=''){
		if ($faultdetail == '' && $this->debug_flag) {
			$faultdetail = $this->getDebug();
		}
		$this->fault = new soap_fault($faultcode,$faultactor,$faultstring,$faultdetail);
		$this->fault->soap_defencoding = $this->soap_defencoding;
	}

    /**
    * Sets up wsdl object.
    * Acts as a flag to enable internal WSDL generation
    *
    * @param string $serviceName, name of the service
    * @param mixed $namespace optional 'tns' service namespace or false
    * @param mixed $endpoint optional URL of service endpoint or false
    * @param string $style optional (rpc|document) WSDL style (also specified by operation)
    * @param string $transport optional SOAP transport
    * @param mixed $schemaTargetNamespace optional 'types' targetNamespace for service schema or false
    */
    function configureWSDL($serviceName,$namespace = false,$endpoint = false,$style='rpc', $transport = 'http://schemas.xmlsoap.org/soap/http', $schemaTargetNamespace = false)
    {
    	global $HTTP_SERVER_VARS;

		if (isset($_SERVER)) {
			$SERVER_NAME = $_SERVER['SERVER_NAME'];
			$SERVER_PORT = $_SERVER['SERVER_PORT'];
			$SCRIPT_NAME = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
			$HTTPS = $_SERVER['HTTPS'];
		} elseif (isset($HTTP_SERVER_VARS)) {
			$SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
			$SERVER_PORT = $HTTP_SERVER_VARS['SERVER_PORT'];
			$SCRIPT_NAME = isset($HTTP_SERVER_VARS['PHP_SELF']) ? $HTTP_SERVER_VARS['PHP_SELF'] : $HTTP_SERVER_VARS['SCRIPT_NAME'];
			$HTTPS = $HTTP_SERVER_VARS['HTTPS'];
		} else {
			$this->setError("Neither _SERVER nor HTTP_SERVER_VARS is available");
		}
		if ($SERVER_PORT == 80) {
			$SERVER_PORT = '';
		} else {
			$SERVER_PORT = ':' . $SERVER_PORT;
		}
        if(false == $namespace) {
            $namespace = "http://$SERVER_NAME/soap/$serviceName";
        }
        
        if(false == $endpoint) {
        	if ($HTTPS == '1' || $HTTPS == 'on') {
        		$SCHEME = 'https';
        	} else {
        		$SCHEME = 'http';
        	}
            $endpoint = "$SCHEME://$SERVER_NAME$SERVER_PORT$SCRIPT_NAME";
        }
        
        if(false == $schemaTargetNamespace) {
            $schemaTargetNamespace = $namespace;
        }
        
		$this->wsdl = new wsdl;
		$this->wsdl->serviceName = $serviceName;
        $this->wsdl->endpoint = $endpoint;
		$this->wsdl->namespaces['tns'] = $namespace;
		$this->wsdl->namespaces['soap'] = 'http://schemas.xmlsoap.org/wsdl/soap/';
		$this->wsdl->namespaces['wsdl'] = 'http://schemas.xmlsoap.org/wsdl/';
		if ($schemaTargetNamespace != $namespace) {
			$this->wsdl->namespaces['types'] = $schemaTargetNamespace;
		}
        $this->wsdl->schemas[$schemaTargetNamespace][0] = new xmlschema('', '', $this->wsdl->namespaces);
        $this->wsdl->schemas[$schemaTargetNamespace][0]->schemaTargetNamespace = $schemaTargetNamespace;
        $this->wsdl->schemas[$schemaTargetNamespace][0]->imports['http://schemas.xmlsoap.org/soap/encoding/'][0] = array('location' => '', 'loaded' => true);
        $this->wsdl->schemas[$schemaTargetNamespace][0]->imports['http://schemas.xmlsoap.org/wsdl/'][0] = array('location' => '', 'loaded' => true);
        $this->wsdl->bindings[$serviceName.'Binding'] = array(
        	'name'=>$serviceName.'Binding',
            'style'=>$style,
            'transport'=>$transport,
            'portType'=>$serviceName.'PortType');
        $this->wsdl->ports[$serviceName.'Port'] = array(
        	'binding'=>$serviceName.'Binding',
            'location'=>$endpoint,
            'bindingType'=>'http://schemas.xmlsoap.org/wsdl/soap/');
    }
}




?>
