<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * dom_xmlrpc_array_document wraps a PHP array with the DOM XML-RPC API
 * @package dom-xmlrpc
 * @copyright (C) 2004 John Heinstein. All rights reserved
 * @license http://www.gnu.org/copyleft/lesser.html LGPL License
 * @author John Heinstein <johnkarl@nbnet.nb.ca>
 * @link http://www.engageinteractive.com/dom_xmlrpc/ DOM XML-RPC Home Page
 * DOM XML-RPC is Free Software
 **/

defined('_VALID_MOS') or die();
if(!defined('PHP_HTTP_TOOLS_INCLUDE_PATH')){
	define('PHP_HTTP_TOOLS_INCLUDE_PATH', (dirname(__file__) . "/"));
}
define('CRLF', "\r\n");
define('CR', "\r");
define('LF', "\n");

define('HTTP_READ_STATE_BEGIN', 1);
define('HTTP_READ_STATE_HEADERS', 2);
define('HTTP_READ_STATE_BODY', 3);
require_once (PHP_HTTP_TOOLS_INCLUDE_PATH . 'php_http_exceptions.php');
class php_http_request{
	var $headers = null;
	var $requestMethod = 'POST';
	var $requestPath = '';
	var $protocol = 'HTTP';
	var $protocolVersion = '1.1';

	function &getHeaders(){
		return $this->headers;
	}

	function setHeader($name, $value, $allowMultipleHeaders = false){
		$this->headers->setHeader($name, $value, $allowMultipleHeaders);
	}

	function setHeaders(){

		$this->setHeader('User-Agent', 'PHP-HTTP-Client(Generic)/0.1');
		$this->setHeader('Connection', 'Close');
	}

	function setRequestMethod($method){
		$method = strtoupper($method);
		switch($method){
			case 'POST':
			case 'GET':
			case 'HEAD':
			case 'PUT':
				$this->requestMethod = $method;
				return true;
				break;
		}
		return false;
	}

	function setRequestPath($path){
		$this->requestPath = $path;
	}

	function setProtocolVersion($version){
		if(($version == '1.0') || ($version == '1.1')){
			$this->protocolVersion = $version;
			return true;
		}
		return false;
	}

	function setAuthorization($user, $password){
		$encodedChallengeResponse = 'Basic ' . base64_encode($this->user . ':' . $this->password);
		$this->setHeader('Authorization', $encodedChallengeResponse);
	}

}

class php_http_client_generic extends php_http_request{
	var $connection;
	var $responseHeadersAsObject = false;
	var $response = null;
	var $events = array('onRequest'         => null, 'onRead' => null, 'onResponse' => null,
						'onResponseHeaders' => null, 'onResponseBody' => null);

	function php_http_client_generic($host = '', $path = '/', $port = 80, $timeout = 0){
		$this->connection = new php_http_connection($host, $path, $port, $timeout);
		$this->headers = new php_http_headers();
		$this->requestPath = $path;
		$this->response = new php_http_response();
		$this->setHeaders();
	}

	function generateResponseHeadersAsObject($responseHeadersAsObject){
		$this->responseHeadersAsObject = $responseHeadersAsObject;
		if($responseHeadersAsObject){
			$this->response->headers = new php_http_headers();
		}
	}

	function fireEvent($target, $data){
		if($this->events[$target] != null){
			call_user_func($this->events[$target], $data);
		}
	}

	function setHTTPEvent($option, $truthVal, $customHandler = null){
		if($customHandler != null){
			$handler = &$customHandler;
		} else{
			$handler = array(&$this, 'defaultHTTPEventHandler');
		}
		switch($option){
			case 'onRequest':
			case 'onRead':
			case 'onResponse':
			case 'onResponseHeaders':
			case 'onResponseBody':
				$truthVal ? ($this->events[$option] = &$handler):
				($this->events[$option] = null);
				break;
		}
	}

	function getHTTPEvent($option){
		switch($option){
			case 'onRequest':
			case 'onRead':
			case 'onResponse':
			case 'onResponseHeaders':
			case 'onResponseBody':
				return ($this->events[$option] != null);
				break;
		}
	}

	function defaultHTTPEventHandler($data){
		$this->printHTML($data);
	}

	function printHTML($html){
		print ('<pre>' . htmlentities($html) . '</pre>');
	}

	function connect(){
		if(!$this->headers->headerExists('Host')){
			$this->setHeader('Host', $this->connection->host);
		}
		return $this->connection->connect();
	}

	function disconnect(){
		return $this->connection->disconnect();
	}

	function isConnected(){
		return $this->connection->isOpen();
	}

	function &get($url){
		$this->setRequestMethod('GET');
		$this->setRequestPath($url);
		$this->get_custom($url);
		$this->connect();
		$result = $this->send('');
		return $result;
	}

	function get_custom($url){

	}

	function &post($data){
		$this->setRequestMethod('POST');
		$this->setHeader('Content-Type', 'text/html');
		$this->post_custom($data);
		$this->connect();
		return $this->send($data);
	}

	function post_custom($data){

	}

	function &head($url){
		$this->setRequestMethod('HEAD');
		$this->head_custom($url);
		$this->connect();
		return $this->send('');
	}

	function head_custom($url){

	}

	function send($message){
		$conn = &$this->connection;
		if($conn->isOpen()){

			$request = $this->requestMethod . ' ' . $this->requestPath . ' ' . $this->protocol . '/' .
				$this->protocolVersion . CRLF;
			$request .= $this->headers->toString() . CRLF;
			$request .= $message;

			$response = $headers = $body = '';
			$readState = HTTP_READ_STATE_BEGIN;
			$this->fireEvent('onRequest', $request);

			$connResource = &$conn->connection;
			fputs($connResource, $request);

			while(!feof($connResource)){
				$data = fgets($connResource, 4096);
				$this->fireEvent('onRead', $data);
				switch($readState){
					case HTTP_READ_STATE_BEGIN:
						$this->response->statusLine = $data;
						$readState = HTTP_READ_STATE_HEADERS;
						break;
					case HTTP_READ_STATE_HEADERS:
						if(trim($data) == ''){

							$readState = HTTP_READ_STATE_BODY;
						} else{
							if($this->responseHeadersAsObject){
								$this->response->setUnformattedHeader($data);
							} else{
								$this->response->headers .= $data;
							}
						}
						break;
					case HTTP_READ_STATE_BODY:
						$this->response->message .= $data;
						break;
				}
			}
			$this->normalizeResponseIfChunked();
			$headerString = is_object($this->response->headers) ? $this->response->headers->toString() :
				$this->response->headers;
			$this->fireEvent('onResponseHeaders', $headerString);
			$this->fireEvent('onResponseBody', $this->response->message);
			$this->fireEvent('onResponse', $this->response->headers . $this->response->message);
			return $this->response;
		} else{
			HTTPExceptions::raiseException(HTTP_SOCKET_CONNECTION_ERR, ('HTTP Transport Error - Unable to establish connection to host ' .
				$conn->host));
		}
	}

	function normalizeResponseIfChunked(){
		if(($this->protocolVersion = '1.1') && (!$this->response->isResponseChunkDecoded)){
			if($this->responseHeadersAsObject){
				if($this->response->headers->headerExists('Transfer-Encoding') && ($this->response->headers->getHeader
				('Transfer-Encoding') == 'chunked')
				){
					$this->response->message = $this->decodeChunkedData($this->response->getResponse
					());
					$this->response->isResponseChunkDecoded = true;
				}
			} else{
				if((strpos($this->response->headers, 'Transfer-Encoding') !== false) && (strpos($this->response->headers,
					'chunked') !== false)
				){
					$this->response->message = $this->decodeChunkedData($this->response->getResponse
					());
					$this->response->isResponseChunkDecoded = true;
				}
			}
		}
	}

	function decodeChunkedData($data){
		$chunkStart = $chunkEnd = strpos($data, CRLF) + 2;
		$chunkLengthInHex = substr($data, 0, $chunkEnd);
		$chunkLength = hexdec(trim($chunkLengthInHex));
		$decodedData = '';
		while($chunkLength > 0){
			$chunkEnd = strpos($data, CRLF, ($chunkStart + $chunkLength));
			if(!$chunkEnd){

				$decodedData .= substr($data, $chunkStart);
				break;
			}
			$decodedData .= substr($data, $chunkStart, ($chunkEnd - $chunkStart));
			$chunkStart = $chunkEnd + 2;
			$chunkEnd = strpos($data, CRLF, $chunkStart) + 2;
			if(!$chunkEnd)
				break;
			$chunkLengthInHex = substr($data, $chunkStart, ($chunkEnd - $chunkStart));
			$chunkLength = hexdec(trim($chunkLengthInHex));
			$chunkStart = $chunkEnd;
		}
		return $decodedData;
	}

}

class php_http_connection{
	var $connection = null;
	var $host;
	var $path;
	var $port;
	var $timeout;
	var $errorNumber = 0;
	var $errorString = '';

	function php_http_connection($host = '', $path = '/', $port = 80, $timeout = 0){
		$this->host = $this->formatHost($host);
		$this->path = $this->formatPath($path);
		$this->port = $port;
		$this->timeout = $timeout;
	}

	function formatHost($hostString){
		$hasProtocol = (substr(strtoupper($hostString), 0, 7) == 'HTTP://');
		if($hasProtocol){
			$hostString = substr($hostString, 7);
		}
		return $hostString;
	}

	function formatPath($pathString){
		if(($pathString == '') || ($pathString == null)){
			$pathString = '/';
		}
		return $pathString;
	}

	function connect(){
		if($this->timeout == 0){
			$this->connection = @fsockopen($this->host, $this->port, $errorNumber, $errorString);
		} else{
			$this->connection = @fsockopen($this->host, $this->port, $errorNumber, $errorString,
				$this->timeout);
		}
		$this->errorNumber = $errorNumber;
		$this->errorString = $errorString;
		return is_resource($this->connection);
	}

	function isOpen(){
		return (is_resource($this->connection) && (!feof($this->connection)));
	}

	function disconnect(){
		fclose($this->connection);
		$this->connection = null;
		return true;
	}

}

class php_http_headers{
	var $headers;

	function php_http_headers(){
		$this->headers = array();
	}

	function &getHeader($name){
		if($this->headerExists($name)){
			return $this->headers[$name];
		}
		return false;
	}

	function setHeader($name, $value, $allowMultipleHeaders = false){
		if($allowMultipleHeaders){
			if(isset($this->headers[$name])){
				if(is_array($this->headers[$name])){
					$this->headers[$name][count($this->headers)] = $value;
				} else{
					$tempVal = $this->headers[$name];
					$this->headers[$name] = array($tempVal, $value);
				}
			} else{
				$this->headers[$name] = array();
				$this->headers[$name][0] = $value;
			}
		} else{
			$this->headers[$name] = $value;
		}
	}

	function headerExists($name){
		return isset($this->headers[$name]);
	}

	function removeHeader($name){
		if($this->headerExists($name)){
			unset($this->headers[$name]);
			return true;
		}
		return false;
	}

	function getHeaders(){
		return $this->headers;
	}

	function getHeaderList(){
		return array_keys($this->headers);
	}

	function toString(){
		$retString = '';
		foreach($this->headers as $key => $value){
			if(is_array($value)){
				foreach($value as $key2 => $value2){
					$retString .= $key . ': ' . $value2 . CRLF;
				}
			} else{
				$retString .= $key . ': ' . $value . CRLF;
			}
		}
		return $retString;
	}

}

class php_http_response{
	var $statusLine = '';
	var $headers = '';
	var $message = '';
	var $isResponseChunkDecoded = false;

	function getResponse(){
		return $this->message;
	}

	function getStatusLine(){
		return $this->statusLine;
	}

	function getStatusCode(){
		$statusArray = explode(' ', $this->statusLine);
		if(count($statusArray > 1)){
			return intval($statusArray[1], 10);
		}
		return -1;
	}

	function &getHeaders(){
		return $this->headers;
	}

	function setUnformattedHeader($headerString){
		$colonIndex = strpos($headerString, ':');
		if($colonIndex !== false){
			$key = trim(substr($headerString, 0, $colonIndex));
			$value = trim(substr($headerString, ($colonIndex + 1)));
			$this->headers->setHeader($key, $value, true);
		}
	}

}


?>
